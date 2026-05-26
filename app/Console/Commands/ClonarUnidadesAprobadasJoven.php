<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

class ClonarUnidadesAprobadasJoven extends Command
{
    protected $signature = 'joven:clonar-unidades
                            {periodo_origen : ID del periodo de origen (ej: 15)}
                            {periodo_destino : ID del periodo de destino (ej: 16)}
                            {--dry-run : Simula la ejecución sin guardar cambios}
                            {--force : Borra las unidades existentes del periodo destino antes de clonar}';

    protected $description = 'Clona las unidades aprobadas de joven de un periodo a otro';

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
        $unidadesOrigen = DB::table('joven_evaluacion_unidad_aprobadas')
            ->where('periodo_id', $periodoOrigen)
            ->orderBy('id')
            ->get();

        if ($unidadesOrigen->isEmpty()) {
            $this->error("No se encontraron unidades aprobadas para el periodo {$periodoOrigen}");
            return 1;
        }

        // Block if destination already has rows
        $countDestino = DB::table('joven_evaluacion_unidad_aprobadas')
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

        DB::beginTransaction();

        try {
            // Wipe existing destination rows if --force was used
            if ($countDestino > 0) {
                $deleted = DB::table('joven_evaluacion_unidad_aprobadas')
                    ->where('periodo_id', $periodoDestino)
                    ->delete();
                $this->line("  Borradas {$deleted} unidades del periodo destino");
            }

            // Build insert data, dropping the original PK and remapping periodo_id
            $insertData = [];
            foreach ($unidadesOrigen as $unidad) {
                $insertData[] = [
                    'unidad_id'  => $unidad->unidad_id,
                    'periodo_id' => $periodoDestino,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            foreach (array_chunk($insertData, 500) as $chunk) {
                DB::table('joven_evaluacion_unidad_aprobadas')->insert($chunk);
            }

            $this->line("  " . count($insertData) . " unidades clonadas");

            // Sanity check
            $countOrigen = $unidadesOrigen->count();
            $countNuevo = DB::table('joven_evaluacion_unidad_aprobadas')
                ->where('periodo_id', $periodoDestino)
                ->count();

            $status = ($countOrigen === $countNuevo) ? '✓' : '✗';
            $this->line("  {$status} Verificación: origen={$countOrigen}, destino={$countNuevo}");

            if ($countOrigen !== $countNuevo) {
                throw new \Exception('Discrepancia de conteo en joven_evaluacion_unidad_aprobadas');
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
