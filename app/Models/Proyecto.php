<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class Proyecto extends Model
{
    use HasFactory;

    protected $fillable = ['estado', 'tipo', 'codigo', 'sigeva', 'titulo', 'inicio', 'fin', 'facultad_id','duracion','unidad_id','campo_id','disciplina_id','investigacion','linea','resumen','clave1','clave2','clave3','clave4','clave5','clave6','key1','key2','key3','key4','key5','key6'];

    public function integrantes()
    {
        return $this->hasMany(Integrante::class);
    }

    public function unidad() {
        return $this->belongsTo('App\Models\Unidad');
    }

    public function getFacultadNombreAttribute()
    {
        return DB::table('facultads')
            ->where('id', $this->facultad_id)
            ->value('nombre');
    }

    public function disciplina() {
        // Consulta directa a la tabla de disciplinaes
        return DB::table('disciplinas')->where('id', $this->disciplina_id)->first();
    }

    public function especialidad() {
        // Consulta directa a la tabla de especialidades
        return DB::table('especialidads')->where('id', $this->especialidad_id)->first();
    }

    public function estados()
    {
        return $this->hasMany(ProyectoEstado::class, 'proyecto_id');
    }
}
