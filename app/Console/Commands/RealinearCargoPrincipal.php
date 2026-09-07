<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Realinea investigadors.cargo_id + deddoc + facultad_id con el cargo principal
 * que sale de investigador_cargos.
 *
 * Es lo que ya hace la ultima parte de cargos:actualizar, PERO sin tocar el
 * origen: ese comando primero desactiva todo y reinserta desde cargos_alfabetico
 * de mysql_origen. Aca el pivot local es la unica fuente y no se modifica ni una
 * fila: solo se escribe investigadors.
 *
 * El principal es el primero de:
 *
 *     WHERE activo = 1 ORDER BY deddoc, cargos.orden, ingreso DESC
 *
 * el mismo ORDER BY de cargos:actualizar, resuelto por el motor para que el
 * orden de `deddoc` sea identico al de alli.
 *
 * `universidad_id` NO se toca: ningun comando de este circuito lo escribe.
 *
 * Que corrige por defecto (los 60 casos sin ambiguedad del 2026-09-04):
 *   NO ES EL PRINCIPAL  el cargo esta entre los activos pero no es el que va
 *   FACULTAD <> PIVOT   el cargo coincide y la unidad academica quedo vieja
 *   DEDDOC <> PIVOT     idem con la dedicacion
 *
 * Lo que NO toca sin pedirlo expresamente:
 *   --incluir-sin-inv       investigadors sin cargo y con designacion activa (65).
 *                           NO USAR salvo que se sepa muy bien lo que se hace: a
 *                           esa gente se le quito el cargo A MANO desde la pantalla
 *                           de Integrantes (caso ACCIARESI, integrante 44857, al
 *                           que se le saco el Profesor Consulto). La designacion
 *                           docente sigue vigente en la universidad, por eso el
 *                           pivot esta activo, pero en SICADI se decidio que el
 *                           investigador no lleve cargo. Usar esta opcion revierte
 *                           esa decision para los 65 de una.
 *   --incluir-inv-distinto  el cargo de investigadors no esta entre los activos
 *                           (335). Son mayormente Interino contra Ordinario, y
 *                           para resolverlos hace falta el listado alfabetico:
 *                           el pivot solo no alcanza para saber cual esta vigente.
 */
class RealinearCargoPrincipal extends Command
{
    protected $signature = 'cargos:realinear-principal
        {--cuil= : Procesar solo este CUIL}
        {--incluir-sin-inv : PELIGROSO. Les pone cargo a los que no tienen. Ver la nota de la clase}
        {--incluir-inv-distinto : Incluir a los que tienen un cargo que no esta entre los activos}
        {--commit : Escribir. Sin esto solo muestra que haria}
        {--limite=50 : Cortar el listado en N filas (0 = sin limite)}';

    protected $description = 'Alinea investigadors con el cargo principal del pivot, sin tocar el origen';

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
        $cuil          = $this->option('cuil');
        $commit        = (bool) $this->option('commit');
        $sinInv        = (bool) $this->option('incluir-sin-inv');
        $invDistinto   = (bool) $this->option('incluir-inv-distinto');
        $limite        = (int) $this->option('limite');

        $this->info('=== Realineado del cargo principal ===');
        $this->line('');
        $this->line('Fuente: investigador_cargos (activo = 1). El pivot NO se modifica.');
        $this->line('universidad_id no se toca.');
        if (!$commit) {
            $this->warn('SIMULACION. Agrega --commit para escribir.');
        }
        $this->line('');

        $sql =
            'SELECT i.id AS investigador_id, p.cuil, '.
            "TRIM(CONCAT(COALESCE(p.apellido, ''), ', ', COALESCE(p.nombre, ''))) AS persona, ".
            'i.cargo_id AS inv_cargo_id, i.deddoc AS inv_deddoc, i.facultad_id AS inv_facultad_id, '.
            'ca.nombre AS inv_cargo '.
            'FROM investigadors i '.
            'JOIN personas p ON p.id = i.persona_id '.
            'LEFT JOIN cargos ca ON ca.id = i.cargo_id '.
            'WHERE EXISTS (SELECT 1 FROM investigador_cargos ig '.
            '              WHERE ig.investigador_id = i.id AND ig.activo = 1) ';

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

        $activasPorInv = array();
        foreach (array_chunk($ids, 2000) as $lote) {
            if (count($lote) === 0) {
                continue;
            }
            $marcas = implode(',', array_fill(0, count($lote), '?'));
            $rows = DB::select(
                'SELECT ig.investigador_id, ig.cargo_id, ig.deddoc, ig.facultad_id, ig.ingreso, '.
                'cg.nombre AS cargo_nombre, fc.nombre AS facultad_nombre '.
                'FROM investigador_cargos ig '.
                'LEFT JOIN cargos    cg ON cg.id = ig.cargo_id '.
                'LEFT JOIN facultads fc ON fc.id = ig.facultad_id '.
                'WHERE ig.activo = 1 AND ig.investigador_id IN ('.$marcas.') '.
                'ORDER BY ig.investigador_id, ig.deddoc, cg.orden IS NULL, cg.orden, ig.cargo_id, ig.ingreso DESC',
                $lote
            );
            foreach ($rows as $r) {
                if (!isset($activasPorInv[(int) $r->investigador_id])) {
                    $activasPorInv[(int) $r->investigador_id] = array();
                }
                $activasPorInv[(int) $r->investigador_id][] = $r;
            }
        }

