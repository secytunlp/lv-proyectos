<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * investigador_sicadis lleva UNA FILA POR CONVOCATORIA: la fila de un año
 * significa "participo de esa convocatoria y obtuvo esta categoria".
 *
 * sicadi:forzar-2024 le inserto una fila a TODO investigador con categoria
 * DI1..DI5, se hubiera presentado o no. Este comando saca del año las filas de
 * investigadores que NO tienen solicitud SICADI de esa convocatoria.
 *
 * La categoria vigente NO se pierde: sigue en investigadors.sicadi_id.
 *
 * Por defecto BORRA las filas. Con --a-null las deja sin year en vez de
 * borrarlas (mas conservador: se puede revertir).
 *
 * Dry-run por defecto. Para persistir: --commit
 */
class DepurarPivotConvocatoriaSicadi extends Command
{
    protected $signature = 'sicadi:depurar-pivot-convocatoria
        {--year=2024 : Año de la convocatoria a depurar}
        {--a-null : En vez de borrar, dejar las filas sin year}
        {--solo= : Listar solo los casos que contengan este texto}
        {--limite=40 : Cortar el listado en N filas (0 = sin limite)}
        {--commit : Persistir los cambios (por defecto es dry-run)}';

    protected $description = 'Saca de la convocatoria las filas de investigador_sicadis de quienes no tienen solicitud de ese año';

