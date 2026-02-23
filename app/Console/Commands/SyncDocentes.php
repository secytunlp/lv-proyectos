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
    protected $signature = 'command:name';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
            ->chunk(1000, function ($rows) {

                $data = collect($rows)->map(function ($row) {
                    return [
                        'id' => $row->id,
                        'nombre' => $row->nombre,
                        'apellido' => $row->apellido,
                        'documento' => $row->documento,
                        'cuil' => $row->nu_precuil . '-' . str_pad($row->documento, 8, '0', STR_PAD_LEFT) . '-' . $row->nu_postcuil,
                        'genero' => $row->genero,
                        'calle' => $row->calle,
                        'nro' => $row->nro,
                        'piso' => $row->piso,
                        'depto' => $row->depto,
                        'localidad' => $row->localidad,
                        'provincia_id' => $row->provincia_id,
                        'cp' => $row->cp,
                        'telefono' => $row->telefono,
                        'email' => $row->email,
                        'nacimiento' => $row->nacimiento,
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
            });

        $this->info('Sincronización finalizada ✔');
    }
}
