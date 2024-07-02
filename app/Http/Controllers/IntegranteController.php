<?php

namespace App\Http\Controllers;

use App\Models\Cargo;
use App\Models\Carrerainv;
use App\Models\Categoria;
use App\Models\Integrante;
use App\Models\Investigador;
use App\Models\Persona;
use App\Models\Proyecto;
use App\Models\Sicadi;
use App\Models\Titulo;
use App\Models\Unidad;
use App\Models\Universidad;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Constants;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator; // Importar la fachada Validator
use Illuminate\Support\Facades\Auth; // Asegúrate de importar esta línea
class IntegranteController extends Controller
{

    function __construct()
    {
        $this->middleware('permission:integrante-listar|integrante-crear|integrante-editar|integrante-eliminar', ['only' => ['index','store','dataTable']]);
        $this->middleware('permission:integrante-crear', ['only' => ['create','store','buscarInvestigador']]);
        $this->middleware('permission:integrante-editar', ['only' => ['edit','update']]);
        $this->middleware('permission:integrante-eliminar', ['only' => ['destroy']]);
        //dd(session()->all());
    }

    // Función en tu controlador
    public function buscarInvestigador(Request $request)
    {
        $term = $request->input('term');

        $resultados = Investigador::whereHas('persona', function($query) use ($term) {
            $query->where('apellido', 'like', '%' . $term . '%')
                ->orWhere('cuil', 'like', '%' . $term . '%');
        })
            ->with(['persona:id,apellido,nombre,cuil,email', 'titulos:id,nombre', 'tituloposts:id,nombre', 'cargos:id,nombre', 'carrerainvs:id,nombre', 'categorias:id,nombre', 'sicadis:id,nombre','becas:id,institucion,beca,desde,hasta'])
    ->get();

    // Mapear los resultados para incluir los campos necesarios en la respuesta JSON
    $resultados = $resultados->map(function($investigador) {
        $titulo = $investigador->titulos->first(); // Tomar solo el primer título
        $titulopost = $investigador->tituloposts->first(); // Tomar solo el primer título
        $cargo = $investigador->cargos()->where('activo', true)->orderBy('deddoc', 'desc')->orderBy('orden', 'asc')->first(); // Obtener el cargo activo con mayor dedicación
        $carrerainv = $investigador->carrerainvs()->where('actual', true)->first();
        $sicadi = $investigador->sicadis()->where('actual', true)->first();
        $categoria = $investigador->categorias()->where('actual', true)->first();
        $hoy = Carbon::today(); //Aquí se obtiene la fecha de hoy
        $beca = $investigador->becas()->where('desde', '<=',$hoy)->where('hasta', '>=',$hoy)->first();
        return [
            'id' => $investigador->id,
            'apellido' => $investigador->persona->apellido,
            'nombre' => $investigador->persona->nombre,
            'cuil' => $investigador->persona->cuil,
            'email' => $investigador->persona->email,
            'titulo' => $titulo ? [
                'id' => $titulo->id,
                'nombre' => $titulo->nombre,
                'pivot' => [
                    'egreso' => $titulo->pivot->egreso
                ]
            ] : null,
            'titulopost' => $titulopost ? [
                'id' => $titulopost->id,
                'nombre' => $titulopost->nombre,
                'pivot' => [
                    'egreso' => $titulopost->pivot->egreso
                ]
            ] : null,
            'cargo' => $cargo ? [
                'id' => $cargo->id,
                'nombre' => $cargo->nombre,
                'pivot' => [
                    'deddoc' => $cargo->pivot->deddoc,
                    'ingreso' => $cargo->pivot->ingreso,
                    'facultad_id' => $cargo->pivot->facultad_id,
                    'activo' => $cargo->pivot->activo,
                    'universidad_id' => $cargo->pivot->universidad_id,
                ]
            ] : null,
            'unidad_id' => $investigador->unidad_id,
            'carrerainv' => $carrerainv ? [
                'id' => $carrerainv->id,
                'nombre' => $carrerainv->nombre,
                'pivot' => [
                    'ingreso' => $carrerainv->pivot->ingreso,
                    'organismo_id' => $carrerainv->pivot->organismo_id,

                ]
            ] : null,
            'categoria' => $categoria ? [
                'id' => $categoria->id,
                'nombre' => $categoria->nombre,

            ] : null,
            'sicadi' => $sicadi ? [
                'id' => $sicadi->id,
                'nombre' => $sicadi->nombre,

            ] : null,
            'beca' => $beca ? [
                'id' => $beca->id,
                'beca' => $beca->beca,
                'institucion' => $beca->institucion,
                'desde' => $beca->desde,
                'hasta' => $beca->hasta,

            ] : null,
        ];
    });

    return response()->json($resultados);
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
        return view('integrantes.index', compact('proyecto'));
    }

