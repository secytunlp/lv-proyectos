<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Joven extends Model
{
    use HasFactory;

    protected $fillable = ['investigador_id', 'periodo_id', 'estado','email','notificacion','fecha','nacimiento','telefono','calle','nro','piso','depto','cp', 'titulo_id','egresogrado', 'titulopost_id','egresoposgrado','doctorado','unidad_id','cargo_id','deddoc','ingreso_cargo', 'facultad_id', 'facultadplanilla_id','director', 'carrerainv_id', 'organismo_id','ingreso_carrera','unidadcarrera_id','unidadbeca_id','puntaje','diferencia','observaciones','objetivo','curriculum','justificacion','disciplina'];


    public function investigador() {
        return $this->belongsTo('App\Models\Investigador', 'investigador_id');
    }

    public function periodo() {
        return $this->belongsTo('App\Models\Periodo', 'periodo_id');
    }

    public function titulo() {
        return $this->belongsTo('App\Models\Titulo', 'titulo_id');
    }


    public function titulopost() {
        return $this->belongsTo('App\Models\Titulo', 'titulopost_id');
    }

    public function unidad() {
        return $this->belongsTo('App\Models\Unidad', 'unidad_id');
    }

    public function cargo() {
        return $this->belongsTo('App\Models\Cargo', 'cargo_id');
    }


    public function becas()
    {
        return $this->hasMany(JovenBeca::class, 'joven_id');
    }

    public function proyectos()
    {
        return $this->hasMany(JovenProyecto::class, 'joven_id');
    }

    public function presupuestos()
    {
        return $this->hasMany(JovenPresupuesto::class, 'joven_id');
    }

    public function estados()
    {
        return $this->hasMany(JovenEstado::class);
    }

    public function evaluacions()
    {
        return $this->hasMany(JovenEvaluacion::class);
    }

}
