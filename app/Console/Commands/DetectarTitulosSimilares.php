<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DetectarTitulosSimilares extends Command
{
    protected $signature = 'detect:titulos-similares';
    protected $description = 'Detecta títulos similares por porcentaje';

    public function handle()
    {
        $universidades = DB::table('titulos')
            ->select('universidad_id')
            ->distinct()
            ->pluck('universidad_id');

        foreach ($universidades as $universidad_id) {

            $this->info("\nUniversidad: {$universidad_id}");

            $titulos = DB::table('titulos')
                ->where('universidad_id', $universidad_id)
                ->get();

            foreach ($titulos as $t1) {

                foreach ($titulos as $t2) {

                    if ($t1->id >= $t2->id) {
                        continue;
                    }

                    $nombre1 = $this->limpiarTitulo($t1->nombre);
                    $nombre2 = $this->limpiarTitulo($t2->nombre);

                    similar_text($nombre1, $nombre2, $porcentaje);

                    if ($porcentaje >= 85) {

                        $this->warn("\nSimilitud {$porcentaje}%");

                        $this->line("{$t1->id} - {$t1->nombre}");
                        $this->line("{$t2->id} - {$t2->nombre}");

                        if ($this->confirm('¿Querés fusionarlos?')) {

                            $mantener = $this->choice(
                                '¿Qué ID querés conservar?',
                                [$t1->id, $t2->id]
                            );

                            $eliminar = $mantener == $t1->id ? $t2->id : $t1->id;

                            $this->fusionarTitulos($mantener, $eliminar);

                            $this->info("Título {$eliminar} fusionado con {$mantener}");
                        }

                        $this->line('------------------------------');
                    }
                }
            }
        }

        $this->info("Proceso finalizado.");
    }

    private function limpiarTitulo($texto)
    {
        $texto = strtolower($texto);

        $prefijos = [
            'licenciado en',
            'licenciada en',
            'licenciatura en',
            'ingeniero en',
            'ingeniera en',
            'ingenieria en',
            'doctorado en',
            'doctor en',
            'doctora en',
            'maestria en',
            'magister en',
            'medico especialista',
            'medica especialista'
        ];

        foreach ($prefijos as $p) {
            $texto = str_replace($p, '', $texto);
        }

        return trim($texto);
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
