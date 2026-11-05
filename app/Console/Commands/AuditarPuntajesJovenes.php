<?php

namespace App\Console\Commands;

use App\Http\Controllers\JovenEvaluacionController;
use App\Models\Joven;
use App\Models\JovenEvaluacion;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Compara el puntaje guardado de cada evaluacion de Jovenes contra el total que
 * calcula la planilla PDF (joven_evaluacions.pdfevaluacion) con los items guardados.
 *
 * Origen: el total que se guarda lo calcula sumar_total() en el navegador. Un
 * ReferenceError en esa funcion ($pasoElTope sin declarar, cuando un item de D1-D4
 * superaba su tope) dejaba el campo oculto en 0 y la evaluacion se grababa con
 * puntaje 0.00 aunque los items estuvieran bien. Ver fix-puntaje-cero-evaluacion-jovenes.md
 *
 * Para recalcular se renderiza la misma vista del PDF (sin generar el PDF) y se lee
 * el total de la marca <!--TOTAL_EVALUACION:x-->, asi el numero es identico al del PDF.
 */
class AuditarPuntajesJovenes extends Command
{
    protected $signature = 'jovenes:auditar-puntajes
                            {--periodo= : periodo_id de las solicitudes (por defecto, todos)}
                            {--solo-cero : revisar solo las evaluaciones con puntaje 0 o NULL}
                            {--fix : actualizar joven_evaluacions.puntaje al recalculado}
                            {--promedios : con --fix, recalcular tambien puntaje/diferencia de las solicitudes ya cerradas (estado Evaluada)}';

    protected $description = 'Compara el puntaje guardado de las evaluaciones de Jovenes contra el total de la planilla PDF y reporta diferencias';

    public function handle()
    {
        $periodoId = $this->option('periodo');
        $fix = $this->option('fix');

        $query = JovenEvaluacion::query()->where('estado', 'Evaluada');
        if ($periodoId) {
            $query->whereIn('joven_id', function ($q) use ($periodoId) {
                $q->select('id')->from('jovens')->where('periodo_id', $periodoId);
            });
        }
        if ($this->option('solo-cero')) {
            $query->where(function ($q) {
                $q->whereNull('puntaje')->orWhere('puntaje', '<=', 0);
            });
        }

        $evaluaciones = $query->orderBy('joven_id')->get();
        $this->info('Evaluaciones a revisar (estado Evaluada): ' . $evaluaciones->count());

        $controller = new JovenEvaluacionController();
        $desviadas = [];
        $errores = [];

        $bar = $this->output->createProgressBar($evaluaciones->count());
        foreach ($evaluaciones as $ev) {
            $bar->advance();
            try {
                $recalc = $this->recalcular($controller, $ev);
            } catch (\Throwable $e) {
                $errores[] = [$ev->id, $ev->joven_id, mb_substr($e->getMessage(), 0, 90)];
                continue;
            }
            if ($recalc === null) {
                $errores[] = [$ev->id, $ev->joven_id, 'No se encontro el total en la vista'];
                continue;
            }
            $guardado = (float) $ev->puntaje;
            $diff = round($recalc - $guardado, 2);
            if (abs($diff) > 0.01) {
                $cab = $controller->datosCabeceraEvaluacion($ev->id);
                $joven = Joven::find($ev->joven_id);
                $desviadas[] = [
                    'evaluacion_id' => $ev->id,
                    'joven_id' => $ev->joven_id,
                    'solicitante' => $cab->persona_apellido ?? '',
                    'evaluador' => $cab->usuario_nombre ?? '',
                    'estado_solicitud' => $joven->estado ?? '',
                    'guardado' => number_format($guardado, 2, '.', ''),
                    'recalculado' => number_format($recalc, 2, '.', ''),
                    'diff' => number_format($diff, 2, '.', ''),
                ];
            }
        }
        $bar->finish();
        $this->newLine(2);

        if ($errores) {
            $this->warn('No se pudieron recalcular: ' . count($errores));
            $this->table(['eval_id', 'joven_id', 'motivo'], $errores);
        }

        if (empty($desviadas)) {
            $this->info('No se encontraron diferencias.');
            return 0;
        }

        $this->warn('Diferencias encontradas: ' . count($desviadas));
        $this->table(array_keys($desviadas[0]), $desviadas);

        if (!$fix) {
            $this->line('Solo informe. Con --fix se actualiza el puntaje de esas evaluaciones.');
            return 0;
        }

        if (!$this->confirm('Actualizar el puntaje guardado al recalculado en las ' . count($desviadas) . ' evaluaciones?')) {
            $this->info('Cancelado. No se modifico nada.');
            return 0;
        }

        DB::transaction(function () use ($desviadas) {
            foreach ($desviadas as $d) {
                JovenEvaluacion::where('id', $d['evaluacion_id'])->update(['puntaje' => $d['recalculado']]);
            }
        });
        $this->info('Puntajes de evaluaciones actualizados.');

        $jovenIds = collect($desviadas)->pluck('joven_id')->unique();
        $cerradas = Joven::whereIn('id', $jovenIds)->where('estado', 'Evaluada')->get();
        $abiertas = Joven::whereIn('id', $jovenIds)->where('estado', '!=', 'Evaluada')->pluck('id');

        if ($abiertas->count()) {
            $this->line('Solicitudes todavia en evaluacion (usar "Actualizar puntaje" cuando corresponda): ' . $abiertas->implode(', '));
        }

        if ($cerradas->isEmpty()) {
            return 0;
        }

        if (!$this->option('promedios')) {
            $this->warn('OJO: ' . $cerradas->count() . ' solicitudes ya tenian el promedio cerrado con el puntaje viejo: ' . $cerradas->pluck('id')->implode(', '));
            $this->warn('Volver a correr con --fix --promedios para recalcular su puntaje/diferencia (no cambia el estado).');
            return 0;
        }

        $filas = [];
        DB::transaction(function () use ($cerradas, &$filas) {
            foreach ($cerradas as $joven) {
                [$puntaje, $diferencia] = $this->promedio($joven);
                $filas[] = [$joven->id, $joven->puntaje, $puntaje, $joven->diferencia, $diferencia];
                $joven->puntaje = $puntaje;
                $joven->diferencia = $diferencia;
                $joven->save();
            }
        });
        $this->info('Promedios recalculados (el estado de las solicitudes no se toco):');
        $this->table(['joven_id', 'puntaje antes', 'puntaje ahora', 'dif. antes', 'dif. ahora'], $filas);

        return 0;
    }

