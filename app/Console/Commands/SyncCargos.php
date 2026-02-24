<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncCargos extends Command
{
    protected $signature = 'sync:cargos';
    protected $description = 'Sincroniza cargos desde DB origen';

    public function handle()
    {
        $this->info('Iniciando sincronización de cargos...');

        $totalFilas = 0;
        $totalInsertadas = 0;

        DB::connection('mysql_origen')
            ->table('cargos_alfabetico')
            ->join('docente', 'cargos_alfabetico.dni', '=', 'docente.nu_documento')
            ->leftJoin('deddoc', 'cargos_alfabetico.cd_deddoc', '=', 'deddoc.cd_deddoc')
            ->select([
                'docente.cd_docente as investigador_id',
                'cargos_alfabetico.cd_cargo as cargo_id',
                'deddoc.ds_deddoc as deddoc',
                'cargos_alfabetico.dt_fecha as ingreso',
                'cargos_alfabetico.cd_facultad as facultad_id',
            ])
            ->where('cargos_alfabetico.escalafon', 'Docente')
            ->whereNotIn('cargos_alfabetico.situacion', [
                'Licencia sin goce de sueldos',
                'Renuncia',
                'Jubilación'
            ])
            ->whereIn('cargos_alfabetico.cd_facultad', [
                177,179,174,180,169,187,175,167,181,177,173,170,172,171,165,176,168,1220
            ])
            ->whereIn('cargos_alfabetico.cd_cargo', [1,2,3,4,5,14])
            ->orderBy('docente.cd_docente')
            ->chunk(1000, function ($rows) use (&$totalFilas, &$totalInsertadas) {

                $totalFilas += count($rows);

                $data = collect($rows)->map(function ($row) {

                    // Normalizar deddoc al enum válido
                    $deddocMap = [
                        'Exclusiva' => 'Exclusiva',
                        'Semi Exclusiva' => 'Semi Exclusiva',
                        'Simple' => 'Simple',
                    ];

                    $deddoc = $deddocMap[$row->deddoc] ?? null;

                    if (!$row->investigador_id || !$row->cargo_id) {
                        return null;
                    }

                    return [
                        'investigador_id' => (int) $row->investigador_id,
                        'cargo_id' => (int) $row->cargo_id,
                        'deddoc' => $deddoc,
                        'ingreso' => $row->ingreso ?: null,
                        'facultad_id' => $row->facultad_id ?: null,
                        'activo' => 1,
                        'universidad_id' => 11,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                })
                    ->filter()
                    ->values()
                    ->toArray();

                DB::connection('mysql')->statement('SET FOREIGN_KEY_CHECKS=0');

                DB::connection('mysql')
                    ->table('investigador_cargos')
                    ->upsert(
                        $data,
                        ['investigador_id', 'cargo_id', 'facultad_id'],
                        ['deddoc', 'ingreso', 'updated_at']
                    );

                DB::connection('mysql')->statement('SET FOREIGN_KEY_CHECKS=1');

                $totalInsertadas += count($data);
            });

        $this->info('Sincronización finalizada ✔');
        $this->info("Total filas leídas: $totalFilas");
        $this->info("Filas insertadas/actualizadas: $totalInsertadas");
    }
}
