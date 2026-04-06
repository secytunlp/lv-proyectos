<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

class VerificarInvestigadorVsHistorialSicadi extends Command
{
    protected $signature = 'sicadi:verificar-investigador-historial';
    protected $description = 'Verifica diferencias entre investigadors y investigador_sicadis actual';

    public function handle()
    {
        $this->info('Buscando diferencias entre investigadors y historial actual...');

        $data = DB::table('investigadors as i')
            ->join('personas as p', 'p.id', '=', 'i.persona_id')
            ->join('sicadis as s_actual', 's_actual.id', '=', 'i.sicadi_id')
            ->join('investigador_sicadis as isd', function ($join) {
                $join->on('isd.investigador_id', '=', 'i.id')
                    ->where('isd.actual', 1);
            })
            ->join('sicadis as s_hist', 's_hist.id', '=', 'isd.sicadi_id')
            ->where('i.sicadi_id', '!=', 1)
            ->whereColumn('i.sicadi_id', '!=', 'isd.sicadi_id')
            ->select(
                'i.id as investigador_id',
                'p.cuil',
                'p.apellido',
                'p.nombre',
                's_actual.nombre as categoria_investigador',
                's_hist.nombre as categoria_historial',
                'isd.year'
            )
            ->orderBy('p.apellido')
            ->get();

        if ($data->isEmpty()) {
            $this->info('No se encontraron diferencias.');
            return Command::SUCCESS;
        }

        foreach ($data as $r) {
            $this->line(
                "ID: {$r->investigador_id} | " .
                "{$r->apellido}, {$r->nombre} | " .
                "CUIL: {$r->cuil} | " .
                "Investigador: {$r->categoria_investigador} | " .
                "Historial: {$r->categoria_historial} | " .
                "Año: {$r->year}"
            );
        }

        $this->info("Total encontrados: " . $data->count());

        return Command::SUCCESS;
    }
}
