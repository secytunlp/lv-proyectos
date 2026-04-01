<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SolicitudSicadi;
use App\Models\Investigador;
use DB;

class VerificarCategoriasSicadi extends Command
{
    protected $signature = 'sicadi:verificar';
    protected $description = 'Verifica categorias SICADI asignadas distintas al sicadi_id del investigador';

    public function handle()
    {
        $this->info('Buscando diferencias de categorías SICADI...');

        $resultados = DB::table('solicitud_sicadis as s')
            ->join('personas as p', 'p.cuil', '=', 's.cuil')
            ->join('investigadors as i', 'i.persona_id', '=', 'p.id')
            ->join('sicadis', 'i.sicadi_id', '=', 'sicadis.id')
            ->whereNotNull('s.categoria_asignada')
            ->whereColumn('s.categoria_asignada', '!=', 'sicadis.nombre')
            ->select(
                'i.id as investigador_id',
                'p.cuil',
                'sicadis.nombre as categoria_actual',
                's.categoria_asignada as categoria_nueva',
                's.id as solicitud_id'
            )
            ->get();

        if ($resultados->isEmpty()) {
            $this->info('No se encontraron diferencias.');
            return Command::SUCCESS;
        }

        foreach ($resultados as $r) {
            $this->line(
                "Investigador: {$r->investigador_id} | " .
                "Doc: {$r->cuil} | " .
                "Actual: {$r->categoria_actual} | " .
                "Nueva: {$r->categoria_nueva} | " .
                "Solicitud: {$r->solicitud_id}"
            );
        }

        $this->info("Total encontrados: " . $resultados->count());

        return Command::SUCCESS;
    }
}
