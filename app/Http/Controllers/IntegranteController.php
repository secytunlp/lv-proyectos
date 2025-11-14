<?php

namespace App\Http\Controllers;

use App\Models\Cargo;
use App\Models\Carrerainv;
use App\Models\Categoria;
use App\Models\Integrante;
use App\Models\IntegranteEstado;
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
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ZipArchive;

use PDF;
use App\Traits\SanitizesInput;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use Spatie\Permission\Models\Role; // Importa la clase Role
use App\Mail\SolicitudEnviada;
//use Barryvdh\DomPDF\Facade as PDF;

class IntegranteController extends Controller
{
    use SanitizesInput;
    function __construct()
    {
        $this->middleware('permission:integrante-listar|integrante-crear|integrante-editar|integrante-eliminar', ['only' => ['index','store','dataTable','admitir']]);
        $this->middleware('permission:integrante-crear', ['only' => ['create','store','buscarInvestigador','generateAltaPDF','archivos']]);
        $this->middleware('permission:integrante-editar', ['only' => ['edit','update','enviar']]);
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
            ->with(['persona:id,apellido,nombre,cuil,email,nacimiento', 'titulos:id,nombre', 'tituloposts:id,nombre', 'cargos:id,nombre', 'carrerainvs:id,nombre', 'categorias:id,nombre', 'sicadis:id,nombre','becas:id,institucion,beca,desde,hasta'])
    ->get();

    // Mapear los resultados para incluir los campos necesarios en la respuesta JSON
    $resultados = $resultados->map(function($investigador) {
        $titulo = $investigador->titulos->first(); // Tomar solo el primer título
        $titulopost = $investigador->tituloposts->first(); // Tomar solo el primer título
        $cargo = $investigador->cargos()->where('activo', true)->orderBy('deddoc', 'asc')->orderBy('orden', 'asc')->first(); // Obtener el cargo activo con mayor dedicación

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
            'nacimiento' => $investigador->persona->nacimiento,
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
        $user = auth()->user();
        $selectedRoleId = session('selected_rol');
        if ($selectedRoleId != 1) {
            $query->where(function ($query) {
                $query->whereColumn('integrantes.alta', '!=', 'integrantes.baja')
                    ->orWhereNull('integrantes.alta')
                    ->orWhereNull('integrantes.baja');
            });

        }

        // Aplicar la búsqueda
        if (!empty($busqueda)) {
            $query->where(function ($query) use ($columnas, $busqueda) {
                foreach ($columnas as $columna) {
                    $query->orWhere($columna, 'like', "%$busqueda%");
                }
            });
        }


        if ($selectedRoleId == 2) {

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
            'tipo' => 'required',
            'apellido' => 'required',
            'cuil' => 'required|regex:/^\d{2}-\d{8}-\d{1}$/',
            'curriculum' => 'file|mimes:pdf,doc,docx|max:4048',
            'actividades' => 'file|mimes:pdf,doc,docx|max:4048',
            'resolucion' => 'file|mimes:pdf,doc,docx|max:4048',
        ];

        // Definir los mensajes de error personalizados
        $messages = [
            'cuil.regex' => 'El formato del CUIL es inválido.',
            'curriculum.mimes' => 'El archivo de curriculum debe ser un documento de tipo: pdf, doc, docx.',
            'curriculum.max' => 'El archivo de curriculum no debe ser mayor a 4 MB.',
            'actividades.mimes' => 'El archivo de Plan de trabajo debe ser un documento de tipo: pdf, doc, docx.',
            'actividades.max' => 'El archivo de Plan de trabajo no debe ser mayor a 4 MB.',
            'resolucion.mimes' => 'El archivo de Resolución Beca ó Certificado de alumno de Doctorado/Maestría debe ser un documento de tipo: pdf, doc, docx.',
            'resolucion.max' => 'El archivo de Resolución Beca ó Certificado de alumno de Doctorado/Maestría no debe ser mayor a 4 MB.',
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


        $input = $this->sanitizeInput($request->all());


        $proyecto_id=$input['proyecto_id'];

        // Crear la carpeta si no existe
        /*$destinationPath = public_path('/files/' . Constants::YEAR.'/'.$proyecto_id);
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0777, true);
        }*/


        $input['alta']= Constants::YEAR.'-01-01';
        $input['estado']= 'Alta Creada';
        // Manejo de archivos
        /*$input['curriculum'] ='';
        if ($files = $request->file('curriculum')) {
            $file = $request->file('curriculum');
            $name = 'CV_'.time().'.'.$file->getClientOriginalExtension();

            $file->move($destinationPath, $name);
            $input['curriculum'] = "files/".Constants::YEAR."$proyecto_id/$name";
        }*/

        $input['curriculum'] = '';
        if ($request->hasFile('curriculum')) {
            $file = $request->file('curriculum');
            $filename = 'CV_' . Str::uuid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('public/files/' . Constants::YEAR.'/'.$proyecto_id, $filename);
            $input['curriculum'] = Storage::url($path); // Genera URL tipo /storage/files/...
        }


        /*$input['actividades'] ='';
        if ($files = $request->file('actividades')) {
            $file = $request->file('actividades');
            $name = 'PLAN_'.time().'.'.$file->getClientOriginalExtension();

            $file->move($destinationPath, $name);
            $input['actividades'] = "files/".Constants::YEAR."$proyecto_id/$name";
        }*/

        $input['actividades'] = '';
        if ($request->hasFile('actividades')) {
            $file = $request->file('actividades');
            $filename = 'PLAN_' . Str::uuid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('public/files/' . Constants::YEAR.'/'.$proyecto_id, $filename);
            $input['actividades'] = Storage::url($path); // Genera URL tipo /storage/files/...
        }

        /*$input['resolucion'] ='';
        if ($files = $request->file('resolucion')) {
            $file = $request->file('resolucion');
            $name = 'RES_'.time().'.'.$file->getClientOriginalExtension();

            $file->move($destinationPath, $name);
            $input['resolucion'] = "files/".Constants::YEAR."$proyecto_id/$name";
        }*/

        $input['resolucion'] = '';
        if ($request->hasFile('resolucion')) {
            $file = $request->file('resolucion');
            $filename = 'RES_' . Str::uuid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('public/files/' . Constants::YEAR.'/'.$proyecto_id, $filename);
            $input['resolucion'] = Storage::url($path); // Genera URL tipo /storage/files/...
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
                //dd($investigador);


                // Almacenar el resultado de la validación en una variable
                $resultadoValidacion = $this->validarHorasGuardar($request, $investigador, $proyecto_id);

                // Verificar si la validación fue exitosa
                if ($resultadoValidacion !== 'validacion_exitosa') {
                    // Si no fue exitosa, retornar el resultado de la validación (que incluye el redirect()->back())
                    return $resultadoValidacion;
                }
            }

            $input['investigador_id'] = $investigador->id; // Asignar el nuevo ident al input

        }catch(QueryException $ex){


                $error=$ex->getMessage();
                $ok=0;

        }




        if ($ok){
            try {
                if ($investigador) {
                    // Paso 3: Buscar el integrante usando el investigador_id y el proyecto_id
                    $integrante = Integrante::where('investigador_id', $investigador->id)
                        ->where('proyecto_id', $proyecto_id)
                        ->whereColumn('alta', 'baja')
                        ->first();
                }




                $input['categoria_id']= $investigador->categoria_id;
                $input['sicadi_id']= $investigador->sicadi_id;
                // Si no se encuentra un integrante existente, crear uno nuevo
                if (empty($integrante)) {
                    $motivo='Nuevo integrante';
                    $integrante = Integrante::create($input);
                }
                else{
                    $integrante->baja=null;
                    $motivo='Reincorporar integrante';
                    $integrante->update($input);
                }
                $this->guardarIntegrante($request,$integrante,$input['alta']);


                $this->cambiarEstado($integrante,$motivo);


                DB::commit();
                $respuestaID = 'success';
                $respuestaMSJ = 'Alta creada con éxito';

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
    public function show($id)
    {
        $integrante = Integrante::find($id);

        $proyectoId = $integrante->proyecto_id;
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

        return view('integrantes.show',compact('titulos','tituloposts','facultades','cargos','universidades','unidads','carrerainvs','sicadis','years','organismos','categorias','sicadis','proyecto','integrante'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Proyecto  $proyecto
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $integrante = Integrante::find($id);

        $proyectoId = $integrante->proyecto_id;
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

        return view('integrantes.edit',compact('titulos','tituloposts','facultades','cargos','universidades','unidads','carrerainvs','sicadis','years','organismos','categorias','sicadis','proyecto','integrante'));

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
        // Definir las reglas de validación
        $rules = [
            'tipo' => 'required',
            'apellido' => 'required',
            'cuil' => 'required|regex:/^\d{2}-\d{8}-\d{1}$/',
            'curriculum' => 'file|mimes:pdf,doc,docx|max:4048',
            'actividades' => 'file|mimes:pdf,doc,docx|max:4048',
            'resolucion' => 'file|mimes:pdf,doc,docx|max:4048',
        ];

        // Definir los mensajes de error personalizados
        $messages = [
            'cuil.regex' => 'El formato del CUIL es inválido.',
            'curriculum.mimes' => 'El archivo de curriculum debe ser un documento de tipo: pdf, doc, docx.',
            'curriculum.max' => 'El archivo de curriculum no debe ser mayor a 4 MB.',
            'actividades.mimes' => 'El archivo de Plan de trabajo debe ser un documento de tipo: pdf, doc, docx.',
            'actividades.max' => 'El archivo de Plan de trabajo no debe ser mayor a 4 MB.',
            'resolucion.mimes' => 'El archivo de Resolución Beca ó Certificado de alumno de Doctorado/Maestría debe ser un documento de tipo: pdf, doc, docx.',
            'resolucion.max' => 'El archivo de Resolución Beca ó Certificado de alumno de Doctorado/Maestría no debe ser mayor a 4 MB.',
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


        $input = $this->sanitizeInput($request->all());

        $proyecto_id=$input['proyecto_id'];




        // Crear la carpeta si no existe
        /*$destinationPath = public_path('/files/' . Constants::YEAR.'/'.$proyecto_id);
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0777, true);
        }*/


        $integrante = Integrante::find($id);

        /*$input['alta']= Constants::YEAR.'-01-01';
        $input['estado']= 'Alta Creada';*/
        // Manejo de archivos
        //$input['curriculum'] ='';
        /*if ($files = $request->file('curriculum')) {
            // Eliminar el archivo anterior si existe
            if ($integrante && $integrante->curriculum) {
                $oldFile = public_path($integrante->curriculum);
                //log::info('Archivo: '.$oldFile );
                if (file_exists($oldFile)) {
                    unlink($oldFile);
                }
            }

            $file = $request->file('curriculum');
            $name = 'CV_'.time().'.'.$file->getClientOriginalExtension();

            $file->move($destinationPath, $name);
            $input['curriculum'] = "files/".Constants::YEAR."/$proyecto_id/$name";
        }*/

        if ($request->hasFile('curriculum')) {
            // Eliminar curriculum anterior si existe
            if (!empty($integrante->curriculum)) {
                $rutaAnterior = str_replace('/storage/', 'public/', $integrante->curriculum); // Ej: public/files/sicadi/2025/CV_123.pdf
                if (Storage::exists($rutaAnterior)) {
                    Storage::delete($rutaAnterior);
                }
            }

            $file = $request->file('curriculum');
            $filename = 'CV_' . Str::uuid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('public/files/' . Constants::YEAR.'/'.$proyecto_id, $filename);
            $input['curriculum'] = Storage::url($path); // Genera URL tipo /storage/files/...
        }
        if ($request->has('delete_cv') && $integrante->curriculum) {
            $rutaAnterior = str_replace('/storage/', 'public/', $integrante->curriculum); // Ej: public/images/sicadi/foto_xyz.png
            if (Storage::exists($rutaAnterior)) {
                Storage::delete($rutaAnterior);
            }
            $input['curriculum'] = null;
        }

        //$input['actividades'] ='';
        /*if ($files = $request->file('actividades')) {
            // Eliminar el archivo anterior si existe
            if ($integrante && $integrante->actividades) {
                $oldFile = public_path($integrante->actividades);
                if (file_exists($oldFile)) {
                    unlink($oldFile);
                }
            }
            $file = $request->file('actividades');
            $name = 'PLAN_'.time().'.'.$file->getClientOriginalExtension();

            $file->move($destinationPath, $name);
            $input['actividades'] = "files/".Constants::YEAR."/$proyecto_id/$name";
        }*/

        if ($request->hasFile('actividades')) {
            // Eliminar actividades anterior si existe
            if (!empty($integrante->actividades)) {
                $rutaAnterior = str_replace('/storage/', 'public/', $integrante->actividades); // Ej: public/files/sicadi/2025/CV_123.pdf
                if (Storage::exists($rutaAnterior)) {
                    Storage::delete($rutaAnterior);
                }
            }

            $file = $request->file('actividades');
            $filename = 'PLAN_' . Str::uuid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('public/files/' . Constants::YEAR.'/'.$proyecto_id, $filename);
            $input['actividades'] = Storage::url($path); // Genera URL tipo /storage/files/...
        }
        if ($request->has('delete_actividades') && $integrante->actividades) {
            $rutaAnterior = str_replace('/storage/', 'public/', $integrante->actividades); // Ej: public/images/sicadi/foto_xyz.png
            if (Storage::exists($rutaAnterior)) {
                Storage::delete($rutaAnterior);
            }
            $input['actividades'] = null;
        }

        /*if ($files = $request->file('resolucion')) {
            // Eliminar el archivo anterior si existe
            if ($integrante && $integrante->resolucion) {
                $oldFile = public_path($integrante->resolucion);
                if (file_exists($oldFile)) {
                    unlink($oldFile);
                }
            }
            $file = $request->file('resolucion');
            $name = 'RES_'.time().'.'.$file->getClientOriginalExtension();

            $file->move($destinationPath, $name);
            $input['resolucion'] = "files/".Constants::YEAR."/$proyecto_id/$name";
        }*/

        if ($request->hasFile('resolucion')) {
            // Eliminar resolucion anterior si existe
            if (!empty($integrante->resolucion)) {
                $rutaAnterior = str_replace('/storage/', 'public/', $integrante->resolucion); // Ej: public/files/sicadi/2025/CV_123.pdf
                if (Storage::exists($rutaAnterior)) {
                    Storage::delete($rutaAnterior);
                }
            }

            $file = $request->file('resolucion');
            $filename = 'RES_' . Str::uuid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('public/files//' . Constants::YEAR.'/'.$proyecto_id, $filename);
            $input['resolucion'] = Storage::url($path); // Genera URL tipo /storage/files/...
        }
        if ($request->has('delete_resolucion') && $integrante->resolucion) {
            $rutaAnterior = str_replace('/storage/', 'public/', $integrante->resolucion); // Ej: public/images/sicadi/foto_xyz.png
            if (Storage::exists($rutaAnterior)) {
                Storage::delete($rutaAnterior);
            }
            $input['resolucion'] = null;
        }

        DB::beginTransaction();
        $ok = 1;

        try {

            $investigador = $integrante->investigador;

            //dd($investigador);
            $input['investigador_id'] = $investigador->id; // Asignar el nuevo ident al input
            // Almacenar el resultado de la validación en una variable
            $resultadoValidacion = $this->validarHorasGuardar($request, $investigador, $proyecto_id);

            // Verificar si la validación fue exitosa
            if ($resultadoValidacion !== 'validacion_exitosa') {
                // Si no fue exitosa, retornar el resultado de la validación (que incluye el redirect()->back())
                return $resultadoValidacion;
            }


        }catch(QueryException $ex){


            $error=$ex->getMessage();
            $ok=0;

        }




        if ($ok){
            try {
                $input['categoria_id']= $integrante->categoria_id;
                $input['sicadi_id']= $integrante->sicadi_id;

                $integrante->update($input);

                $this->guardarIntegrante($request,$integrante,$integrante->alta);


                $this->cambiarEstado($integrante,'Modificación de alta');



                DB::commit();
                $respuestaID = 'success';
                $respuestaMSJ = 'Alta modificada con éxito';

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

    public function generateAltaPDF(Request $request, $attach = false)
    {
        return $this->generatePDF($request,'alta',$attach);
    }

    public function generateBajaPDF(Request $request, $attach = false)
    {
        return $this->generatePDF($request,'baja',$attach);

    }

    public function generateCambioPDF(Request $request, $attach = false)
    {
        return $this->generatePDF($request,'cambio',$attach);

    }

    public function generateCambioHSPDF(Request $request, $attach = false)
    {
        return $this->generatePDF($request,'cambioHS',$attach);

    }

    public function generateCambioTipoPDF(Request $request, $attach = false)
    {
        return $this->generatePDF($request,'cambioTipo',$attach,true);
    }

    public function generatePDF(Request $request, $tipoStr,$attach = false,$anterior=false)
    {
        $integranteId = $request->query('integrante_id');




        // Consulta base
        $query = Integrante::select('integrantes.id as id','investigadors.id as investigador_id', 'proyectos.id as proyecto_id','integrantes.tipo as tipo', DB::raw("CONCAT(personas.apellido, ', ', personas.nombre) as persona_apellido"), 'cuil', 'categorias.nombre as categoria_nombre', 'sicadis.nombre as sicadi_nombre', 'cargos.nombre as cargo_nombre','integrantes.deddoc', 'integrantes.beca as beca','integrantes.institucion','integrantes.alta_beca','integrantes.baja_beca', 'carrerainvs.nombre as carrerainv_nombre', 'organismos.codigo as organismo_nombre','integrantes.alta as alta','integrantes.baja as baja','integrantes.cambio as cambio', 'integrantes.horas as horas', 'integrantes.horas_anteriores as horas_anteriores', 'facultads.nombre as facultad_nombre', 'integrantes.estado as estado', 'titulos.nombre as titulogrado_nombre', 'pos.nombre as tituloposgrado_nombre', 'unidads.nombre as unidad_nombre', 'unidads.sigla as unidad_sigla', 'universidads.nombre as universidad_nombre','integrantes.carrera','integrantes.total','integrantes.materias','integrantes.estado','integrantes.reduccion','integrantes.consecuencias','integrantes.motivos')
            ->leftJoin('categorias', 'integrantes.categoria_id', '=', 'categorias.id')
            ->leftJoin('investigadors', 'integrantes.investigador_id', '=', 'investigadors.id')
            ->leftJoin('proyectos', 'integrantes.proyecto_id', '=', 'proyectos.id')
            ->leftJoin('personas', 'investigadors.persona_id', '=', 'personas.id')
            ->leftJoin('sicadis', 'integrantes.sicadi_id', '=', 'sicadis.id')
            ->leftJoin('cargos', 'integrantes.cargo_id', '=', 'cargos.id')
            ->leftJoin('carrerainvs', 'integrantes.carrerainv_id', '=', 'carrerainvs.id')
            ->leftJoin('organismos', 'integrantes.organismo_id', '=', 'organismos.id')
            ->leftJoin('titulos', 'integrantes.titulo_id', '=', 'titulos.id')
            ->leftJoin('unidads', 'integrantes.unidad_id', '=', 'unidads.id')
            ->leftJoin('universidads', 'integrantes.universidad_id', '=', 'universidads.id')
            ->leftJoin('titulos as pos', 'integrantes.titulopost_id', '=', 'pos.id')
            ->leftJoin('facultads', 'integrantes.facultad_id', '=', 'facultads.id');



        $query->where('integrantes.id', $integranteId);



        // Obtener solo los elementos paginados
        $datos = $query->first();
        //dd($datos);
        //$integrante = Integrante::findOrFail($integranteId);
        //dd($datos);

        $proyecto = Proyecto::findOrFail($datos->proyecto_id);
        //dd($proyecto);
        $directorQuery = Integrante::select(DB::raw("CONCAT(personas.apellido, ', ', personas.nombre) as director_apellido, personas.cuil as director_cuil"))
            ->leftJoin('investigadors', 'integrantes.investigador_id', '=', 'investigadors.id')
            ->leftJoin('proyectos', 'integrantes.proyecto_id', '=', 'proyectos.id')
            ->leftJoin('personas', 'investigadors.persona_id', '=', 'personas.id');
        $directorQuery->where('integrantes.tipo', 'Director');
        $directorQuery->where('integrantes.proyecto_id', $datos->proyecto_id);
        $director = $directorQuery->first();

        $selectedRoleId = session('selected_rol');
        if ($selectedRoleId==2){
            $user = auth()->user();

            if ($director->director_cuil!=$user->cuil){
                abort(403, 'No autorizado.');
            }

        }


        $proyecto_id=$datos->proyecto_id;
        $otrosProyecto = Integrante::where('investigador_id', $datos->investigador_id)
            ->where(function ($query) {
                $query->where('estado', '!=', 'Baja Creada')
                    ->orWhere('estado', '!=', 'Baja Recibida')
                    ->orWhereNull('estado') // Agregamos los que tengan estado = null
                    ->orWhere('baja', '>', Carbon::now()->format('Y-m-d'))
                    ->orWhereNull('baja')
                    ->orWhere('baja', '0000-00-00');
            })
            ->whereHas('proyecto', function ($query) use ($proyecto_id) {
                $query->where('estado', 'Acreditado')
                    ->where('id', '<>', $proyecto_id)
                    ->where('fin', '>', Carbon::now()->subYear()->endOfYear()->format('Y-m-d'));
            })
            ->get();
        $otroProyecto=array();
// Acceder al director del proyecto en el resultado
        foreach ($otrosProyecto as $intProyecto) {
            $proyectoArray=array();
            $proyectoArray['horas']=$intProyecto->horas;
            $proyectoArray['tipo']=$intProyecto->tipo;
            $proy = Proyecto::findOrFail($intProyecto->proyecto_id);
            $proyectoArray['codigo']=$proy->codigo;
            $proyectoArray['titulo']=$proy->titulo;

            //dd($proyecto);
            $directorPQuery = Integrante::select(DB::raw("CONCAT(personas.apellido, ', ', personas.nombre) as director_apellido"))
                ->leftJoin('investigadors', 'integrantes.investigador_id', '=', 'investigadors.id')
                ->leftJoin('proyectos', 'integrantes.proyecto_id', '=', 'proyectos.id')
                ->leftJoin('personas', 'investigadors.persona_id', '=', 'personas.id');
            $directorPQuery->where('integrantes.tipo', 'Director');
            $directorPQuery->where('integrantes.proyecto_id', $intProyecto->proyecto_id);
            $dir = $directorPQuery->first();
            $baja = (($intProyecto->baja)&&($intProyecto->baja!='0000-00-00'))?$intProyecto->baja:$proy->fin;
            $proyectoArray['director']=$dir->director_apellido;
            $proyectoArray['periodo']=date('d/m/Y', strtotime($intProyecto->alta)).' - '.date('d/m/Y', strtotime($baja));
            $otroProyecto[]=$proyectoArray;
        }

        $estadoFiltrado=array();
        if ($anterior){
            // Obtener los estados en orden descendente por id
            $estados = IntegranteEstado::where('integrante_id', $integranteId)
                ->orderBy('id', 'desc')
                ->get();

// Encontrar el índice del registro con el comentario "Iniciar cambio de tipo"
            $index = $estados->search(function ($estado) {
                return $estado->comentarios === "Iniciar cambio de tipo";
            });

// Obtener el siguiente registro después del encontrado
            $estadoFiltrado = null;
            if ($index !== false && $index + 1 < $estados->count()) {
                $estadoFiltrado = $estados->get($index + 1);
            }
        }

        switch ($tipoStr) {
            case 'cambioTipo':
                $fecha = $datos->cambio;
                $template = 'integrantes.pdfcambioTipo';
                break;
            case 'alta':
                $fecha = $datos->alta;
                $template = 'integrantes.pdfalta';
                break;
            case 'baja':
                $fecha = $datos->baja;
                $template = 'integrantes.pdfbaja';
                break;
            case 'cambio':
                $fecha = $datos->cambio;
                $template = 'integrantes.pdfcambio';
                break;
            case 'cambioHS':
                $fecha = $datos->cambio;
                $template = 'integrantes.pdfcambioHS';
                break;

        }

        $data = [
            'tipoIntegrante' => ($datos->tipo=='Colaborador')?'COLABORADOR':'INTEGRANTE',
            'facultad' => $datos->facultad_nombre,
            'codigo' => $proyecto->codigo,
            'duracion' => ($proyecto->duracion==2)?'BIANUAL':'TETRA ANUAL',
            'denominacion' => $proyecto->titulo,
            'inicio' => $proyecto->inicio,
            'fin' => $proyecto->fin,
            'director' => $director->director_apellido,
            'integrante' => $datos->persona_apellido,
            'cuil' => $datos->cuil,
            'categoria_spu' => $datos->categoria_nombre,
            'categoria_sicadi' => $datos->categoria_sicadi,
            'titulo_grado' => $datos->titulogrado_nombre,
            'titulo_posgrado' => $datos->tituloposgrado_nombre,
            'cargo_docente' => $datos->cargo_nombre,
            'dedicacion_docente' => $datos->deddoc,
            'carrera_inv' => $datos->carrerainv_nombre,
            'organismo' => $datos->organismo_nombre,
            'becario' => ($datos->beca)?'SI':'NO',
            'beca' => $datos->beca,
            'institucion' => $datos->institucion,
            'alta_beca' => $datos->alta_beca,
            'baja_beca' => $datos->baja_beca,
            'unidad' => $datos->unidad_nombre.' - '.$datos->unidad_sigla,
            'fecha' => $fecha,
            'universidad' => $datos->universidad_nombre,
            'horas' => $datos->horas,
            'horas_anteriores' => $datos->horas_anteriores,
            'tipo_investigador' => $datos->tipo,
            'tipo_investigador_anterior' => (!empty($estadoFiltrado))?$estadoFiltrado->tipo:'',
            'estudiante' => ($datos->titulogrado_nombre)?'NO':'SI',
            'carrera' => $datos->carrera,
            'total' => $datos->total.'/'.$datos->materias,
            'otroProyecto' => $otroProyecto,
            'estado' => $datos->estado,
            'reduccion' => $datos->reduccion,
            'consecuencias' => $datos->consecuencias,
            'motivos' => $datos->motivos,
        ];
        //dd($data);




        $pdf = PDF::loadView($template, $data);

        $pdfPath = $tipoStr.'_' . $datos->cuil . '.pdf';

        if ($attach) {
            $fullPath = public_path('/temp/' . $pdfPath);
            $pdf->save($fullPath);
            return $fullPath; // Devuelve la ruta del archivo para su uso posterior
        } else {

            return $pdf->download($pdfPath);
        }

        // Renderiza la vista de previsualización para HTML
        //return view('integrantes.alta', $data);
    }


    public function archivos(Request $request)
    {
        $integranteId = $request->query('integrante_id');

        $integrante = Integrante::findOrFail($integranteId);

        $files = [
            'curriculum' => $integrante->curriculum,
            'actividades' => $integrante->actividades,
            'resolucion' => $integrante->resolucion,
            // Agrega aquí otros archivos que necesites
        ];

        // Filtrar archivos válidos
        $validFiles = array_filter($files, function($filePath) {
            return $filePath && file_exists(public_path($filePath));
        });

        // Verificar si hay archivos válidos
        if (empty($validFiles)) {
            return response()->json(['message' => 'No hay archivos para descargar.'], 404);
        }

        $zip = new ZipArchive;
        $zipFileName = 'archivos_integrante_' . $integranteId . '.zip';
        $zipFilePath = public_path('temp/' . $zipFileName);

        if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            foreach ($files as $fileKey => $filePath) {
                if ($filePath && file_exists(public_path( $filePath))) {
                    $zip->addFile(public_path($filePath), $fileKey . '_' . basename($filePath));
                }
            }
            $zip->close();
        } else {
            abort(500, 'No se pudo crear el archivo ZIP.');
        }

        return response()->download($zipFilePath)->deleteFileAfterSend(true);
    }

    public function enviar($id)
    {
        ///$integranteId = $request->query('integrante_id');
        $integrante = Integrante::findOrFail($id);
        $proyecto_id = $integrante->proyecto_id;
        $error='';
        $ok = 1;

        $errores = [];

        $today = now();
        $cierreDate = \Carbon\Carbon::parse(Constants::CIERRE);
        if ($today->gt($cierreDate)) {
            $errores[] ='La convocatoria no está vigente';
        }


        if (empty($integrante->tipo) || empty($integrante->horas)) {
            $errores[] = 'Complete todos los campos de la pestaña Proyectos';
        }

        if (empty($integrante->curriculum)) {
            $errores[] = 'Falta subir el Curriculum';
        } else {
            $filePath = public_path($integrante->curriculum);
            if (!file_exists($filePath)) {
                $errores[] = 'Hubo un error al subir el Curriculum, intente nuevamente, si el problema persiste envíenos un mail a proyectos.secyt@presi.unlp.edu.ar';
            }
        }

        if (empty($integrante->actividades)) {
            $errores[] = 'Falta subir el Plan de Trabajo';
        } else {
            $filePath = public_path($integrante->actividades);
            if (!file_exists($filePath)) {
                $errores[] = 'Hubo un error al subir el Plan de Trabajo, intente nuevamente, si el problema persiste envíenos un mail a proyectos.secyt@presi.unlp.edu.ar';
            }
        }

        $this->validarEnviar($integrante,$errores);



        if (!empty($errores)) {
            return redirect()->back()->withErrors($errores)->withInput();
        }


        DB::beginTransaction();


        try {

            $investigador = $integrante->investigador;


            // Almacenar el resultado de la validación en una variable
            $resultadoValidacion = $this->validarHorasEnviar($integrante, $investigador, $proyecto_id);

            // Verificar si la validación fue exitosa
            if ($resultadoValidacion !== 'validacion_exitosa') {
                // Si no fue exitosa, retornar el resultado de la validación (que incluye el redirect()->back())
                return $resultadoValidacion;
            }


        }catch(QueryException $ex){


            $error=$ex->getMessage();
            $ok=0;

        }




        if ($ok){
            try {

                $integrante->estado = 'Alta Recibida';
                $integrante->save();

                $this->cambiarEstado($integrante,'Envio de alta');



                $integranteMail = ($integrante->tipo=='Colaborador')?$integrante->tipo:'Integrante';


                $userId = Auth::id();
                $user = User::find($userId); // Obtener el usuario por su ID
                // Preparar datos para el correo
                $datosCorreo = [
                    'from_email' => $user->email,
                    'from_name' => $user->name,
                    'asunto' => 'Solicitud de ALTA de '.$integranteMail,
                    'codigo' => $integrante->proyecto->codigo,
                    'integranteMail' => $integranteMail,
                    'integrante' => $integrante->investigador->persona->apellido.', '.$integrante->investigador->persona->nombre.' ('.$integrante->investigador->persona->cuil.')',
                    'tipo' => 'Alta',
                    'fecha' => Carbon::parse($integrante->alta)->format('d/m/Y'), // Formatear la fecha en el formato deseado
                    'comment' => '',
                ];



                // Generar el PDF y obtener la ruta
                $pdfPath = $this->generateAltaPDF(new Request(['integrante_id' => $integrante->id]), true);

                $this->enviarCorreosAlUsuario($datosCorreo,$integrante,$userId,true,$pdfPath);
                // Enviar correo electrónico al usuario
                //Mail::to($user->email)->send(new SolicitudEnviada($datosCorreo,$integrante,true,$pdfPath));

                // Enviar correo electrónico a tu servidor (ejemplo)
                //Mail::to('marcosp@presi.unlp.edu.ar')->send(new SolicitudEnviada($datosCorreo,$integrante,true,$pdfPath));

                DB::commit();
                // Eliminar el archivo PDF temporal
                if (file_exists($pdfPath)) {
                    unlink($pdfPath);
                }
                $respuestaID = 'success';
                $respuestaMSJ = 'Alta enviada con éxito';

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

    public function destroy($id)
    {
        $integrante = Integrante::findOrFail($id);

        $proyecto_id = $integrante->proyecto_id;
        DB::beginTransaction();
        try {

            $integrante->estado = '';
            $integrante->baja=$integrante->alta;
            $integrante->save();

            $this->cambiarEstado($integrante,'Anulación de alta');






            DB::commit();
            $respuestaID = 'success';
            $respuestaMSJ = 'Alta anulada con éxito';

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

        return redirect()->route('integrantes.index', array('proyecto_id' => $proyecto_id))->with($respuestaID, $respuestaMSJ);
    }

    public function admitir($id)
    {
        $integrante = Integrante::findOrFail($id);

        $proyecto_id = $integrante->proyecto_id;
        DB::beginTransaction();
        try {

            $integrante->estado = '';
            //$integrante->baja=$integrante->alta;
            $integrante->save();
            $investigador = Investigador::find($integrante->investigador_id);


            $this->actualizarInvestigador($integrante,$investigador);


            $this->cambiarEstado($integrante,'Confirmación de alta');


            $integranteMail = ($integrante->tipo=='Colaborador')?$integrante->tipo:'Integrante';

            // Preparar datos para el correo
            $datosCorreo = [
                'from_email' => Constants::MAIL_PROYECTOS,
                'from_name' => Constants::NOMBRE_PROYECTOS,
                'asunto' => 'Confirmación de solicitud de ALTA de '.$integranteMail,
                'codigo' => $integrante->proyecto->codigo,
                'integranteMail' => $integranteMail,
                'integrante' => $integrante->investigador->persona->apellido.', '.$integrante->investigador->persona->nombre.' ('.$integrante->investigador->persona->cuil.')',
                'tipo' => 'Alta',
                'fecha' => Carbon::parse($integrante->alta)->format('d/m/Y'), // Formatear la fecha en el formato deseado
                'comment' => '',
            ];


            // Llama a la función enviarCorreos
            $this->enviarCorreos($proyecto_id, $datosCorreo, $integrante);


            // Enviar correo electrónico a tu servidor (ejemplo)
            //Mail::to('mpinia@hotmail.com')->send(new SolicitudEnviada($datosCorreo,$integrante));


            DB::commit();
            $respuestaID = 'success';
            $respuestaMSJ = 'Confirmación con éxito';

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
        return redirect()->route('integrantes.index', array('proyecto_id' => $proyecto_id))->with($respuestaID, $respuestaMSJ);
    }

    public function rechazar($id)
    {
        $integrante = Integrante::find($id);

        $proyectoId = $integrante->proyecto_id;
        $proyecto = null;

        // Si se proporciona un ID de proyecto, buscalo en la base de datos
        if ($proyectoId) {
            $proyecto = Proyecto::findOrFail($proyectoId);
        }


        return view('integrantes.deny',compact('proyecto','integrante'));

    }

    public function saveDeny(Request $request, $id)
    {
        $this->validate($request, [
            'comentarios' => 'required'
        ]);

        $input = $this->sanitizeInput($request->all());

        $integrante = Integrante::findOrFail($id);

        $proyecto_id = $integrante->proyecto_id;
        DB::beginTransaction();
        try {

            $integrante->estado = '';
            $integrante->baja=$integrante->alta;
            $integrante->save();

            $this->cambiarEstado($integrante,$input['comentarios']);

            $integranteMail = ($integrante->tipo=='Colaborador')?$integrante->tipo:'Integrante';

            // Preparar datos para el correo
            $datosCorreo = [
                'from_email' => Constants::MAIL_PROYECTOS,
                'from_name' => Constants::NOMBRE_PROYECTOS,
                'asunto' => 'Rechazo de solicitud de ALTA de '.$integranteMail,
                'codigo' => $integrante->proyecto->codigo,
                'integranteMail' => $integranteMail,
                'integrante' => $integrante->investigador->persona->apellido.', '.$integrante->investigador->persona->nombre.' ('.$integrante->investigador->persona->cuil.')',
                'tipo' => 'Alta',
                'fecha' => Carbon::parse($integrante->alta)->format('d/m/Y'), // Formatear la fecha en el formato deseado
                'comment' => $input['comentarios'],
            ];

            // Llama a la función enviarCorreos
            $this->enviarCorreos($proyecto_id, $datosCorreo, $integrante);


            DB::commit();
            $respuestaID = 'success';
            $respuestaMSJ = 'Rechazada con éxito';

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
        return redirect()->route('integrantes.index', array('proyecto_id' => $proyecto_id))->with($respuestaID, $respuestaMSJ);
    }

    public function baja($id)
    {
        $integrante = Integrante::find($id);

        $proyectoId = $integrante->proyecto_id;
        $proyecto = null;

        // Si se proporciona un ID de proyecto, buscalo en la base de datos
        if ($proyectoId) {
            $proyecto = Proyecto::findOrFail($proyectoId);
        }


        return view('integrantes.baja',compact('proyecto','integrante'));

    }

    public function remove(Request $request, $id)
    {

        // Extender el validador para incluir la regla de año actual
        Validator::extend('current_year', function ($attribute, $value, $parameters, $validator) {
            return date('Y', strtotime($value)) == date('Y');
        });

        // Definir las reglas de validación
        $rules = [
            'baja' => 'required|date|current_year',
            'consecuencias' => 'required',
            'motivos' => 'required',
            'minint' => 'accepted',
            'minded' => 'accepted',
        ];

        // Definir los mensajes de error personalizados
        $messages = [
            'baja.required' => 'El campo Fecha de baja es obligatorio.',
            'baja.current_year' => 'Fecha de baja fuera del período.',
            'minint.accepted' => 'Debe marcar el checkbox de mínimo de integrantes.',
            'minded.accepted' => 'Debe marcar el checkbox de mínimo de mayor dedicación.',
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

        $integrante = Integrante::findOrFail($id);

        $proyecto_id = $integrante->proyecto_id;
        DB::beginTransaction();
        try {

            $integrante->estado = 'Baja Creada';
            $integrante->baja=$input['baja'];
            $integrante->consecuencias=$input['consecuencias'];
            $integrante->motivos=$input['motivos'];
            $integrante->save();

            $this->cambiarEstado($integrante,'Baja creada');

            DB::commit();
            $respuestaID = 'success';
            $respuestaMSJ = 'Baja creada con éxito';

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


        return redirect()->route('integrantes.index', array('proyecto_id' => $proyecto_id))->with($respuestaID, $respuestaMSJ);
    }

    public function anular($id)
    {
        $integrante = Integrante::findOrFail($id);

        $proyecto_id = $integrante->proyecto_id;
        DB::beginTransaction();
        try {

            $integrante->estado = '';

            $integrante->baja=null;
            $integrante->consecuencias=null;
            $integrante->motivos=null;
            $integrante->save();

            $this->cambiarEstado($integrante,'Anulación de baja');

            DB::commit();
            $respuestaID = 'success';
            $respuestaMSJ = 'Baja anulada con éxito';

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


        return redirect()->route('integrantes.index', array('proyecto_id' => $proyecto_id))->with($respuestaID, $respuestaMSJ);
    }

    public function enviarBaja($id)
    {
        ///$integranteId = $request->query('integrante_id');
        $integrante = Integrante::findOrFail($id);
        $proyecto_id = $integrante->proyecto_id;
        $error='';
        $ok = 1;

        $errores = [];

        /*$today = now();
        $cierreDate = \Carbon\Carbon::parse(Constants::CIERRE);
        if ($today->gt($cierreDate)) {
            $errores[] ='La convocatoria no está vigente';
        }*/





        DB::beginTransaction();

            try {

                $integrante->estado = 'Baja Recibida';
                $integrante->save();
                $this->cambiarEstado($integrante,'Envio de baja');

                // Obtener el ID del usuario autenticado
                $userId = Auth::id();
                $user = User::find($userId);
                // Generar el PDF y obtener la ruta
                $pdfPath = $this->generateBajaPDF(new Request(['integrante_id' => $integrante->id]), true);

                $integranteMail = ($integrante->tipo=='Colaborador')?$integrante->tipo:'Integrante';

                // Preparar datos para el correo
                $datosCorreo = [
                    'from_email' => $user->email,
                    'from_name' => $user->name,
                    'asunto' => 'Solicitud de Baja de '.$integranteMail,
                    'codigo' => $integrante->proyecto->codigo,
                    'integranteMail' => $integranteMail,
                    'integrante' => $integrante->investigador->persona->apellido.', '.$integrante->investigador->persona->nombre.' ('.$integrante->investigador->persona->cuil.')',
                    'tipo' => 'Baja',
                    'fecha' => Carbon::parse($integrante->baja)->format('d/m/Y'), // Formatear la fecha en el formato deseado
                    'comment' => '',
                ];


                $this->enviarCorreosAlUsuario($datosCorreo,$integrante,$userId,true,$pdfPath);

                DB::commit();
                // Eliminar el archivo PDF temporal
                if (file_exists($pdfPath)) {
                    unlink($pdfPath);
                }
                $respuestaID = 'success';
                $respuestaMSJ = 'Baja enviada con éxito';

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


        return redirect()->route('integrantes.index', array('proyecto_id' => $proyecto_id))->with($respuestaID, $respuestaMSJ);
    }

    public function admitirBaja($id)
    {
        $integrante = Integrante::findOrFail($id);

        $proyecto_id = $integrante->proyecto_id;
        DB::beginTransaction();
        try {

            $integrante->estado = '';
            //$integrante->baja=$integrante->alta;
            $integrante->save();

            $this->cambiarEstado($integrante,'Confirmación de baja');


            $integranteMail = ($integrante->tipo=='Colaborador')?$integrante->tipo:'Integrante';

            // Preparar datos para el correo
            $datosCorreo = [
                'from_email' => Constants::MAIL_PROYECTOS,
                'from_name' => Constants::NOMBRE_PROYECTOS,
                'asunto' => 'Confirmación de solicitud de BAJA de '.$integranteMail,
                'codigo' => $integrante->proyecto->codigo,
                'integranteMail' => $integranteMail,
                'integrante' => $integrante->investigador->persona->apellido.', '.$integrante->investigador->persona->nombre.' ('.$integrante->investigador->persona->cuil.')',
                'tipo' => 'Baja',
                'fecha' => Carbon::parse($integrante->baja)->format('d/m/Y'), // Formatear la fecha en el formato deseado
                'comment' => '',
            ];


            // Llama a la función enviarCorreos
            $this->enviarCorreos($proyecto_id, $datosCorreo, $integrante);


            DB::commit();
            $respuestaID = 'success';
            $respuestaMSJ = 'Confirmación con éxito';

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


        return redirect()->route('integrantes.index', array('proyecto_id' => $proyecto_id))->with($respuestaID, $respuestaMSJ);
    }

    public function rechazarBaja($id)
    {
        $integrante = Integrante::find($id);

        $proyectoId = $integrante->proyecto_id;
        $proyecto = null;

        // Si se proporciona un ID de proyecto, buscalo en la base de datos
        if ($proyectoId) {
            $proyecto = Proyecto::findOrFail($proyectoId);
        }


        return view('integrantes.denyBaja',compact('proyecto','integrante'));

    }

    public function saveDenyBaja(Request $request, $id)
    {
        $this->validate($request, [
            'comentarios' => 'required'
        ]);

        $input = $this->sanitizeInput($request->all());

        $integrante = Integrante::findOrFail($id);

        $proyecto_id = $integrante->proyecto_id;
        DB::beginTransaction();
        try {

            $integrante->estado = '';
            $fecha = $integrante->baja;
            $integrante->baja=null;
            $integrante->save();

            $this->cambiarEstado($integrante,$input['comentarios']);

            $integranteMail = ($integrante->tipo=='Colaborador')?$integrante->tipo:'Integrante';

            // Preparar datos para el correo
            $datosCorreo = [
                'from_email' => Constants::MAIL_PROYECTOS,
                'from_name' => Constants::NOMBRE_PROYECTOS,
                'asunto' => 'Rechazo de solicitud de BAJA de '.$integranteMail,
                'codigo' => $integrante->proyecto->codigo,
                'integranteMail' => $integranteMail,
                'integrante' => $integrante->investigador->persona->apellido.', '.$integrante->investigador->persona->nombre.' ('.$integrante->investigador->persona->cuil.')',
                'tipo' => 'Baja',
                'fecha' => Carbon::parse($fecha)->format('d/m/Y'), // Formatear la fecha en el formato deseado
                'comment' => $input['comentarios'],
            ];

            // Llama a la función enviarCorreos
            $this->enviarCorreos($proyecto_id, $datosCorreo, $integrante);


            DB::commit();
            $respuestaID = 'success';
            $respuestaMSJ = 'Rechazada con éxito';

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


        return redirect()->route('integrantes.index', array('proyecto_id' => $proyecto_id))->with($respuestaID, $respuestaMSJ);
    }

    public function cambio($id)
    {
        $integrante = Integrante::find($id);

        $proyectoId = $integrante->proyecto_id;
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

        return view('integrantes.cambio',compact('titulos','tituloposts','facultades','cargos','universidades','unidads','carrerainvs','sicadis','years','organismos','categorias','sicadis','proyecto','integrante'));

    }

    public function cambiar(Request $request, $id)
    {
        // Definir las reglas de validación
        $rules = [
            'tipo' => 'required',

            'curriculum' => 'file|mimes:pdf,doc,docx|max:4048',
            'actividades' => 'file|mimes:pdf,doc,docx|max:4048',
            'resolucion' => 'file|mimes:pdf,doc,docx|max:4048',
        ];

        // Definir los mensajes de error personalizados
        $messages = [

            'curriculum.mimes' => 'El archivo de curriculum debe ser un documento de tipo: pdf, doc, docx.',
            'curriculum.max' => 'El archivo de curriculum no debe ser mayor a 4 MB.',
            'actividades.mimes' => 'El archivo de Plan de trabajo debe ser un documento de tipo: pdf, doc, docx.',
            'actividades.max' => 'El archivo de Plan de trabajo no debe ser mayor a 4 MB.',
            'resolucion.mimes' => 'El archivo de Resolución Beca ó Certificado de alumno de Doctorado/Maestría debe ser un documento de tipo: pdf, doc, docx.',
            'resolucion.max' => 'El archivo de Resolución Beca ó Certificado de alumno de Doctorado/Maestría no debe ser mayor a 4 MB.',
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


        $input = $this->sanitizeInput($request->all());

        $proyecto_id=$input['proyecto_id'];




        // Crear la carpeta si no existe
        /*$destinationPath = public_path('/files/' . Constants::YEAR.'/'.$proyecto_id);
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0777, true);
        }*/
        $input['alta']= Constants::YEAR.'-01-01';

        $integrante = Integrante::find($id);

        /*$input['alta']= Constants::YEAR.'-01-01';
        $input['estado']= 'Alta Creada';*/
        // Manejo de archivos
        //$input['curriculum'] ='';
        if ($request->hasFile('curriculum')) {
            // Eliminar curriculum anterior si existe
            if (!empty($integrante->curriculum)) {
                $rutaAnterior = str_replace('/storage/', 'public/', $integrante->curriculum); // Ej: public/files/sicadi/2025/CV_123.pdf
                if (Storage::exists($rutaAnterior)) {
                    Storage::delete($rutaAnterior);
                }
            }

            $file = $request->file('curriculum');
            $filename = 'CV_' . Str::uuid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('public/files//' . Constants::YEAR.'/'.$proyecto_id, $filename);
            $input['curriculum'] = Storage::url($path); // Genera URL tipo /storage/files/...
        }
        if ($request->has('delete_cv') && $integrante->curriculum) {
            $rutaAnterior = str_replace('/storage/', 'public/', $integrante->curriculum); // Ej: public/images/sicadi/foto_xyz.png
            if (Storage::exists($rutaAnterior)) {
                Storage::delete($rutaAnterior);
            }
            $input['curriculum'] = null;
        }

        //$input['actividades'] ='';
        /*if ($files = $request->file('actividades')) {
            // Eliminar el archivo anterior si existe
            if ($integrante && $integrante->actividades) {
                $oldFile = public_path($integrante->actividades);
                if (file_exists($oldFile)) {
                    unlink($oldFile);
                }
            }
            $file = $request->file('actividades');
            $name = 'PLAN_'.time().'.'.$file->getClientOriginalExtension();

            $file->move($destinationPath, $name);
            $input['actividades'] = "files/".Constants::YEAR."/$proyecto_id/$name";
        }*/

        if ($request->hasFile('actividades')) {
            // Eliminar actividades anterior si existe
            if (!empty($integrante->actividades)) {
                $rutaAnterior = str_replace('/storage/', 'public/', $integrante->actividades); // Ej: public/files/sicadi/2025/CV_123.pdf
                if (Storage::exists($rutaAnterior)) {
                    Storage::delete($rutaAnterior);
                }
            }

            $file = $request->file('actividades');
            $filename = 'PLAN_' . Str::uuid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('public/files/' . Constants::YEAR.'/'.$proyecto_id, $filename);
            $input['actividades'] = Storage::url($path); // Genera URL tipo /storage/files/...
        }
        if ($request->has('delete_actividades') && $integrante->actividades) {
            $rutaAnterior = str_replace('/storage/', 'public/', $integrante->actividades); // Ej: public/images/sicadi/foto_xyz.png
            if (Storage::exists($rutaAnterior)) {
                Storage::delete($rutaAnterior);
            }
            $input['actividades'] = null;
        }

        /*if ($files = $request->file('resolucion')) {
            // Eliminar el archivo anterior si existe
            if ($integrante && $integrante->resolucion) {
                $oldFile = public_path($integrante->resolucion);
                if (file_exists($oldFile)) {
                    unlink($oldFile);
                }
            }
            $file = $request->file('resolucion');
            $name = 'RES_'.time().'.'.$file->getClientOriginalExtension();

            $file->move($destinationPath, $name);
            $input['resolucion'] = "files/".Constants::YEAR."/$proyecto_id/$name";
        }*/

        if ($request->hasFile('resolucion')) {
            // Eliminar resolucion anterior si existe
            if (!empty($integrante->resolucion)) {
                $rutaAnterior = str_replace('/storage/', 'public/', $integrante->resolucion); // Ej: public/files/sicadi/2025/CV_123.pdf
                if (Storage::exists($rutaAnterior)) {
                    Storage::delete($rutaAnterior);
                }
            }

            $file = $request->file('resolucion');
            $filename = 'RES_' . Str::uuid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('public/files//' . Constants::YEAR.'/'.$proyecto_id, $filename);
            $input['resolucion'] = Storage::url($path); // Genera URL tipo /storage/files/...
        }
        if ($request->has('delete_resolucion') && $integrante->resolucion) {
            $rutaAnterior = str_replace('/storage/', 'public/', $integrante->resolucion); // Ej: public/images/sicadi/foto_xyz.png
            if (Storage::exists($rutaAnterior)) {
                Storage::delete($rutaAnterior);
            }
            $input['resolucion'] = null;
        }

        DB::beginTransaction();
        $ok = 1;

        try {

            $investigador = $integrante->investigador;

            //dd($investigador);
            $input['investigador_id'] = $investigador->id; // Asignar el nuevo ident al input
            // Almacenar el resultado de la validación en una variable
            $resultadoValidacion = $this->validarHorasGuardar($request, $investigador, $proyecto_id);

            // Verificar si la validación fue exitosa
            if ($resultadoValidacion !== 'validacion_exitosa') {
                // Si no fue exitosa, retornar el resultado de la validación (que incluye el redirect()->back())
                return $resultadoValidacion;
            }



        }catch(QueryException $ex){


            $error=$ex->getMessage();
            $ok=0;

        }




        if ($ok){
            try {
                $input['categoria_id']= $integrante->categoria_id;
                $input['sicadi_id']= $integrante->sicadi_id;
                $input['estado'] = 'Cambio Creado';
                $integrante->update($input);
                $this->guardarIntegrante($request,$integrante,$input['alta']);


                $this->cambiarEstado($integrante,'Cambio creado');

                DB::commit();
                $respuestaID = 'success';
                $respuestaMSJ = 'Cambio creado con éxito';

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

    public function enviarCambio($id)
    {
        ///$integranteId = $request->query('integrante_id');
        $integrante = Integrante::findOrFail($id);
        $proyecto_id = $integrante->proyecto_id;
        $error='';
        $ok = 1;

        $errores = [];

        $today = now();
        $cierreDate = \Carbon\Carbon::parse(Constants::CIERRE);
        if ($today->gt($cierreDate)) {
            $errores[] ='La convocatoria no está vigente';
        }


        if (empty($integrante->tipo) || empty($integrante->horas)) {
            $errores[] = 'Complete todos los campos de la pestaña Proyectos';
        }

        if (empty($integrante->curriculum)) {
            $errores[] = 'Falta subir el Curriculum';
        } else {
            $filePath = public_path($integrante->curriculum);
            if (!file_exists($filePath)) {
                $errores[] = 'Hubo un error al subir el Curriculum, intente nuevamente, si el problema persiste envíenos un mail a proyectos.secyt@presi.unlp.edu.ar';
            }
        }

        if (empty($integrante->actividades)) {
            $errores[] = 'Falta subir el Plan de Trabajo';
        } else {
            $filePath = public_path($integrante->actividades);
            if (!file_exists($filePath)) {
                $errores[] = 'Hubo un error al subir el Plan de Trabajo, intente nuevamente, si el problema persiste envíenos un mail a proyectos.secyt@presi.unlp.edu.ar';
            }
        }
        $this->validarEnviar($integrante,$errores);

        if (!empty($errores)) {
            return redirect()->back()->withErrors($errores)->withInput();
        }


        DB::beginTransaction();


        try {

            $investigador = $integrante->investigador;


            // Almacenar el resultado de la validación en una variable
            $resultadoValidacion = $this->validarHorasEnviar($integrante, $investigador, $proyecto_id);

            // Verificar si la validación fue exitosa
            if ($resultadoValidacion !== 'validacion_exitosa') {
                // Si no fue exitosa, retornar el resultado de la validación (que incluye el redirect()->back())
                return $resultadoValidacion;
            }


        }catch(QueryException $ex){


            $error=$ex->getMessage();
            $ok=0;

        }




        if ($ok){
            try {

                $integrante->estado = 'Cambio Recibido';
                $integrante->save();

                $this->cambiarEstado($integrante,'Envio de cambio');

                // Obtener el ID del usuario autenticado
                $userId = Auth::id();
                $user = User::find($userId); // Obtener el usuario por su ID
                //$integranteMail = ($integrante->tipo=='Colaborador')?$integrante->tipo:'Integrante';

                // Preparar datos para el correo
                $datosCorreo = [
                    'from_email' => $user->email,
                    'from_name' => $user->name,
                    'asunto' => 'Solicitud de Cambio de Colaborador',
                    'codigo' => $integrante->proyecto->codigo,
                    'integranteMail' => 'Integrante',
                    'integrante' => $integrante->investigador->persona->apellido.', '.$integrante->investigador->persona->nombre.' ('.$integrante->investigador->persona->cuil.')',
                    'tipo' => 'Cambio',
                    'fecha' => Carbon::parse($integrante->alta)->format('d/m/Y'), // Formatear la fecha en el formato deseado
                    'comment' => '',
                ];

                // Generar el PDF y obtener la ruta
                $pdfPath = $this->generateCambioPDF(new Request(['integrante_id' => $integrante->id]), true);

                $this->enviarCorreosAlUsuario($datosCorreo,$integrante,$userId,true,$pdfPath);

                DB::commit();
                // Eliminar el archivo PDF temporal
                if (file_exists($pdfPath)) {
                    unlink($pdfPath);
                }
                $respuestaID = 'success';
                $respuestaMSJ = 'Cambio enviado con éxito';

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

    public function admitirCambio($id)
    {
        $integrante = Integrante::findOrFail($id);

        $proyecto_id = $integrante->proyecto_id;
        DB::beginTransaction();
        try {

            $integrante->estado = '';
            //$integrante->baja=$integrante->alta;
            $integrante->save();
            $investigador = Investigador::find($integrante->investigador_id);

            $this->actualizarInvestigador($integrante,$investigador);
            $this->cambiarEstado($integrante,'Confirmación de cambio');

            //$integranteMail = ($integrante->tipo=='Colaborador')?$integrante->tipo:'Integrante';

            // Preparar datos para el correo
            $datosCorreo = [
                'from_email' => Constants::MAIL_PROYECTOS,
                'from_name' => Constants::NOMBRE_PROYECTOS,
                'asunto' => 'Confirmación de solicitud de Cambio de Colaborador',
                'codigo' => $integrante->proyecto->codigo,
                'integranteMail' => 'Integrante',
                'integrante' => $integrante->investigador->persona->apellido.', '.$integrante->investigador->persona->nombre.' ('.$integrante->investigador->persona->cuil.')',
                'tipo' => 'Cambio',
                'fecha' => Carbon::parse($integrante->alta)->format('d/m/Y'), // Formatear la fecha en el formato deseado
                'comment' => '',
            ];

            // Llama a la función enviarCorreos
            $this->enviarCorreos($proyecto_id, $datosCorreo, $integrante);


            DB::commit();
            $respuestaID = 'success';
            $respuestaMSJ = 'Confirmación con éxito';

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


        return redirect()->route('integrantes.index', array('proyecto_id' => $proyecto_id))->with($respuestaID, $respuestaMSJ);
    }

    public function rechazarCambio($id)
    {
        $integrante = Integrante::find($id);

        $proyectoId = $integrante->proyecto_id;
        $proyecto = null;

        // Si se proporciona un ID de proyecto, buscalo en la base de datos
        if ($proyectoId) {
            $proyecto = Proyecto::findOrFail($proyectoId);
        }


        return view('integrantes.denyCambio',compact('proyecto','integrante'));

    }

    public function saveDenyCambio(Request $request, $id)
    {
        $this->validate($request, [
            'comentarios' => 'required'
        ]);

        $input = $this->sanitizeInput($request->all());

        $integrante = Integrante::findOrFail($id);

        $proyecto_id = $integrante->proyecto_id;
        DB::beginTransaction();
        try {

            $fechaActual = $integrante->alta;

            // Obtener los estados en orden descendente por id
            $estados = IntegranteEstado::where('integrante_id', $id)
                ->orderBy('id', 'desc')
                ->get();

            // Filtrar los estados hasta encontrar uno con una fecha de alta distinta a la actual
            $estadoFiltrado = $estados->first(function ($estado) use ($fechaActual) {
                return $estado->alta !== $fechaActual;
            });


            $integrante->estado = '';
            $fecha = $fechaActual;
            $integrante->alta = $estadoFiltrado->alta;
            $integrante->tipo = $estadoFiltrado->tipo;
            $integrante->horas = $estadoFiltrado->horas;
            $integrante->baja = $estadoFiltrado->baja;
            $integrante->categoria_id = $estadoFiltrado->categoria_id;
            $integrante->sicadi_id = $estadoFiltrado->sicadi_id;
            $integrante->deddoc = $estadoFiltrado->deddoc;
            $integrante->cargo_id = $estadoFiltrado->cargo_id;
            $integrante->facultad_id = $estadoFiltrado->facultad_id;
            $integrante->unidad_id = $estadoFiltrado->unidad_id;
            $integrante->carrerainv_id = $estadoFiltrado->carrerainv_id;
            $integrante->organismo_id = $estadoFiltrado->organismo_id;
            $integrante->institucion = ($estadoFiltrado->institucion)?$estadoFiltrado->institucion:null;
            $integrante->beca = ($estadoFiltrado->beca)?$estadoFiltrado->beca:null;
            $integrante->save();

            $this->cambiarEstado($integrante,$input['comentarios']);


            // Obtener el ID del usuario autenticado
            $userId = Auth::id();


            $integranteMail = ($integrante->tipo=='Colaborador')?$integrante->tipo:'Integrante';

            // Preparar datos para el correo
            $datosCorreo = [
                'from_email' => Constants::MAIL_PROYECTOS,
                'from_name' => Constants::NOMBRE_PROYECTOS,
                'asunto' => 'Rechazo de solicitud de CAMBIO de Colaborador',
                'codigo' => $integrante->proyecto->codigo,
                'integranteMail' => 'Colaborador',
                'integrante' => $integrante->investigador->persona->apellido.', '.$integrante->investigador->persona->nombre.' ('.$integrante->investigador->persona->cuil.')',
                'tipo' => 'Alta',
                'fecha' => Carbon::parse($fecha)->format('d/m/Y'), // Formatear la fecha en el formato deseado
                'comment' => $input['comentarios'],
            ];

            // Llama a la función enviarCorreos
            $this->enviarCorreos($proyecto_id, $datosCorreo, $integrante);


            DB::commit();
            $respuestaID = 'success';
            $respuestaMSJ = 'Rechazada con éxito';

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


        return redirect()->route('integrantes.index', array('proyecto_id' => $proyecto_id))->with($respuestaID, $respuestaMSJ);
    }

    public function anularCambio($id)
    {
        $integrante = Integrante::findOrFail($id);

        $proyecto_id = $integrante->proyecto_id;
        DB::beginTransaction();
        try {

            $fechaActual = $integrante->alta;

            // Obtener los estados en orden descendente por id
            $estados = IntegranteEstado::where('integrante_id', $id)
                ->orderBy('id', 'desc')
                ->get();

            // Filtrar los estados hasta encontrar uno con una fecha de alta distinta a la actual
            $estadoFiltrado = $estados->first(function ($estado) use ($fechaActual) {
                return $estado->alta !== $fechaActual;
            });
            //dd($estadoFiltrado);

            $integrante->estado = '';
            $integrante->alta = $estadoFiltrado->alta;
            $integrante->tipo = $estadoFiltrado->tipo;
            $integrante->horas = $estadoFiltrado->horas;
            $integrante->baja = $estadoFiltrado->baja;
            $integrante->categoria_id = $estadoFiltrado->categoria_id;
            $integrante->sicadi_id = $estadoFiltrado->sicadi_id;
            $integrante->deddoc = $estadoFiltrado->deddoc;
            $integrante->cargo_id = $estadoFiltrado->cargo_id;
            $integrante->facultad_id = $estadoFiltrado->facultad_id;
            $integrante->unidad_id = $estadoFiltrado->unidad_id;
            $integrante->carrerainv_id = $estadoFiltrado->carrerainv_id;
            $integrante->organismo_id = $estadoFiltrado->organismo_id;
            $integrante->institucion = ($estadoFiltrado->institucion)?$estadoFiltrado->institucion:null;
            $integrante->beca = ($estadoFiltrado->beca)?$estadoFiltrado->beca:null;
            $integrante->save();

            $this->cambiarEstado($integrante,'Anulación de cambio');


            DB::commit();
            $respuestaID = 'success';
            $respuestaMSJ = 'Cambio anulado con éxito';

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


        return redirect()->route('integrantes.index', array('proyecto_id' => $proyecto_id))->with($respuestaID, $respuestaMSJ);
    }
    public function enviarCorreos($proyecto_id, $datosCorreo, $integrante)
    {
        // Obtener el CUIL del director
        $directorQuery = Integrante::select(DB::raw("personas.cuil"))
            ->leftJoin('investigadors', 'integrantes.investigador_id', '=', 'investigadors.id')
            ->leftJoin('proyectos', 'integrantes.proyecto_id', '=', 'proyectos.id')
            ->leftJoin('personas', 'investigadors.persona_id', '=', 'personas.id')
            ->where('integrantes.tipo', 'Director')
            ->where('integrantes.proyecto_id', $proyecto_id);

        $director = $directorQuery->first();

        if ($director) {
            $user = User::where('cuil', $director->cuil)->first();
            if ($user) {
                // Enviar correo electrónico al usuario del director
                //Mail::to($user->email)->send(new SolicitudEnviada($datosCorreo, $integrante));
            }
        }

        // Obtener el nombre del rol correspondiente al id 4
        $roleName = Role::find(Constants::ID_ADMIN_FACULTAD_PROYECTOS)->name;

        // Obtener usuarios que pertenecen a la facultad especificada y tienen el rol con id 4
        $usuarios = User::where('facultad_id', $integrante->facultad_id)
            ->role($roleName)
            ->get();

        // Enviar correo electrónico a cada usuario
        foreach ($usuarios as $usuario) {
            //Mail::to($usuario->email)->send(new SolicitudEnviada($datosCorreo, $integrante));
        }
    }

    public function enviarCorreosAlUsuario($datosCorreo, $integrante,$userId, $adjuntarArchivos, $adjuntarPlanilla)
    {

        $user = User::find($userId); // Obtener el usuario por su ID

        // Enviar correo electrónico al usuario
        //Mail::to($user->email)->send(new SolicitudEnviada($datosCorreo,$integrante, $adjuntarArchivos, $adjuntarPlanilla));

        // Enviar correo electrónico a tu servidor (ejemplo)
        //Mail::to('marcosp@presi.unlp.edu.ar')->send(new SolicitudEnviada($datosCorreo,$integrante, $adjuntarArchivos, $adjuntarPlanilla));

        // Obtener el nombre del rol correspondiente al id 4
        $roleName = Role::find(Constants::ID_ADMIN_FACULTAD_PROYECTOS)->name;

        // Obtener usuarios que pertenecen a la facultad especificada y tienen el rol con id 4
        $usuarios = User::where('facultad_id', $integrante->facultad_id)
            ->role($roleName)
            ->get();

        // Enviar correo electrónico a cada usuario
        foreach ($usuarios as $usuario) {
            //Mail::to($usuario->email)->send(new SolicitudEnviada($datosCorreo, $integrante, $adjuntarArchivos, $adjuntarPlanilla));
        }
    }

    public function cambioHS($id)
    {
        $integrante = Integrante::find($id);

        $proyectoId = $integrante->proyecto_id;
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

        return view('integrantes.cambioHS',compact('titulos','tituloposts','facultades','cargos','universidades','unidads','carrerainvs','sicadis','years','organismos','categorias','sicadis','proyecto','integrante'));

    }

    public function cambiarHS(Request $request, $id)
    {
        // Extender el validador para incluir la regla de año actual
        Validator::extend('current_year', function ($attribute, $value, $parameters, $validator) {
            return date('Y', strtotime($value)) == date('Y');
        });

        // Definir las reglas de validación
        $rules = [
            'cambio' => 'required|date|current_year',

        ];

        // Definir los mensajes de error personalizados
        $messages = [
            'cambio.required' => 'El campo Fecha de Cambio es obligatorio.',
            'cambio.current_year' => 'Fecha de cambio fuera del período.',

        ];

        // Crear el validador con las reglas y mensajes
        $validator = Validator::make($request->all(), $rules, $messages);

        // Añadir la validación personalizada para verificar que horas y horas_anteriores sean distintos
        $validator->after(function ($validator) use ($request) {
            if ($request->input('horas') == $request->input('horas_anteriores')) {
                $validator->errors()->add('horas', 'No modificó las horas.');
            }
        });

        // Validar y verificar si hay errores
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }


        $input = $this->sanitizeInput($request->all());


        $proyecto_id=$input['proyecto_id'];


        //$input['cambio']= Carbon::now()->format('Y-m-d');

        $integrante = Integrante::find($id);
        // Crear la carpeta si no existe
        /*$destinationPath = public_path('/files/' . Constants::YEAR.'/'.$proyecto_id);
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0777, true);
        }*/

        /*if ($files = $request->file('resolucion')) {
            // Eliminar el archivo anterior si existe
            if ($integrante && $integrante->resolucion) {
                $oldFile = public_path($integrante->resolucion);
                if (file_exists($oldFile)) {
                    unlink($oldFile);
                }
            }
            $file = $request->file('resolucion');
            $name = 'RES_'.time().'.'.$file->getClientOriginalExtension();

            $file->move($destinationPath, $name);
            $input['resolucion'] = "files/".Constants::YEAR."/$proyecto_id/$name";
        }*/

        if ($request->hasFile('resolucion')) {
            // Eliminar resolucion anterior si existe
            if (!empty($integrante->resolucion)) {
                $rutaAnterior = str_replace('/storage/', 'public/', $integrante->resolucion); // Ej: public/files/sicadi/2025/CV_123.pdf
                if (Storage::exists($rutaAnterior)) {
                    Storage::delete($rutaAnterior);
                }
            }

            $file = $request->file('resolucion');
            $filename = 'RES_' . Str::uuid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('public/files//' . Constants::YEAR.'/'.$proyecto_id, $filename);
            $input['resolucion'] = Storage::url($path); // Genera URL tipo /storage/files/...
        }
        if ($request->has('delete_resolucion') && $integrante->resolucion) {
            $rutaAnterior = str_replace('/storage/', 'public/', $integrante->resolucion); // Ej: public/images/sicadi/foto_xyz.png
            if (Storage::exists($rutaAnterior)) {
                Storage::delete($rutaAnterior);
            }
            $input['resolucion'] = null;
        }


        DB::beginTransaction();
        $ok = 1;

        try {

            $investigador = $integrante->investigador;

            //dd($investigador);
            $input['investigador_id'] = $investigador->id; // Asignar el nuevo ident al input

            // Almacenar el resultado de la validación en una variable
            $resultadoValidacion = $this->validarHorasGuardar($request, $investigador, $proyecto_id);

            // Verificar si la validación fue exitosa
            if ($resultadoValidacion !== 'validacion_exitosa') {
                // Si no fue exitosa, retornar el resultado de la validación (que incluye el redirect()->back())
                return $resultadoValidacion;
            }

        }catch(QueryException $ex){


            $error=$ex->getMessage();
            $ok=0;

        }




        if ($ok){
            try {
                $input['categoria_id']= $integrante->categoria_id;
                $input['sicadi_id']= $integrante->sicadi_id;
                $input['estado'] = 'Cambio HS. Creado';
                $input['curriculum'] =null;
                $input['actividades'] =null;
                $integrante->update($input);
                $this->guardarIntegrante($request,$integrante);


                $this->cambiarEstado($integrante,'Cambio HS. creado');


                DB::commit();
                $respuestaID = 'success';
                $respuestaMSJ = 'Cambio de dedicación horaria creado con éxito';

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

    public function enviarCambioHS($id)
    {
        ///$integranteId = $request->query('integrante_id');
        $integrante = Integrante::findOrFail($id);
        $proyecto_id = $integrante->proyecto_id;
        $error='';
        $ok = 1;

        $errores = [];




        if (empty($integrante->tipo) || empty($integrante->horas)|| empty($integrante->cambio)) {
            $errores[] = 'Complete todos los campos de la pestaña Proyectos';
        }

        if (($integrante->horas_anteriores>$integrante->horas)&&empty($integrante->reduccion)) {
            $errores[] = 'En el caso de ser una reducción horaria, especificar las consecuencias que la misma tendrá en el desarrollo del proyecto';
        }

        $this->validarEnviar($integrante,$errores);

        if (!empty($errores)) {
            return redirect()->back()->withErrors($errores)->withInput();
        }


        DB::beginTransaction();


        try {

            $investigador = $integrante->investigador;


            // Almacenar el resultado de la validación en una variable
            $resultadoValidacion = $this->validarHorasEnviar($integrante, $investigador, $proyecto_id);

            // Verificar si la validación fue exitosa
            if ($resultadoValidacion !== 'validacion_exitosa') {
                // Si no fue exitosa, retornar el resultado de la validación (que incluye el redirect()->back())
                return $resultadoValidacion;
            }


        }catch(QueryException $ex){


            $error=$ex->getMessage();
            $ok=0;

        }




        if ($ok){
            try {

                $integrante->estado = 'Cambio Hs. Recibido';
                $integrante->save();

                $this->cambiarEstado($integrante,'Envio de cambio de hs.');


                // Obtener el ID del usuario autenticado
                $userId = Auth::id();
                $user = User::find($userId); // Obtener el usuario por su ID
                $integranteMail = ($integrante->tipo=='Colaborador')?$integrante->tipo:'Integrante';

                // Preparar datos para el correo
                $datosCorreo = [
                    'from_email' => $user->email,
                    'from_name' => $user->name,
                    'asunto' => 'Solicitud de Cambio de dedicación horaria',
                    'codigo' => $integrante->proyecto->codigo,
                    'integranteMail' => $integranteMail,
                    'integrante' => $integrante->investigador->persona->apellido.', '.$integrante->investigador->persona->nombre.' ('.$integrante->investigador->persona->cuil.')',
                    'tipo' => 'Cambio de dedicación horaria',
                    'fecha' => Carbon::parse($integrante->cambio)->format('d/m/Y'), // Formatear la fecha en el formato deseado
                    'comment' => '',
                ];

                // Generar el PDF y obtener la ruta
                $pdfPath = $this->generateCambioHSPDF(new Request(['integrante_id' => $integrante->id]), true);

                $this->enviarCorreosAlUsuario($datosCorreo,$integrante,$userId,true,$pdfPath);

                DB::commit();
                // Eliminar el archivo PDF temporal
                if (file_exists($pdfPath)) {
                    unlink($pdfPath);
                }
                $respuestaID = 'success';
                $respuestaMSJ = 'Cambio HS enviado con éxito';

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

    public function anularHS($id)
    {
        $integrante = Integrante::findOrFail($id);

        $proyecto_id = $integrante->proyecto_id;
        DB::beginTransaction();
        try {

            $integrante->estado = '';

            $integrante->cambio=null;
            $integrante->reduccion=null;
            $integrante->horas=$integrante->horas_anteriores;
            $integrante->horas_anteriores=null;
            $integrante->save();

            $this->cambiarEstado($integrante,'Anulación de cambio de horas');


            DB::commit();
            $respuestaID = 'success';
            $respuestaMSJ = 'Cambio de horas anulado con éxito';

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

        return redirect()->route('integrantes.index', array('proyecto_id' => $proyecto_id))->with($respuestaID, $respuestaMSJ);
    }

    public function admitirCambioHS($id)
    {
        $integrante = Integrante::findOrFail($id);

        $proyecto_id = $integrante->proyecto_id;
        DB::beginTransaction();
        try {

            $integrante->estado = '';
            //$integrante->baja=$integrante->alta;
            $integrante->horas_anteriores=null;
            $integrante->cambio=null;
            $integrante->reduccion=null;
            $integrante->save();
            $investigador = Investigador::find($integrante->investigador_id);

            $this->actualizarInvestigador($integrante,$investigador);

            $this->cambiarEstado($integrante,'Confirmación de cambio de horas');


            $integranteMail = ($integrante->tipo=='Colaborador')?$integrante->tipo:'Integrante';

            // Preparar datos para el correo
            $datosCorreo = [
                'from_email' => Constants::MAIL_PROYECTOS,
                'from_name' => Constants::NOMBRE_PROYECTOS,
                'asunto' => 'Confirmación de solicitud de Cambio de dedicación horaria',
                'codigo' => $integrante->proyecto->codigo,
                'integranteMail' => $integranteMail,
                'integrante' => $integrante->investigador->persona->apellido.', '.$integrante->investigador->persona->nombre.' ('.$integrante->investigador->persona->cuil.')',
                'tipo' => 'Cambio de dedicación horaria',
                'fecha' => Carbon::parse($integrante->cambio)->format('d/m/Y'), // Formatear la fecha en el formato deseado
                'comment' => '',
            ];

            // Llama a la función enviarCorreos
            $this->enviarCorreos($proyecto_id, $datosCorreo, $integrante);


            DB::commit();
            $respuestaID = 'success';
            $respuestaMSJ = 'Confirmación con éxito';

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

        return redirect()->route('integrantes.index', array('proyecto_id' => $proyecto_id))->with($respuestaID, $respuestaMSJ);
    }

    public function rechazarCambioHS($id)
    {
        $integrante = Integrante::find($id);

        $proyectoId = $integrante->proyecto_id;
        $proyecto = null;

        // Si se proporciona un ID de proyecto, buscalo en la base de datos
        if ($proyectoId) {
            $proyecto = Proyecto::findOrFail($proyectoId);
        }


        return view('integrantes.denyCambioHS',compact('proyecto','integrante'));

    }

    public function saveDenyCambioHS(Request $request, $id)
    {
        $this->validate($request, [
            'comentarios' => 'required'
        ]);

        $input = $this->sanitizeInput($request->all());

        $integrante = Integrante::findOrFail($id);

        $proyecto_id = $integrante->proyecto_id;
        DB::beginTransaction();
        try {




            $integrante->estado = '';
            $fecha=$integrante->cambio;
            $integrante->cambio=null;
            $integrante->reduccion=null;
            $integrante->horas=$integrante->horas_anteriores;
            $integrante->horas_anteriores=null;
            $integrante->save();

            $this->cambiarEstado($integrante,$input['comentarios']);


            $integranteMail = ($integrante->tipo=='Colaborador')?$integrante->tipo:'Integrante';

            // Preparar datos para el correo
            $datosCorreo = [
                'from_email' => Constants::MAIL_PROYECTOS,
                'from_name' => Constants::NOMBRE_PROYECTOS,
                'asunto' => 'Rechazo de solicitud de CAMBIO de dedicación horaria',
                'codigo' => $integrante->proyecto->codigo,
                'integranteMail' => $integranteMail,
                'integrante' => $integrante->investigador->persona->apellido.', '.$integrante->investigador->persona->nombre.' ('.$integrante->investigador->persona->cuil.')',
                'tipo' => 'Cambio de dedicación horaria',
                'fecha' => Carbon::parse($fecha)->format('d/m/Y'), // Formatear la fecha en el formato deseado
                'comment' => $input['comentarios'],
            ];

            // Llama a la función enviarCorreos
            $this->enviarCorreos($proyecto_id, $datosCorreo, $integrante);


            DB::commit();
            $respuestaID = 'success';
            $respuestaMSJ = 'Rechazada con éxito';

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


        return redirect()->route('integrantes.index', array('proyecto_id' => $proyecto_id))->with($respuestaID, $respuestaMSJ);
    }

    public function actualizarInvestigador($integrante, $investigador)
    {
        if ($investigador) {
            $investigador->categoria_id = $integrante->categoria_id;
            $investigador->sicadi_id = $integrante->sicadi_id;
            $investigador->carrerainv_id = $integrante->carrerainv_id;
            $investigador->organismo_id = $integrante->organismo_id;
            $investigador->facultad_id = $integrante->facultad_id;
            $investigador->cargo_id = $integrante->cargo_id;
            $investigador->deddoc = $integrante->deddoc;
            $investigador->universidad_id = $integrante->universidad_id;
            $investigador->titulo_id = $integrante->titulo_id;
            $investigador->titulopost_id = $integrante->titulopost_id;
            $investigador->unidad_id = $integrante->unidad_id;
            $investigador->institucion = ($integrante->institucion)?$integrante->institucion:null;
            $investigador->beca = ($integrante->beca)?$integrante->beca:null;
            $investigador->materias = $integrante->materias;
            $investigador->total = $integrante->total;
            $investigador->carrera = $integrante->carrera;

            $investigador->save();
        }
        $persona = $investigador->persona;  // Obtener la persona asociada

        // Actualizar los datos de la persona aquí
        if ($persona) {
            /*$persona->apellido = $integrante->apellido; // Cambia esto a los datos que quieras actualizar
            $persona->nombre = $integrante->nombre;
            $persona->cuil = $integrante->cuil;*/
            $persona->email = $integrante->email;
            $persona->nacimiento = $integrante->nacimiento;
            $persona->save();
        }
        if (!empty($integrante->titulo_id)) {


            // Verificar si el título ya está asociado
            $tituloId = $integrante->titulo_id;
            $egresoGrado = $integrante->egresogrado;

            // Comprobar si el título ya está asociado con el investigador
            $existingTitulo = $investigador->titulos->first(function ($titulo) use ($tituloId) {
                return $titulo->id == $tituloId;
            });

            if ($existingTitulo) {
                // Si el título ya está asociado, actualizar la relación si el egreso es diferente
                if ($existingTitulo->pivot->egreso != $egresoGrado) {
                    $investigador->titulos()->updateExistingPivot($tituloId, [
                        'egreso' => $egresoGrado,
                        'updated_at' => now(),
                    ]);

                    Log::info("Título actualizado: " . $tituloId . " - Egreso: " . $egresoGrado);
                } else {
                    Log::info("Título ya está asociado con el mismo egreso: " . $tituloId);
                }
            } else {
                // Si el título no está asociado, adjuntarlo
                $investigador->titulos()->attach($tituloId, [
                    'egreso' => $egresoGrado,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                Log::info("Nuevo título asociado: " . $tituloId . " - Egreso: " . $egresoGrado);
            }
        }


        // Guardar el primer título pasado en $request->titulopost en la columna titulopost_id del investigador
        if (!empty($integrante->titulopost_id)) {

            // Verificar si el título ya está asociado
            $tituloId = $integrante->titulopost_id;
            $egresoPosgrado = $integrante->egresoposgrado;

            // Comprobar si el título ya está asociado con el investigador
            $existingTituloPosgrado = $investigador->tituloposts->first(function ($titulo) use ($tituloId) {
                return $titulo->id == $tituloId;
            });

            if ($existingTituloPosgrado) {
                // Si el título ya está asociado, actualizar la relación si el egreso es diferente
                if ($existingTituloPosgrado->pivot->egreso != $egresoPosgrado) {
                    $investigador->tituloposts()->updateExistingPivot($tituloId, [
                        'egreso' => $egresoPosgrado,
                        'updated_at' => now(),
                    ]);

                    Log::info("Título posgrado actualizado: " . $tituloId . " - Egreso: " . $egresoPosgrado);
                } else {
                    Log::info("Título posgrado ya está asociado con el mismo egreso: " . $tituloId);
                }
            } else {
                // Si el título no está asociado, adjuntarlo
                $investigador->tituloposts()->attach($tituloId, [
                    'egreso' => $egresoPosgrado,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                Log::info("Nuevo título posgrado asociado: " . $tituloId . " - Egreso: " . $egresoGrado);
            }




        }




        if (!empty($integrante->cargo_id)) {

            $existingCargos = $investigador->cargos->map(function ($cargo) {
                return [
                    'cargo_id' => $cargo->id, // Ajusta según el nombre de la columna en tu tabla 'cargos'
                    'deddoc' => $cargo->pivot->deddoc,
                    'ingreso' => $cargo->pivot->ingreso,
                    'facultad_id' => $cargo->pivot->facultad_id,
                    'universidad_id' => $cargo->pivot->universidad_id,
                ];
            })->toArray();

            //Log::info('Existing Cargos: ' . json_encode($existingCargos));

            // Verificar si el nuevo cargo ya existe en las cargos del investigador
            $existingCargo = collect($existingCargos)->first(function ($existingCargo) use ($integrante) {
                return $existingCargo['cargo_id'] == $integrante->cargo_id && $existingCargo['deddoc'] == $integrante->deddoc &&  $existingCargo['facultad_id'] == $integrante->facultad_id && $existingCargo['universidad_id'] == $integrante->universidad_id;
            });

            //Log::info("Datos del Integrante: " . json_encode($integrante));
            //Log::info("Datos del Cargo Existente: " . json_encode($existingCargo));


            if ($existingCargo) {
                Log::info("El cargo ya está asociado con los mismos datos: " . json_encode($existingCargo));
            } else {
                // Si el cargo no existe, insertarlo en la tabla 'investigador_cargos'
                DB::table('investigador_cargos')->insert([
                    'investigador_id' => $investigador->id,
                    'cargo_id' => $integrante->cargo_id,
                    'deddoc' => $integrante->deddoc,
                    'ingreso' => $integrante->alta_cargo,
                    'facultad_id' => $integrante->facultad_id,
                    'universidad_id' => $integrante->universidad_id,
                    'activo' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                Log::info("Nuevo Cargo Insertado: " . $integrante->cargo_id . " - Dedicación: " . $integrante->deddoc);
            }
        }


        if (!empty($integrante->carrerainv_id)) {

            // Datos de la nueva carrera
            $nuevaCarrera = [
                'carrerainv_id' => $integrante->carrerainv_id,
                'organismo_id' => $integrante->organismo_id,
                'ingreso' => $integrante->ingreso_carrerainv,
            ];

            // Buscar si la carrera ya está asociada con el investigador
            $existingCarrera = $investigador->carrerainvs->first(function ($carrerainv) use ($nuevaCarrera) {
                return $carrerainv->id == $nuevaCarrera['carrerainv_id'];
            });

            if ($existingCarrera) {
                // Si la carrera ya está asociada, verificar si los datos son diferentes
                if ($existingCarrera->pivot->organismo_id != $nuevaCarrera['organismo_id'] ||
                    $existingCarrera->pivot->ingreso != $nuevaCarrera['ingreso']) {

                    // Actualizar el registro en la tabla intermedia
                    $investigador->carrerainvs()->updateExistingPivot($nuevaCarrera['carrerainv_id'], [
                        'organismo_id' => $nuevaCarrera['organismo_id'],
                        'ingreso' => $nuevaCarrera['ingreso'],
                        'updated_at' => now(),
                    ]);

                    Log::info("Carrera actualizada: " . $nuevaCarrera['carrerainv_id']);
                } else {
                    Log::info("La carrera ya está asociada con los mismos datos: " . $nuevaCarrera['carrerainv_id']);
                }
            } else {
                // Si la carrera no está asociada, agregarla
                $investigador->carrerainvs()->attach($nuevaCarrera['carrerainv_id'], array_merge($nuevaCarrera, [
                    'actual' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]));

                Log::info("Nueva carrera asociada: " . $nuevaCarrera['carrerainv_id']);
            }
        }
        if (!empty($integrante->categoria_id)) {

            // Datos de la nueva categoría
            $nuevaCategoria = [
                'categoria_id' => $integrante->categoria_id,
            ];

            // Buscar si la categoría ya está asociada con el investigador
            $existingCategoria = $investigador->categorias->first(function ($categoria) use ($nuevaCategoria) {
                return $categoria->id == $nuevaCategoria['categoria_id'];
            });

            if ($existingCategoria) {
                // Si la categoría ya está asociada, no es necesario hacer nada ya que es la misma categoría
                Log::info("La categoría ya está asociada: " . $nuevaCategoria['categoria_id']);
            } else {
                // Si la categoría no está asociada, agregarla
                DB::table('investigador_categorias')->insert([
                    'investigador_id' => $investigador->id,
                    'categoria_id' => $nuevaCategoria['categoria_id'],
                    'actual' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                Log::info("Nueva categoría asociada: " . $nuevaCategoria['categoria_id']);
            }
        }

        if (!empty($integrante->sicadi_id)) {

            // Datos del nuevo SICADI
            $nuevoSicadi = [
                'sicadi_id' => $integrante->sicadi_id,
            ];

            // Buscar si el SICADI ya está asociado con el investigador
            $existingSicadi = $investigador->sicadis->first(function ($sicadi) use ($nuevoSicadi) {
                return $sicadi->id == $nuevoSicadi['sicadi_id'];
            });

            if ($existingSicadi) {
                // Si el SICADI ya está asociado, no es necesario hacer nada ya que es el mismo SICADI
                Log::info("El SICADI ya está asociado: " . $nuevoSicadi['sicadi_id']);
            } else {
                // Si el SICADI no está asociado, agregarlo
                DB::table('investigador_sicadis')->insert([
                    'investigador_id' => $investigador->id,
                    'sicadi_id' => $nuevoSicadi['sicadi_id'],
                    'actual' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                Log::info("Nuevo SICADI asociado: " . $nuevoSicadi['sicadi_id']);
            }
        }


        if (!empty($integrante->beca)) {


            // Obtener los IDs e instituciones de las becas existentes del investigador
            $existingBecas = $investigador->becas->map(function($beca) {
                return [
                    'beca' => $beca->beca,
                    'institucion' => $beca->institucion,
                    'desde' => $beca->desde,
                    'hasta' => $beca->hasta,
                ];
            })->toArray();

// Verificar si la nueva beca ya existe en las becas del investigador
            $existingBeca = collect($existingBecas)->first(function ($existingBeca) use ($integrante) {
                return $existingBeca['beca'] == $integrante->beca && $existingBeca['institucion'] == $integrante->institucion;
            });

            if ($existingBeca) {
                // Si la beca existe, verificar si las fechas 'desde' y 'hasta' son distintas
                if ($existingBeca['desde'] != $integrante->alta_beca || $existingBeca['hasta'] != $integrante->baja_beca) {
                    // Actualizar las fechas en la beca existente
                    DB::table('investigador_becas')
                        ->where('investigador_id', $investigador->id)
                        ->where('beca', $integrante->beca)
                        ->where('institucion', $integrante->institucion)
                        ->update([
                            'desde' => $integrante->alta_beca,
                            'hasta' => $integrante->baja_beca,
                            'updated_at' => now(),
                        ]);

                    Log::info("Fechas de Beca Actualizadas: " . $integrante->beca . " - Institución: " . $integrante->institucion);
                } else {
                    Log::info("La beca ya existe y las fechas son las mismas: " . $integrante->beca . " - Institución: " . $integrante->institucion);
                }
            } else {
                // Si la beca no existe, insertarla en la tabla 'investigador_becas'
                DB::table('investigador_becas')->insert([
                    'investigador_id' => $investigador->id,
                    'beca' => $integrante->beca,
                    'institucion' => $integrante->institucion,
                    'desde' => $integrante->alta_beca,
                    'hasta' => $integrante->baja_beca,
                    'unlp' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                Log::info("Nueva Beca Insertada: " . $integrante->beca . " - Institución: " . $integrante->institucion);
            }


        }
    }


    public function cambiarEstado($integrante, $comentarios)
    {

        // Actualizar el registro de estado existente donde 'hasta' es null
        IntegranteEstado::where('integrante_id', $integrante->id)
            ->whereNull('hasta')
            ->update(['hasta' => Carbon::now()]);

        // Obtener el ID del usuario autenticado
        $userId = Auth::id();

        // Crear registro en integrante_estados con el estado "Alta Creada" y el user_id
        $integrante->estados()->create([
            'tipo' => $integrante->tipo,
            'alta' => $integrante->alta,
            'baja' => $integrante->baja,
            'cambio' => $integrante->cambio,
            'estado' => $integrante->estado,
            'user_id' => $userId,
            'horas' => $integrante->horas,
            'categoria_id' => ($integrante->categoria_id)?$integrante->categoria_id:null,
            'sicadi_id' => ($integrante->sicadi_id)?$integrante->sicadi_id:null,
            'deddoc' => $integrante->deddoc,
            'cargo_id' => ($integrante->cargo_id)?$integrante->cargo_id:null,
            'facultad_id' => ($integrante->facultad_id)?$integrante->facultad_id:null,
            'unidad_id' => ($integrante->unidad_id)?$integrante->unidad_id:null,
            'carrerainv_id' => ($integrante->carrerainv_id)?$integrante->carrerainv_id:null,
            'organismo_id' => ($integrante->organismo_id)?$integrante->organismo_id:null,
            'institucion' => ($integrante->institucion)?$integrante->institucion:null,
            'beca' => ($integrante->beca)?$integrante->beca:null,
            'consecuencias' => $integrante->consecuencias,
            'motivos' => $integrante->motivos,
            'reduccion' => $integrante->reduccion,
            'comentarios' => $comentarios,
            'desde' => Carbon::now(), // Establecer 'desde' como la fecha actual

        ]);
    }

    public function validarHorasGuardar($request,$investigador,$proyecto_id)
    {
        //dd($request);
        $unProyecto=0;
        $dosProyectos=0;
        if ($request->tipo === 'Colaborador') {
            $unProyecto=1;
            $colaboradorConCargo = 0;
            $maxHoras=4;
            $minHoras=4;
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
        elseif ($request->universidad_id!=11) {
            $unProyecto = 1;
            $minHoras=3;
            $maxHoras = 6;
            if(!empty($request->deddocs)){
                if ($request->deddocs[0]=='Simple'){
                    //Log::info("Entra Dedicacion: ".$request->deddocs[0]);
                    if (!empty($request->carrerainvs)) {

                        if (!in_array($request->carrerainvs[0], explode(",", Constants::CARRERAS_INVESTIGACION))) {

                            $maxHoras = 4;

                        }
                    }

                }
            }


        }
        elseif (!empty($request->becas[0])) {

            /*if ($integrante->beca!='RETENCION DE POSTGRADUADO'){
                $unProyecto=1;
            }*/
            //dd($request->becas[0]->beca);
            if ($request->becas[0]!='Beca posdoctoral'){
                $unProyecto=1;
            }
            /*if (!empty($request->becas[0]->institucion)) {
                if ($request->becas[0]->institucion=='CIN'){
                    $maxHoras=12;
                    $minHoras=12;
                }
                else{
                    $maxHoras=40;
                    $minHoras=40;
                }
            }*/
            if (!empty($request->institucions[0])) {
                if ($request->institucions[0]=='CIN'){
                    $maxHoras=12;
                    $minHoras=12;
                }
                else{
                    $maxHoras=40;
                    $minHoras=40;
                }
            }


        }
        elseif ($request->materias>0) {
            $unProyecto=1;
            $maxHoras=4;
            $minHoras=4;
        }
        elseif(in_array($request->cargos[0], explode(",",Constants::EMERITOS_CONSULTOS))){
            $unProyecto = 1;
            $maxHoras = 4;
            $minHoras=4;
        }
        elseif(in_array($request->carrerainvs[0], explode(",",Constants::CARRERAS_INVESTIGACION))){
            $dosProyectos = 1;
            $maxHoras = 35;
            $minHoras=10;
        }
        elseif (!empty($request->deddocs)) {

            switch ($request->deddocs[0]) {
                case 'Exclusiva':
                    $dosProyectos = 1;
                    $maxHoras = 35;
                    $minHoras=10;
                    break;
                case 'Semi Exclusiva':
                    $dosProyectos = 1;
                    $maxHoras = 15;
                    $minHoras=6;
                    break;
                case 'Simple':
                    $unProyecto = 1;
                    $maxHoras = 4;
                    $minHoras=4;
                    break;
            }


        }

        if (!$unProyecto){

            if (!empty($request->becas[0])) {


                if ($request->becas[0] == 'Beca posdoctoral') {
                    $dosProyectos = 1;
                    $minHoras=10;
                }

            }
        }
        if ($unProyecto || $dosProyectos){
            // Verificar si el investigador participa en otro proyecto activo
            // Consulta para obtener los integrantes
            $integrantes = Integrante::where('investigador_id', $investigador->id)
                ->where(function ($query) {
                    $query->where('estado', '!=', 'Baja Creada')
                        ->where('estado', '!=', 'Baja Recibida')
                        ->orWhereNull('estado') // Agregamos los que tengan estado = null
                        ->where(function ($q) {
                            $q->where('baja', '>', Carbon::now()->format('Y-m-d'))
                                ->orWhereNull('baja')
                                ->orWhere('baja', '0000-00-00');
                        });
                })
                ->whereHas('proyecto', function ($query) use ($proyecto_id) {
                    $query->where('estado', 'Acreditado')
                        ->where('id', '<>', $proyecto_id)
                        ->where('fin', '>', Carbon::now()->subYear()->endOfYear()->format('Y-m-d'));
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
                        $horasTotales = $horasOtroProyecto + $request->horas;
                        $comparar = ($minHoras==$maxHoras)?0:1;
                        if ($comparar){
                            if ($horasTotales < $minHoras || $horasTotales > $maxHoras) {
                                return redirect()->back()
                                    ->withErrors(['cuil' => 'Las horas a aportar deben estar entre ' . $minHoras . ' y ' . $maxHoras . ' - ya aporta ' . $horasOtroProyecto . ' al proyecto ' . $proyectos])
                                    ->withInput();
                            }
                        }
                        elseif ($horasTotales!=$maxHoras){
                            return redirect()->back()
                                ->withErrors(['cuil' => 'Las horas a aportar deben ser '.$maxHoras.' - ya aporta '.$horasOtroProyecto.' al proyecto '. $proyectos])
                                ->withInput();
                        }
                    }
                }

            }
            else{
                //Log::info("No está en proyectos ".$unProyecto." horas: ".$input['horas']." max: ".$maxHoras);
                //if ($unProyecto){
                if ($request->horas){
                    $comparar = ($minHoras==$maxHoras)?0:1;
                    if ($comparar){
                        if ($request->horas < $minHoras || $request->horas > $maxHoras) {
                            return redirect()->back()
                                ->withErrors(['cuil' => 'Las horas a aportar deben estar entre ' . $minHoras . ' y ' . $maxHoras])
                                ->withInput();
                        }
                    }
                    elseif ($request->horas!=$maxHoras){
                        return redirect()->back()
                            ->withErrors(['cuil' => 'Las horas a aportar deben ser '.$maxHoras])
                            ->withInput();
                    }

                }
                //}
            }
        }
        // Si pasa todas las validaciones, retornar un valor indicando éxito
        return 'validacion_exitosa';
    }

    public function validarHorasEnviar($integrante,$investigador,$proyecto_id)
    {
        $unProyecto=0;
        $dosProyectos=0;
        if ($integrante->tipo === 'Colaborador') {
            $unProyecto=1;
            $colaboradorConCargo = 0;
            $maxHoras=4;
            $minHoras=4;

        }
        elseif ($integrante->universidad_id!=11) {
            $unProyecto = 1;
            $minHoras=3;
            $maxHoras = 6;
            if ($integrante->deddocs=='Simple'){
                //Log::info("Entra Dedicacion: ".$request->deddocs[0]);
                if(!in_array($integrante->carrerainv_id, explode(",",Constants::CARRERAS_INVESTIGACION))){

                    $maxHoras = 4;

                }

            }

        }
        elseif (!empty($integrante->beca)) {

            /*if ($integrante->beca!='RETENCION DE POSTGRADUADO'){
                $unProyecto=1;
            }*/
            if ($integrante->beca!='Beca posdoctoral'){
                $unProyecto=1;
            }
            if (!empty($integrante->institucion)) {
                if ($integrante->institucion=='CIN'){
                    $maxHoras=12;
                    $minHoras=12;
                }
                else{
                    $maxHoras=40;
                    $minHoras=40;
                }
            }


        }
        elseif ($integrante->materias>0) {
            $unProyecto=1;
            $maxHoras=4;
            $minHoras=4;
        }
        elseif(in_array($integrante->cargo_id, explode(",",Constants::EMERITOS_CONSULTOS))){
            $unProyecto = 1;
            $maxHoras = 4;
            $minHoras=4;
        }
        elseif(in_array($integrante->carrerainv_id, explode(",",Constants::CARRERAS_INVESTIGACION))){
            $dosProyectos = 1;
            $maxHoras = 35;
            $minHoras=10;
        }
        elseif (!empty($integrante->deddocs)) {

            switch ($integrante->deddocs) {
                case 'Exclusiva':
                    $dosProyectos = 1;
                    $maxHoras = 35;
                    $minHoras=10;
                    break;
                case 'Semi Exclusiva':
                    $dosProyectos = 1;
                    $maxHoras = 15;
                    $minHoras=6;
                    break;
                case 'Simple':
                    $unProyecto = 1;
                    $maxHoras = 4;
                    $minHoras=4;
                    break;
            }


        }

        if (!$unProyecto){

            if (!empty($integrante->beca)) {


                if ($integrante->beca == 'Beca posdoctoral') {
                    $dosProyectos = 1;
                    $minHoras=10;
                }






            }
        }
        if ($unProyecto || $dosProyectos){
            // Verificar si el investigador participa en otro proyecto activo
            // Consulta para obtener los integrantes
            $integrantes = Integrante::where('investigador_id', $investigador->id)
                ->where(function ($query) {
                    $query->where('estado', '!=', 'Baja Creada')
                        ->where('estado', '!=', 'Baja Recibida')
                        ->orWhereNull('estado') // Agregamos los que tengan estado = null
                        ->where(function ($q) {
                            $q->where('baja', '>', Carbon::now()->format('Y-m-d'))
                                ->orWhereNull('baja')
                                ->orWhere('baja', '0000-00-00');
                        });
                })
                ->whereHas('proyecto', function ($query) use ($proyecto_id) {
                    $query->where('estado', 'Acreditado')
                        ->where('id', '<>', $proyecto_id)
                        // Modificar para que 'fin' sea mayor al 31/12 del año anterior
                        ->where('fin', '>', Carbon::now()->subYear()->endOfYear()->format('Y-m-d'));
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
                        $horasTotales = $horasOtroProyecto + $integrante->horas;
                        $comparar = ($minHoras==$maxHoras)?0:1;
                        if ($comparar){
                            if ($horasTotales < $minHoras || $horasTotales > $maxHoras) {
                                return redirect()->back()
                                    ->withErrors(['cuil' => 'Las horas a aportar deben estar entre ' . $minHoras . ' y ' . $maxHoras . ' - ya aporta ' . $horasOtroProyecto . ' al proyecto ' . $proyectos])
                                    ->withInput();
                            }
                        }
                        elseif ($horasTotales!=$maxHoras){
                            return redirect()->back()
                                ->withErrors(['cuil' => 'Las horas a aportar deben ser '.$maxHoras.' - ya aporta '.$horasOtroProyecto.' al proyecto '. $proyectos])
                                ->withInput();
                        }
                    }
                }

            }
            else{
                //Log::info("No está en proyectos ".$unProyecto." horas: ".$input['horas']." max: ".$maxHoras);
                //if ($unProyecto){
                if ($integrante->horas){
                    $comparar = ($minHoras==$maxHoras)?0:1;
                    if ($comparar){
                        if ($integrante->horas < $minHoras || $integrante->horas > $maxHoras) {
                            return redirect()->back()
                                ->withErrors(['cuil' => 'Las horas a aportar deben estar entre ' . $minHoras . ' y ' . $maxHoras])
                                ->withInput();
                        }
                    }
                    elseif ($integrante->horas!=$maxHoras){
                        return redirect()->back()
                            ->withErrors(['cuil' => 'Las horas a aportar deben ser '.$maxHoras])
                            ->withInput();
                    }

                }
                //}
            }
        }
        // Si pasa todas las validaciones, retornar un valor indicando éxito
        return 'validacion_exitosa';
    }

    public function cambioTipo($id)
    {
        $integrante = Integrante::find($id);

        $proyectoId = $integrante->proyecto_id;
        $proyecto = null;

        // Si se proporciona un ID de proyecto, buscalo en la base de datos
        if ($proyectoId) {
            $proyecto = Proyecto::findOrFail($proyectoId);
        }

        $currentTipo = $integrante->tipo;
        $tipos = [''=>'','Investigador Formado' => 'Investigador Formado', 'Investigador En Formación' => 'Investigador En Formación', 'Becario, Tesista' => 'Becario, Tesista','Colaborador'=>'Colaborador'];
        $filteredTipos = array_filter($tipos, function($value) use ($currentTipo) {
            return $value !== $currentTipo;
        });


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

        return view('integrantes.cambioTipo',compact('titulos','tituloposts','facultades','cargos','universidades','unidads','carrerainvs','sicadis','years','organismos','categorias','sicadis','proyecto','integrante','filteredTipos'));

    }

    public function cambiarTipo(Request $request, $id)
    {
        // Extender el validador para incluir la regla de año actual
        Validator::extend('current_year', function ($attribute, $value, $parameters, $validator) {
            return date('Y', strtotime($value)) == date('Y');
        });

        // Definir las reglas de validación
        $rules = [
            'tipo' => 'required',
            'cambio' => 'required|date|current_year',
            'horas' => 'required',

        ];

        // Definir los mensajes de error personalizados
        $messages = [
            'cambio.required' => 'El campo Fecha de Cambio es obligatorio.',
            'cambio.current_year' => 'Fecha de cambio fuera del período.',

        ];

        // Crear el validador con las reglas y mensajes
        $validator = Validator::make($request->all(), $rules, $messages);




        // Añadir la validación personalizada para verificar que horas y horas_anteriores sean distintos
        /*$validator->after(function ($validator) use ($request) {
            if ($request->input('horas') == $request->input('horas_anteriores')) {
                $validator->errors()->add('horas', 'No modificó las horas.');
            }
        });*/

        // Validar y verificar si hay errores
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }


        $input = $this->sanitizeInput($request->all());

        $proyecto_id=$input['proyecto_id'];


        //$input['cambio']= Carbon::now()->format('Y-m-d');

        $integrante = Integrante::find($id);

        // Crear la carpeta si no existe
        /*$destinationPath = public_path('/files/' . Constants::YEAR.'/'.$proyecto_id);
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0777, true);
        }*/

        /*if ($files = $request->file('resolucion')) {
            // Eliminar el archivo anterior si existe
            if ($integrante && $integrante->resolucion) {
                $oldFile = public_path($integrante->resolucion);
                if (file_exists($oldFile)) {
                    unlink($oldFile);
                }
            }
            $file = $request->file('resolucion');
            $name = 'RES_'.time().'.'.$file->getClientOriginalExtension();

            $file->move($destinationPath, $name);
            $input['resolucion'] = "files/".Constants::YEAR."/$proyecto_id/$name";
        }*/

        if ($request->hasFile('resolucion')) {
            // Eliminar resolucion anterior si existe
            if (!empty($integrante->resolucion)) {
                $rutaAnterior = str_replace('/storage/', 'public/', $integrante->resolucion); // Ej: public/files/sicadi/2025/CV_123.pdf
                if (Storage::exists($rutaAnterior)) {
                    Storage::delete($rutaAnterior);
                }
            }

            $file = $request->file('resolucion');
            $filename = 'RES_' . Str::uuid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('public/files//' . Constants::YEAR.'/'.$proyecto_id, $filename);
            $input['resolucion'] = Storage::url($path); // Genera URL tipo /storage/files/...
        }
        if ($request->has('delete_resolucion') && $integrante->resolucion) {
            $rutaAnterior = str_replace('/storage/', 'public/', $integrante->resolucion); // Ej: public/images/sicadi/foto_xyz.png
            if (Storage::exists($rutaAnterior)) {
                Storage::delete($rutaAnterior);
            }
            $input['resolucion'] = null;
        }

        DB::beginTransaction();
        $ok = 1;

        try {

            $investigador = $integrante->investigador;

            //dd($investigador);
            $input['investigador_id'] = $investigador->id; // Asignar el nuevo ident al input

            // Almacenar el resultado de la validación en una variable
            $resultadoValidacion = $this->validarHorasGuardar($request, $investigador, $proyecto_id);

            // Verificar si la validación fue exitosa
            if ($resultadoValidacion !== 'validacion_exitosa') {
                // Si no fue exitosa, retornar el resultado de la validación (que incluye el redirect()->back())
                return $resultadoValidacion;
            }

        }catch(QueryException $ex){


            $error=$ex->getMessage();
            $ok=0;

        }




        if ($ok){
            try {
                $input['categoria_id']= $integrante->categoria_id;
                $input['sicadi_id']= $integrante->sicadi_id;
                $input['estado'] = 'Cambio Tipo Creado';
                $input['curriculum'] =null;
                $input['actividades'] =null;

                $integrante->update($input);
                $this->guardarIntegrante($request,$integrante);


                //dd($integrante);
                $errores=array();
                if (($integrante->horas_anteriores>$integrante->horas)&&empty($integrante->reduccion)) {
                    $errores[] = 'En el caso de ser una reducción horaria, especificar las consecuencias que la misma tendrá en el desarrollo del proyecto';
                }
                $this->validarEnviar($integrante,$errores);
                if (!empty($errores)) {
                    return redirect()->back()->withErrors($errores)->withInput();
                }


                $this->cambiarEstado($integrante,'Iniciar cambio de tipo');


                DB::commit();
                $respuestaID = 'success';
                $respuestaMSJ = 'Cambio de tipo creado con éxito';

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

    public function enviarCambioTipo($id)
    {
        ///$integranteId = $request->query('integrante_id');
        $integrante = Integrante::findOrFail($id);
        $proyecto_id = $integrante->proyecto_id;
        $error='';
        $ok = 1;

        $errores = [];




        if (empty($integrante->tipo) || empty($integrante->horas)|| empty($integrante->cambio)) {
            $errores[] = 'Complete todos los campos de la pestaña Proyectos';
        }

        if (($integrante->horas_anteriores>$integrante->horas)&&empty($integrante->reduccion)) {
            $errores[] = 'En el caso de ser una reducción horaria, especificar las consecuencias que la misma tendrá en el desarrollo del proyecto';
        }

        $this->validarEnviar($integrante,$errores);





        if (!empty($errores)) {
            return redirect()->back()->withErrors($errores)->withInput();
        }


        DB::beginTransaction();


        try {

            $investigador = $integrante->investigador;


            // Almacenar el resultado de la validación en una variable
            $resultadoValidacion = $this->validarHorasEnviar($integrante, $investigador, $proyecto_id);

            // Verificar si la validación fue exitosa
            if ($resultadoValidacion !== 'validacion_exitosa') {
                // Si no fue exitosa, retornar el resultado de la validación (que incluye el redirect()->back())
                return $resultadoValidacion;
            }


        }catch(QueryException $ex){


            $error=$ex->getMessage();
            $ok=0;

        }




        if ($ok){
            try {

                $integrante->estado = 'Cambio Tipo Recibido';
                $integrante->save();

                $this->cambiarEstado($integrante,'Envio de cambio de tipo');


                // Obtener el ID del usuario autenticado
                $userId = Auth::id();
                $user = User::find($userId); // Obtener el usuario por su ID
                $integranteMail = ($integrante->tipo=='Colaborador')?$integrante->tipo:'Integrante';

                // Preparar datos para el correo
                $datosCorreo = [
                    'from_email' => $user->email,
                    'from_name' => $user->name,
                    'asunto' => 'Solicitud de Cambio de tipo de integrante',
                    'codigo' => $integrante->proyecto->codigo,
                    'integranteMail' => $integranteMail,
                    'integrante' => $integrante->investigador->persona->apellido.', '.$integrante->investigador->persona->nombre.' ('.$integrante->investigador->persona->cuil.')',
                    'tipo' => 'Cambio de tipo de integrante',
                    'fecha' => Carbon::parse($integrante->cambio)->format('d/m/Y'), // Formatear la fecha en el formato deseado
                    'comment' => '',
                ];

                // Generar el PDF y obtener la ruta
                $pdfPath = $this->generateCambioTipoPDF(new Request(['integrante_id' => $integrante->id]),true);

                $this->enviarCorreosAlUsuario($datosCorreo,$integrante,$userId,true,$pdfPath);

                DB::commit();
                // Eliminar el archivo PDF temporal
                if (file_exists($pdfPath)) {
                    unlink($pdfPath);
                }
                $respuestaID = 'success';
                $respuestaMSJ = 'Cambio de tipo enviado con éxito';

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

    public function anularTipo($id)
    {
        $integrante = Integrante::findOrFail($id);

        $proyecto_id = $integrante->proyecto_id;
        DB::beginTransaction();
        try {

            // Obtener los estados en orden descendente por id
            $estados = IntegranteEstado::where('integrante_id', $id)
                ->orderBy('id', 'desc')
                ->get();

// Encontrar el índice del registro con el comentario "Iniciar cambio de tipo"
            $index = $estados->search(function ($estado) {
                return $estado->comentarios === "Iniciar cambio de tipo";
            });

// Obtener el siguiente registro después del encontrado
            $estadoFiltrado = null;
            if ($index !== false && $index + 1 < $estados->count()) {
                $estadoFiltrado = $estados->get($index + 1);
            }

            $integrante->estado = '';
            $integrante->alta = $estadoFiltrado->alta;
            $integrante->tipo = $estadoFiltrado->tipo;
            $integrante->horas = $estadoFiltrado->horas;
            $integrante->baja = $estadoFiltrado->baja;
            $integrante->categoria_id = ($estadoFiltrado->categoria_id)?$estadoFiltrado->categoria_id:null;
            $integrante->sicadi_id = ($estadoFiltrado->sicadi_id)?$estadoFiltrado->sicadi_id:null;
            $integrante->deddoc = $estadoFiltrado->deddoc;
            $integrante->cargo_id = ($estadoFiltrado->cargo_id)?$estadoFiltrado->cargo_id:null;
            $integrante->facultad_id = ($estadoFiltrado->facultad_id)?$estadoFiltrado->facultad_id:null;
            $integrante->unidad_id = ($estadoFiltrado->unidad_id)?$estadoFiltrado->unidad_id:null;
            $integrante->carrerainv_id = ($estadoFiltrado->carrerainv_id)?$estadoFiltrado->carrerainv_id:null;
            $integrante->organismo_id = ($estadoFiltrado->organismo_id)?$estadoFiltrado->organismo_id:null;
            $integrante->institucion = ($estadoFiltrado->institucion)?$estadoFiltrado->institucion:null;
            $integrante->beca = ($estadoFiltrado->beca)?$estadoFiltrado->beca:null;
            $integrante->save();

            $this->cambiarEstado($integrante,'Anulación de cambio de tipo');


            DB::commit();
            $respuestaID = 'success';
            $respuestaMSJ = 'Cambio de tipo anulado con éxito';

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

        return redirect()->route('integrantes.index', array('proyecto_id' => $proyecto_id))->with($respuestaID, $respuestaMSJ);
    }

    public function admitirCambioTipo($id)
    {
        $integrante = Integrante::findOrFail($id);

        $proyecto_id = $integrante->proyecto_id;
        DB::beginTransaction();
        try {

            $integrante->estado = '';
            $integrante->horas_anteriores=null;
            $integrante->cambio=null;
            $integrante->reduccion=null;
            $integrante->save();
            $investigador = Investigador::find($integrante->investigador_id);

            $this->actualizarInvestigador($integrante,$investigador);

            $this->cambiarEstado($integrante,'Confirmación de cambio de horas');


            $integranteMail = ($integrante->tipo=='Colaborador')?$integrante->tipo:'Integrante';

            // Preparar datos para el correo
            $datosCorreo = [
                'from_email' => Constants::MAIL_PROYECTOS,
                'from_name' => Constants::NOMBRE_PROYECTOS,
                'asunto' => 'Confirmación de solicitud de Cambio de tipo de integrante',
                'codigo' => $integrante->proyecto->codigo,
                'integranteMail' => $integranteMail,
                'integrante' => $integrante->investigador->persona->apellido.', '.$integrante->investigador->persona->nombre.' ('.$integrante->investigador->persona->cuil.')',
                'tipo' => 'Cambio de tipo de integrante',
                'fecha' => Carbon::parse($integrante->cambio)->format('d/m/Y'), // Formatear la fecha en el formato deseado
                'comment' => '',
            ];

            // Llama a la función enviarCorreos
            $this->enviarCorreos($proyecto_id, $datosCorreo, $integrante);


            DB::commit();
            $respuestaID = 'success';
            $respuestaMSJ = 'Confirmación con éxito';

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

        return redirect()->route('integrantes.index', array('proyecto_id' => $proyecto_id))->with($respuestaID, $respuestaMSJ);
    }

    public function rechazarCambioTipo($id)
    {
        $integrante = Integrante::find($id);

        $proyectoId = $integrante->proyecto_id;
        $proyecto = null;

        // Si se proporciona un ID de proyecto, buscalo en la base de datos
        if ($proyectoId) {
            $proyecto = Proyecto::findOrFail($proyectoId);
        }


        return view('integrantes.denyCambioTipo',compact('proyecto','integrante'));

    }

    public function saveDenyCambioTipo(Request $request, $id)
    {
        $this->validate($request, [
            'comentarios' => 'required'
        ]);

        $input = $this->sanitizeInput($request->all());

        $integrante = Integrante::findOrFail($id);

        $proyecto_id = $integrante->proyecto_id;
        DB::beginTransaction();
        try {

// Obtener los estados en orden descendente por id
            $estados = IntegranteEstado::where('integrante_id', $id)
                ->orderBy('id', 'desc')
                ->get();

// Encontrar el índice del registro con el comentario "Iniciar cambio de tipo"
            $index = $estados->search(function ($estado) {
                return $estado->comentarios === "Iniciar cambio de tipo";
            });

// Obtener el siguiente registro después del encontrado
            $estadoFiltrado = null;
            if ($index !== false && $index + 1 < $estados->count()) {
                $estadoFiltrado = $estados->get($index + 1);
            }

            $integrante->estado = '';
            $integrante->alta = $estadoFiltrado->alta;
            $integrante->tipo = $estadoFiltrado->tipo;
            $integrante->horas = $estadoFiltrado->horas;
            $integrante->baja = $estadoFiltrado->baja;
            $integrante->categoria_id = $estadoFiltrado->categoria_id;
            $integrante->sicadi_id = $estadoFiltrado->sicadi_id;
            $integrante->deddoc = $estadoFiltrado->deddoc;
            $integrante->cargo_id = $estadoFiltrado->cargo_id;
            $integrante->facultad_id = $estadoFiltrado->facultad_id;
            $integrante->unidad_id = $estadoFiltrado->unidad_id;
            $integrante->carrerainv_id = $estadoFiltrado->carrerainv_id;
            $integrante->organismo_id = $estadoFiltrado->organismo_id;
            $integrante->institucion = ($estadoFiltrado->institucion)?$estadoFiltrado->institucion:null;
            $integrante->beca = ($estadoFiltrado->beca)?$estadoFiltrado->beca:null;
            $integrante->save();

            $this->cambiarEstado($integrante,$input['comentarios']);


            $integranteMail = ($integrante->tipo=='Colaborador')?$integrante->tipo:'Integrante';

            // Preparar datos para el correo
            $datosCorreo = [
                'from_email' => Constants::MAIL_PROYECTOS,
                'from_name' => Constants::NOMBRE_PROYECTOS,
                'asunto' => 'Rechazo de solicitud de CAMBIO de tipo de integrante',
                'codigo' => $integrante->proyecto->codigo,
                'integranteMail' => $integranteMail,
                'integrante' => $integrante->investigador->persona->apellido.', '.$integrante->investigador->persona->nombre.' ('.$integrante->investigador->persona->cuil.')',
                'tipo' => 'Cambio de tipo de integrante',
                'fecha' => Carbon::parse($integrante->cambio)->format('d/m/Y'), // Formatear la fecha en el formato deseado
                'comment' => $input['comentarios'],
            ];

            // Llama a la función enviarCorreos
            $this->enviarCorreos($proyecto_id, $datosCorreo, $integrante);


            DB::commit();
            $respuestaID = 'success';
            $respuestaMSJ = 'Rechazada con éxito';

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


        return redirect()->route('integrantes.index', array('proyecto_id' => $proyecto_id))->with($respuestaID, $respuestaMSJ);
    }

    public function guardarIntegrante(Request $request, $integrante,$alta='')
    {
        // Guardar el primer título pasado en $request->titulo en la columna titulo_id del investigador
        if (!empty($request->titulos)) {
            $integrante->titulo_id = $this->safeRequest($request, 'titulos');
            $integrante->egresogrado = $this->safeRequest($request, 'egresos');
            $integrante->carrera = null;
            $integrante->total = null;
            $integrante->materias = null;
            $integrante->save();
        }

        // Guardar el primer título pasado en $request->titulopost en la columna titulopost_id del investigador
        if (!empty($request->tituloposts)) {
            $integrante->titulopost_id = $this->safeRequest($request, 'tituloposts');
            $integrante->egresoposgrado = $this->safeRequest($request, 'egresoposts');
            $integrante->save();
        }




        //log::info('Cargo: '.$request->cargos[0] );

        if ($request->cargos[0]) {
            //log::info('Lo carga');
            $integrante->cargo_id = $this->safeRequest($request, 'cargos');
        }
        else{
            //log::info('No lo carga');
            $integrante->cargo_id = null;
        }
        if ($request->deddocs[0]) {
            $integrante->deddoc = $this->safeRequest($request, 'deddocs');
        }
        else{
            $integrante->deddoc = null;
        }


        if ($request->ingresos[0]) {
            $integrante->alta_cargo = $this->safeRequest($request, 'ingresos');
            if ($integrante->alta_cargo) {
                // Verificar si $integrante->alta_cargo es una cadena y convertirla a Carbon si es necesario
                if (is_string($integrante->alta_cargo)) {
                    $integrante->alta_cargo = Carbon::parse($integrante->alta_cargo);
                }

                if ($alta){
                    // Verificar si $integrante->alta_cargo es un objeto Carbon válido y comparar fechas
                    if ($integrante->alta_cargo instanceof Carbon) {
                        // Convertir $input['alta'] a Carbon si no lo es
                        $altaInput = Carbon::parse($alta);

                        // Comparar fechas
                        if ($integrante->alta_cargo->gt($altaInput)) {
                            // Si la fecha de alta_cargo es mayor, actualizar $input['alta'] con la fecha de alta_cargo
                            $integrante->alta = $integrante->alta_cargo->toDateString(); // Ajusta el formato según necesites
                        }
                    }
                }


            }
        }
        else{
            $integrante->alta_cargo = null;
        }
        $integrante->save();


        if ($request->carrerainvs[0]) {
            $integrante->carrerainv_id = $this->safeRequest($request, 'carrerainvs');
        }
        else {
            $integrante->carrerainv_id = null;
        }
        if ($request->organismos[0]) {
            $integrante->organismo_id = $this->safeRequest($request, 'organismos');
        }
        else {
            $integrante->organismo_id = null;
        }
        if ($request->carringresos[0]) {
            $integrante->ingreso_carrerainv = $this->safeRequest($request, 'carringresos');
        }
        else {
            $integrante->ingreso_carrerainv = null;
        }
        $integrante->save();




        if ($request->becas[0]) {
            $integrante->beca = $this->safeRequest($request, 'becas');
        }
        else {
            $integrante->beca = null;
        }
        if ($request->institucions[0]) {
            $integrante->institucion = $this->safeRequest($request, 'institucions');
        }
        else {
            $integrante->institucion = null;
        }
        if ($request->becadesdes[0]) {
            $integrante->alta_beca = $this->safeRequest($request, 'becadesdes');

            if ($integrante->alta_beca){
                // Verificar si $integrante->alta_beca es una cadena y convertirla a Carbon si es necesario
                if (is_string($integrante->alta_beca)) {
                    $integrante->alta_beca = Carbon::parse($integrante->alta_beca);
                }

                if ($alta) {
                    // Verificar si $integrante->alta_beca es un objeto Carbon válido y comparar fechas
                    if ($integrante->alta_beca instanceof Carbon) {
                        // Convertir $input['alta'] a Carbon si no lo es
                        $altaInput = Carbon::parse($alta);

                        // Comparar fechas
                        if ($integrante->alta_beca->gt($altaInput)) {
                            // Si la fecha de alta_beca es mayor, actualizar $input['alta'] con la fecha de alta_beca
                            $integrante->alta = $integrante->alta_beca->toDateString(); // Ajusta el formato según necesites
                        }
                    }
                }

            }
        }
        else {
            $integrante->alta_beca = null;
        }
        if ($request->becahastas[0]) {
            $integrante->baja_beca = $this->safeRequest($request, 'becahastas');
        }
        else {
            $integrante->baja_beca = null;
        }
        $integrante->save();
    }

    public function validarEnviar($integrante,&$errores)
    {
        if (empty($integrante->investigador->persona->nombre) || empty($integrante->investigador->persona->apellido) || empty($integrante->investigador->persona->cuil) || empty($integrante->email)|| empty($integrante->nacimiento)) {
            $errores[] = 'Complete todos los campos de la pestaña Datos Personales';
        }

        if (empty($integrante->universidad_id) ) {
            $errores[] = 'Falta seleccionar la universidad en la pestaña Universidad';
        }

        if ((empty($integrante->titulo_id))&&((empty($integrante->carrera))||(empty($integrante->materias))||(empty($integrante->total)))){

            $errores[] = 'Debe especificar Carrera, Total de materia y Materias adeudadas (si no es estudiante complete el título de grado)';
        }

        if (
            (empty($integrante->titulo_id) && empty($integrante->egresogrado)) ||
            (!empty($integrante->titulo_id) && !empty($integrante->egresogrado) )
        ) {

        }else{
            $errores[] = 'Complete todos los campos del título de grado en la pestaña Universidad';
        }

        if (
            (empty($integrante->titulopost_id) && empty($integrante->egresoposgrado)) ||
            (!empty($integrante->titulopost_id) && !empty($integrante->egresoposgrado) )
        ) {

        }else{
            $errores[] = 'Complete todos los campos del título de posgrado en la pestaña Universidad';
        }

        if (
            (empty($integrante->cargo_id) && empty($integrante->deddoc) && empty($integrante->alta_cargo)) ||
            (!empty($integrante->cargo_id) && !empty($integrante->deddoc) && !empty($integrante->alta_cargo))
        ) {

        }else{

            $errores[] = 'Complete todos los campos del Cargo Docente en la pestaña Universidad';
        }
        if ($integrante->tipo != 'Colaborador') {
            if (
                (empty($integrante->cargo_id) && empty($integrante->carrerainv_id) && empty($integrante->beca))
            ) {
                $errores[] = 'Si no posee cargo, debe ser becario o tener un cargo en la carrera de investigación';
            }
        }


        if (empty($integrante->unidad_id) ) {
            $errores[] = 'Falta seleccionar el Lugar de Trabajo en la pestaña Investigación';
        }

        if (
            (empty($integrante->carrerainv_id) && empty($integrante->organismo_id) && empty($integrante->ingreso_carrerainv)) ||
            (!empty($integrante->carrerainv_id) && !empty($integrante->organismo_id) && !empty($integrante->ingreso_carrerainv))
        ) {

        }else{
            $errores[] = 'Complete todos los campos de la Carrera de Investigación en la pestaña Investigación';
        }
        // Verificar si los campos requeridos están presentes en Becas
        if (
            (empty($integrante->institucion) && empty($integrante->beca) && empty($integrante->alta_beca) && empty($integrante->baja_beca)) ||
            (!empty($integrante->institucion) && !empty($integrante->beca) && !empty($integrante->alta_beca) && !empty($integrante->baja_beca))
        ) {
            if ($integrante->tipo == 'Becario, Tesista'){
                if (empty($integrante->resolucion)) {
                    $errores[] = 'Falta subir el certificado de alumno de Doctorado/Maestría';
                } else {
                    $filePath = public_path($integrante->resolucion);
                    if (!file_exists($filePath)) {
                        $errores[] = 'Hubo un error al subir el certificado de alumno de Doctorado/Maestría, intente nuevamente, si el problema persiste envíenos un mail a proyectos.secyt@presi.unlp.edu.ar';
                    }
                }
            }
        } else {
            $errores[] = 'Complete todos los campos de la pestaña Becas';
            if (empty($integrante->resolucion)) {
                $errores[] = 'Falta subir la Resolución de la Beca';
            } else {
                $filePath = public_path($integrante->resolucion);
                if (!file_exists($filePath)) {
                    $errores[] = 'Hubo un error al subir la Resolución de la Beca, intente nuevamente, si el problema persiste envíenos un mail a proyectos.secyt@presi.unlp.edu.ar';
                }
            }

        }
        if (($integrante->tipo != 'Becario, Tesista')&&(!empty($integrante->institucion) || !empty($integrante->beca) || !empty($integrante->alta_beca) || !empty($integrante->baja_beca))){
            $errores[] = 'Si el integrante posee beca el tipo debe ser Becario, Tesista';
        }
        if (!empty($integrante->baja_beca)){
            $integrante->baja_beca = Carbon::parse($integrante->baja_beca);
            // Verificar si $integrante->alta_beca es un objeto Carbon válido y comparar fechas
            if ($integrante->baja_beca instanceof Carbon) {
                // Comparar fechas
                $today = now();
                if ($today->gt($integrante->baja_beca)) {
                    $errores[] = 'La beca finalizó';
                }
            }
        }

        if (!empty($integrante->alta_beca) && !empty($integrante->baja_beca)) {
            $integrante->alta_beca = Carbon::parse($integrante->alta_beca);
            $integrante->baja_beca = Carbon::parse($integrante->baja_beca);
            // Verificar si $integrante->alta_beca y $integrante->baja_beca son objetos Carbon válidos y comparar fechas
            if ($integrante->alta_beca instanceof Carbon && $integrante->baja_beca instanceof Carbon) {
                // Comparar fechas para asegurarse de que alta_beca es menor que baja_beca
                if ($integrante->alta_beca->gt($integrante->baja_beca)) {
                    $errores[] = 'La fecha de inicio de la beca no puede ser mayor que la fecha de finalización';
                }
            }
        }
    }
}
