<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use App\Models\SolicitudSicadi;

class AsignarFotosSicadi extends Command
{
    protected $signature = 'sicadi:asignar-fotos';
    protected $description = 'Asigna archivos de imagen a solicitudes SICADI según el CUIL';

    public function handle()
    {
        $ruta = 'E:\Documents\Mis Webs\lv-proyectos\public\images\sicadi';

        if (!File::exists($ruta)) {
            $this->error("La ruta no existe: $ruta");
            return 1;
        }

        $archivos = File::files($ruta);
        $asignados = 0;
        $noEncontrados = 0;

        foreach ($archivos as $archivo) {
            if ($archivo->getExtension() !== 'png') {
                continue;
            }

            $cuil = pathinfo($archivo->getFilename(), PATHINFO_FILENAME);

            //$solicitud = SolicitudSicadi::where('cuil', $cuil)->first();
            // Buscar solicitud cuyo CUIL, sin guiones, coincida
            $solicitud = SolicitudSicadi::get()->first(function ($s) use ($cuil) {
                return str_replace('-', '', $s->cuil) === $cuil;
            });

            if ($solicitud) {
                $solicitud->foto = $archivo->getFilename();
                $solicitud->save();
                $this->info("✔ Asignado {$archivo->getFilename()} a ID {$solicitud->id}");
                $asignados++;
            } else {
                $this->warn("✖ No se encontró solicitud con CUIL $cuil");
                $noEncontrados++;
            }
        }

        $this->line("-----");
        $this->info("Total asignados: $asignados");
        $this->info("No encontrados: $noEncontrados");

        return 0;
    }
}
