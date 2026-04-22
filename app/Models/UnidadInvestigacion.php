<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnidadInvestigacion extends Model
{
    use HasFactory;

    protected $fillable = ['tipo', 'denominacion','sigla','especialidad','objetivos','lineas','justificacion','funciones','produccion','proyectos','rrhh','memorias','reglamento','infraestructura','equipamiento','observaciones','fecha_disposicion','disposicion','estado','tipificacion','anidada'];




    public function facultads()
    {
        return $this->hasMany(UnidadFacultad::class, 'unidad_id');
    }

    public function estados()
    {
        return $this->hasMany(UnidadInvestigacionEstado::class, 'unidad_id');
    }

    public function colegios()
    {
        return $this->hasMany(UnidadColegio::class, 'unidad_id');
    }

    public function externos()
    {
        return $this->hasMany(UnidadExterno::class, 'unidad_id');
    }

}