    /**
     * Total de la evaluacion segun la planilla PDF.
     */
    private function recalcular(JovenEvaluacionController $controller, JovenEvaluacion $ev)
    {
        $joven = Joven::findOrFail($ev->joven_id);
        $datos = $controller->datosCabeceraEvaluacion($ev->id);
        $data = $controller->datosEvaluacionPDF($ev, $joven, $datos);
        $html = view('joven_evaluacions.pdfevaluacion', $data)->render();

        if (preg_match('/<!--TOTAL_EVALUACION:(-?[\d.]+)-->/', $html, $m)) {
            return round((float) $m[1], 2);
        }
        return null;
    }

    /**
     * Mismo criterio que JovenEvaluacionController::actualizar(): una evaluacion -> su puntaje;
     * dos -> promedio; mas de dos -> promedio del par con menor diferencia.
     */
    private function promedio(Joven $joven)
    {
        $evaluaciones = $joven->evaluacions()->where('estado', 'Evaluada')->get();

        if ($evaluaciones->count() === 1) {
            return [$evaluaciones->first()->puntaje, 0];
        }
        if ($evaluaciones->count() === 2) {
            $p1 = $evaluaciones[0]->puntaje;
            $p2 = $evaluaciones[1]->puntaje;
            return [($p1 + $p2) / 2, abs($p1 - $p2)];
        }
        if ($evaluaciones->count() > 2) {
            $ordenadas = $evaluaciones->sortBy('puntaje')->values();
            $minDiff = PHP_INT_MAX;
            $e1 = $e2 = null;
            for ($i = 0; $i < $ordenadas->count() - 1; $i++) {
                for ($j = $i + 1; $j < $ordenadas->count(); $j++) {
                    $diff = abs($ordenadas[$i]->puntaje - $ordenadas[$j]->puntaje);
                    if ($diff < $minDiff) {
                        $minDiff = $diff;
                        $e1 = $ordenadas[$i];
                        $e2 = $ordenadas[$j];
                    }
                }
            }
            return [($e1->puntaje + $e2->puntaje) / 2, $minDiff];
        }
        return [null, null];
    }
}
