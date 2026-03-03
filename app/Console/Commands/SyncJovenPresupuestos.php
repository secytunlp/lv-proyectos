<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncJovenPresupuestos extends Command
{
    protected $signature = 'sync:jovenpresupuestos';
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
                    ->table('presupuestojovenes')

                    ->selectRaw("presupuestojovenes.cd_presupuesto as id,presupuestojovenes.cd_solicitud as joven_id, presupuestojovenes.cd_tipopresupuesto as tipo_presupuesto_id, presupuestojovenes.ds_presupuesto as detalle,
       presupuestojovenes.nu_monto as monto, presupuestojovenes.dt_fecha as fecha")

            ->orderBy('presupuestojovenes.cd_presupuesto')

            ->chunk(1000, function ($rows) use (&$totalFilas, &$totalInsertadas, &$totalOmitidas, &$skippedRows) {

                $totalFilas += count($rows);

                $data = collect($rows)->map(function ($row) use (&$skippedRows, &$totalOmitidas) {

                    // Validaciones básicas: persona_id o ident vacíos




                    // 🧹 LIMPIEZA DE FECHA
                    $fecha = $row->fecha;

                    if (
                        empty($fecha) ||
                        $fecha === '0000-00-00' ||
                        $fecha === '0000-00-00 00:00:00'
                    ) {
                        $fecha = null;
                    }



                    return [
                        'id' => $row->id,
                        'joven_id' => $row->joven_id,
                        'tipo_presupuesto_id' => $row->tipo_presupuesto_id,
                        'fecha' => $fecha,

                        'detalle' => trim($row->detalle),
                        'monto' => is_numeric($row->monto) ? (float)$row->monto : null,

                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                })->filter()->toArray();

                if (!empty($data)) {
                    DB::connection('mysql')->statement('SET FOREIGN_KEY_CHECKS=0');
                    DB::connection('mysql')
                        ->table('joven_presupuestos')
                        ->upsert(
                            $data,
                            ['id'], // clave única
                            [
                                'joven_id','tipo_presupuesto_id','fecha','detalle','monto','updated_at'
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
                    "joven: {$skip['joven_id']} - Motivo: {$skip['motivo']} - Beca: {$skip['beca']} - Institucion: {$skip['institucion']}"
                );
            }
        }
    }
}
