<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Para cada solicitud SICADI Otorgada cuyo CUIL no encuentra fila en personas,
 * intenta averiguar por que:
 *
 *   - MISMO DNI, OTRO CUIL  -> la persona existe pero con otro prefijo o con el
 *                              CUIL mal cargado (caso tipico 20/23/24/27).
 *   - MISMO NOMBRE          -> hay alguien con ese apellido y nombre, con otro
 *                              documento (o sin documento cargado).
 *   - APELLIDO PARECIDO     -> solo coincide el apellido; hay que mirarlo a mano.
 *   - NO ENCONTRADA         -> no esta en el sistema.
 *
 * Ademas informa si la persona hallada ya tiene investigador.
 *
 * Solo lee: no modifica nada.
 */
class DiagnosticarSinPersonaSicadi extends Command
{
    protected $signature = 'sicadi:diagnosticar-sin-persona
        {--year= : Filtrar por año de la convocatoria}
        {--estado=Otorgada : Estado de la solicitud a considerar}
        {--limite=0 : Cortar el listado en N filas (0 = sin limite)}';

    protected $description = 'Averigua por que los CUIL de solicitudes Otorgadas no encuentran persona';

    private function cuilNorm($col)
    {
        return "REPLACE(REPLACE(REPLACE(".$col.", '-', ''), '.', ''), ' ', '')";
    }

    public function handle()
    {
        $year   = $this->option('year');
        $estado = $this->option('estado');
        $limite = (int) $this->option('limite');

        // La columna documento existe en el $fillable del modelo pero no en
        // todas las bases: se usa solo si esta.
        $hayDocumento = Schema::hasColumn('solicitud_sicadis', 'documento');

        $sql =
            'SELECT '.
            '  s.id                 AS solicitud_id, '.
            '  s.cuil               AS cuil, '.
            ($hayDocumento ? '  s.documento          AS documento, ' : '  NULL AS documento, ').
            '  s.apellido           AS apellido, '.
            '  s.nombre             AS nombre, '.
            '  COALESCE(s.presentacion_ua, s.cargo_ua) AS ua, '.
            '  c.tipo               AS conv_tipo, '.
            '  c.year               AS conv_year, '.
            '  s.categoria_asignada AS cat_solicitud '.
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

        $rows    = array();
        $resumen = array();

        foreach ($filas as $f) {
            $digitos = preg_replace('/\D/', '', (string) $f->cuil);
            $dni     = null;
            if (strlen($digitos) === 11) {
                $dni = (int) substr($digitos, 2, 8);
            } elseif ($f->documento !== null && $f->documento !== '') {
                $dni = (int) preg_replace('/\D/', '', (string) $f->documento);
            }

            $hallazgo = 'NO ENCONTRADA';
            $detalle  = '';

            // ---- 1) por documento ----
            $cand = array();
            if ($dni) {
                $cand = DB::select(
                    'SELECT p.id, p.apellido, p.nombre, p.cuil, p.documento, i.id AS investigador_id '.
                    'FROM personas p LEFT JOIN investigadors i ON i.persona_id = p.id '.
                    'WHERE p.documento = ? LIMIT 5',
                    array($dni)
                );
            }
            if (count($cand) > 0) {
                $hallazgo = 'MISMO DNI, OTRO CUIL';
            }

            // ---- 2) por apellido + nombre exacto ----
            if (count($cand) === 0) {
                $cand = DB::select(
                    'SELECT p.id, p.apellido, p.nombre, p.cuil, p.documento, i.id AS investigador_id '.
                    'FROM personas p LEFT JOIN investigadors i ON i.persona_id = p.id '.
                    'WHERE TRIM(p.apellido) = TRIM(?) AND TRIM(p.nombre) = TRIM(?) LIMIT 5',
                    array((string) $f->apellido, (string) $f->nombre)
                );
                if (count($cand) > 0) {
                    $hallazgo = 'MISMO NOMBRE';
                }
            }

            // ---- 3) por apellido solo ----
            if (count($cand) === 0) {
                $cand = DB::select(
                    'SELECT p.id, p.apellido, p.nombre, p.cuil, p.documento, i.id AS investigador_id '.
                    'FROM personas p LEFT JOIN investigadors i ON i.persona_id = p.id '.
                    'WHERE TRIM(p.apellido) = TRIM(?) LIMIT 5',
                    array((string) $f->apellido)
                );
                if (count($cand) > 0) {
                    $hallazgo = 'APELLIDO PARECIDO ('.count($cand).')';
                }
            }

            if (count($cand) > 0) {
                $partes = array();
                foreach ($cand as $c) {
                    $partes[] = '#'.$c->id.' '.$c->apellido.', '.$c->nombre.
                        ' [cuil '.($c->cuil === null || $c->cuil === '' ? 's/d' : $c->cuil).
                        ' / doc '.($c->documento === null ? 's/d' : $c->documento).']'.
                        ($c->investigador_id === null ? ' SIN INV' : ' inv#'.$c->investigador_id);
                }
                $detalle = implode(' ;; ', $partes);
            }

            $clave = preg_replace('/ \(\d+\)$/', '', $hallazgo);
            if (!isset($resumen[$clave])) {
                $resumen[$clave] = 0;
            }
            $resumen[$clave]++;

            $rows[] = array(
                $f->solicitud_id,
                $f->cuil,
                $this->corta(trim($f->apellido.', '.$f->nombre), 28),
                $this->corta($f->ua, 12),
                trim($f->conv_tipo.' '.$f->conv_year),
                $f->cat_solicitud,
                $hallazgo,
                $this->corta($detalle, 70),
            );
        }

        $recortado = false;
        if ($limite > 0 && count($rows) > $limite) {
            $rows = array_slice($rows, 0, $limite);
            $recortado = true;
        }

        $this->table(
            array('Sol.', 'CUIL solicitud', 'Persona (solicitud)', 'U. Acad.', 'Convocatoria', 'Cat.', 'Hallazgo', 'Candidato en personas'),
            $rows
        );
        if ($recortado) {
            $this->warn('Listado recortado a '.$limite.' filas.');
        }

        arsort($resumen);
        $resRows = array();
        foreach ($resumen as $h => $cant) {
            $resRows[] = array($h, $cant);
        }
        $this->line('');
        $this->info('Resumen sobre '.count($filas).' solicitudes sin persona:');
        $this->table(array('Hallazgo', 'Cantidad'), $resRows);

        return 0;
    }

    private function corta($v, $n)
    {
        $v = (string) $v;
        return mb_strlen($v) > $n ? mb_substr($v, 0, $n - 1).'.' : $v;
    }
}
