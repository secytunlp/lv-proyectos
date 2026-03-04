<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncJovenEvaluacionPuntajeProduccions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:jovenevaluacionpuntajeproduccions';

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
            ->table('puntajeantproduccion')



            ->selectRaw("`cd_puntajeantproduccion` as id,`cd_evaluacion` as joven_evaluacion_id,`cd_modeloplanilla` as joven_evaluacion_planilla_id,`cd_antproduccionmaximo` as joven_evaluacion_planilla_produccion_max_id,`nu_cant` as cantidad,`nu_puntaje` as puntaje
")
            ->orderBy('puntajeantproduccion.cd_puntajeantproduccion')
            ->chunk(1000, function ($rows) use (&$totalFilas, &$totalInsertadas, &$totalOmitidas, &$skippedRows){
                $totalFilas += count($rows);
                $data = collect($rows)->map(function ($row) use (&$skippedRows, &$totalOmitidas) {








                    return [
                        'id' => $row->id,
                        'joven_evaluacion_id' => $row->joven_evaluacion_id ?: null,
                        'joven_evaluacion_planilla_id' => $row->joven_evaluacion_planilla_id  ?: null,
                        'joven_evaluacion_planilla_produccion_max_id' => $row->joven_evaluacion_planilla_produccion_max_id  ?: null,

                        'puntaje' => is_numeric($row->puntaje) ? (float)$row->puntaje : null,
                        'cantidad' => is_numeric($row->cantidad) ? (int)$row->cantidad : null,


                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                })->filter()->toArray();
                try {
                    if (!empty($data)) {
                        DB::connection('mysql')->statement('SET FOREIGN_KEY_CHECKS=0');

                    DB::connection('mysql')
                        ->table('joven_evaluacion_puntaje_produccions')
                        ->upsert(
                            $data,
                            ['id'],
                            [
                                'joven_evaluacion_id','joven_evaluacion_planilla_id','joven_evaluacion_planilla_produccion_max_id','cantidad','puntaje',
                                'updated_at'
                            ]
                        );
                        DB::connection('mysql')->statement('SET FOREIGN_KEY_CHECKS=1');
                        $totalInsertadas += count($data);
                    }
                } catch (\Illuminate\Database\QueryException $e) {
                    // Revisar si es error de duplicado
                    if ($e->errorInfo[0] == '23000' && $e->errorInfo[1] == 1062) {
                        $skippedRows[] = [
                            'id' => null, // No siempre hay un id único si falla todo el batch
                            'motivo' => 'Error duplicado: ' . $e->getMessage(),
                            'estado' => null,
                            'tipo' => null,
                            'deddoc' => null,
                            'institucion' => null,
                            'beca' => null,
                        ];
                        $totalOmitidas += count($data); // Omitimos todo el batch que falló
                    } else {
                        // si es otro error, relanzarlo
                        throw $e;
                    }
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
