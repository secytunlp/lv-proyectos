<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SyncUnidadInvestigacions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:unidadinvestigacions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sincroniza cyt_unidad desde DB origen a unidadinvestigacions';

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
            ->table('cyt_unidad')
            ->leftJoin('cyt_tipo_unidad', 'cyt_unidad.tipoUnidad_oid', '=', 'cyt_tipo_unidad.oid')

            ->selectRaw("
                cyt_unidad.oid as id, cyt_tipo_unidad.nombre as tipo, cyt_unidad.denominacion, cyt_unidad.sigla, cyt_unidad.especialidad, cyt_unidad.objetivos, cyt_unidad.lineas, cyt_unidad.justificacion, cyt_unidad.funciones, cyt_unidad.produccion, cyt_unidad.proyectos, cyt_unidad.rrhh, cyt_unidad.reglamento, cyt_unidad.infraestructura, cyt_unidad.equipamiento, cyt_unidad.observaciones, cyt_unidad.dt_disposicion as fecha_disposicion, cyt_unidad.nu_disposicion as disposicion")
            ->orderBy('cyt_unidad.oid')
            ->chunk(1000, function ($rows) use (&$totalFilas, &$totalInsertadas, &$totalOmitidas, &$skippedRows){
                $totalFilas += count($rows);
                $data = collect($rows)->map(function ($row) use (&$skippedRows, &$totalOmitidas) {

                try {


                    // 🧹 LIMPIEZA DE FECHA
                    $fecha_disposicion = $row->fecha_disposicion;

                    if (
                        empty($fecha_disposicion) ||
                        $fecha_disposicion === '0000-00-00' ||
                        $fecha_disposicion === '0000-00-00 00:00:00'
                    ) {
                        $fecha_disposicion = null;
                    }



                    $tiposValidos = [
                        'Centro','Laboratorio','Instituto','Unidad promocional de I/D', 'Laboratorio'
                    ];

                    $tipoRow = trim((string)$row->tipo);

                    if (!empty($tipoRow) && !in_array($tipoRow, $tiposValidos)) {
                        // Solo omitimos si tiene valor y no está en la lista
                        $skippedRows[] = [
                            'id' => $row->id,
                            'motivo' => 'Tipo inválida',
                            'estado' => $row->estado,
                            'tipo' => $row->tipo,
                            'investigacion' => $row->investigacion
                        ];
                        $totalOmitidas++;
                        return null; // omite la fila
                    }

                    $tipoFinal = empty($tipoRow) ? null : $tipoRow;



                    return [
                        'id' => $row->id,
                        'tipo' => $tipoFinal,
                        'estado' => 'Creada',
                        'denominacion' => trim($row->denominacion),
                        'sigla' => trim($row->sigla),
                        'especialidad' => trim($row->especialidad),
                        'objetivos' => trim($row->objetivos),
                        'lineas' => trim($row->lineas),
                        'justificacion' => trim($row->justificacion),
                        'funciones' => trim($row->funciones),
                        'produccion' => trim($row->produccion),
                        'proyectos' => trim($row->proyectos),
                        'rrhh' => trim($row->rrhh),
                        'reglamento' => trim($row->reglamento),
                        'infraestructura' => trim($row->infraestructura),
                        'equipamiento' => trim($row->equipamiento),
                        'observaciones' => trim($row->observaciones),
                        'fecha_disposicion' => $fecha_disposicion,
                        'disposicion' => trim($row->disposicion),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                    } catch (\Throwable $e) {

                    $this->error($e->getMessage());

                    return null;
                }
                })->filter()->toArray();

                if (!empty($data)) {
                    try{
                        DB::connection('mysql')->statement('SET FOREIGN_KEY_CHECKS=0');

                        DB::connection('mysql')
                            ->table('unidad_investigacions')
                            ->upsert(
                                $data,
                                ['id'],
                                [
                                    'tipo','estado','sigla','denominacion','fecha_disposicion','lineas','especialidad',
                                    'objetivos','justificacion','funciones','produccion','proyectos','rrhh',
                                    'reglamento','infraestructura','equipamiento','observaciones','disposicion','updated_at'
                                ]
                            );
                        DB::connection('mysql')->statement('SET FOREIGN_KEY_CHECKS=1');
                        $totalInsertadas += count($data);
                    } catch (\Throwable $e) {

                        $this->error($e->getMessage());

                    }

                }

            });

        $this->info('Sincronización finalizada ✔');

        $this->info("Total filas leídas: $totalFilas");
        $this->info("Filas insertadas/actualizadas: $totalInsertadas");
        $this->info("Filas omitidas (proyectos): $totalOmitidas");

        if (!empty($skippedRows)) {
            $this->info("Detalle de unidades omitidos:");

            foreach ($skippedRows as $skip) {
                $this->line(
                    "ID {$skip['id']} - Motivo: {$skip['motivo']}  - Tipo: {$skip['tipo']}"
                );
            }
        }
    }
}
