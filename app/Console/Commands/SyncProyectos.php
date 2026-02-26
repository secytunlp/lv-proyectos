<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncProyectos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:proyectos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sincroniza proyecto desde DB origen a proyectos';

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
            ->table('proyecto')
            ->leftJoin('tipoacreditacion', 'proyecto.cd_tipoacreditacion', '=', 'tipoacreditacion.cd_tipoacreditacion')
            ->leftJoin('estadoproyecto', 'proyecto.cd_estado', '=', 'estadoproyecto.cd_estado')
            ->selectRaw("
                proyecto.cd_proyecto as id, CASE tipoacreditacion.ds_tipoacreditacion
            WHEN 'Proyectos I+D' THEN 'I+D'
            ELSE tipoacreditacion.ds_tipoacreditacion END as tipo, estadoproyecto.ds_estado as estado, proyecto.ds_codigo as codigo, proyecto.ds_codigoSIGEVA as sigeva,
       proyecto.ds_titulo as titulo,
       proyecto.dt_ini as inicio, proyecto.dt_fin as fin, proyecto.cd_facultad as facultad_id, proyecto.nu_duracion as duracion,
       proyecto.cd_unidad as unidad_id, proyecto.cd_campo as campo_id, proyecto.cd_disciplina as disciplina_id, proyecto.cd_especialidad as especialidad_id,
       CASE proyecto.ds_tipo
           WHEN 'A' THEN 'Aplicada'
           WHEN 'B' THEN 'Básica'
           WHEN 'D' THEN 'Desarrollo'
           WHEN 'C' THEN 'Creación' END AS investigacion, proyecto.ds_linea as linea, proyecto.ds_abstract1 as resumen,
    proyecto.ds_clave1 as clave1, proyecto.ds_clave2 as clave2, proyecto.ds_clave3 as clave3, proyecto.ds_clave4 as clave4, proyecto.ds_clave5 as clave5,
    proyecto.ds_clave6 as clave6, proyecto.ds_claveeng1 as key1, proyecto.ds_claveeng2 as key2, proyecto.ds_claveeng3 as key3, proyecto.ds_claveeng4 as key4,
    proyecto.ds_claveeng5 as key5, proyecto.ds_claveeng6 as key6")
            ->orderBy('cd_proyecto')
            ->chunk(1000, function ($rows) use (&$totalFilas, &$totalInsertadas, &$totalOmitidas, &$skippedRows){
                $totalFilas += count($rows);
                $data = collect($rows)->map(function ($row) use (&$skippedRows, &$totalOmitidas) {

                    $estadosValidos = [
                        'Creado','Recibido','Admitido','No Admitido','Acreditado','En evaluación','No acreditado','Evaluado','Retirado'
                    ];

                    $estadoRow = trim((string)$row->estado);

                    $estadoFinal = in_array($estadoRow, $estadosValidos)
                        ? trim($row->estado)
                        : null;

                    if (is_null($estadoFinal)) {
                        $skippedRows[] = [
                            'id' => $row->id,
                            'motivo' => 'Estado inválido',
                            'estado' => $row->estado,
                            'tipo' => $row->tipo,
                            'investigacion' => $row->investigacion
                        ];
                        $totalOmitidas++;
                        return null;
                    }

                    // 🧹 LIMPIEZA DE FECHA
                    $inicio = $row->inicio;

                    if (
                        empty($inicio) ||
                        $inicio === '0000-00-00' ||
                        $inicio === '0000-00-00 00:00:00'
                    ) {
                        $inicio = null;
                    }

                    $fin = $row->fin;

                    if (
                        empty($fin) ||
                        $fin === '0000-00-00' ||
                        $fin === '0000-00-00 00:00:00'
                    ) {
                        $fin = null;
                    }

                    $tiposValidos = [
                        'I+D','PPID','PIIT-AP','PIO'
                    ];

                    $tipoRow = trim((string)$row->tipo);

                    $tipoFinal = in_array($tipoRow, $tiposValidos)
                        ? trim($row->tipo)
                        : null;

                    if (is_null($tipoFinal)) {
                        $skippedRows[] = [
                            'id' => $row->id,
                            'motivo' => 'Tipo inválido',
                            'estado' => $row->estado,
                            'tipo' => $row->tipo,
                            'investigacion' => $row->investigacion
                        ];
                        $totalOmitidas++;
                        return null;
                    }

                    $investigacionsValidos = [
                        'Creado','Recibido','Admitido','No Admitido','Acreditado','En evaluación','No acreditado','Evaluado','Retirado'
                    ];

                    $investigacionRow = trim((string)$row->investigacion);

                    $investigacionFinal = in_array($investigacionRow, $investigacionsValidos)
                        ? trim($row->investigacion)
                        : null;

                    if (is_null($investigacionFinal)) {
                        $skippedRows[] = [
                            'id' => $row->id,
                            'motivo' => 'Investigacion inválido',
                            'estado' => $row->estado,
                            'tipo' => $row->tipo,
                            'investigacion' => $row->investigacion
                        ];
                        $totalOmitidas++;
                        return null;
                    }

                    return [
                        'id' => $row->id,
                        'tipo' => $tipoFinal,
                        'estado' => $estadoFinal,
                        'codigo' => trim($row->codigo),
                        'sigeva' => trim($row->sigeva),
                        'titulo' => trim($row->titulo),
                        'inicio' => $inicio,
                        'fin' => $fin,
                        'facultad_id' => $row->facultad_id ?: null,
                        'duracion' => $row->duracion ?: null,
                        'unidad_id' => $row->unidad_id ?: null,
                        'campo_id' => $row->campo_id ?: null,
                        'disciplina_id' => $row->disciplina_id ?: null,
                        'especialidad_id' => $row->especialidad_id ?: null,
                        'investigacion' => $investigacionFinal,
                        'linea' => trim($row->linea),
                        'resumen' => trim($row->resumen),
                        'clave1' => trim($row->clave1),
                        'clave2' => trim($row->clave2),
                        'clave3' => trim($row->clave3),
                        'clave4' => trim($row->clave4),
                        'clave5' => trim($row->clave5),
                        'clave6' => trim($row->clave6),
                        'key1' => trim($row->key1),
                        'key2' => trim($row->key2),
                        'key3' => trim($row->key3),
                        'key4' => trim($row->key4),
                        'key5' => trim($row->key5),
                        'key6' => trim($row->key6),

                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                })->filter()->toArray();

                if (!empty($data)) {
                    DB::connection('mysql')->statement('SET FOREIGN_KEY_CHECKS=0');

                DB::connection('mysql')
                    ->table('proyectos')
                    ->upsert(
                        $data,
                        ['id'],
                        [
                            'tipo','estado','codigo','sigeva','titulo','inicio','fin',
                            'facultad_id','duracion','unidad_id','campo_id','disciplina_id',
                            'especialidad_id','investigacion','linea','resumen',
                            'clave1','clave2','clave3','clave4','clave5','clave6',
                            'key1','key2','key3','key4','key5','key6'
                        ]
                    );
                    DB::connection('mysql')->statement('SET FOREIGN_KEY_CHECKS=1');
                    $totalInsertadas += count($data);
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
                    "ID {$skip['id']} - Motivo: {$skip['motivo']} - Estado: {$skip['estado']} - Tipo: {$skip['tipo']} - Investigacion: {$skip['investigacion']}"
                );
            }
        }
    }
}
