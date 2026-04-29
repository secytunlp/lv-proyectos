<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class LimpiarArchivos extends Command
{
    protected $signature = 'archivos:limpiar
                            {--dry-run : Muestra qué se eliminaría sin borrar nada}
                            {--solo-temp : Solo vacía la carpeta temp}
                            {--modulo=todos : Módulo a limpiar: viajes, sicadi, jovenes, integrantes, todos}';

    protected $description = 'Elimina archivos huérfanos de viajes, sicadi, jovenes e integrantes, y vacía la carpeta temp';

    protected array $columnasViajes = [
        'curriculum',
        'trabajo',
        'aceptacion',
        'invitacion',
        'convenioB',
        'convenioC',
        'aval',
        'cvprofesor',
    ];

    protected array $columnasSicadiCurriculum = [
        'curriculum',
    ];

    protected array $columnasSicadiFotos = [
        'foto',
    ];

    protected array $columnasJovenes = [
        'curriculum',
    ];

    protected array $columnasIntegrantes = [
        'curriculum',
        'actividades',
        'resolucion',
    ];

    public function handle(): int
    {
        $dryRun   = $this->option('dry-run');
        $soloTemp = $this->option('solo-temp');
        $modulo   = $this->option('modulo');

        if ($dryRun) {
            $this->warn('*** MODO DRY-RUN: no se eliminará nada ***');
        }

        if (!$soloTemp) {
            if (in_array($modulo, ['viajes', 'todos'])) {
                $this->limpiarModulo(
                    'viajes',
                    'viajes',
                    $this->columnasViajes,
                    'public/files/viajes',
                    $dryRun
                );
            }

            if (in_array($modulo, ['sicadi', 'todos'])) {
                $this->limpiarModulo(
                    'sicadi - curriculum',
                    'solicitud_sicadis',
                    $this->columnasSicadiCurriculum,
                    'public/files/sicadi',
                    $dryRun
                );
                $this->limpiarModulo(
                    'sicadi - fotos',
                    'solicitud_sicadis',
                    $this->columnasSicadiFotos,
                    'public/images/sicadi',
                    $dryRun
                );
            }

            if (in_array($modulo, ['jovenes', 'todos'])) {
                $this->limpiarModulo(
                    'jovenes',
                    'jovens',
                    $this->columnasJovenes,
                    'public/files/jovenes',
                    $dryRun
                );
            }

            if (in_array($modulo, ['integrantes', 'todos'])) {
                $this->limpiarModulo(
                    'integrantes',
                    'integrantes',
                    $this->columnasIntegrantes,
                    'public/files',
                    $dryRun
                );
            }
        }

        $this->limpiarTemp($dryRun);

        $this->info('Listo.');
        return 0;
    }

    protected function limpiarModulo(string $label, string $tabla, array $columnas, string $carpeta, bool $dryRun): void
    {
        $this->info("--- Buscando archivos huérfanos en $carpeta/ [$label] ---");

        // Obtener todas las rutas registradas en la DB
        $rutasEnDb = collect();
        foreach ($columnas as $columna) {
            DB::table($tabla)
                ->whereNotNull($columna)
                ->where($columna, '!=', '')
                ->pluck($columna)
                ->each(function ($url) use (&$rutasEnDb) {
                    $rutaStorage = $this->urlToStoragePath($url);
                    if ($rutaStorage) {
                        $rutasEnDb->push($rutaStorage);
                    }
                });
        }

        $this->line("  Archivos registrados en DB: {$rutasEnDb->count()}");

        // Listar archivos físicos en la carpeta
        $archivosEnDisco = Storage::allFiles($carpeta);
        $this->line("  Archivos en disco: " . count($archivosEnDisco));

        $huerfanos = collect($archivosEnDisco)->filter(function ($archivo) use ($rutasEnDb) {
            return !$rutasEnDb->contains($archivo);
        });

        if ($huerfanos->isEmpty()) {
            $this->info("  No se encontraron archivos huérfanos.");
            return;
        }

        $this->warn("  Archivos huérfanos encontrados: {$huerfanos->count()}");

        foreach ($huerfanos as $huerfano) {
            $this->line("    - $huerfano");
            if (!$dryRun) {
                Storage::delete($huerfano);
            }
        }

        if (!$dryRun) {
            $this->info("  Se eliminaron {$huerfanos->count()} archivos huérfanos.");
        }
    }

    protected function limpiarTemp(bool $dryRun): void
    {
        $this->info('--- Vaciando public/temp/ ---');

        $tempPath = public_path('temp');

        if (!is_dir($tempPath)) {
            $this->warn("La carpeta temp no existe: $tempPath");
            return;
        }

        $archivos = glob($tempPath . '/*');
        $count    = count($archivos);

        if ($count === 0) {
            $this->info('La carpeta temp ya está vacía.');
            return;
        }

        $this->warn("Archivos en temp: $count");

        foreach ($archivos as $archivo) {
            $this->line('  - ' . basename($archivo));
            if (!$dryRun && is_file($archivo)) {
                unlink($archivo);
            }
        }

        if (!$dryRun) {
            $this->info("Se eliminaron $count archivos de temp.");
        }
    }

    /**
     * Convierte URL tipo /storage/files/... a ruta Storage tipo public/files/...
     */
    protected function urlToStoragePath(string $url): ?string
    {
        if (str_starts_with($url, '/storage/')) {
            return 'public/' . ltrim(substr($url, strlen('/storage/')), '/');
        }

        if (str_starts_with($url, 'files/') || str_starts_with($url, 'images/')) {
            return 'public/' . $url;
        }

        return null;
    }
}
