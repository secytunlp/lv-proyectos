<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncCarreras extends Command
{
    protected $signature = 'sync:carreras';
    protected $description = 'Sincroniza carreras desde DB origen a personas';

    public function handle()
    {
        $this->info('Iniciando sincronización...');

        $totalFilas = 0;
        $totalInsertadas = 0;

        $rows = DB::connection('mysql_origen')
            ->table('docente')
            ->select([
                'cd_docente as investigador_id',
                'cd_carrerainv as carrerainv_id',
                'cd_organismo as organismo_id'
            ])

            ->whereNotNull('cd_carrerainv')
            ->whereNotNull('cd_organismo')
            ->whereIn('cd_carrerainv', [1,2,3,4,5,6,8,9,12,13])
            ->whereIn('cd_organismo', [1,2,3,4,5,8,9])
            ->get();



        $totalFilas = $rows->count();

        $data = $rows->map(function ($row) {

            // evitar basura
            if (!$row->investigador_id || !$row->carrerainv_id || !$row->organismo_id) {
                return null;
            }

            return [
                'investigador_id' => (int) $row->investigador_id,
                'carrerainv_id' => (int) $row->carrerainv_id,
                'organismo_id' => (int) $row->organismo_id,
                'ingreso' => null,
                'actual' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        })
            ->filter() // elimina nulls
            ->values()
            ->toArray();


        DB::connection('mysql')->statement('SET FOREIGN_KEY_CHECKS=0');

        DB::connection('mysql')
            ->table('investigador_carreras')
            ->upsert(
                $data,
                ['investigador_id', 'carrerainv_id', 'organismo_id'],
                ['updated_at']
            );

        DB::connection('mysql')->statement('SET FOREIGN_KEY_CHECKS=1');

        $totalInsertadas = count($data);

        $this->info('Sincronización finalizada ✔');
        $this->info("Total filas leídas: $totalFilas");
        $this->info("Filas insertadas/actualizadas: $totalInsertadas");
    }
}
