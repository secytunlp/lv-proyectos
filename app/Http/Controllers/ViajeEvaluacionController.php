<?php

namespace App\Http\Controllers;


use App\Mail\ViajeEnviada;
use App\Models\Viaje;
use App\Models\ViajeEvaluacion;
use App\Models\ViajeEvaluacionEstado;

use App\Traits\SanitizesInput;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Constants;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator; // Importar la fachada Validator
use Illuminate\Support\Facades\Auth; // Asegúrate de importar esta línea
use Illuminate\Support\Facades\Storage;
use ZipArchive;

use PDF;

use Illuminate\Support\Facades\Mail;
use App\Models\User;
use Spatie\Permission\Models\Role; // Importa la clase Role

use App\Mail\EvaluadorViajeEnviada;

use App\Http\Controllers\ViajeController;
//use Barryvdh\DomPDF\Facade as PDF;
class ViajeEvaluacionController extends Controller
{

    use SanitizesInput;
    function __construct()
    {
        /*$this->middleware('permission:integrante-listar|integrante-crear|integrante-editar|integrante-eliminar', ['only' => ['index','store','dataTable','admitir']]);
        $this->middleware('permission:integrante-crear', ['only' => ['create','store','buscarInvestigador','generateAltaPDF','archivos']]);
        $this->middleware('permission:integrante-editar', ['only' => ['edit','update','enviar']]);
        $this->middleware('permission:integrante-eliminar', ['only' => ['destroy']]);*/
        //dd(session()->all());
    }





    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $viajeId = $request->query('viaje_id');
        $viaje = null;

        // Si se proporciona un ID de viaje, buscalo en la base de datos
        if ($viajeId) {
            $viaje = Viaje::findOrFail($viajeId);
        }
        $periodos = DB::table('periodos')->orderBy('nombre','DESC')->get();
        $currentPeriod = Constants::YEAR_VIAJES;

        // Pasar el viaje (si existe) a la vista
        return view('viaje_evaluacions.index', compact('viaje','periodos','currentPeriod'));
    }




    public function dataTable(Request $request)
    {

        $viajeId = $request->input('viaje_id');
        $columnas = ['personas.nombre','periodos.nombre', 'personas.apellido', 'viaje_evaluacions.fecha',DB::raw("IFNULL(users.name, viaje_evaluacions.user_name)"),DB::raw("CASE WHEN viaje_evaluacions.interno = 1 THEN 'SI' ELSE 'NO' END"), // Transformación para la columna interno
            'viaje_evaluacions.estado','viaje_evaluacions.puntaje']; // Define las columnas disponibles
        $columnaOrden = $columnas[$request->input('order.0.column')];
        $orden = $request->input('order.0.dir');


        $busqueda = $request->input('search.value');
        $periodo = $request->input('periodo'); // Obtener el filtro de período de la solicitud
        $predefinido = $request->input('predefinido');

        // Consulta base
        $query = ViajeEvaluacion::select('viaje_evaluacions.id as id', 'personas.nombre as persona_nombre','periodos.nombre as periodo', DB::raw("CONCAT(personas.apellido, ', ', personas.nombre) as persona_apellido"), 'viaje_evaluacions.fecha',
            DB::raw("IFNULL(users.name, viaje_evaluacions.user_name) as usuario_nombre"), DB::raw("CASE WHEN viaje_evaluacions.interno = 1 THEN 'SI' ELSE 'NO' END AS interno"), // Transformación para la columna interno
            'viaje_evaluacions.estado','viaje_evaluacions.puntaje',DB::raw("(SELECT COUNT(*) FROM viaje_evaluacions je2
                  WHERE je2.viaje_id = viaje_evaluacions.viaje_id
                  AND (je2.estado = 'evaluada' OR je2.estado = 'en evaluacion')) as total_evaluaciones"),DB::raw("(SELECT COUNT(*) FROM viaje_evaluacions je3
                  WHERE je3.viaje_id = viaje_evaluacions.viaje_id
                  AND je3.estado = 'evaluada') as evaluaciones_evaluada"))
            ->leftJoin('viajes', 'viaje_evaluacions.viaje_id', '=', 'viajes.id')
            ->leftJoin('investigadors', 'viajes.investigador_id', '=', 'investigadors.id')
            ->leftJoin('periodos', 'viajes.periodo_id', '=', 'periodos.id')
            ->leftJoin('personas', 'investigadors.persona_id', '=', 'personas.id')
            ->leftJoin('users', 'viaje_evaluacions.user_id', '=', 'users.id');


        // Aplicar filtro por viaje si se proporciona el ID del viaje
        if ($viajeId) {
            $query->where('viaje_evaluacions.viaje_id', $viajeId);
        }
        // Filtrar por período si se proporciona
        if (!empty($periodo)) {
            $query->where('viajes.periodo_id', $periodo);
        }
        if (!empty($predefinido)) {
            if ($predefinido == 2) {
                $query->having(DB::raw('total_evaluaciones'), '<', 2);
            }
            if ($predefinido == 3) {
                // Mostrar solo solicitudes con estado "En evaluacion"
                $query->where('viajes.estado', 'En evaluacion')
                    // Filtrar solicitudes con más de una evaluación en estado "Evaluada"
                    ->having(DB::raw('evaluaciones_evaluada'), '>', 1);
            }
        }
        $user = auth()->user();
        $selectedRoleId = session('selected_rol');
        /*if ($selectedRoleId != 1) {
            $query->where(function ($query) {
                $query->whereColumn('integrantes.alta', '!=', 'integrantes.baja')
                    ->orWhereNull('integrantes.alta')
                    ->orWhereNull('integrantes.baja');
            });

        }*/

        // Aplicar la búsqueda
        if (!empty($busqueda)) {
            $query->where(function ($query) use ($columnas, $busqueda) {
                foreach ($columnas as $columna) {
                    $query->orWhere($columna, 'like', "%$busqueda%");
                }
            });
        }


        if ($selectedRoleId == 6) {
            $user = auth()->user();

            $query->where(function ($query) use ($user) {
                $query->where('viaje_evaluacions.user_cuil', '=', $user->cuil)
                    ->orWhere('viaje_evaluacions.user_id', '=', $user->id);
            });
        }

        if ($selectedRoleId==4){
            $user = auth()->user();
            //$currentDate = date('Y-m-d');

            $query->where(function ($query) use ($user) {
                $query->where('viajes.facultadplanilla_id', '=', $user->facultad_id);
            });
        }


        // Obtener la cantidad total de registros después de aplicar el filtro de búsqueda
        $recordsFiltered = $query->count();

        // Protección contra consumo excesivo de recursos
        $length = intval($request->input('length', 10));
        $length = ($length > 0 && $length <= 100) ? $length : 10;

        $start = intval($request->input('start', 0));
        $start = ($start >= 0) ? $start : 0;
        // Obtener solo los elementos paginados
        $datos = $query->orderBy($columnaOrden, $orden)
            ->skip($start)
            ->take($length)
            ->get();

        // Obtener la cantidad total de registros sin filtrar
        $recordsTotal = ViajeEvaluacion::count();

        return response()->json([
            'data' => $datos,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'draw' => $request->draw,
        ]);
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $rules = [
            'internos.*' => 'required',
            'externos.*' => 'required',
        ];

        // Definir los mensajes de error personalizados
        $messages = [
            'internos.*.required' => 'Falta seleccionar evaluadores',
            'externos.*.required' => 'Falta seleccionar evaluadores',
        ];


        // Crear el validador con las reglas y mensajes
        $validator = Validator::make($request->all(), $rules, $messages);
        // Validar y verificar si hay errores
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }


        $viaje_id=$request->viaje_id;
        $viaje = Viaje::findOrFail($viaje_id);
        DB::beginTransaction();
        $ok=1;
        $noBorrar='';

        if(!empty($request->internos)) {
            if ($request->internos_id) {
                foreach ($request->internos_id as $id) {
                    $noBorrar .= $id . ',';
                }


            }
            foreach($request->internos as $item=>$v){
                $user_id = $request->internos[$item];
                $user = User::find($user_id);

                $cuilUsuario = $user->cuil;
                $periodo = $viaje->periodo_id; // Cambia 'periodo' por el nombre de la relación en tu modelo
                $participacionExistente = Viaje::where('periodo_id', $periodo)
                    ->whereHas('investigador', function($query) use ($cuilUsuario) {
                        $query->whereHas('persona', function($query) use ($cuilUsuario) {
                            $query->where('cuil', $cuilUsuario);
                        });
                    })
                    ->where(function($query) {
                        $query->where('estado', '!=', 'Creada')
                            ->where('estado', '!=', 'Retirada');
                    })
                    ->where('id', '!=', $viaje->id) // Excluir el viaje actual de la verificación
                    ->exists();
                if ($participacionExistente) {

                    $error = "El evaluador: ".$user->name." presentó solicitud";
                }

                $data2=array(
                    'viaje_id'=>$viaje_id,
                    'user_id'=>$user_id,
                    'interno'=>1,
                    'estado'=>'Creada',
                    'fecha'=>Carbon::now()
                );
                try {
                    if (!empty($request->internos_id[$item])){
                        $data2['id']=$request->internos_id[$item];
                        $evaluacion=ViajeEvaluacion::find($request->internos_id[$item]);
                        //$alineacion->update($data2);
                    }
                    else{
                        $evaluacion = ViajeEvaluacion::create($data2);
                        $this->cambiarEstado($evaluacion,'Evaluador asignado');
                    }
                    $noBorrar .=$evaluacion->id.',';
                }catch(QueryException $ex){
                    if ($ex->errorInfo[1] === 1062) {
                        $error="El evaluador ".$user->name." ya tiene asignada la solicitud.";
                    } else {
                        $error = $ex->getMessage();
                    }

                    $ok=0;
                    continue;
                }


            }
        }
        if(!empty($request->externos)) {
            if ($request->externos_id) {
                foreach ($request->externos_id as $id) {
                    $noBorrar .= $id . ',';
                }


            }
            foreach($request->externos as $item=>$v){
                $user_id = $request->externos[$item];
                $user = User::find($user_id);

                $cuilUsuario = $user->cuil;
                $periodo = $viaje->periodo_id; // Cambia 'periodo' por el nombre de la relación en tu modelo
                $participacionExistente = Viaje::where('periodo_id', $periodo)
                    ->whereHas('investigador', function($query) use ($cuilUsuario) {
                        $query->whereHas('persona', function($query) use ($cuilUsuario) {
                            $query->where('cuil', $cuilUsuario);
                        });
                    })
                    ->where(function($query) {
                        $query->where('estado', '!=', 'Creada')
                            ->where('estado', '!=', 'Retirada');
                    })
                    ->where('id', '!=', $viaje->id) // Excluir el viaje actual de la verificación
                    ->exists();
                if ($participacionExistente) {

                    $error = "El evaluador: ".$user->name." presentó solicitud";
                }

                $data2=array(
                    'viaje_id'=>$viaje_id,
                    'user_id'=>$user_id,
                    'interno'=>0,
                    'estado'=>'Creada',
                    'fecha'=>Carbon::now()
                );
                try {
                    if (!empty($request->externos_id[$item])){
                        $data2['id']=$request->externos_id[$item];
                        $evaluacion=ViajeEvaluacion::find($request->externos_id[$item]);
                        //$alineacion->update($data2);
                    }
                    else{
                        $evaluacion = ViajeEvaluacion::create($data2);
                        $this->cambiarEstado($evaluacion,'Evaluador asignado');
                    }
                    $noBorrar .=$evaluacion->id.',';
                }catch(QueryException $ex){
                    if ($ex->errorInfo[1] === 1062) {
                        $error="El evaluador ".$user->name." ya tiene asignada la solicitud.";
                    } else {
                        $error = $ex->getMessage();
                    }

                    $ok=0;
                    continue;
                }


            }
        }








        try {
            ViajeEvaluacion::where('viaje_id',"$viaje_id")->whereNotIn('id', explode(',', $noBorrar))->delete();

        }
        catch(QueryException $ex){
            $error = $ex->getMessage();
            $ok=0;

        }
        if ($ok){
            DB::commit();
            $respuestaID='success';
            $respuestaMSJ='Evaluadores asignados con éxito';
        }
        else{
            DB::rollback();
            $respuestaID='error';
            $respuestaMSJ=$error;
        }
        return redirect()->route('viaje_evaluacions.index', array('viaje_id' => $viaje_id))->with($respuestaID, $respuestaMSJ);

    }

    public function cambiarEstado($viaje_evaluacion, $comentarios)
    {

        // Actualizar el registro de estado existente donde 'hasta' es null
        ViajeEvaluacionEstado::where('viaje_evaluacion_id', $viaje_evaluacion->id)
            ->whereNull('hasta')
            ->update(['hasta' => Carbon::now()]);

        // Obtener el ID del usuario autenticado
        $userId = Auth::id();

        // Crear registro en integrante_estados con el estado "Alta Creada" y el user_id
        $viaje_evaluacion->estados()->create([
            'estado' => $viaje_evaluacion->estado,
            'user_id' => $userId,
            'comentarios' => $comentarios,
            'desde' => Carbon::now(), // Establecer 'desde' como la fecha actual

        ]);
    }



    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Proyecto  $proyecto
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Proyecto  $proyecto
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $viajeId = $request->input('viaje_id');
        $viaje = null;
        // Si se proporciona un ID de viaje, buscalo en la base de datos
        if ($viajeId) {
            $viaje = Viaje::findOrFail($viajeId);

            if (($viaje->estado!='Admitida')&&($viaje->estado!='En evaluación')) {

                return redirect()->route('viaje_evaluacions.index', ['viaje_id' => $viajeId])
                    ->withErrors(['message' => 'La solicitud debe estar con estado ADMITIDA o EN EVALUACION para que se le puedan asignar evaluadores']);
            }
        }


        $periodoId = $viaje->periodo_id;

