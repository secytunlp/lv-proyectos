<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

class ClonarPlanillasJoven extends Command
{
    protected $signature = 'joven:clonar-planillas
                            {periodo_origen : ID del periodo de origen (ej: 16)}
                            {periodo_destino : ID del periodo de destino (ej: 17)}
                            {--dry-run : Simula la ejecución sin guardar cambios}
                            {--force : Borra las planillas existentes del periodo destino antes de clonar}';

    protected $description = 'Clona la planilla de evaluación de jóvenes (y sus máximos) de un periodo a otro';

    /**
     * Tablas hijas que referencian joven_evaluacion_planillas vía joven_evaluacion_planilla_id.
     * Las demás FKs (joven_evaluacion_planilla_*_id, evaluacion_grupo_id) son catálogos
     * compartidos y NO se remapean.
     */
    private $childTables = [
        'joven_evaluacion_planilla_posgrado_maxs',
        'joven_evaluacion_planilla_cargo_maxs',
        'joven_evaluacion_planilla_ant_acad_maxs',
        'joven_evaluacion_planilla_otro_maxs',
        'joven_evaluacion_planilla_produccion_maxs',
        'joven_evaluacion_planilla_anterior_maxs',
        'joven_evaluacion_planilla_justificacion_maxs',
    ];

