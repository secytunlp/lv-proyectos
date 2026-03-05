<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IntegranteEstado extends Model
{
    use HasFactory;

    protected $fillable = ['integrante_id','user_id', 'tipo', 'alta', 'baja', 'cambio', 'horas','estado', 'consecuencias', 'motivos', 'reduccion', 'categoria_id', 'sicadi_id','deddoc','cargo_id','facultad_id','unidad_id','carrerainv_id','organismo_id','institucion','beca','desde','hasta','comentarios'];

    public function integrante()
    {
        return $this->belongsTo(Integrante::class);
    }
}
