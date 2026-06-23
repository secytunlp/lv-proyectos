<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Viaje;
use App\Models\ViajeEvaluacion;
use Illuminate\Support\Facades\DB;

class AuditarPuntajesViajes extends Command
{
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

    /**
     * Recalcula el total de una evaluacion replicando la logica del PDF (con topes).
     * Devuelve null si no se puede determinar la planilla.
     */
    private function calcularTotalEvaluacion($evaluacionId)
    {
        $evaluacion = ViajeEvaluacion::find($evaluacionId);
        if (!$evaluacion) {
            return null;
        }
        $viaje = Viaje::find($evaluacion->viaje_id);
        if (!$viaje) {
            return null;
        }

        $tipoMap = array(
            'Investigador Formado' => 'Formados',
            'Investigador En Formación' => 'En formación',
        );
        $motivoMap = array(
            'A) Reuniones Científicas' => 'A',
            'B) Estadía de trabajo para investigar en ámbitos académicos externos a la UNLP' => 'B',
            'C) Estadía de Trabajo en la UNLP para un Investigador Invitado' => 'C',
        );
        $tipo = isset($tipoMap[$viaje->tipo]) ? $tipoMap[$viaje->tipo] : null;
        $motivo = isset($motivoMap[$viaje->motivo]) ? $motivoMap[$viaje->motivo] : null;

        if (!$tipo || !$motivo) {
            return null;
        }

        $planilla = DB::table('viaje_evaluacion_planillas')
            ->where('periodo_id', $viaje->periodo_id)
            ->where('tipo', $tipo)
            ->where('motivo', $motivo)
            ->first();

        if (!$planilla) {
            return null;
        }

        $total = 0;

        // CATEGORIA
        $pc = $evaluacion->puntaje_categorias->first();
        if ($pc) {
            $cm = DB::table('viaje_evaluacion_planilla_categoria_maxs')
                ->where('id', $pc->viaje_evaluacion_planilla_categoria_max_id)
                ->first();
            if ($cm) {
                $total += $cm->maximo;
            }
        }

        // CARGO
        $pca = $evaluacion->puntaje_cargos->first();
        if ($pca) {
            $cam = DB::table('viaje_evaluacion_planilla_cargo_maxs')
                ->where('id', $pca->viaje_evaluacion_planilla_cargo_max_id)
                ->first();
            if ($cam) {
                $total += $cam->maximo;
            }
        }

        // ITEMS con tope de subgrupo y de padre
        $itemMaximos = DB::table('viaje_evaluacion_planilla_item_maxs as im')
            ->leftJoin('evaluacion_grupos as g', 'im.evaluacion_grupo_id', '=', 'g.id')
            ->leftJoin('viaje_evaluacion_planilla_items as it', 'im.viaje_evaluacion_planilla_item_id', '=', 'it.id')
            ->where('im.viaje_evaluacion_planilla_id', $planilla->id)
            ->select('im.*', 'g.maximo as grupo_maximo', 'g.padre_id', 'it.orden')
            ->orderBy('it.orden', 'ASC')
            ->get();

        $subAcum = array();
        $grupoMax = array();
        $grupoPadre = array();

        foreach ($itemMaximos as $im) {
            $pi = $evaluacion->puntaje_items
                ->where('viaje_evaluacion_planilla_item_max_id', $im->id)
                ->first();
            $cantidad = ($pi && $pi->cantidad) ? (int)$pi->cantidad : 0;
            $valor = $cantidad * $im->maximo;

            $gid = $im->evaluacion_grupo_id;
            if (!isset($subAcum[$gid])) {
                $subAcum[$gid] = 0;
                $grupoMax[$gid] = $im->grupo_maximo;
                $grupoPadre[$gid] = $im->padre_id;
            }
            $subAcum[$gid] += $valor;
        }

        $porPadre = array();
        foreach ($subAcum as $gid => $acum) {
            $gm = $grupoMax[$gid];
            if ($gm != 0 && $acum > $gm) {
                $acum = $gm;
            }
            $padre = $grupoPadre[$gid];
            if ($padre) {
                if (!isset($porPadre[$padre])) {
                    $porPadre[$padre] = 0;
                }
                $porPadre[$padre] += $acum;
            } else {
                $total += $acum;
            }
        }

        foreach ($porPadre as $padre => $acum) {
            $pm = DB::table('evaluacion_grupos')->where('id', $padre)->value('maximo');
            if ($pm && $acum > $pm) {
                $acum = $pm;
            }
            $total += $acum;
        }

        // EVENTOS con tope por grupo
        $eventoMaximos = DB::table('viaje_evaluacion_planilla_evento_maxs as em')
            ->leftJoin('evaluacion_grupos as g', 'em.evaluacion_grupo_id', '=', 'g.id')
            ->where('em.viaje_evaluacion_planilla_id', $planilla->id)
            ->select('em.*', 'g.maximo as grupo_maximo')
            ->get();

        $subEvento = array();
        $grupoMaxEv = array();
        foreach ($eventoMaximos as $em) {
            $pe = $evaluacion->puntaje_eventos
                ->where('viaje_evaluacion_planilla_evento_max_id', $em->id)
                ->first();
            $valor = ($pe && $pe->puntaje) ? $pe->puntaje : 0;
            $gid = $em->evaluacion_grupo_id;
            if (!isset($subEvento[$gid])) {
                $subEvento[$gid] = 0;
                $grupoMaxEv[$gid] = $em->grupo_maximo;
            }
            $subEvento[$gid] += $valor;
        }
        foreach ($subEvento as $gid => $acum) {
            $gm = $grupoMaxEv[$gid];
            if ($gm != 0 && $acum > $gm) {
                $acum = $gm;
            }
            $total += $acum;
        }

        // PLAN (solo motivo != A)
        if ($motivo != 'A') {
            foreach ($evaluacion->puntaje_plans as $pp) {
                $total += ($pp->puntaje) ? $pp->puntaje : 0;
            }
        }

        return round($total, 2);
    }
}
