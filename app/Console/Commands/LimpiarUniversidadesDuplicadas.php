<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Universidad;
use Illuminate\Support\Facades\DB;

class LimpiarUniversidadesDuplicadas extends Command
{
    protected $signature = 'fix:universidades';
    protected $description = 'Eliminar universidades duplicadas y migrar relaciones';

    public function handle()
    {

        DB::transaction(function () {

            $duplicadas = Universidad::select('nombre')
                ->groupBy('nombre')
                ->havingRaw('COUNT(*) > 1')
                ->get();

            foreach ($duplicadas as $dup) {

                $universidades = Universidad::where('nombre', $dup->nombre)
                    ->orderBy('id')
                    ->get();

                $principal = $universidades->shift();

                $this->info("Universidad principal: {$principal->nombre} ({$principal->id})");

                foreach ($universidades as $extra) {

                    $this->warn("Migrando universidad {$extra->id}");

                    DB::table('investigadors')
                        ->where('universidad_id', $extra->id)
                        ->update(['universidad_id' => $principal->id]);

                    DB::table('titulos')
                        ->where('universidad_id', $extra->id)
                        ->update(['universidad_id' => $principal->id]);

                    DB::table('integrantes')
                        ->where('universidad_id', $extra->id)
                        ->update(['universidad_id' => $principal->id]);

                    DB::table('investigador_cargos')
                        ->where('universidad_id', $extra->id)
                        ->update(['universidad_id' => $principal->id]);

                    DB::table('investigador_categorias')
                        ->where('universidad_id', $extra->id)
                        ->update(['universidad_id' => $principal->id]);

                    $extra->delete();
                }
            }

        });

        $this->info('Limpieza finalizada.');
    }
}
