<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

class ClonarPlanillasViaje extends Command
{
    protected $signature = 'viaje:clonar-planillas
                            {periodo_origen : ID del periodo de origen (ej: 16)}
                            {periodo_destino : ID del periodo de destino (ej: 17)}
                            {--dry-run : Simula la ejecución sin guardar cambios}
                            {--force : Borra las planillas existentes del periodo destino antes de clonar}';

    protected $description = 'Clona las planillas de evaluación de viaje de un periodo a otro';

    /**
     * Child tables that reference viaje_evaluacion_planillas via viaje_evaluacion_planilla_id.
     * Other FKs in these tables (cargo_id, categoria_id, item_id, evento_id, evaluacion_grupo_id)
     * are shared catalog references and must NOT be remapped.
     */
    private $childTables = [
        'viaje_evaluacion_planilla_plan_maxs',
        'viaje_evaluacion_planilla_categoria_maxs',
        'viaje_evaluacion_planilla_cargo_maxs',
        'viaje_evaluacion_planilla_item_maxs',
        'viaje_evaluacion_planilla_evento_maxs',
    ];

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
        $planillasOrigen = DB::table('viaje_evaluacion_planillas')
            ->where('periodo_id', $periodoOrigen)
            ->orderBy('id')
            ->get();

        if ($planillasOrigen->isEmpty()) {
            $this->error("No se encontraron planillas para el periodo {$periodoOrigen}");
            return 1;
        }

        // Block if destination already has planillas
        $planillasDestinoExistentes = DB::table('viaje_evaluacion_planillas')
            ->where('periodo_id', $periodoDestino)
            ->pluck('id');

        if ($planillasDestinoExistentes->isNotEmpty()) {
            if (!$force) {
                $this->error("El periodo {$periodoDestino} ya tiene " . $planillasDestinoExistentes->count() . " planillas. Operación abortada.");
                $this->line("Si querés borrarlas y volver a clonar, usá --force.");
                return 1;
            }

            // Check that no evaluation has actually used these planillas before allowing wipe
            $enUso = $this->planillasEnUso($planillasDestinoExistentes->all());
            if ($enUso) {
                $this->error("No se puede usar --force: existen evaluaciones con puntajes cargados que referencian las planillas del periodo {$periodoDestino}.");
                $this->line("Tablas con referencias: " . implode(', ', $enUso));
                return 1;
            }

            $this->warn("Se borrarán " . $planillasDestinoExistentes->count() . " planillas existentes del periodo {$periodoDestino} (y sus filas hijas).");
            if (!$dryRun && !$this->confirm('¿Confirmás?')) {
                $this->info('Operación cancelada.');
                return 0;
            }
        }

        $this->info("Clonando " . $planillasOrigen->count() . " planillas del periodo {$periodoOrigen} -> {$periodoDestino}");
        if ($dryRun) {
            $this->warn('MODO DRY-RUN: no se guardarán cambios');
        }

        DB::beginTransaction();

