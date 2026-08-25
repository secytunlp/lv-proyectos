<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * investigador_sicadis lleva UNA FILA POR CONVOCATORIA/AÑO, y la primera
 * convocatoria fue 2024. Las filas con year 2023 (o con year nulo) son un error
 * de carga: las metieron sicadi:forzar-2024 y sicadi:actualizar-2023.
 *
 * Este comando pasa esas filas al año correcto. Casos por investigador:
 *
 *   MOVER                -> tiene fila en el año viejo y NINGUNA en el nuevo.
 *                           Se cambia el year. Caso limpio.
 *   FUSIONAR             -> ya tiene fila en el año nuevo con la MISMA
 *                           categoria; la vieja sobra. Solo con --fusionar.
 *   CONFLICTO            -> ya tiene fila en el año nuevo con OTRA categoria.
 *   MULTIPLE             -> tiene VARIAS filas en el año viejo. Moverlas todas
 *                           dejaria dos filas del mismo año. Solo se resuelve
 *                           con --resolver-multiples, quedandose con la que
 *                           coincide con investigadors.sicadi_id (la regla del
 *                           sistema: la actual es la del sicadi_id) y borrando
 *                           las otras.
 *   MULTIPLE SIN GANADORA-> varias filas y ninguna coincide con el sicadi_id
 *                           del investigador. No se toca.
 *
 * Al terminar deja una sola fila actual = 1 por investigador, la que coincide
 * con investigadors.sicadi_id.
 *
 * Dry-run por defecto. Para persistir: --commit
 *
 * Uso tipico:
 *   php artisan sicadi:corregir-years-pivot --de=2023
 *   php artisan sicadi:corregir-years-pivot --de=null
 */
class CorregirYearsPivotSicadi extends Command
{
    protected $signature = 'sicadi:corregir-years-pivot
        {--de=2023 : Año equivocado a corregir. Acepta "null" para las filas sin year}
        {--a=2024 : Año correcto (el de la primera convocatoria)}
        {--fusionar : Borrar la fila vieja cuando ya existe la del año nuevo con la misma categoria}
        {--resolver-multiples : Cuando hay varias filas del año viejo, quedarse con la del sicadi_id del investigador y borrar las otras}
        {--solo= : Listar solo los casos que contengan este texto}
        {--limite=40 : Cortar el listado en N filas (0 = sin limite)}
        {--commit : Persistir los cambios (por defecto es dry-run)}';

    protected $description = 'Pasa las filas de investigador_sicadis del año equivocado (o sin year) al año de la convocatoria real';

