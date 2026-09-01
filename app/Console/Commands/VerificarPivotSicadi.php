<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Verifica las reglas 3 y 4 del modelo, sin mirar solicitud_sicadis:
 *
 *   3) investigador_sicadis lleva UNA FILA POR CONVOCATORIA/AÑO por investigador.
 *   4) La categoria actual es investigadors.sicadi_id, y la fila del pivot con
 *      actual = 1 tiene que coincidir con ella.
 *
 * Es el tercer control, complementario de los otros dos:
 *
 *   comparar-categorias  solicitudes -> investigadors   (solo Otorgadas)
 *   comparar-inverso     investigadors -> solicitudes
 *   verificar-pivot      investigadors <-> pivot        (no mira solicitudes)
 *
 * Los dos primeros solo ven a quien tiene solicitud, asi que una desincronizacion
 * entre investigadors y el pivot en alguien sin solicitud Otorgada es invisible
 * para ambos. Ademas el bug 2 (IntegranteController::actualizarInvestigador():3312,
 * updateOrInsert con actual=1 sin desmarcar las anteriores) sigue generando
 * duplicados cada vez que se aprueba un alta o cambio de integrante: este comando
 * es el que hay que correr periodicamente mientras ese bug siga sin corregir.
 *
 * Diagnosticos:
 *   SIN PIVOT           tiene categoria y ninguna fila en el pivot
 *   SIN ACTUAL          tiene filas pero ninguna marcada actual = 1
 *   ACTUAL DUPLICADO    mas de una fila con actual = 1        <- bug 2
 *   INV <> PIVOT        la fila actual no coincide con investigadors.sicadi_id
 *   PIVOT SIN INV       hay pivot actual con categoria y el investigador esta sin categoria o en s/c
 *   ANIO DUPLICADO      mas de una fila para el mismo año     <- viola la regla 3
 *   SIN YEAR            alguna fila sin year (solo con --incluir-sin-year)
 *
 * Solo lee: no modifica nada.
 */
class VerificarPivotSicadi extends Command
{
    protected $signature = 'sicadi:verificar-pivot
        {--cuil= : Filtrar por un CUIL puntual}
        {--solo= : Mostrar solo los diagnosticos que contengan este texto (ej: "ACTUAL DUPLICADO")}
        {--incluir-sin-year : Marcar tambien las filas de pivot sin year}
        {--limite=0 : Cortar el listado en N filas (0 = sin limite)}';

    protected $description = 'Verifica que el pivot actual coincida con investigadors.sicadi_id y que haya una sola fila por año';

    private function cuilNorm($col)
    {
        return "REPLACE(REPLACE(REPLACE(".$col.", '-', ''), '.', ''), ' ', '')";
    }

