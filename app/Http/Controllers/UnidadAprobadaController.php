<?php

namespace App\Http\Controllers;

use App\Constants;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Gestión de las unidades aprobadas por período para las convocatorias de
 * Jóvenes Investigadores y Viajes/Estadías.
 *
 * Tablas destino (estructura idéntica): {id, unidad_id, periodo_id, timestamps}
 *   - joven_evaluacion_unidad_aprobadas
 *   - viaje_evaluacion_unidad_aprobadas
 */
class UnidadAprobadaController extends Controller
{
    /**
     * Devuelve la configuración (tabla destino, etiqueta y año por defecto)
     * para el tipo de convocatoria indicado. Aborta 404 si el tipo es inválido.
     */
    private function tipoConfig($tipo)
    {
        $mapa = [
            'joven' => [
                'tabla' => 'joven_evaluacion_unidad_aprobadas',
                'label' => 'Jóvenes Investigadores',
                'year'  => Constants::YEAR_JOVENES,
            ],
            'viaje' => [
                'tabla' => 'viaje_evaluacion_unidad_aprobadas',
                'label' => 'Viajes/Estadías',
                'year'  => Constants::YEAR_VIAJES,
            ],
        ];

        if (! isset($mapa[$tipo])) {
            abort(404, 'Tipo de convocatoria inválido.');
        }

        return $mapa[$tipo];
    }

    /**
     * Pantalla principal: elegir tipo + período, ver las unidades aprobadas
     * y disponer del selector para agregar nuevas.
     */
    public function index(Request $request)
    {
        $tipo = $request->input('tipo', 'joven');
        $cfg = $this->tipoConfig($tipo);

        $periodos = DB::table('periodos')->orderBy('nombre', 'DESC')->get();

        // Período por defecto: el que coincide con el año configurado en Constants.
        $periodoId = $request->input('periodo_id');
        if (! $periodoId) {
            $actual = $periodos->firstWhere('nombre', $cfg['year']);
            $periodoId = $actual ? $actual->id : optional($periodos->first())->id;
        }

        $aprobadas = collect();
        $disponibles = collect();

        if ($periodoId) {
            $aprobadasIds = DB::table($cfg['tabla'])
                ->where('periodo_id', $periodoId)
                ->pluck('unidad_id')
                ->all();

            $aprobadasSet = array_flip($aprobadasIds);

            // Se carga el catálogo una sola vez y se reparte en aprobadas / disponibles.
            $todas = DB::table('unidads')->orderBy('nombre', 'ASC')->get();

            foreach ($todas as $u) {
                $u->etiqueta = $u->nombre . ($u->sigla ? ' (' . $u->sigla . ')' : '');
                if (isset($aprobadasSet[$u->id])) {
                    $aprobadas->push($u);
                } else {
                    $disponibles->push($u);
                }
            }
        }

        // Conteo de unidades aprobadas por período en cada tabla, para ofrecer
        // "traer de otro período" mostrando sólo los que tienen datos.
        $conteos = [
            'joven' => DB::table('joven_evaluacion_unidad_aprobadas')
                ->select('periodo_id', DB::raw('count(*) as total'))
                ->groupBy('periodo_id')->pluck('total', 'periodo_id'),
            'viaje' => DB::table('viaje_evaluacion_unidad_aprobadas')
                ->select('periodo_id', DB::raw('count(*) as total'))
                ->groupBy('periodo_id')->pluck('total', 'periodo_id'),
        ];

        return view('unidad_aprobadas.index', [
            'tipo'        => $tipo,
            'tipos'       => ['joven' => 'Jóvenes Investigadores', 'viaje' => 'Viajes/Estadías'],
            'periodos'    => $periodos,
            'periodoId'   => $periodoId,
            'aprobadas'   => $aprobadas,
            'disponibles' => $disponibles,
            'conteos'     => $conteos,
        ]);
    }

