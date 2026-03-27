<?php

namespace App\Http\Controllers;

use App\Models\UnidadInvestigacion;
use App\Models\UnidadInvestigacionEstado;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Traits\SanitizesInput;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UnidadInvestigacionEstadoController extends Controller
{
    //
    use SanitizesInput;

    function __construct()
    {
        $this->middleware('permission:unidad_estado-listar|unidad_estado-crear|unidad_estado-editar|unidad_estado-eliminar', ['only' => ['index','store','dataTable']]);
        $this->middleware('permission:unidad_estado-crear', ['only' => ['create','store','buscarInvestigador']]);
        $this->middleware('permission:unidad_estado-editar', ['only' => ['edit','update']]);
        $this->middleware('permission:unidad_estado-eliminar', ['only' => ['destroy']]);
        //dd(session()->all());
    }





    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $unidadId = $request->query('unidad_id');
        $unidad = null;

        // Si se proporciona un ID de unidad, buscalo en la base de datos
        if ($unidadId) {
            $unidad = UnidadInvestigacion::findOrFail($unidadId);
        }

        // Pasar el unidad (si existe) a la vista
        return view('unidad_estados.index', compact('unidad'));
    }

    public function dataTable(Request $request)
    {

        $unidadId = $request->input('unidad_id');
        $columnas = ['unidad_investigacions.denominacion','unidad_investigacions.sigla','unidad_investigacion_estados.estado','desde','hasta','unidad_investigacion_estados.comentarios',DB::raw("IFNULL(users.name, unidad_investigacion_estados.user_name)") ]; // Define las columnas disponibles
        $columnaOrden = $columnas[$request->input('order.0.column')];
        $orden = $request->input('order.0.dir');
        $busqueda = $request->input('search.value');



        // Consulta base
        $query = UnidadInvestigacionEstado::select('unidad_investigacions.id as id', 'unidad_investigacions.denominacion', 'unidad_investigacions.sigla','unidad_investigacion_estados.estado', 'unidad_investigacion_estados.desde as desde', 'unidad_investigacion_estados.hasta as hasta', 'unidad_investigacion_estados.comentarios as comentarios',
            DB::raw("IFNULL(users.name, unidad_investigacion_estados.user_name) as usuario_nombre") )
            ->leftJoin('unidad_investigacions', 'unidad_investigacion_estados.unidad_id', '=', 'unidad_investigacions.id')
            ->leftJoin('users', 'unidad_investigacion_estados.user_id', '=', 'users.id')


        ;


        // Aplicar filtro por proyecto si se proporciona el ID del proyecto
        if ($unidadId) {
            $query->where('unidad_investigacion_estados.unidad_id', $unidadId);
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
        $recordsTotal = UnidadInvestigacionEstado::count();

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
        $unidadId = $request->input('unidad_id');
        $unidad = null;

        // Si se proporciona un ID de unidad, buscalo en la base de datos
        if ($unidadId) {
            $unidad = UnidadInvestigacion::findOrFail($unidadId);
        }

        return view('unidad_estados.create',compact('unidad'));
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

        $unidad_id=$input['unidad_id'];


        $unidad = UnidadInvestigacion::find($unidad_id);

        DB::beginTransaction();
        $ok = 1;







        if ($ok){
            try {

                //dd($input);
                $unidad->update($input);

                // Actualizar el registro de estado existente donde 'hasta' es null
                UnidadInvestigacionEstado::where('unidad_id', $unidad->id)
                    ->whereNull('hasta')
                    ->update(['hasta' => Carbon::now()]);

                // Obtener el ID del usuario autenticado
                $userId = Auth::id();



                // Crear registro en integrante_estados con el estado "Alta Creada" y el user_id
                $unidad->estados()->create([
                    'estado' => $unidad->estado,
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

        return redirect()->route('unidad_estados.index', array('unidad_id' => $unidad_id))->with($respuestaID, $respuestaMSJ);

    }
}
