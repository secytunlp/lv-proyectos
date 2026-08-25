<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Compara, para cada solicitud SICADI Otorgada con categoria asignada, las
 * tres fuentes:
 *
 *   1) solicitud_sicadis.categoria_asignada   (lo que dice la solicitud)
 *   2) investigadors.sicadi_id                (la categoria vigente del investigador)
 *   3) investigador_sicadis (actual = 1)      (el historial marcado como actual)
 *
 * El cruce con personas se hace por CUIL normalizado (sin guiones, puntos ni
 * espacios), porque las solicitudes importadas pueden traerlo en cualquiera de
 * los dos formatos.
 *
 * Solo lee: no modifica nada.
 */
class CompararCategoriasSicadi extends Command
{
    protected $signature = 'sicadi:comparar-categorias
        {--year= : Filtrar por año de la convocatoria (ej: 2024)}
        {--cuil= : Filtrar por un CUIL puntual}
        {--estado=Otorgada : Estado de la solicitud a considerar (vacio = todos los estados)}
        {--solo= : Mostrar solo los diagnosticos que contengan este texto (ej: "SIN INVESTIGADOR")}
        {--todos : Listar tambien las filas donde las tres fuentes coinciden}
        {--limite=0 : Cortar el listado en N filas (0 = sin limite)}';

    protected $description = 'Compara categoria_asignada de solicitudes Otorgadas contra investigadors.sicadi_id e investigador_sicadis (actual)';

    /** Valores que significan "sin categoria" */
    private $sinCategoria = array('', 'S/C', 'SC', 'S/D', 'SIN CATEGORIA', 'SIN CATEGORÍA', '-', 'NO POSEE');

    /** Expresion SQL para normalizar un CUIL */
    private function cuilNorm($col)
    {
        return "REPLACE(REPLACE(REPLACE(".$col.", '-', ''), '.', ''), ' ', '')";
    }

