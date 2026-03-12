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

                    if (str_contains($nombre1, 'orientacion') && str_contains($nombre2, 'orientacion')) {

                        $o1 = trim(explode('orientacion', $nombre1)[1]);
                        $o2 = trim(explode('orientacion', $nombre2)[1]);

                        if ($o1 !== $o2) {
                            continue; // son orientaciones distintas
                        }
                    }

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

                            // obtener nombres originales
                            $tituloMantener = DB::table('titulos')->where('id', $mantener)->first();
                            $tituloEliminar = DB::table('titulos')->where('id', $eliminar)->first();

                            $nuevoNombre = $this->unificarGenero(
                                strtolower($tituloMantener->nombre),
                                strtolower($tituloEliminar->nombre)
                            );

                            if ($nuevoNombre) {
                                DB::table('titulos')
                                    ->where('id', $mantener)
                                    ->update(['nombre' => $nuevoNombre]);

                                $this->info("Nombre actualizado a: {$nuevoNombre}");
                            }

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
        $texto = str_replace('ñ', 'n', $texto);
        $prefijos = [



            'licenciado en lengua y literatura',

            'profesor en lengua y literatura',
            'profesor de lengua y literatura',


            'profesor de musica especialidad',

            'profesor/a de',
            'profesor de',
            'profesora de',

            'magister scientiae',


            'abogado especializado en derecho',
            'abogada especializada en derecho',

            'especialista en derecho',

        ];

        foreach ($prefijos as $p) {
            $texto = str_replace($p, '', $texto);
        }

        // quedarse con lo que está después del último " en "
        if (str_contains($texto, ' en ')) {
            $partes = explode(' en ', $texto);
            $texto = end($partes);
        }



        return trim($texto);
    }

    private function unificarGenero($nombre1, $nombre2)
    {
        $n1 = strtolower($nombre1);
        $n2 = strtolower($nombre2);

        $n1 = iconv('UTF-8', 'ASCII//TRANSLIT', $n1);
        $n2 = iconv('UTF-8', 'ASCII//TRANSLIT', $n2);

        $n1 = preg_replace('/\s+/', ' ', trim($n1));
        $n2 = preg_replace('/\s+/', ' ', trim($n2));

        $palabras1 = explode(' ', $n1);
        $palabras2 = explode(' ', $n2);

        if (count($palabras1) !== count($palabras2)) {
            return null;
        }

        for ($i = 0; $i < count($palabras1); $i++) {

            if ($palabras1[$i] !== $palabras2[$i]) {

                $p1 = $palabras1[$i];
                $p2 = $palabras2[$i];

                if (
                    substr($p1, -1) === 'o' &&
                    substr($p2, -1) === 'a' &&
                    substr($p1, 0, -1) === substr($p2, 0, -1)
                ) {
                    $palabras1[$i] = substr($p1, 0, -1) . 'o/a';
                } else {
                    return null;
                }
            }
        }

        return strtoupper(implode(' ', $palabras1));
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