    /**
     * Tablas de puntajes cargados por los evaluadores. Referencian la planilla y el *_max_id,
     * así que si alguna tiene filas para el destino no se puede usar --force.
     */
    private $puntajeTables = [
        'joven_evaluacion_puntaje_posgrados',
        'joven_evaluacion_puntaje_cargos',
        'joven_evaluacion_puntaje_ant_acads',
        'joven_evaluacion_puntaje_otros',
        'joven_evaluacion_puntaje_produccions',
        'joven_evaluacion_puntaje_anteriors',
        'joven_evaluacion_puntaje_justificacions',
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

        if (!DB::table('periodos')->where('id', $periodoDestino)->exists()) {
            $this->error("No existe el periodo {$periodoDestino}.");
            return 1;
        }

        $planillasOrigen = DB::table('joven_evaluacion_planillas')
            ->where('periodo_id', $periodoOrigen)
            ->orderBy('id')
            ->get();

        if ($planillasOrigen->isEmpty()) {
            $this->error("No se encontraron planillas para el periodo {$periodoOrigen}");
            return 1;
        }

        // El controlador lee la planilla con ->where('periodo_id', ...)->first():
        // más de una por periodo no se soporta en la app.
        if ($planillasOrigen->count() > 1) {
            $this->warn("Ojo: el periodo {$periodoOrigen} tiene {$planillasOrigen->count()} planillas; JovenEvaluacionController usa sólo la primera. Se clonan todas igual.");
        }

        $planillasDestinoExistentes = DB::table('joven_evaluacion_planillas')
            ->where('periodo_id', $periodoDestino)
            ->pluck('id');

        if ($planillasDestinoExistentes->isNotEmpty()) {
            if (!$force) {
                $this->error("El periodo {$periodoDestino} ya tiene " . $planillasDestinoExistentes->count() . " planilla(s). Operación abortada.");
                $this->line("Si querés borrarlas y volver a clonar, usá --force.");
                return 1;
            }

            $enUso = $this->planillasEnUso($planillasDestinoExistentes->all());
            if ($enUso) {
                $this->error("No se puede usar --force: existen evaluaciones con puntajes cargados que referencian las planillas del periodo {$periodoDestino}.");
                $this->line("Tablas con referencias: " . implode(', ', $enUso));
                return 1;
            }

            $this->warn("Se borrarán " . $planillasDestinoExistentes->count() . " planilla(s) existentes del periodo {$periodoDestino} (y sus filas hijas).");
            if (!$dryRun && !$this->confirm('¿Confirmás?')) {
                $this->info('Operación cancelada.');
                return 0;
            }
        }

        $this->info("Clonando " . $planillasOrigen->count() . " planilla(s) de jóvenes del periodo {$periodoOrigen} -> {$periodoDestino}");
        if ($dryRun) {
            $this->warn('MODO DRY-RUN: no se guardarán cambios');
        }

        DB::beginTransaction();

        try {
            if ($planillasDestinoExistentes->isNotEmpty()) {
                $this->borrarPlanillasDestino($planillasDestinoExistentes->all());
            }

            $mapPlanillas = [];

            foreach ($planillasOrigen as $planilla) {
                // Copia genérica: todas las columnas menos el id, con el periodo nuevo
                $data = $this->prepararFila((array) $planilla);
                $data['periodo_id'] = $periodoDestino;

                $newId = DB::table('joven_evaluacion_planillas')->insertGetId($data);

                $mapPlanillas[$planilla->id] = $newId;
                $this->line("  Planilla {$planilla->id} ({$planilla->nombre}, máx {$planilla->maximo}) -> {$newId}");
            }

            foreach ($this->childTables as $tabla) {
                $this->clonarTabla($tabla, $mapPlanillas);
            }

            $this->verificarConteos($mapPlanillas);

            if ($dryRun) {
                DB::rollBack();
                $this->warn('DRY-RUN finalizado: cambios revertidos.');
            } else {
                DB::commit();
                $this->info('Planillas de jóvenes clonadas con éxito.');
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
     * Quita el id y renueva los timestamps sólo si la tabla los tiene.
     */
    private function prepararFila(array $data)
    {
        unset($data['id']);
        if (array_key_exists('created_at', $data)) {
            $data['created_at'] = now();
        }
        if (array_key_exists('updated_at', $data)) {
            $data['updated_at'] = now();
        }
        return $data;
    }

    /**
     * Clona las filas de una tabla hija remapeando sólo joven_evaluacion_planilla_id.
     * Se preservan maximo, minimo, tope, evaluacion_grupo_id, nombre, etc.
     */
    private function clonarTabla($tabla, array $mapPlanillas)
    {
        $rows = DB::table($tabla)
            ->whereIn('joven_evaluacion_planilla_id', array_keys($mapPlanillas))
            ->orderBy('id')
            ->get();

        if ($rows->isEmpty()) {
            $this->line("  {$tabla}: sin filas para clonar");
            return;
        }

        $insertData = [];
        foreach ($rows as $row) {
            $data = $this->prepararFila((array) $row);
            $data['joven_evaluacion_planilla_id'] = $mapPlanillas[$row->joven_evaluacion_planilla_id];
            $insertData[] = $data;
        }

        foreach (array_chunk($insertData, 200) as $chunk) {
            DB::table($tabla)->insert($chunk);
        }

        $this->line("  {$tabla}: " . count($insertData) . " filas clonadas");
    }

    private function verificarConteos(array $mapPlanillas)
    {
        $oldIds = array_keys($mapPlanillas);
        $newIds = array_values($mapPlanillas);

        $this->info('Verificación de conteos:');
        foreach ($this->childTables as $tabla) {
            $countOrigen = DB::table($tabla)->whereIn('joven_evaluacion_planilla_id', $oldIds)->count();
            $countDestino = DB::table($tabla)->whereIn('joven_evaluacion_planilla_id', $newIds)->count();

            $status = ($countOrigen === $countDestino) ? '✓' : '✗';
            $this->line("  {$status} {$tabla}: origen={$countOrigen}, destino={$countDestino}");

            if ($countOrigen !== $countDestino) {
                throw new \Exception("Discrepancia de conteo en {$tabla}");
            }
        }
    }

    private function planillasEnUso(array $planillaIds)
    {
        $enUso = [];
        foreach ($this->puntajeTables as $tabla) {
            if (DB::table($tabla)->whereIn('joven_evaluacion_planilla_id', $planillaIds)->exists()) {
                $enUso[] = $tabla;
            }
        }
        return $enUso;
    }

    private function borrarPlanillasDestino(array $planillaIds)
    {
        foreach ($this->childTables as $tabla) {
            $deleted = DB::table($tabla)
                ->whereIn('joven_evaluacion_planilla_id', $planillaIds)
                ->delete();
            $this->line("  Borradas {$deleted} filas de {$tabla}");
        }

        $deleted = DB::table('joven_evaluacion_planillas')
            ->whereIn('id', $planillaIds)
            ->delete();
        $this->line("  Borradas {$deleted} planilla(s) del periodo destino");
    }
}
