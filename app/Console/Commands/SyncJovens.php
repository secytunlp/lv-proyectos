<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncJovens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:jovens';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sincroniza solicitudjovenes desde DB origen a jovens';

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
            ->table('solicitudjovenes')
            ->leftJoin('cyt_solicitudjovenes_estado', 'solicitudjovenes.cd_solicitud', '=', 'cyt_solicitudjovenes_estado.solicitud_oid')
            ->leftJoin('estado', 'cyt_solicitudjovenes_estado.estado_oid', '=', 'estado.cd_estado')
            ->leftJoin('deddoc', 'solicitudjovenes.cd_deddoc', '=', 'deddoc.cd_deddoc')

            ->selectRaw("
                solicitudjovenes.cd_solicitud as id,solicitudjovenes.cd_docente as investigador_id,solicitudjovenes.cd_periodo as periodo_id,
                estado.ds_estado as estado,solicitudjovenes.ds_mail as email,solicitudjovenes.bl_notificacion as notificacion,solicitudjovenes.nu_telefono as telefono,
                solicitudjovenes.dt_fecha as fecha,solicitudjovenes.dt_nacimiento as nacimiento,solicitudjovenes.ds_calle as calle,
                solicitudjovenes.nu_nro as nro,solicitudjovenes.nu_piso as piso,solicitudjovenes.ds_depto as depto,solicitudjovenes.nu_cp as cp,
                solicitudjovenes.cd_titulogrado as titulo_id,
       CASE solicitudjovenes.dt_egresogrado WHEN '0000-00-00' THEN null ELSE solicitudjovenes.dt_egresogrado END as egresogrado,
       solicitudjovenes.cd_tituloposgrado as titulopost_id, CASE solicitudjovenes.dt_egresoposgrado WHEN '0000-00-00' THEN null ELSE solicitudjovenes.dt_egresoposgrado END as egresoposgrado,
       solicitudjovenes.bl_doctorado as doctorado, solicitudjovenes.cd_unidad as unidad_id,
       CASE solicitudjovenes.`cd_cargo`
           WHEN '6' THEN null
           ELSE solicitudjovenes.cd_cargo END as cargo_id,
       CASE deddoc.`ds_deddoc`
           WHEN 's/d' THEN null
           WHEN 'SI-1' THEN 'Simple'
           WHEN 'SE-1' THEN 'Semi Exclusiva'
           ELSE deddoc.ds_deddoc END as deddoc, CASE solicitudjovenes.`cd_facultad`
                                                    WHEN '574' THEN null
                                                    ELSE cd_facultad END as facultad_id, solicitudjovenes.cd_facultadplanilla as facultadplanilla_id, solicitudjovenes.bl_director as director,
       CASE solicitudjovenes.`cd_carrerainv`
           WHEN '11' THEN null
           WHEN '10' THEN null
           ELSE solicitudjovenes.cd_carrerainv END as carrerainv_id,
       CASE solicitudjovenes.`cd_organismo`
           WHEN '7' THEN null
           else solicitudjovenes.cd_organismo END as organismo_id, CASE solicitudjovenes.dt_ingreso WHEN '0000-00-00' THEN null ELSE solicitudjovenes.dt_ingreso END as ingreso_carrerainv,
       solicitudjovenes.cd_unidadcarrera as unidadcarrera_id, solicitudjovenes.cd_unidadbeca as unidadbeca_id, solicitudjovenes.nu_puntaje as puntaje, solicitudjovenes.nu_diferencia as diferencia,
       solicitudjovenes.ds_curriculum as curriculum,
       solicitudjovenes.ds_disciplina as disciplina, solicitudjovenes.ds_observaciones as observaciones, solicitudjovenes.ds_justificacion as justificacion, solicitudjovenes.ds_objetivo as objetivo")
            ->whereNull('cyt_solicitudjovenes_estado.fechaHasta')
            ->orderBy('solicitudjovenes.cd_solicitud')
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

                    $email = filter_var($row->email, FILTER_VALIDATE_EMAIL) ? $row->email : null;

                    // 🧹 LIMPIEZA DE FECHA
                    $fecha = $row->fecha;

                    if (
                        empty($fecha) ||
                        $fecha === '0000-00-00' ||
                        $fecha === '0000-00-00 00:00:00'
                    ) {
                        $fecha = null;
                    }

                    $nacimiento = $row->nacimiento;

                    if (
                        empty($nacimiento) ||
                        $nacimiento === '0000-00-00' ||
                        $nacimiento === '0000-00-00 00:00:00'
                    ) {
                        $nacimiento = null;
                    }

                    $egresogrado = $row->egresogrado;

                    if (
                        empty($egresogrado) ||
                        $egresogrado === '0000-00-00' ||
                        $egresogrado === '0000-00-00 00:00:00'
                    ) {
                        $egresogrado = null;
                    }



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
                            'institucion' => null,
                            'beca' => null,
                        ];
                        $totalOmitidas++;
                        return null; // omite la fila
                    }

                    $deddocFinal = empty($deddocRow) ? null : $deddocRow;

                    $egresoposgrado = $row->egresoposgrado;

                    if (
                        empty($egresoposgrado) ||
                        $egresoposgrado === '0000-00-00' ||
                        $egresoposgrado === '0000-00-00 00:00:00'
                    ) {
                        $egresoposgrado = null;
                    }

                    $ingreso_carrerainv = $row->ingreso_carrerainv;

                    if (
                        empty($ingreso_carrerainv) ||
                        $ingreso_carrerainv === '0000-00-00' ||
                        $ingreso_carrerainv === '0000-00-00 00:00:00'
                    ) {
                        $ingreso_carrerainv = null;
                    }





                    return [
                        'id' => $row->id,
                        'investigador_id' => $row->investigador_id ?: null,
                        'periodo_id' => $row->periodo_id ?: null,
                        'estado' => $estadoFinal,
                        'email' => $email,
                        'fecha' => $fecha,
                        'nacimiento' => $nacimiento,
                        'notificacion' => $row->notificacion ?: 0,
                        'telefono' => trim($row->telefono),
                        'egresogrado' => $egresogrado,

                        'cp' => $row->cp ?: null,

                        'curriculum' => trim($row->curriculum),

                        'calle' => trim($row->calle),
                        'nro' => trim($row->nro),
                        'piso' => trim($row->piso),
                        'depto' => trim($row->depto),

                        'titulo_id' => $row->titulo_id ?: null,
                        'titulopost_id' => $row->titulopost_id ?: null,


                        'doctorado' => $row->doctorado ?: 0,
                        'facultadplanilla_id' => $row->facultadplanilla_id ?: null,
                        'deddoc' => $deddocFinal,
                        'cargo_id' => $row->cargo_id ?: null,
                        'egresoposgrado' => $egresoposgrado,
                        'facultad_id' => $row->facultad_id ?: null,
                        'unidad_id' => $row->unidad_id ?: null,
                        'carrerainv_id' => $row->carrerainv_id ?: null,
                        'organismo_id' => $row->organismo_id ?: null,
                        'ingreso_carrerainv' => $ingreso_carrerainv,
                        'director' => $row->director ?: 0,
                        'unidadcarrera_id' => $row->unidadcarrera_id ?: null,
                        'unidadbeca_id' => $row->unidadbeca_id ?: null,
                        'puntaje' => is_numeric($row->puntaje) ? (int)$row->puntaje : null,
                        'diferencia' => is_numeric($row->diferencia) ? (int)$row->diferencia : null,
                        'observaciones' => trim($row->observaciones),
                        'justificacion' => trim($row->justificacion),
                        'objetivo' => trim($row->objetivo),

                        'disciplina' => trim($row->disciplina),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                })->filter()->toArray();
                try {
                    if (!empty($data)) {
                        DB::connection('mysql')->statement('SET FOREIGN_KEY_CHECKS=0');

                    DB::connection('mysql')
                        ->table('jovens')
                        ->upsert(
                            $data,
                            ['id'],
                            [
                                'investigador_id','periodo_id','estado','email','fecha','nacimiento','egresogrado','notificacion','cp',
                                'curriculum','telefono','calle','nro','piso','depto',
                                'doctorado','facultadplanilla_id','deddoc','cargo_id','egresoposgrado','facultad_id','unidad_id','carrerainv_id','organismo_id',
                                'ingreso_carrerainv','director','unidadcarrera_id','unidadbeca_id','observaciones','titulo_id','titulopost_id',
                                'puntaje','diferencia','justificacion','objetivo','disciplina'
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
