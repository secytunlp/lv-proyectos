<?php

namespace App\Http\Controllers;


use App\Models\JovenEvaluacion;
use App\Models\JovenEvaluacionEstado;

use App\Traits\SanitizesInput;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator; // Importar la fachada Validator
use Illuminate\Support\Facades\Auth; // Asegúrate de importar esta línea

class JovenEvaluacionEstadoController extends Controller
{

    use SanitizesInput;
    function __construct()
    {
       /* $this->middleware('permission:joven_estado-listar|joven_estado-crear|joven_estado-editar|joven_estado-eliminar', ['only' => ['index','store','dataTable']]);
        $this->middleware('permission:joven_estado-crear', ['only' => ['create','store','buscarInvestigador']]);
        $this->middleware('permission:joven_estado-editar', ['only' => ['edit','update']]);
        $this->middleware('permission:joven_estado-eliminar', ['only' => ['destroy']]);*/
        //dd(session()->all());
    }





    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $jovenEvaluacionId = $request->query('joven_evaluacion_id');
        $joven_evaluacion = null;

        // Si se proporciona un ID de joven_evaluacion, buscalo en la base de datos
        if ($jovenEvaluacionId) {
            $joven_evaluacion = JovenEvaluacion::findOrFail($jovenEvaluacionId);
        }

        // Pasar el joven (si existe) a la vista
        return view('joven_evaluacion_estados.index', compact('joven_evaluacion'));
    }

    public function dataTable(Request $request)
    {

        $jovenEvaluacionId = $request->input('joven_evaluacion_id');
        $columnas = ['evaluadors.name','joven_evaluacion_estados.estado', DB::raw("IFNULL(evaluadors.name, joven_evaluacions.user_name)"),'desde','hasta','joven_evaluacion_estados.comentarios',DB::raw("IFNULL(users.name, joven_evaluacion_estados.user_name)") ]; // Define las columnas disponibles
        $columnaOrden = $columnas[$request->input('order.0.column')];
        $orden = $request->input('order.0.dir');
        $busqueda = $request->input('search.value');



        // Consulta base
        $query = JovenEvaluacionEstado::select('joven_evaluacion_estados.id as id', DB::raw("IFNULL(evaluadors.name, joven_evaluacions.user_name) as persona_nombre"),'joven_evaluacion_estados.estado', DB::raw("IFNULL(evaluadors.name, joven_evaluacions.user_name) as persona_apellido"), 'joven_evaluacion_estados.desde as desde', 'joven_evaluacion_estados.hasta as hasta', 'joven_evaluacion_estados.comentarios as comentarios',
            DB::raw("IFNULL(users.name, joven_evaluacion_estados.user_name) as usuario_nombre") )
            ->leftJoin('joven_evaluacions', 'joven_evaluacion_estados.joven_evaluacion_id', '=', 'joven_evaluacions.id')
            ->leftJoin('users', 'joven_evaluacion_estados.user_id', '=', 'users.id')

            ->leftJoin('users as evaluadors', 'joven_evaluacions.user_id', '=', 'evaluadors.id')
            ;


        // Aplicar filtro por proyecto si se proporciona el ID del proyecto
        if ($jovenEvaluacionId) {
            $query->where('joven_evaluacion_estados.joven_evaluacion_id', $jovenEvaluacionId);
        }


        // Aplicar la búsqueda
        if (!empty($busqueda)) {
            $query->where(function ($query) use ($columnas, $busqueda) {
                foreach ($columnas as $columna) {
                    $query->orWhere($columna, 'like', "%$busqueda%");
                }
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
        $recordsTotal = JovenEvaluacionEstado::count();

        return response()->json([
            'data' => $datos,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'draw' => $request->draw,
        ]);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $jovenEvaluacionId = $request->input('joven_evaluacion_id');
        $joven_evaluacion = null;

        // Si se proporciona un ID de joven, buscalo en la base de datos
        if ($jovenEvaluacionId) {
            $joven_evaluacion = JovenEvaluacion::findOrFail($jovenEvaluacionId);
        }

        return view('joven_evaluacion_estados.create',compact('joven_evaluacion'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Definir las reglas de validación
        $rules = [
            'comentarios' => 'required',

        ];

        // Definir los mensajes de error personalizados
        $messages = [

        ];

        // Crear el validador con las reglas y mensajes
        $validator = Validator::make($request->all(), $rules, $messages);



        // Validar y verificar si hay errores
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }


        $input = $this->sanitizeInput($request->all());

        $joven_evaluacion_id=$input['joven_evaluacion_id'];


        $jovenEvaluacion = JovenEvaluacion::find($joven_evaluacion_id);

        DB::beginTransaction();
        $ok = 1;







        if ($ok){
            try {


                $jovenEvaluacion->update($input);

                // Actualizar el registro de estado existente donde 'hasta' es null
                JovenEvaluacionEstado::where('joven_evaluacion_id', $jovenEvaluacion->id)
                    ->whereNull('hasta')
                    ->update(['hasta' => Carbon::now()]);

                // Obtener el ID del usuario autenticado
                $userId = Auth::id();



                // Crear registro en integrante_estados con el estado "Alta Creada" y el user_id
                $jovenEvaluacion->estados()->create([
                    'estado' => $jovenEvaluacion->estado,
                    'user_id' => $userId,
                    'comentarios' => $input['comentarios'],
                    'desde' => Carbon::now(), // Establecer 'desde' como la fecha actual

                ]);

















                DB::commit();
                $respuestaID = 'success';
                $respuestaMSJ = 'Estado creado con éxito';

            } catch (QueryException $ex) {
                // Manejar la excepción de la base de datos
                DB::rollback();

                    $respuestaID = 'error';
                    $respuestaMSJ = $ex->getMessage();

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

        return redirect()->route('joven_evaluacion_estados.index', array('joven_evaluacion_id' => $joven_evaluacion_id))->with($respuestaID, $respuestaMSJ);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Proyecto  $proyecto
     * @return \Illuminate\Http\Response
     */
    public function show(Proyecto $proyecto)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Proyecto  $proyecto
     * @return \Illuminate\Http\Response
     */
    public function edit(Proyecto $proyecto)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Proyecto  $proyecto
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Proyecto $proyecto)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Proyecto  $proyecto
     * @return \Illuminate\Http\Response
     */
    public function destroy(Proyecto $proyecto)
    {
        //
    }
}
