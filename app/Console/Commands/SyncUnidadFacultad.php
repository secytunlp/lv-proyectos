<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncUnidadFacultad extends Command
{
    protected $signature = 'sync:unidadfacultads';
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
                    ->table('cyt_unidad_facultad')

                    ->selectRaw("cyt_unidad_facultad.oid as id, cyt_unidad_facultad.unidad_oid as unidad_id, cyt_unidad_facultad.facultad_oid as facultad_id")

            ->orderBy('cyt_unidad_facultad')

            ->chunk(1000, function ($rows) use (&$totalFilas, &$totalInsertadas, &$totalOmitidas, &$skippedRows) {

                $totalFilas += count($rows);

                $data = collect($rows)->map(function ($row) use (&$skippedRows, &$totalOmitidas) {

                    // Validaciones básicas: persona_id o ident vacíos




                    return [
                        'id' => $row->id,
                        'unidad_id' => $row->unidad_id,
                        'facultad_id' => $row->facultad_id,


                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                })->filter()->toArray();

                if (!empty($data)) {
                    DB::connection('mysql')->statement('SET FOREIGN_KEY_CHECKS=0');
                    DB::connection('mysql')
                        ->table('unidad_facultads')
                        ->upsert(
                            $data,
                            ['id'], // clave única
                            [
                                'unidad_id','facultad_id','updated_at'
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
                    "unidad: {$skip['unidad_id']} - Motivo: {$skip['motivo']} - Beca: {$skip['beca']} - Institucion: {$skip['institucion']}"
                );
            }
        }
    }
}
