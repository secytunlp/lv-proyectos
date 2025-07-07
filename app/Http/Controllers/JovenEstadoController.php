<?php

namespace App\Http\Controllers;


use App\Models\Joven;
use App\Models\JovenEstado;

use App\Models\Proyecto;

use App\Traits\SanitizesInput;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator; // Importar la fachada Validator
use Illuminate\Support\Facades\Auth; // Asegúrate de importar esta línea

class JovenEstadoController extends Controller
{
    use SanitizesInput;
    function __construct()
    {
        $this->middleware('permission:joven_estado-listar|joven_estado-crear|joven_estado-editar|joven_estado-eliminar', ['only' => ['index','store','dataTable']]);
        $this->middleware('permission:joven_estado-crear', ['only' => ['create','store','buscarInvestigador']]);
        $this->middleware('permission:joven_estado-editar', ['only' => ['edit','update']]);
        $this->middleware('permission:joven_estado-eliminar', ['only' => ['destroy']]);
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

        // Pasar el joven (si existe) a la vista
        return view('joven_estados.index', compact('joven'));
    }

    public function dataTable(Request $request)
    {

        $jovenId = $request->input('joven_id');
        $columnas = ['personas.nombre','joven_estados.estado', 'personas.apellido','desde','hasta','joven_estados.comentarios',DB::raw("IFNULL(users.name, joven_estados.user_name)") ]; // Define las columnas disponibles
        $columnaOrden = $columnas[$request->input('order.0.column')];
        $orden = $request->input('order.0.dir');
        $busqueda = $request->input('search.value');



        // Consulta base
        $query = JovenEstado::select('joven_estados.id as id', 'personas.nombre as persona_nombre','joven_estados.estado', DB::raw("CONCAT(personas.apellido, ', ', personas.nombre) as persona_apellido"), 'joven_estados.desde as desde', 'joven_estados.hasta as hasta', 'joven_estados.comentarios as comentarios',
            DB::raw("IFNULL(users.name, joven_estados.user_name) as usuario_nombre") )
            ->leftJoin('jovens', 'joven_estados.joven_id', '=', 'jovens.id')
            ->leftJoin('users', 'joven_estados.user_id', '=', 'users.id')

            ->leftJoin('investigadors', 'jovens.investigador_id', '=', 'investigadors.id')

            ->leftJoin('personas', 'investigadors.persona_id', '=', 'personas.id')
            ;


        // Aplicar filtro por proyecto si se proporciona el ID del proyecto
        if ($jovenId) {
            $query->where('joven_estados.joven_id', $jovenId);
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
        $recordsTotal = JovenEstado::count();

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
        $jovenId = $request->input('joven_id');
        $joven = null;

        // Si se proporciona un ID de joven, buscalo en la base de datos
        if ($jovenId) {
            $joven = Joven::findOrFail($jovenId);
        }

        return view('joven_estados.create',compact('joven'));
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

        $joven_id=$input['joven_id'];


        $joven = Joven::find($joven_id);

        DB::beginTransaction();
        $ok = 1;







        if ($ok){
            try {


                $joven->update($input);

                // Actualizar el registro de estado existente donde 'hasta' es null
                JovenEstado::where('joven_id', $joven->id)
                    ->whereNull('hasta')
                    ->update(['hasta' => Carbon::now()]);

                // Obtener el ID del usuario autenticado
                $userId = Auth::id();



                // Crear registro en integrante_estados con el estado "Alta Creada" y el user_id
                $joven->estados()->create([
                    'estado' => $joven->estado,
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

        return redirect()->route('joven_estados.index', array('joven_id' => $joven_id))->with($respuestaID, $respuestaMSJ);

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
