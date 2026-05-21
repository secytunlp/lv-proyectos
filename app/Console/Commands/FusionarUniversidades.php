<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Universidad;
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

        if (!$this->confirm('¿Confirmás la fusión?')) {
            $this->line('Cancelado.');
            return self::SUCCESS;
        }

        $this->fusionarUniversidades($mantener->id, $eliminar->id);

        $this->info("Fusionada {$eliminar->id} dentro de {$mantener->id}.");
        return self::SUCCESS;
    }
}
