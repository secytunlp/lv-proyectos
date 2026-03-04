<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncViajeEvaluacionPuntajeCategorias extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:viajeevaluacionpuntajecategorias';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sincroniza integrante desde DB origen a integrantes';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Iniciando sincronización...');
        $skippedRows = [];
        $totalFilas = 0;
        $totalInsertadas = 0;
        $totalOmitidas = 0;
        DB::connection('mysql_origen')
            ->table('puntajecategoria')



            ->selectRaw("`cd_puntajecategoria` as id,`cd_evaluacion` as viaje_evaluacion_id,`cd_modeloplanilla` as viaje_evaluacion_planilla_id,`cd_categoriamaximo` as viaje_evaluacion_planilla_categoria_max_id
")
            ->orderBy('puntajecategoria.cd_puntajecategoria')
            ->chunk(1000, function ($rows) use (&$totalFilas, &$totalInsertadas, &$totalOmitidas, &$skippedRows){
                $totalFilas += count($rows);
                $data = collect($rows)->map(function ($row) use (&$skippedRows, &$totalOmitidas) {








                    return [
                        'id' => $row->id,
                        'viaje_evaluacion_id' => $row->viaje_evaluacion_id ?: null,
                        'viaje_evaluacion_planilla_id' => $row->viaje_evaluacion_planilla_id  ?: null,
                        'viaje_evaluacion_planilla_categoria_max_id' => $row->viaje_evaluacion_planilla_categoria_max_id  ?: null,





                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                })->filter()->toArray();
                try {
                    if (!empty($data)) {
                        DB::connection('mysql')->statement('SET FOREIGN_KEY_CHECKS=0');

                    DB::connection('mysql')
                        ->table('viaje_evaluacion_puntaje_categorias')
                        ->upsert(
                            $data,
                            ['id'],
                            [
                                'viaje_evaluacion_id','viaje_evaluacion_planilla_id','viaje_evaluacion_planilla_categoria_max_id',
                                'updated_at'
                            ]
                        );
                        DB::connection('mysql')->statement('SET FOREIGN_KEY_CHECKS=1');
                        $totalInsertadas += count($data);
                    }
                } catch (\Illuminate\Database\QueryException $e) {

                    $this->error('ERROR SQL DETECTADO:');
                    $this->error('Mensaje: ' . $e->getMessage());
                    $this->error('SQLSTATE: ' . $e->errorInfo[0] ?? 'N/A');
                    $this->error('Código MySQL: ' . $e->errorInfo[1] ?? 'N/A');

                    // Opcional: ver los bindings
                    $this->error('Query: ' . $e->getSql());

                    // NO relances para debug
                    return;
                }

            });

        $this->info('Sincronización finalizada ✔');

        $this->info("Total filas leídas: $totalFilas");
        $this->info("Filas insertadas/actualizadas: $totalInsertadas");
        $this->info("Filas omitidas (proyectos): $totalOmitidas");

        if (!empty($skippedRows)) {
            $this->info("Detalle de proyectos omitidos:");

            foreach ($skippedRows as $skip) {
                $this->line(
                    "ID {$skip['id']} - Motivo: {$skip['motivo']} - Estado: {$skip['estado']} - Tipo: {$skip['tipo']} - Deddoc: {$skip['deddoc']} - Beca: {$skip['beca']} - Institucion: {$skip['institucion']}"
                );
            }
        }
    }
}
