<?php

namespace App\Http\Controllers;

use App\Models\Cargo;
use App\Models\Carrerainv;
use App\Models\Categoria;
use App\Models\Miembro;
use App\Models\MiembroEstado;
use App\Models\Investigador;
use App\Models\Unidad;
use App\Models\Sicadi;
use App\Models\Titulo;
use App\Models\Universidad;
use App\Traits\SanitizesInput;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class MiembroEstadoController extends Controller
{
    use SanitizesInput;
    function __construct()
    {
        $this->middleware('permission:miembro_estado-listar|miembro_estado-crear|miembro_estado-editar|miembro_estado-eliminar', ['only' => ['index','store','dataTable']]);
        $this->middleware('permission:miembro_estado-crear', ['only' => ['create','store','buscarInvestigador']]);
        $this->middleware('permission:miembro_estado-editar', ['only' => ['edit','update']]);
        $this->middleware('permission:miembro_estado-eliminar', ['only' => ['destroy']]);
        //dd(session()->all());
    }





    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $miembroId = $request->query('miembro_id');
        $miembro = null;

        // Si se proporciona un ID de miembro, buscalo en la base de datos
        if ($miembroId) {
            $miembro = Miembro::findOrFail($miembroId);
        }

        // Pasar el miembro (si existe) a la vista
        return view('miembro_estados.index', compact('miembro'));
    }

    public function dataTable(Request $request)
    {

        $miembroId = $request->input('miembro_id');
        $columnas = ['miembros.nombre','miembro_estados.estado','unidad_investigacions.denominacion','unidad_investigacions.sigla', 'miembros.apellido', 'miembros.cuil','miembro_estados.tipo', 'categorias.nombre', 'sicadis.nombre', 'cargos.nombre','miembro_estados.deddoc','miembro_estados.beca', 'carrerainvs.nombre', 'organismos.codigo', 'facultads.nombre','miembro_estados.horas',DB::raw("CASE WHEN miembros.estudiante = 1 THEN 'SI' ELSE 'NO' END"),DB::raw("CASE WHEN miembros.activo = 1 THEN 'SI' ELSE 'NO' END"),'desde','hasta','miembro_estados.comentarios',DB::raw("IFNULL(users.name, miembro_estados.user_name)") ]; // Define las columnas disponibles
        $columnaOrden = $columnas[$request->input('order.0.column')];
        $orden = $request->input('order.0.dir');
        $busqueda = $request->input('search.value');



        // Consulta base
        $query = MiembroEstado::select('miembro_estados.id as id', 'miembros.nombre as persona_nombre','miembro_estados.estado','unidad_investigacions.denominacion','unidad_investigacions.sigla', DB::raw("CONCAT(miembros.apellido, ', ', miembros.nombre) as persona_apellido"),'miembro_estados.cuil as cuil','miembro_estados.tipo as tipo', 'categorias.nombre as categoria_nombre', 'sicadis.nombre as sicadi_nombre', 'cargos.nombre as cargo_nombre','miembro_estados.deddoc', 'miembro_estados.beca', DB::raw("CONCAT(carrerainvs.nombre, ' ', organismos.codigo) as carrerainv_nombre"), 'organismos.codigo as organismo_nombre', 'facultads.nombre as facultad_nombre', 'miembro_estados.horas as horas',DB::raw("CASE WHEN miembros.estudiante = 1 THEN 'SI' ELSE 'NO' END as estudiante"),DB::raw("CASE WHEN miembros.activo = 1 THEN 'SI' ELSE 'NO' END as activo"), 'miembro_estados.desde as desde', 'miembro_estados.hasta as hasta', 'miembro_estados.comentarios as comentarios',
            DB::raw("IFNULL(users.name, miembro_estados.user_name) as usuario_nombre") )
            ->leftJoin('miembros', 'miembro_estados.miembro_id', '=', 'miembros.id')
            ->leftJoin('users', 'miembro_estados.user_id', '=', 'users.id')
            ->leftJoin('categorias', 'miembro_estados.categoria_id', '=', 'categorias.id')

            ->leftJoin('unidad_investigacions', 'miembros.unidad_id', '=', 'unidad_investigacions.id')

            ->leftJoin('sicadis', 'miembro_estados.sicadi_id', '=', 'sicadis.id')
            ->leftJoin('cargos', 'miembro_estados.cargo_id', '=', 'cargos.id')
            ->leftJoin('carrerainvs', 'miembro_estados.carrerainv_id', '=', 'carrerainvs.id')
            ->leftJoin('organismos', 'miembro_estados.organismo_id', '=', 'organismos.id')
            ->leftJoin('facultads', 'miembro_estados.facultad_id', '=', 'facultads.id');


        // Aplicar filtro por unidad si se proporciona el ID del unidad
        if ($miembroId) {
            $query->where('miembro_estados.miembro_id', $miembroId);
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
        $recordsTotal = MiembroEstado::count();

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
        $miembroId = $request->input('miembro_id');
        $miembro = null;

        // Si se proporciona un ID de miembro, buscalo en la base de datos
        if ($miembroId) {
            $miembro = Miembro::findOrFail($miembroId);
        }
        //dd($miembro);
        //$provincias = DB::table('provincias')->OrderBy('nombre')->pluck('nombre', 'id'); // Obtener todas las provincias
        $titulos=Titulo::where('nivel', 'Grado')->orderBy('nombre','ASC')->get();
        $titulos = $titulos->pluck('full_name', 'id')->prepend('','');
        $tituloposts=Titulo::where('nivel', 'Posgrado')->orderBy('nombre','ASC')->get();
        $tituloposts = $tituloposts->pluck('full_name', 'id')->prepend('','');
        $facultades = DB::table('facultads')->pluck('nombre', 'id')->prepend('','');// Obtener todas las facultades directamente desde la tabla
        // Obtener los cargos ordenados por el campo 'orden' y seleccionar solo los campos 'id' y 'nombre'
        $cargos = Cargo::orderBy('orden')->pluck('nombre', 'id')->prepend('', '');

        $carrerainvs = Carrerainv::where('activo','1')->orderBy('orden')->pluck('nombre', 'id')->prepend('', '');
        $organismos = DB::table('organismos')->where('activo','1')->pluck('codigo', 'id')->prepend('','');
        $currentYear = date('Y');
        $startYear = 1994;
        $years = range($currentYear, $startYear);
        $years = array_combine($years, $years); // Esto crea un array asociativo con los años como claves y valores
        $categorias = Categoria::orderBy('id')->pluck('nombre', 'id')->prepend('', '');

        $sicadis = Sicadi::orderBy('id')->pluck('nombre', 'id')->prepend('', '');
        return view('miembro_estados.create',compact('titulos','tituloposts','facultades','cargos','carrerainvs','sicadis','years','organismos','categorias','sicadis','miembro'));
    }

    private function safeRequest($request, $key, $default = null)
    {
        if (!isset($request->$key[0])) {
            return $default;
        }

        return $this->sanitizeInput($request->$key[0]);
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

        $miembro_id=$input['miembro_id'];


        $miembro = Miembro::find($miembro_id);

        DB::beginTransaction();
        $ok = 1;







        if ($ok){
            try {

                //$miembro = Miembro::update($input);
                $miembro->update($input);





                if ($request->cargos[0]) {
                    $miembro->cargo_id = $request->cargos[0];
                    $miembro->deddoc = $request->deddocs[0];
                    $miembro->facultad_id = $request->facultads[0];



                }
                else{
                    $miembro->cargo_id = null;
                    $miembro->deddoc = null;
                    $miembro->facultad_id = null;

                }




                if ($request->carrerainvs[0]) {
                    $miembro->carrerainv_id = $request->carrerainvs[0];
                    $miembro->organismo_id = $request->organismos[0];

                }
                else{
                    $miembro->carrerainv_id = null;
                    $miembro->organismo_id = null;

                }
                if ($request->categorias[0]) {
                    $miembro->categoria_id = $request->categorias[0];


                }
                else{
                    $miembro->categoria_id = null;
                }

                if ($request->sicadis[0]) {
                    $miembro->sicadi_id = $request->sicadis[0];


                }
                else{
                    $miembro->sicadi_id = null;
                }


                if ($request->becas[0]) {
                    $miembro->beca = $request->becas[0];;



                }
                else{
                    $miembro->beca = null;

                }
                $miembro->save();
                // Actualizar el registro de estado existente donde 'hasta' es null
                MiembroEstado::where('miembro_id', $miembro->id)
                    ->whereNull('hasta')
                    ->update(['hasta' => Carbon::now()]);

                // Obtener el ID del usuario autenticado
                $userId = Auth::id();

                // Crear registro en miembro_estados con el estado "Alta Creada" y el user_id
                $miembro->estados()->create([
                    'tipo' => $miembro->tipo,
                    'nombre' => $miembro->nombre,
                    'apellido' => $miembro->apellido,
                    'cuil' => $miembro->cuil,
                    'estado' => $miembro->estado,
                    'user_id' => $userId,
                    'horas' => $miembro->horas,
                    'categoria_id' => ($miembro->categoria_id)?$miembro->categoria_id:null,
                    'sicadi_id' => ($miembro->sicadi_id)?$miembro->sicadi_id:null,
                    'deddoc' => $miembro->deddoc,
                    'cargo_id' => ($miembro->cargo_id)?$miembro->cargo_id:null,
                    'facultad_id' => ($miembro->facultad_id)?$miembro->facultad_id:null,
                    'activo' => $miembro->activo,
                    'carrerainv_id' => ($miembro->carrerainv_id)?$miembro->carrerainv_id:null,
                    'organismo_id' => ($miembro->organismo_id)?$miembro->organismo_id:null,
                    'estudiante' => $miembro->estudiante,
                    'beca' => ($miembro->beca)?$miembro->beca:null,
                    'desde' => Carbon::now(), // Establecer 'desde' como la fecha actual
                    'comentarios' => $input['comentarios'],
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

        return redirect()->route('miembro_estados.index', array('miembro_id' => $miembro_id))->with($respuestaID, $respuestaMSJ);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Unidad  $unidad
     * @return \Illuminate\Http\Response
     */
    public function show(Unidad $unidad)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Unidad  $unidad
     * @return \Illuminate\Http\Response
     */
    public function edit(Unidad $unidad)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Unidad  $unidad
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Unidad $unidad)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Unidad  $unidad
     * @return \Illuminate\Http\Response
     */
    public function destroy(Unidad $unidad)
    {
        //
    }
}
