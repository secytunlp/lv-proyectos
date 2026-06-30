<?php

namespace App\Traits;

use App\Models\Viaje;
use App\Models\ViajeEvaluacion;
use Illuminate\Support\Facades\DB;

/**
 * Calculates the total score of a viaje evaluation, replicating the blade/JS logic:
 *  - Items are split into two BLOCKS by iterador1/iterador2 (Prod. 5 años / RRHH).
 *  - Inside each block, every subgroup caps at its grupo_maximo.
 *  - Each block then caps as a whole:
 *      * Prod block -> padre_maximo of its first item (e.g. 45)
 *      * RRHH block -> sum of the grupo_maximo of its groups (maxGrupoSiguiente)
 *  - Events cap per group. Categoria/Cargo are single caps. Plan only when motivo != A.
 *
 * The two-level cap (block + subgroup) is what keeps the total <= 100 and what the
 * previous version was missing (it summed groups loose, without the block cap).
 *
 * PHP 7.x compatible.
 */
trait CalculaViajeEvaluacion
{
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

        // ----- ITEMS: split into blocks by iterador1/iterador2 (same order as blade) -----
        $itemMaximos = DB::table('viaje_evaluacion_planilla_item_maxs as im')
            ->leftJoin('evaluacion_grupos as g', 'im.evaluacion_grupo_id', '=', 'g.id')
            ->leftJoin('viaje_evaluacion_planilla_items as it', 'im.viaje_evaluacion_planilla_item_id', '=', 'it.id')
            ->where('im.viaje_evaluacion_planilla_id', $planilla->id)
            ->select('im.*', 'g.maximo as grupo_maximo', 'g.padre_id', 'it.orden')
            ->orderBy('it.orden', 'ASC')
            ->orderBy('im.id', 'ASC')
            ->get();

        $iterador1 = (int) $planilla->iterador1;
        $iterador2 = (int) $planilla->iterador2;

        $bloqueProd = array();   // grupo_id => acumulado (bloque Prod)
        $bloqueRRHH = array();   // grupo_id => acumulado (bloque RRHH)
        $grupoMax = array();     // grupo_id => grupo_maximo
        $padreMaxProd = 0;       // tope del bloque Prod (padre_maximo del primer item)
        $maxGrupoRRHH = 0;       // tope del bloque RRHH (suma de grupo_maximo de sus grupos)
        $rrhhGruposVistos = array();

        $primerItem = $itemMaximos->first();
        if ($primerItem) {
            $padreProd = DB::table('evaluacion_grupos')
                ->where('id', $primerItem->padre_id)
                ->value('maximo');
            $padreMaxProd = $padreProd ? $padreProd : 0;
        }

        $index = 0;
        foreach ($itemMaximos as $im) {
            $pi = $evaluacion->puntaje_items
                ->where('viaje_evaluacion_planilla_item_max_id', $im->id)
                ->first();
            $cantidad = ($pi && $pi->cantidad) ? (int) $pi->cantidad : 0;
            $valor = $cantidad * $im->maximo;
            $gid = $im->evaluacion_grupo_id;

            if (!isset($grupoMax[$gid])) {
                $grupoMax[$gid] = $im->grupo_maximo;
            }

            if ($index < $iterador1) {
                if (!isset($bloqueProd[$gid])) {
                    $bloqueProd[$gid] = 0;
                }
                $bloqueProd[$gid] += $valor;
            } elseif ($index < $iterador2) {
                if (!isset($bloqueRRHH[$gid])) {
                    $bloqueRRHH[$gid] = 0;
                    if (!isset($rrhhGruposVistos[$gid])) {
                        $maxGrupoRRHH += $im->grupo_maximo;
                        $rrhhGruposVistos[$gid] = true;
                    }
                }
                $bloqueRRHH[$gid] += $valor;
            }

            $index++;
        }

        // Cerrar bloque PROD: topear subgrupos, sumar, topear el bloque a padreMaxProd.
        $totalProd = 0;
        foreach ($bloqueProd as $gid => $acum) {
            $gm = $grupoMax[$gid];
            if ($gm != 0 && $acum > $gm) {
                $acum = $gm;
            }
            $totalProd += $acum;
        }
        if ($padreMaxProd != 0 && $totalProd > $padreMaxProd) {
            $totalProd = $padreMaxProd;
        }
        $total += $totalProd;

        // Cerrar bloque RRHH: topear subgrupos, sumar, topear el bloque a maxGrupoRRHH.
        $totalRRHH = 0;
        foreach ($bloqueRRHH as $gid => $acum) {
            $gm = $grupoMax[$gid];
            if ($gm != 0 && $acum > $gm) {
                $acum = $gm;
            }
            $totalRRHH += $acum;
        }
        if ($maxGrupoRRHH != 0 && $totalRRHH > $maxGrupoRRHH) {
            $totalRRHH = $maxGrupoRRHH;
        }
        $total += $totalRRHH;

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
