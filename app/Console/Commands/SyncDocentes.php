<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncDocentes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:docentes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sincroniza docentes desde DB origen a personas';

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
            ->table('docente')
            ->select([
                'cd_docente as id',
                'ds_nombre as nombre',
                'ds_apellido as apellido',
                'nu_documento as documento',
                'nu_precuil',
                'nu_postcuil',
                'ds_sexo as genero',
                'ds_calle as calle',
                'nu_nro as nro',
                'nu_piso as piso',
                'ds_depto as depto',
                'ds_localidad as localidad',
                'cd_provincia as provincia_id',
                'nu_cp as cp',
                'nu_telefono as telefono',
                'ds_mail as email',
                'dt_nacimiento as nacimiento'
            ])
            ->orderBy('cd_docente')
            ->chunk(1000, function ($rows) use (&$totalFilas, &$totalInsertadas, &$totalOmitidas, &$skippedRows){
                $totalFilas += count($rows);
                $data = collect($rows)->map(function ($row) {

                    // 🧹 LIMPIEZA DE FECHA
                    $nacimiento = $row->nacimiento;

                    if (
                        empty($nacimiento) ||
                        $nacimiento === '0000-00-00' ||
                        $nacimiento === '0000-00-00 00:00:00'
                    ) {
                        $nacimiento = null;
                    }

                    // 🧹 LIMPIEZA DE EMAIL (opcional pero recomendable)
                    $email = filter_var($row->email, FILTER_VALIDATE_EMAIL) ? $row->email : null;

                    // 🧹 LIMPIEZA DE TELÉFONO (básica)
                    $telefono = $row->telefono ? trim($row->telefono) : null;

                    $precuil = trim((string)$row->nu_precuil);
                    $postcuil = trim((string)$row->nu_postcuil);
                    $documento = trim((string)$row->documento);

                    $cuil = null;

                    if ($documento !== '') {

                        $documento = preg_replace('/\D/', '', (string)$row->documento); // solo números

                        if (strlen($documento) > 8) {
                            $documento = substr($documento, 0, 8); // deja los primeros 8
                        }
                        $doc = str_pad($documento, 8, '0', STR_PAD_LEFT);



                        // validar que sean numéricos
                        if (is_numeric($precuil) && is_numeric($postcuil)) {

                            // normalizar (ej: 0 → 00 si querés consistencia)
                            $precuil = substr(str_pad($precuil, 2, '0', STR_PAD_LEFT), 0, 2);
                            $postcuil = substr(str_pad($postcuil, 1, '0', STR_PAD_LEFT), 0, 1);

                            $cuil = $precuil . '-' . $doc . '-' . $postcuil;

                            // seguridad absoluta
                            /*if (strlen($cuil) !== 13) {
                                $cuil = null;
                            }*/
                        }
                    }

                    // Si no se pudo generar cuil, guardamos la fila en skipped
                    if ($cuil === null) {
                        $skippedRows[] = [
                            'id' => $row->id,
                            'nombre' => $row->ds_nombre,
                            'apellido' => $row->ds_apellido,
                            'documento' => $row->nu_documento,
                            'precuil' => $precuil,
                            'postcuil' => $postcuil,
                        ];
                        $totalOmitidas++;
                    }

                    return [
                        'id' => $row->id,
                        'nombre' => trim($row->nombre),
                        'apellido' => trim($row->apellido),
                        'documento' => $row->documento,

                        'cuil' => $cuil,

                        'genero' => $row->genero ?: null,
                        'calle' => $row->calle ?: null,
                        'nro' => $row->nro ?: null,
                        'piso' => $row->piso ?: null,
                        'depto' => $row->depto ?: null,
                        'localidad' => $row->localidad ?: null,
                        'provincia_id' => $row->provincia_id ?: null,
                        'cp' => $row->cp ?: null,
                        'telefono' => $telefono,
                        'email' => $email,
                        'nacimiento' => $nacimiento,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                })->toArray();

                DB::connection('mysql')
                    ->table('personas')
                    ->upsert(
                        $data,
                        ['id'],
                        [
                            'nombre','apellido','documento','cuil','genero',
                            'calle','nro','piso','depto','localidad',
                            'provincia_id','cp','telefono','email','nacimiento'
                        ]
                    );
                $totalInsertadas += count($data);
            });

        $this->info('Sincronización finalizada ✔');
        $this->info('Filas omitidas por cuil inválido: ' . count($skippedRows));

        if (!empty($skippedRows)) {
            foreach ($skippedRows as $skip) {
                $this->line("ID {$skip['id']} - {$skip['apellido']}, {$skip['nombre']} - DOC: {$skip['documento']} - precuil: {$skip['precuil']} postcuil: {$skip['postcuil']}");
            }
        }
        $this->info("Total filas leídas: $totalFilas");
        $this->info("Filas insertadas/actualizadas: $totalInsertadas");
        $this->info("Filas omitidas por cuil inválido: $totalOmitidas");
    }
}
