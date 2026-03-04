<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncViajeEvaluacions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:viajeevaluacions';

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
            ->table('evaluacion')
            ->leftJoin('cyt_user', 'evaluacion.cd_usuario', '=', 'cyt_user.oid')
            ->leftJoin('cyt_evaluacion_estado', 'evaluacion.cd_evaluacion', '=', 'cyt_evaluacion_estado.evaluacion_oid')

            ->selectRaw("evaluacion.cd_evaluacion as id,evaluacion.cd_solicitud as viaje_id,
                       CASE evaluacion.cd_usuario
                           WHEN 1 THEN '2'
                           ELSE NULL END as user_id, cyt_user.ds_name as user_name, cyt_user.ds_username as user_cuil,

                       CASE cyt_evaluacion_estado.estado_oid
                           WHEN 1 THEN 'Creada'
                           WHEN 2 THEN 'Recibida'
                           WHEN 3 THEN 'Aceptada'
                           WHEN 4 THEN 'Rechazada'

                           WHEN 6 THEN 'En evaluación'

                           WHEN 8 THEN 'Evaluada'


                           END as estado,

                       evaluacion.dt_fecha as fecha, evaluacion.nu_puntaje as puntaje, CAST(evaluacion.bl_interno AS UNSIGNED) as interno, evaluacion.ds_observacion as observaciones
                ")
        ->whereNull('cyt_evaluacion_estado.fechaHasta')
            ->orderBy('evaluacion.cd_evaluacion')
            ->chunk(1000, function ($rows) use (&$totalFilas, &$totalInsertadas, &$totalOmitidas, &$skippedRows){
                $totalFilas += count($rows);
                $data = collect($rows)->map(function ($row) use (&$skippedRows, &$totalOmitidas) {




                    $estadosValidos = [
                        'Creada','Recibida','Aceptada','Rechazada','En evaluación','Evaluada','Rectificada'
                    ];

                    $estadoRow = trim((string)$row->estado);

                    if (!empty($estadoRow) && !in_array($estadoRow, $estadosValidos)) {
                        // Solo omitimos si tiene valor y no está en la lista
                        $skippedRows[] = [
                            'id' => $row->id,
                            'motivo' => 'Estado inválida',
                            'estado' => $row->estado,

                        ];
                        $totalOmitidas++;
                        return null; // omite la fila
                    }

                    $estadoFinal = empty($estadoRow) ? null : $estadoRow;



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
                        'viaje_id' => $row->viaje_id ?: null,
                        'user_id' => $row->user_id ?: null,
                        'user_name' => trim($row->user_name),
                        'user_cuil' => trim($row->user_cuil),
                        'estado' => $estadoFinal,

                        'fecha' => $fecha,
                        'interno' => $row->interno ?: 0,
                        'puntaje' => is_numeric($row->puntaje) ? (float)$row->puntaje : null,
                        'observaciones' => trim($row->observaciones),

                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                })->filter()->toArray();
                try {
                    if (!empty($data)) {
                        DB::connection('mysql')->statement('SET FOREIGN_KEY_CHECKS=0');

                    DB::connection('mysql')
                        ->table('viaje_evaluacions')
                        ->upsert(
                            $data,
                            ['id'],
                            [
                                'viaje_id','user_id','user_name','user_cuil','estado',
                                'fecha','interno','puntaje','observaciones','updated_at'
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
