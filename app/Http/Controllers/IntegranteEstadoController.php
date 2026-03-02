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
use App\Traits\SanitizesInput;
class IntegranteEstadoController extends Controller
{
    use SanitizesInput;
    function __construct()
    {
        $this->middleware('permission:integrante_estado-listar|integrante_estado-crear|integrante_estado-editar|integrante_estado-eliminar', ['only' => ['index','store','dataTable']]);
        $this->middleware('permission:integrante_estado-crear', ['only' => ['create','store','buscarInvestigador']]);
        $this->middleware('permission:integrante_estado-editar', ['only' => ['edit','update']]);
        $this->middleware('permission:integrante_estado-eliminar', ['only' => ['destroy']]);
        //dd(session()->all());
    }





    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $integranteId = $request->query('integrante_id');
        $integrante = null;

        // Si se proporciona un ID de integrante, buscalo en la base de datos
        if ($integranteId) {
            $integrante = Integrante::findOrFail($integranteId);
        }

        // Pasar el integrante (si existe) a la vista
        return view('integrante_estados.index', compact('integrante'));
    }

    public function dataTable(Request $request)
    {

        $integranteId = $request->input('integrante_id');
        $columnas = ['personas.nombre','integrante_estados.estado','proyectos.codigo', 'personas.apellido','integrante_estados.tipo', 'categorias.nombre', 'sicadis.nombre', 'cargos.nombre','integrante_estados.deddoc','integrante_estados.beca','integrante_estados.institucion', 'carrerainvs.nombre', 'organismos.codigo','integrante_estados.alta','integrante_estados.baja','integrante_estados.cambio', 'facultads.nombre','integrante_estados.horas','desde','hasta','integrante_estados.comentarios',DB::raw("IFNULL(users.name, integrante_estados.user_name)") ]; // Define las columnas disponibles
        $columnaOrden = $columnas[$request->input('order.0.column')];
        $orden = $request->input('order.0.dir');
        $busqueda = $request->input('search.value');



        // Consulta base
        $query = IntegranteEstado::select('integrante_estados.id as id', 'personas.nombre as persona_nombre','integrante_estados.estado','proyectos.codigo as codigo', DB::raw("CONCAT(personas.apellido, ', ', personas.nombre) as persona_apellido"),'integrante_estados.tipo as tipo', 'categorias.nombre as categoria_nombre', 'sicadis.nombre as sicadi_nombre', 'cargos.nombre as cargo_nombre','integrante_estados.deddoc', DB::raw("CONCAT(integrante_estados.beca, ' ', integrante_estados.institucion) as beca"),'integrante_estados.institucion', DB::raw("CONCAT(carrerainvs.nombre, ' ', organismos.codigo) as carrerainv_nombre"), 'organismos.codigo as organismo_nombre','integrante_estados.alta as alta','integrante_estados.baja as baja','integrante_estados.cambio as cambio', 'facultads.nombre as facultad_nombre', 'integrante_estados.horas as horas', 'integrante_estados.desde as desde', 'integrante_estados.hasta as hasta', 'integrante_estados.comentarios as comentarios',
            DB::raw("IFNULL(users.name, integrante_estados.user_name) as usuario_nombre") )
            ->leftJoin('integrantes', 'integrante_estados.integrante_id', '=', 'integrantes.id')
            ->leftJoin('users', 'integrante_estados.user_id', '=', 'users.id')
            ->leftJoin('categorias', 'integrante_estados.categoria_id', '=', 'categorias.id')
            ->leftJoin('investigadors', 'integrantes.investigador_id', '=', 'investigadors.id')
            ->leftJoin('proyectos', 'integrantes.proyecto_id', '=', 'proyectos.id')
            ->leftJoin('personas', 'investigadors.persona_id', '=', 'personas.id')
            ->leftJoin('sicadis', 'integrante_estados.sicadi_id', '=', 'sicadis.id')
            ->leftJoin('cargos', 'integrante_estados.cargo_id', '=', 'cargos.id')
            ->leftJoin('carrerainvs', 'integrante_estados.carrerainv_id', '=', 'carrerainvs.id')
            ->leftJoin('organismos', 'integrante_estados.organismo_id', '=', 'organismos.id')
            ->leftJoin('facultads', 'integrante_estados.facultad_id', '=', 'facultads.id');


        // Aplicar filtro por proyecto si se proporciona el ID del proyecto
        if ($integranteId) {
            $query->where('integrante_estados.integrante_id', $integranteId);
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
        $recordsTotal = IntegranteEstado::count();

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
        $integranteId = $request->input('integrante_id');
        $integrante = null;

        // Si se proporciona un ID de integrante, buscalo en la base de datos
        if ($integranteId) {
            $integrante = Integrante::findOrFail($integranteId);
        }
        //dd($integrante);
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
        return view('integrante_estados.create',compact('titulos','tituloposts','facultades','cargos','universidades','unidads','carrerainvs','sicadis','years','organismos','categorias','sicadis','integrante'));
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

        $integrante_id=$input['integrante_id'];


        $integrante = Integrante::find($integrante_id);

        DB::beginTransaction();
        $ok = 1;







        if ($ok){
            try {

                //$integrante = Integrante::update($input);
                $integrante->update($input);
                $investigador = Investigador::find($integrante->investigador_id);

                $titulos = collect($request->titulos)
                    ->filter()
                    ->values();

                if ($titulos->isNotEmpty()) {

                    $integrante->titulo_id = $this->safeRequest($request, 'titulos');
                    $integrante->egresogrado = $this->safeRequest($request, 'egresos');
                    $integrante->carrera = null;
                    $integrante->total = null;
                    $integrante->materias = null;
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
                else {
                    // 👇 SI LO BORRÓ, LIMPIAR
                    $integrante->titulo_id = null;
                    $integrante->egresogrado = null;
                }


                $tituloposts = collect($request->tituloposts)
                    ->filter()
                    ->values();

                if ($tituloposts->isNotEmpty()) {
                    $integrante->titulopost_id = $this->safeRequest($request, 'tituloposts');
                    $integrante->egresoposgrado = $this->safeRequest($request, 'egresoposts');
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
                else {
                    // 👇 SI LO BORRÓ, LIMPIAR
                    $integrante->titulopost_id = null;
                    $integrante->egresoposgrado = null;
                }





                if ($request->cargos[0]) {
                    $integrante->cargo_id = $request->cargos[0];
                    $integrante->deddoc = $request->deddocs[0];
                    $integrante->facultad_id = $request->facultads[0];
                    $integrante->universidad_id = $request->universidads[0];
                    $integrante->alta_cargo = $request->ingresos[0];

                    //$integrante->save();

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
                else{
                    $integrante->cargo_id = null;
                    $integrante->deddoc = null;
                    $integrante->facultad_id = null;
                    $integrante->universidad_id = null;
                    $integrante->alta_cargo = null;
                }




                if ($request->carrerainvs[0]) {
                    $integrante->carrerainv_id = $request->carrerainvs[0];
                    $integrante->organismo_id = $request->organismos[0];
                    $integrante->ingreso_carrerainv = $request->carringresos[0];
                    //$integrante->save();
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
                else{
                    $integrante->carrerainv_id = null;
                    $integrante->organismo_id = null;
                    $integrante->ingreso_carrerainv = null;
                }
                if ($request->categorias[0]) {
                    $integrante->categoria_id = $request->categorias[0];

                    //$integrante->save();
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
                else{
                    $integrante->categoria_id = null;
                }

                if ($request->sicadis[0]) {
                    $integrante->sicadi_id = $request->sicadis[0];

                    //$integrante->save();
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
                else{
                    $integrante->sicadi_id = null;
                }


                if ($request->becas[0]) {
                    $integrante->beca = $request->becas[0];;
                    $integrante->institucion = $request->institucions[0];
                    $integrante->alta_beca = $request->becadesdes[0];
                    $integrante->baja_beca = $request->becahastas[0];
                    /*if ($integrante->alta_beca){
                        if ($integrante->alta_beca->gt(Carbon::parse($input['alta']))) {
                            // Actualizar la fecha de alta en $input['alta'] con la fecha de alta del cargo
                            $input['alta'] = $integrante->alta_beca;
                        }
                    }*/
                    //$integrante->save();

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
                else{
                    $integrante->beca = null;
                    $integrante->institucion = null;
                    $integrante->alta_beca = null;
                    $integrante->baja_beca = null;
                }
                $integrante->save();
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
                    'unidad_id' => $integrante->unidad_id,
                    'institucion' => ($integrante->institucion)?$integrante->institucion:null,
                    'beca' => ($integrante->beca)?$integrante->beca:null,
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

        return redirect()->route('integrante_estados.index', array('integrante_id' => $integrante_id))->with($respuestaID, $respuestaMSJ);

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
