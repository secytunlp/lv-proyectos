<?php

namespace App\Http\Controllers;


use App\Models\ViajeEvaluacion;
use App\Models\ViajeEvaluacionEstado;

use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator; // Importar la fachada Validator
use Illuminate\Support\Facades\Auth; // Asegúrate de importar esta línea

class ViajeEvaluacionEstadoController extends Controller
{

    function __construct()
    {
       /* $this->middleware('permission:viaje_estado-listar|viaje_estado-crear|viaje_estado-editar|viaje_estado-eliminar', ['only' => ['index','store','dataTable']]);
        $this->middleware('permission:viaje_estado-crear', ['only' => ['create','store','buscarInvestigador']]);
        $this->middleware('permission:viaje_estado-editar', ['only' => ['edit','update']]);
        $this->middleware('permission:viaje_estado-eliminar', ['only' => ['destroy']]);*/
        //dd(session()->all());
    }





    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $viajeEvaluacionId = $request->query('viaje_evaluacion_id');
        $viaje_evaluacion = null;

        // Si se proporciona un ID de viaje_evaluacion, buscalo en la base de datos
        if ($viajeEvaluacionId) {
            $viaje_evaluacion = ViajeEvaluacion::findOrFail($viajeEvaluacionId);
        }

        // Pasar el viaje (si existe) a la vista
        return view('viaje_evaluacion_estados.index', compact('viaje_evaluacion'));
    }

    public function dataTable(Request $request)
    {

        $viajeEvaluacionId = $request->input('viaje_evaluacion_id');
        $columnas = ['evaluadors.name','viaje_evaluacion_estados.estado', DB::raw("IFNULL(evaluadors.name, viaje_evaluacions.user_name)"),'desde','hasta','viaje_evaluacion_estados.comentarios',DB::raw("IFNULL(users.name, viaje_evaluacion_estados.user_name)") ]; // Define las columnas disponibles
        $columnaOrden = $columnas[$request->input('order.0.column')];
        $orden = $request->input('order.0.dir');
        $busqueda = $request->input('search.value');



        // Consulta base
        $query = ViajeEvaluacionEstado::select('viaje_evaluacion_estados.id as id', DB::raw("IFNULL(evaluadors.name, viaje_evaluacions.user_name) as persona_nombre"),'viaje_evaluacion_estados.estado', DB::raw("IFNULL(evaluadors.name, viaje_evaluacions.user_name) as persona_apellido"), 'viaje_evaluacion_estados.desde as desde', 'viaje_evaluacion_estados.hasta as hasta', 'viaje_evaluacion_estados.comentarios as comentarios',
            DB::raw("IFNULL(users.name, viaje_evaluacion_estados.user_name) as usuario_nombre") )
            ->leftJoin('viaje_evaluacions', 'viaje_evaluacion_estados.viaje_evaluacion_id', '=', 'viaje_evaluacions.id')
            ->leftJoin('users', 'viaje_evaluacion_estados.user_id', '=', 'users.id')

            ->leftJoin('users as evaluadors', 'viaje_evaluacions.user_id', '=', 'evaluadors.id')
            ;


        // Aplicar filtro por proyecto si se proporciona el ID del proyecto
        if ($viajeEvaluacionId) {
            $query->where('viaje_evaluacion_estados.viaje_evaluacion_id', $viajeEvaluacionId);
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
        $recordsTotal = ViajeEvaluacionEstado::count();

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
        $viajeEvaluacionId = $request->input('viaje_evaluacion_id');
        $viaje_evaluacion = null;

        // Si se proporciona un ID de viaje, buscalo en la base de datos
        if ($viajeEvaluacionId) {
            $viaje_evaluacion = ViajeEvaluacion::findOrFail($viajeEvaluacionId);
        }

        return view('viaje_evaluacion_estados.create',compact('viaje_evaluacion'));
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


        $input = $request->all();

        $viaje_evaluacion_id=$input['viaje_evaluacion_id'];


        $viajeEvaluacion = ViajeEvaluacion::find($viaje_evaluacion_id);

        DB::beginTransaction();
        $ok = 1;







        if ($ok){
            try {


                $viajeEvaluacion->update($input);

                // Actualizar el registro de estado existente donde 'hasta' es null
                ViajeEvaluacionEstado::where('viaje_evaluacion_id', $viajeEvaluacion->id)
                    ->whereNull('hasta')
                    ->update(['hasta' => Carbon::now()]);

                // Obtener el ID del usuario autenticado
                $userId = Auth::id();



                // Crear registro en integrante_estados con el estado "Alta Creada" y el user_id
                $viajeEvaluacion->estados()->create([
                    'estado' => $viajeEvaluacion->estado,
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

        return redirect()->route('viaje_evaluacion_estados.index', array('viaje_evaluacion_id' => $viaje_evaluacion_id))->with($respuestaID, $respuestaMSJ);

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
