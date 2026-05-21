<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Universidad;
use Illuminate\Support\Facades\DB;
use App\Console\Commands\Concerns\FusionaUniversidades;

class FusionarUniversidades extends Command
{
    use FusionaUniversidades;

    // {mantener} stays; {eliminar} is merged into it and deleted.
    protected $signature = 'fusionar:universidades {mantener} {eliminar}';
    protected $description = 'Fusiona una universidad dentro de otra, por ID';

    public function handle()
    {
        $mantenerId = (int) $this->argument('mantener');
        $eliminarId = (int) $this->argument('eliminar');

        if ($mantenerId === $eliminarId) {
            $this->error('No podés fusionar una universidad consigo misma.');
            return self::FAILURE;
        }

        $mantener = Universidad::find($mantenerId);
        if (!$mantener) {
            $this->error("No existe la universidad a mantener (ID {$mantenerId}).");
            return self::FAILURE;
        }

        $eliminar = Universidad::find($eliminarId);
        if (!$eliminar) {
            $this->error("No existe la universidad a eliminar (ID {$eliminarId}).");
            return self::FAILURE;
        }

        // Show both names so the user confirms the right pair.
        $this->info('Se MANTIENE:');
        $this->line("  {$mantener->id} - {$mantener->nombre}");
        $this->warn('Se ELIMINA (su contenido pasa a la anterior):');
        $this->line("  {$eliminar->id} - {$eliminar->nombre}");

        // Preview which records will be moved before asking.
        $this->previewRelaciones($eliminar->id);

        if (!$this->confirm('¿Confirmás la fusión?')) {
            $this->line('Cancelado.');
            return self::SUCCESS;
        }

        $resumen = $this->fusionarUniversidades($mantener->id, $eliminar->id);

        $this->info("Fusionada {$eliminar->id} dentro de {$mantener->id}.");
        $this->mostrarResumen($resumen);

        return self::SUCCESS;
    }

    /**
     * Show the records that will be moved, before confirming.
     * Investigadores are listed by person name; titles by name/level;
     * the rest are counted.
     */
    private function previewRelaciones($eliminarId): void
    {
        $this->newLine();
        $this->line('Registros que se moverán:');

        // Investigadores (con nombre de la persona)
        $investigadores = DB::table('investigadors as i')
            ->leftJoin('personas as p', 'p.id', '=', 'i.persona_id')
            ->where('i.universidad_id', $eliminarId)
            ->get(['i.id', 'p.apellido', 'p.nombre']);

        if ($investigadores->count() > 0) {
            $this->line("  Investigadores ({$investigadores->count()}):");
            foreach ($investigadores as $inv) {
                $nombre = trim("{$inv->apellido}, {$inv->nombre}", ', ');
                $nombre = $nombre !== '' ? $nombre : '(sin persona)';
                $this->line("      #{$inv->id} - {$nombre}");
            }
        }

        // Títulos (nombre + nivel)
        $titulos = DB::table('titulos')
            ->where('universidad_id', $eliminarId)
            ->get(['id', 'nombre', 'nivel']);

        if ($titulos->count() > 0) {
            $this->line("  Títulos ({$titulos->count()}):");
            foreach ($titulos as $t) {
                $this->line("      #{$t->id} - {$t->nombre} ({$t->nivel})");
            }
        }

        // El resto, solo conteo
        $otras = [
            'integrantes',
            'investigador_cargos',
            'investigador_categorias',
        ];

        foreach ($otras as $tabla) {
            $cant = DB::table($tabla)->where('universidad_id', $eliminarId)->count();
            if ($cant > 0) {
                $this->line("  {$tabla}: {$cant}");
            }
        }

        $this->newLine();
    }

    /**
     * Print what the merge touched, per table.
     */
    private function mostrarResumen(array $resumen): void
    {
        $this->newLine();
        $this->line('Relaciones movidas:');

        foreach ($resumen as $tabla => $datos) {
            // Skip tables where nothing happened.
            if ($datos['reasignadas'] === 0 && $datos['duplicadas'] === 0) {
                continue;
            }

            $this->line("  {$tabla}: {$datos['reasignadas']} reasignadas"
                . ($datos['duplicadas'] > 0 ? ", {$datos['duplicadas']} duplicadas eliminadas" : ''));

            foreach ($datos['fks'] as $ref => $cant) {
                $this->line("      FK {$ref}: {$cant} redirigidas");
            }
        }
    }
}
