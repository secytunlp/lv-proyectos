<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncJovenEstados extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:jovenestados';

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
            ->table('cyt_solicitudjovenes_estado')
            ->leftJoin('cyt_user', 'cyt_solicitudjovenes_estado.user_oid', '=', 'cyt_user.oid')


            ->selectRaw("
                cyt_solicitudjovenes_estado.oid as id,cyt_solicitudjovenes_estado.solicitud_oid as joven_id,
       CASE cyt_solicitudjovenes_estado.user_oid
           WHEN 1 THEN '2'
           ELSE NULL END as user_id, cyt_user.ds_name as user_name,

       CASE cyt_solicitudjovenes_estado.estado_oid
           WHEN 1 THEN 'Creada'
           WHEN 2 THEN 'Recibida'
           WHEN 3 THEN 'Admitida'
           WHEN 4 THEN 'No Admitida'
           WHEN 5 THEN 'Otorgada-No-Rendida'
           WHEN 6 THEN 'En evaluación'
           WHEN 7 THEN 'No otorgada'
           WHEN 8 THEN 'Evaluada'
           WHEN 9 THEN 'Otorgada-Rendida'
           WHEN 10 THEN 'Otorgada-Renunciada'
           WHEN 11 THEN 'Retirada'
           WHEN 12 THEN 'Otorgada-Devuelta'

           END as estado,

       cyt_solicitudjovenes_estado.fechaDesde as desde, cyt_solicitudjovenes_estado.fechaHasta as hasta, cyt_solicitudjovenes_estado.motivo as comentarios")
            ->orderBy('cyt_solicitudjovenes_estado.oid')
            ->chunk(1000, function ($rows) use (&$totalFilas, &$totalInsertadas, &$totalOmitidas, &$skippedRows){
                $totalFilas += count($rows);
                $data = collect($rows)->map(function ($row) use (&$skippedRows, &$totalOmitidas) {




                    $estadosValidos = [
                        'Creada','Recibida','Admitida','No Admitida','Otorgada-No-Rendida','En evaluación','No otorgada','Evaluada','Otorgada-Rendida','Otorgada-Renunciada','Retirada','Otorgada-Devuelta'
                    ];

                    $estadoRow = trim((string)$row->estado);

                    if (!empty($estadoRow) && !in_array($estadoRow, $estadosValidos)) {
                        // Solo omitimos si tiene valor y no está en la lista
                        $skippedRows[] = [
                            'id' => $row->id,
                            'motivo' => 'Estado inválida',
                            'estado' => $row->estado,
                            'tipo' => null,
                            'deddoc' => null,
                            'institucion' => null,
                            'beca' => null,
                        ];
                        $totalOmitidas++;
                        return null; // omite la fila
                    }

                    $estadoFinal = empty($estadoRow) ? null : $estadoRow;



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
                        'joven_id' => $row->joven_id ?: null,
                        'user_id' => $row->user_id ?: null,
                        'user_name' => trim($row->user_name),

                        'estado' => $estadoFinal,

                        'desde' => $desde,
                        'hasta' => $hasta,
                        'comentarios' => trim($row->comentarios),

                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                })->filter()->toArray();
                try {
                    if (!empty($data)) {
                        DB::connection('mysql')->statement('SET FOREIGN_KEY_CHECKS=0');

                    DB::connection('mysql')
                        ->table('joven_estados')
                        ->upsert(
                            $data,
                            ['id'],
                            [
                                'joven_id','user_id','user_name','estado',
                                'desde','hasta','comentarios','updated_at'
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
