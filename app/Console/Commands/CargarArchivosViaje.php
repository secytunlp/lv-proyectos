<?php

namespace App\Console\Commands;

use App\Constants;
use App\Models\Viaje;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CargarArchivosViaje extends Command
{
    // Usage:
    //   php artisan viaje:cargar-archivos {id} --cv=/path/CV.pdf --trabajo=/path/T.pdf
    //   php artisan viaje:cargar-archivos {id} --aval=/path/AVAL.pdf --anio=2025 --reemplazar
    protected $signature = 'viaje:cargar-archivos
                            {id : Viaje (solicitud) ID}
                            {--cv= : Absolute path to the curriculum file}
                            {--trabajo= : Absolute path to the trabajo file}
                            {--aceptacion= : Absolute path to the aceptacion file}
                            {--invitacion= : Absolute path to the invitacion file}
                            {--convenio-b= : Absolute path to the convenio (tipo B) file}
                            {--convenio-c= : Absolute path to the convenio (tipo C) file}
                            {--aval= : Absolute path to the aval file}
                            {--cvprofesor= : Absolute path to the profesor curriculum file}
                            {--anio= : Convocatoria folder to use (defaults to Constants::YEAR_VIAJES)}
                            {--reemplazar : Delete the previous file stored in each column}
                            {--dry-run : Show what would change without writing}';

    protected $description = 'Attach files to an existing viaje solicitud without altering its state';

    /**
     * Columns that hold a file, with the filename prefix ViajeController uses.
     */
    private const CAMPOS = [
        'cv'         => ['columna' => 'curriculum', 'prefijo' => 'CV_'],
        'trabajo'    => ['columna' => 'trabajo',    'prefijo' => 'Trabajo_'],
        'aceptacion' => ['columna' => 'aceptacion', 'prefijo' => 'Aceptacion_'],
        'invitacion' => ['columna' => 'invitacion', 'prefijo' => 'Invitacion_'],
        'convenio-b' => ['columna' => 'convenioB',  'prefijo' => 'ConvenioB_'],
        'convenio-c' => ['columna' => 'convenioC',  'prefijo' => 'ConvenioC_'],
        'aval'       => ['columna' => 'aval',       'prefijo' => 'Aval_'],
        'cvprofesor' => ['columna' => 'cvprofesor', 'prefijo' => 'CVProfesor_'],
    ];

    public function handle()
    {
        $viaje = Viaje::find($this->argument('id'));

        if (!$viaje) {
            $this->error("Viaje {$this->argument('id')} not found.");
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
            $this->error('Provide at least one of: --cv, --trabajo, --aceptacion, --invitacion, --convenio-b, --convenio-c, --aval, --cvprofesor.');
            return 1;
        }

        // ViajeController names the folder after the applicant's CUIL, taken from the
        // logged-in user. There is no auth user here, so it comes from the solicitud.
        $cuil = optional(optional($viaje->investigador)->persona)->cuil;

        if (!$cuil) {
            $this->error("Viaje {$viaje->id} has no investigador/persona with CUIL; cannot build the storage path.");
            return 1;
        }

        $anio = $this->option('anio') ?: Constants::YEAR_VIAJES;

        // Mirror ViajeController storage path: public/files/viajes/{YEAR_VIAJES}/{cuil}
        $dir = 'public/files/viajes/' . $anio . '/' . $cuil;

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
                $anterior = $viaje->{$col};
                $this->line("  {$col} = {$val}" . ($anterior ? "   (anterior: {$anterior})" : ''));
            }
            return 0;
        }

        // Update columns directly without firing model events / state changes
        foreach ($changes as $col => $val) {
            if ($this->option('reemplazar')) {
                $this->borrarAnterior($viaje->{$col});
            }
            $viaje->{$col} = $val;
        }
        $viaje->save();

        $this->info("Viaje {$viaje->id} updated:");
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
