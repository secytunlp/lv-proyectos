<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JovenEvaluacionPuntajePosgrado extends Model
{
    use HasFactory;

    protected $fillable = ['joven_evaluacion_id','joven_evaluacion_planilla_id','joven_evaluacion_planilla_posgrado_max_id'];

    public function joven_evaluacion()
    {
        return $this->belongsTo(JovenEvaluacion::class);
    }



}
