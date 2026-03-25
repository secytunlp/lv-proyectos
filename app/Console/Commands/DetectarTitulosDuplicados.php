<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DetectarTitulosDuplicados extends Command
{
    protected $signature = 'detect:titulos-duplicados';
    protected $description = 'Detecta títulos duplicados por nombre y universidad';

    public function handle()
    {
        $grupos = DB::table('titulos')
            ->select('nombre', 'universidad_id', DB::raw('COUNT(*) as total'))
            ->groupBy('nombre', 'universidad_id')
            ->having('total', '>', 1)
            ->get();

        foreach ($grupos as $grupo) {

            $this->warn("\nDuplicados encontrados:");

            $titulos = DB::table('titulos')
                ->where('nombre', $grupo->nombre)
                ->where('universidad_id', $grupo->universidad_id)
                ->get();

            foreach ($titulos as $t) {
                $this->line("{$t->id} - {$t->nombre} (Universidad {$t->universidad_id})");
            }

            if ($this->confirm('¿Querés fusionarlos?')) {

                $ids = $titulos->pluck('id')->toArray();

                $mantener = $this->choice(
                    '¿Qué ID querés conservar?',
                    $ids
                );

                foreach ($ids as $id) {

                    if ($id != $mantener) {

                        $this->fusionarTitulos($mantener, $id);

                        $this->info("Título {$id} fusionado con {$mantener}");
                    }

                }
            }

            $this->line("--------------------------------");
        }

        $this->info("Proceso finalizado.");
    }

    private function fusionarTitulos($mantener, $eliminar)
    {
        DB::transaction(function () use ($mantener, $eliminar) {

            // Investigador
            DB::table('investigadors')
                ->where('titulo_id', $eliminar)
                ->update(['titulo_id' => $mantener]);

            DB::table('investigadors')
                ->where('titulopost_id', $eliminar)
                ->update(['titulopost_id' => $mantener]);

            // Pivot investigador_titulos
            $pivotTitulos = DB::table('investigador_titulos')
                ->where('titulo_id', $eliminar)
                ->get();

            foreach ($pivotTitulos as $pivot) {
                $exists = DB::table('investigador_titulos')
                    ->where('investigador_id', $pivot->investigador_id)
                    ->where('titulo_id', $mantener)
                    ->exists();

                if ($exists) {
                    // Ya existe → eliminar
                    DB::table('investigador_titulos')
                        ->where('investigador_id', $pivot->investigador_id)
                        ->where('titulo_id', $eliminar)
                        ->delete();
                } else {
                    // No existe → actualizar
                    DB::table('investigador_titulos')
                        ->where('investigador_id', $pivot->investigador_id)
                        ->where('titulo_id', $eliminar)
                        ->update(['titulo_id' => $mantener]);
                }
            }

            // Pivot investigador_tituloposts
            DB::table('investigador_tituloposts')
                ->where('titulo_id', $eliminar)
                ->update(['titulo_id' => $mantener]);

            DB::table('integrantes')
                ->where('titulo_id', $eliminar)
                ->update(['titulo_id' => $mantener]);

            DB::table('integrantes')
                ->where('titulopost_id', $eliminar)
                ->update(['titulopost_id' => $mantener]);
            // Joven
            DB::table('jovens')
                ->where('titulo_id', $eliminar)
                ->update(['titulo_id' => $mantener]);

            DB::table('jovens')
                ->where('titulopost_id', $eliminar)
                ->update(['titulopost_id' => $mantener]);

            // Viaje
            DB::table('viajes')
                ->where('titulo_id', $eliminar)
                ->update(['titulo_id' => $mantener]);

            // eliminar duplicado
            DB::table('titulos')
                ->where('id', $eliminar)
                ->delete();
        });
    }
}
