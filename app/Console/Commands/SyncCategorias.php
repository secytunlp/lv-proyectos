<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncCategorias extends Command
{
    protected $signature = 'sync:carreras';
    protected $description = 'Sincroniza categorias desde DB origen a personas';

    public function handle()
    {
        $this->info('Iniciando sincronización...');

        $totalFilas = 0;
        $totalInsertadas = 0;

        $rows = DB::connection('mysql_origen')
            ->table('docente')
            ->select([
                'cd_docente as investigador_id',
                'cd_categoria as categoria_id',
                'cd_univcat as universidad_id'
            ])

            ->whereNotNull('cd_categoria')

            ->whereIn('cd_categoria', [6,7,8,9,10])

            ->get();



        $totalFilas = $rows->count();

        $data = $rows->map(function ($row) {

            // evitar basura
            if (!$row->investigador_id || !$row->categoria_id) {
                return null;
            }

            return [
                'investigador_id' => (int) $row->investigador_id,
                'categoria_id' => (int) $row->categoria_id,
                'universidad_id' => (int) $row->universidad_id,
                'year' => null,
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
            ->table('investigador_categorias')
            ->upsert(
                $data,
                ['investigador_id', 'categoria_id', 'year'],
                ['updated_at']
            );

        DB::connection('mysql')->statement('SET FOREIGN_KEY_CHECKS=1');

        $totalInsertadas = count($data);

        $this->info('Sincronización finalizada ✔');
        $this->info("Total filas leídas: $totalFilas");
        $this->info("Filas insertadas/actualizadas: $totalInsertadas");
    }
}
