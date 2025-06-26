<?php

namespace App\Http\Controllers;


use App\Mail\JovenEnviada;
use App\Models\Joven;
use App\Models\JovenEvaluacion;
use App\Models\JovenEvaluacionEstado;
use App\Models\JovenEvaluacionPuntajeAntAcad;
use App\Models\JovenEvaluacionPuntajePosgrado;
use App\Models\JovenEvaluacionPuntajeCargo;
use App\Models\JovenEvaluacionPuntajeOtro;
use App\Models\JovenEvaluacionPuntajeProduccion;
use App\Models\JovenEvaluacionPuntajeAnterior;
use App\Models\JovenEvaluacionPuntajeJustificacion;
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

use App\Mail\EvaluadorJovenEnviada;

use App\Http\Controllers\JovenController;
//use Barryvdh\DomPDF\Facade as PDF;
class JovenEvaluacionController extends Controller
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
        $jovenId = $request->query('joven_id');
        $joven = null;

        // Si se proporciona un ID de joven, buscalo en la base de datos
        if ($jovenId) {
            $joven = Joven::findOrFail($jovenId);
        }
        $periodos = DB::table('periodos')->orderBy('nombre','DESC')->get();
        $currentPeriod = Constants::YEAR_JOVENES;

        // Pasar el joven (si existe) a la vista
        return view('joven_evaluacions.index', compact('joven','periodos','currentPeriod'));
    }


    public function clearFilter(Request $request)
    {
        // Limpiar el valor del filtro en la sesión
        $request->session()->forget('nombre_filtro_joven_evaluacion');
        //Log::info('Sesion limpia:', $request->session()->all());
        return response()->json(['status' => 'success']);
    }

    public function dataTable(Request $request)
    {

        $jovenId = $request->input('joven_id');
        $columnas = ['personas.nombre','periodos.nombre', 'personas.apellido', 'joven_evaluacions.fecha',DB::raw("IFNULL(users.name, joven_evaluacions.user_name)"),DB::raw("CASE WHEN joven_evaluacions.interno = 1 THEN 'SI' ELSE 'NO' END"), // Transformación para la columna interno
            'joven_evaluacions.estado','joven_evaluacions.puntaje']; // Define las columnas disponibles
        $columnaOrden = $columnas[$request->input('order.0.column')];
        $orden = $request->input('order.0.dir');


        $busqueda = $request->input('search.value');
        $periodo = $request->input('periodo'); // Obtener el filtro de período de la solicitud
        $predefinido = $request->input('predefinido');

        // Consulta base
        $query = JovenEvaluacion::select('joven_evaluacions.id as id', 'personas.nombre as persona_nombre','periodos.nombre as periodo', DB::raw("CONCAT(personas.apellido, ', ', personas.nombre) as persona_apellido"), 'joven_evaluacions.fecha',
            DB::raw("IFNULL(users.name, joven_evaluacions.user_name) as usuario_nombre"), DB::raw("CASE WHEN joven_evaluacions.interno = 1 THEN 'SI' ELSE 'NO' END AS interno"), // Transformación para la columna interno
            'joven_evaluacions.estado','joven_evaluacions.puntaje',DB::raw("(SELECT COUNT(*) FROM joven_evaluacions je2
                  WHERE je2.joven_id = joven_evaluacions.joven_id
                  AND (je2.estado = 'evaluada' OR je2.estado = 'en evaluacion')) as total_evaluaciones"),DB::raw("(SELECT COUNT(*) FROM joven_evaluacions je3
                  WHERE je3.joven_id = joven_evaluacions.joven_id
                  AND je3.estado = 'evaluada') as evaluaciones_evaluada"))
            ->leftJoin('jovens', 'joven_evaluacions.joven_id', '=', 'jovens.id')
            ->leftJoin('investigadors', 'jovens.investigador_id', '=', 'investigadors.id')
            ->leftJoin('periodos', 'jovens.periodo_id', '=', 'periodos.id')
            ->leftJoin('personas', 'investigadors.persona_id', '=', 'personas.id')
            ->leftJoin('users', 'joven_evaluacions.user_id', '=', 'users.id');


        // Aplicar filtro por joven si se proporciona el ID del joven
        if ($jovenId) {
            $query->where('joven_evaluacions.joven_id', $jovenId);
        }
        // Filtrar por período si se proporciona
        if (!empty($periodo)) {
            $query->where('jovens.periodo_id', $periodo);
        }
        if (!empty($predefinido)) {
            if ($predefinido == 2) {
                $query->having(DB::raw('total_evaluaciones'), '<', 2);
            }
            if ($predefinido == 3) {
                // Mostrar solo solicitudes con estado "En evaluacion"
                $query->where('jovens.estado', 'En evaluacion')
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
        if (!empty($busqueda)) {


            $request->session()->put('nombre_filtro_joven_evaluacion', $busqueda);

        }
        else{
            $busqueda = $request->session()->get('nombre_filtro_joven_evaluacion');

        }
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
                $query->where('joven_evaluacions.user_cuil', '=', $user->cuil)
                    ->orWhere('joven_evaluacions.user_id', '=', $user->id);
            });
        }

        if ($selectedRoleId==4){
            $user = auth()->user();
            //$currentDate = date('Y-m-d');

            $query->where(function ($query) use ($user) {
                $query->where('jovens.facultadplanilla_id', '=', $user->facultad_id);
            });
        }


        // Obtener la cantidad total de registros después de aplicar el filtro de búsqueda
        $recordsFiltered = $query->count();

        // Obtener solo los elementos paginados
        $datos = $query->orderBy($columnaOrden, $orden)
            ->skip($request->input('start'))
            ->take($request->input('length'))
            ->get();

        // Obtener la cantidad total de registros sin filtrar
        $recordsTotal = JovenEvaluacion::count();

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


        $joven_id=$request->joven_id;
        $joven = Joven::findOrFail($joven_id);
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
                $periodo = $joven->periodo_id; // Cambia 'periodo' por el nombre de la relación en tu modelo
                $participacionExistente = Joven::where('periodo_id', $periodo)
                    ->whereHas('investigador', function($query) use ($cuilUsuario) {
                        $query->whereHas('persona', function($query) use ($cuilUsuario) {
                            $query->where('cuil', $cuilUsuario);
                        });
                    })
                    ->where(function($query) {
                        $query->where('estado', '!=', 'Creada')
                            ->where('estado', '!=', 'Retirada');
                    })
                    ->where('id', '!=', $joven->id) // Excluir el joven actual de la verificación
                    ->exists();
                if ($participacionExistente) {

                    $error = "El evaluador: ".$user->name." presentó solicitud";
                }

                $data2=array(
                    'joven_id'=>$joven_id,
                    'user_id'=>$user_id,
                    'interno'=>1,
                    'estado'=>'Creada',
                    'fecha'=>Carbon::now()
                );
                try {
                    if (!empty($request->internos_id[$item])){
                        $data2['id']=$request->internos_id[$item];
                        $evaluacion=JovenEvaluacion::find($request->internos_id[$item]);
                        //$alineacion->update($data2);
                    }
                    else{
                        $evaluacion = JovenEvaluacion::create($data2);
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
                $periodo = $joven->periodo_id; // Cambia 'periodo' por el nombre de la relación en tu modelo
                $participacionExistente = Joven::where('periodo_id', $periodo)
                    ->whereHas('investigador', function($query) use ($cuilUsuario) {
                        $query->whereHas('persona', function($query) use ($cuilUsuario) {
                            $query->where('cuil', $cuilUsuario);
                        });
                    })
                    ->where(function($query) {
                        $query->where('estado', '!=', 'Creada')
                            ->where('estado', '!=', 'Retirada');
                    })
                    ->where('id', '!=', $joven->id) // Excluir el joven actual de la verificación
                    ->exists();
                if ($participacionExistente) {

                    $error = "El evaluador: ".$user->name." presentó solicitud";
                }

                $data2=array(
                    'joven_id'=>$joven_id,
                    'user_id'=>$user_id,
                    'interno'=>0,
                    'estado'=>'Creada',
                    'fecha'=>Carbon::now()
                );
                try {
                    if (!empty($request->externos_id[$item])){
                        $data2['id']=$request->externos_id[$item];
                        $evaluacion=JovenEvaluacion::find($request->externos_id[$item]);
                        //$alineacion->update($data2);
                    }
                    else{
                        $evaluacion = JovenEvaluacion::create($data2);
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
            JovenEvaluacion::where('joven_id',"$joven_id")->whereNotIn('id', explode(',', $noBorrar))->delete();

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
        return redirect()->route('joven_evaluacions.index', array('joven_id' => $joven_id))->with($respuestaID, $respuestaMSJ);

    }

    public function cambiarEstado($joven_evaluacion, $comentarios)
    {

        // Actualizar el registro de estado existente donde 'hasta' es null
        JovenEvaluacionEstado::where('joven_evaluacion_id', $joven_evaluacion->id)
            ->whereNull('hasta')
            ->update(['hasta' => Carbon::now()]);

        // Obtener el ID del usuario autenticado
        $userId = Auth::id();

        // Crear registro en integrante_estados con el estado "Alta Creada" y el user_id
        $joven_evaluacion->estados()->create([
            'estado' => $joven_evaluacion->estado,
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
        $jovenId = $request->input('joven_id');
        $joven = null;
        // Si se proporciona un ID de joven, buscalo en la base de datos
        if ($jovenId) {
            $joven = Joven::findOrFail($jovenId);

            if (($joven->estado!='Admitida')&&($joven->estado!='En evaluación')) {

                return redirect()->route('joven_evaluacions.index', ['joven_id' => $jovenId])
                    ->withErrors(['message' => 'La solicitud debe estar con estado ADMITIDA o EN EVALUACION para que se le puedan asignar evaluadores']);
            }
        }


        $periodoId = $joven->periodo_id;

// Obtener los evaluadores que pertenezcan a la misma facultad y no hayan participado en la misma convocatoria
        $internos = User::whereHas('roles', function ($query) {
            $query->where('name', 'Evaluador');
        })
            ->where('facultad_id', $joven->facultadplanilla_id) // Filtrar por la facultad del joven
            ->where(function ($query) use ($periodoId) {
                // Usuarios evaluadores que no participaron en la convocatoria (por cuil)
                $query->whereNotExists(function ($subQuery) use ($periodoId) {
                    $subQuery->select(DB::raw(1))
                        ->from('jovens')
                        ->join('investigadors', 'investigadors.id', '=', 'jovens.investigador_id')
                        ->join('personas', 'personas.id', '=', 'investigadors.persona_id')
                        ->whereColumn('personas.cuil', 'users.cuil') // Comparar por cuil
                        ->where('jovens.periodo_id', $periodoId);
                })
                    // O si participaron, su estado debe ser "Creada" o "Retirada" (por cuil)
                    ->orWhereExists(function ($subQuery) use ($periodoId) {
                        $subQuery->select(DB::raw(1))
                            ->from('jovens')
                            ->join('investigadors', 'investigadors.id', '=', 'jovens.investigador_id')
                            ->join('personas', 'personas.id', '=', 'investigadors.persona_id')
                            ->whereColumn('personas.cuil', 'users.cuil') // Comparar por cuil
                            ->where('jovens.periodo_id', $periodoId)
                            ->where(function ($query) {
                                $query->where('jovens.estado', 'Creada')
                                    ->orWhere('jovens.estado', 'Retirada');
                            });
                    });
            })
            ->get();



        $internos = $internos->pluck('name', 'id')->prepend('', '');


        $externos = User::whereHas('roles', function ($query) {
            $query->where('name', 'Evaluador');
        })
            ->where('facultad_id','!=', $joven->facultadplanilla_id) // Filtrar por distinta facultad que el joven
            ->where(function ($query) use ($periodoId) {
                // Usuarios evaluadores que no participaron en la convocatoria (por cuil)
                $query->whereNotExists(function ($subQuery) use ($periodoId) {
                    $subQuery->select(DB::raw(1))
                        ->from('jovens')
                        ->join('investigadors', 'investigadors.id', '=', 'jovens.investigador_id')
                        ->join('personas', 'personas.id', '=', 'investigadors.persona_id')
                        ->whereColumn('personas.cuil', 'users.cuil') // Comparar por cuil
                        ->where('jovens.periodo_id', $periodoId);
                })
                    // O si participaron, su estado debe ser "Creada" o "Retirada" (por cuil)
                    ->orWhereExists(function ($subQuery) use ($periodoId) {
                        $subQuery->select(DB::raw(1))
                            ->from('jovens')
                            ->join('investigadors', 'investigadors.id', '=', 'jovens.investigador_id')
                            ->join('personas', 'personas.id', '=', 'investigadors.persona_id')
                            ->whereColumn('personas.cuil', 'users.cuil') // Comparar por cuil
                            ->where('jovens.periodo_id', $periodoId)
                            ->where(function ($query) {
                                $query->where('jovens.estado', 'Creada')
                                    ->orWhere('jovens.estado', 'Retirada');
                            });
                    });
            })
            ->get();
        $externos = $externos->pluck('name', 'id')->prepend('','');

        return view('joven_evaluacions.create',compact('internos','externos','joven'));

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

        if ($request->has('joven_evaluacion_id')) {
            $evaluacionId = $request->query('joven_evaluacion_id');
            $evaluacion = JovenEvaluacion::findOrFail($evaluacionId);
            $joven = Joven::findOrFail($evaluacion->joven_id);
            // Haz algo con $evaluacionId
        } else {
            $joven = Joven::findOrFail($request->query('joven_id'));
            $user = auth()->user();
            $evaluacion = $joven->evaluacions()->where('user_id', $user->id)->first(); // Evaluación del usuario
            $evaluacionId=$evaluacion->id;
        }

        // Consulta base
        $query = JovenEvaluacion::select('joven_evaluacions.id as id','investigadors.id as investigador_id', 'periodos.nombre as periodo_nombre', DB::raw("CONCAT(personas.apellido, ', ', personas.nombre) as persona_apellido"),'personas.cuil', 'facultads.nombre as facultad_nombre', 'joven_evaluacions.estado as estado','joven_evaluacions.puntaje as puntaje',
            DB::raw("IFNULL(users.name, joven_evaluacions.user_name) as usuario_nombre"),
            DB::raw("IFNULL(users.cuil, joven_evaluacions.user_cuil) as usuario_cuil"))
            ->leftJoin('jovens', 'joven_evaluacions.joven_id', '=', 'jovens.id')
            ->leftJoin('investigadors', 'jovens.investigador_id', '=', 'investigadors.id')
            ->leftJoin('periodos', 'jovens.periodo_id', '=', 'periodos.id')
            ->leftJoin('personas', 'investigadors.persona_id', '=', 'personas.id')
            ->leftJoin('facultads', 'jovens.facultadplanilla_id', '=', 'facultads.id')
            ->leftJoin('users', 'joven_evaluacions.user_id', '=', 'users.id');



        $query->where('joven_evaluacions.id', $evaluacionId);



        // Obtener solo los elementos paginados
        $datos = $query->first();
        //dd($datos);

        // Verifica si hay puntajes
        if (!$datos->puntaje) {
            return response()->json([
                'message' => 'Aún no ha cargado ningún puntaje.',
            ], 400); // 400 Bad Request
        }
        $planilla = DB::table('joven_evaluacion_planillas')

            ->where('joven_evaluacion_planillas.periodo_id',$joven->periodo_id)
            ->first();

        $posgradoMaximos = DB::table('joven_evaluacion_planillas')
            ->leftJoin('joven_evaluacion_planilla_posgrado_maxs', 'joven_evaluacion_planilla_posgrado_maxs.joven_evaluacion_planilla_id', '=', 'joven_evaluacion_planillas.id')
            ->leftJoin('joven_evaluacion_planilla_posgrados', 'joven_evaluacion_planilla_posgrado_maxs.joven_evaluacion_planilla_posgrado_id', '=', 'joven_evaluacion_planilla_posgrados.id')
            ->where('joven_evaluacion_planillas.periodo_id',$joven->periodo_id)
            ->select('joven_evaluacion_planilla_posgrado_maxs.*', 'joven_evaluacion_planilla_posgrados.nombre as posgrado_nombre')
            ->get();

        $maxPosgrado = DB::table('joven_evaluacion_planillas')
            ->leftJoin('joven_evaluacion_planilla_posgrado_maxs', 'joven_evaluacion_planilla_posgrado_maxs.joven_evaluacion_planilla_id', '=', 'joven_evaluacion_planillas.id')

            ->where('joven_evaluacion_planillas.periodo_id', $joven->periodo_id)
            ->max('joven_evaluacion_planilla_posgrado_maxs.maximo');  // Aquí obtienes el máximo valor de la columna 'maximo'

        $antAcadMaximos = DB::table('joven_evaluacion_planillas')
            ->leftJoin('joven_evaluacion_planilla_ant_acad_maxs', 'joven_evaluacion_planilla_ant_acad_maxs.joven_evaluacion_planilla_id', '=', 'joven_evaluacion_planillas.id')
            ->leftJoin('joven_evaluacion_planilla_ant_acads', 'joven_evaluacion_planilla_ant_acad_maxs.joven_evaluacion_planilla_ant_acad_id', '=', 'joven_evaluacion_planilla_ant_acads.id')
            ->leftJoin('evaluacion_grupos', 'joven_evaluacion_planilla_ant_acad_maxs.evaluacion_grupo_id', '=', 'evaluacion_grupos.id')
            ->where('joven_evaluacion_planillas.periodo_id',$joven->periodo_id)

            ->select('joven_evaluacion_planilla_ant_acad_maxs.*', 'joven_evaluacion_planilla_ant_acads.nombre as ant_acad_nombre', 'evaluacion_grupos.maximo as grupo_maximo', 'evaluacion_grupos.nombre as grupo_nombre')
            ->get();

        $cargoMaximos = DB::table('joven_evaluacion_planillas')
            ->leftJoin('joven_evaluacion_planilla_cargo_maxs', 'joven_evaluacion_planilla_cargo_maxs.joven_evaluacion_planilla_id', '=', 'joven_evaluacion_planillas.id')
            ->leftJoin('joven_evaluacion_planilla_cargos', 'joven_evaluacion_planilla_cargo_maxs.joven_evaluacion_planilla_cargo_id', '=', 'joven_evaluacion_planilla_cargos.id')
            ->where('joven_evaluacion_planillas.periodo_id',$joven->periodo_id)
            ->select('joven_evaluacion_planilla_cargo_maxs.*', 'joven_evaluacion_planilla_cargos.nombre as cargo_nombre', 'joven_evaluacion_planilla_cargos.id as cargo_id')
            ->get();

        $maxCargo = DB::table('joven_evaluacion_planillas')
            ->leftJoin('joven_evaluacion_planilla_cargo_maxs', 'joven_evaluacion_planilla_cargo_maxs.joven_evaluacion_planilla_id', '=', 'joven_evaluacion_planillas.id')

            ->where('joven_evaluacion_planillas.periodo_id', $joven->periodo_id)
            ->max('joven_evaluacion_planilla_cargo_maxs.maximo');  // Aquí obtienes el máximo valor de la columna 'maximo'

        $otroMaximos = DB::table('joven_evaluacion_planillas')
            ->leftJoin('joven_evaluacion_planilla_otro_maxs', 'joven_evaluacion_planilla_otro_maxs.joven_evaluacion_planilla_id', '=', 'joven_evaluacion_planillas.id')
            ->leftJoin('joven_evaluacion_planilla_otros', 'joven_evaluacion_planilla_otro_maxs.joven_evaluacion_planilla_otro_id', '=', 'joven_evaluacion_planilla_otros.id')
            ->leftJoin('evaluacion_subgrupos', 'joven_evaluacion_planilla_otros.evaluacion_subgrupo_id', '=', 'evaluacion_subgrupos.id')
            ->leftJoin('evaluacion_grupos', 'joven_evaluacion_planilla_otro_maxs.evaluacion_grupo_id', '=', 'evaluacion_grupos.id')
            ->leftJoin('evaluacion_grupos as padre', 'evaluacion_grupos.padre_id', '=', 'padre.id')
            ->where('joven_evaluacion_planillas.periodo_id',$joven->periodo_id)

            ->select('joven_evaluacion_planilla_otro_maxs.*', 'joven_evaluacion_planilla_otros.nombre as otro_nombre', 'evaluacion_grupos.maximo as grupo_maximo', 'evaluacion_grupos.nombre as grupo_nombre', 'padre.nombre as padre_nombre','padre.id as padre_id','padre.maximo as padre_maximo', 'evaluacion_subgrupos.nombre as subgrupo_nombre','joven_evaluacion_planilla_otros.evaluacion_subgrupo_id')
            ->get();

        $produccionMaximos = DB::table('joven_evaluacion_planillas')
            ->leftJoin('joven_evaluacion_planilla_produccion_maxs', 'joven_evaluacion_planilla_produccion_maxs.joven_evaluacion_planilla_id', '=', 'joven_evaluacion_planillas.id')
            ->leftJoin('joven_evaluacion_planilla_produccions', 'joven_evaluacion_planilla_produccion_maxs.joven_evaluacion_planilla_produccion_id', '=', 'joven_evaluacion_planilla_produccions.id')
            ->leftJoin('evaluacion_subgrupos', 'joven_evaluacion_planilla_produccions.evaluacion_subgrupo_id', '=', 'evaluacion_subgrupos.id')
            ->leftJoin('evaluacion_grupos', 'joven_evaluacion_planilla_produccion_maxs.evaluacion_grupo_id', '=', 'evaluacion_grupos.id')
            ->leftJoin('evaluacion_grupos as padre', 'evaluacion_grupos.padre_id', '=', 'padre.id')
            ->where('joven_evaluacion_planillas.periodo_id',$joven->periodo_id)

            ->select('joven_evaluacion_planilla_produccion_maxs.*', 'joven_evaluacion_planilla_produccions.nombre as produccion_nombre', 'evaluacion_grupos.maximo as grupo_maximo', 'evaluacion_grupos.nombre as grupo_nombre', 'padre.nombre as padre_nombre','padre.id as padre_id','padre.maximo as padre_maximo', 'evaluacion_subgrupos.nombre as subgrupo_nombre','joven_evaluacion_planilla_produccions.evaluacion_subgrupo_id')
            ->get();

        $anteriorMaximos = DB::table('joven_evaluacion_planillas')
            ->leftJoin('joven_evaluacion_planilla_anterior_maxs', 'joven_evaluacion_planilla_anterior_maxs.joven_evaluacion_planilla_id', '=', 'joven_evaluacion_planillas.id')
            ->leftJoin('joven_evaluacion_planilla_anteriors', 'joven_evaluacion_planilla_anterior_maxs.joven_evaluacion_planilla_anterior_id', '=', 'joven_evaluacion_planilla_anteriors.id')
            ->leftJoin('evaluacion_subgrupos', 'joven_evaluacion_planilla_anteriors.evaluacion_subgrupo_id', '=', 'evaluacion_subgrupos.id')
            ->leftJoin('evaluacion_grupos', 'joven_evaluacion_planilla_anterior_maxs.evaluacion_grupo_id', '=', 'evaluacion_grupos.id')
            ->leftJoin('evaluacion_grupos as padre', 'evaluacion_grupos.padre_id', '=', 'padre.id')
            ->where('joven_evaluacion_planillas.periodo_id',$joven->periodo_id)

            ->select('joven_evaluacion_planilla_anterior_maxs.*', 'joven_evaluacion_planilla_anteriors.nombre as anterior_nombre', 'evaluacion_grupos.maximo as grupo_maximo', 'evaluacion_grupos.nombre as grupo_nombre', 'padre.nombre as padre_nombre','padre.id as padre_id','padre.maximo as padre_maximo', 'evaluacion_subgrupos.nombre as subgrupo_nombre','joven_evaluacion_planilla_anteriors.evaluacion_subgrupo_id')
            ->get();

        $justificacionMaximos = DB::table('joven_evaluacion_planillas')
            ->leftJoin('joven_evaluacion_planilla_justificacion_maxs', 'joven_evaluacion_planilla_justificacion_maxs.joven_evaluacion_planilla_id', '=', 'joven_evaluacion_planillas.id')
            ->leftJoin('joven_evaluacion_planilla_justificacions', 'joven_evaluacion_planilla_justificacion_maxs.joven_evaluacion_planilla_justificacion_id', '=', 'joven_evaluacion_planilla_justificacions.id')
            ->leftJoin('evaluacion_subgrupos', 'joven_evaluacion_planilla_justificacions.evaluacion_subgrupo_id', '=', 'evaluacion_subgrupos.id')
            ->leftJoin('evaluacion_grupos', 'joven_evaluacion_planilla_justificacion_maxs.evaluacion_grupo_id', '=', 'evaluacion_grupos.id')
            ->leftJoin('evaluacion_grupos as padre', 'evaluacion_grupos.padre_id', '=', 'padre.id')
            ->where('joven_evaluacion_planillas.periodo_id',$joven->periodo_id)

            ->select('joven_evaluacion_planilla_justificacion_maxs.*', 'joven_evaluacion_planilla_justificacions.nombre as justificacion_nombre', 'evaluacion_grupos.maximo as grupo_maximo', 'evaluacion_grupos.nombre as grupo_nombre', 'padre.nombre as padre_nombre','padre.id as padre_id','padre.maximo as padre_maximo', 'evaluacion_subgrupos.nombre as subgrupo_nombre','joven_evaluacion_planilla_justificacions.evaluacion_subgrupo_id')
            ->get();

        $data = [
            'year' => $datos->periodo_nombre,
            'facultadplanilla' => $datos->facultad_nombre,
            'solicitante' => $datos->persona_apellido,
            'estado' => $datos->estado,
            'evaluador' => $datos->usuario_nombre,
            'evaluacion' => $evaluacion,
            'posgradoMaximos' => $posgradoMaximos,
            'maxPosgrado' => $maxPosgrado,
            'planilla' => $planilla,
            'antAcadMaximos' => $antAcadMaximos,
            'cargoMaximos' => $cargoMaximos,
            'maxCargo' => $maxCargo,
            'otroMaximos' => $otroMaximos,
            'produccionMaximos' => $produccionMaximos,
            'anteriorMaximos' => $anteriorMaximos,
            'justificacionMaximos' => $justificacionMaximos,
        ];
        //dd($data);


        $template = 'joven_evaluacions.pdfevaluacion';

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
        //return view('joven_evaluacions.pdfevaluacion', $data);
    }




    public function evaluar($id)
    {
        $joven = Joven::find($id);
        $user = auth()->user();
        $evaluacion = $joven->evaluacions()->where('user_id', $user->id)->first(); // Evaluación del usuario
        $facultad= DB::table('facultads')->where('id', $joven->facultadplanilla_id)->first();

        $planilla = DB::table('joven_evaluacion_planillas')

            ->where('joven_evaluacion_planillas.periodo_id',$joven->periodo_id)
            ->first();

        $posgradoMaximos = DB::table('joven_evaluacion_planillas')
                        ->leftJoin('joven_evaluacion_planilla_posgrado_maxs', 'joven_evaluacion_planilla_posgrado_maxs.joven_evaluacion_planilla_id', '=', 'joven_evaluacion_planillas.id')
                        ->leftJoin('joven_evaluacion_planilla_posgrados', 'joven_evaluacion_planilla_posgrado_maxs.joven_evaluacion_planilla_posgrado_id', '=', 'joven_evaluacion_planilla_posgrados.id')
                        ->where('joven_evaluacion_planillas.periodo_id',$joven->periodo_id)
                        ->select('joven_evaluacion_planilla_posgrado_maxs.*', 'joven_evaluacion_planilla_posgrados.nombre as posgrado_nombre')
                        ->get();

        $maxPosgrado = DB::table('joven_evaluacion_planillas')
            ->leftJoin('joven_evaluacion_planilla_posgrado_maxs', 'joven_evaluacion_planilla_posgrado_maxs.joven_evaluacion_planilla_id', '=', 'joven_evaluacion_planillas.id')

            ->where('joven_evaluacion_planillas.periodo_id', $joven->periodo_id)
            ->max('joven_evaluacion_planilla_posgrado_maxs.maximo');  // Aquí obtienes el máximo valor de la columna 'maximo'



        //dd($puntajePosgrado);

        $antAcadMaximos = DB::table('joven_evaluacion_planillas')
            ->leftJoin('joven_evaluacion_planilla_ant_acad_maxs', 'joven_evaluacion_planilla_ant_acad_maxs.joven_evaluacion_planilla_id', '=', 'joven_evaluacion_planillas.id')
            ->leftJoin('joven_evaluacion_planilla_ant_acads', 'joven_evaluacion_planilla_ant_acad_maxs.joven_evaluacion_planilla_ant_acad_id', '=', 'joven_evaluacion_planilla_ant_acads.id')
            ->leftJoin('evaluacion_grupos', 'joven_evaluacion_planilla_ant_acad_maxs.evaluacion_grupo_id', '=', 'evaluacion_grupos.id')
            ->where('joven_evaluacion_planillas.periodo_id',$joven->periodo_id)

            ->select('joven_evaluacion_planilla_ant_acad_maxs.*', 'joven_evaluacion_planilla_ant_acads.nombre as ant_acad_nombre', 'evaluacion_grupos.maximo as grupo_maximo', 'evaluacion_grupos.nombre as grupo_nombre')
            ->get();


        /*$maxAntAcad = DB::table('joven_evaluacion_planillas')
            ->leftJoin('joven_evaluacion_planilla_ant_acad_maxs', 'joven_evaluacion_planilla_ant_acad_maxs.joven_evaluacion_planilla_id', '=', 'joven_evaluacion_planillas.id')
            ->leftJoin('evaluacion_grupos', 'joven_evaluacion_planilla_ant_acad_maxs.evaluacion_grupo_id', '=', 'evaluacion_grupos.id')
            ->where('joven_evaluacion_planillas.periodo_id', $joven->periodo_id)
            ->max('evaluacion_grupos.maximo');*/

        /*$puntajeAntAcad = JovenEvaluacionPuntajeAntAcad::where('joven_evaluacion_id', $evaluacion->id)->get();

        dd($puntajeAntAcad);*/
        $cargoMaximos = DB::table('joven_evaluacion_planillas')
            ->leftJoin('joven_evaluacion_planilla_cargo_maxs', 'joven_evaluacion_planilla_cargo_maxs.joven_evaluacion_planilla_id', '=', 'joven_evaluacion_planillas.id')
            ->leftJoin('joven_evaluacion_planilla_cargos', 'joven_evaluacion_planilla_cargo_maxs.joven_evaluacion_planilla_cargo_id', '=', 'joven_evaluacion_planilla_cargos.id')
            ->where('joven_evaluacion_planillas.periodo_id',$joven->periodo_id)
            ->select('joven_evaluacion_planilla_cargo_maxs.*', 'joven_evaluacion_planilla_cargos.nombre as cargo_nombre', 'joven_evaluacion_planilla_cargos.id as cargo_id')
            ->get();



        $otroMaximos = DB::table('joven_evaluacion_planillas')
            ->leftJoin('joven_evaluacion_planilla_otro_maxs', 'joven_evaluacion_planilla_otro_maxs.joven_evaluacion_planilla_id', '=', 'joven_evaluacion_planillas.id')
            ->leftJoin('joven_evaluacion_planilla_otros', 'joven_evaluacion_planilla_otro_maxs.joven_evaluacion_planilla_otro_id', '=', 'joven_evaluacion_planilla_otros.id')
            ->leftJoin('evaluacion_subgrupos', 'joven_evaluacion_planilla_otros.evaluacion_subgrupo_id', '=', 'evaluacion_subgrupos.id')
            ->leftJoin('evaluacion_grupos', 'joven_evaluacion_planilla_otro_maxs.evaluacion_grupo_id', '=', 'evaluacion_grupos.id')
            ->leftJoin('evaluacion_grupos as padre', 'evaluacion_grupos.padre_id', '=', 'padre.id')
            ->where('joven_evaluacion_planillas.periodo_id',$joven->periodo_id)

            ->select('joven_evaluacion_planilla_otro_maxs.*', 'joven_evaluacion_planilla_otros.nombre as otro_nombre', 'evaluacion_grupos.maximo as grupo_maximo', 'evaluacion_grupos.nombre as grupo_nombre', 'padre.nombre as padre_nombre','padre.id as padre_id','padre.maximo as padre_maximo', 'evaluacion_subgrupos.nombre as subgrupo_nombre','joven_evaluacion_planilla_otros.evaluacion_subgrupo_id')
            ->get();




        $produccionMaximos = DB::table('joven_evaluacion_planillas')
            ->leftJoin('joven_evaluacion_planilla_produccion_maxs', 'joven_evaluacion_planilla_produccion_maxs.joven_evaluacion_planilla_id', '=', 'joven_evaluacion_planillas.id')
            ->leftJoin('joven_evaluacion_planilla_produccions', 'joven_evaluacion_planilla_produccion_maxs.joven_evaluacion_planilla_produccion_id', '=', 'joven_evaluacion_planilla_produccions.id')
            ->leftJoin('evaluacion_subgrupos', 'joven_evaluacion_planilla_produccions.evaluacion_subgrupo_id', '=', 'evaluacion_subgrupos.id')
            ->leftJoin('evaluacion_grupos', 'joven_evaluacion_planilla_produccion_maxs.evaluacion_grupo_id', '=', 'evaluacion_grupos.id')
            ->leftJoin('evaluacion_grupos as padre', 'evaluacion_grupos.padre_id', '=', 'padre.id')
            ->where('joven_evaluacion_planillas.periodo_id',$joven->periodo_id)

            ->select('joven_evaluacion_planilla_produccion_maxs.*', 'joven_evaluacion_planilla_produccions.nombre as produccion_nombre', 'evaluacion_grupos.maximo as grupo_maximo', 'evaluacion_grupos.nombre as grupo_nombre', 'padre.nombre as padre_nombre','padre.id as padre_id','padre.maximo as padre_maximo', 'evaluacion_subgrupos.nombre as subgrupo_nombre','joven_evaluacion_planilla_produccions.evaluacion_subgrupo_id')
            ->get();




       //dd($produccionMaximos);
        $unidadAprobada = DB::table('joven_evaluacion_unidad_aprobadas')
            ->where(function($query) use ($joven) {
                $query->where('unidad_id', $joven->unidad_id)
                    ->orWhere('unidad_id', $joven->unidadcarrera_id)
                    ->orWhere('unidad_id', $joven->unidadbeca_id);
            })
            ->exists();

        $anteriorMaximos = DB::table('joven_evaluacion_planillas')
            ->leftJoin('joven_evaluacion_planilla_anterior_maxs', 'joven_evaluacion_planilla_anterior_maxs.joven_evaluacion_planilla_id', '=', 'joven_evaluacion_planillas.id')
            ->leftJoin('joven_evaluacion_planilla_anteriors', 'joven_evaluacion_planilla_anterior_maxs.joven_evaluacion_planilla_anterior_id', '=', 'joven_evaluacion_planilla_anteriors.id')
            ->leftJoin('evaluacion_subgrupos', 'joven_evaluacion_planilla_anteriors.evaluacion_subgrupo_id', '=', 'evaluacion_subgrupos.id')
            ->leftJoin('evaluacion_grupos', 'joven_evaluacion_planilla_anterior_maxs.evaluacion_grupo_id', '=', 'evaluacion_grupos.id')
            ->leftJoin('evaluacion_grupos as padre', 'evaluacion_grupos.padre_id', '=', 'padre.id')
            ->where('joven_evaluacion_planillas.periodo_id',$joven->periodo_id)

            ->select('joven_evaluacion_planilla_anterior_maxs.*', 'joven_evaluacion_planilla_anteriors.nombre as anterior_nombre', 'evaluacion_grupos.maximo as grupo_maximo', 'evaluacion_grupos.nombre as grupo_nombre', 'padre.nombre as padre_nombre','padre.id as padre_id','padre.maximo as padre_maximo', 'evaluacion_subgrupos.nombre as subgrupo_nombre','joven_evaluacion_planilla_anteriors.evaluacion_subgrupo_id')
            ->get();




        $periodo_anterior_id = $joven->periodo->id - 1;
        $subsidioAnterior = Joven::where('investigador_id', $joven->investigador->id)
            ->where('periodo_id', $periodo_anterior_id) // Asegúrate de tener el campo 'periodo_id' en la tabla
            ->whereIn('estado', ['Otorgada-No-Rendida', 'Otorgada-Rendida', 'Otorgada-Renunciada','Otorgada-Devuelta']) // Verificar si el estado es uno de los tres
            ->exists();

        $justificacionMaximos = DB::table('joven_evaluacion_planillas')
            ->leftJoin('joven_evaluacion_planilla_justificacion_maxs', 'joven_evaluacion_planilla_justificacion_maxs.joven_evaluacion_planilla_id', '=', 'joven_evaluacion_planillas.id')
            ->leftJoin('joven_evaluacion_planilla_justificacions', 'joven_evaluacion_planilla_justificacion_maxs.joven_evaluacion_planilla_justificacion_id', '=', 'joven_evaluacion_planilla_justificacions.id')
            ->leftJoin('evaluacion_subgrupos', 'joven_evaluacion_planilla_justificacions.evaluacion_subgrupo_id', '=', 'evaluacion_subgrupos.id')
            ->leftJoin('evaluacion_grupos', 'joven_evaluacion_planilla_justificacion_maxs.evaluacion_grupo_id', '=', 'evaluacion_grupos.id')
            ->leftJoin('evaluacion_grupos as padre', 'evaluacion_grupos.padre_id', '=', 'padre.id')
            ->where('joven_evaluacion_planillas.periodo_id',$joven->periodo_id)

            ->select('joven_evaluacion_planilla_justificacion_maxs.*', 'joven_evaluacion_planilla_justificacions.nombre as justificacion_nombre', 'evaluacion_grupos.maximo as grupo_maximo', 'evaluacion_grupos.nombre as grupo_nombre', 'padre.nombre as padre_nombre','padre.id as padre_id','padre.maximo as padre_maximo', 'evaluacion_subgrupos.nombre as subgrupo_nombre','joven_evaluacion_planilla_justificacions.evaluacion_subgrupo_id')
            ->get();



        //dd($anteriorMaximos);
        $fechaEgreso = Carbon::parse($joven->egresogrado);
        $yearEgresado = $fechaEgreso->diffInYears(Carbon::now());
        //dd($evaluacion->puntaje_ant_acads);
        return view('joven_evaluacions.evaluar',compact('joven','evaluacion','facultad','planilla','posgradoMaximos','maxPosgrado','antAcadMaximos','yearEgresado','cargoMaximos','otroMaximos','produccionMaximos','unidadAprobada','anteriorMaximos','subsidioAnterior','justificacionMaximos'));

    }

    public function saveEvaluar(Request $request, $id)
    {
        //dd($request);
        $input = $this->sanitizeInput($request->all());

        $evaluacion = JovenEvaluacion::findOrFail($id);


        DB::beginTransaction();
        try {

            $input['fecha']=Carbon::now();
            $evaluacion->update($input);

            $evaluacion->puntaje_posgrados()->delete();

            //Log::info("Posgrado mas ID: " . $request->joven_planilla_posgrado_max_id);
            $posgradorMaximo = explode('-',trim($request->joven_planilla_posgrado_max_id));
            //Log::info("Posgrado seleccionado: " . print_r($posgradorMaximo, true));
            if (count($posgradorMaximo) > 1) {
                DB::table('joven_evaluacion_puntaje_posgrados')->insert([
                    'joven_evaluacion_id' => $id, // Supongo que tienes un objeto $investigador disponible
                    'joven_evaluacion_planilla_id' => $request->joven_evaluacion_planilla_id,
                    'joven_evaluacion_planilla_posgrado_max_id' => $posgradorMaximo[0],

                    'created_at' => now(), // Establece la fecha y hora de creación
                    'updated_at' => now(), // Establece la fecha y hora de actualización
                ]);
            }

            $evaluacion->puntaje_cargos()->delete();


            $cargorMaximo = explode('-',trim($request->cargomaximo));

            if ($cargorMaximo) {
                DB::table('joven_evaluacion_puntaje_cargos')->insert([
                    'joven_evaluacion_id' => $id, // Supongo que tienes un objeto $investigador disponible
                    'joven_evaluacion_planilla_id' => $request->joven_evaluacion_planilla_id,
                    'joven_evaluacion_planilla_cargo_max_id' => $cargorMaximo[0],

                    'created_at' => now(), // Establece la fecha y hora de creación
                    'updated_at' => now(), // Establece la fecha y hora de actualización
                ]);
            }

            $evaluacion->puntaje_ant_acads()->delete();
            $cantAntAcad = $request->cantantacad;
            //dd($cantAntAcad);
            for ($i = 0; $i < $cantAntAcad; $i++) {
                $inputName = 'id_ant_acad' . $i;
                if ($request->has($inputName)) {
                    $id_ant_acad = $request->input($inputName);
                }
                $puntaje = NULL;
                $inputName = 'puntajeantacad' . $i;
                if ($request->has($inputName)) {
                    $puntaje = $request->input($inputName);
                }
                $inputName = 'posgrado' . $i;
                if ($request->has($inputName)) {
                    $puntaje = 2;
                }
                if ($id_ant_acad && $puntaje) {
                    DB::table('joven_evaluacion_puntaje_ant_acads')->insert([
                        'joven_evaluacion_id' => $id, // Supongo que tienes un objeto $investigador disponible
                        'joven_evaluacion_planilla_id' => $request->joven_evaluacion_planilla_id,
                        'joven_evaluacion_planilla_ant_acad_max_id' => $id_ant_acad,
                        'puntaje' => $puntaje,
                        'created_at' => now(), // Establece la fecha y hora de creación
                        'updated_at' => now(), // Establece la fecha y hora de actualización
                    ]);
                }
            }

            $evaluacion->puntaje_otros()->delete();
            $cantOtros = $request->cantotros;
            //dd($cantOtros);
            for ($i = 0; $i < $cantOtros; $i++) {
                $inputName = 'id_otros' . $i;
                if ($request->has($inputName)) {
                    $id_otro = $request->input($inputName);
                }
                $puntaje = NULL;
                $inputName = 'puntajeotros' . $i;
                if ($request->has($inputName)) {
                    $puntaje = $request->input($inputName);
                }

                if ($id_otro && $puntaje) {
                    DB::table('joven_evaluacion_puntaje_otros')->insert([
                        'joven_evaluacion_id' => $id, // Supongo que tienes un objeto $investigador disponible
                        'joven_evaluacion_planilla_id' => $request->joven_evaluacion_planilla_id,
                        'joven_evaluacion_planilla_otro_max_id' => $id_otro,
                        'puntaje' => $puntaje,
                        'created_at' => now(), // Establece la fecha y hora de creación
                        'updated_at' => now(), // Establece la fecha y hora de actualización
                    ]);
                }
            }

            $evaluacion->puntaje_produccions()->delete();
            $cantProduccions = $request->cantproduccions;
            //dd($cantProduccion);
            for ($i = 0; $i < $cantProduccions; $i++) {
                $inputName = 'id_produccion' . $i;
                if ($request->has($inputName)) {
                    $id_produccion = $request->input($inputName);
                }
                $puntaje = NULL;
                $inputName = 'puntajeproduccion' . $i;
                if ($request->has($inputName)) {
                    $puntaje = $request->input($inputName);
                }
                $cantidad = NULL;
                $inputName = 'cantproduccion' . $i;
                if ($request->has($inputName)) {
                    $cantidad = $request->input($inputName);
                }

                if ($id_produccion && $puntaje) {
                    DB::table('joven_evaluacion_puntaje_produccions')->insert([
                        'joven_evaluacion_id' => $id, // Supongo que tienes un objeto $investigador disponible
                        'joven_evaluacion_planilla_id' => $request->joven_evaluacion_planilla_id,
                        'joven_evaluacion_planilla_produccion_max_id' => $id_produccion,
                        'puntaje' => $puntaje,
                        'cantidad' => $cantidad,
                        'created_at' => now(), // Establece la fecha y hora de creación
                        'updated_at' => now(), // Establece la fecha y hora de actualización
                    ]);
                }
            }

            $evaluacion->puntaje_anteriors()->delete();
            $cantAnteriors = $request->cantanteriors;
            //dd($cantAnteriors);
            for ($i = 0; $i < $cantAnteriors; $i++) {
                $inputName = 'id_anterior' . $i;
                if ($request->has($inputName)) {
                    $id_anterior = $request->input($inputName);
                }
                $puntaje = NULL;
                $inputName = 'puntajeanterior' . $i;
                if ($request->has($inputName)) {
                    $puntaje = $request->input($inputName);
                }

                if ($id_anterior && $puntaje) {
                    DB::table('joven_evaluacion_puntaje_anteriors')->insert([
                        'joven_evaluacion_id' => $id, // Supongo que tienes un objeto $investigador disponible
                        'joven_evaluacion_planilla_id' => $request->joven_evaluacion_planilla_id,
                        'joven_evaluacion_planilla_anterior_max_id' => $id_anterior,
                        'puntaje' => $puntaje,
                        'created_at' => now(), // Establece la fecha y hora de creación
                        'updated_at' => now(), // Establece la fecha y hora de actualización
                    ]);
                }
            }

            $evaluacion->puntaje_justificacions()->delete();
            $cantJustificacions = $request->cantjustificacions;
            //dd($cantJustificacions);
            for ($i = 0; $i < $cantJustificacions; $i++) {
                $inputName = 'id_justificacions' . $i;
                if ($request->has($inputName)) {
                    $id_justificacion = $request->input($inputName);
                }
                $puntaje = NULL;
                $inputName = 'puntajejustificacions' . $i;
                if ($request->has($inputName)) {
                    $puntaje = $request->input($inputName);
                }

                if ($id_justificacion && $puntaje) {
                    DB::table('joven_evaluacion_puntaje_justificacions')->insert([
                        'joven_evaluacion_id' => $id, // Supongo que tienes un objeto $investigador disponible
                        'joven_evaluacion_planilla_id' => $request->joven_evaluacion_planilla_id,
                        'joven_evaluacion_planilla_justificacion_max_id' => $id_anterior,
                        'puntaje' => $puntaje,
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




        return redirect()->route('jovens.index')->with($respuestaID, $respuestaMSJ);
    }


    public function aceptar( $id)
    {


        $joven = Joven::findOrFail($id);

        $user = auth()->user();
        $evaluacion = $joven->evaluacions()->where('user_id', $user->id)->first(); // Evaluación del usuario
        DB::beginTransaction();
        try {

            $evaluacion->estado = 'En evaluacion';
            $evaluacion->save();

            $this->cambiarEstado($evaluacion,'Evaluación aceptada');

            $year = $joven->periodo->nombre;

            // Preparar datos para el correo
            $datosCorreo = [
                'asunto' => 'Aceptación de Evaluación de Subsidios para Jóvenes Investigadores '.$year,
                'from_email' => $user->email,
                'from_name' => $user->name,
                'year' => $year,

                'investigador' => $joven->investigador->persona->apellido.', '.$joven->investigador->persona->nombre.' ('.$joven->investigador->persona->cuil.')',
                'comment' => 'La solicitud fue admitida para su evaluación',
            ];



            // Llama a la función enviarCorreos
            $this->enviarCorreo($datosCorreo, $joven);


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
        return redirect()->route('jovens.index')->with($respuestaID, $respuestaMSJ);
    }


    public function rechazar($id)
    {
        $joven = Joven::find($id);




        return view('joven_evaluacions.deny',compact('joven'));

    }

    public function saveDeny(Request $request, $id)
    {
        $this->validate($request, [
            'comentarios' => 'required'
        ]);

        $input = $this->sanitizeInput($request->all());

        $joven = Joven::findOrFail($id);

        $user = auth()->user();
        $evaluacion = $joven->evaluacions()->where('user_id', $user->id)->first(); // Evaluación del usuario
        DB::beginTransaction();
        try {

            $evaluacion->estado = 'Rechazada';
            $evaluacion->save();

            $this->cambiarEstado($evaluacion,$input['comentarios']);

            $year = $joven->periodo->nombre;

            // Preparar datos para el correo
            $datosCorreo = [
                'asunto' => 'Rechazo de Evaluación de Subsidios para Jóvenes Investigadores '.$year,
                'from_email' => $user->email,
                'from_name' => $user->name,
                'year' => $year,

                'investigador' => $joven->investigador->persona->apellido.', '.$joven->investigador->persona->nombre.' ('.$joven->investigador->persona->cuil.')',
                'comment' => '<strong>Motivos del rechazo</strong>: '.$input['comentarios'],
            ];



            // Llama a la función enviarCorreos
            $this->enviarCorreo($datosCorreo, $joven);


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
        return redirect()->route('jovens.index')->with($respuestaID, $respuestaMSJ);
    }







    public function enviarCorreo($datosCorreo, $joven)
    {
        // Enviar correo electrónico a tu servidor (ejemplo)
        Mail::to(Constants::MAIL_JOVENES)->send(new JovenEnviada($datosCorreo,$joven));
    }

    public function enviarCorreoEvaluacion($datosCorreo, $joven,$pdf)
    {
        // Enviar correo electrónico a tu servidor (ejemplo)
        Mail::to(Constants::MAIL_JOVENES)->send(new JovenEnviada($datosCorreo,$joven,false,$pdf));
    }

    public function enviarCorreoEvaluador($datosCorreo, $user, $joven)
    {

        $jovenController = new JovenController();
        // Generar el PDF y obtener la ruta
        $pdfPath = $jovenController->generatePDF(new Request(['joven_id' => $joven->id]), true);


        // Enviar correo electrónico al usuario
        Mail::to($user->email)->send(new EvaluadorJovenEnviada($datosCorreo, $joven, true, $pdfPath));


    }

    public function actualizar($id)
    {
        $joven = Joven::find($id);


        $error='';
        $ok = 1;

        $errores = [];

        if ($joven->estado != 'En evaluación'){
            $errores[] = 'La solicitud ya fue evaluada';
        }

        if (!empty($errores)) {
            return redirect()->back()->withErrors($errores)->withInput();
        }

        DB::beginTransaction();


        if ($ok){
            try {




                $evaluaciones = $joven->evaluacions()->where('estado', 'Evaluada')->get();

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

// Actualizar los campos en el modelo $joven
                $joven->puntaje = $puntaje;
                $joven->diferencia = $diferencia;
                $joven->estado = "Evaluada";
                $joven->save();




                $jovenController = new JovenController();
                $jovenController->cambiarEstado($joven,'Actualizar puntaje');


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


        return redirect()->route('joven_evaluacions.index', array('joven_id' => $id))->with($respuestaID, $respuestaMSJ);
    }
    public function send($id)
    {
        $joven = Joven::find($id);
        $user = auth()->user();
        $evaluacion = $joven->evaluacions()->where('user_id', $user->id)->first(); // Evaluación del usuario

        $error='';
        $ok = 1;

        $errores = [];

        if (!$evaluacion->puntaje){
            $errores[] = 'Aún no ha cargado puntajes';
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



                $year = $joven->periodo->nombre;

                // Preparar datos para el correo
                $datosCorreo = [
                    'asunto' => 'Evaluación de Subsidios para Jóvenes Investigadores '.$year,
                    'from_email' => $user->email,
                    'from_name' => $user->name,
                    'year' => $year,

                    'investigador' => $joven->investigador->persona->apellido.', '.$joven->investigador->persona->nombre.' ('.$joven->investigador->persona->cuil.')',
                    'comment' => '',
                ];

                // Generar el PDF y obtener la ruta
                $pdfPath = $this->generatePDF(new Request(['joven_evaluacion_id' => $evaluacion->id]), true);

                $this->enviarCorreoEvaluacion($datosCorreo,$joven,$pdfPath);

                $evaluadasTodas = !$joven->evaluacions()->where('estado', '!=', 'Evaluada')->exists();

                if ($evaluadasTodas){
                    $evaluaciones = $joven->evaluacions()->where('estado', 'Evaluada')->get();

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

// Actualizar los campos en el modelo $joven
                    $joven->puntaje = $puntaje;
                    $joven->diferencia = $diferencia;
                    $joven->estado = "Evaluada";
                    $joven->save();




                    $jovenController = new JovenController();
                    $jovenController->cambiarEstado($joven,'Ultima evaluacion enviada');
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

        return redirect()->route('jovens.index')->with($respuestaID, $respuestaMSJ);
    }

    public function enviar($joven_id)
    {
        // Buscar el joven por ID
        $joven = Joven::findOrFail($joven_id);
        $year = $joven->periodo->nombre;
        $error='';
        $success='';
        $ok=1;
        foreach ($joven->evaluacions->where('estado', 'Creada') as $evaluacion){
            $evaluacion->estado='Recibida';
            $evaluacion->fecha=Carbon::now();
            try {

                $evaluacion->update();
                $this->cambiarEstado($evaluacion,'Envío a evaluador');
                $user = User::find($evaluacion->user_id);

                // Preparar datos para el correo
                $roleName = Role::find(Constants::ID_COORDINADOR)->name;

                // Obtener el CAT del viaje
                $cat = DB::table('facultads')->where('id', $joven->facultadplanilla_id)->value('cat');

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

                // Preparar datos para el correo
                $datosCorreo = [
                    'asunto' => 'Evaluación de Subsidios para Jóvenes Investigadores '.$year,
                    'from_email' => Constants::MAIL_JOVENES,
                    'from_name' => Constants::NOMBRE_JOVENES,
                    'year' => $year,
                    'evaluador' => $user->name,
                    'fecha' => Carbon::parse($evaluacion->fecha)->format('d/m/Y'), // Formatear la fecha en el formato deseado
                    'postulante' => $joven->investigador->persona->apellido.', '.$joven->investigador->persona->nombre,
                    'urlWeb' => url('/'),
                    'urlInstructivo' => '',
                    'coordinadores' => $coordinadores,
                ];


                // Llama a la función enviarCorreos
                $this->enviarCorreoEvaluador($datosCorreo, $user,$joven);
                $success .='Enviada al evaluador '.$user->name.'<br>';

            }catch(QueryException $ex){

                    $error = $ex->getMessage();


                $ok=0;
                continue;
            }


        }

        if ($ok){
            try {
                $jovenController = new JovenController();
                $joven->estado = 'En evaluación';
                $joven->update();
                $jovenController->cambiarEstado($joven,'Envío a evaluadores');
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
        return redirect()->route('joven_evaluacions.index', array('joven_id' => $joven_id))->with($respuestaID, $respuestaMSJ);
    }





}
