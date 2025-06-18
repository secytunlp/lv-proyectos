<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class Proyecto extends Model
{
    use HasFactory;


    public function integrantes()
    {
        return $this->hasMany(Integrante::class);
    }

    public function disciplina() {
        // Consulta directa a la tabla de disciplinaes
        return DB::table('disciplinas')->where('id', $this->disciplina_id)->first();
    }

    public function especialidad() {
        // Consulta directa a la tabla de especialidades
        return DB::table('especialidads')->where('id', $this->especialidad_id)->first();
    }
}
