<?php

namespace App\Http\Controllers;

use App\Models\Proyecto;
use App\Models\ProyectoEstado;
use App\Models\Unidad;
use App\Traits\SanitizesInput;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProyectoEstadoController extends Controller
{
    //
    use SanitizesInput;

    function __construct()
    {
        $this->middleware('permission:proyecto_estado-listar|proyecto_estado-crear|proyecto_estado-editar|proyecto_estado-eliminar', ['only' => ['index','store','dataTable']]);
        $this->middleware('permission:proyecto_estado-crear', ['only' => ['create','store','buscarInvestigador']]);
        $this->middleware('permission:proyecto_estado-editar', ['only' => ['edit','update']]);
        $this->middleware('permission:proyecto_estado-eliminar', ['only' => ['destroy']]);
        //dd(session()->all());
    }





    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $proyectoId = $request->query('proyecto_id');
        $proyecto = null;

        // Si se proporciona un ID de proyecto, buscalo en la base de datos
        if ($proyectoId) {
            $proyecto = Proyecto::findOrFail($proyectoId);
        }

        // Pasar el proyecto (si existe) a la vista
        return view('proyecto_estados.index', compact('proyecto'));
    }

    public function dataTable(Request $request)
    {

        $proyectoId = $request->input('proyecto_id');

        $columnas = ['proyecto_estados.tipo','proyecto_estados.codigo',  'proyecto_estados.titulo', 'personas.apellido', 'proyecto_estados.inicio', 'proyecto_estados.fin', 'facultads.nombre','proyecto_estados.estado','desde','hasta','proyecto_estados.comentarios','users.name' ]; // Define las columnas disponibles
        $columnaOrden = $columnas[$request->input('order.0.column')];
        $orden = $request->input('order.0.dir');
        $busqueda = $request->input('search.value');



        // Consulta base

        $query = ProyectoEstado::select('proyecto_estados.id as id', 'proyecto_estados.tipo as proyecto_tipo','proyecto_estados.codigo',  'proyecto_estados.titulo', DB::raw("CONCAT(personas.apellido, ', ', personas.nombre) as director"), 'proyecto_estados.inicio', 'proyecto_estados.fin', 'facultads.nombre as facultad_nombre','proyecto_estados.estado','desde','hasta','proyecto_estados.comentarios','users.name')
            ->leftJoin('proyectos', 'proyecto_estados.proyecto_id', '=', 'proyectos.id')
            ->leftJoin('facultads', 'proyecto_estados.facultad_id', '=', 'facultads.id')
            ->leftJoin('integrantes', function ($join) {
                $join->on('proyectos.id', '=', 'integrantes.proyecto_id')
                    ->where('integrantes.tipo', '=', 'Director');
            })
            ->leftJoin('investigadors', 'integrantes.investigador_id', '=', 'investigadors.id')
            ->leftJoin('personas', 'investigadors.persona_id', '=', 'personas.id')
            ->leftJoin('users', 'proyecto_estados.user_id', '=', 'users.id');




        ;


        // Aplicar filtro por proyecto si se proporciona el ID del proyecto
        if ($proyectoId) {
            $query->where('proyecto_estados.proyecto_id', $proyectoId);
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
        $recordsTotal = ProyectoEstado::count();

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
        $proyectoId = $request->input('proyecto_id');
        $proyecto = null;

        // Si se proporciona un ID de proyecto, buscalo en la base de datos
        if ($proyectoId) {
            $proyecto = Proyecto::findOrFail($proyectoId);
        }



        $facultades = DB::table('facultads')->pluck('nombre', 'id')->prepend('','');// Obtener todas las facultades directamente desde la tabla

        $disciplinas = DB::table('disciplinas')->pluck('nombre', 'id')->prepend('','');
        $especialidades = DB::table('especialidads')->pluck('nombre', 'id')->prepend('','');

        $campos = DB::table('campos')->pluck('nombre', 'id')->prepend('','');

        $unidads=Unidad::orderBy('nombre','ASC')->get();
        $unidads->each->append('path_to_parent');
        $unidads = $unidads->pluck('path_to_parent', 'id')->prepend('','');

        return view('proyecto_estados.create',compact('proyecto','facultades','disciplinas','especialidades','campos','unidads'));
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

        $proyecto_id=$input['proyecto_id'];


        $proyecto = Proyecto::find($proyecto_id);

        DB::beginTransaction();
        $ok = 1;







        if ($ok){
            try {

                $proyecto->update($input);

                // Actualizar el registro de estado existente donde 'hasta' es null
                ProyectoEstado::where('proyecto_id', $proyecto->id)
                    ->whereNull('hasta')
                    ->update(['hasta' => Carbon::now()]);

                // Obtener el ID del usuario autenticado
                $userId = Auth::id();



                // Crear registro en integrante_estados con el estado "Alta Creada" y el user_id
                $proyecto->estados()->create([
                    'estado' => $proyecto->estado,
                    'user_id' => $userId,
                    'comentarios' => $input['comentarios'],
                    'desde' => Carbon::now(), // Establecer 'desde' como la fecha actual
                    'tipo' => $proyecto->tipo,
                    'codigo' => $proyecto->codigo,
                    'sigeva' => $proyecto->sigeva,
                    'titulo' => $proyecto->titulo,
                    'inicio' => $proyecto->inicio,
                    'fin' => $proyecto->fin,
                    'facultad_id' => $proyecto->facultad_id,
                    'duracion' => $proyecto->duracion,
                    'unidad_id' => $proyecto->unidad_id,
                    'campo_id' => $proyecto->campo_id,
                    'disciplina_id' => $proyecto->disciplina_id,
                    'investigacion' => $proyecto->investigacion,
                    'linea' => $proyecto->linea,
                    'resumen' => $proyecto->resumen,
                    'clave1' => $proyecto->clave1,
                    'clave2' => $proyecto->clave2,
                    'clave3' => $proyecto->clave3,
                    'clave4' => $proyecto->clave4,
                    'clave5' => $proyecto->clave5,
                    'clave6' => $proyecto->clave6,
                    'key1' => $proyecto->key1,
                    'key2' => $proyecto->key2,
                    'key3' => $proyecto->key3,
                    'key4' => $proyecto->key4,
                    'key5' => $proyecto->key5,
                    'key6' => $proyecto->key6,
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

        return redirect()->route('proyecto_estados.index', array('proyecto_id' => $proyecto_id))->with($respuestaID, $respuestaMSJ);

    }
}
