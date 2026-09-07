<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Cuarto control de pivots: los cargos docentes.
 *
 *   investigadors.cargo_id + deddoc + facultad_id  <->  investigador_cargos (activo = 1)
 *
 * Este pivot NO funciona como los otros tres, y de asumir que si salio una
 * primera version de este comando que marcaba 7789 casos sobre 11866 (66%), casi
 * todos falsos. Las tres diferencias que importan:
 *
 * 1) EL PIVOT ES UN SUBCONJUNTO, POR DISEÑO. `sync:cargos` lo puebla desde
 *    cargos_alfabetico del origen filtrando por escalafon Docente, 18 facultades,
 *    los cargos [1,2,3,4,5,14], y excluyendo licencia sin goce / renuncia /
 *    jubilacion. `investigadors.cargo_id` en cambio llega sin ese filtro. Que
 *    alguien tenga cargo y ninguna fila en el pivot es lo NORMAL — son ~4200
 *    casos — y por eso SIN PIVOT solo se marca con --incluir-sin-pivot.
 *
 * 2) `universidad_id` NO VIAJA. Ni `sync:cargos` ni `cargos:actualizar` lo
 *    escriben: el select del sync no lo trae y el update de investigadors toca
 *    solo cargo_id, deddoc y facultad_id. Compararlo daba ~1600 diferencias que
 *    no significan nada. No se compara.
 *
 * 3) EL MISMO CARGO REPETIDO NO ES DUPLICADO. El updateOrInsert de
 *    cargos:actualizar usa como clave (investigador, cargo, deddoc, facultad,
 *    ingreso): la misma persona con el mismo cargo en dos fechas distintas son
 *    dos designaciones, historial legitimo. No se marca.
 *
 * QUIEN MANDA ACA es `cargos:actualizar` (ActualizarCargosDocentes), no el
 * formulario. Ese comando desactiva todo, reinserta lo vigente y elige el cargo
 * principal con:
 *
 *     ->where('activo',1)->orderBy('deddoc')->orderBy('cargo_id')->orderByDesc('ingreso')  <-- cargo_id esta MAL, ver nota
 *
 * y con eso escribe cargo_id + deddoc + facultad_id en investigadors. Este
 * control replica ese mismo ORDER BY en SQL (no reimplementado en PHP, para que
 * el orden de `deddoc` lo resuelva el motor igual que alli) y marca
 * NO ES EL PRINCIPAL cuando investigadors no coincide con esa fila.
 *
 * Esa es la senal mas util: ademas de detectar que falto correr cargos:actualizar,
 * atrapa lo que escribe mal el formulario. InvestigadorController elige el mayor
 * cargo con una condicion rota —
 *
 *     if ($mayorDeddoc === null || $request->deddocs[$item] < $mayorDeddoc) {
 *         $mayorDeddoc = $request->deddocs[$item];
 *         $mayorCargo  = $request->cargos[$item];
 *         if ($request->deddocs[$item] == $mayorDeddoc) {   // recien asignado: siempre true
 *             if ($mayorCargo === null || $this->esMayorCargo($request->cargos[$item], $mayorCargo)) {
 *
 * el if interno compara la dedicacion contra un valor asignado dos lineas antes
 * y esMayorCargo() termina comparando el cargo consigo mismo; ademas la condicion
 * externa es `<` estricta, asi que un segundo cargo con la MISMA dedicacion nunca
 * entra. A igual dedicacion gana el primero de la lista del formulario, no el de
 * mayor jerarquia. Mismo patron que el bug 7.
 *
 * Diagnosticos:
 *   PIVOT SIN INV       hay cargo activo y el investigador esta sin cargo
 *   SIN ACTIVO          tiene filas, ninguna activa, y el investigador tiene cargo
 *   INV <> PIVOT        el cargo de investigadors no esta entre los activos
 *   NO ES EL PRINCIPAL  esta entre los activos, pero no es el que elige el criterio
 *   DEDDOC <> PIVOT     el cargo coincide pero la dedicacion no
 *   FACULTAD <> PIVOT   el cargo coincide pero la unidad academica no
 *   SIN PIVOT           solo con --incluir-sin-pivot (ver punto 1)
 *
 * Solo lee: no modifica nada.
 */
class VerificarPivotCargos extends Command
{
    protected $signature = 'cargos:verificar-pivot
        {--cuil= : Filtrar por un CUIL puntual}
        {--solo= : Mostrar solo los diagnosticos que contengan este texto}
        {--incluir-sin-pivot : Marcar tambien a los que tienen cargo y ninguna fila (normal, ~4200)}
        {--limite=50 : Cortar el listado en N filas (0 = sin limite)}';

    protected $description = 'Verifica investigadors.cargo_id + deddoc + facultad contra investigador_cargos';

    private function cuilNorm($col)
    {
        return "REPLACE(REPLACE(REPLACE(".$col.", '-', ''), '.', ''), ' ', '')";
    }


    /**
     * Profesor Consulto y Profesor Emerito son distinciones academicas, no un
     * escalon del escalafon docente. Si investigadors ya tiene una de las dos,
     * NO se reemplaza por el cargo del pivot.
     */
    private function esProtegido($nombre)
    {
        $n = mb_strtoupper((string) $nombre, 'UTF-8');
        $n = str_replace(array('Á','É','Í','Ó','Ú'), array('A','E','I','O','U'), $n);
        return strpos($n, 'CONSULTO') !== false || strpos($n, 'EMERITO') !== false;
    }

    private function corta($v, $n)
    {
        $v = (string) $v;
        return mb_strlen($v) > $n ? mb_substr($v, 0, $n - 1).'.' : $v;
    }

    public function handle()
    {
        $cuil            = $this->option('cuil');
        $solo            = $this->option('solo');
        $incluirSinPivot = (bool) $this->option('incluir-sin-pivot');
        $limite          = (int) $this->option('limite');

        $this->info('=== Cargos docentes: investigadors <-> investigador_cargos ===');
        $this->line('');
        $this->line('El pivot es un subconjunto filtrado por sync:cargos, asi que tener cargo y');
        $this->line('ninguna fila es normal: SIN PIVOT solo se marca con --incluir-sin-pivot.');
        $this->line('universidad_id no se compara: ningun comando lo escribe en este circuito.');
        $this->line('Consulto y Emerito en investigadors no se comparan: son distinciones que se');
        $this->line('conservan aunque el pivot diga otra cosa.');
        $this->line('');

        $sql =
            'SELECT '.
            '  i.id AS investigador_id, p.cuil AS cuil, '.
            "  TRIM(CONCAT(COALESCE(p.apellido, ''), ', ', COALESCE(p.nombre, ''))) AS persona, ".
            '  i.cargo_id AS inv_cargo_id, i.deddoc AS inv_deddoc, i.facultad_id AS inv_facultad_id, '.
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

        $ids = array();
        foreach ($filas as $f) {
            $ids[] = (int) $f->investigador_id;
        }

        // Las filas activas, ordenadas EXACTAMENTE como las ordena cargos:actualizar
        // para elegir el principal. Se deja que el motor resuelva el orden de
        // `deddoc` en lugar de reimplementarlo aca.
        $activasPorInv = array();
        foreach (array_chunk($ids, 2000) as $lote) {
            if (count($lote) === 0) {
                continue;
            }
            $marcas = implode(',', array_fill(0, count($lote), '?'));
            $rows = DB::select(
                'SELECT ig.investigador_id, ig.cargo_id, ig.deddoc, ig.ingreso, ig.facultad_id, '.
                'cg.nombre AS cargo_nombre, fc.nombre AS facultad_nombre '.
                'FROM investigador_cargos ig '.
                'LEFT JOIN cargos    cg ON cg.id = ig.cargo_id '.
                'LEFT JOIN facultads fc ON fc.id = ig.facultad_id '.
                'WHERE ig.activo = 1 AND ig.investigador_id IN ('.$marcas.') '.
                'ORDER BY ig.investigador_id, ig.deddoc, cg.orden IS NULL, cg.orden, ig.cargo_id, ig.ingreso DESC',
                $lote
            );
            foreach ($rows as $r) {
                $activasPorInv[(int) $r->investigador_id][] = $r;
            }
        }

        $rowsOut   = array();
        $resumen   = array();
        $conDif     = 0;
        $revisados  = 0;
        $protegidos = 0;

        foreach ($filas as $f) {
            $revisados++;

            $invId    = (int) $f->investigador_id;
            $invCargo = (int) $f->inv_cargo_id;
            $invTiene = ($invCargo !== 0);
            $pvFilas  = (int) $f->filas;
            $activos  = (int) $f->activos;

            $activas = isset($activasPorInv[$invId]) ? $activasPorInv[$invId] : array();

            // Consulto y Emerito no se comparan: son distinciones academicas que
            // se conservan aunque el pivot diga otra cosa.
            if ($invTiene && $this->esProtegido($f->inv_cargo)) {
                $protegidos++;
                continue;
            }

            $marcas = array();

            if ($pvFilas === 0) {
                if ($invTiene && $incluirSinPivot) {
                    $marcas[] = 'SIN PIVOT';
                }
            } elseif ($activos === 0) {
                if ($invTiene) {
                    $marcas[] = 'SIN ACTIVO';
                }
            } elseif (!$invTiene) {
                $marcas[] = 'PIVOT SIN INV';
            } else {
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
                    // la primera del array es la que elegiria cargos:actualizar
                    $principal = $activas[0];
                    if ((int) $principal->cargo_id !== $invCargo) {
                        $marcas[] = 'NO ES EL PRINCIPAL';
                    }
                    if ((string) $coincide->deddoc !== (string) $f->inv_deddoc) {
                        $marcas[] = 'DEDDOC <> PIVOT';
                    }
                    if ((int) $coincide->facultad_id !== (int) $f->inv_facultad_id) {
                        $marcas[] = 'FACULTAD <> PIVOT';
                    }
                }
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
            foreach ($activas as $r) {
                $detalle[] = ($r->cargo_nombre === null ? '?' : $r->cargo_nombre)
                    .' '.($r->deddoc === null ? 's/d' : $r->deddoc)
                    .' '.($r->facultad_nombre === null ? '-' : $this->corta($r->facultad_nombre, 10))
                    .' '.($r->ingreso === null ? 's/i' : substr((string) $r->ingreso, 0, 7));
            }

            $rowsOut[] = array(
                $invId,
                $f->cuil,
                $this->corta($f->persona, 26),
                $this->corta(($f->inv_cargo === null ? '(sin)' : $f->inv_cargo)
                    .' '.($f->inv_deddoc === null ? 's/d' : $f->inv_deddoc), 24),
                $this->corta($f->inv_facultad === null ? '-' : $f->inv_facultad, 12),
                $this->corta(implode(' | ', $detalle), 46),
                $diagnostico,
            );
        }

        if ($conDif === 0) {
            $this->info('Cargos consistentes: '.$revisados.' investigadores revisados, ninguna diferencia.');
            $this->line('investigadors coincide con el cargo principal de sus filas activas.');
            return 0;
        }

        $recortado = false;
        if ($limite > 0 && count($rowsOut) > $limite) {
            $rowsOut = array_slice($rowsOut, 0, $limite);
            $recortado = true;
        }

        if (count($rowsOut) > 0) {
            $this->table(
                array('Inv.', 'CUIL', 'Persona', 'Investigador', 'U. Acad.', 'Activos del pivot (1ro = principal)', 'Diagnostico'),
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
        if ($protegidos > 0) {
            $this->line('No comparados por Consulto / Emerito: '.$protegidos.'.');
        }

        $this->line('');
        $this->line('Varios cargos activos a la vez es legitimo. El principal es el primero de la');
        $this->line('columna Activos: mayor dedicacion, luego mayor jerarquia de cargo (cargos.orden), luego ingreso mas reciente,');
        $this->line('el mismo criterio de cargos:actualizar.');
        $this->line('');
        $this->line('NO ES EL PRINCIPAL = investigadors quedo con un cargo activo que no es el que');
        $this->line('corresponde. O falto correr cargos:actualizar, o lo escribio el formulario, que');
        $this->line('a igual dedicacion se queda con el primero de la lista en vez del de mayor');
        $this->line('jerarquia. Correr cargos:actualizar realinea; el bug del formulario no.');

        return 0;
    }
}
