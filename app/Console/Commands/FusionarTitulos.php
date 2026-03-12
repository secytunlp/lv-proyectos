<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FusionarTitulos extends Command
{
    protected $signature = 'titulos:fusionar {mantener} {eliminar}';
    protected $description = 'Fusiona un título con otro';

    public function handle()
    {
        $mantener = $this->argument('mantener');
        $eliminar = $this->argument('eliminar');

        $tituloMantener = DB::table('titulos')->where('id', $mantener)->first();
        $tituloEliminar = DB::table('titulos')->where('id', $eliminar)->first();

        if (!$tituloMantener) {
            $this->error("El título {$mantener} no existe");
            return;
        }

        if (!$tituloEliminar) {
            $this->error("El título {$eliminar} no existe");
            return;
        }

        $this->info("Mantener: {$tituloMantener->nombre}");
        $this->info("Eliminar: {$tituloEliminar->nombre}");

        if (!$this->confirm('¿Continuar con la fusión?')) {
            return;
        }

        DB::transaction(function () use ($mantener, $eliminar) {

            DB::table('investigadors')
                ->where('titulo_id', $eliminar)
                ->update(['titulo_id' => $mantener]);

            DB::table('investigadors')
                ->where('titulopost_id', $eliminar)
                ->update(['titulopost_id' => $mantener]);

            DB::table('investigador_titulos')
                ->where('titulo_id', $eliminar)
                ->update(['titulo_id' => $mantener]);

            DB::table('investigador_tituloposts')
                ->where('titulo_id', $eliminar)
                ->update(['titulo_id' => $mantener]);

            DB::table('integrantes')
                ->where('titulo_id', $eliminar)
                ->update(['titulo_id' => $mantener]);

            DB::table('integrantes')
                ->where('titulopost_id', $eliminar)
                ->update(['titulopost_id' => $mantener]);

            DB::table('jovens')
                ->where('titulo_id', $eliminar)
                ->update(['titulo_id' => $mantener]);

            DB::table('jovens')
                ->where('titulopost_id', $eliminar)
                ->update(['titulopost_id' => $mantener]);

            DB::table('viajes')
                ->where('titulo_id', $eliminar)
                ->update(['titulo_id' => $mantener]);

            DB::table('titulos')
                ->where('id', $eliminar)
                ->delete();
        });

        $this->info("Título {$eliminar} fusionado con {$mantener}");
    }
}
