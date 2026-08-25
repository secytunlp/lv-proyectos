<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Baja la categoria de las solicitudes SICADI en estado Otorgada a:
 *
 *   - investigadors.sicadi_id
 *   - investigador_sicadis (fila con actual = 1, year = año de la convocatoria)
 *
 * Toma, por investigador, la solicitud Otorgada de la convocatoria MAS RECIENTE.
 * Deja una sola fila actual = 1 por investigador (esto normaliza los pivots
 * duplicados).
 *
 * NO toca integrantes.sicadi_id ni integrante_estados: para propagar el cambio
 * a los proyectos en ejecucion existe sicadi:actualizar-2023.
 *
 * Dry-run por defecto (hace rollback). Para persistir: --commit
 */
class AplicarOtorgadasSicadi extends Command
{
    protected $signature = 'sicadi:aplicar-otorgadas
        {--year= : Aplicar solo las convocatorias de ese año}
        {--cuil= : Aplicar solo a un CUIL puntual}
        {--commit : Persistir los cambios (por defecto es dry-run)}';

    protected $description = 'Aplica la categoria de las solicitudes Otorgadas a investigadors e investigador_sicadis';

    /** Valores que significan "sin categoria" */
    private $sinCategoria = array('', 'S/C', 'SC', 'S/D', 'SIN CATEGORIA', 'SIN CATEGORÍA', '-', 'NO POSEE');

    private function cuilNorm($col)
    {
        return "REPLACE(REPLACE(REPLACE(".$col.", '-', ''), '.', ''), ' ', '')";
    }

