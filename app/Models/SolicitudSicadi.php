<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SolicitudSicadi extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'apellido','email_institucional','email_alternativo','celular','calle','nro','piso','dpto','localidad','cp','observaciones','tipoDocumento','documento','cuil','nacimiento','cuil','genero','foto','fallecimiento','sedici','orcid','scholar','scopus','titulo','titulo_entidad', 'posgrado','posgrado_entidad','cargo_docente', 'cargo_dedicacion','cargo_ua', 'ui_sigla','ui_nombre','beca_tipo','beca_entidad','nacionalidad','beca_inicio','beca_ui_sigla','beca_ui_nombre','carrera_cargo','carrera_empleador','beca_fin','carrera_ui_sigla','carrera_ui_nombre','proyecto_entidad', 'proyecto_codigo', 'carrera_ingreso','proyecto_director','proyecto_titulo','proyecto_inicio','presentacion_ua','categoria_spu','categoria_solicitada', 'mecanismo','area','subarea','foto','proyecto_fin','observaciones'];







}
