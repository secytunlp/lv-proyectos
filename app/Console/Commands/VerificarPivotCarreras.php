<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Control del tercer pivot con columna `actual`, el que no tenia ninguno:
 *
 *   investigadors.carrerainv_id + organismo_id  <->  investigador_carreras
 *
 * Es el equivalente de sicadi:verificar-pivot para las carreras de investigacion.
 * Mismos diagnosticos, con dos diferencias de fondo:
 *
 *   - La identidad de una fila es el PAR (carrerainv_id, organismo_id), no la
 *     carrera sola: la misma carrera puede existir en dos organismos (CONICET,
 *     CIC, ...) y comparar solo por carrerainv_id los confunde.
 *   - El pivot no tiene `year` sino `ingreso`, asi que en lugar de ANIO DUPLICADO
 *     se marca PAR DUPLICADO: dos filas con la misma carrera en el mismo organismo.
 *
 * Por que hacia falta: el bug 7 (InvestigadorController, radio con value fijo mas
 * la bandera $esActual inicializada fuera del loop) dejaba marcadas `actual = 1`
 * todas las filas posteriores a la elegida. Afectaba a los tres pivots, pero
 * investigador_categorias e investigador_sicadis ya tenian control y este no.
 * Medido a mano el 2026-09-04, antes del fix: 198 investigadores con actual
 * duplicado. El bug esta corregido, asi que lo que quede es residuo historico:
 * se limpia una vez y no vuelve.
 *
 * Diagnosticos:
 *   SIN PIVOT           tiene carrera y ninguna fila en el pivot
 *   SIN ACTUAL          tiene filas pero ninguna marcada actual = 1
 *   ACTUAL DUPLICADO    mas de una fila con actual = 1        <- bug 7
 *   INV <> PIVOT        ninguna fila actual coincide con el par del investigador
 *   PIVOT SIN INV       hay pivot actual con carrera y el investigador esta sin carrera
 *   PAR DUPLICADO       mas de una fila con la misma carrera y organismo
 *
 * Cuando hay ACTUAL DUPLICADO y una de las filas actuales SI coincide con el
 * investigador, no se marca INV <> PIVOT: esa es la firma del arrastre del bug 7
 * (la eleccion del usuario quedo bien en investigadors y sobran las filas
 * posteriores). Se separa asi de los casos donde la carrera esta realmente mal.
 *
 * Solo lee: no modifica nada.
 */
class VerificarPivotCarreras extends Command
{
    protected $signature = 'carreras:verificar-pivot
        {--cuil= : Filtrar por un CUIL puntual}
        {--solo= : Mostrar solo los diagnosticos que contengan este texto (ej: "ACTUAL DUPLICADO")}
        {--incluir-sc : No tratar las carreras tipo s/c como "sin carrera"}
        {--limite=50 : Cortar el listado en N filas (0 = sin limite)}';

    protected $description = 'Verifica que el pivot actual coincida con investigadors.carrerainv_id + organismo_id';

    private $carNombres = array();
    private $scIds      = array();
    private $tratarSc   = true;

    /** 0, NULL y las carreras tipo s/c son la misma cosa: "sin carrera" */
    private function norm($id)
    {
        $id = (int) $id;
        if ($id === 0) {
            return 0;
        }
        if ($this->tratarSc && in_array($id, $this->scIds, true)) {
            return 0;
        }
        return $id;
    }

    /** Clave de identidad de una fila: carrera + organismo */
    private function par($carrera, $organismo)
    {
        return $this->norm($carrera).':'.((int) $organismo);
    }

    private function cuilNorm($col)
    {
        return "REPLACE(REPLACE(REPLACE(".$col.", '-', ''), '.', ''), ' ', '')";
    }

    private function car($id)
    {
        $id = (int) $id;
        if ($id === 0) {
            return '(sin)';
        }
        return isset($this->carNombres[$id]) ? $this->carNombres[$id] : ('INEXISTENTE('.$id.')');
    }