    /**
     * Agrega una o varias unidades a las aprobadas del período (omite duplicados).
     */
    public function agregar(Request $request)
    {
        $tipo = $request->input('tipo');
        $cfg = $this->tipoConfig($tipo);

        $request->validate([
            'periodo_id'   => 'required|exists:periodos,id',
            'unidad_ids'   => 'required|array',
            'unidad_ids.*' => 'exists:unidads,id',
        ], [
            'unidad_ids.required' => 'Debe seleccionar al menos una unidad.',
        ]);

        $periodoId = (int) $request->input('periodo_id');
        $solicitadas = array_map('intval', $request->input('unidad_ids'));

        // Evitar duplicados (no hay índice único en la tabla).
        $existentes = DB::table($cfg['tabla'])
            ->where('periodo_id', $periodoId)
            ->pluck('unidad_id')
            ->all();

        $nuevas = array_values(array_diff($solicitadas, $existentes));

        if (! empty($nuevas)) {
            $filas = array_map(function ($unidadId) use ($periodoId) {
                return [
                    'unidad_id'  => $unidadId,
                    'periodo_id' => $periodoId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }, $nuevas);

            DB::table($cfg['tabla'])->insert($filas);
        }

        $agregadas = count($nuevas);
        $omitidas = count($solicitadas) - $agregadas;

        $msg = "{$agregadas} unidad(es) agregada(s).";
        if ($omitidas > 0) {
            $msg .= " {$omitidas} ya estaban aprobadas y se omitieron.";
        }

        return redirect()
            ->route('unidad_aprobadas.index', ['tipo' => $tipo, 'periodo_id' => $periodoId])
            ->with('success', $msg);
    }

    /**
     * Trae (copia) todas las unidades aprobadas de un período de origen
     * —de cualquiera de las dos tablas— al período/tabla de destino.
     * Descarta unidades inexistentes en el catálogo y omite duplicados.
     */
    public function importar(Request $request)
    {
        $tipo = $request->input('tipo');
        $cfg = $this->tipoConfig($tipo);

        $request->validate([
            'periodo_id'        => 'required|exists:periodos,id',
            'origen_tipo'       => 'required|in:joven,viaje',
            'origen_periodo_id' => 'required|exists:periodos,id',
        ]);

        $origenCfg = $this->tipoConfig($request->input('origen_tipo'));

        $periodoId = (int) $request->input('periodo_id');
        $origenPeriodoId = (int) $request->input('origen_periodo_id');

        // El origen no puede ser exactamente el mismo (misma tabla y mismo período).
        if ($cfg['tabla'] === $origenCfg['tabla'] && $periodoId === $origenPeriodoId) {
            return redirect()
                ->route('unidad_aprobadas.index', ['tipo' => $tipo, 'periodo_id' => $periodoId])
                ->with('error', 'El período de origen no puede ser el mismo que el destino.');
        }

        $origenIds = DB::table($origenCfg['tabla'])
            ->where('periodo_id', $origenPeriodoId)
            ->pluck('unidad_id')
            ->all();

        if (empty($origenIds)) {
            return redirect()
                ->route('unidad_aprobadas.index', ['tipo' => $tipo, 'periodo_id' => $periodoId])
                ->with('error', 'El período de origen seleccionado no tiene unidades aprobadas.');
        }

        // Seguridad de FK: sólo unidades que aún existen en el catálogo `unidads`.
        $validas = DB::table('unidads')->whereIn('id', $origenIds)->pluck('id')->all();

        // Evitar duplicados en el destino.
        $existentes = DB::table($cfg['tabla'])
            ->where('periodo_id', $periodoId)
            ->pluck('unidad_id')
            ->all();

        $nuevas = array_values(array_diff($validas, $existentes));

        if (! empty($nuevas)) {
            $filas = array_map(function ($unidadId) use ($periodoId) {
                return [
                    'unidad_id'  => $unidadId,
                    'periodo_id' => $periodoId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }, $nuevas);

            DB::table($cfg['tabla'])->insert($filas);
        }

        $importadas = count($nuevas);
        $huerfanas = count($origenIds) - count($validas);
        $duplicadas = count($validas) - $importadas;

        $msg = "{$importadas} unidad(es) traída(s) del período de origen.";
        if ($duplicadas > 0) {
            $msg .= " {$duplicadas} ya estaban aprobadas y se omitieron.";
        }
        if ($huerfanas > 0) {
            $msg .= " {$huerfanas} se descartaron por no existir en el catálogo de unidades.";
        }

        return redirect()
            ->route('unidad_aprobadas.index', ['tipo' => $tipo, 'periodo_id' => $periodoId])
            ->with('success', $msg);
    }

    /**
     * Quita una unidad de las aprobadas del período.
     */
    public function quitar(Request $request)
    {
        $tipo = $request->input('tipo');
        $cfg = $this->tipoConfig($tipo);

        $request->validate([
            'periodo_id' => 'required|exists:periodos,id',
            'unidad_id'  => 'required|integer',
        ]);

        $periodoId = (int) $request->input('periodo_id');
        $unidadId = (int) $request->input('unidad_id');

        $borradas = DB::table($cfg['tabla'])
            ->where('periodo_id', $periodoId)
            ->where('unidad_id', $unidadId)
            ->delete();

        $msg = $borradas > 0
            ? 'Unidad quitada de las aprobadas.'
            : 'La unidad no estaba en las aprobadas.';

        return redirect()
            ->route('unidad_aprobadas.index', ['tipo' => $tipo, 'periodo_id' => $periodoId])
            ->with('success', $msg);
    }
}
