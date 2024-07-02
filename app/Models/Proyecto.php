<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proyecto extends Model
{
    use HasFactory;


    public function integrantes()
    {
        return $this->hasMany(Integrante::class);
    }
}
