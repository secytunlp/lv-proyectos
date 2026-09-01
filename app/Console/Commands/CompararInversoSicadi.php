<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Control INVERSO de sicadi:comparar-categorias.
 *
 * comparar-categorias arranca desde solicitud_sicadis y hace LEFT JOIN hacia
 * investigadors: si no hay solicitud, el investigador no se evalua nunca. Por
 * eso una categoria cargada en investigadors sin respaldo en solicitud_sicadis
 * pasa todos sus controles sin aparecer (caso PERALTA, 2026-08-31).
 *
 * Este comando arranca al reves: desde investigadors con categoria, y busca los
 * que no tienen una solicitud Otorgada con categoria asignada detras.
 *
 *   SIN SOLICITUD            no hay ninguna solicitud con ese CUIL
 *   SOLICITUD NO OTORGADA    hay solicitud(es), ninguna Otorgada
 *   OTORGADA SIN CATEGORIA   hay Otorgada pero con categoria_asignada vacia
 *
 * Para cada caso cuenta ademas en cuantas filas de integrantes e
 * integrante_estados vive la MISMA categoria: son las que la reponen al primer
 * tramite que se apruebe, via IntegranteController::actualizarInvestigador().
 *
 * Con --origen consulta cd_categoriasicadi en la base de SICyT. Si el valor
 * tambien esta ahi, limpiar produccion no alcanza: sync:investigadors y
 * sync:integrantes lo vuelven a escribir. El orden correcto es origen primero.
 *
 * Solo lee: no modifica nada.
 */
class CompararInversoSicadi extends Command
{
    protected $signature = 'sicadi:comparar-inverso
        {--cuil= : Filtrar por un CUIL puntual}
        {--solo= : Mostrar solo los diagnosticos que contengan este texto (ej: "SIN SOLICITUD")}
        {--incluir-sc : Incluir tambien los investigadores en s/c}
        {--origen : Consultar cd_categoriasicadi en la base de SICyT (conexion mysql_origen)}
        {--limite=0 : Cortar el listado en N filas (0 = sin limite)}';

    protected $description = 'Investigadores con categoria SICADI sin respaldo en solicitud_sicadis (inverso de sicadi:comparar-categorias)';

    /** Expresion SQL para normalizar un CUIL */
    private function cuilNorm($col)
    {
        return "REPLACE(REPLACE(REPLACE(".$col.", '-', ''), '.', ''), ' ', '')";
    }

