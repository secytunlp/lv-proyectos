<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnidadInvestigacionEstado extends Model
{
    use HasFactory;

    protected $fillable = ['unidad_id','user_id','estado','desde','hasta','comentarios'];

    public function unidad()
    {
        return $this->belongsTo(UnidadInvestigacion::class, 'unidad_id');
    }
}
