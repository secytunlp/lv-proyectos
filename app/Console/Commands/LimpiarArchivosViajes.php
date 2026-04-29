<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class LimpiarArchivosViajes extends Command
{
    protected $signature = 'viajes:limpiar-archivos
                            {--dry-run : Muestra qué se eliminaría sin borrar nada}
                            {--solo-temp : Solo vacía la carpeta temp}';

    protected $description = 'Elimina archivos de viajes que no están registrados en la DB y vacía la carpeta temp';

    // Columnas de la tabla viajes que contienen rutas de archivos
    protected array $columnasArchivos = [
        'curriculum',
        'trabajo',
        'aceptacion',
        'invitacion',
        'convenioB',
        'convenioC',
        'aval',
        'cvprofesor',
    ];

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $soloTemp = $this->option('solo-temp');

        if ($dryRun) {
            $this->warn('*** MODO DRY-RUN: no se eliminará nada ***');
        }

        if (!$soloTemp) {
            $this->limpiarArchivosHuerfanos($dryRun);
        }

        $this->limpiarTemp($dryRun);

        $this->info('Listo.');
        return 0;
    }

    protected function limpiarArchivosHuerfanos(bool $dryRun): void
    {
        $this->info('--- Buscando archivos huérfanos en storage/app/public/files/viajes/ ---');

        // Obtener todas las rutas registradas en la DB (sin nulos ni vacíos)
        $rutasEnDb = collect();
        foreach ($this->columnasArchivos as $columna) {
            DB::table('viajes')
                ->whereNotNull($columna)
                ->where($columna, '!=', '')
                ->pluck($columna)
                ->each(function ($url) use (&$rutasEnDb) {
                    // Convertir URL tipo /storage/files/... a ruta Storage tipo public/files/...
                    $rutaStorage = $this->urlToStoragePath($url);
                    if ($rutaStorage) {
                        $rutasEnDb->push($rutaStorage);
                    }
                });
        }

        $this->line("Archivos registrados en DB: {$rutasEnDb->count()}");

        // Listar todos los archivos físicos en storage/app/public/files/viajes/
        $archivosEnDisco = Storage::files('public/files/viajes', true); // true = recursivo... pero usamos allFiles
        $archivosEnDisco = Storage::allFiles('public/files/viajes');

        $this->line("Archivos en disco: " . count($archivosEnDisco));

        $huerfanos = collect($archivosEnDisco)->filter(function ($archivoEnDisco) use ($rutasEnDb) {
            return !$rutasEnDb->contains($archivoEnDisco);
        });

        if ($huerfanos->isEmpty()) {
            $this->info('No se encontraron archivos huérfanos.');
            return;
        }

        $this->warn("Archivos huérfanos encontrados: {$huerfanos->count()}");

        foreach ($huerfanos as $huerfano) {
            $this->line("  - $huerfano");
            if (!$dryRun) {
                Storage::delete($huerfano);
            }
        }

        if (!$dryRun) {
            $this->info("Se eliminaron {$huerfanos->count()} archivos huérfanos.");
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
        $count = count($archivos);

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
     * Convierte una URL tipo /storage/files/viajes/...
     * a una ruta de Storage tipo public/files/viajes/...
     */
    protected function urlToStoragePath(string $url): ?string
    {
        // Ejemplo: /storage/files/viajes/2026/27-xxx/CV_uuid.pdf
        //       -> public/files/viajes/2026/27-xxx/CV_uuid.pdf
        if (str_starts_with($url, '/storage/')) {
            return 'public/' . ltrim(substr($url, strlen('/storage/')), '/');
        }

        // Por si hay rutas legacy tipo files/viajes/...
        if (str_starts_with($url, 'files/')) {
            return 'public/' . $url;
        }

        return null;
    }
}
