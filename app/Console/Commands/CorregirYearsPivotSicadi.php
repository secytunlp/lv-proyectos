<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * investigador_sicadis lleva UNA FILA POR CONVOCATORIA/AÑO, y la primera
 * convocatoria fue 2024. Las filas con year 2023 son un error de carga
 * (las metieron sicadi:forzar-2024 y sicadi:actualizar-2023).
 *
 * Este comando pasa esas filas al año correcto. Casos:
 *
 *   MOVER      -> el investigador tiene fila en el año viejo y NINGUNA en el
 *                 nuevo. Se cambia el year. Es el caso limpio.
 *   FUSIONAR   -> ya tiene fila en el año nuevo con la MISMA categoria. La fila
 *                 vieja sobra. Solo se borra si se pasa --fusionar.
 *   CONFLICTO  -> ya tiene fila en el año nuevo con OTRA categoria. No se toca.
 *
 * Al mover o fusionar se respeta que quede una sola fila actual = 1 por
 * investigador, y que sea la que coincide con investigadors.sicadi_id.
 *
 * Dry-run por defecto. Para persistir: --commit
 */
class CorregirYearsPivotSicadi extends Command
{
    protected $signature = 'sicadi:corregir-years-pivot
        {--de=2023 : Año equivocado a corregir}
        {--a=2024 : Año correcto (el de la primera convocatoria)}
        {--fusionar : Borrar la fila vieja cuando ya existe la del año nuevo con la misma categoria}
        {--solo= : Listar solo los casos que contengan este texto (MOVER, FUSIONAR, CONFLICTO, MULTIPLE)}
        {--limite=40 : Cortar el listado en N filas (0 = sin limite)}
        {--commit : Persistir los cambios (por defecto es dry-run)}';

    protected $description = 'Pasa las filas de investigador_sicadis del año equivocado al año de la convocatoria real';

