<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncInvestigadors extends Command
{
    protected $signature = 'sync:investigadors';
    protected $description = 'Sincroniza investigadores desde DB origen a investigadors';

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
            ->table('docente')
            ->selectRaw("
                cd_docente as id,
                nu_ident as ident,
                cd_docente as persona_id,
                cd_categoria as categoria_id,
                cd_categoriasicadi as sicadi_id,
                CASE cd_carrerainv WHEN '11' THEN null WHEN '10' THEN null ELSE cd_carrerainv END as carrerainv_id,
                CASE cd_organismo WHEN '7' THEN null ELSE cd_organismo END as organismo_id,
                CASE cd_facultad WHEN '574' THEN null ELSE cd_facultad END as facultad_id,
                CASE cd_cargo WHEN '6' THEN null ELSE cd_cargo END as cargo_id,
                CASE ds_deddoc
                    WHEN 's/d' THEN null
                    WHEN 'SI-1' THEN 'Simple'
                    WHEN 'SE-1' THEN 'Semi Exclusiva'
                    ELSE ds_deddoc
                END as deddoc,
                cd_universidad as universidad_id,
                CASE cd_titulo WHEN '9999' THEN null ELSE cd_titulo END as titulo_id,
                CASE cd_titulopost WHEN '9999' THEN null ELSE cd_titulopost END as titulopost_id,
                cd_unidad as unidad_id,
                ds_orgbeca as institucion,
                ds_tipobeca as beca,
                nu_materias as materias,
                nu_totalMat as total,
                ds_carrera as carrera
            ")
            ->orderBy('cd_docente')
            ->chunk(1000, function ($rows) use (&$totalFilas, &$totalInsertadas, &$totalOmitidas, &$skippedRows) {

                $totalFilas += count($rows);

                $data = collect($rows)->map(function ($row) use (&$skippedRows, &$totalOmitidas) {

                    // Validaciones básicas: si persona_id o ident están vacíos, lo omitimos
                    if (empty($row->persona_id) || empty($row->ident)) {
                        $skippedRows[] = [
                            'id' => $row->id,
                            'ident' => $row->ident,
                            'persona_id' => $row->persona_id,
                        ];
                        $totalOmitidas++;
                        return null;
                    }

                    return [
                        'id' => $row->id,
                        'ident' => $row->ident,
                        'persona_id' => $row->persona_id,
                        'categoria_id' => $row->categoria_id ?: null,
                        'sicadi_id' => $row->sicadi_id ?: null,
                        'carrerainv_id' => $row->carrerainv_id ?: null,
                        'organismo_id' => $row->organismo_id ?: null,
                        'facultad_id' => $row->facultad_id ?: null,
                        'cargo_id' => $row->cargo_id ?: null,
                        'deddoc' => $row->deddoc ?: null,
                        'universidad_id' => $row->universidad_id ?: null,
                        'titulo_id' => $row->titulo_id ?: null,
                        'titulopost_id' => $row->titulopost_id ?: null,
                        'unidad_id' => $row->unidad_id ?: null,
                        'institucion' => $row->institucion ?: null,
                        'beca' => $row->beca ?: null,
                        'materias' => $row->materias ?: null,
                        'total' => $row->total ?: null,
                        'carrera' => $row->carrera ?: null,
                    ];
                })->filter()->toArray();

                if (!empty($data)) {
                    DB::connection('mysql')
                        ->table('investigadors')
                        ->upsert(
                            $data,
                            ['id'], // clave única
                            [
                                'ident','persona_id','categoria_id','sicadi_id','carrerainv_id',
                                'organismo_id','facultad_id','cargo_id','deddoc','universidad_id',
                                'titulo_id','titulopost_id','unidad_id','institucion','beca',
                                'materias','total','carrera'
                            ]
                        );
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
                $this->line("ID {$skip['id']} - Persona: {$skip['persona_id']} - Ident: {$skip['ident']}");
            }
        }
    }
}
