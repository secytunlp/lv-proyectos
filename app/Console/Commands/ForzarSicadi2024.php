<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

class ForzarSicadi2024 extends Command
{
    protected $signature = 'sicadi:forzar-2024';
    protected $description = 'Fuerza investigador_sicadis a coincidir con investigadors para año 2024';

    public function handle()
    {
        $this->info('Forzando SICADI 2024...');

        $investigadores = DB::table('investigadors')
            ->whereIn('sicadi_id', [6,7,8,9,10])
            ->select('id', 'sicadi_id')
            ->get();
        $count = 0;

        foreach ($investigadores as $inv) {

            // borrar registros 2024 existentes
            DB::table('investigador_sicadis')
                ->where('investigador_id', $inv->id)
                ->whereIn('year', [2023, 2024])
                ->delete();

            // insertar el correcto
            DB::table('investigador_sicadis')->insert([
                'investigador_id' => $inv->id,
                'sicadi_id' => $inv->sicadi_id,
                'year' => 2024,
                'actual' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            $count++;
        }

        $this->info("Registros actualizados: {$count}");

        return Command::SUCCESS;
    }
}
