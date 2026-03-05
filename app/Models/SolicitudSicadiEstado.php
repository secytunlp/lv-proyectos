<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SolicitudSicadiEstado extends Model
{
    use HasFactory;

    protected $fillable = ['solicitud_sicadi_id','user_id','estado','desde','hasta','comentarios'];

    public function solicitud_sicadi()
    {
        return $this->belongsTo(SolicitudSicadi::class);
    }
}