    public function handle()
    {
        $cuil          = $this->option('cuil');
        $solo          = $this->option('solo');
        $incluirSinYear = (bool) $this->option('incluir-sin-year');
        $limite        = (int) $this->option('limite');

        $sql =
            'SELECT '.
            '  i.id        AS investigador_id, '.
            '  p.cuil      AS cuil, '.
            "  TRIM(CONCAT(COALESCE(p.apellido, ''), ', ', COALESCE(p.nombre, ''))) AS persona, ".
            '  f.nombre    AS ua, '.
            '  i.sicadi_id AS inv_sicadi_id, '.
            '  si.nombre   AS inv_cat, '.
            '  pv.filas          AS pv_filas, '.
            '  pv.actuales       AS pv_actuales, '.
            '  pv.sin_year       AS pv_sin_year, '.
            '  pv.years_distintos AS pv_years, '.
            '  pv.act_ids        AS pv_act_ids, '.
            '  pv.detalle        AS pv_detalle '.
            'FROM investigadors i '.
            'JOIN personas p ON p.id = i.persona_id '.
            'LEFT JOIN sicadis   si ON si.id = i.sicadi_id '.
            'LEFT JOIN facultads f  ON f.id  = i.facultad_id '.
            'LEFT JOIN ( '.
            '   SELECT isd.investigador_id, '.
            '          COUNT(*) AS filas, '.
            '          SUM(isd.actual = 1) AS actuales, '.
            '          SUM(isd.year IS NULL) AS sin_year, '.
            '          COUNT(DISTINCT COALESCE(isd.year, 0)) AS years_distintos, '.
            '          GROUP_CONCAT(DISTINCT CASE WHEN isd.actual = 1 THEN isd.sicadi_id END) AS act_ids, '.
            "          GROUP_CONCAT(CONCAT(COALESCE(sp.nombre, '?'), ' ', COALESCE(isd.year, 's/a'), ".
            "                              CASE WHEN isd.actual = 1 THEN '*' ELSE '' END) ".
            "                       ORDER BY isd.year DESC SEPARATOR ' | ') AS detalle ".
            '   FROM investigador_sicadis isd '.
            '   LEFT JOIN sicadis sp ON sp.id = isd.sicadi_id '.
            '   GROUP BY isd.investigador_id '.
            ') pv ON pv.investigador_id = i.id '.
            // solo interesan los que tienen algo de alguno de los dos lados
            'WHERE ( pv.investigador_id IS NOT NULL '.
            '     OR (i.sicadi_id IS NOT NULL AND i.sicadi_id <> 1) ) ';

        $bind = array();
        if ($cuil !== null && $cuil !== '') {
            $sql .= 'AND '.$this->cuilNorm('p.cuil').' = '.$this->cuilNorm('?').' ';
            $bind[] = $cuil;
        }
        $sql .= 'ORDER BY persona';

        $filas = DB::select($sql, $bind);

        $rowsOut   = array();
        $resumen   = array();
        $conDif    = 0;
        $revisados = 0;

        foreach ($filas as $f) {
            $revisados++;

            $invId    = (int) $f->inv_sicadi_id;          // 0 si NULL
            $invTiene = ($invId !== 0 && $invId !== 1);   // 1 = s/c
            $pvFilas  = (int) $f->pv_filas;
            $actuales = (int) $f->pv_actuales;

            $actIds = array();
            if ($f->pv_act_ids !== null && $f->pv_act_ids !== '') {
                foreach (explode(',', $f->pv_act_ids) as $v) {
                    $actIds[] = (int) $v;
                }
            }

            $marcas = array();

            // --- regla 4 ---
            if ($pvFilas === 0) {
                if ($invTiene) {
                    $marcas[] = 'SIN PIVOT';
                }
            } elseif ($actuales === 0) {
                if ($invTiene) {
                    $marcas[] = 'SIN ACTUAL';
                }
            } elseif ($actuales > 1) {
                $marcas[] = 'ACTUAL DUPLICADO ('.$actuales.')';
                if ($invTiene && !in_array($invId, $actIds, true)) {
                    $marcas[] = 'INV <> PIVOT';
                }
            } else {
                $actId = $actIds[0];
                if ($invTiene) {
                    if ($actId !== $invId) {
                        $marcas[] = 'INV <> PIVOT';
                    }
                } elseif ($actId !== 1) {
                    // el pivot afirma una categoria real y el investigador no la tiene
                    $marcas[] = 'PIVOT SIN INV';
                }
            }

            // --- regla 3 ---
            if ($pvFilas > (int) $f->pv_years) {
                $marcas[] = 'ANIO DUPLICADO';
            }
            if ($incluirSinYear && (int) $f->pv_sin_year > 0) {
                $marcas[] = 'SIN YEAR ('.(int) $f->pv_sin_year.')';
            }

            if (count($marcas) === 0) {
                continue;
            }

            $diagnostico = implode(' + ', $marcas);

            if (!isset($resumen[$diagnostico])) {
                $resumen[$diagnostico] = 0;
            }
            $resumen[$diagnostico]++;
            $conDif++;

            if ($solo !== null && $solo !== '' && stripos($diagnostico, $solo) === false) {
                continue;
            }

            $rowsOut[] = array(
                (int) $f->investigador_id,
                $f->cuil,
                $this->corta($f->persona, 30),
                $this->corta($f->ua, 14),
                $f->inv_cat === null ? '(sin)' : $f->inv_cat,
                $f->pv_detalle === null ? '-' : $this->corta($f->pv_detalle, 42),
                $diagnostico,
            );
        }

        if ($conDif === 0) {
            $this->info('Pivot consistente: '.$revisados.' investigadores revisados, ninguna diferencia.');
            $this->line('Toda fila actual = 1 coincide con investigadors.sicadi_id y no hay años repetidos.');
            return 0;
        }

        $recortado = false;
        if ($limite > 0 && count($rowsOut) > $limite) {
            $rowsOut = array_slice($rowsOut, 0, $limite);
            $recortado = true;
        }

        if (count($rowsOut) > 0) {
            $this->table(
                array('Inv.', 'CUIL', 'Persona', 'U. Acad.', 'Investigador', 'Pivot (* = actual)', 'Diagnostico'),
                $rowsOut
            );
            if ($recortado) {
                $this->warn('Listado recortado a '.$limite.' filas (usa --limite=0 para verlas todas).');
            }
        } else {
            $this->info('Sin filas para mostrar con ese --solo.');
        }

        arsort($resumen);
        $resRows = array();
        foreach ($resumen as $diag => $cant) {
            $resRows[] = array($diag, $cant);
        }

        $this->line('');
        $this->info('Sobre '.$revisados.' investigadores con categoria o con pivot:');
        $this->table(array('Diagnostico', 'Cantidad'), $resRows);
        $this->line('Con alguna diferencia: '.$conDif);

        $this->line('');
        $this->line('ACTUAL DUPLICADO es la firma del bug 2 (IntegranteController:3312): se marca actual=1');
        $this->line('sin desmarcar las anteriores al aprobar un alta o cambio de integrante.');
        $this->line('INV <> PIVOT y SIN ACTUAL se corrigen con sicadi:aplicar-otorgadas --year=<año> --commit,');
        $this->line('que deja una sola fila actual por investigador. ANIO DUPLICADO: sicadi:corregir-years-pivot.');

        if (!$incluirSinYear) {
            $this->line('');
            $this->line('Las filas de pivot sin year no se marcan. Usa --incluir-sin-year para verlas');
            $this->line('(al 2026-09 son ~69, todas s/c de solicitudes 2025/2026 en tramite: esperadas).');
        }

        return 0;
    }

    private function corta($v, $n)
    {
        $v = (string) $v;
        return mb_strlen($v) > $n ? mb_substr($v, 0, $n - 1).'.' : $v;
    }
}
