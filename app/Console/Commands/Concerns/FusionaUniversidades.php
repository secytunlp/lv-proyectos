<?php

namespace App\Console\Commands\Concerns;

use Illuminate\Support\Facades\DB;

/**
 * Shared merge logic for universities: reassigns related rows from the
 * university being removed to the one being kept, handling unique-index
 * collisions and foreign-key references along the way.
 */
trait FusionaUniversidades
{
    /**
     * Merge $eliminar into $mantener.
     *
     * Returns a summary array keyed by table, each with:
     *   - reasignadas: rows moved to $mantener
     *   - duplicadas:  duplicate rows removed (already existed in $mantener)
     *   - fks:         [childTable.column => rows redirected]
     */
    private function fusionarUniversidades($mantener, $eliminar): array
    {
        // Tables to reassign, with the columns (besides universidad_id) that
        // make a row unique. If a row in $eliminar would collide with an
        // existing row in $mantener on these columns, it's a duplicate and
        // gets deleted instead of reassigned.
        $tablas = [
            'investigadors'           => [],
            'titulos'                 => ['nombre', 'nivel'],
            'integrantes'             => [],
            'investigador_cargos'     => [],
            'investigador_categorias' => [],
        ];

        $resumen = [];

        DB::transaction(function () use ($mantener, $eliminar, $tablas, &$resumen) {

            foreach ($tablas as $tabla => $clavesUnicas) {

                $resumen[$tabla] = [
                    'reasignadas' => 0,
                    'duplicadas'  => 0,
                    'fks'         => [],
                ];

                if (!empty($clavesUnicas)) {
                    // Find rows in $eliminar that already have an equivalent
                    // row in $mantener (would violate the unique index).
                    // We need both ids: the duplicate (to remove) and its twin
                    // in $mantener (to redirect foreign keys to).
                    $duplicados = DB::table("{$tabla} as e")
                        ->where('e.universidad_id', $eliminar)
                        ->whereExists(function ($q) use ($tabla, $mantener, $clavesUnicas) {
                            $q->select(DB::raw(1))
                                ->from("{$tabla} as m")
                                ->where('m.universidad_id', $mantener);

                            foreach ($clavesUnicas as $col) {
                                $q->whereColumn("m.{$col}", "e.{$col}");
                            }
                        })
                        ->get(array_merge(['e.id'], array_map(function ($c) {
                            return "e.{$c}";
                        }, $clavesUnicas)));

                    foreach ($duplicados as $dup) {
                        // Find the surviving twin id in $mantener.
                        $twin = DB::table($tabla)->where('universidad_id', $mantener);
                        foreach ($clavesUnicas as $col) {
                            $twin->where($col, $dup->{$col});
                        }
                        $twinId = $twin->value('id');

                        if ($twinId) {
                            // Redirect foreign keys pointing to the duplicate
                            // row towards the surviving twin, then delete it.
                            $fks = $this->redirigirFks($tabla, $dup->id, $twinId);

                            foreach ($fks as $ref => $cant) {
                                $resumen[$tabla]['fks'][$ref] =
                                    ($resumen[$tabla]['fks'][$ref] ?? 0) + $cant;
                            }

                            DB::table($tabla)->where('id', $dup->id)->delete();
                            $resumen[$tabla]['duplicadas']++;
                        }
                    }
                }

                // Reassign the remaining (non-colliding) rows.
                $resumen[$tabla]['reasignadas'] = DB::table($tabla)
                    ->where('universidad_id', $eliminar)
                    ->update(['universidad_id' => $mantener]);
            }

            DB::table('universidads')
                ->where('id', $eliminar)
                ->delete();
        });

        return $resumen;
    }

    /**
     * Redirect foreign keys that point to a row being deleted ($viejoId)
     * towards the surviving row ($nuevoId).
     *
     * Returns ["childTable.column" => rows updated] for reporting.
     */
    private function redirigirFks(string $tabla, $viejoId, $nuevoId): array
    {
        // Map: parent table => list of [child table, child column] FKs.
        $fks = [
            'titulos' => [
                ['viajes', 'titulo_id'],
                ['integrantes', 'titulo_id'],
                ['integrantes', 'titulopost_id'],
                ['jovens', 'titulo_id'],
                ['jovens', 'titulopost_id'],
                ['investigador_titulos', 'titulo_id'],
                ['investigadors', 'titulo_id'],
                ['investigadors', 'titulopost_id'],
                ['investigador_tituloposts', 'titulo_id'],
            ],
        ];

        $tocadas = [];

        foreach ($fks[$tabla] ?? [] as [$childTable, $childColumn]) {
            $cant = DB::table($childTable)
                ->where($childColumn, $viejoId)
                ->update([$childColumn => $nuevoId]);

            if ($cant > 0) {
                $tocadas["{$childTable}.{$childColumn}"] = $cant;
            }
        }

        return $tocadas;
    }
}
