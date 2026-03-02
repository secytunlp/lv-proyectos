<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncJovenBecaAnteriores extends Command
{
    protected $signature = 'sync:jovenbecaanteriores';
    protected $description = 'Sincroniza becas UNLP desde DB origen a investigadors';

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
                    ->table('solicitudjovenesbeca')
                    ->leftJoin('solicitudjovenes', 'solicitudjovenesbeca.cd_solicitud', '=', 'solicitudjovenes.cd_solicitud')
                    ->selectRaw("
                solicitudjovenes.`cd_solicitud` as joven_id,CASE solicitudjovenesbeca.bl_unlp WHEN '1' THEN 'UNLP' ELSE
                                                    CASE
                                                        WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%CIN%' THEN 'CIN'
                                                    ELSE
                                                        CASE
                                                            WHEN solicitudjovenesbeca.ds_tipobeca = 'I' THEN 'CONICET'
                                                            WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%ESTÍMULO A LAS VOCACIONES CIENTÍFICAS%' THEN 'CIN'
                                                            WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%ESTIMULO A LA VOCACION CIENTIFICA%' THEN 'CIN'
                                                            WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%Estímulo a la vocación cientifica%' THEN 'CIN'
                                                            WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%estímulo a las investigaciones científicas%' THEN 'CIN'
                                                            WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%Estímulo a la Voacación Científica%' THEN 'CIN'
                                                            WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%EVC%' THEN 'CIN'
                                                           WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%ANPCyT%' THEN 'ANPCyT'
                                                           WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%INICIAL%' THEN 'ANPCyT'
                                                           WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%AGENCIA%' THEN 'ANPCyT'
                                                           WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%CIC%' THEN 'CIC'

                                                           WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%COMISIÓN DE INVESTIGACIONES CIENTÍFICAS DE LA PROVINCIA DE BUENOS AIRES%' THEN 'CIC'

                                                           WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%ENTRENAMIENTO%' THEN 'CIC'
                                                            WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%ENTREMANIENTO%' THEN 'CIC'
                                                           WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%CONICET%' THEN 'CONICET'
                                                           WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%TIPO I%' THEN 'CONICET'
                                                           WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%TIPO  I%' THEN 'CONICET'
                                                            WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%PGTI%' THEN 'CONICET'
                                                           WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%TIPO 1%' THEN 'CONICET'
                                                           WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%TIPO 2%' THEN 'CONICET'
                                                           WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%UNLP%' THEN 'UNLP'
                                                           WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%CIN%' THEN 'CIN'
                                                           ELSE
                                                               CASE
                                                                   WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%ESTUDIO%' THEN 'CIC'
                                                               ELSE
                                                                   'OTRA'
                                                            END
                                                        END
    END END AS institucion,
       CASE
           WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%TIPO II%' THEN 'TIPO II'
           WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%TIPOII%' THEN 'TIPO II'
           WHEN solicitudjovenesbeca.ds_tipobeca = 'I' THEN 'TIPO I'
           WHEN solicitudjovenesbeca.ds_tipobeca = 'a' THEN 'TIPO A'
           WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%ESTÍMULO A LAS VOCACIONES CIENTÍFICAS%' THEN 'EVC'
           WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%ESTIMULO A LA VOCACION CIENTIFICA%' THEN 'EVC'
           WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%Estímulo a la vocación cientifica%' THEN 'EVC'
           WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%estímulo a las investigaciones científicas%' THEN 'EVC'
           WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%Estímulos a las Vocaciones Científicas%' THEN 'EVC'
           WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%Estímulo a Vocaciones Científicas%' THEN 'EVC'
           WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%Vocaciones Científicas%' THEN 'EVC'
           WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%Estímulo a la Voacación Científica%' THEN 'EVC'
           WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%EVC%' THEN 'EVC'
           WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%CIN%' THEN 'EVC'
           WHEN solicitudjovenesbeca.ds_tipobeca = 'TIPO A' THEN 'TIPO A'
           WHEN solicitudjovenesbeca.ds_tipobeca = 'TIPO A - Maestría' THEN 'Tipo A - Maestría'
           WHEN solicitudjovenesbeca.ds_tipobeca = 'Beca tipo A' THEN 'TIPO A'
           WHEN solicitudjovenesbeca.ds_tipobeca = 'TIPO B' THEN 'TIPO B'
           WHEN solicitudjovenesbeca.ds_tipobeca = 'Tipo B' THEN 'TIPO B'
           WHEN solicitudjovenesbeca.ds_tipobeca = 'TIPO B (MAESTRíA)' THEN 'TIPO B (MAESTRÍA)'
           WHEN solicitudjovenesbeca.ds_tipobeca = 'RETENCIÓN BECARIOS' THEN 'RETENCION DE POSTGRADUADO'
           WHEN solicitudjovenesbeca.ds_tipobeca = 'RETENCION DE POSTGRADUADO' THEN 'RETENCION DE POSTGRADUADO'
           WHEN solicitudjovenesbeca.ds_tipobeca = 'Beca Cofinanciada (UNLP-CIC)' THEN 'Beca Cofinanciada (UNLP-CIC)'
           ELSE
           CASE
               WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%TIPO I%' THEN 'TIPO I'
               WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%TIPO  I%' THEN 'TIPO I'
               WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%Tpo 1%' THEN 'TIPO I'
               WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%TIPO 1%' THEN 'TIPO I'
               WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%PGT1%' THEN 'TIPO I'
               WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%PG TI%' THEN 'TIPO I'
               WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%PGTI%' THEN 'TIPO I'
               WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%I GRADO%' THEN 'TIPO I'
               WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%I (CONICET)%' THEN 'TIPO I'
               WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%I - CONICET%' THEN 'TIPO I'
               WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%TIPO 2%' THEN 'TIPO II'
               WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%TIPO2%' THEN 'TIPO II'
               WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%FORMACIÓN SUPERIOR%' THEN 'Formación Superior'
               WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%ENTRENAMIENTO%' THEN 'Beca de entrenamiento'
               WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%Entreamiento%' THEN 'Beca de entrenamiento'
               WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%ENTREMANIENTO%' THEN 'Beca de entrenamiento'
               WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%PERFECCIONAMIENTO%' THEN 'BECA DE PERFECCIONAMIENTO'
               WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%PERFECCIONAMINETO%' THEN 'BECA DE PERFECCIONAMIENTO'
               WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%INICIACIóN%' THEN 'Iniciación'
               WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%INICIAL%' THEN 'Beca inicial'
               WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%INCIAL%' THEN 'Beca inicial'
               WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%Inical%' THEN 'Beca inicial'
               WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%ESTUDIO%' THEN 'Beca inicial'
               ELSE
                    CASE
                        WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%POSDOCTORAL%' THEN 'Beca posdoctoral'
                        WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%POSDOCTORADO%' THEN 'Beca posdoctoral'
                        WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%POSTDOCTORAL%' THEN 'Beca posdoctoral'
                        WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%POSTDOCTORAL%' THEN 'Beca posdoctoral'
                        WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%POSDOCT.%' THEN 'Beca posdoctoral'
                        WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%Postdoc%' THEN 'Beca posdoctoral'
                        WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%POSDTOCTORAL%' THEN 'Beca posdoctoral'
                        WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%POSTOCTORAL%' THEN 'Beca posdoctoral'
                        WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%POSDOC%' THEN 'Beca posdoctoral'
                    ELSE
                        CASE
                            WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%DOCTORAL%' THEN 'Beca doctoral'
                            WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%Doctotral%' THEN 'Beca doctoral'
                            WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%Doctoado%' THEN 'Beca doctoral'
                            WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%POSGRADO%' THEN 'Beca doctoral'
                            WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%POST-GRADO%' THEN 'Beca doctoral'
                            WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%POSTGRADO%' THEN 'Beca doctoral'
                            WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%DOCTORADO%' THEN 'Beca doctoral'
                            WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%Doctotal%' THEN 'Beca doctoral'
                            WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%SUPERIOR%' THEN 'Beca superior'
                            WHEN solicitudjovenesbeca.ds_tipobeca LIKE '%INICIO%' THEN 'Beca inicial'
                        END
                    END
            END
           END AS beca, dt_desde as desde, dt_hasta as hasta, solicitudjovenesbeca.bl_unlp as unlp, bl_agregado as agregada, solicitudjovenesbeca.ds_tipobeca as original
            ")
            ->orderBy('solicitudjovenesbeca.cd_solicitud')
            ->chunk(1000, function ($rows) use (&$totalFilas, &$totalInsertadas, &$totalOmitidas, &$skippedRows) {

                $totalFilas += count($rows);

                $data = collect($rows)->map(function ($row) use (&$skippedRows, &$totalOmitidas) {

                    // Validaciones básicas: persona_id o ident vacíos


                    // Validación de beca
                    $becaValidas = [
                        'Beca inicial','Beca superior','Beca de entrenamiento','Beca doctoral','Beca posdoctoral','Beca finalización del doctorado','Beca maestría','Formación Superior','Iniciación','TIPO I','TIPO II','TIPO A','Tipo A - Maestría','Tipo A - Doctorado','Beca Cofinanciada (UNLP-CIC)','Especial de Maestría','TIPO B','TIPO B (DOCTORADO)','TIPO B (MAESTRÍA)','BECA DE PERFECCIONAMIENTO','CONICET 2','RETENCION DE POSTGRADUADO','EVC'
                    ];
                    // normalizar array válido (una sola vez idealmente, fuera del map)
                    $becaValidasNorm = array_map(function($b) {
                        return mb_strtoupper(trim($b));
                    }, $becaValidas);

// normalizar valor entrante
                    $becaRow = mb_strtoupper(trim((string)$row->beca));

// comparar
                    $becaFinal = in_array($becaRow, $becaValidasNorm) ? trim($row->beca) : null;

                    // Validación de institución
                    $institucionValidas = ['ANPCyT','CIC','CONICET','UNLP','OTRA','CIN'];

// normalizar una sola vez
                    $institucionValidasNorm = array_flip(array_map(function($i) {
                        return mb_strtoupper(trim($i));
                    }, $institucionValidas));

// normalizar valor entrante
                    $institucionRow = mb_strtoupper(trim((string)$row->institucion));

// comparar
                    $institucionFinal = isset($institucionValidasNorm[$institucionRow])
                        ? trim($row->institucion)
                        : null;

                    // Omitir si beca o institucion inválida
                    if (is_null($becaFinal)) {
                        $skippedRows[] = [

                            'joven_id' => $row->joven_id,

                            'motivo' => 'Beca inválida',
                            'institucion' => $row->beca,
                            'beca' => $row->beca,
                        ];
                        $totalOmitidas++;
                        return null;
                    }

                    if (is_null($institucionFinal)) {
                        $skippedRows[] = [
                            'joven_id' => $row->joven_id,

                            'motivo' => 'Institución inválida',
                            'institucion' => $row->institucion,
                            'beca' => $row->beca,
                        ];
                        $totalOmitidas++;
                        return null;
                    }

                    // 🧹 LIMPIEZA DE FECHA
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
                        'joven_id' => $row->joven_id,
                        'institucion' => $institucionFinal,
                        'beca' => $becaFinal,
                        'unlp' => $row->unlp ?: 0,
                        'desde' => $desde,
                        'hasta' => $hasta,
                        'original' => $row->original ?: null,
                        'agregada' => $row->agregada ?: 0,
                        'actual' => 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                })->filter()->toArray();

                if (!empty($data)) {
                    DB::connection('mysql')->statement('SET FOREIGN_KEY_CHECKS=0');
                    DB::connection('mysql')
                        ->table('joven_becas')
                        ->upsert(
                            $data,
                            ['joven_id','institucion','beca','desde','hasta'], // clave única
                            [
                                'unlp','original','agregada','actual','updated_at'
                            ]
                        );
                    DB::connection('mysql')->statement('SET FOREIGN_KEY_CHECKS=1');
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
                $this->line(
                    "joven: {$skip['joven_id']} - Motivo: {$skip['motivo']} - Beca: {$skip['beca']}"
                );
            }
        }
    }
}
