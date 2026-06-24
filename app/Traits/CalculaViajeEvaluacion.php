<?php

namespace App\Traits;

use App\Models\Viaje;
use App\Models\ViajeEvaluacion;
use Illuminate\Support\Facades\DB;

/**
 * Calculates the total score of a viaje evaluation, replicating the PDF logic
 * (per-subgroup caps + per-parent-group caps). This is the single source of truth
 * for the stored total: saveEvaluar and the audit command should both use it so the
 * saved value can never diverge from the sum of the items (which is what the JS
 * timing bug caused).
 *
 * PHP 7.x compatible: no arrow functions, no union types, no str_contains.
 */
trait CalculaViajeEvaluacion
{
    /**
     * Recalculates the total for an evaluation from its saved scores.
     *
     * @param  int  $evaluacionId
     * @return float|null  Null when the planilla cannot be resolved.
     */
    public function calcularTotalEvaluacion($evaluacionId)
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
            'Investigador Formado'   => 'Formados',
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

        // ----- CATEGORIA -----
        $pc = $evaluacion->puntaje_categorias->first();
        if ($pc) {
            $cm = DB::table('viaje_evaluacion_planilla_categoria_maxs')
                ->where('id', $pc->viaje_evaluacion_planilla_categoria_max_id)
                ->first();
            if ($cm) {
                $total += $cm->maximo;
            }
        }

        // ----- CARGO -----
        $pca = $evaluacion->puntaje_cargos->first();
        if ($pca) {
            $cam = DB::table('viaje_evaluacion_planilla_cargo_maxs')
                ->where('id', $pca->viaje_evaluacion_planilla_cargo_max_id)
                ->first();
            if ($cam) {
                $total += $cam->maximo;
            }
        }

        // ----- ITEMS: cap per subgroup, then per parent group -----
        $itemMaximos = DB::table('viaje_evaluacion_planilla_item_maxs as im')
            ->leftJoin('evaluacion_grupos as g', 'im.evaluacion_grupo_id', '=', 'g.id')
            ->leftJoin('viaje_evaluacion_planilla_items as it', 'im.viaje_evaluacion_planilla_item_id', '=', 'it.id')
            ->where('im.viaje_evaluacion_planilla_id', $planilla->id)
            ->select('im.*', 'g.maximo as grupo_maximo', 'g.padre_id', 'it.orden')
            ->orderBy('it.orden', 'ASC')
            ->get();

        $subAcum = array();    // grupo_id => acumulado
        $grupoMax = array();   // grupo_id => tope del subgrupo
        $grupoPadre = array(); // grupo_id => padre_id

        foreach ($itemMaximos as $im) {
            $pi = $evaluacion->puntaje_items
                ->where('viaje_evaluacion_planilla_item_max_id', $im->id)
                ->first();
            $cantidad = ($pi && $pi->cantidad) ? (int) $pi->cantidad : 0;
            $valor = $cantidad * $im->maximo;

            $gid = $im->evaluacion_grupo_id;
            if (!isset($subAcum[$gid])) {
                $subAcum[$gid] = 0;
                $grupoMax[$gid] = $im->grupo_maximo;
                $grupoPadre[$gid] = $im->padre_id;
            }
            $subAcum[$gid] += $valor;
        }

        $porPadre = array(); // padre_id => acumulado (post tope de subgrupo)
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
                // Subgrupo sin padre: suma directa.
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

        // ----- EVENTOS: cap per group -----
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

        // ----- PLAN (only motivo != A) -----
        if ($motivo != 'A') {
            foreach ($evaluacion->puntaje_plans as $pp) {
                $total += ($pp->puntaje) ? $pp->puntaje : 0;
            }
        }

        return round($total, 2);
    }
}
