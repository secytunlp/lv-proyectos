<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Investigador;
use App\Models\Integrante;

class ActualizarCargosDocentes extends Command
{
    protected $signature = 'cargos:actualizar';
    protected $description = 'Actualiza cargos docentes desde cargos_alfabetico';

    public function handle()
    {
        $this->info('Sincronizando cargos investigadores...');

        DB::beginTransaction();

        try {

            $cargos = DB::table('cargos_alfabetico')
                ->where('escalafon','Docente')
                ->whereIn('cd_deddoc', array(1,2,3))
                ->whereNotIn('situacion', array(
                    'Licencia sin goce de sueldos',
                    'Renuncia',
                    'Jubilación'
                ))
                ->whereIn('cd_facultad', array(
                    165,167,168,169,170,171,172,173,174,
                    175,176,177,179,180,181,187,1220
                ))
                ->distinct()
                ->orderBy('dni')
                ->orderBy('cd_deddoc')
                ->orderBy('cd_cargo')
                ->get()
                ->groupBy('dni');

            $ruta = storage_path('app/faltantes_investigadores.txt');

// limpiar archivo al inicio
            file_put_contents($ruta, '');
            foreach ($cargos as $dni => $lista) {

                $investigador = Investigador::whereHas('persona', function($q) use ($dni){
                    $q->where('documento',$dni);
                })->first();

                if (!$investigador) {


                    $docente = $lista->first();
                    $nombreCompleto = trim($docente->investigador);

                    $partes = explode(' ', $nombreCompleto);
                    
                    $apellido = str_replace(',', '', $partes[0] ?? '');
                    $nombre = implode(' ', array_slice($partes, 1));
                    $dni = trim($docente->dni);
                    $cargo = (int) $docente->cd_cargo;
                    $facultad = (int) $docente->cd_facultad;
                    $nacimiento = $docente->nacimiento ?? '';
                    $deddocMap = [
                        1 => 'Exclusiva',
                        2 => 'Semi Exclusiva',
                        3 => 'Simple'
                    ];

                    $deddoc = $deddocMap[(int)$docente->cd_deddoc] ?? '';
                    $url = 'https://sicadi.presi.unlp.edu.ar/investigadors/create?' . http_build_query([
                            'dni' => $dni,
                            'apellido' => $apellido,
                            'nombre' => $nombre,
                            'nacimiento' => $nacimiento,
                            // 👇 BONUS
                            'cargo' => $cargo,
                            'facultad' => $facultad,
                            'deddoc' => $deddoc,
                        ]);

                    // guardar en archivo
                    file_put_contents($ruta, $url . PHP_EOL, FILE_APPEND);
                    continue;
                }

                // 🔴 DESACTIVAR TODOS ANTES
                DB::table('investigador_cargos')
                    ->where('investigador_id', $investigador->id)
                    ->update(['activo' => 0]);
                // 🟢 INSERTAR / ACTIVAR
                foreach ($lista as $c) {
                    $ingreso = date('Y-m-d H:i:s', strtotime($c->dt_fecha));
                    $deddocMap = [
                        1 => 'Exclusiva',
                        2 => 'Semi Exclusiva',
                        3 => 'Simple'
                    ];

                    $deddocEnum = $deddocMap[$c->cd_deddoc] ?? null;
                    $data = [
                        'investigador_id' => $investigador->id,
                        'cargo_id'       => $c->cd_cargo,
                        'deddoc'         => $deddocEnum,
                        'facultad_id'    => $c->cd_facultad,
                        'ingreso'        => $ingreso
                    ];

                    /*$this->line(
                        'INTENTA → '.
                        implode(' | ', $data)
                    );*/

                    DB::table('investigador_cargos')
                        ->updateOrInsert($data, ['activo'=>1]);
                }



                // ⭐ CARGO PRINCIPAL
                $principal = DB::table('investigador_cargos')
                    ->where('investigador_id',$investigador->id)
                    ->where('activo',1)
                    ->orderBy('deddoc')
                    ->orderBy('cargo_id')
                    ->orderByDesc('ingreso')
                    ->first();

                if ($principal) {

                    DB::table('investigadors')
                        ->where('id',$investigador->id)
                        ->update(array(
                            'cargo_id'=>$principal->cargo_id,
                            'deddoc'=>$principal->deddoc,
                            'facultad_id'=>$principal->facultad_id
                        ));



                    DB::table('integrantes as i')
                        ->join('proyectos as p','p.id','=','i.proyecto_id')
                        ->where('i.investigador_id',$investigador->id)
                        ->where('i.estado',1)
                        ->where(function($q){
                            $q->whereNull('p.fin')
                                ->orWhere('p.fin','>=', now());
                        })
                        ->update(array(
                            'i.cargo_id'    => $principal->cargo_id,
                            'i.deddoc'      => $principal->deddoc,
                            'i.facultad_id' => $principal->facultad_id
                        ));

                    DB::table('integrante_estados as ie')
                        ->join('integrantes as i','i.id','=','ie.integrante_id')
                        ->join('proyectos as p','p.id','=','i.proyecto_id')
                        ->where('i.investigador_id',$investigador->id)
                        ->where('i.estado',1)
                        ->whereNull('ie.hasta')
                        ->where(function($q){
                            $q->whereNull('p.fin')
                                ->orWhere('p.fin','>=', now());
                        })
                        ->update(array(
                            'ie.cargo_id'    => $principal->cargo_id,
                            'ie.deddoc'      => $principal->deddoc,
                            'ie.facultad_id' => $principal->facultad_id
                        ));
                }
                else {
                    // Obtener el DNI y nombre (o apellido y nombre) de la persona relacionada
                    $persona = $investigador->persona()->first();
                    $dni = $persona->documento ?? 'N/A';
                    $nombre = $persona->apellido . ', ' . $persona->nombre ?? 'N/A';

                    $this->warn("Investigador SIN cargos docentes activos → ID {$investigador->id} | DNI: {$dni} | Nombre: {$nombre}");

                    DB::table('investigadors')
                        ->where('id', $investigador->id)
                        ->update([
                            'cargo_id' => null,
                            'deddoc' => null,
                            'facultad_id' => null
                        ]);

                    continue;
                }

            }

            DB::commit();

            $this->info('Proceso OK');

        } catch (\Exception $e) {

            DB::rollBack();
            $this->error($e->getMessage());
        }
    }
}
