<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ViajeEvaluacionEstado extends Model
{
    use HasFactory;

    protected $fillable = ['viaje_evaluacion_id','user_id','estado','desde','hasta','comentarios'];

    public function viaje_evaluacion()
    {
        return $this->belongsTo(ViajeEvaluacion::class);
    }
}