// Obtener los evaluadores que pertenezcan a la misma facultad y no hayan participado en la misma convocatoria
        $internos = User::whereHas('roles', function ($query) {
            $query->where('name', 'Evaluador');
        })
            ->where('facultad_id', $viaje->facultadplanilla_id) // Filtrar por la facultad del viaje
            ->where(function ($query) use ($periodoId) {
                // Usuarios evaluadores que no participaron en la convocatoria (por cuil)
                $query->whereNotExists(function ($subQuery) use ($periodoId) {
                    $subQuery->select(DB::raw(1))
                        ->from('viajes')
                        ->join('investigadors', 'investigadors.id', '=', 'viajes.investigador_id')
                        ->join('personas', 'personas.id', '=', 'investigadors.persona_id')
                        ->whereColumn('personas.cuil', 'users.cuil') // Comparar por cuil
                        ->where('viajes.periodo_id', $periodoId);
                })
                    // O si participaron, su estado debe ser "Creada" o "Retirada" (por cuil)
                    ->orWhereExists(function ($subQuery) use ($periodoId) {
                        $subQuery->select(DB::raw(1))
                            ->from('viajes')
                            ->join('investigadors', 'investigadors.id', '=', 'viajes.investigador_id')
                            ->join('personas', 'personas.id', '=', 'investigadors.persona_id')
                            ->whereColumn('personas.cuil', 'users.cuil') // Comparar por cuil
                            ->where('viajes.periodo_id', $periodoId)
                            ->where(function ($query) {
                                $query->where('viajes.estado', 'Creada')
                                    ->orWhere('viajes.estado', 'Retirada');
                            });
                    });
            })
            ->get();



        $internos = $internos->pluck('name', 'id')->prepend('', '');


        $externos = User::whereHas('roles', function ($query) {
            $query->where('name', 'Evaluador');
        })
            ->where('facultad_id','!=', $viaje->facultadplanilla_id) // Filtrar por distinta facultad que el viaje
            ->where(function ($query) use ($periodoId) {
                // Usuarios evaluadores que no participaron en la convocatoria (por cuil)
                $query->whereNotExists(function ($subQuery) use ($periodoId) {
                    $subQuery->select(DB::raw(1))
                        ->from('viajes')
                        ->join('investigadors', 'investigadors.id', '=', 'viajes.investigador_id')
                        ->join('personas', 'personas.id', '=', 'investigadors.persona_id')
                        ->whereColumn('personas.cuil', 'users.cuil') // Comparar por cuil
                        ->where('viajes.periodo_id', $periodoId);
                })
                    // O si participaron, su estado debe ser "Creada" o "Retirada" (por cuil)
                    ->orWhereExists(function ($subQuery) use ($periodoId) {
                        $subQuery->select(DB::raw(1))
                            ->from('viajes')
                            ->join('investigadors', 'investigadors.id', '=', 'viajes.investigador_id')
                            ->join('personas', 'personas.id', '=', 'investigadors.persona_id')
                            ->whereColumn('personas.cuil', 'users.cuil') // Comparar por cuil
                            ->where('viajes.periodo_id', $periodoId)
                            ->where(function ($query) {
                                $query->where('viajes.estado', 'Creada')
                                    ->orWhere('viajes.estado', 'Retirada');
                            });
                    });
            })
            ->get();
        $externos = $externos->pluck('name', 'id')->prepend('','');

        return view('viaje_evaluacions.create',compact('internos','externos','viaje'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Proyecto  $proyecto
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

    }



    public function generatePDF(Request $request,$attach = false)
    {

        if ($request->has('viaje_evaluacion_id')) {
            $evaluacionId = $request->query('viaje_evaluacion_id');
            $evaluacion = ViajeEvaluacion::findOrFail($evaluacionId);
            $viaje = Viaje::findOrFail($evaluacion->viaje_id);
            // Haz algo con $evaluacionId
        } else {
            $viaje = Viaje::findOrFail($request->query('viaje_id'));
            $user = auth()->user();
            $evaluacion = $viaje->evaluacions()->where('user_id', $user->id)->first(); // Evaluación del usuario
            $evaluacionId=$evaluacion->id;
        }

        // Consulta base
        $query = ViajeEvaluacion::select('viaje_evaluacions.id as id','investigadors.id as investigador_id', 'periodos.nombre as periodo_nombre', DB::raw("CONCAT(personas.apellido, ', ', personas.nombre) as persona_apellido"),'personas.cuil', 'facultads.nombre as facultad_nombre', 'viaje_evaluacions.estado as estado','viaje_evaluacions.puntaje as puntaje',
            DB::raw("IFNULL(users.name, viaje_evaluacions.user_name) as usuario_nombre"),
            DB::raw("IFNULL(users.cuil, viaje_evaluacions.user_cuil) as usuario_cuil"))
            ->leftJoin('viajes', 'viaje_evaluacions.viaje_id', '=', 'viajes.id')
            ->leftJoin('investigadors', 'viajes.investigador_id', '=', 'investigadors.id')
            ->leftJoin('periodos', 'viajes.periodo_id', '=', 'periodos.id')
            ->leftJoin('personas', 'investigadors.persona_id', '=', 'personas.id')
            ->leftJoin('facultads', 'viajes.facultadplanilla_id', '=', 'facultads.id')
            ->leftJoin('users', 'viaje_evaluacions.user_id', '=', 'users.id');



        $query->where('viaje_evaluacions.id', $evaluacionId);



        // Obtener solo los elementos paginados
        $datos = $query->first();
        //dd($datos);

        // Verifica si hay puntajes
        if (!$datos->puntaje) {
            return response()->json([
                'message' => 'Aún no ha cargado ningún puntaje.',
            ], 400); // 400 Bad Request
        }


        $tipoMap = [
            'Investigador Formado' => 'Formados',
            'Investigador En Formación' => 'En formación',
        ];

        $motivoMap = [
            'A) Reuniones Científicas' => 'A',
            'B) Estadía de trabajo para investigar en ámbitos académicos externos a la UNLP' => 'B',
            'C) Estadía de Trabajo en la UNLP para un Investigador Invitado' => 'C',
        ];

        $tipo = $tipoMap[$viaje->tipo] ?? null;
        $motivo = $motivoMap[$viaje->motivo] ?? null;

        if ($tipo && $motivo) {
            $planilla = DB::table('viaje_evaluacion_planillas')
                ->where('viaje_evaluacion_planillas.periodo_id', $viaje->periodo_id)
                ->where('viaje_evaluacion_planillas.tipo', $tipo)
                ->where('viaje_evaluacion_planillas.motivo', $motivo)
                ->first();
        } else {
            $planilla = null; // Manejo de casos en los que no se encuentren coincidencias
        }


        $categoriaMaximos = DB::table('viaje_evaluacion_planillas')
            ->leftJoin('viaje_evaluacion_planilla_categoria_maxs', 'viaje_evaluacion_planilla_categoria_maxs.viaje_evaluacion_planilla_id', '=', 'viaje_evaluacion_planillas.id')
            ->leftJoin('categorias', 'viaje_evaluacion_planilla_categoria_maxs.categoria_id', '=', 'categorias.id')
            ->leftJoin('sicadis', 'viaje_evaluacion_planilla_categoria_maxs.categoria_id', '=', 'sicadis.id')
            ->where('viaje_evaluacion_planilla_categoria_maxs.viaje_evaluacion_planilla_id', $planilla->id)
            ->select(
                'viaje_evaluacion_planilla_categoria_maxs.*',
                DB::raw("
            CASE
                WHEN categorias.id = 1 THEN categorias.nombre
                ELSE CONCAT(categorias.nombre, '/', sicadis.nombre)
            END as categoria_nombre
        ")
            )
            ->orderBy('categorias.id', 'ASC')
            ->get();

        $maxCategoria = DB::table('viaje_evaluacion_planillas')
            ->leftJoin('viaje_evaluacion_planilla_categoria_maxs', 'viaje_evaluacion_planilla_categoria_maxs.viaje_evaluacion_planilla_id', '=', 'viaje_evaluacion_planillas.id')

            ->where('viaje_evaluacion_planilla_categoria_maxs.viaje_evaluacion_planilla_id',$planilla->id)
            ->max('viaje_evaluacion_planilla_categoria_maxs.maximo');  // Aquí obtienes el máximo valor de la columna 'maximo'



        $cargoMaximos = DB::table('viaje_evaluacion_planillas')
            ->leftJoin('viaje_evaluacion_planilla_cargo_maxs', 'viaje_evaluacion_planilla_cargo_maxs.viaje_evaluacion_planilla_id', '=', 'viaje_evaluacion_planillas.id')
            ->leftJoin('viaje_evaluacion_planilla_cargos', 'viaje_evaluacion_planilla_cargo_maxs.viaje_evaluacion_planilla_cargo_id', '=', 'viaje_evaluacion_planilla_cargos.id')
            ->where('viaje_evaluacion_planilla_cargo_maxs.viaje_evaluacion_planilla_id',$planilla->id)
            ->select('viaje_evaluacion_planilla_cargo_maxs.*', 'viaje_evaluacion_planilla_cargos.nombre as cargo_nombre', 'viaje_evaluacion_planilla_cargos.id as cargo_id')
            ->get();

        $maxCargo = DB::table('viaje_evaluacion_planillas')
            ->leftJoin('viaje_evaluacion_planilla_cargo_maxs', 'viaje_evaluacion_planilla_cargo_maxs.viaje_evaluacion_planilla_id', '=', 'viaje_evaluacion_planillas.id')

            ->where('viaje_evaluacion_planilla_cargo_maxs.viaje_evaluacion_planilla_id',$planilla->id)
            ->max('viaje_evaluacion_planilla_cargo_maxs.maximo');  // Aquí obtienes el máximo valor de la columna 'maximo'

        $itemMaximos = DB::table('viaje_evaluacion_planillas')
            ->leftJoin('viaje_evaluacion_planilla_item_maxs', 'viaje_evaluacion_planilla_item_maxs.viaje_evaluacion_planilla_id', '=', 'viaje_evaluacion_planillas.id')
            ->leftJoin('viaje_evaluacion_planilla_items', 'viaje_evaluacion_planilla_item_maxs.viaje_evaluacion_planilla_item_id', '=', 'viaje_evaluacion_planilla_items.id')
            ->leftJoin('evaluacion_grupos', 'viaje_evaluacion_planilla_item_maxs.evaluacion_grupo_id', '=', 'evaluacion_grupos.id')
            ->leftJoin('evaluacion_grupos as padre', 'evaluacion_grupos.padre_id', '=', 'padre.id')
            ->where('viaje_evaluacion_planilla_item_maxs.viaje_evaluacion_planilla_id',$planilla->id)

            ->select('viaje_evaluacion_planilla_item_maxs.*', 'viaje_evaluacion_planilla_items.nombre as item_nombre', 'evaluacion_grupos.maximo as grupo_maximo', 'evaluacion_grupos.nombre as grupo_nombre', 'padre.nombre as padre_nombre','padre.id as padre_id','padre.maximo as padre_maximo')
            ->orderBy('viaje_evaluacion_planilla_items.orden', 'ASC')
            ->get();

        $eventoMaximos = DB::table('viaje_evaluacion_planillas')
            ->leftJoin('viaje_evaluacion_planilla_evento_maxs', 'viaje_evaluacion_planilla_evento_maxs.viaje_evaluacion_planilla_id', '=', 'viaje_evaluacion_planillas.id')
            ->leftJoin('viaje_evaluacion_planilla_eventos', 'viaje_evaluacion_planilla_evento_maxs.viaje_evaluacion_planilla_evento_id', '=', 'viaje_evaluacion_planilla_eventos.id')
            ->leftJoin('evaluacion_grupos', 'viaje_evaluacion_planilla_evento_maxs.evaluacion_grupo_id', '=', 'evaluacion_grupos.id')
            ->leftJoin('evaluacion_grupos as padre', 'evaluacion_grupos.padre_id', '=', 'padre.id')
            ->where('viaje_evaluacion_planilla_evento_maxs.viaje_evaluacion_planilla_id',$planilla->id)

            ->select('viaje_evaluacion_planilla_evento_maxs.*', 'viaje_evaluacion_planilla_eventos.nombre as evento_nombre', 'evaluacion_grupos.maximo as grupo_maximo', 'evaluacion_grupos.nombre as grupo_nombre', 'padre.nombre as padre_nombre','padre.id as padre_id','padre.maximo as padre_maximo')
            ->orderBy('viaje_evaluacion_planilla_eventos.orden', 'ASC')
            ->get();

        $planMaximos = DB::table('viaje_evaluacion_planillas')
            ->leftJoin('viaje_evaluacion_planilla_plan_maxs', 'viaje_evaluacion_planilla_plan_maxs.viaje_evaluacion_planilla_id', '=', 'viaje_evaluacion_planillas.id')


            ->where('viaje_evaluacion_planilla_plan_maxs.viaje_evaluacion_planilla_id',$planilla->id)

            ->select('viaje_evaluacion_planilla_plan_maxs.*')

            ->get();

        $data = [
            'year' => $datos->periodo_nombre,
            'tipo' => $tipo,
            'motivo' => $motivo,
            'facultadplanilla' => $datos->facultad_nombre,
            'solicitante' => $datos->persona_apellido,
            'estado' => $datos->estado,
            'evaluador' => $datos->usuario_nombre,
            'evaluacion' => $evaluacion,

            'planilla' => $planilla,

            'cargoMaximos' => $cargoMaximos,
            'maxCargo' => $maxCargo,
            'categoriaMaximos' => $categoriaMaximos,
            'maxCategoria' => $maxCategoria,
            'itemMaximos' => $itemMaximos,
            'eventoMaximos' => $eventoMaximos,
            'planMaximos' => $planMaximos,

        ];
        //dd($data);


        $template = 'viaje_evaluacions.pdfevaluacion';

        $pdf = PDF::loadView($template, $data);

        $pdfPath = 'Evaluacion_' . $datos->cuil . '_' . $datos->usuario_cuil . '.pdf';

        if ($attach) {
            $fullPath = public_path('/temp/' . $pdfPath);
            $pdf->save($fullPath);
            return $fullPath; // Devuelve la ruta del archivo para su uso posterior
        } else {

            return $pdf->download($pdfPath);
        }

        // Renderiza la vista de previsualización para HTML
        //return view('viaje_evaluacions.pdfevaluacion', $data);
    }




    public function evaluar($id)
    {
        $viaje = Viaje::find($id);
        $user = auth()->user();
        $evaluacion = $viaje->evaluacions()->where('user_id', $user->id)->first(); // Evaluación del usuario
        $facultad= DB::table('facultads')->where('id', $viaje->facultadplanilla_id)->first();

        $tipoMap = [
            'Investigador Formado' => 'Formados',
            'Investigador En Formación' => 'En formación',
        ];

        $motivoMap = [
            'A) Reuniones Científicas' => 'A',
            'B) Estadía de trabajo para investigar en ámbitos académicos externos a la UNLP' => 'B',
            'C) Estadía de Trabajo en la UNLP para un Investigador Invitado' => 'C',
        ];

        $tipo = $tipoMap[$viaje->tipo] ?? null;
        $motivo = $motivoMap[$viaje->motivo] ?? null;

        if ($tipo && $motivo) {
            $planilla = DB::table('viaje_evaluacion_planillas')
                ->where('viaje_evaluacion_planillas.periodo_id', $viaje->periodo_id)
                ->where('viaje_evaluacion_planillas.tipo', $tipo)
                ->where('viaje_evaluacion_planillas.motivo', $motivo)
                ->first();
        } else {
            $planilla = null; // Manejo de casos en los que no se encuentren coincidencias
        }
        //dd($planilla);

        $categoriaMaximos = DB::table('viaje_evaluacion_planillas')
            ->leftJoin('viaje_evaluacion_planilla_categoria_maxs', 'viaje_evaluacion_planilla_categoria_maxs.viaje_evaluacion_planilla_id', '=', 'viaje_evaluacion_planillas.id')
            ->leftJoin('categorias', 'viaje_evaluacion_planilla_categoria_maxs.categoria_id', '=', 'categorias.id')
            ->leftJoin('sicadis', 'viaje_evaluacion_planilla_categoria_maxs.categoria_id', '=', 'sicadis.id')
            ->where('viaje_evaluacion_planilla_categoria_maxs.viaje_evaluacion_planilla_id', $planilla->id)
            ->select(
                'viaje_evaluacion_planilla_categoria_maxs.*',
                DB::raw("
            CASE
                WHEN categorias.id = 1 THEN categorias.nombre
                ELSE CONCAT(categorias.nombre, '/', sicadis.nombre)
            END as categoria_nombre
        ")
            )
            ->orderBy('categorias.id', 'ASC')
            ->get();

        //dd($categoriaMaximos);
        $categoria_id = 1;
        if ($viaje->sicadi_id) {
            $categoria_id = $viaje->sicadi_id;
        }
        else{
            $categoria_id = $viaje->categoria_id;
        }


        $maxCategoria = DB::table('viaje_evaluacion_planillas')
            ->leftJoin('viaje_evaluacion_planilla_categoria_maxs', 'viaje_evaluacion_planilla_categoria_maxs.viaje_evaluacion_planilla_id', '=', 'viaje_evaluacion_planillas.id')

            ->where('viaje_evaluacion_planilla_categoria_maxs.viaje_evaluacion_planilla_id',$planilla->id)
            ->max('viaje_evaluacion_planilla_categoria_maxs.maximo');  // Aquí obtienes el máximo valor de la columna 'maximo'*/





        $cargoMaximos = DB::table('viaje_evaluacion_planillas')
            ->leftJoin('viaje_evaluacion_planilla_cargo_maxs', 'viaje_evaluacion_planilla_cargo_maxs.viaje_evaluacion_planilla_id', '=', 'viaje_evaluacion_planillas.id')
            ->leftJoin('viaje_evaluacion_planilla_cargos', 'viaje_evaluacion_planilla_cargo_maxs.viaje_evaluacion_planilla_cargo_id', '=', 'viaje_evaluacion_planilla_cargos.id')
            ->where('viaje_evaluacion_planilla_cargo_maxs.viaje_evaluacion_planilla_id',$planilla->id)
            ->select('viaje_evaluacion_planilla_cargo_maxs.*', 'viaje_evaluacion_planilla_cargos.nombre as cargo_nombre', 'viaje_evaluacion_planilla_cargos.id as cargo_id')
            ->get();


        $maxCargo = DB::table('viaje_evaluacion_planillas')
            ->leftJoin('viaje_evaluacion_planilla_cargo_maxs', 'viaje_evaluacion_planilla_cargo_maxs.viaje_evaluacion_planilla_id', '=', 'viaje_evaluacion_planillas.id')

            ->where('viaje_evaluacion_planilla_cargo_maxs.viaje_evaluacion_planilla_id',$planilla->id)
            ->max('viaje_evaluacion_planilla_cargo_maxs.maximo');  // Aquí obtienes el máximo valor de la columna 'maximo'*/


        $itemMaximos = DB::table('viaje_evaluacion_planillas')
            ->leftJoin('viaje_evaluacion_planilla_item_maxs', 'viaje_evaluacion_planilla_item_maxs.viaje_evaluacion_planilla_id', '=', 'viaje_evaluacion_planillas.id')
            ->leftJoin('viaje_evaluacion_planilla_items', 'viaje_evaluacion_planilla_item_maxs.viaje_evaluacion_planilla_item_id', '=', 'viaje_evaluacion_planilla_items.id')
            ->leftJoin('evaluacion_grupos', 'viaje_evaluacion_planilla_item_maxs.evaluacion_grupo_id', '=', 'evaluacion_grupos.id')
            ->leftJoin('evaluacion_grupos as padre', 'evaluacion_grupos.padre_id', '=', 'padre.id')
            ->where('viaje_evaluacion_planilla_item_maxs.viaje_evaluacion_planilla_id',$planilla->id)

            ->select('viaje_evaluacion_planilla_item_maxs.*', 'viaje_evaluacion_planilla_items.nombre as item_nombre', 'evaluacion_grupos.maximo as grupo_maximo', 'evaluacion_grupos.nombre as grupo_nombre', 'padre.nombre as padre_nombre','padre.id as padre_id','padre.maximo as padre_maximo')
            ->orderBy('viaje_evaluacion_planilla_items.orden', 'ASC')
            ->get();



        $planMaximos = DB::table('viaje_evaluacion_planillas')
            ->leftJoin('viaje_evaluacion_planilla_plan_maxs', 'viaje_evaluacion_planilla_plan_maxs.viaje_evaluacion_planilla_id', '=', 'viaje_evaluacion_planillas.id')


            ->where('viaje_evaluacion_planilla_plan_maxs.viaje_evaluacion_planilla_id',$planilla->id)

            ->select('viaje_evaluacion_planilla_plan_maxs.*')

            ->get();


        //dd($planMaximos);


        $eventoMaximos = DB::table('viaje_evaluacion_planillas')
            ->leftJoin('viaje_evaluacion_planilla_evento_maxs', 'viaje_evaluacion_planilla_evento_maxs.viaje_evaluacion_planilla_id', '=', 'viaje_evaluacion_planillas.id')
            ->leftJoin('viaje_evaluacion_planilla_eventos', 'viaje_evaluacion_planilla_evento_maxs.viaje_evaluacion_planilla_evento_id', '=', 'viaje_evaluacion_planilla_eventos.id')
            ->leftJoin('evaluacion_grupos', 'viaje_evaluacion_planilla_evento_maxs.evaluacion_grupo_id', '=', 'evaluacion_grupos.id')
            ->leftJoin('evaluacion_grupos as padre', 'evaluacion_grupos.padre_id', '=', 'padre.id')
            ->where('viaje_evaluacion_planilla_evento_maxs.viaje_evaluacion_planilla_id',$planilla->id)

            ->select('viaje_evaluacion_planilla_evento_maxs.*', 'viaje_evaluacion_planilla_eventos.nombre as evento_nombre', 'evaluacion_grupos.maximo as grupo_maximo', 'evaluacion_grupos.nombre as grupo_nombre', 'padre.nombre as padre_nombre','padre.id as padre_id','padre.maximo as padre_maximo')
            ->orderBy('viaje_evaluacion_planilla_eventos.orden', 'ASC')
            ->get();

        //dd($eventoMaximos);


        $unidadAprobada = DB::table('viaje_evaluacion_unidad_aprobadas')
            ->where(function($query) use ($viaje) {
                $query->where('unidad_id', $viaje->unidad_id)
                    ->orWhere('unidad_id', $viaje->unidadcarrera_id)
                    ->orWhere('unidad_id', $viaje->unidadbeca_id);
            })
            ->exists();


        return view('viaje_evaluacions.evaluar',compact('viaje','planilla','evaluacion','facultad','planilla','categoriaMaximos','maxCategoria','categoria_id','cargoMaximos','maxCargo','unidadAprobada','itemMaximos','planMaximos','eventoMaximos'));

    }

    public function saveEvaluar(Request $request, $id)
    {
        //dd($request);
        $input = $this->sanitizeInput($request->all());

        $evaluacion = ViajeEvaluacion::findOrFail($id);


        DB::beginTransaction();
        try {

            $input['fecha']=Carbon::now();
            $evaluacion->update($input);

            $evaluacion->puntaje_categorias()->delete();


            $categoriarMaximo = explode('-',trim($request->viaje_planilla_categoria_max_id));

            if ($categoriarMaximo) {
                DB::table('viaje_evaluacion_puntaje_categorias')->insert([
                    'viaje_evaluacion_id' => $id, // Supongo que tienes un objeto $investigador disponible
                    'viaje_evaluacion_planilla_id' => $request->viaje_evaluacion_planilla_id,
                    'viaje_evaluacion_planilla_categoria_max_id' => $categoriarMaximo[0],

                    'created_at' => now(), // Establece la fecha y hora de creación
                    'updated_at' => now(), // Establece la fecha y hora de actualización
                ]);
            }

            $evaluacion->puntaje_cargos()->delete();


            $cargorMaximo = explode('-',trim($request->cargomaximo));

            if ($cargorMaximo) {
                DB::table('viaje_evaluacion_puntaje_cargos')->insert([
                    'viaje_evaluacion_id' => $id, // Supongo que tienes un objeto $investigador disponible
                    'viaje_evaluacion_planilla_id' => $request->viaje_evaluacion_planilla_id,
                    'viaje_evaluacion_planilla_cargo_max_id' => $cargorMaximo[0],

                    'created_at' => now(), // Establece la fecha y hora de creación
                    'updated_at' => now(), // Establece la fecha y hora de actualización
                ]);
            }

            $evaluacion->puntaje_items()->delete();
            $iterador1 = $request->iterador1;
            $iterador2 = $request->iterador2;

            //$cantItem = $iterador1+($iterador2-$iterador1);
            $cantItem =$iterador2;
            //dd($cantItem);
            for ($i = 0; $i < $cantItem; $i++) {
                $inputName = 'id_item' . $i;
                if ($request->has($inputName)) {
                    $id_item = $request->input($inputName);
                }
                $puntaje = NULL;
                $inputName = 'puntajeitem' . $i;
                if ($request->has($inputName)) {
                    $puntaje = $request->input($inputName);
                }

                if ($id_item && $puntaje) {
                    DB::table('viaje_evaluacion_puntaje_items')->insert([
                        'viaje_evaluacion_id' => $id, // Supongo que tienes un objeto $investigador disponible
                        'viaje_evaluacion_planilla_id' => $request->viaje_evaluacion_planilla_id,
                        'viaje_evaluacion_planilla_item_max_id' => $id_item,
                        'puntaje' => $puntaje,
                        'created_at' => now(), // Establece la fecha y hora de creación
                        'updated_at' => now(), // Establece la fecha y hora de actualización
                    ]);
                }
            }



            $evaluacion->puntaje_eventos()->delete();
            $cantEventos = $request->canteventos;
            //dd($cantEventos);
            for ($i = 0; $i < $cantEventos; $i++) {
                $inputName = 'id_evento' . $i;
                if ($request->has($inputName)) {
                    $id_evento = $request->input($inputName);
                }
                $puntaje = NULL;
                $inputName = 'puntajeevento' . $i;
                if ($request->has($inputName)) {
                    $puntaje = $request->input($inputName);
                }

                $justificacion=NULL;
                $inputName = 'justificacionevento' . $i;
                if ($request->has($inputName)) {
                    $justificacion = $request->input($inputName);
                }



                if ($id_evento && $puntaje) {
                    DB::table('viaje_evaluacion_puntaje_eventos')->insert([
                        'viaje_evaluacion_id' => $id, // Supongo que tienes un objeto $investigador disponible
                        'viaje_evaluacion_planilla_id' => $request->viaje_evaluacion_planilla_id,
                        'viaje_evaluacion_planilla_evento_max_id' => $id_evento,
                        'puntaje' => $puntaje,
                        'justificacion' => $justificacion,
                        'created_at' => now(), // Establece la fecha y hora de creación
                        'updated_at' => now(), // Establece la fecha y hora de actualización
                    ]);
                }
            }

            $evaluacion->puntaje_plans()->delete();
            $cantPlans = $request->cantplans;
            //dd($cantPlans);
            for ($i = 0; $i < $cantPlans; $i++) {
                $inputName = 'id_plan' . $i;
                if ($request->has($inputName)) {
                    $id_plan = $request->input($inputName);
                }
                $puntaje = NULL;
                $inputName = 'puntajeplan' . $i;
                if ($request->has($inputName)) {
                    $puntaje = $request->input($inputName);
                }

                $justificacion=NULL;
                $inputName = 'justificacionplan' . $i;
                if ($request->has($inputName)) {
                    $justificacion = $request->input($inputName);
                }



                if ($id_plan && $puntaje) {
                    DB::table('viaje_evaluacion_puntaje_plans')->insert([
                        'viaje_evaluacion_id' => $id, // Supongo que tienes un objeto $investigador disponible
                        'viaje_evaluacion_planilla_id' => $request->viaje_evaluacion_planilla_id,
                        'viaje_evaluacion_planilla_plan_max_id' => $id_plan,
                        'puntaje' => $puntaje,
                        'justificacion' => $justificacion,
                        'created_at' => now(), // Establece la fecha y hora de creación
                        'updated_at' => now(), // Establece la fecha y hora de actualización
                    ]);
                }
            }


            DB::commit();
            $respuestaID = 'success';
            $respuestaMSJ = 'Evaluación guardada con éxito';

        } catch (QueryException $ex) {
            // Manejar la excepción de la base de datos
            DB::rollback();
            if ($ex->errorInfo[1] == 1062) {
                $respuestaID = 'error';
                $respuestaMSJ = 'El/la integrante ya forma parte del proyecto.';
            } else {
                $respuestaID = 'error';
                $respuestaMSJ = $ex->getMessage();
            }
        } catch (\Exception $ex) {
            // Manejar cualquier otra excepción
            DB::rollback();
            $respuestaID = 'error';
            $respuestaMSJ = $ex->getMessage(); // Obtener el mensaje de error de la excepción
        }




        return redirect()->route('viajes.index')->with($respuestaID, $respuestaMSJ);
    }


    public function aceptar( $id)
    {


        $viaje = Viaje::findOrFail($id);

        $user = auth()->user();
        $evaluacion = $viaje->evaluacions()->where('user_id', $user->id)->first(); // Evaluación del usuario
        DB::beginTransaction();
        try {

            $evaluacion->estado = 'En evaluacion';
            $evaluacion->save();

            $this->cambiarEstado($evaluacion,'Evaluación aceptada');

            $year = $viaje->periodo->nombre;




            $datosCorreo = [
                'from_email' => $user->email,
                'from_name' => $user->name,
                'asunto' => 'Aceptación de Evaluación de Subsidios para Viajes y/o Estadías ('.Constants::MES_DESDE_VIAJES.' '.$year.' - '.Constants::MES_HASTA_VIAJES.' '.(intval($year)+1).')',
                'year' => $year,

                'mes_desde' => Constants::MES_DESDE_VIAJES,
                'mes_hasta' => Constants::MES_HASTA_VIAJES,

                'fecha' => Carbon::parse($evaluacion->fecha)->format('d/m/Y'), // Formatear la fecha en el formato deseado
                'motivo' => $viaje->motivo,

                'investigador' => $viaje->investigador->persona->apellido.', '.$viaje->investigador->persona->nombre.' ('.$viaje->investigador->persona->cuil.')',
                'comment' => 'La solicitud fue admitida para su evaluación',
            ];


            // Llama a la función enviarCorreos
            $this->enviarCorreo($datosCorreo, $viaje);


            DB::commit();
            $respuestaID = 'success';
            $respuestaMSJ = 'Evaluación aceptada con éxito';

        } catch (QueryException $ex) {
            // Manejar la excepción de la base de datos
            DB::rollback();
            if ($ex->errorInfo[1] == 1062) {
                $respuestaID = 'error';
                $respuestaMSJ = 'El/la integrante ya forma parte del proyecto.';
            } else {
                $respuestaID = 'error';
                $respuestaMSJ = $ex->getMessage();
            }
        } catch (\Exception $ex) {
            // Manejar cualquier otra excepción
            DB::rollback();
            $respuestaID = 'error';
            $respuestaMSJ = $ex->getMessage(); // Obtener el mensaje de error de la excepción
        }





        /*return redirect()->route('investigadors.index')
            ->with('success','Investigador eliminado con éxito');*/
        return redirect()->route('viajes.index')->with($respuestaID, $respuestaMSJ);
    }


    public function rechazar($id)
    {
        $viaje = Viaje::find($id);




        return view('viaje_evaluacions.deny',compact('viaje'));

    }

    public function saveDeny(Request $request, $id)
    {
        $this->validate($request, [
            'comentarios' => 'required'
        ]);

        $input = $this->sanitizeInput($request->all());

        $viaje = Viaje::findOrFail($id);

        $user = auth()->user();
        $evaluacion = $viaje->evaluacions()->where('user_id', $user->id)->first(); // Evaluación del usuario
        DB::beginTransaction();
        try {

            $evaluacion->estado = 'Rechazada';
            $evaluacion->save();

            $this->cambiarEstado($evaluacion,$input['comentarios']);

            $year = $viaje->periodo->nombre;



            $datosCorreo = [
                'from_email' => $user->email,
                'from_name' => $user->name,
                'asunto' => 'Rechazo de Evaluación de Subsidios para Viajes y/o Estadías ('.Constants::MES_DESDE_VIAJES.' '.$year.' - '.Constants::MES_HASTA_VIAJES.' '.(intval($year)+1).')',
                'year' => $year,

                'mes_desde' => Constants::MES_DESDE_VIAJES,
                'mes_hasta' => Constants::MES_HASTA_VIAJES,

                'fecha' => Carbon::parse($evaluacion->fecha)->format('d/m/Y'), // Formatear la fecha en el formato deseado
                'motivo' => $viaje->motivo,

                'investigador' => $viaje->investigador->persona->apellido.', '.$viaje->investigador->persona->nombre.' ('.$viaje->investigador->persona->cuil.')',
                'comment' => '<strong>Motivos del rechazo</strong>: '.$input['comentarios'],
            ];


            // Llama a la función enviarCorreos
            $this->enviarCorreo($datosCorreo, $viaje);


            DB::commit();
            $respuestaID = 'success';
            $respuestaMSJ = 'Evaluación rechazada con éxito';

        } catch (QueryException $ex) {
            // Manejar la excepción de la base de datos
            DB::rollback();
            if ($ex->errorInfo[1] == 1062) {
                $respuestaID = 'error';
                $respuestaMSJ = 'El/la integrante ya forma parte del proyecto.';
            } else {
                $respuestaID = 'error';
                $respuestaMSJ = $ex->getMessage();
            }
        } catch (\Exception $ex) {
            // Manejar cualquier otra excepción
            DB::rollback();
            $respuestaID = 'error';
            $respuestaMSJ = $ex->getMessage(); // Obtener el mensaje de error de la excepción
        }





        /*return redirect()->route('investigadors.index')
            ->with('success','Investigador eliminado con éxito');*/
        return redirect()->route('viajes.index')->with($respuestaID, $respuestaMSJ);
    }







    public function enviarCorreo($datosCorreo, $viaje)
    {
        // Enviar correo electrónico a tu servidor (ejemplo)
        Mail::to(Constants::MAIL_VIAJES)->send(new ViajeEnviada($datosCorreo,$viaje));
    }

    public function enviarCorreoEvaluacion($datosCorreo, $viaje,$pdf)
    {
        // Enviar correo electrónico a tu servidor (ejemplo)
        Mail::to(Constants::MAIL_VIAJES)->send(new ViajeEnviada($datosCorreo,$viaje,false,$pdf));
    }

    public function enviarCorreoEvaluador($datosCorreo, $user, $viaje)
    {

        $viajeController = new ViajeController();
        // Generar el PDF y obtener la ruta
        $pdfPath = $viajeController->generatePDF(new Request(['viaje_id' => $viaje->id]), true);


        // Enviar correo electrónico al usuario
        Mail::to($user->email)->send(new EvaluadorViajeEnviada($datosCorreo, $viaje, true, $pdfPath));


    }

    public function actualizar($id)
    {
        $viaje = Viaje::find($id);


        $error='';
        $ok = 1;

        $errores = [];

        if ($viaje->estado != 'En evaluación'){
            $errores[] = 'La solicitud ya fue evaluada';
        }

        if (!empty($errores)) {
            return redirect()->back()->withErrors($errores)->withInput();
        }

        DB::beginTransaction();


        if ($ok){
            try {




                $evaluaciones = $viaje->evaluacions()->where('estado', 'Evaluada')->get();

                if ($evaluaciones->count() === 1) {
                    // Si hay una sola evaluación
                    $evaluacion = $evaluaciones->first();
                    $puntaje = $evaluacion->puntaje;
                    $diferencia = 0;
                } elseif ($evaluaciones->count() === 2) {
                    // Si hay dos evaluaciones
                    $puntaje1 = $evaluaciones[0]->puntaje;
                    $puntaje2 = $evaluaciones[1]->puntaje;

                    $puntaje = ($puntaje1 + $puntaje2) / 2;
                    $diferencia = abs($puntaje1 - $puntaje2);
                } elseif ($evaluaciones->count() > 2) {
                    // Si hay más de dos evaluaciones, necesitamos quedarnos con las dos con menor diferencia
                    $sortedEvaluaciones = $evaluaciones->sortBy('puntaje')->values();

                    $minDiff = PHP_INT_MAX;
                    $e1 = null;
                    $e2 = null;

                    // Iterar sobre todas las combinaciones de evaluaciones para encontrar la menor diferencia
                    for ($i = 0; $i < $sortedEvaluaciones->count() - 1; $i++) {
                        for ($j = $i + 1; $j < $sortedEvaluaciones->count(); $j++) {
                            $puntaje1 = $sortedEvaluaciones[$i]->puntaje;
                            $puntaje2 = $sortedEvaluaciones[$j]->puntaje;

                            $diff = abs($puntaje1 - $puntaje2);
                            if ($diff < $minDiff) {
                                $minDiff = $diff;
                                $e1 = $sortedEvaluaciones[$i];
                                $e2 = $sortedEvaluaciones[$j];
                            }
                        }
                    }

                    // Calcular puntaje como el promedio de los dos puntajes con menor diferencia
                    $puntaje = ($e1->puntaje + $e2->puntaje) / 2;
                    $diferencia = $minDiff;
                } else {
                    // Si no hay evaluaciones en estado "Evaluada", no hacer nada o manejar el caso de forma adecuada
                    $puntaje = null;
                    $diferencia = null;
                }

// Actualizar los campos en el modelo $viaje
                $viaje->puntaje = $puntaje;
                $viaje->diferencia = $diferencia;
                $viaje->estado = "Evaluada";
                $viaje->save();




                $viajeController = new ViajeController();
                $viajeController->cambiarEstado($viaje,'Actualizar puntaje');


                DB::commit();

                $respuestaID = 'success';
                $respuestaMSJ = 'Actualizada con éxito';

            } catch (QueryException $ex) {
                // Manejar la excepción de la base de datos
                DB::rollback();
                if ($ex->errorInfo[1] == 1062) {
                    $respuestaID = 'error';
                    $respuestaMSJ = 'El/la integrante ya forma parte del proyecto.';
                } else {
                    $respuestaID = 'error';
                    $respuestaMSJ = $ex->getMessage();
                }
            } catch (\Exception $ex) {
                // Manejar cualquier otra excepción
                DB::rollback();
                $respuestaID = 'error';
                $respuestaMSJ = $ex->getMessage(); // Obtener el mensaje de error de la excepción
            }
        }
        else{
            DB::rollback();
            $respuestaID='error';
            $respuestaMSJ=$error;
        }


        return redirect()->route('viaje_evaluacions.index', array('viaje_id' => $id))->with($respuestaID, $respuestaMSJ);
    }
    public function send($id)
    {
        $viaje = Viaje::find($id);
        $user = auth()->user();
        $evaluacion = $viaje->evaluacions()->where('user_id', $user->id)->first(); // Evaluación del usuario

        $error='';
        $ok = 1;

        $errores = [];

        if (!$evaluacion->puntaje){
            $errores[] = 'Aún no ha cargado puntajes';
        }

        $eventoPuntajes = $evaluacion->puntaje_eventos()->get();

        foreach ($eventoPuntajes as $eventoPuntaje){

            $eventoMaximo = DB::table('viaje_evaluacion_planilla_evento_maxs')
                ->leftJoin('viaje_evaluacion_planilla_eventos', 'viaje_evaluacion_planilla_evento_maxs.viaje_evaluacion_planilla_evento_id', '=', 'viaje_evaluacion_planilla_eventos.id')
                ->where('viaje_evaluacion_planilla_evento_maxs.id',$eventoPuntaje->viaje_evaluacion_planilla_evento_max_id)
                ->select('viaje_evaluacion_planilla_evento_maxs.*', 'viaje_evaluacion_planilla_eventos.nombre as evento_nombre')
                ->first();

            if ($eventoMaximo->minimo==0){
                if($eventoPuntaje->puntaje && !$eventoPuntaje->justificacion)
                $errores[] = 'Falta cargar la justificación del ítem: '.$eventoMaximo->evento_nombre;
            }

        }

        if (!empty($errores)) {
            return redirect()->back()->withErrors($errores)->withInput();
        }

        DB::beginTransaction();


        if ($ok){
            try {

                $evaluacion->estado = 'Evaluada';
                $evaluacion->save();

                $this->cambiarEstado($evaluacion,'Envío de evaluación');



                $year = $viaje->periodo->nombre;


                $datosCorreo = [
                    'from_email' => $user->email,
                    'from_name' => $user->name,
                    'asunto' => 'Evaluación de Subsidios para Viajes y/o Estadías ('.Constants::MES_DESDE_VIAJES.' '.$year.' - '.Constants::MES_HASTA_VIAJES.' '.(intval($year)+1).')',
                    'year' => $year,

                    'mes_desde' => Constants::MES_DESDE_VIAJES,
                    'mes_hasta' => Constants::MES_HASTA_VIAJES,

                    'fecha' => Carbon::parse($evaluacion->fecha)->format('d/m/Y'), // Formatear la fecha en el formato deseado
                    'motivo' => $viaje->motivo,

                    'investigador' => $viaje->investigador->persona->apellido.', '.$viaje->investigador->persona->nombre.' ('.$viaje->investigador->persona->cuil.')',
                    'comment' => '',
                ];

                // Generar el PDF y obtener la ruta
                $pdfPath = $this->generatePDF(new Request(['viaje_evaluacion_id' => $evaluacion->id]), true);

                $this->enviarCorreoEvaluacion($datosCorreo,$viaje,$pdfPath);

                $evaluadasTodas = !$viaje->evaluacions()->where('estado', '!=', 'Evaluada')->exists();

                if ($evaluadasTodas){
                    $evaluaciones = $viaje->evaluacions()->where('estado', 'Evaluada')->get();

                    if ($evaluaciones->count() === 1) {
                        // Si hay una sola evaluación
                        $evaluacion = $evaluaciones->first();
                        $puntaje = $evaluacion->puntaje;
                        $diferencia = 0;
                    } elseif ($evaluaciones->count() === 2) {
                        // Si hay dos evaluaciones
                        $puntaje1 = $evaluaciones[0]->puntaje;
                        $puntaje2 = $evaluaciones[1]->puntaje;

                        $puntaje = ($puntaje1 + $puntaje2) / 2;
                        $diferencia = abs($puntaje1 - $puntaje2);
                    } elseif ($evaluaciones->count() > 2) {
                        // Si hay más de dos evaluaciones, necesitamos quedarnos con las dos con menor diferencia
                        $sortedEvaluaciones = $evaluaciones->sortBy('puntaje')->values();

                        $minDiff = PHP_INT_MAX;
                        $e1 = null;
                        $e2 = null;

                        // Iterar sobre todas las combinaciones de evaluaciones para encontrar la menor diferencia
                        for ($i = 0; $i < $sortedEvaluaciones->count() - 1; $i++) {
                            for ($j = $i + 1; $j < $sortedEvaluaciones->count(); $j++) {
                                $puntaje1 = $sortedEvaluaciones[$i]->puntaje;
                                $puntaje2 = $sortedEvaluaciones[$j]->puntaje;

                                $diff = abs($puntaje1 - $puntaje2);
                                if ($diff < $minDiff) {
                                    $minDiff = $diff;
                                    $e1 = $sortedEvaluaciones[$i];
                                    $e2 = $sortedEvaluaciones[$j];
                                }
                            }
                        }

                        // Calcular puntaje como el promedio de los dos puntajes con menor diferencia
                        $puntaje = ($e1->puntaje + $e2->puntaje) / 2;
                        $diferencia = $minDiff;
                    } else {
                        // Si no hay evaluaciones en estado "Evaluada", no hacer nada o manejar el caso de forma adecuada
                        $puntaje = null;
                        $diferencia = null;
                    }

// Actualizar los campos en el modelo $viaje
                    $viaje->puntaje = $puntaje;
                    $viaje->diferencia = $diferencia;
                    $viaje->estado = "Evaluada";
                    $viaje->save();




                    $viajeController = new ViajeController();
                    $viajeController->cambiarEstado($viaje,'Ultima evaluacion enviada');
                }

                DB::commit();
                // Eliminar el archivo PDF temporal
                if (file_exists($pdfPath)) {
                    unlink($pdfPath);
                }
                $respuestaID = 'success';
                $respuestaMSJ = 'Evaluación enviada con éxito';

            } catch (QueryException $ex) {
                // Manejar la excepción de la base de datos
                DB::rollback();
                if ($ex->errorInfo[1] == 1062) {
                    $respuestaID = 'error';
                    $respuestaMSJ = 'El/la integrante ya forma parte del proyecto.';
                } else {
                    $respuestaID = 'error';
                    $respuestaMSJ = $ex->getMessage();
                }
            } catch (\Exception $ex) {
                // Manejar cualquier otra excepción
                DB::rollback();
                $respuestaID = 'error';
                $respuestaMSJ = $ex->getMessage(); // Obtener el mensaje de error de la excepción
            }
        }
        else{
            DB::rollback();
            $respuestaID='error';
            $respuestaMSJ=$error;
        }

        return redirect()->route('viajes.index')->with($respuestaID, $respuestaMSJ);
    }

    public function enviar($viaje_id)
    {
        // Buscar el viaje por ID
        $viaje = Viaje::findOrFail($viaje_id);
        $year = $viaje->periodo->nombre;
        $error='';
        $success='';
        $ok=1;
        foreach ($viaje->evaluacions->where('estado', 'Creada') as $evaluacion){
            $evaluacion->estado='Recibida';
            $evaluacion->fecha=Carbon::now();
            try {

                $evaluacion->update();
                $this->cambiarEstado($evaluacion,'Envío a evaluador');
                $user = User::find($evaluacion->user_id);
                // Preparar datos para el correo
                $roleName = Role::find(Constants::ID_COORDINADOR)->name;

                // Obtener el CAT del viaje
                $cat = DB::table('facultads')->where('id', $viaje->facultadplanilla_id)->value('cat');

// Obtener los usuarios coordinadores de la misma CAT
                $coordinadors = User::join('facultads', 'users.facultad_id', '=', 'facultads.id')
                    ->where('facultads.cat', $cat)
                    ->role($roleName)
                    ->select('users.*', 'facultads.nombre as facultad_nombre') // Incluye el nombre de la facultad
                    ->get();

                $coordinadores='';

                // Enviar correo electrónico a cada usuario
                foreach ($coordinadors as $coordinador) {

                    $coordinadores .=$coordinador->name. ' ('.$coordinador->facultad_nombre .') '.$coordinador->email.'<br><br>';
                }
                $tipo = ($evaluacion->interno)?'INTERNO':'EXTERNO';
                $datosCorreo = [
                    'from_email' => Constants::MAIL_VIAJES,
                    'from_name' => Constants::NOMBRE_VIAJES,
                    'asunto' => 'Evaluación de Subsidios para Viajes y/o Estadías ('.Constants::MES_DESDE_VIAJES.' '.$year.' - '.Constants::MES_HASTA_VIAJES.' '.(intval($year)+1).')',
                    'year' => $year,
                    'tipo' => $tipo,
                    'mes_desde' => Constants::MES_DESDE_VIAJES,
                    'mes_hasta' => Constants::MES_HASTA_VIAJES,
                    'postulante' => $viaje->investigador->persona->apellido.', '.$viaje->investigador->persona->nombre.' ('.$viaje->investigador->persona->cuil.')',
                    'fecha' => Carbon::parse($evaluacion->fecha)->format('d/m/Y'), // Formatear la fecha en el formato deseado
                    'motivo' => $viaje->motivo,
                    'evaluador' => $user->name,
                    'urlWeb' => url('/'),
                    'urlInstructivo' => '',
                    'coordinadores' => $coordinadores,
                ];


                // Llama a la función enviarCorreos
                $this->enviarCorreoEvaluador($datosCorreo, $user,$viaje);
                $success .='Enviada al evaluador '.$user->name.'<br>';

            }catch(QueryException $ex){

                    $error = $ex->getMessage();


                $ok=0;
                continue;
            }


        }

        if ($ok){
            try {
                $viajeController = new ViajeController();
                $viaje->estado = 'En evaluación';
                $viaje->update();
                $viajeController->cambiarEstado($viaje,'Envío a evaluadores');
                DB::commit();
                $respuestaID='success';
                $respuestaMSJ=$success;
            }catch(QueryException $ex){

                $error = $ex->getMessage();


                DB::rollback();
                $respuestaID='error';
                $respuestaMSJ=$error;
            }

        }
        else{
            DB::rollback();
            $respuestaID='error';
            $respuestaMSJ=$error;
        }
        return redirect()->route('viaje_evaluacions.index', array('viaje_id' => $viaje_id))->with($respuestaID, $respuestaMSJ);
    }





}