    public function handle()
    {
        $commit = (bool) $this->option('commit');
        $year   = $this->option('year');
        $cuil   = $this->option('cuil');

        $sql =
            'SELECT '.
            '  s.id                 AS solicitud_id, '.
            '  s.cuil               AS cuil, '.
            "  TRIM(CONCAT(p.apellido, ', ', p.nombre)) AS persona, ".
            '  c.tipo               AS conv_tipo, '.
            '  c.year               AS conv_year, '.
            '  s.categoria_asignada AS cat_solicitud, '.
            '  i.id                 AS investigador_id, '.
            '  i.sicadi_id          AS sicadi_actual_id, '.
            '  si.nombre            AS cat_investigador, '.
            '  sn.id                AS sicadi_nuevo_id '.
            'FROM solicitud_sicadis s '.
            'JOIN sicadi_convocatorias c    ON c.id = s.convocatoria_id '.
            'JOIN personas p                ON '.$this->cuilNorm('p.cuil').' = '.$this->cuilNorm('s.cuil').' '.
            'JOIN investigadors i           ON i.persona_id = p.id '.
            'LEFT JOIN sicadis si           ON si.id = i.sicadi_id '.
            'LEFT JOIN sicadis sn           ON UPPER(TRIM(sn.nombre)) = UPPER(TRIM(s.categoria_asignada)) '.
            "WHERE UPPER(TRIM(s.estado)) = 'OTORGADA' ".
            "  AND s.categoria_asignada IS NOT NULL AND TRIM(s.categoria_asignada) <> '' ".
            '  AND c.year IS NOT NULL ';

        $bind = array();
        if ($year !== null && $year !== '') {
            $sql .= 'AND c.year = ? ';
            $bind[] = $year;
        }
        if ($cuil !== null && $cuil !== '') {
            $sql .= 'AND '.$this->cuilNorm('s.cuil').' = '.$this->cuilNorm('?').' ';
            $bind[] = $cuil;
        }
        $sql .= 'ORDER BY i.id, c.year DESC, s.id DESC';

        $filas = DB::select($sql, $bind);

        if (count($filas) === 0) {
            $this->warn('No hay solicitudes Otorgadas que lleguen a un investigador con esos filtros.');
            return 0;
        }

        // ------------------------------------------------------------------
        // Una sola solicitud por investigador: la de la convocatoria mas nueva
        // ------------------------------------------------------------------
        $porInvestigador = array();
        foreach ($filas as $f) {
            if (!isset($porInvestigador[$f->investigador_id])) {
                $porInvestigador[$f->investigador_id] = $f;
            }
        }

        $aplicar    = array();
        $sinSicadi  = array();
        $yaOk       = 0;

        foreach ($porInvestigador as $f) {
            if ($f->sicadi_nuevo_id === null) {
                $sinSicadi[] = $f;
                continue;
            }

            $pivots = DB::select(
                'SELECT isd.id, isd.sicadi_id, isd.year, isd.actual, sp.nombre '.
                'FROM investigador_sicadis isd '.
                'LEFT JOIN sicadis sp ON sp.id = isd.sicadi_id '.
                'WHERE isd.investigador_id = ? ORDER BY isd.year DESC, isd.id DESC',
                array($f->investigador_id)
            );

            $actuales = array();
            foreach ($pivots as $pv) {
                if ((int) $pv->actual === 1) {
                    $actuales[] = $pv;
                }
            }

            // investigador_sicadis lleva UNA FILA POR CONVOCATORIA/AÑO, asi que
            // la fila destino es la del año de la convocatoria — exista o no con
            // la categoria correcta. Si ya hay una de ese año con otra categoria
            // se le corrige el sicadi_id; nunca se agrega una segunda del mismo año.
            $destino = null;
            foreach ($pivots as $pv) {
                if ((int) $pv->year === (int) $f->conv_year) {
                    $destino = $pv;
                    break;
                }
            }

            $acciones = array();

            if ((int) $f->sicadi_actual_id !== (int) $f->sicadi_nuevo_id) {
                $acciones[] = 'investigadors.sicadi_id';
            }
            if ($destino === null) {
                $acciones[] = 'pivot: alta '.$f->cat_solicitud.' '.$f->conv_year;
            } else {
                if ((int) $destino->sicadi_id !== (int) $f->sicadi_nuevo_id) {
                    $acciones[] = 'pivot '.$f->conv_year.': '.
                        ($destino->nombre === null ? '?' : $destino->nombre).' -> '.$f->cat_solicitud;
                }
                if ((int) $destino->actual !== 1) {
                    $acciones[] = 'pivot: marcar actual';
                }
            }
            $aDesmarcar = 0;
            foreach ($actuales as $pv) {
                if ($destino === null || (int) $pv->id !== (int) $destino->id) {
                    $aDesmarcar++;
                }
            }
            if ($aDesmarcar > 0) {
                $acciones[] = 'pivot: desmarcar '.$aDesmarcar;
            }

            if (count($acciones) === 0) {
                $yaOk++;
                continue;
            }

            $f->_acciones   = $acciones;
            $f->_destino    = $destino;
            $f->_actuales   = $actuales;
            $aplicar[]      = $f;
        }

        // ------------------------------------------------------------------
        // Informe previo
        // ------------------------------------------------------------------
        $this->line('');
        $this->line('============================================================');
        $this->info($commit ? 'MODO: COMMIT (se persiste)' : 'MODO: DRY-RUN (no se guarda nada)');
        $this->line('============================================================');

        if (count($sinSicadi) > 0) {
            $this->line('');
            $this->warn('Categoria de la solicitud que no existe en la tabla sicadis (OMITIDAS): '.count($sinSicadi));
            $rows = array();
            foreach ($sinSicadi as $f) {
                $rows[] = array($f->solicitud_id, $f->cuil, $f->persona, $f->cat_solicitud);
            }
            $this->table(array('Sol.', 'CUIL', 'Persona', 'Categoria'), $rows);
        }

        $this->line('');
        $this->info('Investigadores a actualizar: '.count($aplicar).'   (ya correctos: '.$yaOk.')');

        if (count($aplicar) === 0) {
            $this->line('Nada que hacer.');
            return 0;
        }

        $rows = array();
        foreach ($aplicar as $f) {
            $rows[] = array(
                $f->investigador_id,
                $f->cuil,
                $this->corta($f->persona, 30),
                trim($f->conv_tipo.' '.$f->conv_year),
                $this->esVacia($f->cat_investigador) ? '(sin)' : $f->cat_investigador,
                $f->cat_solicitud,
                implode(' + ', $f->_acciones),
            );
        }
        $this->table(
            array('Inv.', 'CUIL', 'Persona', 'Convocatoria', 'Actual', 'Nueva', 'Acciones'),
            $rows
        );

        // ------------------------------------------------------------------
        // Aplicar
        // ------------------------------------------------------------------
        $nInv = 0; $nAlta = 0; $nMarca = 0; $nDesmarca = 0; $nCorrige = 0;

        DB::beginTransaction();
        try {
            foreach ($aplicar as $f) {
                // 1) investigadors
                if ((int) $f->sicadi_actual_id !== (int) $f->sicadi_nuevo_id) {
                    $nInv += DB::table('investigadors')
                        ->where('id', $f->investigador_id)
                        ->update(array('sicadi_id' => $f->sicadi_nuevo_id));
                }

                // 2) pivot destino
                if ($f->_destino === null) {
                    $destinoId = DB::table('investigador_sicadis')->insertGetId(array(
                        'investigador_id' => $f->investigador_id,
                        'sicadi_id'       => $f->sicadi_nuevo_id,
                        'notificacion'    => null,
                        'actual'          => 1,
                        'year'            => $f->conv_year,
                        'created_at'      => now(),
                        'updated_at'      => now(),
                    ));
                    $nAlta++;
                } else {
                    // Ya hay fila de ese año: se corrige en el lugar (una por año)
                    $destinoId = $f->_destino->id;
                    $cambios = array();
                    if ((int) $f->_destino->sicadi_id !== (int) $f->sicadi_nuevo_id) {
                        $cambios['sicadi_id'] = $f->sicadi_nuevo_id;
                        $nCorrige++;
                    }
                    if ((int) $f->_destino->actual !== 1) {
                        $cambios['actual'] = 1;
                        $nMarca++;
                    }
                    if (!empty($cambios)) {
                        $cambios['updated_at'] = now();
                        DB::table('investigador_sicadis')->where('id', $destinoId)->update($cambios);
                    }
                }

                // 3) desmarcar el resto
                $nDesmarca += DB::table('investigador_sicadis')
                    ->where('investigador_id', $f->investigador_id)
                    ->where('actual', 1)
                    ->where('id', '<>', $destinoId)
                    ->update(array('actual' => 0, 'updated_at' => now()));
            }

            $this->line('');
            $this->info('Filas afectadas:');
            $this->table(
                array('Operacion', 'Filas'),
                array(
                    array('investigadors.sicadi_id',                  $nInv),
                    array('investigador_sicadis (altas)',             $nAlta),
                    array('investigador_sicadis (categoria del año)', $nCorrige),
                    array('investigador_sicadis (marcar actual)',     $nMarca),
                    array('investigador_sicadis (desmarcar)',         $nDesmarca),
                )
            );

            if ($commit) {
                DB::commit();
                $this->line('');
                $this->info('COMMIT: cambios persistidos.');
                $this->warn('Recorda que integrantes.sicadi_id NO se modifico (proyectos en ejecucion).');
            } else {
                DB::rollBack();
                $this->line('');
                $this->warn('DRY-RUN: nada se guardo. Volve a correr con --commit para grabar.');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Error (rollback): '.$e->getMessage());
            return 1;
        }

        return 0;
    }

    private function esVacia($cat)
    {
        return in_array(strtoupper(trim((string) $cat)), $this->sinCategoria, true);
    }

    private function corta($v, $n)
    {
        $v = (string) $v;
        return mb_strlen($v) > $n ? mb_substr($v, 0, $n - 1).'.' : $v;
    }
}
