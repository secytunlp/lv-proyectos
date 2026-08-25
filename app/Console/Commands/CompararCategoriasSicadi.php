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
        {--todos : Listar tambien las filas donde las tres fuentes coinciden}
        {--limite=0 : Cortar el listado en N filas (0 = sin limite)}';

    protected $description = 'Compara categoria_asignada de solicitudes Otorgadas contra investigadors.sicadi_id e investigador_sicadis (actual)';

    /** Expresion SQL para normalizar un CUIL */
    private function cuilNorm($col)
    {
        return "REPLACE(REPLACE(REPLACE(".$col.", '-', ''), '.', ''), ' ', '')";
    }

    public function handle()
    {
        $year    = $this->option('year');
        $cuil    = $this->option('cuil');
        $estado  = $this->option('estado');
        $todos   = (bool) $this->option('todos');
        $limite  = (int) $this->option('limite');

        $sql =
            'SELECT '.
            '  s.id                 AS solicitud_id, '.
            '  s.cuil               AS cuil, '.
            '  s.estado             AS sol_estado, '.
            "  TRIM(CONCAT(COALESCE(p.apellido, s.apellido, ''), ', ', COALESCE(p.nombre, s.nombre, ''))) AS persona, ".
            '  c.tipo               AS conv_tipo, '.
            '  c.year               AS conv_year, '.
            '  s.categoria_solicitada AS cat_solicitada, '.
            '  s.categoria_asignada AS cat_solicitud, '.
            '  i.id                 AS investigador_id, '.
            '  si.nombre            AS cat_investigador, '.
            '  sp.nombre            AS cat_pivot, '.
            '  isd.year             AS pivot_year, '.
            '  (SELECT COUNT(*) FROM investigador_sicadis x '.
            '     WHERE x.investigador_id = i.id AND x.actual = 1) AS pivot_actuales '.
            'FROM solicitud_sicadis s '.
            'LEFT JOIN sicadi_convocatorias c ON c.id = s.convocatoria_id '.
            'LEFT JOIN personas p            ON '.$this->cuilNorm('p.cuil').' = '.$this->cuilNorm('s.cuil').' '.
            'LEFT JOIN investigadors i       ON i.persona_id = p.id '.
            'LEFT JOIN sicadis si            ON si.id = i.sicadi_id '.
            'LEFT JOIN investigador_sicadis isd ON isd.investigador_id = i.id AND isd.actual = 1 '.
            'LEFT JOIN sicadis sp            ON sp.id = isd.sicadi_id '.
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

        $rows     = array();
        $resumen  = array();
        $conDif   = 0;

        foreach ($filas as $f) {
            $solicitud    = $this->norm($f->cat_solicitud);
            $investigador = $this->norm($f->cat_investigador);
            $pivot        = $this->norm($f->cat_pivot);

            $marcas = array();

            if ($f->investigador_id === null) {
                $marcas[] = 'SIN INVESTIGADOR';
            } else {
                if ($investigador === '') {
                    $marcas[] = 'INV SIN CATEGORIA';
                } elseif ($solicitud !== $investigador) {
                    $marcas[] = 'SOLIC <> INV';
                }

                if ((int) $f->pivot_actuales === 0) {
                    $marcas[] = 'SIN PIVOT ACTUAL';
                } else {
                    if ((int) $f->pivot_actuales > 1) {
                        $marcas[] = 'PIVOT DUPLICADO ('.$f->pivot_actuales.')';
                    }
                    if ($investigador !== '' && $pivot !== $investigador) {
                        $marcas[] = 'INV <> PIVOT';
                    }
                    if ($solicitud !== $pivot) {
                        $marcas[] = 'SOLIC <> PIVOT';
                    }
                }
            }

            $diagnostico = count($marcas) === 0 ? 'OK' : implode(' + ', $marcas);

            if (!isset($resumen[$diagnostico])) {
                $resumen[$diagnostico] = 0;
            }
            $resumen[$diagnostico]++;

            if ($diagnostico === 'OK') {
                if (!$todos) {
                    continue;
                }
            } else {
                $conDif++;
            }

            $fila = array(
                $f->solicitud_id,
                $f->cuil,
                $this->corta($f->persona, 32),
                trim($f->conv_tipo.' '.$f->conv_year),
                $f->cat_solicitud,
                $f->cat_investigador === null ? '-' : $f->cat_investigador,
                $f->cat_pivot === null ? '-' : $f->cat_pivot.($f->pivot_year ? ' ('.$f->pivot_year.')' : ''),
            );
            if ($mostrarEstadoSol) {
                $fila[] = $f->sol_estado;
            }
            $fila[] = $diagnostico;

            $rows[] = $fila;
        }

        if ($limite > 0 && count($rows) > $limite) {
            $rows = array_slice($rows, 0, $limite);
            $recortado = true;
        } else {
            $recortado = false;
        }

        if (count($rows) > 0) {
            $cab = array('Sol.', 'CUIL', 'Persona', 'Convocatoria', 'Solicitud', 'Investigador', 'Pivot (actual)');
            if ($mostrarEstadoSol) {
                $cab[] = 'Est. sol.';
            }
            $cab[] = 'Diagnostico';

            $this->table($cab, $rows);
            if ($recortado) {
                $this->warn('Listado recortado a '.$limite.' filas (usa --limite=0 para verlas todas).');
            }
        } else {
            $this->info('Sin diferencias para mostrar (usa --todos para ver tambien las coincidencias).');
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

        return 0;
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