    public function handle()
    {
        $de       = (int) $this->option('de');
        $a        = (int) $this->option('a');
        $fusionar = (bool) $this->option('fusionar');
        $solo     = $this->option('solo');
        $limite   = (int) $this->option('limite');
        $commit   = (bool) $this->option('commit');

        if ($de === $a) {
            $this->error('--de y --a no pueden ser el mismo año.');
            return 1;
        }

        // ------------------------------------------------------------------
        // Panorama: como estan repartidos los years hoy
        // ------------------------------------------------------------------
        $panorama = DB::select(
            'SELECT year, COUNT(*) AS filas, SUM(actual = 1) AS actuales '.
            'FROM investigador_sicadis GROUP BY year ORDER BY year'
        );

        $this->info('Distribucion actual de years en investigador_sicadis:');
        $pRows = array();
        foreach ($panorama as $p) {
            $pRows[] = array($p->year === null ? '(null)' : $p->year, $p->filas, (int) $p->actuales);
        }
        $this->table(array('Year', 'Filas', 'Con actual=1'), $pRows);

        // ------------------------------------------------------------------
        // Filas a corregir
        // ------------------------------------------------------------------
        $filas = DB::select(
            'SELECT '.
            '  v.id            AS pivot_id, '.
            '  v.investigador_id, '.
            '  v.sicadi_id, '.
            '  v.actual, '.
            '  sv.nombre       AS categoria, '.
            "  TRIM(CONCAT(p.apellido, ', ', p.nombre)) AS persona, ".
            '  i.sicadi_id     AS sicadi_investigador, '.
            '  n.id            AS nueva_id, '.
            '  n.sicadi_id     AS nueva_sicadi_id, '.
            '  n.actual        AS nueva_actual, '.
            '  sn.nombre       AS nueva_categoria '.
            'FROM investigador_sicadis v '.
            'JOIN investigadors i ON i.id = v.investigador_id '.
            'LEFT JOIN personas p ON p.id = i.persona_id '.
            'LEFT JOIN sicadis sv ON sv.id = v.sicadi_id '.
            'LEFT JOIN investigador_sicadis n ON n.investigador_id = v.investigador_id AND n.year = ? '.
            'LEFT JOIN sicadis sn ON sn.id = n.sicadi_id '.
            'WHERE v.year = ? '.
            'ORDER BY persona',
            array($a, $de)
        );

        if (count($filas) === 0) {
            $this->line('');
            $this->info('No hay filas con year '.$de.'. Nada que corregir.');
            return 0;
        }

        // Investigadores con MAS DE UNA fila en el año viejo: moverlas todas
        // dejaria dos filas en el año nuevo, rompiendo "una fila por año".
        $multiples = array();
        foreach (DB::select(
            'SELECT investigador_id, COUNT(*) AS c FROM investigador_sicadis '.
            'WHERE year = ? GROUP BY investigador_id HAVING c > 1',
            array($de)
        ) as $m) {
            $multiples[$m->investigador_id] = (int) $m->c;
        }

        $mover = array();
        $fus   = array();
        $conf  = array();
        $mult  = array();
        $rows  = array();

        foreach ($filas as $f) {
            if (isset($multiples[$f->investigador_id])) {
                $caso = 'MULTIPLE EN '.$de.' ('.$multiples[$f->investigador_id].')';
                $mult[] = $f;
            } elseif ($f->nueva_id === null) {
                $caso = 'MOVER';
                $mover[] = $f;
            } elseif ((int) $f->nueva_sicadi_id === (int) $f->sicadi_id) {
                $caso = 'FUSIONAR';
                $fus[] = $f;
            } else {
                $caso = 'CONFLICTO';
                $conf[] = $f;
            }

            if ($solo !== null && $solo !== '' && stripos($caso, $solo) === false) {
                continue;
            }

            $rows[] = array(
                $f->investigador_id,
                $this->corta($f->persona, 30),
                ($f->categoria === null ? '?' : $f->categoria).((int) $f->actual === 1 ? ' (actual)' : ''),
                $f->nueva_id === null ? '-' : ($f->nueva_categoria === null ? '?' : $f->nueva_categoria).((int) $f->nueva_actual === 1 ? ' (actual)' : ''),
                $caso,
            );
        }

        $recortado = false;
        if ($limite > 0 && count($rows) > $limite) {
            $rows = array_slice($rows, 0, $limite);
            $recortado = true;
        }

        $this->line('');
        $this->line('============================================================');
        $this->info($commit ? 'MODO: COMMIT (se persiste)' : 'MODO: DRY-RUN (no se guarda nada)');
        $this->line('============================================================');

        if (count($rows) > 0) {
            $this->table(
                array('Inv.', 'Persona', 'Fila '.$de, 'Fila '.$a, 'Caso'),
                $rows
            );
            if ($recortado) {
                $this->warn('Listado recortado a '.$limite.' filas (usa --limite=0 para verlas todas).');
            }
        }

        $this->line('');
        $this->info('Resumen:');
        $this->table(
            array('Caso', 'Cantidad', 'Que se hace'),
            array(
                array('MOVER',              count($mover), 'year '.$de.' -> '.$a),
                array('FUSIONAR',           count($fus),   $fusionar ? 'se borra la fila '.$de : 'NO se toca (pasa --fusionar)'),
                array('CONFLICTO',          count($conf),  'no se toca, revisar a mano'),
                array('MULTIPLE EN '.$de,   count($mult),  'no se toca, dejaria 2 filas en '.$a),
            )
        );

        if (count($conf) > 0) {
            $this->warn('Hay '.count($conf).' filas con dos categorias distintas entre '.$de.' y '.$a.'. Revisalas antes de seguir.');
        }
        if (count($mult) > 0) {
            $this->warn('Hay '.count($mult).' filas de investigadores con mas de un registro en '.$de.'. '.
                'Moverlas dejaria dos filas en '.$a.' para la misma persona: hay que decidir cual queda. '.
                'Verlas con: --solo=MULTIPLE --limite=0');
        }

        $aplicables = count($mover) + ($fusionar ? count($fus) : 0);
        if ($aplicables === 0) {
            $this->line('');
            $this->warn('Nada para aplicar.');
            return 0;
        }

        // ------------------------------------------------------------------
        // Aplicar
        // ------------------------------------------------------------------
        $nMovidas = 0; $nBorradas = 0; $nMarcadas = 0; $nDesmarcadas = 0;

        DB::beginTransaction();
        try {
            foreach ($mover as $f) {
                $nMovidas += DB::table('investigador_sicadis')
                    ->where('id', $f->pivot_id)
                    ->update(array('year' => $a, 'updated_at' => now()));
            }

            if ($fusionar) {
                foreach ($fus as $f) {
                    // Si la vieja era la marcada actual, la nueva pasa a serlo
                    if ((int) $f->actual === 1 && (int) $f->nueva_actual !== 1) {
                        $nMarcadas += DB::table('investigador_sicadis')
                            ->where('id', $f->nueva_id)
                            ->update(array('actual' => 1, 'updated_at' => now()));
                    }
                    $nBorradas += DB::table('investigador_sicadis')
                        ->where('id', $f->pivot_id)
                        ->delete();
                }
            }

            // Una sola actual = 1 por investigador, y que sea la de investigadors.sicadi_id
            $tocados = array();
            foreach (array_merge($mover, $fusionar ? $fus : array()) as $f) {
                $tocados[$f->investigador_id] = $f->sicadi_investigador;
            }

            foreach ($tocados as $invId => $sicadiInv) {
                $actuales = DB::table('investigador_sicadis')
                    ->where('investigador_id', $invId)
                    ->where('actual', 1)
                    ->orderBy('year', 'desc')
                    ->orderBy('id', 'desc')
                    ->get();

                if ($actuales->count() <= 1) {
                    continue;
                }

                // Preferir la que coincide con investigadors.sicadi_id; si no, la mas reciente
                $quedaId = null;
                foreach ($actuales as $r) {
                    if ((int) $r->sicadi_id === (int) $sicadiInv) {
                        $quedaId = $r->id;
                        break;
                    }
                }
                if ($quedaId === null) {
                    $quedaId = $actuales->first()->id;
                }

                $nDesmarcadas += DB::table('investigador_sicadis')
                    ->where('investigador_id', $invId)
                    ->where('actual', 1)
                    ->where('id', '<>', $quedaId)
                    ->update(array('actual' => 0, 'updated_at' => now()));
            }

            $this->line('');
            $this->info('Filas afectadas:');
            $this->table(
                array('Operacion', 'Filas'),
                array(
                    array('year '.$de.' -> '.$a,                 $nMovidas),
                    array('filas '.$de.' borradas (fusion)',     $nBorradas),
                    array('marcadas actual=1',                   $nMarcadas),
                    array('desmarcadas (duplicadas)',            $nDesmarcadas),
                )
            );

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

    private function corta($v, $n)
    {
        $v = (string) $v;
        return mb_strlen($v) > $n ? mb_substr($v, 0, $n - 1).'.' : $v;
    }
}