    public function handle()
    {
        $deOpt     = (string) $this->option('de');
        $deEsNull  = (strtolower(trim($deOpt)) === 'null');
        $de        = $deEsNull ? null : (int) $deOpt;
        $a         = (int) $this->option('a');
        $fusionar  = (bool) $this->option('fusionar');
        $resolver  = (bool) $this->option('resolver-multiples');
        $solo      = $this->option('solo');
        $limite    = (int) $this->option('limite');
        $commit    = (bool) $this->option('commit');

        $etiquetaDe = $deEsNull ? '(sin year)' : (string) $de;

        if (!$deEsNull && $de === $a) {
            $this->error('--de y --a no pueden ser el mismo año.');
            return 1;
        }

        // Condicion SQL del "año viejo"
        $condDe = $deEsNull ? 'year IS NULL' : 'year = ?';
        $bindDe = $deEsNull ? array() : array($de);

        // ------------------------------------------------------------------
        // Panorama
        // ------------------------------------------------------------------
        $panorama = DB::select(
            'SELECT year, COUNT(*) AS filas, SUM(actual = 1) AS actuales '.
            'FROM investigador_sicadis GROUP BY year ORDER BY year'
        );

        $this->info('Distribucion actual de years en investigador_sicadis:');
        $pRows = array();
        foreach ($panorama as $p) {
            $pRows[] = array($p->year === null ? '(sin year)' : $p->year, $p->filas, (int) $p->actuales);
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
            '  si.nombre       AS categoria_investigador, '.
            '  n.id            AS nueva_id, '.
            '  n.sicadi_id     AS nueva_sicadi_id, '.
            '  n.actual        AS nueva_actual, '.
            '  sn.nombre       AS nueva_categoria '.
            'FROM investigador_sicadis v '.
            'JOIN investigadors i ON i.id = v.investigador_id '.
            'LEFT JOIN personas p ON p.id = i.persona_id '.
            'LEFT JOIN sicadis sv ON sv.id = v.sicadi_id '.
            'LEFT JOIN sicadis si ON si.id = i.sicadi_id '.
            'LEFT JOIN investigador_sicadis n ON n.investigador_id = v.investigador_id AND n.year = ? '.
            'LEFT JOIN sicadis sn ON sn.id = n.sicadi_id '.
            'WHERE v.'.$condDe.' '.
            'ORDER BY persona, v.id',
            array_merge(array($a), $bindDe)
        );

        if (count($filas) === 0) {
            $this->line('');
            $this->info('No hay filas con year '.$etiquetaDe.'. Nada que corregir.');
            return 0;
        }

        // Investigadores con MAS DE UNA fila en el año viejo
        $multiples = array();
        foreach (DB::select(
            'SELECT investigador_id, COUNT(*) AS c FROM investigador_sicadis '.
            'WHERE '.$condDe.' GROUP BY investigador_id HAVING c > 1',
            $bindDe
        ) as $m) {
            $multiples[$m->investigador_id] = (int) $m->c;
        }

        // ------------------------------------------------------------------
        // Clasificacion
        // ------------------------------------------------------------------
        $mover = array();
        $fus   = array();
        $conf  = array();
        $multOk = array();   // resolubles: [investigador_id => ['queda'=>fila, 'borrar'=>[filas]]]
        $multNo = array();   // sin ganadora o con fila en el año nuevo
        $rows  = array();

        // primero agrupo los multiples por investigador
        $grupos = array();
        foreach ($filas as $f) {
            if (isset($multiples[$f->investigador_id])) {
                $grupos[$f->investigador_id][] = $f;
            }
        }

        foreach ($grupos as $invId => $lista) {
            $primera = $lista[0];

            if ($primera->nueva_id !== null) {
                $multNo[$invId] = array('lista' => $lista, 'motivo' => 'ya tiene fila en '.$a);
                continue;
            }

            $queda = null;
            foreach ($lista as $r) {
                if ($primera->sicadi_investigador !== null
                    && (int) $r->sicadi_id === (int) $primera->sicadi_investigador) {
                    $queda = $r;
                    break;
                }
            }

            if ($queda === null) {
                $multNo[$invId] = array('lista' => $lista, 'motivo' => 'ninguna coincide con investigadors.sicadi_id');
                continue;
            }

            $borrar = array();
            foreach ($lista as $r) {
                if ((int) $r->pivot_id !== (int) $queda->pivot_id) {
                    $borrar[] = $r;
                }
            }
            $multOk[$invId] = array('queda' => $queda, 'borrar' => $borrar);
        }

        foreach ($filas as $f) {
            if (isset($multiples[$f->investigador_id])) {
                if (isset($multNo[$f->investigador_id])) {
                    $caso = 'MULTIPLE SIN GANADORA ('.$multiples[$f->investigador_id].')';
                } else {
                    $g = $multOk[$f->investigador_id];
                    $caso = ((int) $g['queda']->pivot_id === (int) $f->pivot_id)
                        ? 'MULTIPLE: queda ('.$multiples[$f->investigador_id].')'
                        : 'MULTIPLE: se borra ('.$multiples[$f->investigador_id].')';
                }
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
                $this->corta($f->persona, 28),
                ($f->categoria === null ? '?' : $f->categoria).((int) $f->actual === 1 ? ' (actual)' : ''),
                $f->categoria_investigador === null ? '-' : $f->categoria_investigador,
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
        $this->line('Corrigiendo filas con year '.$etiquetaDe.' -> '.$a);
        $this->line('============================================================');

        if (count($rows) > 0) {
            $this->table(
                array('Inv.', 'Persona', 'Fila '.$etiquetaDe, 'investigadors', 'Fila '.$a, 'Caso'),
                $rows
            );
            if ($recortado) {
                $this->warn('Listado recortado a '.$limite.' filas (usa --limite=0 para verlas todas).');
            }
        }

        $filasMultOk = 0;
        $filasBorrar = 0;
        foreach ($multOk as $g) {
            $filasMultOk++;
            $filasBorrar += count($g['borrar']);
        }
        $filasMultNo = 0;
        foreach ($multNo as $g) {
            $filasMultNo += count($g['lista']);
        }

        $this->line('');
        $this->info('Resumen:');
        $this->table(
            array('Caso', 'Cantidad', 'Que se hace'),
            array(
                array('MOVER',                  count($mover), 'year '.$etiquetaDe.' -> '.$a),
                array('FUSIONAR',               count($fus),   $fusionar ? 'se borra la fila vieja' : 'NO se toca (pasa --fusionar)'),
                array('CONFLICTO',              count($conf),  'no se toca, revisar a mano'),
                array('MULTIPLE (investig.)',   $filasMultOk,  $resolver ? 'queda 1 y se borran '.$filasBorrar : 'NO se toca (pasa --resolver-multiples)'),
                array('MULTIPLE SIN GANADORA',  $filasMultNo,  'no se toca, revisar a mano'),
            )
        );

        if ($filasMultNo > 0) {
            $this->warn('Hay filas de '.count($multNo).' investigadores con varias categorias en '.$etiquetaDe.
                ' donde no se puede decidir sola. Verlas con: --solo="SIN GANADORA" --limite=0');
        }

        $hayAlgo = count($mover) > 0
            || ($fusionar && count($fus) > 0)
            || ($resolver && $filasMultOk > 0);

        if (!$hayAlgo) {
            $this->line('');
            $this->warn('Nada para aplicar.');
            return 0;
        }

        // ------------------------------------------------------------------
        // Aplicar
        // ------------------------------------------------------------------
        $nMovidas = 0; $nBorradas = 0; $nMarcadas = 0; $nDesmarcadas = 0;
        $tocados = array();

        DB::beginTransaction();
        try {
            foreach ($mover as $f) {
                $nMovidas += DB::table('investigador_sicadis')
                    ->where('id', $f->pivot_id)
                    ->update(array('year' => $a, 'updated_at' => now()));
                $tocados[$f->investigador_id] = $f->sicadi_investigador;
            }

            if ($fusionar) {
                foreach ($fus as $f) {
                    if ((int) $f->actual === 1 && (int) $f->nueva_actual !== 1) {
                        $nMarcadas += DB::table('investigador_sicadis')
                            ->where('id', $f->nueva_id)
                            ->update(array('actual' => 1, 'updated_at' => now()));
                    }
                    $nBorradas += DB::table('investigador_sicadis')
                        ->where('id', $f->pivot_id)
                        ->delete();
                    $tocados[$f->investigador_id] = $f->sicadi_investigador;
                }
            }

            if ($resolver) {
                foreach ($multOk as $invId => $g) {
                    foreach ($g['borrar'] as $r) {
                        $nBorradas += DB::table('investigador_sicadis')
                            ->where('id', $r->pivot_id)
                            ->delete();
                    }
                    $nMovidas += DB::table('investigador_sicadis')
                        ->where('id', $g['queda']->pivot_id)
                        ->update(array('year' => $a, 'actual' => 1, 'updated_at' => now()));
                    $tocados[$invId] = $g['queda']->sicadi_investigador;
                }
            }

            // Una sola actual = 1 por investigador, la del sicadi_id
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

                $quedaId = null;
                foreach ($actuales as $r) {
                    if ($sicadiInv !== null && (int) $r->sicadi_id === (int) $sicadiInv) {
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
                    array('year '.$etiquetaDe.' -> '.$a,  $nMovidas),
                    array('filas borradas',               $nBorradas),
                    array('marcadas actual=1',            $nMarcadas),
                    array('desmarcadas (duplicadas)',     $nDesmarcadas),
                )
            );

            if ($commit) {
                DB::commit();
                $this->line('');
                $this->info('COMMIT: cambios persistidos.');
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
