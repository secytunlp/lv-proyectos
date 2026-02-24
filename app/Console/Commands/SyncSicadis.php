<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncSicadis extends Command
{
    protected $signature = 'sync:sicadis';
    protected $description = 'Sincroniza sicadis desde DB origen a personas';

    public function handle()
    {
        $this->info('Iniciando sincronización...');

        $totalFilas = 0;
        $totalInsertadas = 0;

        $rows = DB::connection('mysql_origen')
            ->table('docente')
            ->select([
                'cd_docente as investigador_id',
                'cd_sicadi as sicadi_id',
                'cd_univcat as universidad_id'
            ])

            ->whereNotNull('cd_sicadi')

            ->whereIn('cd_sicadi', [6,7,8,9,10])

            ->get();



        $totalFilas = $rows->count();

        $data = $rows->map(function ($row) {

            // evitar basura
            if (!$row->investigador_id || !$row->sicadi_id) {
                return null;
            }

            return [
                'investigador_id' => (int) $row->investigador_id,
                'sicadi_id' => (int) $row->sicadi_id,
                'universidad_id' => (int) $row->universidad_id,
                'year' => 2023,
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
            ->table('investigador_sicadis')
            ->upsert(
                $data,
                ['investigador_id', 'sicadi_id', 'year'],
                ['updated_at']
            );

        DB::connection('mysql')->statement('SET FOREIGN_KEY_CHECKS=1');

        $totalInsertadas = count($data);

        $this->info('Sincronización finalizada ✔');
        $this->info("Total filas leídas: $totalFilas");
        $this->info("Filas insertadas/actualizadas: $totalInsertadas");
    }
}