    public function handle()
    {
        $cuil   = $this->option('cuil');
        $solo   = $this->option('solo');
        $limite = (int) $this->option('limite');

        $this->tratarSc = !$this->option('incluir-sc');

        $sinCarrera = array('S/C', 'SC', 'S/D', '-', 'NINGUNA', 'NO POSEE', 'SIN CARRERA', 'SIN CATEGORIA');
        foreach (DB::table('carrerainvs')->select('id', 'nombre')->get() as $c) {
            $this->carNombres[(int) $c->id] = $c->nombre;
            if (in_array(strtoupper(trim($c->nombre)), $sinCarrera, true)) {
                $this->scIds[] = (int) $c->id;
            }
        }
        if ($this->tratarSc && count($this->scIds) > 0) {
            $etiquetas = array();
            foreach ($this->scIds as $id) {
                $etiquetas[] = $this->carNombres[$id].' (id '.$id.')';
            }
            $this->line('Tratando como "sin carrera": '.implode(', ', $etiquetas).'. Usa --incluir-sc para no hacerlo.');
        }

        $sql =
            'SELECT '.
            '  i.id             AS investigador_id, '.
            '  p.cuil           AS cuil, '.
            "  TRIM(CONCAT(COALESCE(p.apellido, ''), ', ', COALESCE(p.nombre, ''))) AS persona, ".
            '  f.nombre         AS ua, '.
            '  i.carrerainv_id  AS inv_carrera_id, '.
            '  i.organismo_id   AS inv_organismo_id, '.
            '  ci.nombre        AS inv_carrera, '.
            '  oi.codigo        AS inv_organismo, '.
            '  pv.filas         AS pv_filas, '.
            '  pv.actuales      AS pv_actuales, '.
            '  pv.pares         AS pv_pares, '.
            '  pv.act_pares     AS pv_act_pares, '.
            '  pv.detalle       AS pv_detalle '.
            'FROM investigadors i '.
            'JOIN personas p ON p.id = i.persona_id '.
            'LEFT JOIN carrerainvs ci ON ci.id = i.carrerainv_id '.
            'LEFT JOIN organismos  oi ON oi.id = i.organismo_id '.
            'LEFT JOIN facultads   f  ON f.id  = i.facultad_id '.
            'LEFT JOIN ( '.
            '   SELECT ic.investigador_id, '.
            '          COUNT(*) AS filas, '.
            '          SUM(ic.actual = 1) AS actuales, '.
            "          COUNT(DISTINCT CONCAT(COALESCE(ic.carrerainv_id, 0), ':', COALESCE(ic.organismo_id, 0))) AS pares, ".
            "          GROUP_CONCAT(DISTINCT CASE WHEN ic.actual = 1 ".
            "                 THEN CONCAT(COALESCE(ic.carrerainv_id, 0), ':', COALESCE(ic.organismo_id, 0)) END) AS act_pares, ".
            "          GROUP_CONCAT(CONCAT(COALESCE(cp.nombre, '?'), ' ', COALESCE(op.codigo, '?'), ".
            "                              ' ', COALESCE(ic.ingreso, 's/i'), ".
            "                              CASE WHEN ic.actual = 1 THEN '*' ELSE '' END) ".
            "                       ORDER BY ic.ingreso DESC SEPARATOR ' | ') AS detalle ".
            '   FROM investigador_carreras ic '.
            '   LEFT JOIN carrerainvs cp ON cp.id = ic.carrerainv_id '.
            '   LEFT JOIN organismos  op ON op.id = ic.organismo_id '.
            '   GROUP BY ic.investigador_id '.
            ') pv ON pv.investigador_id = i.id '.
            // solo interesan los que tienen algo de alguno de los dos lados
            'WHERE ( pv.investigador_id IS NOT NULL OR i.carrerainv_id IS NOT NULL ) ';

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

            $invCarRaw = (int) $f->inv_carrera_id;
            $invCar    = $this->norm($invCarRaw);
            $invTiene  = ($invCar !== 0);
            $invPar    = $this->par($invCarRaw, $f->inv_organismo_id);

            $pvFilas  = (int) $f->pv_filas;
            $actuales = (int) $f->pv_actuales;

            // pares actuales, ya normalizados
            $actPares = array();
            if ($f->pv_act_pares !== null && $f->pv_act_pares !== '') {
                foreach (explode(',', $f->pv_act_pares) as $v) {
                    $partes = explode(':', $v);
                    $car    = isset($partes[0]) ? (int) $partes[0] : 0;
                    $org    = isset($partes[1]) ? (int) $partes[1] : 0;
                    if ($this->norm($car) !== 0) {
                        $actPares[] = $this->par($car, $org);
                    }
                }
            }
            $actuales = count($actPares) > 0 ? $actuales : 0;   // actual s/c = sin actual real

            $marcas = array();

            if ($invCarRaw !== 0 && !isset($this->carNombres[$invCarRaw])) {
                $marcas[] = 'CARRERA INEXISTENTE ('.$invCarRaw.')';
            }

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
                // si el par del investigador esta entre los actuales, es arrastre
                // del bug 7 y no un desfasaje real: no se marca INV <> PIVOT.
                if ($invTiene && !in_array($invPar, $actPares, true)) {
                    $marcas[] = 'INV <> PIVOT';
                }
            } else {
                if ($invTiene) {
                    if ($actPares[0] !== $invPar) {
                        $marcas[] = 'INV <> PIVOT';
                    }
                } else {
                    $marcas[] = 'PIVOT SIN INV';
                }
            }

