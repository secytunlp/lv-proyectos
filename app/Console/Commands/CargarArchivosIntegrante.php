<?php

namespace App\Console\Commands;

use App\Constants;
use App\Models\Integrante;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CargarArchivosIntegrante extends Command
{
    // Usage:
    //   php artisan integrante:cargar-archivos {id} --cv=/path/CV.pdf --plan=/path/PLAN.pdf
    protected $signature = 'integrante:cargar-archivos
                            {id : Integrante ID}
                            {--cv= : Absolute path to the curriculum file}
                            {--plan= : Absolute path to the work plan file}
                            {--dry-run : Show what would change without writing}';

    protected $description = 'Attach curriculum and/or work plan files to an existing integrante without altering its state';

    public function handle()
    {
        $integrante = Integrante::find($this->argument('id'));

        if (!$integrante) {
            $this->error("Integrante {$this->argument('id')} not found.");
            return 1;
        }

        $cvPath   = $this->option('cv');
        $planPath = $this->option('plan');
        $dryRun   = $this->option('dry-run');

        if (!$cvPath && !$planPath) {
            $this->error('Provide at least --cv or --plan.');
            return 1;
        }

        $proyectoId = $integrante->proyecto_id;
        // Mirror IntegranteController storage path: public/files/{YEAR}/{proyecto_id}
        $dir = 'public/files/' . Constants::YEAR . '/' . $proyectoId;

        $changes = [];

        if ($cvPath) {
            $url = $this->storeFile($cvPath, $dir, 'CV_', $dryRun);
            if ($url === false) return 1;
            $changes['curriculum'] = $url;
        }

        if ($planPath) {
            $url = $this->storeFile($planPath, $dir, 'PLAN_', $dryRun);
            if ($url === false) return 1;
            $changes['actividades'] = $url;
        }

        if ($dryRun) {
            $this->info('DRY RUN - would set:');
            foreach ($changes as $col => $val) {
                $this->line("  {$col} = {$val}");
            }
            return 0;
        }

        // Update columns directly without firing model events / state changes
        foreach ($changes as $col => $val) {
            $integrante->{$col} = $val;
        }
        $integrante->save();

        $this->info("Integrante {$integrante->id} updated:");
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
}
