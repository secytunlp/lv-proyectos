<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProyectoEstado extends Model
{
    use HasFactory;

    protected $fillable = ['proyecto_id','user_id','estado', 'tipo', 'codigo', 'sigeva', 'titulo', 'inicio', 'fin', 'facultad_id','duracion','unidad_id','campo_id','disciplina_id','especialidad_id','investigacion','linea','resumen','clave1','clave2','clave3','clave4','clave5','clave6','key1','key2','key3','key4','key5','key6','desde','hasta','comentarios'];


    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class);
    }
}
