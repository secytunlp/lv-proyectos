<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Para las solicitudes SICADI Otorgadas cuyo CUIL no encuentra persona pero si
 * hay una persona con el MISMO DNI, decide de que lado esta el error validando
 * el digito verificador del CUIL:
 *
 *   PERSONA MAL     -> el CUIL de la solicitud valida y el de la persona no.
 *                      Se corrige personas.cuil.
 *   SOLICITUD MAL   -> valida el de la persona y no el de la solicitud.
 *                      Se corrige solicitud_sicadis.cuil.
 *   AMBOS VALIDOS   -> los dos cierran pero difieren (prefijo distinto). Manual.
 *   AMBOS MAL       -> ninguno cierra. Manual.
 *   COLISION        -> el CUIL a grabar ya lo tiene otra persona. Manual.
 *
 * Dry-run por defecto. Para persistir: --commit
 */
class CotejarCuilSicadi extends Command
{
    protected $signature = 'sicadi:cotejar-cuil
        {--year= : Filtrar por año de la convocatoria}
        {--estado=Otorgada : Estado de la solicitud a considerar}
        {--solo= : Mostrar solo las filas cuya accion contenga este texto}
        {--commit : Persistir las correcciones inequivocas (por defecto es dry-run)}';

    protected $description = 'Coteja el CUIL de las solicitudes contra personas por DNI y corrige el lado que tiene el digito verificador mal';

    private function cuilNorm($col)
    {
        return "REPLACE(REPLACE(REPLACE(".$col.", '-', ''), '.', ''), ' ', '')";
    }

    /** Valida el digito verificador de un CUIL de 11 digitos */
    private function cuilValido($cuil)
    {
        $d = preg_replace('/\D/', '', (string) $cuil);
        if (strlen($d) !== 11) {
            return false;
        }
        // prefijo 00 = CUIL no cargado
        if (substr($d, 0, 2) === '00') {
            return false;
        }
        $mult = array(5, 4, 3, 2, 7, 6, 5, 4, 3, 2);
        $suma = 0;
        for ($i = 0; $i < 10; $i++) {
            $suma += $mult[$i] * (int) $d[$i];
        }
        $resto = $suma % 11;
        $dv = 11 - $resto;
        if ($dv === 11) {
            $dv = 0;
        }
        if ($dv === 10) {
            $dv = 9;
        }
        return $dv === (int) $d[10];
    }

