<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

class VerificarHistorialSicadi extends Command
{
    protected $signature = 'sicadi:verificar-historial';
    protected $description = 'Categorias en investigador_sicadis que no existen en solicitud_sicadis';

    public function handle()
    {
        $this->info('Buscando categorías en historial que no están en solicitudes...');

        $resultados = DB::table('investigador_sicadis as isd')
            ->join('investigadors as i', 'i.id', '=', 'isd.investigador_id')
            ->join('personas as p', 'p.id', '=', 'i.persona_id')
            ->join('sicadis as s', 's.id', '=', 'isd.sicadi_id')
            ->leftJoin('solicitud_sicadis as ss', function ($join) {
                $join->on('ss.cuil', '=', 'p.cuil')
                    ->whereRaw('UPPER(TRIM(ss.categoria_asignada)) = UPPER(TRIM(s.nombre))');
            })
            ->whereNull('ss.id')
            ->select(
                'i.id as investigador_id',
                'p.cuil',
                's.nombre as categoria_historial',
                'isd.year',
                'isd.id as historial_id'
            )
            ->get();

        if ($resultados->isEmpty()) {
            $this->info('No se encontraron diferencias.');
            return Command::SUCCESS;
        }

        foreach ($resultados as $r) {
            $this->line(
                "Investigador: {$r->investigador_id} | " .
                "CUIL: {$r->cuil} | " .
                "Historial: {$r->categoria_historial} | " .
                "Año: {$r->year}"
            );
        }

        $this->info("Total encontrados: " . $resultados->count());

        return Command::SUCCESS;
    }
}
