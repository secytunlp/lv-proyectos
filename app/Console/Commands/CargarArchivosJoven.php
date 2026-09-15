<?php

namespace App\Console\Commands;

use App\Constants;
use App\Models\Joven;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CargarArchivosJoven extends Command
{
    // Usage:
    //   php artisan joven:cargar-archivos {id} --cv=/path/CV.pdf
    //   php artisan joven:cargar-archivos {id} --cv=/path/CV.pdf --anio=2025 --reemplazar
    protected $signature = 'joven:cargar-archivos
                            {id : Joven (solicitud) ID}
                            {--cv= : Absolute path to the curriculum file}
                            {--anio= : Convocatoria folder to use (defaults to Constants::YEAR_JOVENES)}
                            {--reemplazar : Delete the previous file stored in the column}
                            {--dry-run : Show what would change without writing}';

    protected $description = 'Attach the curriculum file to an existing joven solicitud without altering its state';

    /**
     * Columns that hold a file, with the filename prefix the controller uses.
     */
    private const CAMPOS = [
        'cv' => ['columna' => 'curriculum', 'prefijo' => 'CV_'],
    ];

    public function handle()
    {
        $joven = Joven::find($this->argument('id'));

        if (!$joven) {
            $this->error("Joven {$this->argument('id')} not found.");
            return 1;
        }

        $dryRun = $this->option('dry-run');

        // Which options were actually passed
        $pedidos = [];
        foreach (self::CAMPOS as $opcion => $campo) {
            if ($this->option($opcion)) {
                $pedidos[$opcion] = $this->option($opcion);
            }
        }

        if (empty($pedidos)) {
            $this->error('Provide at least --cv.');
            return 1;
        }

        // JovenController names the folder after the applicant's CUIL, taken from the
        // logged-in user. There is no auth user here, so it comes from the solicitud.
        $cuil = optional(optional($joven->investigador)->persona)->cuil;

        if (!$cuil) {
            $this->error("Joven {$joven->id} has no investigador/persona with CUIL; cannot build the storage path.");
            return 1;
        }

        $anio = $this->option('anio') ?: Constants::YEAR_JOVENES;

        // Mirror JovenController storage path: public/files/jovenes/{YEAR_JOVENES}/{cuil}
        $dir = 'public/files/jovenes/' . $anio . '/' . $cuil;

        $this->line("Carpeta destino: {$dir}");

        $changes = [];

        foreach ($pedidos as $opcion => $sourcePath) {
            $campo = self::CAMPOS[$opcion];
            $url   = $this->storeFile($sourcePath, $dir, $campo['prefijo'], $dryRun);
            if ($url === false) return 1;
            $changes[$campo['columna']] = $url;
        }

        if ($dryRun) {
            $this->info('DRY RUN - would set:');
            foreach ($changes as $col => $val) {
                $anterior = $joven->{$col};
                $this->line("  {$col} = {$val}" . ($anterior ? "   (anterior: {$anterior})" : ''));
            }
            return 0;
        }

        // Update columns directly without firing model events / state changes
        foreach ($changes as $col => $val) {
            if ($this->option('reemplazar')) {
                $this->borrarAnterior($joven->{$col});
            }
            $joven->{$col} = $val;
        }
        $joven->save();

        $this->info("Joven {$joven->id} updated:");
        foreach ($changes as $col => $val) {
            $this->line("  {$col} = {$val}");
        }

        return 0;
    }

    private function storeFile($sourcePath, $dir, $prefix, $dryRun)
    {
        if (!file_exists($sourcePath)) {
            $this->error("Source file not found: {$sourcePath}");
            return false;
        }

        $ext      = pathinfo($sourcePath, PATHINFO_EXTENSION) ?: 'pdf';
        $filename = $prefix . Str::uuid() . '.' . $ext;
        $target   = $dir . '/' . $filename;

        if ($dryRun) {
            return Storage::url($target);
        }

        // Read and store via the same disk the controller uses (storage/app/public)
        Storage::put($target, file_get_contents($sourcePath));

        // Storage::url() yields the /storage/... form expected by the rest of the app
        return Storage::url($target);
    }

    /**
     * Same criterion the controller uses on update: /storage/... -> public/...
     */
    private function borrarAnterior($url)
    {
        if (empty($url)) {
            return;
        }

        $ruta = str_replace('/storage/', 'public/', $url);

        if (Storage::exists($ruta)) {
            Storage::delete($ruta);
            $this->line("  borrado anterior: {$ruta}");
        }
    }
}