    public function dataTable(Request $request)
    {

        $proyectoId = $request->input('proyecto_id');
        $columnas = ['personas.nombre','proyectos.codigo','integrantes.tipo', 'personas.apellido', 'cuil', 'categorias.nombre', 'sicadis.nombre', 'cargos.nombre','integrantes.deddoc','integrantes.beca','integrantes.institucion', 'carrerainvs.nombre', 'organismos.codigo','alta','baja','horas', 'facultads.nombre', 'integrantes.estado']; // Define las columnas disponibles
        $columnaOrden = $columnas[$request->input('order.0.column')];
        $orden = $request->input('order.0.dir');
        $busqueda = $request->input('search.value');



        // Consulta base
        $query = Integrante::select('integrantes.id as id', 'personas.nombre as persona_nombre','proyectos.codigo as codigo','integrantes.tipo as tipo', DB::raw("CONCAT(personas.apellido, ', ', personas.nombre) as persona_apellido"), 'cuil', 'categorias.nombre as categoria_nombre', 'sicadis.nombre as sicadi_nombre', 'cargos.nombre as cargo_nombre','integrantes.deddoc', DB::raw("CONCAT(integrantes.beca, ' ', integrantes.institucion) as beca"),'integrantes.institucion', DB::raw("CONCAT(carrerainvs.nombre, ' ', organismos.codigo) as carrerainv_nombre"), 'organismos.codigo as organismo_nombre','integrantes.alta as alta','integrantes.baja as baja', 'integrantes.horas as horas', 'facultads.nombre as facultad_nombre', 'integrantes.estado as estado')
            ->leftJoin('categorias', 'integrantes.categoria_id', '=', 'categorias.id')
            ->leftJoin('investigadors', 'integrantes.investigador_id', '=', 'investigadors.id')
            ->leftJoin('proyectos', 'integrantes.proyecto_id', '=', 'proyectos.id')
            ->leftJoin('personas', 'investigadors.persona_id', '=', 'personas.id')
            ->leftJoin('sicadis', 'integrantes.sicadi_id', '=', 'sicadis.id')
            ->leftJoin('cargos', 'integrantes.cargo_id', '=', 'cargos.id')
            ->leftJoin('carrerainvs', 'integrantes.carrerainv_id', '=', 'carrerainvs.id')
            ->leftJoin('organismos', 'integrantes.organismo_id', '=', 'organismos.id')
            ->leftJoin('facultads', 'integrantes.facultad_id', '=', 'facultads.id');


        // Aplicar filtro por proyecto si se proporciona el ID del proyecto
        if ($proyectoId) {
            $query->where('integrantes.proyecto_id', $proyectoId);
        }


        // Aplicar la búsqueda
        if (!empty($busqueda)) {
            $query->where(function ($query) use ($columnas, $busqueda) {
                foreach ($columnas as $columna) {
                    $query->orWhere($columna, 'like', "%$busqueda%");
                }
            });
        }

        $selectedRoleId = session('selected_rol');
        if ($selectedRoleId == 3) {
            $user = auth()->user();
            $currentDate = date('Y-m-d');

            $query->where(function ($query) use ($user, $currentDate) {
                $query->whereExists(function ($subquery) use ($user, $currentDate) {
                    $subquery->select(DB::raw(1))
                        ->from('integrantes as i')
                        ->join('proyectos as p', 'i.proyecto_id', '=', 'p.id')
                        ->join('investigadors as inv', 'i.investigador_id', '=', 'inv.id')
                        ->join('personas as per', 'inv.persona_id', '=', 'per.id')
                        ->where('i.tipo', 'Director')
                        ->where('per.cuil', '=', $user->cuil)
                        ->where('p.inicio', '<=', $currentDate)
                        ->where('p.fin', '>=', $currentDate)
                        ->where('p.estado', '=', 'Acreditado')
                        ->whereColumn('i.proyecto_id', 'integrantes.proyecto_id');
                });
            });
        }
        if ($selectedRoleId == 4) {
            $user = auth()->user();


            $query->where(function ($query) use ($user) {
                $query->whereExists(function ($subquery) use ($user) {
                    $subquery->select(DB::raw(1))
                        ->from('integrantes as i')
                        ->join('proyectos as p', 'i.proyecto_id', '=', 'p.id')
                        ->join('investigadors as inv', 'i.investigador_id', '=', 'inv.id')
                        ->join('personas as per', 'inv.persona_id', '=', 'per.id')
                        ->where('i.tipo', 'Director')
                        ->where('p.facultad_id', '=', $user->facultad_id)

                        ->where('p.estado', '=', 'Acreditado')
                        ->whereColumn('i.proyecto_id', 'integrantes.proyecto_id');
                });
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
        $recordsTotal = Integrante::count();

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
        //$provincias = DB::table('provincias')->OrderBy('nombre')->pluck('nombre', 'id'); // Obtener todas las provincias
        $titulos=Titulo::where('nivel', 'Grado')->orderBy('nombre','ASC')->get();
        $titulos = $titulos->pluck('full_name', 'id')->prepend('','');
        $tituloposts=Titulo::where('nivel', 'Posgrado')->orderBy('nombre','ASC')->get();
        $tituloposts = $tituloposts->pluck('full_name', 'id')->prepend('','');
        $facultades = DB::table('facultads')->pluck('nombre', 'id')->prepend('','');// Obtener todas las facultades directamente desde la tabla
        // Obtener los cargos ordenados por el campo 'orden' y seleccionar solo los campos 'id' y 'nombre'
        $cargos = Cargo::orderBy('orden')->pluck('nombre', 'id')->prepend('', '');
        $universidades=Universidad::orderBy('nombre','ASC')->get();
        $universidades = $universidades->pluck('nombre', 'id')->prepend('','');
        $unidads=Unidad::orderBy('nombre','ASC')->get();
        $unidads->each->append('path_to_parent');
        $unidads = $unidads->pluck('path_to_parent', 'id')->prepend('','');
        $carrerainvs = Carrerainv::where('activo','1')->orderBy('orden')->pluck('nombre', 'id')->prepend('', '');
        $organismos = DB::table('organismos')->where('activo','1')->pluck('codigo', 'id')->prepend('','');
        $currentYear = date('Y');
        $startYear = 1994;
        $years = range($currentYear, $startYear);
        $years = array_combine($years, $years); // Esto crea un array asociativo con los años como claves y valores
        $categorias = Categoria::orderBy('id')->pluck('nombre', 'id')->prepend('', '');

        $sicadis = Sicadi::orderBy('id')->pluck('nombre', 'id')->prepend('', '');
        return view('integrantes.create',compact('titulos','tituloposts','facultades','cargos','universidades','unidads','carrerainvs','sicadis','years','organismos','categorias','sicadis','proyecto'));
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
            'tipo' => 'required',
            'apellido' => 'required',
            'cuil' => 'required|regex:/^\d{2}-\d{8}-\d{1}$/',
            'curriculum' => 'file|mimes:pdf,doc,docx|max:4048',
            'actividades' => 'file|mimes:pdf,doc,docx|max:4048',
        ];

        // Definir los mensajes de error personalizados
        $messages = [
            'cuil.regex' => 'El formato del CUIL es inválido.',
            'curriculum.mimes' => 'El archivo de curriculum debe ser un documento de tipo: pdf, doc, docx.',
            'curriculum.max' => 'El archivo de curriculum no debe ser mayor a 4 MB.',
            'actividades.mimes' => 'El archivo de actividades debe ser un documento de tipo: pdf, doc, docx.',
            'actividades.max' => 'El archivo de actividades no debe ser mayor a 4 MB.',
        ];

        // Crear el validador con las reglas y mensajes
        $validator = Validator::make($request->all(), $rules, $messages);

        // Añadir la validación personalizada para la fecha de cierre
        $validator->after(function ($validator) {
            $today = now();
            $cierreDate = \Carbon\Carbon::parse(Constants::CIERRE);
            if ($today->gt($cierreDate)) {
                $validator->errors()->add('convocatoria', 'La convocatoria no está vigente.');
            }
        });

        // Validar y verificar si hay errores
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }


        $input = $request->all();

        $proyecto_id=$input['proyecto_id'];

        $input['alta']= Constants::YEAR.'-01-01';
        $input['estado']= 'Alta Creada';
        // Manejo de archivos
        $input['curriculum'] ='';
        if ($files = $request->file('curriculum')) {
            $file = $request->file('curriculum');
            $name = time().'.'.$file->getClientOriginalExtension();
            $destinationPath = public_path('/files');
            $file->move($destinationPath, $name);
            $input['curriculum'] = "$name";
        }

        $input['actividades'] ='';
        if ($files = $request->file('actividades')) {
            $file = $request->file('actividades');
            $name = time().'.'.$file->getClientOriginalExtension();
            $destinationPath = public_path('/files');
            $file->move($destinationPath, $name);
            $input['actividades'] = "$name";
        }

        DB::beginTransaction();
        $ok = 1;

        try {
            $persona = Persona::where('cuil','=',$input['cuil'])->first();
            if (empty($persona)){
                // Obtener el máximo ident de los investigadores
                $ultimoId = Investigador::max('ident');
                $nuevoIdent = $ultimoId + 1; // Incrementar en 1 el último identificador
                $input['ident'] = $nuevoIdent; // Asignar el nuevo ident al input

                // Crear la persona y luego el investigador
                $persona = Persona::create($input);
                $investigador = $persona->investigador()->create($input);

            }
            else {
                $investigador = $persona->investigador;
            }
            //dd($investigador);
            $input['investigador_id'] = $investigador->id; // Asignar el nuevo ident al input
            $unProyecto=0;
            $dosProyectos=0;
            if ($input['tipo'] === 'Colaborador') {
                $unProyecto=1;
                $colaboradorConCargo = 0;
                $maxHoras=4;
                if (!empty($request->carrerainvs)) {
                    if ($request->carrerainvs[0]){
                        $colaboradorConCargo = 1;
                    }

                }
                if (!empty($request->cargos)) {
                    if ($request->cargos[0]) {
                        $colaboradorConCargo = 1;
                    }
                }
                if (!empty($request->becas)) {
                    if (!empty($request->becas[0]->beca)) {
                        $colaboradorConCargo = 1;
                    }
                }
                if ($colaboradorConCargo){
                    return redirect()->back()
                        ->withErrors(['cuil' => 'Los COLABORADORES no deben tener cargo docente ni Beca ni cargo en la Carrera de Investigación'])
                        ->withInput();
                }

            }
            elseif (!empty($request->becas[0]->beca)) {

                if ($request->becas[0]->beca!='RETENCION DE POSTGRADUADO'){
                    $unProyecto=1;
                }
                if ($request->becas[0]->beca!='Beca posdoctoral'){
                    $unProyecto=1;
                }
                $maxHoras=40;

            }
            elseif ($input['materias']>0) {
                $unProyecto=1;
                $maxHoras=4;
            }
            elseif (!empty($request->deddocs)) {

                if ($request->deddocs[0]=='Simple'){
                    //Log::info("Entra Dedicacion: ".$request->deddocs[0]);

                    if (!$request->carrerainvs[0]) {
                        $unProyecto = 1;
                        $maxHoras = 4;
                    }

                }
            }

            if (!$unProyecto){

                if (!empty($request->becas)) {
                    if (!empty($request->becas[0]->beca)) {

                        if ($request->becas[0]->beca == 'Beca posdoctoral') {
                            $dosProyectos = 1;
                        }
                        $maxHoras=40;
                    }
                    if (!empty($request->carrerainvs)) {
                        if (!$request->carrerainvs[0]) {
                            $dosProyectos = 1;
                            $maxHoras = 35;
                        }
                    }
                    if (!empty($request->deddocs)) {
                        if ($request->deddocs[0] != 'Simple') {
                            $dosProyectos = 1;
                            $maxHoras=35;
                        }
                    }

                }
            }
            if ($unProyecto || $dosProyectos){
                // Verificar si el investigador participa en otro proyecto activo
                // Consulta para obtener los integrantes
                $integrantes = Integrante::where('investigador_id', $investigador->id)
                    ->where(function ($query) {
                        $query->where('estado', '!=', 'Baja Creada')
                            ->orWhere('estado', '!=', 'Baja Recibida')
                            ->orWhere('baja', '>', Carbon::now()->format('Y-m-d'))
                            ->orWhereNull('baja')
                            ->orWhere('baja', '0000-00-00');
                    })
                    ->whereHas('proyecto', function ($query) use ($proyecto_id) {
                        $query->where('estado', 'Acreditado')
                            ->where('id', '<>', $proyecto_id)
                            ->where('fin', '>', Carbon::now()->subYears(1)->format('Y-m-d'));
                    })
                    ->get();
                // Verificar si la colección de integrantes no está vacía
                if (!$integrantes->isEmpty()) {
                    //dd($integrantes);
                    //Log::info("Horas ".$integrantes[0]->horas);
                    $proyectos = $integrantes->pluck('proyecto.codigo')->unique()->join(', ');
                    if ($unProyecto){



                        return redirect()->back()
                            ->withErrors(['cuil' => 'El investigador ya forma parte de los siguientes proyectos: ' . $proyectos])
                            ->withInput();
                    }
                    else{
                        if ($integrantes->count()>1){


                            return redirect()->back()
                                ->withErrors(['cuil' => 'El investigador ya forma parte de los siguientes proyectos: ' . $proyectos])
                                ->withInput();

                        }
                        else{
                            $horasOtroProyecto = $integrantes[0]->horas;
                            $horasTotales = $horasOtroProyecto + $input['horas'];
                            if ($horasTotales>$maxHoras){
                                return redirect()->back()
                                    ->withErrors(['cuil' => 'Las horas a aportar deben ser '.$maxHoras.' - ya aporta '.$horasOtroProyecto.' al proyecto '. $proyectos])
                                    ->withInput();
                            }
                        }
                    }

                }
                else{
                    Log::info("No está en proyectos ".$unProyecto." horas: ".$input['horas']." max: ".$maxHoras);
                    if ($unProyecto){
                        if ($input['horas']){
                            if ($input['horas']>$maxHoras){
                                return redirect()->back()
                                    ->withErrors(['cuil' => 'Las horas a aportar deben ser '.$maxHoras])
                                    ->withInput();
                            }
                        }
                    }
                }
            }


        }catch(QueryException $ex){


                $error=$ex->getMessage();
                $ok=0;

        }




        if ($ok){
            try {
                $input['categoria_id']= $investigador->categoria_id;
                $input['sicadi_id']= $investigador->sicadi_id;
                $integrante = Integrante::create($input);
                // Guardar el primer título pasado en $request->titulo en la columna titulo_id del investigador
                if (!empty($request->titulos)) {
                    $integrante->titulo_id = $request->titulos[0];
                    $integrante->egresogrado = $request->egresos[0];
                    $integrante->carrera = null;
                    $integrante->total = null;
                    $integrante->materias = null;
                    $integrante->save();
                }

                // Guardar el primer título pasado en $request->titulopost en la columna titulopost_id del investigador
                if (!empty($request->tituloposts)) {
                    $integrante->titulopost_id = $request->tituloposts[0];
                    $integrante->egresoposgrado = $request->egresoposts[0];
                    $integrante->save();
                }




                if ($request->cargos[0]) {
                    $integrante->cargo_id = $request->cargos[0];
                    $integrante->deddoc = $request->deddocs[0];
                    $integrante->facultad_id = $request->facultads[0];
                    $integrante->universidad_id = $request->universidads[0];
                    $integrante->alta_cargo = $request->ingresos[0];

                    $integrante->save();
                }


                if ($request->carrerainvs[0]) {
                    $integrante->carrerainv_id = $request->carrerainvs[0];;
                    $integrante->organismo_id = $request->organismos[0];
                    $integrante->ingreso_carrerainv = $request->carringresos[0];;
                    $integrante->save();
                }



                if ($request->becas[0]) {
                    $integrante->beca = $request->becas[0];;
                    $integrante->institucion = $request->institucions[0];
                    $integrante->alta_beca = $request->becadesdes[0];
                    $integrante->baja_beca = $request->becahastas[0];
                    if ($integrante->alta_beca){
                        if ($integrante->alta_beca->gt(Carbon::parse($input['alta']))) {
                            // Actualizar la fecha de alta en $input['alta'] con la fecha de alta del cargo
                            $input['alta'] = $integrante->alta_beca;
                        }
                    }
                    $integrante->save();
                }

                // Obtener el ID del usuario autenticado
                $userId = Auth::id();

                // Crear registro en integrante_estados con el estado "Alta Creada" y el user_id
                $integrante->estados()->create([
                    'tipo' => $integrante->tipo,
                    'alta' => $integrante->alta,
                    'estado' => $integrante->estado,
                    'user_id' => $userId,
                    'horas' => $integrante->horas,
                    'categoria_id' => $integrante->categoria_id,
                    'sicadi_id' => $integrante->sicadi_id,
                    'deddoc' => $integrante->deddoc,
                    'cargo_id' => $integrante->cargo_id,
                    'facultad_id' => $integrante->facultad_id,
                    'unidad_id' => $integrante->unidad_id,
                    'carrerainv_id' => $integrante->carrerainv_id,
                    'organismo_id' => $integrante->organismo_id,
                    'unidad_id' => $integrante->unidad_id,
                    'institucion' => $integrante->institucion,
                    'beca' => $integrante->beca,
                    'desde' => Carbon::now(), // Establecer 'desde' como la fecha actual

                ]);

                DB::commit();
                $respuestaID = 'success';
                $respuestaMSJ = 'Integrante creado con éxito';

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

        return redirect()->route('integrantes.index', array('proyecto_id' => $proyecto_id))->with($respuestaID, $respuestaMSJ);

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