    public function handle()
    {
        $year   = (int) $this->option('year');
        $aNull  = (bool) $this->option('a-null');
        $solo   = $this->option('solo');
        $limite = (int) $this->option('limite');
        $commit = (bool) $this->option('commit');

        // ------------------------------------------------------------------
        // Solicitudes de ESA convocatoria, por CUIL normalizado
        // ------------------------------------------------------------------
        $solicitudes = array();
        foreach (DB::select(
            'SELECT ss.cuil, ss.estado, ss.categoria_asignada '.
            'FROM solicitud_sicadis ss '.
            'JOIN sicadi_convocatorias cc ON cc.id = ss.convocatoria_id '.
            'WHERE cc.year = ? '.
            'ORDER BY ss.id ASC',
            array($year)
        ) as $s) {
            $k = preg_replace('/\D/', '', (string) $s->cuil);
            if ($k !== '') {
                $solicitudes[$k] = $s;
            }
        }

        $this->info('Solicitudes de la convocatoria '.$year.': '.count($solicitudes));

        // ------------------------------------------------------------------
        // Filas del pivot de ese año
        // ------------------------------------------------------------------
        $filas = DB::select(
            'SELECT '.
            '  v.id AS pivot_id, v.investigador_id, v.sicadi_id, v.actual, '.
            '  sv.nombre AS categoria, '.
            "  TRIM(CONCAT(COALESCE(p.apellido,''), ', ', COALESCE(p.nombre,''))) AS persona, ".
            '  p.cuil AS cuil, '.
            '  si.nombre AS categoria_investigador, '.
            '  i.sicadi_id AS sicadi_investigador_id '.
            'FROM investigador_sicadis v '.
            'JOIN investigadors i ON i.id = v.investigador_id '.
            'LEFT JOIN personas p ON p.id = i.persona_id '.
            'LEFT JOIN sicadis sv ON sv.id = v.sicadi_id '.
            'LEFT JOIN sicadis si ON si.id = i.sicadi_id '.
            'WHERE v.year = ? '.
            'ORDER BY persona, v.id',
            array($year)
        );

        if (count($filas) === 0) {
            $this->warn('No hay filas con year '.$year.'.');
            return 0;
        }

        // Cuantas filas actual=1 tiene hoy cada investigador
        $actualesPorInv = array();
        foreach (DB::select(
            'SELECT investigador_id, COUNT(*) AS c FROM investigador_sicadis WHERE actual = 1 GROUP BY investigador_id'
        ) as $r) {
            $actualesPorInv[$r->investigador_id] = (int) $r->c;
        }

        $sacar    = array();
        $quedan   = 0;
        $rows     = array();
        $resumen  = array();
        $cruce    = array();        // situacion investigador x solicitud
        $sacadasActuales = array(); // investigador_id => cuantas actual=1 se le sacan

        foreach ($filas as $f) {
            $k = preg_replace('/\D/', '', (string) $f->cuil);
            $sol = ($k !== '' && isset($solicitudes[$k])) ? $solicitudes[$k] : null;

            if ($sol === null) {
                $caso = 'SACAR (sin solicitud '.$year.')';
                $sacar[] = $f;
                if ((int) $f->actual === 1) {
                    if (!isset($sacadasActuales[$f->investigador_id])) {
                        $sacadasActuales[$f->investigador_id] = 0;
                    }
                    $sacadasActuales[$f->investigador_id]++;
                }
                $textoSol = '-';
            } else {
                $cat = trim((string) $sol->categoria_asignada);
                $textoSol = $sol->estado.' '.($cat === '' ? 'sin cat.' : $cat);
                $caso = 'QUEDA ('.$sol->estado.')';
                $quedan++;
            }

            if (!isset($resumen[$caso])) {
                $resumen[$caso] = 0;
            }
            $resumen[$caso]++;

            // Cruce: que dice investigadors.sicadi_id sobre esta fila del pivot
            if ($f->categoria_investigador === null) {
                $sit = 'investigador SIN categoria';
            } elseif (strtoupper(trim($f->categoria_investigador)) === 'S/C') {
                $sit = 'investigador s/c';
            } elseif ((int) $f->sicadi_id === (int) $f->sicadi_investigador_id) {
                $sit = 'coincide con el pivot';
            } else {
                $sit = 'difiere del pivot';
            }
            $col = ($sol === null) ? 'sin solicitud' : 'con solicitud';
            if (!isset($cruce[$sit])) {
                $cruce[$sit] = array('con solicitud' => 0, 'sin solicitud' => 0);
            }
            $cruce[$sit][$col]++;

            if ($solo !== null && $solo !== '' && stripos($caso, $solo) === false) {
                continue;
            }

            $rows[] = array(
                $f->investigador_id,
                $this->corta($f->persona, 30),
                ($f->categoria === null ? '?' : $f->categoria).((int) $f->actual === 1 ? ' (actual)' : ''),
                $f->categoria_investigador === null ? '-' : $f->categoria_investigador,
                $textoSol,
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
        $this->line('Convocatoria '.$year.' · accion: '.($aNull ? 'dejar sin year' : 'BORRAR la fila'));
        $this->line('============================================================');

        if (count($rows) > 0) {
            $this->table(
                array('Inv.', 'Persona', 'Fila '.$year, 'investigadors', 'Solicitud '.$year, 'Caso'),
                $rows
            );
            if ($recortado) {
                $this->warn('Listado recortado a '.$limite.' filas (usa --limite=0 para verlas todas).');
            }
        }

        arsort($resumen);
        $resRows = array();
        foreach ($resumen as $c => $cant) {
            $resRows[] = array($c, $cant);
        }
        $this->line('');
        $this->info('Resumen sobre '.count($filas).' filas del pivot '.$year.':');
        $this->table(array('Caso', 'Cantidad'), $resRows);

        // ------------------------------------------------------------------
        // Cruce: de donde sale cada fila del pivot
        // ------------------------------------------------------------------
        $cruceRows = array();
        foreach ($cruce as $sit => $c) {
            $cruceRows[] = array($sit, $c['con solicitud'], $c['sin solicitud'], $c['con solicitud'] + $c['sin solicitud']);
        }
        $this->line('');
        $this->info('Cruce: que dice investigadors.sicadi_id sobre cada fila del pivot '.$year.':');
        $this->table(
            array('Situacion', 'Con solicitud '.$year, 'Sin solicitud '.$year, 'Total'),
            $cruceRows
        );

        // ------------------------------------------------------------------
        // Impacto: quien queda sin ninguna fila actual = 1
        // ------------------------------------------------------------------
        $sinActual = 0;
        foreach ($sacadasActuales as $invId => $n) {
            $tenia = isset($actualesPorInv[$invId]) ? $actualesPorInv[$invId] : 0;
            if ($tenia - $n <= 0) {
                $sinActual++;
            }
        }

        $this->line('');
        $this->info('Impacto:');
        $this->table(
            array('Concepto', 'Cantidad'),
            array(
                array('Filas a sacar',                              count($sacar)),
                array('De esas, marcadas actual=1',                 array_sum($sacadasActuales)),
                array('Investigadores que quedan SIN fila actual=1', $sinActual),
                array('Filas que quedan en '.$year,                 $quedan),
            )
        );

        if ($sinActual > 0) {
            $this->warn($sinActual.' investigadores quedarian sin ninguna fila actual=1 en el pivot. '.
                'Su categoria sigue en investigadors.sicadi_id, pero si alguna pantalla la lee del pivot, ahi apareceria vacia.');
        }

        if (count($sacar) === 0) {
            $this->line('');
            $this->info('Nada para sacar.');
            return 0;
        }

        // ------------------------------------------------------------------
        // Aplicar (en lotes, son miles de filas)
        // ------------------------------------------------------------------
        $ids = array();
        foreach ($sacar as $f) {
            $ids[] = $f->pivot_id;
        }

        $afectadas = 0;

        DB::beginTransaction();
        try {
            foreach (array_chunk($ids, 500) as $lote) {
                if ($aNull) {
                    $afectadas += DB::table('investigador_sicadis')
                        ->whereIn('id', $lote)
                        ->update(array('year' => null, 'updated_at' => now()));
                } else {
                    $afectadas += DB::table('investigador_sicadis')
                        ->whereIn('id', $lote)
                        ->delete();
                }
            }

            $this->line('');
            $this->info(($aNull ? 'Filas dejadas sin year: ' : 'Filas borradas: ').$afectadas);

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
