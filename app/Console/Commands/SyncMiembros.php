<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncMiembros extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:miembros';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sincroniza cyt_unidad_integrante desde DB origen a miembros';

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
        DB::connection('cyt_unidad_integrante')
            ->table('cyt_unidad_integrante')
            ->leftJoin('cyt_unidad', 'cyt_unidad_integrante.unidad_oid', '=', 'cyt_unidad.oid')

            ->leftJoin('deddoc', 'cyt_unidad_integrante.dedDoc_oid', '=', 'deddoc.cd_deddoc')
            ->leftJoin('cyt_tipo_integrante', 'cyt_unidad_integrante.tipoIntegrante_oid', '=', 'cyt_tipo_integrante.oid')

            ->selectRaw("
                cyt_unidad_integrante.oid as id,cyt_unidad_integrante.unidad_oid as unidad_id,cyt_tipo_integrante.nombre as tipo,

        cyt_unidad_integrante.nombre, cyt_unidad_integrante.apellido,cyt_unidad_integrante.cuil,
    cyt_tipo_integrante.categoria_oid as categoria_id, cyt_tipo_integrante.categoriasicadi_oid as sicadi_id,
    CASE `ds_deddoc`
           WHEN 's/d' THEN null
           WHEN 'SI-1' THEN 'Simple'
           WHEN 'SE-1' THEN 'Semi Exclusiva'
           ELSE ds_deddoc END as deddoc,
    CASE cyt_tipo_integrante.cargo_oid
           WHEN '6' THEN null
           ELSE cyt_tipo_integrante.cargo_oid END as cargo_id,

    CASE cyt_tipo_integrante.facultad_oid
           WHEN '574' THEN null
           ELSE cyt_tipo_integrante.facultad_oid END as facultad_id,
    CASE cyt_tipo_integrante.carrerainv_oid
           WHEN '11' THEN null
           WHEN '10' THEN null
           ELSE cyt_tipo_integrante.carrerainv_oid END as carrerainv_id,
    CASE cyt_tipo_integrante.organismo_oid
           WHEN '7' THEN null
           else cyt_tipo_integrante.organismo_oid END as organismo_id,

     cyt_tipo_integrante.beca, cyt_tipo_integrante.horas, cyt_tipo_integrante.observaciones, cyt_tipo_integrante.activo , cyt_tipo_integrante.estudiante,


       ")
            ->whereNull('fechaHasta')
            ->orderBy('oid')
            ->chunk(1000, function ($rows) use (&$totalFilas, &$totalInsertadas, &$totalOmitidas, &$skippedRows){
                $totalFilas += count($rows);
                $data = collect($rows)->map(function ($row) use (&$skippedRows, &$totalOmitidas) {

                    // Validación de proyecto
                    if (empty($row->proyecto_id)) {
                        $skippedRows[] = [
                            'id' => $row->id,
                            'motivo' => 'Sin proyecto_id',
                            'estado' => null,
                            'tipo' => null,
                            'deddoc' => null,

                        ];
                        $totalOmitidas++;
                        return null; // omite la fila
                    }


                    $estadosValidos = [
                        'Alta Creada','Alta Recibida','','Baja Creada','Baja Recibida','Cambio Creado','Cambio Recibido','Cambio Hs. Creado','Cambio Hs. Recibido','Cambio Tipo Creado','Cambio Tipo Recibido'
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

                        ];
                        $totalOmitidas++;
                        return null; // omite la fila
                    }

                    $estadoFinal = empty($estadoRow) ? null : $estadoRow;



                    $tiposValidos = [
                        'Director','Sub-Director','Consejo directivo','Consejo aesor','Responsable','Miembro'
                    ];


                    $tipoRow = trim((string)$row->tipo);

                    if (!empty($tipoRow) && !in_array($tipoRow, $tiposValidos)) {
                        // Solo omitimos si tiene valor y no está en la lista
                        $skippedRows[] = [
                            'id' => $row->id,
                            'motivo' => 'Tipo inválida',
                            'estado' => null,
                            'tipo' => $row->tipo,
                            'deddoc' => null,

                        ];
                        $totalOmitidas++;
                        return null; // omite la fila
                    }

                    $tipoFinal = empty($tipoRow) ? null : $tipoRow;

                    $email = filter_var($row->email, FILTER_VALIDATE_EMAIL) ? $row->email : null;

                    $valoresValidos = ['Exclusiva','Semi Exclusiva','Simple'];
                    $deddocRow = trim((string)$row->deddoc);

                    if (!empty($deddocRow) && !in_array($deddocRow, $valoresValidos)) {
                        // Solo omitimos si tiene valor y no está en la lista
                        $skippedRows[] = [
                            'id' => $row->id,
                            'motivo' => 'Deddoc inválida',
                            'estado' => null,
                            'tipo' => null,
                            'deddoc' => $row->deddoc,

                        ];
                        $totalOmitidas++;
                        return null; // omite la fila
                    }

                    $deddocFinal = empty($deddocRow) ? null : $deddocRow;



                    return [
                        'id' => $row->id,

                        'unidad_id' => $row->unidad_id ?: null,
                        'tipo' => $tipoFinal,

                        'horas' => $row->horas ?: null,

                        'estado' => ' ',
                        'nombre' => trim($row->nombre),
                        'apellido' => trim($row->apellido),
                        'cuil' => trim($row->cuil),

                        'categoria_id' => $row->categoria_id ?: null,
                        'sicadi_id' => $row->sicadi_id ?: null,
                        'deddoc' => $deddocFinal,
                        'cargo_id' => $row->cargo_id ?: null,

                        'facultad_id' => $row->facultad_id ?: null,

                        'carrerainv_id' => $row->carrerainv_id ?: null,
                        'organismo_id' => $row->organismo_id ?: null,
                        'beca' => trim($row->beca),
                        'activo' => $row->activo ?: 0,
                        'estudiante' => $row->estudiante ?: 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                })->filter()->toArray();
                try {
                    if (!empty($data)) {
                        DB::connection('mysql')->statement('SET FOREIGN_KEY_CHECKS=0');

                    DB::connection('mysql')
                        ->table('miembros')
                        ->upsert(
                            $data,
                            ['id'],
                            [
                                'unidad_id','tipo','horas','estado',
                                'nombre','apellido','cuil','motivos','cyt','reduccion','email',
                                'categoria_id','sicadi_id','deddoc','cargo_id','alta_cargo','facultad_id','carrerainv_id','organismo_id',
                                'beca','activo','estudiante','updated_at'
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
        $this->info("Filas omitidas (unidads): $totalOmitidas");

        if (!empty($skippedRows)) {
            $this->info("Detalle de unidads omitidos:");

            foreach ($skippedRows as $skip) {
                $this->line(
                    "ID {$skip['id']} - Motivo: {$skip['motivo']} - Estado: {$skip['estado']} - Tipo: {$skip['tipo']} - Deddoc: {$skip['deddoc']} "
                );
            }
        }
    }
}
