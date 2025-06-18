<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JovenEstado extends Model
{
    use HasFactory;

    protected $fillable = ['joven_id','user_id','estado','desde','hasta','comentarios'];

    public function joven()
    {
        return $this->belongsTo(Joven::class);
    }
}
