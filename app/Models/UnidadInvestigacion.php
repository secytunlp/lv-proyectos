<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnidadInvestigacion extends Model
{
    use HasFactory;

    protected $fillable = ['tipo', 'denominacion','sigla','especialidad','objetivos','lineas','justificacion','funciones','produccion','proyectos','rrhh','memorias','reglamento','insfraestructura','equipamiento','observaciones','fecha_disposicion','disposicion','estado'];




    public function facultads()
    {
        return $this->hasMany(Unidadfacultad::class, 'unidad_id');
    }

    public function estados()
    {
        return $this->hasMany(UnidadInvestigacionEstado::class, 'unidad_id');
    }

}
