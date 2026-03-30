<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncMiembroEstados extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:miembroestados';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sincroniza cyt_unidad_integrante desde DB origen a miembroestadoss';

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
        $miembrosMap = [];
        $miembroSeq = (DB::connection('mysql')
                ->table('miembro_estados')
                ->max('miembro_id') ?? 0) + 1;
        DB::connection('mysql_origen')
            ->table('cyt_unidad_integrante')
            ->leftJoin('cyt_unidad', 'cyt_unidad_integrante.unidad_oid', '=', 'cyt_unidad.oid')

            ->leftJoin('deddoc', 'cyt_unidad_integrante.dedDoc_oid', '=', 'deddoc.cd_deddoc')
            ->leftJoin('cyt_tipo_integrante', 'cyt_unidad_integrante.tipoIntegrante_oid', '=', 'cyt_tipo_integrante.oid')
            ->leftJoin('cdt_user', 'cyt_unidad_integrante.user_oid', '=', 'cdt_user.cd_user')
            ->selectRaw("
                cyt_unidad_integrante.oid as id,cyt_unidad_integrante.unidad_oid as unidad_id,cyt_tipo_integrante.nombre as tipo,

        cyt_unidad_integrante.nombre, cyt_unidad_integrante.apellido,cyt_unidad_integrante.cuil,
    cyt_unidad_integrante.categoria_oid as categoria_id, cyt_unidad_integrante.categoriasicadi_oid as sicadi_id,
    CASE `ds_deddoc`
           WHEN 's/d' THEN null
           WHEN 'SI-1' THEN 'Simple'
           WHEN 'SE-1' THEN 'Semi Exclusiva'
           ELSE ds_deddoc END as deddoc,
    CASE cyt_unidad_integrante.cargo_oid
           WHEN '6' THEN null
           ELSE cyt_unidad_integrante.cargo_oid END as cargo_id,

    CASE cyt_unidad_integrante.facultad_oid
           WHEN '574' THEN null
           ELSE cyt_unidad_integrante.facultad_oid END as facultad_id,
    CASE cyt_unidad_integrante.carrerainv_oid
           WHEN '11' THEN null
           WHEN '10' THEN null
           ELSE cyt_unidad_integrante.carrerainv_oid END as carrerainv_id,
    CASE cyt_unidad_integrante.organismo_oid
           WHEN '7' THEN null
           else cyt_unidad_integrante.organismo_oid END as organismo_id,

     cyt_unidad_integrante.beca, cyt_unidad_integrante.horas, cyt_unidad_integrante.observaciones as comentarios, cyt_unidad_integrante.activo , cyt_unidad_integrante.estudiante,
       CASE cyt_unidad_integrante.user_oid
           WHEN 1 THEN '2'
           ELSE NULL END as cd_user, cdt_user.ds_name as user_name, cyt_unidad_integrante.fechaDesde as desde, cyt_unidad_integrante.fechahasta as hasta


       ")

            ->orderBy('cyt_unidad_integrante.unidad_oid')
            ->orderBy('cyt_unidad_integrante.cuil')
            ->orderBy('cyt_unidad_integrante.fechaDesde')
            ->chunk(1000, function ($rows) use (&$totalFilas, &$totalInsertadas, &$totalOmitidas, &$skippedRows,&$miembrosMap,
    &$miembroSeq){
                $totalFilas += count($rows);
                $data = collect($rows)->map(function ($row) use (&$skippedRows, &$totalOmitidas,
                    &$miembrosMap,
                    &$miembroSeq) {

                    // Validación de proyecto
                    if (empty($row->unidad_id)) {
                        $skippedRows[] = [
                            'id' => $row->id,
                            'motivo' => 'Sin unidad_id',
                            'estado' => null,
                            'tipo' => null,
                            'deddoc' => null,

                        ];
                        $totalOmitidas++;
                        return null; // omite la fila
                    }






                    $tiposValidos = [
                        'Director','Sub-Director','Consejo directivo','Consejo asesor','Responsable','Miembro'
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

                    $cuil = preg_replace('/\D/', '', (string)$row->cuil);

                    if (empty($cuil)) {
                        $cuil = 'ID' . $row->id; // fallback único
                    }

                    $key = $row->unidad_id . '|' . $cuil;

                    if (!isset($miembrosMap[$key])) {
                        $this->line("Nuevo miembro: {$key} -> {$miembroSeq}");
                        $miembrosMap[$key] = $miembroSeq++;
                    }

                    $miembroId = $miembrosMap[$key];

                    return [


                        'miembro_id' => $miembroId,
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
                        'comentarios' => trim($row->comentarios),
                        'user_id' => $row->cd_user ?: 0,
                        'user_name' => trim($row->user_name),


                        'desde' => $desde,
                        'hasta' => $hasta,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                })->filter()->toArray();
                try {
                    if (!empty($data)) {
                        DB::connection('mysql')->statement('SET FOREIGN_KEY_CHECKS=0');

                    DB::connection('mysql')
                        ->table('miembro_estados')
                        ->insert(
                            $data

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
