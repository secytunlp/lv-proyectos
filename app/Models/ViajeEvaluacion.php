<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ViajeEvaluacion extends Model
{
    use HasFactory;

    protected $fillable = ['viaje_id','user_id','estado','fecha', 'interno','puntaje','observaciones'];

    public function viaje()
    {
        return $this->belongsTo(Viaje::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id'); // Asegúrate de que el campo 'user_id' se esté usando
    }


    public function estados()
    {
        return $this->hasMany(ViajeEvaluacionEstado::class);
    }
}
