<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ViajeEvaluacionPuntajePlan extends Model
{
    use HasFactory;

    protected $fillable = ['viaje_evaluacion_id','viaje_evaluacion_planilla_id','viaje_evaluacion_planilla_plan_max_id','puntaje','justificacion'];

    public function viaje_evaluacion()
    {
        return $this->belongsTo(ViajeEvaluacion::class);
    }
}
