<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\SolicitudSicadi;

class MoverFotosSicadi extends Command
{
    protected $signature = 'sicadi:mover-fotos';
    protected $description = 'Mover fotos desde /public/images/sicadi a storage y actualizar la base de datos';

    public function handle()
    {
        $solicitudes = SolicitudSicadi::whereNotNull('foto')->get();

        foreach ($solicitudes as $solicitud) {
            $fotoNombre = $solicitud->foto;

            // Saltear si el campo está vacío o no es un nombre de archivo (por ejemplo, ya tiene /storage/ o es una URL)
            if (empty($fotoNombre) || str_contains($fotoNombre, '/storage/')) {
                $this->warn("Campo 'foto' inválido o ya migrado: $fotoNombre");
                continue;
            }

            $rutaActual = public_path('images/sicadi/' . $fotoNombre);

            if (!file_exists($rutaActual)) {
                $this->warn("Archivo no encontrado: $rutaActual");
                continue;
            }

            $extension = pathinfo($fotoNombre, PATHINFO_EXTENSION);
            $nuevoNombre = 'foto_' . Str::uuid() . '.' . $extension;
            $nuevaRuta = 'public/images/sicadi/' . $nuevoNombre;

            // Mover archivo
            Storage::put($nuevaRuta, file_get_contents($rutaActual));

            // Actualizar DB
            $solicitud->foto = Storage::url($nuevaRuta); // guarda como /storage/images/sicadi/...
            $solicitud->save();

            // Eliminar archivo viejo
            unlink($rutaActual);

            $this->info("Archivo $fotoNombre movido y renombrado a $nuevoNombre");
        }


        $this->info('Proceso finalizado.');
        return 0;
    }
}

