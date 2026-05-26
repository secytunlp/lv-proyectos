<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

class ClonarUnidadesAprobadasViaje extends Command
{
    protected $signature = 'viaje:clonar-unidades
                            {periodo_origen : ID del periodo de origen (ej: 15)}
                            {periodo_destino : ID del periodo de destino (ej: 16)}
                            {--dry-run : Simula la ejecución sin guardar cambios}
                            {--force : Borra las unidades existentes del periodo destino antes de clonar}';

    protected $description = 'Clona las unidades aprobadas de viaje de un periodo a otro';

    public function handle()
    {
        $periodoOrigen = (int) $this->argument('periodo_origen');
        $periodoDestino = (int) $this->argument('periodo_destino');
        $dryRun = $this->option('dry-run');
        $force = $this->option('force');

        if ($periodoOrigen === $periodoDestino) {
            $this->error('El periodo de origen y destino no pueden ser iguales.');
            return 1;
        }

        // Validate origin has data
        $unidadesOrigen = DB::table('viaje_evaluacion_unidad_aprobadas')
            ->where('periodo_id', $periodoOrigen)
            ->orderBy('id')
            ->get();

        if ($unidadesOrigen->isEmpty()) {
            $this->error("No se encontraron unidades aprobadas para el periodo {$periodoOrigen}");
            return 1;
        }

        // Block if destination already has rows
        $countDestino = DB::table('viaje_evaluacion_unidad_aprobadas')
            ->where('periodo_id', $periodoDestino)
            ->count();

        if ($countDestino > 0) {
            if (!$force) {
                $this->error("El periodo {$periodoDestino} ya tiene {$countDestino} unidades aprobadas. Operación abortada.");
                $this->line("Si querés borrarlas y volver a clonar, usá --force.");
                return 1;
            }

            $this->warn("Se borrarán {$countDestino} unidades existentes del periodo {$periodoDestino}.");
            if (!$dryRun && !$this->confirm('¿Confirmás?')) {
                $this->info('Operación cancelada.');
                return 0;
            }
        }

        $this->info("Clonando " . $unidadesOrigen->count() . " unidades del periodo {$periodoOrigen} -> {$periodoDestino}");
        if ($dryRun) {
            $this->warn('MODO DRY-RUN: no se guardarán cambios');
        }

        // Filter out unidad_id values that no longer exist in the unidads catalog,
        // otherwise the FK constraint would reject the whole batch.
        $unidadIdsOrigen = $unidadesOrigen->pluck('unidad_id')->all();
        $unidadIdsValidas = DB::table('unidads')
            ->whereIn('id', $unidadIdsOrigen)
            ->pluck('id')
            ->all();

        $idsHuerfanas = array_diff($unidadIdsOrigen, $unidadIdsValidas);

        if (!empty($idsHuerfanas)) {
            $this->warn("Se descartan " . count($idsHuerfanas) . " unidades que ya no existen en `unidads`:");
            $this->line('  ' . implode(', ', $idsHuerfanas));
        }

        $unidadesAClonar = $unidadesOrigen->filter(function ($u) use ($unidadIdsValidas) {
            return in_array($u->unidad_id, $unidadIdsValidas);
        });

        if ($unidadesAClonar->isEmpty()) {
            $this->error('No quedan unidades válidas para clonar.');
            return 1;
        }

        DB::beginTransaction();

        try {
            // Wipe existing destination rows if --force was used
            if ($countDestino > 0) {
                $deleted = DB::table('viaje_evaluacion_unidad_aprobadas')
                    ->where('periodo_id', $periodoDestino)
                    ->delete();
                $this->line("  Borradas {$deleted} unidades del periodo destino");
            }

            // Build insert data, dropping the original PK and remapping periodo_id
            $insertData = [];
            foreach ($unidadesAClonar as $unidad) {
                $insertData[] = [
                    'unidad_id'  => $unidad->unidad_id,
                    'periodo_id' => $periodoDestino,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            foreach (array_chunk($insertData, 500) as $chunk) {
                DB::table('viaje_evaluacion_unidad_aprobadas')->insert($chunk);
            }

            $this->line("  " . count($insertData) . " unidades clonadas");

            // Sanity check
            $countEsperado = $unidadesAClonar->count();
            $countNuevo = DB::table('viaje_evaluacion_unidad_aprobadas')
                ->where('periodo_id', $periodoDestino)
                ->count();

            $status = ($countEsperado === $countNuevo) ? '✓' : '✗';
            $this->line("  {$status} Verificación: esperado={$countEsperado}, destino={$countNuevo}");

            if ($countEsperado !== $countNuevo) {
                throw new \Exception('Discrepancia de conteo en viaje_evaluacion_unidad_aprobadas');
            }

            if ($dryRun) {
                DB::rollBack();
                $this->warn('DRY-RUN finalizado: cambios revertidos.');
            } else {
                DB::commit();
                $this->info('Unidades clonadas con éxito.');
            }

            return 0;

        } catch (QueryException $ex) {
            DB::rollBack();
            $this->error('Error de DB: ' . $ex->getMessage());
            return 1;
        } catch (\Exception $ex) {
            DB::rollBack();
            $this->error('Error: ' . $ex->getMessage());
            return 1;
        }
    }
}