        $aCambiar   = array();
        $motivos    = array();
        $protegidos = 0;

        foreach ($filas as $f) {
            $invId    = (int) $f->investigador_id;
            $activas  = isset($activasPorInv[$invId]) ? $activasPorInv[$invId] : array();
            if (count($activas) === 0) {
                continue;
            }

            $principal = $activas[0];
            $invCargo  = (int) $f->inv_cargo_id;
            $invTiene  = ($invCargo !== 0);

            // Consulto y Emerito no se reemplazan nunca
            if ($invTiene && $this->esProtegido($f->inv_cargo)) {
                $protegidos++;
                continue;
            }

            // en que caso esta
            if (!$invTiene) {
                if (!$sinInv) {
                    continue;
                }
                $motivo = 'SIN INV (revierte una quita manual)';
            } else {
                $entreActivos = null;
                foreach ($activas as $r) {
                    if ((int) $r->cargo_id === $invCargo) {
                        $entreActivos = $r;
                        break;
                    }
                }

                if ($entreActivos === null) {
                    if (!$invDistinto) {
                        continue;
                    }
                    $motivo = 'INV <> PIVOT';
                } else {
                    $difs = array();
                    if ((int) $principal->cargo_id !== $invCargo) {
                        $difs[] = 'NO ES EL PRINCIPAL';
                    }
                    if ((string) $entreActivos->deddoc !== (string) $f->inv_deddoc) {
                        $difs[] = 'DEDDOC';
                    }
                    if ((int) $entreActivos->facultad_id !== (int) $f->inv_facultad_id) {
                        $difs[] = 'FACULTAD';
                    }
                    if (count($difs) === 0) {
                        continue;
                    }
                    $motivo = implode(' + ', $difs);
                }
            }

            $aCambiar[] = array(
                'id'        => $invId,
                'cuil'      => $f->cuil,
                'persona'   => $f->persona,
                'de'        => ($f->inv_cargo === null ? '(sin)' : $f->inv_cargo)
                                .' '.($f->inv_deddoc === null ? 's/d' : $f->inv_deddoc),
                'a'         => ($principal->cargo_nombre === null ? '?' : $principal->cargo_nombre)
                                .' '.($principal->deddoc === null ? 's/d' : $principal->deddoc),
                'facultad'  => $principal->facultad_nombre === null ? '-' : $principal->facultad_nombre,
                'cargo_id'  => $principal->cargo_id,
                'deddoc'    => $principal->deddoc,
                'fac_id'    => $principal->facultad_id,
                'motivo'    => $motivo,
            );

            if (!isset($motivos[$motivo])) {
                $motivos[$motivo] = 0;
            }
            $motivos[$motivo]++;
        }

        if (count($aCambiar) === 0) {
            $this->info('Nada que realinear con las opciones dadas.');
            if ($protegidos > 0) {
                $this->line('Salteados por Consulto / Emerito: '.$protegidos.'.');
            }
            return 0;
        }

        $muestra = $limite > 0 ? array_slice($aCambiar, 0, $limite) : $aCambiar;
        $rows = array();
        foreach ($muestra as $c) {
            $rows[] = array(
                $c['id'],
                $c['cuil'],
                $this->corta($c['persona'], 26),
                $this->corta($c['de'], 26),
                $this->corta($c['a'], 26),
                $this->corta($c['facultad'], 14),
                $c['motivo'],
            );
        }
        $this->table(array('Inv.', 'CUIL', 'Persona', 'De', 'A', 'Facultad', 'Motivo'), $rows);
        if ($limite > 0 && count($aCambiar) > $limite) {
            $this->warn('Listado recortado a '.$limite.' de '.count($aCambiar).' (usa --limite=0).');
        }

        arsort($motivos);
        $resRows = array();
        foreach ($motivos as $m => $n) {
            $resRows[] = array($m, $n);
        }
        $this->line('');
        $this->table(array('Motivo', 'Cantidad'), $resRows);
        $this->info('Total a realinear: '.count($aCambiar));
        if ($protegidos > 0) {
            $this->line('Salteados por Consulto / Emerito: '.$protegidos.' (no se reemplazan).');
        }

        if (!$commit) {
            $this->line('');
            $this->warn('Simulacion: no se escribio nada. Agrega --commit.');
            return 0;
        }

        $ts = date('YmdHis');
        $bk = 'investigadors_backup_cargos_'.$ts;
        $this->line('');
        $this->line('Backup de investigadors...');
        DB::statement("CREATE TABLE $bk LIKE investigadors");
        DB::statement("INSERT INTO $bk SELECT * FROM investigadors");
        $this->line('   '.$bk);

        $ok = 0;
        $err = 0;
        foreach ($aCambiar as $c) {
            try {
                DB::table('investigadors')->where('id', $c['id'])->update(array(
                    'cargo_id'    => $c['cargo_id'],
                    'deddoc'      => $c['deddoc'],
                    'facultad_id' => $c['fac_id'],
                    'updated_at'  => now(),
                ));
                $ok++;
            } catch (\Exception $e) {
                $err++;
                $this->error('  inv '.$c['id'].': '.$e->getMessage());
            }
        }

        $this->line('');
        $this->info('Realineados: '.$ok.($err > 0 ? ', con error: '.$err : '').'.');
        $this->line('Volve a correr cargos:verificar-pivot para confirmar.');

        return 0;
    }
}
