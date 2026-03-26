<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MiembroEstado extends Model
{
    use HasFactory;

    protected $fillable = ['miembro_id','user_id', 'tipo', 'nombre', 'apellido', 'cuil', 'horas','estado', 'categoria_id', 'sicadi_id','deddoc','cargo_id','facultad_id','carrerainv_id','organismo_id','activo','beca','estudiante','desde','hasta','comentarios'];



    public function miembro()
    {
        return $this->belongsTo(Miembro::class);
    }
}
