<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ViajeEstado extends Model
{
    use HasFactory;

    protected $fillable = ['viaje_id','user_id','estado','desde','hasta','comentarios'];

    public function viaje()
    {
        return $this->belongsTo(Viaje::class);
    }
}
