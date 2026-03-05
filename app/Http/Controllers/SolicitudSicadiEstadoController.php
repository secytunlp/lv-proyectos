<?php

namespace App\Http\Controllers;


use App\Models\SolicitudSicadi;
use App\Models\SolicitudSicadiEstado;


use App\Traits\SanitizesInput;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator; // Importar la fachada Validator
use Illuminate\Support\Facades\Auth; // Asegúrate de importar esta línea

class SolicitudSicadiEstadoController extends Controller
{

    use SanitizesInput;
    function __construct()
    {
        $this->middleware('permission:solicitud_sicadi_estado-listar|solicitud_sicadi_estado-crear|solicitud_sicadi_estado-editar|solicitud_sicadi_estado-eliminar', ['only' => ['index','store','dataTable']]);
        $this->middleware('permission:solicitud_sicadi_estado-crear', ['only' => ['create','store','buscarInvestigador']]);
        $this->middleware('permission:solicitud_sicadi_estado-editar', ['only' => ['edit','update']]);
        $this->middleware('permission:solicitud_sicadi_estado-eliminar', ['only' => ['destroy']]);
        //dd(session()->all());
    }





    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $solicitud_sicadiId = $request->query('solicitud_sicadi_id');
        $solicitud_sicadi = null;

        // Si se proporciona un ID de solicitud_sicadi, buscalo en la base de datos
        if ($solicitud_sicadiId) {
            $solicitud_sicadi = SolicitudSicadi::findOrFail($solicitud_sicadiId);
        }

        // Pasar el solicitud_sicadi (si existe) a la vista
        return view('solicitud_sicadi_estados.index', compact('solicitud_sicadi'));
    }

    public function dataTable(Request $request)
    {

        $solicitud_sicadiId = $request->input('solicitud_sicadi_id');
        $columnas = ['solicitud_sicadis.nombre','solicitud_sicadi_estados.estado', 'solicitud_sicadis.apellido','desde','hasta','solicitud_sicadi_estados.comentarios','users.name as usuario_nombre' ]; // Define las columnas disponibles
        $columnaOrden = $columnas[$request->input('order.0.column')];
        $orden = $request->input('order.0.dir');
        $busqueda = $request->input('search.value');



        // Consulta base
        $query = SolicitudSicadiEstado::select('solicitud_sicadi_estados.id as id', 'solicitud_sicadis.nombre as persona_nombre','solicitud_sicadi_estados.estado', DB::raw("CONCAT(solicitud_sicadis.apellido, ', ', solicitud_sicadis.nombre) as persona_apellido"), 'solicitud_sicadi_estados.desde as desde', 'solicitud_sicadi_estados.hasta as hasta', 'solicitud_sicadi_estados.comentarios as comentarios','users.name as usuario_nombre' )
            ->leftJoin('solicitud_sicadis', 'solicitud_sicadi_estados.solicitud_sicadi_id', '=', 'solicitud_sicadis.id')
            ->leftJoin('users', 'solicitud_sicadi_estados.user_id', '=', 'users.id')


            ;


        // Aplicar filtro por proyecto si se proporciona el ID del proyecto
        if ($solicitud_sicadiId) {
            $query->where('solicitud_sicadi_estados.solicitud_sicadi_id', $solicitud_sicadiId);
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
        $recordsTotal = SolicitudSicadiEstado::count();

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
        $solicitud_sicadiId = $request->input('solicitud_sicadi_id');
        $solicitud_sicadi = null;

        // Si se proporciona un ID de solicitud_sicadi, buscalo en la base de datos
        if ($solicitud_sicadiId) {
            $solicitud_sicadi = SolicitudSicadi::findOrFail($solicitud_sicadiId);
        }

        return view('solicitud_sicadi_estados.create',compact('solicitud_sicadi'));
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

        $solicitud_sicadi_id=$input['solicitud_sicadi_id'];


        $solicitud_sicadi = SolicitudSicadi::find($solicitud_sicadi_id);

        DB::beginTransaction();
        $ok = 1;







        if ($ok){
            try {


                $solicitud_sicadi->update($input);

                // Actualizar el registro de estado existente donde 'hasta' es null
                SolicitudSicadiEstado::where('solicitud_sicadi_id', $solicitud_sicadi->id)
                    ->whereNull('hasta')
                    ->update(['hasta' => Carbon::now()]);

                // Obtener el ID del usuario autenticado
                $userId = Auth::id();



                // Crear registro en integrante_estados con el estado "Alta Creada" y el user_id
                $solicitud_sicadi->estados()->create([
                    'estado' => $solicitud_sicadi->estado,
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

        return redirect()->route('solicitud_sicadi_estados.index', array('solicitud_sicadi_id' => $solicitud_sicadi_id))->with($respuestaID, $respuestaMSJ);

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



}
