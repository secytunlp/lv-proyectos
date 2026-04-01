<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Proyecto;
use App\Models\ProyectoEstado;
use Illuminate\Support\Facades\DB;
class GenerarEstadosInicialesProyectos extends Command
{
    protected $signature = 'proyectos:generar-estados-iniciales';
    protected $description = 'Genera estado inicial para proyectos sin historial';

    public function handle()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        $proyectos = Proyecto::doesntHave('estados')->get();

        foreach ($proyectos as $proyecto) {

            ProyectoEstado::create([
                'proyecto_id' => $proyecto->id,
                'estado' => $proyecto->estado,
                'tipo' => $proyecto->tipo,
                'codigo' => $proyecto->codigo,
                'sigeva' => $proyecto->sigeva,
                'titulo' => $proyecto->titulo,
                'inicio' => $proyecto->inicio,
                'fin' => $proyecto->fin,
                'facultad_id' => $proyecto->facultad_id,
                'duracion' => $proyecto->duracion,
                'unidad_id' => $proyecto->unidad_id,
                'campo_id' => $proyecto->campo_id,
                'disciplina_id' => $proyecto->disciplina_id,
                'especialidad_id' => $proyecto->especialidad_id,
                'investigacion' => $proyecto->investigacion,
                'linea' => $proyecto->linea,
                'resumen' => $proyecto->resumen,
                'clave1' => $proyecto->clave1,
                'clave2' => $proyecto->clave2,
                'clave3' => $proyecto->clave3,
                'clave4' => $proyecto->clave4,
                'clave5' => $proyecto->clave5,
                'clave6' => $proyecto->clave6,
                'key1' => $proyecto->key1,
                'key2' => $proyecto->key2,
                'key3' => $proyecto->key3,
                'key4' => $proyecto->key4,
                'key5' => $proyecto->key5,
                'key6' => $proyecto->key6,
                'user_id' => 2,
                'desde' => now(),
                'comentarios' => 'Estado inicial',
            ]);

            $this->info("Estado creado para proyecto ID: {$proyecto->id}");
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        $this->info('Proceso finalizado');
    }
}
