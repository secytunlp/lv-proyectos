<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncUnidads extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:unidads';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sincroniza unidads desde DB origen a personas';

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
            ->table('unidad')
            ->select([
                'cd_unidad as id',
                'cd_tipounidad as tipo',
                'cd_padre as padre_id',
                'bl_hijos as hijos',
                'ds_unidad as nombre',
                'ds_codigo as codigo',
                'ds_sigla as sigla',
                'ds_direccion as direccion',
                'ds_mail as email',
                'cd_facultad as facultad_id',
                'bl_activa as activa'
            ])
            ->orderBy('cd_unidad')
            ->chunk(1000, function ($rows) use (&$totalFilas, &$totalInsertadas, &$totalOmitidas, &$skippedRows){
                $totalFilas += count($rows);
                $data = collect($rows)->map(function ($row) {



                    return [
                        'id' => $row->id,
                        'tipo' => trim($row->tipo),
                        'nombre' => trim($row->nombre),
                        'padre_id' => trim($row->padre_id),
                        'hijos' => $row->hijos,
                        'codigo' => $row->codigo ?: null,
                        'sigla' => $row->sigla ?: null,
                        'direccion' => $row->direccion ?: null,
                        'email' => $row->email ?: null,
                        'facultad_id' => $row->facultad_id ?: null,
                        'activa' => is_numeric($row->activa) ? (int)$row->activa : 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                })->toArray();
                DB::connection('mysql')->statement('SET FOREIGN_KEY_CHECKS=0');
                DB::connection('mysql')
                    ->table('unidads')
                    ->upsert(
                        $data,
                        ['id'],
                        [
                            'nombre','padre_id','hijos','tipo','codigo',
                            'sigla','direccion','email','facultad_id','activa','updated_at'
                        ]
                    );
                DB::connection('mysql')->statement('SET FOREIGN_KEY_CHECKS=1');
                $totalInsertadas += count($data);
            });

        $this->info('Sincronización finalizada ✔');


        if (!empty($skippedRows)) {
            foreach ($skippedRows as $skip) {
                $this->line("ID {$skip['id']} - {$skip['padre_id']}, {$skip['nombre']} - DOC: {$skip['hijos']}");
            }
        }
        $this->info("Total filas leídas: $totalFilas");
        $this->info("Filas insertadas/actualizadas: $totalInsertadas");
        $this->info("Filas omitidas por cuil inválido: $totalOmitidas");
    }
}