    public function handle()
    {
        $year   = $this->option('year');
        $cuil   = $this->option('cuil');
        $estado = $this->option('estado');
        $solo   = $this->option('solo');
        $todos  = (bool) $this->option('todos');
        $limite = (int) $this->option('limite');

        $sql =
            'SELECT '.
            '  s.id                 AS solicitud_id, '.
            '  s.cuil               AS cuil, '.
            '  s.estado             AS sol_estado, '.
            "  TRIM(CONCAT(COALESCE(p.apellido, s.apellido, ''), ', ', COALESCE(p.nombre, s.nombre, ''))) AS persona, ".
            '  COALESCE(s.presentacion_ua, s.cargo_ua) AS ua, '.
            '  c.tipo               AS conv_tipo, '.
            '  c.year               AS conv_year, '.
            '  s.categoria_asignada AS cat_solicitud, '.
            '  p.id                 AS persona_id, '.
            '  i.id                 AS investigador_id, '.
            '  si.nombre            AS cat_investigador, '.
            '  pv.actuales          AS pivot_actuales, '.
            '  pv.detalle           AS pivot_detalle, '.
            '  pv.nombres           AS pivot_nombres '.
            'FROM solicitud_sicadis s '.
            'LEFT JOIN sicadi_convocatorias c ON c.id = s.convocatoria_id '.
            'LEFT JOIN personas p            ON '.$this->cuilNorm('p.cuil').' = '.$this->cuilNorm('s.cuil').' '.
            'LEFT JOIN investigadors i       ON i.persona_id = p.id '.
            'LEFT JOIN sicadis si            ON si.id = i.sicadi_id '.
            'LEFT JOIN ( '.
            '   SELECT isd.investigador_id, '.
            '          COUNT(*) AS actuales, '.
            "          GROUP_CONCAT(DISTINCT CONCAT(COALESCE(sp.nombre, '?'), ' (', COALESCE(isd.year, 's/a'), ')') SEPARATOR ' | ') AS detalle, ".
            "          GROUP_CONCAT(DISTINCT UPPER(TRIM(COALESCE(sp.nombre, ''))) SEPARATOR '|') AS nombres ".
            '   FROM investigador_sicadis isd '.
            '   LEFT JOIN sicadis sp ON sp.id = isd.sicadi_id '.
            '   WHERE isd.actual = 1 '.
            '   GROUP BY isd.investigador_id '.
            ') pv ON pv.investigador_id = i.id '.
            "WHERE s.categoria_asignada IS NOT NULL AND TRIM(s.categoria_asignada) <> '' ";

        $bind = array();
        if ($estado !== null && $estado !== '') {
            $sql .= 'AND UPPER(TRIM(s.estado)) = UPPER(?) ';
            $bind[] = trim($estado);
        }
        if ($year !== null && $year !== '') {
            $sql .= 'AND c.year = ? ';
            $bind[] = $year;
        }
        if ($cuil !== null && $cuil !== '') {
            $sql .= 'AND '.$this->cuilNorm('s.cuil').' = '.$this->cuilNorm('?').' ';
            $bind[] = $cuil;
        }
        $sql .= 'ORDER BY persona, c.year';

        $filas = DB::select($sql, $bind);

        if (count($filas) === 0) {
            $this->warn('No hay solicitudes '.($estado ? '"'.$estado.'" ' : '').'con categoria asignada para ese filtro.');
            return 0;
        }

        // Cuando no se filtra por estado, se agrega una columna con el estado real
        $mostrarEstadoSol = ($estado === null || $estado === '');

        $rows    = array();
        $resumen = array();
        $porYear = array();
        $conDif  = 0;

        foreach ($filas as $f) {
            $solicitud    = $this->norm($f->cat_solicitud);
            $investigador = $this->norm($f->cat_investigador);
            $pivots       = $this->pivots($f->pivot_nombres);
            $actuales     = (int) $f->pivot_actuales;

            $marcas = array();

            if ($f->persona_id === null) {
                $marcas[] = 'SIN PERSONA';
            } elseif ($f->investigador_id === null) {
                $marcas[] = 'SIN INVESTIGADOR';
            } else {
                // --- investigadors.sicadi_id ---
                if ($this->esVacia($investigador)) {
                    $marcas[] = 'INV SIN CATEGORIA';
                } elseif ($solicitud !== $investigador) {
                    $marcas[] = 'SOLIC <> INV';
                }

                // --- investigador_sicadis (actual = 1) ---
                if ($actuales === 0) {
                    $marcas[] = 'SIN PIVOT ACTUAL';
                } elseif ($actuales > 1) {
                    $marcas[] = 'PIVOT DUPLICADO ('.$actuales.')';
                    if (!in_array($solicitud, $pivots, true)) {
                        $marcas[] = 'SOLIC <> PIVOT';
                    }
                } else {
                    $pivot = $pivots[0];
                    if ($this->esVacia($pivot)) {
                        $marcas[] = 'PIVOT SIN CATEGORIA';
                    } else {
                        if (!$this->esVacia($investigador) && $pivot !== $investigador) {
                            $marcas[] = 'INV <> PIVOT';
                        }
                        if ($solicitud !== $pivot) {
                            $marcas[] = 'SOLIC <> PIVOT';
                        }
                    }
                }
            }

            $diagnostico = count($marcas) === 0 ? 'OK' : implode(' + ', $marcas);

            if (!isset($resumen[$diagnostico])) {
                $resumen[$diagnostico] = 0;
            }
            $resumen[$diagnostico]++;

            if ($diagnostico !== 'OK') {
                $conDif++;
            }

            $y = ($f->conv_year === null || $f->conv_year === '') ? '(sin convocatoria)' : (string) $f->conv_year;
            if (!isset($porYear[$y])) {
                $porYear[$y] = array('total' => 0, 'dif' => 0);
            }
            $porYear[$y]['total']++;
            if ($diagnostico !== 'OK') {
                $porYear[$y]['dif']++;
            }

            // --- filtros de listado ---
            if ($diagnostico === 'OK' && !$todos) {
                continue;
            }
            if ($solo !== null && $solo !== '' && stripos($diagnostico, $solo) === false) {
                continue;
            }

            $fila = array(
                $f->solicitud_id,
                $f->cuil,
                $this->corta($f->persona, 30),
                $this->corta($f->ua, 14),
                trim($f->conv_tipo.' '.$f->conv_year),
                $f->cat_solicitud,
                $f->cat_investigador === null ? '-' : $f->cat_investigador,
                $f->pivot_detalle === null ? '-' : $f->pivot_detalle,
            );
            if ($mostrarEstadoSol) {
                $fila[] = $f->sol_estado;
            }
            $fila[] = $diagnostico;

            $rows[] = $fila;
        }

        $recortado = false;
        if ($limite > 0 && count($rows) > $limite) {
            $rows = array_slice($rows, 0, $limite);
            $recortado = true;
        }

        if (count($rows) > 0) {
            $cab = array('Sol.', 'CUIL', 'Persona', 'U. Acad.', 'Convocatoria', 'Solicitud', 'Investigador', 'Pivot (actual)');
            if ($mostrarEstadoSol) {
                $cab[] = 'Est. sol.';
            }
            $cab[] = 'Diagnostico';

            $this->table($cab, $rows);
            if ($recortado) {
                $this->warn('Listado recortado a '.$limite.' filas (usa --limite=0 para verlas todas).');
            }
        } else {
            $this->info('Sin filas para mostrar con esos filtros.');
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
        $this->info(
            'Resumen sobre '.count($filas).' solicitudes '.
            ($mostrarEstadoSol ? '(todos los estados)' : '"'.$estado.'"').
            ' con categoria asignada:'
        );
        $this->table(array('Diagnostico', 'Cantidad'), $resRows);
        $this->line('Con alguna diferencia: '.$conDif);

        // ------------------------------------------------------------------
        // Desglose por año de convocatoria (para ver que no se mira solo un año)
        // ------------------------------------------------------------------
        ksort($porYear);
        $yearRows = array();
        foreach ($porYear as $y => $d) {
            $yearRows[] = array(
                $y,
                $d['total'],
                $d['total'] - $d['dif'],
                $d['dif'],
                $d['total'] > 0 ? round($d['dif'] * 100 / $d['total'], 1).'%' : '-',
            );
        }
        $this->line('');
        $this->info('Por año de convocatoria:');
        $this->table(array('Año', 'Solicitudes', 'OK', 'Con diferencia', '%'), $yearRows);

        return 0;
    }

    private function pivots($concat)
    {
        if ($concat === null || $concat === '') {
            return array('');
        }
        return explode('|', $concat);
    }

    private function esVacia($cat)
    {
        return in_array($cat, $this->sinCategoria, true);
    }

    private function norm($v)
    {
        return $v === null ? '' : strtoupper(trim($v));
    }

    private function corta($v, $n)
    {
        $v = (string) $v;
        return mb_strlen($v) > $n ? mb_substr($v, 0, $n - 1).'.' : $v;
    }
}
