<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JovenEvaluacion extends Model
{
    use HasFactory;

    protected $fillable = ['joven_id','user_id','estado','fecha', 'interno','puntaje','observaciones'];

    public function joven()
    {
        return $this->belongsTo(Joven::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id'); // Asegúrate de que el campo 'user_id' se esté usando
    }


    public function estados()
    {
        return $this->hasMany(JovenEvaluacionEstado::class);
    }

    public function puntaje_posgrados()
    {
        return $this->hasMany(JovenEvaluacionPuntajePosgrado::class);
    }

    public function puntaje_cargos()
    {
        return $this->hasMany(JovenEvaluacionPuntajeCargo::class);
    }

    public function puntaje_ant_acads()
    {
        return $this->hasMany(JovenEvaluacionPuntajeAntAcad::class);
    }

    public function puntaje_otros()
    {
        return $this->hasMany(JovenEvaluacionPuntajeOtro::class);
    }

    public function puntaje_produccions()
    {
        return $this->hasMany(JovenEvaluacionPuntajeProduccion::class);
    }

    public function puntaje_anteriors()
    {
        return $this->hasMany(JovenEvaluacionPuntajeAnterior::class);
    }

    public function puntaje_justificacions()
    {
        return $this->hasMany(JovenEvaluacionPuntajeJustificacion::class);
    }
}
