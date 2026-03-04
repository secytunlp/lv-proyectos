<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncViajeMontos extends Command
{
    protected $signature = 'sync:viajemontos';
    protected $description = 'Sincroniza becas UNLP desde DB origen a investigadors';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->info('Iniciando sincronización de investigadors...');

        $skippedRows = [];
        $totalFilas = 0;
        $totalInsertadas = 0;
        $totalOmitidas = 0;

        DB::connection('mysql_origen')
                    ->table('monto')

                    ->selectRaw("monto.cd_monto as id, monto.cd_solicitud as viaje_id, monto.nu_monto as monto, monto.ds_institucion as institucion, monto.ds_caracter as caracter")

            ->orderBy('monto.cd_monto')

            ->chunk(1000, function ($rows) use (&$totalFilas, &$totalInsertadas, &$totalOmitidas, &$skippedRows) {

                $totalFilas += count($rows);

                $data = collect($rows)->map(function ($row) use (&$skippedRows, &$totalOmitidas) {

                    // Validaciones básicas: persona_id o ident vacíos







                    return [
                        'id' => $row->id,
                        'viaje_id' => $row->viaje_id,
                        'institucion' => trim($row->institucion),


                        'caracter' => trim($row->caracter),
                        'monto' => is_numeric($row->monto) ? (float)$row->monto : null,

                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                })->filter()->toArray();

                if (!empty($data)) {
                    DB::connection('mysql')->statement('SET FOREIGN_KEY_CHECKS=0');
                    DB::connection('mysql')
                        ->table('viaje_montos')
                        ->upsert(
                            $data,
                            ['id'], // clave única
                            [
                                'viaje_id','institucion','caracter','monto','updated_at'
                            ]
                        );
                    DB::connection('mysql')->statement('SET FOREIGN_KEY_CHECKS=1');
                    $totalInsertadas += count($data);
                }
            });

        $this->info('Sincronización finalizada ✔');
        $this->info("Total filas leídas: $totalFilas");
        $this->info("Filas insertadas/actualizadas: $totalInsertadas");
        $this->info("Filas omitidas: $totalOmitidas");

        if (!empty($skippedRows)) {
            $this->info("Detalle de filas omitidas:");
            foreach ($skippedRows as $skip) {
                $this->line(
                    "viaje: {$skip['viaje_id']} - Motivo: {$skip['motivo']} - Beca: {$skip['beca']} - Institucion: {$skip['institucion']}"
                );
            }
        }
    }
}