    public function handle()
    {
        $commit = (bool) $this->option('commit');
        $year   = $this->option('year');
        $estado = $this->option('estado');
        $solo   = $this->option('solo');

        $sql =
            'SELECT '.
            '  s.id       AS solicitud_id, '.
            '  s.cuil     AS cuil_solicitud, '.
            '  s.apellido AS apellido, '.
            '  s.nombre   AS nombre, '.
            '  c.year     AS conv_year '.
            'FROM solicitud_sicadis s '.
            'LEFT JOIN sicadi_convocatorias c ON c.id = s.convocatoria_id '.
            'LEFT JOIN personas p ON '.$this->cuilNorm('p.cuil').' = '.$this->cuilNorm('s.cuil').' '.
            "WHERE s.categoria_asignada IS NOT NULL AND TRIM(s.categoria_asignada) <> '' ".
            '  AND p.id IS NULL ';

        $bind = array();
        if ($estado !== null && $estado !== '') {
            $sql .= 'AND UPPER(TRIM(s.estado)) = UPPER(?) ';
            $bind[] = trim($estado);
        }
        if ($year !== null && $year !== '') {
            $sql .= 'AND c.year = ? ';
            $bind[] = $year;
        }
        $sql .= 'ORDER BY s.apellido, s.nombre';

        $filas = DB::select($sql, $bind);

        if (count($filas) === 0) {
            $this->info('No hay solicitudes sin persona con esos filtros.');
            return 0;
        }

        $rows      = array();
        $resumen   = array();
        $correcciones = array();

        foreach ($filas as $f) {
            $digitos = preg_replace('/\D/', '', (string) $f->cuil_solicitud);
            if (strlen($digitos) !== 11) {
                $this->agregar($resumen, 'CUIL SOLICITUD INVALIDO');
                $rows[] = array($f->solicitud_id, $f->cuil_solicitud, '', '', '', 'CUIL SOLICITUD INVALIDO', trim($f->apellido.', '.$f->nombre));
                continue;
            }
            $dni = (int) substr($digitos, 2, 8);

            $cands = DB::select(
                'SELECT p.id, p.apellido, p.nombre, p.cuil, i.id AS investigador_id '.
                'FROM personas p LEFT JOIN investigadors i ON i.persona_id = p.id '.
                'WHERE p.documento = ? LIMIT 5',
                array($dni)
            );

            if (count($cands) === 0) {
                $this->agregar($resumen, 'SIN CANDIDATO POR DNI');
                $rows[] = array($f->solicitud_id, $f->cuil_solicitud, '', '', '', 'SIN CANDIDATO POR DNI', trim($f->apellido.', '.$f->nombre));
                continue;
            }
            if (count($cands) > 1) {
                $this->agregar($resumen, 'VARIOS CANDIDATOS');
                $rows[] = array($f->solicitud_id, $f->cuil_solicitud, '', '', '', 'VARIOS CANDIDATOS ('.count($cands).')', trim($f->apellido.', '.$f->nombre));
                continue;
            }

            $p = $cands[0];
            $okSol = $this->cuilValido($f->cuil_solicitud);
            $okPer = $this->cuilValido($p->cuil);

            if ($okSol && !$okPer) {
                $accion = 'PERSONA MAL';
            } elseif (!$okSol && $okPer) {
                $accion = 'SOLICITUD MAL';
            } elseif ($okSol && $okPer) {
                $accion = 'AMBOS VALIDOS';
            } else {
                $accion = 'AMBOS MAL';
            }

            $userId = null;
            $notaUser = '';

            // colision: el CUIL a grabar ya lo tiene otro registro
            if ($accion === 'PERSONA MAL') {
                $choque = DB::select(
                    'SELECT id FROM personas WHERE '.$this->cuilNorm('cuil').' = ? AND id <> ? LIMIT 1',
                    array($digitos, $p->id)
                );
                if (count($choque) > 0) {
                    $accion = 'COLISION (persona #'.$choque[0]->id.')';
                }

                // Otra solicitud que hoy cruza con el CUIL viejo quedaria huerfana
                if ($accion === 'PERSONA MAL' && $p->cuil !== null && $p->cuil !== '') {
                    $otra = DB::select(
                        'SELECT id FROM solicitud_sicadis WHERE '.$this->cuilNorm('cuil').' = '.$this->cuilNorm('?').' AND id <> ? LIMIT 1',
                        array($p->cuil, $f->solicitud_id)
                    );
                    if (count($otra) > 0) {
                        $accion = 'REVISAR (solicitud #'.$otra[0]->id.' usa el CUIL viejo)';
                    }
                }

                // users.cuil es UNIQUE y es la clave de "esto es mio" en toda la app
                if ($accion === 'PERSONA MAL' && $p->cuil !== null && $p->cuil !== '') {
                    $us = DB::select(
                        'SELECT id FROM users WHERE '.$this->cuilNorm('cuil').' = '.$this->cuilNorm('?').' LIMIT 1',
                        array($p->cuil)
                    );
                    if (count($us) > 0) {
                        $choqueU = DB::select(
                            'SELECT id FROM users WHERE '.$this->cuilNorm('cuil').' = ? AND id <> ? LIMIT 1',
                            array($digitos, $us[0]->id)
                        );
                        if (count($choqueU) > 0) {
                            $accion = 'COLISION (usuario #'.$choqueU[0]->id.')';
                        } else {
                            $userId = $us[0]->id;
                            $notaUser = ' +user#'.$userId;
                        }
                    }
                }
            } elseif ($accion === 'SOLICITUD MAL') {
                $choque = DB::select(
                    'SELECT id FROM solicitud_sicadis WHERE '.$this->cuilNorm('cuil').' = '.$this->cuilNorm('?').' AND id <> ? LIMIT 1',
                    array($p->cuil, $f->solicitud_id)
                );
                if (count($choque) > 0) {
                    $accion = 'COLISION (solicitud #'.$choque[0]->id.')';
                }
            }

            $this->agregar($resumen, preg_replace('/ \(.*\)$/', '', $accion));

            if ($accion === 'PERSONA MAL' || $accion === 'SOLICITUD MAL') {
                $correcciones[] = array(
                    'accion'        => $accion,
                    'solicitud_id'  => $f->solicitud_id,
                    'persona_id'    => $p->id,
                    'user_id'       => $userId,
                    'cuil_solicitud'=> $f->cuil_solicitud,
                    'cuil_persona'  => $p->cuil,
                );
            }

            $fila = array(
                $f->solicitud_id,
                $f->cuil_solicitud.($okSol ? ' OK' : ' MAL'),
                '#'.$p->id,
                $this->corta($p->apellido.', '.$p->nombre, 26),
                ($p->cuil === null || $p->cuil === '' ? 's/d' : $p->cuil).($okPer ? ' OK' : ' MAL'),
                $accion,
                ($p->investigador_id === null ? 'SIN INV' : 'inv#'.$p->investigador_id).$notaUser,
            );

            if ($solo !== null && $solo !== '' && stripos($accion, $solo) === false) {
                continue;
            }
            $rows[] = $fila;
        }

        $this->line('');
        $this->line('============================================================');
        $this->info($commit ? 'MODO: COMMIT (se persiste)' : 'MODO: DRY-RUN (no se guarda nada)');
        $this->line('============================================================');

        if (count($rows) > 0) {
            $this->table(
                array('Sol.', 'CUIL solicitud', 'Persona', 'Nombre en personas', 'CUIL persona', 'Accion', 'Investigador'),
                $rows
            );
        }

        arsort($resumen);
        $resRows = array();
        foreach ($resumen as $a => $cant) {
            $resRows[] = array($a, $cant);
        }
        $this->line('');
        $this->info('Resumen sobre '.count($filas).' solicitudes sin persona:');
        $this->table(array('Accion', 'Cantidad'), $resRows);

        // ------------------------------------------------------------------
        // Aplicar
        // ------------------------------------------------------------------
        if (count($correcciones) === 0) {
            $this->line('');
            $this->warn('No hay correcciones inequivocas para aplicar.');
            return 0;
        }

        $nPersona = 0; $nSolicitud = 0; $nUser = 0; $errores = array();

        DB::beginTransaction();
        try {
            foreach ($correcciones as $c) {
                try {
                    if ($c['accion'] === 'PERSONA MAL') {
                        $nPersona += DB::table('personas')
                            ->where('id', $c['persona_id'])
                            ->update(array('cuil' => $c['cuil_solicitud'], 'updated_at' => now()));
                        if ($c['user_id'] !== null) {
                            $nUser += DB::table('users')
                                ->where('id', $c['user_id'])
                                ->update(array('cuil' => $c['cuil_solicitud'], 'updated_at' => now()));
                        }
                    } else {
                        $nSolicitud += DB::table('solicitud_sicadis')
                            ->where('id', $c['solicitud_id'])
                            ->update(array('cuil' => $c['cuil_persona'], 'updated_at' => now()));
                    }
                } catch (\Exception $e) {
                    $errores[] = array($c['solicitud_id'], $c['persona_id'], $this->corta($e->getMessage(), 70));
                }
            }

            $this->line('');
            $this->info('Filas afectadas:');
            $this->table(
                array('Operacion', 'Filas'),
                array(
                    array('personas.cuil corregido',          $nPersona),
                    array('users.cuil corregido (acceso)',    $nUser),
                    array('solicitud_sicadis.cuil corregido',  $nSolicitud),
                )
            );

            if (count($errores) > 0) {
                $this->warn('Filas con error (no aplicadas): '.count($errores));
                $this->table(array('Sol.', 'Persona', 'Error'), $errores);
            }

            if ($commit) {
                DB::commit();
                $this->line('');
                $this->info('COMMIT: cambios persistidos.');
                $this->line('Ahora conviene correr: php artisan sicadi:comparar-categorias');
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

    private function agregar(&$arr, $clave)
    {
        if (!isset($arr[$clave])) {
            $arr[$clave] = 0;
        }
        $arr[$clave]++;
    }

    private function corta($v, $n)
    {
        $v = (string) $v;
        return mb_strlen($v) > $n ? mb_substr($v, 0, $n - 1).'.' : $v;
    }
}