            if ($pvFilas > (int) $f->pv_pares) {
                $marcas[] = 'PAR DUPLICADO';
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

            $invTxt = $this->car($invCarRaw);
            if ($f->inv_organismo !== null && $invCarRaw !== 0) {
                $invTxt .= ' '.$f->inv_organismo;
            }

            $rowsOut[] = array(
                (int) $f->investigador_id,
                $f->cuil,
                $this->corta($f->persona, 28),
                $this->corta($f->ua, 12),
                $this->corta($invTxt, 18),
                $f->pv_detalle === null ? '-' : $this->corta($f->pv_detalle, 40),
                $diagnostico,
            );
        }

        if ($conDif === 0) {
            $this->info('Pivot consistente: '.$revisados.' investigadores revisados, ninguna diferencia.');
            $this->line('Toda fila actual = 1 coincide con investigadors.carrerainv_id + organismo_id');
            $this->line('y no hay carreras repetidas en el mismo organismo.');
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
        $this->info('Sobre '.$revisados.' investigadores con carrera o con pivot:');
        $this->table(array('Diagnostico', 'Cantidad'), $resRows);
        $this->line('Con alguna diferencia: '.$conDif);

        $this->line('');
        $this->line('ACTUAL DUPLICADO a secas = arrastre del bug 7: el par del investigador esta entre');
        $this->line('las filas actuales y sobran las posteriores. Se corrige desmarcando las que no');
        $this->line('coinciden; el valor de investigadors ya esta bien y no hay que tocarlo.');
        $this->line('ACTUAL DUPLICADO + INV <> PIVOT, en cambio, es un caso a mirar de a uno: ninguna');
        $this->line('fila actual coincide, asi que hay que decidir cual es la carrera correcta.');
        $this->line('');
        $this->line('El bug 7 esta corregido desde el 2026-09-04, asi que esto es residuo historico:');
        $this->line('se limpia una vez y no vuelve. Ver claude/comandos-verificacion-sicadi.md.');

        return 0;
    }

    private function corta($v, $n)
    {
        $v = (string) $v;
        return mb_strlen($v) > $n ? mb_substr($v, 0, $n - 1).'.' : $v;
    }
}
