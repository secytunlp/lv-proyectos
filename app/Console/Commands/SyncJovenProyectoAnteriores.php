<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncJovenProyectoAnteriores extends Command
{
    protected $signature = 'sync:jovenproyectoanteriores';
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
                    ->table('solicitudjovenesproyecto')
                    ->leftJoin('solicitudjovenes', 'solicitudjovenesproyecto.cd_solicitud', '=', 'solicitudjovenes.cd_solicitud')
                    ->leftJoin('periodo', 'solicitudjovenes.cd_periodo', '=', 'periodo.cd_periodo')

                    ->selectRaw("solicitudjovenesproyecto.cd_solicitud as joven_id, solicitudjovenesproyecto.cd_proyecto as proyecto_id, solicitudjovenesproyecto.dt_hasta as hasta,
    solicitudjovenesproyecto.dt_desde as desde, solicitudjovenesproyecto.ds_codigo as codigo, solicitudjovenesproyecto.ds_director as director,
    solicitudjovenesproyecto.ds_titulo as titulo, solicitudjovenesproyecto.bl_agregado as agregado")
            ->whereRaw("periodo.ds_periodo < YEAR(solicitudjovenesproyecto.dt_desde) OR
    periodo.ds_periodo > IFNULL(YEAR(solicitudjovenesproyecto.dt_hasta), YEAR(solicitudjovenesproyecto.dt_desde))")
            ->orderBy('solicitudjovenesproyecto.cd_solicitudjovenesproyecto')

            ->chunk(1000, function ($rows) use (&$totalFilas, &$totalInsertadas, &$totalOmitidas, &$skippedRows) {

                $totalFilas += count($rows);

                $data = collect($rows)->map(function ($row) use (&$skippedRows, &$totalOmitidas) {

                    // Validaciones básicas: persona_id o ident vacíos




                    // 🧹 LIMPIEZA DE FECHA
                    $desde = $row->desde;

                    if (
                        empty($desde) ||
                        $desde === '0000-00-00' ||
                        $desde === '0000-00-00 00:00:00'
                    ) {
                        $desde = null;
                    }

                    $hasta = $row->hasta;

                    if (
                        empty($hasta) ||
                        $hasta === '0000-00-00' ||
                        $hasta === '0000-00-00 00:00:00'
                    ) {
                        $hasta = null;
                    }

                    return [
                        'id' => $row->id,
                        'joven_id' => $row->joven_id,
                        'proyecto_id' => $row->proyecto_id,
                        'desde' => $desde,
                        'hasta' => $hasta,
                        'codigo' => trim($row->codigo),
                        'director' => trim($row->director),
                        'titulo' => trim($row->titulo),
                        'agregado' => $row->agregado ?: 0,
                        'actual' => 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                })->filter()->toArray();

                if (!empty($data)) {
                    DB::connection('mysql')->statement('SET FOREIGN_KEY_CHECKS=0');
                    DB::connection('mysql')
                        ->table('joven_proyectos')
                        ->upsert(
                            $data,
                            ['id'], // clave única
                            [
                                'joven_id','proyecto_id','desde','hasta','codigo','director','titulo','agregado','actual','updated_at'
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
