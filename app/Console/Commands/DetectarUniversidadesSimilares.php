<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Universidad;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class DetectarUniversidadesSimilares extends Command
{
    protected $signature = 'detect:universidades {--percent=85}';
    protected $description = 'Detecta universidades con nombres similares';

    public function handle()
    {
        $universidades = Universidad::all();
        $threshold = (int) $this->option('percent');

        foreach ($universidades as $u1) {

            foreach ($universidades as $u2) {

                if ($u1->id >= $u2->id) {
                    continue;
                }

                similar_text(
                    $this->normalizar($u1->nombre),
                    $this->normalizar($u2->nombre),
                    $percent
                );

                if ($percent >= $threshold) {

                    $this->warn("\nSimilitud {$percent}%");

                    $this->line("{$u1->id} - {$u1->nombre}");
                    $this->line("{$u2->id} - {$u2->nombre}");

                    if ($this->confirm('¿Fusionar estas universidades?')) {

                        $opciones = [
                            "{$u1->id} - {$u1->nombre}",
                            "{$u2->id} - {$u2->nombre}"
                        ];

                        $seleccion = $this->choice(
                            '¿Con cuál querés quedarte?',
                            $opciones
                        );

                        $mantener = str_starts_with($seleccion, $u1->id) ? $u1->id : $u2->id;
                        $eliminar = $mantener == $u1->id ? $u2->id : $u1->id;

                        $this->fusionarUniversidades($mantener, $eliminar);

                        $this->info("Fusionadas correctamente.");
                    }

                    $this->line("--------------------------------");
                }

            }
        }

        $this->info("Detección finalizada.");
    }

    private function normalizar($texto)
    {
        $texto = Str::ascii($texto);
        $texto = strtolower($texto);

        // eliminar palabras comunes
        $stopwords = [
            'universidad','nacional','de','del','la','las','los',
            'faculty','facultad'
        ];

        $texto = str_replace($stopwords, '', $texto);

        // quitar símbolos
        $texto = preg_replace('/[^a-z0-9 ]/', ' ', $texto);

        // limpiar espacios
        $texto = trim(preg_replace('/\s+/', ' ', $texto));

        return $texto;
    }

    private function fusionarUniversidades($mantener, $eliminar)
    {
        DB::transaction(function () use ($mantener, $eliminar) {

            DB::table('investigadors')
                ->where('universidad_id', $eliminar)
                ->update(['universidad_id' => $mantener]);

            DB::table('titulos')
                ->where('universidad_id', $eliminar)
                ->update(['universidad_id' => $mantener]);

            DB::table('integrantes')
                ->where('universidad_id', $eliminar)
                ->update(['universidad_id' => $mantener]);

            DB::table('investigador_cargos')
                ->where('universidad_id', $eliminar)
                ->update(['universidad_id' => $mantener]);

            DB::table('investigador_categorias')
                ->where('universidad_id', $eliminar)
                ->update(['universidad_id' => $mantener]);

            DB::table('universidads')
                ->where('id', $eliminar)
                ->delete();
        });
    }
}
