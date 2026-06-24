<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Viaje;
use App\Models\ViajeEvaluacion;
use Illuminate\Support\Facades\DB;

use App\Traits\CalculaViajeEvaluacion;

class AuditarPuntajesViajes extends Command
{

    use CalculaViajeEvaluacion;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'viajes:auditar-puntajes {--periodo=} {--fix}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Compara el puntaje guardado de cada evaluacion contra el recalculo con topes y reporta diferencias';

    public function handle()
    {
        $periodoId = $this->option('periodo');
        $fix = $this->option('fix');

        $query = ViajeEvaluacion::whereNotNull('puntaje');

        if ($periodoId) {
            $query->whereIn('viaje_id', function ($q) use ($periodoId) {
                $q->select('id')->from('viajes')->where('periodo_id', $periodoId);
            });
        }

        $evaluaciones = $query->get();
        $this->info('Evaluaciones a revisar: ' . $evaluaciones->count());

        $desviadas = array();

        foreach ($evaluaciones as $ev) {
            $recalc = $this->calcularTotalEvaluacion($ev->id);
            if ($recalc === null) {
                continue;
            }
            $diff = round($recalc - $ev->puntaje, 2);
            if (abs($diff) > 0.01) {
                $desviadas[] = array(
                    'evaluacion_id' => $ev->id,
                    'viaje_id' => $ev->viaje_id,
                    'guardado' => $ev->puntaje,
                    'recalculado' => $recalc,
                    'diff' => $diff,
                );
            }
        }

        if (empty($desviadas)) {
            $this->info('No se encontraron diferencias.');
            return 0;
        }

        $this->warn('Diferencias encontradas: ' . count($desviadas));
        $this->table(
            array('eval_id', 'viaje_id', 'guardado', 'recalculado', 'diff'),
            $desviadas
        );

        if ($fix) {
            if (!$this->confirm('Actualizar el puntaje guardado al recalculado en las ' . count($desviadas) . ' evaluaciones?')) {
                $this->info('Cancelado. No se modifico nada.');
                return 0;
            }
            foreach ($desviadas as $d) {
                ViajeEvaluacion::where('id', $d['evaluacion_id'])
                    ->update(array('puntaje' => $d['recalculado']));
            }
            $this->info('Puntajes actualizados.');
            $this->warn('OJO: revisar viajes que ya cerraron promedio/diferencia; eso no se recalcula aca.');
        }

        return 0;
    }


}
