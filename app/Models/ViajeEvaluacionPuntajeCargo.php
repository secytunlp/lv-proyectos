<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ViajeEvaluacionPuntajeCargo extends Model
{
    use HasFactory;

    protected $fillable = ['viaje_evaluacion_id','viaje_evaluacion_planilla_id','viaje_evaluacion_planilla_cargo_max_id'];

    public function viaje_evaluacion()
    {
        return $this->belongsTo(ViajeEvaluacion::class);
    }
}
