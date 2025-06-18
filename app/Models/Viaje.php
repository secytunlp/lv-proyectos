<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Viaje extends Model
{
    use HasFactory;

    protected $fillable = ['investigador_id', 'periodo_id','proyecto1_id','proyecto2_id', 'estado','email','notificacion','fecha','telefono','calle','nro','piso','depto','cp', 'titulo_id', 'titulogrado','unidad_id','cargo_id','deddoc','ingreso_cargo', 'facultad_id', 'facultadplanilla_id', 'carrerainv_id', 'organismo_id','ingreso_carrera','unidadcarrera_id','institucion','beca','periodobeca','unlp','unidadbeca_id','categoria_id','sicadi_id','tipo','objetivo','motivo','trabajo','aceptacion','titulotrabajo','autores','congresonombre','lugartrabajo','trabajodesde','trabajohasta','resumen','relevancia','invitacion','modalidad','aval','actividades','convenioB','cvprofesor','profesor','lugarprofesor','libros','compilados','capitulos','articulos','congresos','patentes','intelectuales','informes','congreso','tesis','tesinas','nacional','becas','objetivosC','planC','relacionProyectoC','aportesC','actividadesC','convenioC','generalB','especificoB','actividadesB','cronogramaB','aportesB','relevanciaB','relevanciaA','scholar','link','monto','puntaje','diferencia','observaciones','curriculum','justificacionB','disciplina'];




    public function investigador() {
        return $this->belongsTo('App\Models\Investigador', 'investigador_id');
    }

    public function periodo() {
        return $this->belongsTo('App\Models\Periodo', 'periodo_id');
    }

    public function proyecto1() {
        return $this->belongsTo('App\Models\Proyecto', 'proyecto1_id');
    }

    public function proyecto2() {
        return $this->belongsTo('App\Models\Proyecto', 'proyecto2_id');
    }

    public function titulo() {
        return $this->belongsTo('App\Models\Titulo', 'titulo_id');
    }




    public function unidad() {
        return $this->belongsTo('App\Models\Unidad', 'unidad_id');
    }

    public function unidadcarrera() {
        return $this->belongsTo('App\Models\Unidad', 'unidadcarrera_id');
    }

    public function unidadbeca() {
        return $this->belongsTo('App\Models\Unidad', 'unidadbeca_id');
    }

    public function cargo() {
        return $this->belongsTo('App\Models\Cargo', 'cargo_id');
    }

    public function categoria() {
        return $this->belongsTo('App\Models\Categoria', 'categoria_id');
    }

    public function sicadi() {
        return $this->belongsTo('App\Models\Sicadi', 'sicadi_id');
    }


    public function proyectos()
    {
        return $this->hasMany(ViajeProyecto::class, 'viaje_id');
    }

    public function presupuestos()
    {
        return $this->hasMany(ViajePresupuesto::class, 'viaje_id');
    }

    public function estados()
    {
        return $this->hasMany(ViajeEstado::class);
    }

    public function evaluacions()
    {
        return $this->hasMany(ViajeEvaluacion::class);
    }

    public function ambitos()
    {
        return $this->hasMany(ViajeAmbito::class);
    }

    public function montos()
    {
        return $this->hasMany(ViajeMonto::class);
    }



}