        try {
            // Wipe existing destination planillas if --force was used
            if (!empty($planillasDestinoExistentes) && $planillasDestinoExistentes->isNotEmpty()) {
                $this->borrarPlanillasDestino($planillasDestinoExistentes->all());
            }

            $mapPlanillas = [];

            foreach ($planillasOrigen as $planilla) {
                $newId = DB::table('viaje_evaluacion_planillas')->insertGetId([
                    'nombre'     => $planilla->nombre,
                    'periodo_id' => $periodoDestino,
                    'tipo'       => $planilla->tipo,
                    'motivo'     => $planilla->motivo,
                    'maximo'     => $planilla->maximo,
                    'iterador1'  => $planilla->iterador1,
                    'iterador2'  => $planilla->iterador2,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $mapPlanillas[$planilla->id] = $newId;
                $this->line("  Planilla {$planilla->id} ({$planilla->tipo}/{$planilla->motivo}) -> {$newId}");
            }

            // Clone each child table, remapping only viaje_evaluacion_planilla_id
            foreach ($this->childTables as $tabla) {
                $this->clonarTabla($tabla, $mapPlanillas);
            }

            // Sanity check: row counts must match per child table
            $this->verificarConteos($periodoOrigen, $periodoDestino, $mapPlanillas);

            if ($dryRun) {
                DB::rollBack();
                $this->warn('DRY-RUN finalizado: cambios revertidos.');
            } else {
                DB::commit();
                $this->info('Planillas clonadas con éxito.');
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

    /**
     * Clones rows from a child table, remapping the planilla FK and dropping the original PK.
     * All other columns (catalog FKs, maximo, minimo, etc.) are preserved as-is.
     */
    private function clonarTabla($tabla, array $mapPlanillas)
    {
        $oldIds = array_keys($mapPlanillas);

        $rows = DB::table($tabla)
            ->whereIn('viaje_evaluacion_planilla_id', $oldIds)
            ->get();

        if ($rows->isEmpty()) {
            $this->line("  {$tabla}: sin filas para clonar");
            return;
        }

        $insertData = [];
        foreach ($rows as $row) {
            $data = (array) $row;
            $data['viaje_evaluacion_planilla_id'] = $mapPlanillas[$row->viaje_evaluacion_planilla_id];
            unset($data['id']);
            $data['created_at'] = now();
            $data['updated_at'] = now();
            $insertData[] = $data;
        }

        foreach (array_chunk($insertData, 200) as $chunk) {
            DB::table($tabla)->insert($chunk);
        }

        $this->line("  {$tabla}: " . count($insertData) . " filas clonadas");
    }

    /**
     * Validates that each child table has the same row count for origin and destination periods.
     */
    private function verificarConteos($periodoOrigen, $periodoDestino, array $mapPlanillas)
    {
        $oldIds = array_keys($mapPlanillas);
        $newIds = array_values($mapPlanillas);

        $this->info('Verificación de conteos:');
        foreach ($this->childTables as $tabla) {
            $countOrigen = DB::table($tabla)->whereIn('viaje_evaluacion_planilla_id', $oldIds)->count();
            $countDestino = DB::table($tabla)->whereIn('viaje_evaluacion_planilla_id', $newIds)->count();

            $status = ($countOrigen === $countDestino) ? '✓' : '✗';
            $this->line("  {$status} {$tabla}: origen={$countOrigen}, destino={$countDestino}");

            if ($countOrigen !== $countDestino) {
                throw new \Exception("Discrepancia de conteo en {$tabla}");
            }
        }
    }

    /**
     * Checks whether any of the given planillas are referenced by puntaje tables.
     * Returns an array of table names that contain references, or empty array if safe to wipe.
     */
    private function planillasEnUso(array $planillaIds)
    {
        $puntajeTables = [
            'viaje_evaluacion_puntaje_cargos',
            'viaje_evaluacion_puntaje_categorias',
            'viaje_evaluacion_puntaje_items',
            'viaje_evaluacion_puntaje_eventos',
            'viaje_evaluacion_puntaje_plans',
        ];

        $enUso = [];
        foreach ($puntajeTables as $tabla) {
            $existe = DB::table($tabla)
                ->whereIn('viaje_evaluacion_planilla_id', $planillaIds)
                ->exists();
            if ($existe) {
                $enUso[] = $tabla;
            }
        }
        return $enUso;
    }

    /**
     * Deletes existing destination planillas and all their child rows.
     * Should only be called after planillasEnUso() returned empty.
     */
    private function borrarPlanillasDestino(array $planillaIds)
    {
        foreach ($this->childTables as $tabla) {
            $deleted = DB::table($tabla)
                ->whereIn('viaje_evaluacion_planilla_id', $planillaIds)
                ->delete();
            $this->line("  Borradas {$deleted} filas de {$tabla}");
        }

        $deleted = DB::table('viaje_evaluacion_planillas')
            ->whereIn('id', $planillaIds)
            ->delete();
        $this->line("  Borradas {$deleted} planillas del periodo destino");
    }
}
