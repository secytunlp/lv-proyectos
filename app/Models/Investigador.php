<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Investigador extends Model
{
    use HasFactory;

    protected $fillable = ['persona_id','ident', 'categoria_id', 'sicadi_id', 'carrerainv_id', 'organismo_id', 'facultad_id', 'cargo_id','deddoc', 'universidad_id', 'titulo_id', 'titulopost_id', 'unidad_id', 'institucion', 'beca', 'materias', 'total', 'carrera'];

    public function persona() {
        return $this->belongsTo('App\Models\Persona');
    }

    public function universidad() {
        return $this->belongsTo('App\Models\Universidad');
    }

    public function titulo() {
        return $this->belongsTo('App\Models\Titulo', 'titulo_id');
    }

    public function titulopost() {
        return $this->belongsTo('App\Models\Titulo', 'titulopost_id');
    }

    public function titulos() {

        return $this->belongsToMany('App\Models\Titulo', 'investigador_titulos')->withPivot('egreso');;
    }

    public function tituloposts() {

        return $this->belongsToMany('App\Models\Titulo', 'investigador_tituloposts')->withPivot('egreso');;
    }


}
