<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Miembro extends Model
{
    use HasFactory;

    protected $fillable = ['unidad_id', 'tipo', 'nombre', 'apellido', 'cuil', 'horas','estado', 'categoria_id', 'sicadi_id','deddoc','cargo_id','facultad_id','carrerainv_id','organismo_id','beca','activo','estudiante','observaciones','email','nacimiento'];



    public function unidad()
    {
        return $this->belongsTo(UnidadInvestigacion::class, 'unidad_id');
    }

    public function estados()
    {
        return $this->hasMany(MiembroEstado::class);
    }
}
