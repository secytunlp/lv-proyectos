<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SicadiConvocatoria extends Model
{
    use HasFactory;
    protected $table = 'sicadi_convocatorias';

    protected $fillable = ['nombre', 'tipo', 'year']; // Asegúrate de incluir las columnas que usás
}
