<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Integrante extends Model
{
    use HasFactory;

    protected $fillable = ['investigador_id','proyecto_id', 'tipo', 'alta', 'baja', 'cambio', 'horas', 'horas_anteriores','estado', 'curriculum', 'actividades', 'consecuencias', 'motivos', 'cyt', 'reduccion', 'email', 'nacimiento', 'categoria_id', 'sicadi_id','deddoc','cargo_id','alta_cargo','facultad_id','unidad_id','carrerainv_id','organismo_id','ingreso_carrerainv','universidad_id','institucion','beca','alta_beca','baja_beca','resolucion','materias','total','carrera'];

    public function investigador()
    {
        return $this->belongsTo(Investigador::class);
    }

    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class);
    }

    public function estados()
    {
        return $this->hasMany(IntegranteEstado::class);
    }


}