    public function handle()
    {
        $cuil       = $this->option('cuil');
        $solo       = $this->option('solo');
        $incluirSc  = (bool) $this->option('incluir-sc');
        $conOrigen  = (bool) $this->option('origen');
        $limite     = (int) $this->option('limite');

        $sql =
            'SELECT '.
            '  i.id                 AS investigador_id, '.
            '  p.cuil               AS cuil, '.
            "  TRIM(CONCAT(COALESCE(p.apellido, ''), ', ', COALESCE(p.nombre, ''))) AS persona, ".
            '  f.nombre             AS ua, '.
            '  si.nombre            AS cat_investigador, '.
            '  pv.detalle           AS pivot_detalle, '.
            '  sol.total            AS sol_total, '.
            '  sol.otorgadas        AS sol_otorgadas, '.
            '  sol.con_categoria    AS sol_con_categoria, '.
            '  sol.estados          AS sol_estados, '.
            '  ints.n               AS int_filas, '.
            '  ests.n               AS est_filas '.
            'FROM investigadors i '.
            'JOIN personas p  ON p.id  = i.persona_id '.
            'JOIN sicadis  si ON si.id = i.sicadi_id '.
            'LEFT JOIN facultads f ON f.id = i.facultad_id '.

            // --- solicitudes de esa persona, agregadas por CUIL normalizado ---
            'LEFT JOIN ( '.
            '   SELECT '.$this->cuilNorm('s.cuil').' AS k, '.
            '          COUNT(*) AS total, '.
            "          SUM(UPPER(TRIM(s.estado)) = 'OTORGADA') AS otorgadas, ".
            "          SUM(UPPER(TRIM(s.estado)) = 'OTORGADA' ".
            '               AND s.categoria_asignada IS NOT NULL '.
            "               AND TRIM(s.categoria_asignada) <> '') AS con_categoria, ".
            "          GROUP_CONCAT(DISTINCT CONCAT(s.estado, ' ', COALESCE(c.year, '?')) SEPARATOR ' | ') AS estados ".
            '   FROM solicitud_sicadis s '.
            '   LEFT JOIN sicadi_convocatorias c ON c.id = s.convocatoria_id '.
            '   GROUP BY k '.
            ') sol ON sol.k = '.$this->cuilNorm('p.cuil').' '.

            // --- pivot marcado como actual ---
            'LEFT JOIN ( '.
            '   SELECT isd.investigador_id, '.
            "          GROUP_CONCAT(DISTINCT CONCAT(COALESCE(sp.nombre, '?'), ' (', COALESCE(isd.year, 's/a'), ')') SEPARATOR ' | ') AS detalle ".
            '   FROM investigador_sicadis isd '.
            '   LEFT JOIN sicadis sp ON sp.id = isd.sicadi_id '.
            '   WHERE isd.actual = 1 '.
            '   GROUP BY isd.investigador_id '.
            ') pv ON pv.investigador_id = i.id '.

            // --- integrantes con la MISMA categoria (la reponen) ---
            'LEFT JOIN ( '.
            '   SELECT investigador_id, sicadi_id, COUNT(*) AS n '.
            '   FROM integrantes WHERE sicadi_id IS NOT NULL '.
            '   GROUP BY investigador_id, sicadi_id '.
            ') ints ON ints.investigador_id = i.id AND ints.sicadi_id = i.sicadi_id '.

            // --- integrante_estados con la MISMA categoria ---
            'LEFT JOIN ( '.
            '   SELECT ig.investigador_id, ie.sicadi_id, COUNT(*) AS n '.
            '   FROM integrante_estados ie '.
            '   JOIN integrantes ig ON ig.id = ie.integrante_id '.
            '   WHERE ie.sicadi_id IS NOT NULL '.
            '   GROUP BY ig.investigador_id, ie.sicadi_id '.
            ') ests ON ests.investigador_id = i.id AND ests.sicadi_id = i.sicadi_id '.

            'WHERE i.sicadi_id IS NOT NULL ';

        $bind = array();

        if (!$incluirSc) {
            $sql .= 'AND i.sicadi_id <> 1 ';
        }
        if ($cuil !== null && $cuil !== '') {
            $sql .= 'AND '.$this->cuilNorm('p.cuil').' = '.$this->cuilNorm('?').' ';
            $bind[] = $cuil;
        }

        // El corazon del control: nada que respalde la categoria
        $sql .=
            'AND ( sol.total IS NULL '.
            '   OR sol.otorgadas = 0 '.
            '   OR sol.con_categoria = 0 ) '.
            'ORDER BY persona';

        $filas = DB::select($sql, $bind);

        if (count($filas) === 0) {
            $this->info('Sin hallazgos: toda categoria cargada en investigadors tiene una solicitud Otorgada con categoria detras.');
            return 0;
        }

        // ------------------------------------------------------------------
        // Origen (opcional)
        // ------------------------------------------------------------------
        $origenDoc = array();
        $origenInt = array();
        $origenOk  = false;

        if ($conOrigen) {
            $ids = array();
            foreach ($filas as $f) {
                $ids[] = (int) $f->investigador_id;
            }

            try {
                $rows = DB::connection('mysql_origen')
                    ->table('docente')
                    ->whereIn('cd_docente', $ids)
                    ->whereNotNull('cd_categoriasicadi')
                    ->select('cd_docente', 'cd_categoriasicadi')
                    ->get();
                foreach ($rows as $r) {
                    if ((int) $r->cd_categoriasicadi !== 0) {
                        $origenDoc[(int) $r->cd_docente] = (int) $r->cd_categoriasicadi;
                    }
                }

                $rows = DB::connection('mysql_origen')
                    ->table('integrante')
                    ->whereIn('cd_docente', $ids)
                    ->whereNotNull('cd_categoriasicadi')
                    ->select('cd_docente', DB::raw('COUNT(*) AS n'))
                    ->groupBy('cd_docente')
                    ->get();
                foreach ($rows as $r) {
                    $origenInt[(int) $r->cd_docente] = (int) $r->n;
                }

                $origenOk = true;
            } catch (\Exception $e) {
                $this->warn('No se pudo consultar mysql_origen: '.$e->getMessage());
                $this->warn('El listado sigue, pero sin las columnas de origen.');
            }
        }

        // ------------------------------------------------------------------
        // Clasificacion
        // ------------------------------------------------------------------
        $rowsOut  = array();
        $resumen  = array();
        $conRepo  = 0;   // con filas que la reponen (integrantes/estados/origen)

        foreach ($filas as $f) {
            if ($f->sol_total === null) {
                $diagnostico = 'SIN SOLICITUD';
            } elseif ((int) $f->sol_otorgadas === 0) {
                $diagnostico = 'SOLICITUD NO OTORGADA';
            } else {
                $diagnostico = 'OTORGADA SIN CATEGORIA';
            }

            if (!isset($resumen[$diagnostico])) {
                $resumen[$diagnostico] = 0;
            }
            $resumen[$diagnostico]++;

            $intN = (int) $f->int_filas;
            $estN = (int) $f->est_filas;
            $inv  = (int) $f->investigador_id;

            $enOrigen    = isset($origenDoc[$inv]);
            $enOrigenInt = isset($origenInt[$inv]) ? $origenInt[$inv] : 0;

            if ($intN > 0 || $estN > 0 || $enOrigen || $enOrigenInt > 0) {
                $conRepo++;
            }

            if ($solo !== null && $solo !== '' && stripos($diagnostico, $solo) === false) {
                continue;
            }

            $fila = array(
                $inv,
                $f->cuil,
                $this->corta($f->persona, 30),
                $this->corta($f->ua, 14),
                $f->cat_investigador,
                $f->pivot_detalle === null ? '-' : $f->pivot_detalle,
                $f->sol_estados === null ? '(ninguna)' : $this->corta($f->sol_estados, 34),
                $intN === 0 ? '-' : $intN,
                $estN === 0 ? '-' : $estN,
            );

            if ($origenOk) {
                $fila[] = $enOrigen ? ('SI ('.$origenDoc[$inv].')') : '-';
                $fila[] = $enOrigenInt === 0 ? '-' : $enOrigenInt;
            }

            $fila[] = $diagnostico;
            $rowsOut[] = $fila;
        }

        $recortado = false;
        if ($limite > 0 && count($rowsOut) > $limite) {
            $rowsOut = array_slice($rowsOut, 0, $limite);
            $recortado = true;
        }

        if (count($rowsOut) > 0) {
            $cab = array('Inv.', 'CUIL', 'Persona', 'U. Acad.', 'Categoria', 'Pivot (actual)', 'Solicitudes', 'Int.', 'Est.');
            if ($origenOk) {
                $cab[] = 'Origen';
                $cab[] = 'Or.int';
            }
            $cab[] = 'Diagnostico';

            $this->table($cab, $rowsOut);
            if ($recortado) {
                $this->warn('Listado recortado a '.$limite.' filas (usa --limite=0 para verlas todas).');
            }
        } else {
            $this->info('Sin filas para mostrar con ese --solo.');
        }

        // ------------------------------------------------------------------
        // Resumen
        // ------------------------------------------------------------------
        arsort($resumen);
        $resRows = array();
        foreach ($resumen as $diag => $cant) {
            $resRows[] = array($diag, $cant);
        }

        $this->line('');
        $this->info('Investigadores con categoria sin respaldo en solicitud_sicadis:');
        $this->table(array('Diagnostico', 'Cantidad'), $resRows);
        $this->line('Total: '.count($filas));

        // ------------------------------------------------------------------
        // Ayuda de lectura
        // ------------------------------------------------------------------
        $this->line('');
        $this->line('Int. / Est. = filas de integrantes e integrante_estados con la MISMA categoria.');
        $this->line('Si tienen filas, limpiar investigadors no alcanza: IntegranteController::actualizarInvestigador()');
        $this->line('la repone al primer tramite que se apruebe, y ademas recrea la fila del pivot.');

        if ($origenOk) {
            $this->line('');
            $this->line('Origen = cd_categoriasicadi en la base de SICyT. Si dice SI, el sync la vuelve a escribir:');
            $this->line('hay que corregir el origen PRIMERO y despues produccion.');
        } else {
            $this->line('');
            $this->line('Corre con --origen para ver si la categoria tambien esta en la base de SICyT.');
        }

        $this->line('');
        $this->line('SOLICITUD NO OTORGADA con solicitudes de 2025/2026 en tramite suele ser un falso positivo:');
        $this->line('mira la columna Solicitudes antes de tocar nada.');

        if ($conRepo > 0) {
            $this->line('');
            $this->warn($conRepo.' de '.count($filas).' tienen la categoria replicada en integrantes, estados u origen.');
        }

        return 0;
    }

    private function corta($v, $n)
    {
        $v = (string) $v;
        return mb_strlen($v) > $n ? mb_substr($v, 0, $n - 1).'.' : $v;
    }
}
