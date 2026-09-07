<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Cuarto control de pivots: los cargos docentes.
 *
 *   investigadors.cargo_id + deddoc + facultad_id + universidad_id
 *        <->  investigador_cargos (filas con activo = 1)
 *
 * Diferencias con los otros tres pivots:
 *
 *   - La columna se llama `activo`, no `actual`, y VARIOS ACTIVOS ES LEGITIMO:
 *     una persona puede tener mas de un cargo docente a la vez. Lo que no puede
 *     pasar es que investigadors no coincida con ninguno de ellos.
 *   - De este pivot salen CUATRO campos de investigadors, no uno. Se comparan
 *     los cuatro por separado para saber cual esta mal.
 *
 * Como elige el controlador cual baja a investigadors (InvestigadorController,
 * en store() y update()): gana el de MENOR deddoc entre los activos, y a igual
 * deddoc deberia desempatar la jerarquia del cargo via esMayorCargo(). Ese
 * desempate NO funciona:
 *
 *     if ($mayorDeddoc === null || $request->deddocs[$item] < $mayorDeddoc) {
 *         $mayorDeddoc = $request->deddocs[$item];
 *         $mayorCargo  = $request->cargos[$item];
 *         ...
 *         if ($request->deddocs[$item] == $mayorDeddoc) {      // recien asignado: siempre true
 *             if ($mayorCargo === null || $this->esMayorCargo($request->cargos[$item], $mayorCargo)) {
 *
 * El if interno compara la dedicacion contra un valor que se asigno dos lineas
 * antes, y esMayorCargo() termina comparando el cargo consigo mismo. Ademas la
 * condicion externa es `<` estricta, asi que un segundo cargo con la MISMA
 * dedicacion nunca entra. Resultado: a igual dedicacion gana el PRIMERO de la
 * lista del formulario, no el de mayor jerarquia. Es el mismo patron del bug 7.
 *
 * Por eso VARIOS ACTIVOS no es un error pero si lo que hay que mirar: es donde
 * ese desempate pudo elegir mal.
 *
 * Diagnosticos:
 *   SIN PIVOT           tiene cargo y ninguna fila en el pivot
 *   SIN ACTIVO          tiene filas pero ninguna con activo = 1
 *   PIVOT SIN INV       hay cargo activo y el investigador esta sin cargo
 *   INV <> PIVOT        el cargo de investigadors no esta entre los activos
 *   DEDDOC <> PIVOT     el cargo coincide pero la dedicacion no
 *   FACULTAD <> PIVOT   el cargo coincide pero la unidad academica no
 *   UNIVERSIDAD <> PIVOT  idem con la universidad
 *   CARGO DUPLICADO     el mismo cargo repetido en la misma facultad y universidad
 *   VARIOS ACTIVOS (n)  informativo, solo con --incluir-varios
 *
 * Solo lee: no modifica nada.
 */
class VerificarPivotCargos extends Command
{
    protected $signature = 'cargos:verificar-pivot
        {--cuil= : Filtrar por un CUIL puntual}
        {--solo= : Mostrar solo los diagnosticos que contengan este texto}
        {--incluir-varios : Marcar tambien a los que tienen mas de un cargo activo}
        {--limite=50 : Cortar el listado en N filas (0 = sin limite)}';

    protected $description = 'Verifica investigadors.cargo_id + deddoc + facultad + universidad contra investigador_cargos';

    private function cuilNorm($col)
    {
        return "REPLACE(REPLACE(REPLACE(".$col.", '-', ''), '.', ''), ' ', '')";
    }

    private function corta($v, $n)
    {
        $v = (string) $v;
        return mb_strlen($v) > $n ? mb_substr($v, 0, $n - 1).'.' : $v;
    }

    public function handle()
    {
        $cuil           = $this->option('cuil');
        $solo           = $this->option('solo');
        $incluirVarios  = (bool) $this->option('incluir-varios');
        $limite         = (int) $this->option('limite');

        $this->info('=== Cargos docentes: investigadors <-> investigador_cargos ===');
        $this->line('');

        $sql =
            'SELECT '.
            '  i.id AS investigador_id, p.cuil AS cuil, '.
            "  TRIM(CONCAT(COALESCE(p.apellido, ''), ', ', COALESCE(p.nombre, ''))) AS persona, ".
            '  i.cargo_id AS inv_cargo_id, i.deddoc AS inv_deddoc, '.
            '  i.facultad_id AS inv_facultad_id, i.universidad_id AS inv_universidad_id, '.
            '  ca.nombre AS inv_cargo, fa.nombre AS inv_facultad, '.
            '  pv.filas, pv.activos '.
            'FROM investigadors i '.
            'JOIN personas p ON p.id = i.persona_id '.
            'LEFT JOIN cargos    ca ON ca.id = i.cargo_id '.
            'LEFT JOIN facultads fa ON fa.id = i.facultad_id '.
            'LEFT JOIN ( '.
            '   SELECT ig.investigador_id, COUNT(*) AS filas, SUM(ig.activo = 1) AS activos '.
            '   FROM investigador_cargos ig GROUP BY ig.investigador_id '.
            ') pv ON pv.investigador_id = i.id '.
            'WHERE ( pv.investigador_id IS NOT NULL OR i.cargo_id IS NOT NULL ) ';

        $bind = array();
        if ($cuil !== null && $cuil !== '') {
            $sql .= 'AND '.$this->cuilNorm('p.cuil').' = '.$this->cuilNorm('?').' ';
            $bind[] = $cuil;
        }
        $sql .= 'ORDER BY persona';

        $filas = DB::select($sql, $bind);

        // todas las filas de pivot de los investigadores en juego, en una sola query
        $ids = array();
        foreach ($filas as $f) {
            $ids[] = (int) $f->investigador_id;
        }

        $pivotPorInv = array();
        foreach (array_chunk($ids, 2000) as $lote) {
            if (count($lote) === 0) {
                continue;
            }
            $marcas = implode(',', array_fill(0, count($lote), '?'));
            $rows = DB::select(
                'SELECT ig.investigador_id, ig.cargo_id, ig.deddoc, ig.ingreso, ig.activo, '.
                'ig.facultad_id, ig.universidad_id, '.
                'cg.nombre AS cargo_nombre, cg.orden AS cargo_orden, '.
                'fc.nombre AS facultad_nombre, un.nombre AS universidad_nombre '.
                'FROM investigador_cargos ig '.
                'LEFT JOIN cargos       cg ON cg.id = ig.cargo_id '.
                'LEFT JOIN facultads    fc ON fc.id = ig.facultad_id '.
                'LEFT JOIN universidads un ON un.id = ig.universidad_id '.
                'WHERE ig.investigador_id IN ('.$marcas.') '.
                'ORDER BY ig.investigador_id, ig.activo DESC, cg.orden, ig.id',
                $lote
            );
            foreach ($rows as $r) {
                $pivotPorInv[(int) $r->investigador_id][] = $r;
            }
        }

        $rowsOut   = array();
        $resumen   = array();
        $conDif    = 0;
        $revisados = 0;

        foreach ($filas as $f) {
            $revisados++;

            $invId    = (int) $f->investigador_id;
            $invCargo = (int) $f->inv_cargo_id;
            $invTiene = ($invCargo !== 0);
            $pvFilas  = (int) $f->filas;
            $activos  = (int) $f->activos;

            $todas = isset($pivotPorInv[$invId]) ? $pivotPorInv[$invId] : array();
            $activas = array();
            $pares   = array();
            foreach ($todas as $r) {
                if ((int) $r->activo === 1) {
                    $activas[] = $r;
                }
                $pares[] = (int) $r->cargo_id.':'.(int) $r->facultad_id.':'.(int) $r->universidad_id;
            }

            $marcas = array();

            if ($pvFilas === 0) {
                if ($invTiene) {
                    $marcas[] = 'SIN PIVOT';
                }
            } elseif ($activos === 0) {
                if ($invTiene) {
                    $marcas[] = 'SIN ACTIVO';
                }
            } elseif (!$invTiene) {
                $marcas[] = 'PIVOT SIN INV';
            } else {
                // buscar entre los activos el que tenga el mismo cargo
                $coincide = null;
                foreach ($activas as $r) {
                    if ((int) $r->cargo_id === $invCargo) {
                        $coincide = $r;
                        break;
                    }
                }

                if ($coincide === null) {
                    $marcas[] = 'INV <> PIVOT';
                } else {
                    if ((string) $coincide->deddoc !== (string) $f->inv_deddoc) {
                        $marcas[] = 'DEDDOC <> PIVOT';
                    }
                    if ((int) $coincide->facultad_id !== (int) $f->inv_facultad_id) {
                        $marcas[] = 'FACULTAD <> PIVOT';
                    }
                    if ((int) $coincide->universidad_id !== (int) $f->inv_universidad_id) {
                        $marcas[] = 'UNIVERSIDAD <> PIVOT';
                    }
                }
            }

            if (count($pares) > count(array_unique($pares))) {
                $marcas[] = 'CARGO DUPLICADO';
            }

            if ($incluirVarios && $activos > 1) {
                $marcas[] = 'VARIOS ACTIVOS ('.$activos.')';
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

            $detalle = array();
            foreach ($todas as $r) {
                $detalle[] = ($r->cargo_nombre === null ? '?' : $r->cargo_nombre)
                    .' '.($r->deddoc === null ? 's/d' : $r->deddoc)
                    .' '.($r->facultad_nombre === null ? '-' : $this->corta($r->facultad_nombre, 12))
                    .((int) $r->activo === 1 ? '*' : '');
            }

            $rowsOut[] = array(
                $invId,
                $f->cuil,
                $this->corta($f->persona, 26),
                $this->corta(($f->inv_cargo === null ? '(sin)' : $f->inv_cargo)
                    .' '.($f->inv_deddoc === null ? 's/d' : $f->inv_deddoc), 22),
                $this->corta($f->inv_facultad === null ? '-' : $f->inv_facultad, 14),
                $this->corta(implode(' | ', $detalle), 44),
                $diagnostico,
            );
        }

        if ($conDif === 0) {
            $this->info('Cargos consistentes: '.$revisados.' investigadores revisados, ninguna diferencia.');
            $this->line('investigadors.cargo_id + deddoc + facultad + universidad coinciden con');
            $this->line('alguna fila activa del pivot, y no hay cargos repetidos.');
            return 0;
        }

        $recortado = false;
        if ($limite > 0 && count($rowsOut) > $limite) {
            $rowsOut = array_slice($rowsOut, 0, $limite);
            $recortado = true;
        }

        if (count($rowsOut) > 0) {
            $this->table(
                array('Inv.', 'CUIL', 'Persona', 'Investigador', 'U. Acad.', 'Pivot (* = activo)', 'Diagnostico'),
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
        $this->info('Sobre '.$revisados.' investigadores con cargo o con pivot:');
        $this->table(array('Diagnostico', 'Cantidad'), $resRows);
        $this->line('Con alguna diferencia: '.$conDif);

        $this->line('');
        $this->line('Varios cargos activos a la vez es LEGITIMO: no se marca salvo --incluir-varios.');
        $this->line('Lo que no puede pasar es que investigadors no coincida con ninguno de ellos.');
        $this->line('');
        $this->line('De este pivot salen cuatro campos de investigadors, por eso los diagnosticos');
        $this->line('estan desglosados: DEDDOC / FACULTAD / UNIVERSIDAD <> PIVOT significan que el');
        $this->line('cargo coincide pero el resto del cuarteto quedo viejo.');
        $this->line('');
        $this->line('OJO con el desempate: a igual dedicacion, InvestigadorController se queda con el');
        $this->line('PRIMERO de la lista y no con el de mayor jerarquia (esMayorCargo() termina');
        $this->line('comparando el cargo consigo mismo). Los casos con varios activos y misma');
        $this->line('dedicacion son los sospechosos. Ver claude/comandos-verificacion-sicadi.md.');

        return 0;
    }
}
