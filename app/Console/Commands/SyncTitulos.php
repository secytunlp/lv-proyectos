<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncTitulos extends Command
{
    protected $signature = 'sync:titulos';
    protected $description = 'Sincroniza titulos desde DB origen a personas';

    public function handle()
    {
        $this->info('Iniciando sincronización...');

        $totalFilas = 0;
        $totalInsertadas = 0;

        $rows = DB::connection('mysql_origen')
            ->table('docente')
            ->select([
                'cd_docente as investigador_id',
                'cd_titulo as titulo_id'
            ])
            ->whereNotNull('cd_titulo')
            ->where('cd_titulo', '!=', '9999')
            ->where('cd_titulo', '!=', 0) // extra defensivo
            ->get();

        $totalFilas = $rows->count();

        $data = $rows->map(function ($row) {

            // evitar basura
            if (!$row->investigador_id || !$row->titulo_id) {
                return null;
            }

            return [
                'investigador_id' => (int) $row->investigador_id,
                'titulo_id' => (int) $row->titulo_id,
                'egreso' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        })
            ->filter() // elimina nulls
            ->values()
            ->toArray();

        DB::connection('mysql')->statement('SET FOREIGN_KEY_CHECKS=0');

        DB::connection('mysql')
            ->table('investigador_titulos')
            ->upsert(
                $data,
                ['investigador_id', 'titulo_id'],
                []
            );

        DB::connection('mysql')->statement('SET FOREIGN_KEY_CHECKS=1');

        $totalInsertadas = count($data);

        $this->info('Sincronización finalizada ✔');
        $this->info("Total filas leídas: $totalFilas");
        $this->info("Filas insertadas/actualizadas: $totalInsertadas");
    }
}
