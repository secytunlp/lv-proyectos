<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JovenEvaluacionEstado extends Model
{
    use HasFactory;

    protected $fillable = ['joven_evaluacion_id','user_id','estado','desde','hasta','comentarios'];

    public function joven_evaluacion()
    {
        return $this->belongsTo(JovenEvaluacion::class);
    }
}