<?php

namespace App\Http\Controllers;

use App\Models\Carrerainv;
use App\Models\Categoria;
use App\Models\Persona;
use App\Models\Sicadi;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Models\Investigador;
use App\Models\Universidad;
use App\Models\Unidad;
use App\Models\Titulo;
use App\Models\Cargo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use App\Traits\SanitizesInput;

class InvestigadorController extends Controller

{
    use SanitizesInput;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
        $this->middleware('permission:investigador-listar|investigador-crear|investigador-editar|investigador-eliminar', ['only' => ['index','store','dataTable']]);
        $this->middleware('permission:investigador-crear', ['only' => ['create','store','buscarInvestigador']]);
        $this->middleware('permission:investigador-editar', ['only' => ['edit','update']]);
        $this->middleware('permission:investigador-eliminar', ['only' => ['destroy']]);
        //dd(session()->all());
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        //$investigadors = Investigador::all();
        return view ('investigadors.index');
    }


    public function clearFilter(Request $request)
    {
        // Limpiar el valor del filtro en la sesión
        $request->session()->forget('nombre_filtro_investigador');
        //Log::info('Sesion limpia:', $request->session()->all());
        return response()->json(['status' => 'success']);
    }


    public function dataTable(Request $request)
    {
        $columnas = ['personas.nombre','ident', 'personas.apellido', 'cuil', 'categorias.nombre', 'sicadis.nombre', 'cargos.nombre','deddoc','beca','institucion', 'carrerainvs.nombre', 'organismos.codigo', 'facultads.nombre']; // Define las columnas disponibles
        $columnaOrden = $columnas[$request->input('order.0.column')];
        $orden = $request->input('order.0.dir');
        // Obtener valor de búsqueda
        $busqueda = $request->input('search.value', null);



        // Consulta base
        $query = Investigador::select('investigadors.id as id', 'personas.nombre as persona_nombre','ident', DB::raw("CONCAT(personas.apellido, ', ', personas.nombre) as persona_apellido"), 'cuil', 'categorias.nombre as categoria_nombre', 'sicadis.nombre as sicadi_nombre', 'cargos.nombre as cargo_nombre','deddoc', DB::raw("CONCAT(beca, ' ', institucion) as beca"),'institucion', DB::raw("CONCAT(carrerainvs.nombre, ' ', organismos.codigo) as carrerainv_nombre"), 'organismos.codigo as organismo_nombre', 'facultads.nombre as facultad_nombre')
            ->leftJoin('categorias', 'investigadors.categoria_id', '=', 'categorias.id')
            ->leftJoin('personas', 'investigadors.persona_id', '=', 'personas.id')
            ->leftJoin('sicadis', 'investigadors.sicadi_id', '=', 'sicadis.id')
            ->leftJoin('cargos', 'investigadors.cargo_id', '=', 'cargos.id')
            ->leftJoin('carrerainvs', 'investigadors.carrerainv_id', '=', 'carrerainvs.id')
            ->leftJoin('organismos', 'investigadors.organismo_id', '=', 'organismos.id')
            ->leftJoin('facultads', 'investigadors.facultad_id', '=', 'facultads.id');


        /*Log::info("Busqueda antes: " . $busqueda);

        Log::info('Sesion busqueda:', $request->session()->all());*/
        if (!empty($busqueda)) {

            //Log::info("Busqueda no vacia: " . $busqueda);
            $request->session()->put('nombre_filtro_investigador', $busqueda);

        }
        else{
            //Log::info("Busqueda vacia: " . $busqueda);
            $busqueda = $request->session()->get('nombre_filtro_investigador');

        }
        /*Log::info("Busqueda despues: " . $busqueda);
        Log::info('Sesion busqueda:', $request->session()->all());*/
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
        $recordsTotal = Investigador::count();

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
    public function create()
    {
        $provincias = DB::table('provincias')->OrderBy('nombre')->pluck('nombre', 'id'); // Obtener todas las provincias
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
        return view('investigadors.create',compact('provincias','titulos','tituloposts','facultades','cargos','universidades','unidads','carrerainvs','sicadis','years','organismos','categorias','sicadis'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'nombre' => 'required',
            'apellido' => 'required',
            'email' => 'required|email',
            'documento' => 'required',
            'nacimiento' => 'nullable|date',
            'fallecimiento' => 'nullable|date',
            'cuil' => 'nullable|regex:/^\d{2}-\d{8}-\d{1}$/', // Validación de cuil
            'foto' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        'titulos.*' => 'nullable', // Titulos puede estar vacío
        'egresos.*' => 'nullable|date_format:Y-m-d', // Validación del formato de fecha
    ]);

    // Recoger datos del request
        $input = $this->sanitizeInput($request->all());

    // Validación de fechas de egreso
    $errores = [];
    if (!empty($request->titulos) && !empty($request->egresos)) {
        foreach ($request->egresos as $index => $egreso) {
            if (!empty($egreso) && !Carbon::canBeParsed($egreso)) {
                $errores[] = "Formato de fecha no válido para el título '{$request->titulos[$index]}'";
            }
        }
    }

    if (!empty($errores)) {
        return back()->withErrors($errores)->withInput();
    }

        // Manejo de la imagen
        $input['foto'] ='';
        if ($files = $request->file('foto')) {
            $image = $request->file('foto');
            $name = time().'.'.$image->getClientOriginalExtension();
            $destinationPath = public_path('/images');
            $image->move($destinationPath, $name);
            $input['foto'] = "$name";
        }

        DB::beginTransaction();
        $ok = 1;

        try {
            // Obtener el máximo ident de los investigadores
            $ultimoId = Investigador::max('ident');
            $nuevoIdent = $ultimoId + 1; // Incrementar en 1 el último identificador
            $input['ident'] = $nuevoIdent; // Asignar el nuevo ident al input

            // Crear la persona y luego el investigador
            $persona = Persona::create($input);
            $investigador = $persona->investigador()->create($input);
        }catch(QueryException $ex){

            try {
                $persona = Persona::where('documento','=',$input['documento'])->first();
                if (!empty($persona)){
                    $persona->update($input);
                    $investigador=$persona->investigador()->create($input);

                }
            }catch(QueryException $ex){
                if ($ex->errorInfo[1] == 1062) {
                    $error='El documento está en uso';
                } else {
                    // Si no es por una clave duplicada, maneja la excepción de manera general
                    $error=$ex->getMessage();
                }

                $ok=0;

            }


        }

        if ($ok){
            // Guardar el primer título pasado en $request->titulo en la columna titulo_id del investigador
            if (!empty($request->titulos)) {
                $investigador->titulo_id = $request->titulos[0];
                $investigador->carrera = null;
                $investigador->total = null;
                $investigador->materias = null;
                $investigador->save();
            }

            // Guardar el primer título pasado en $request->titulopost en la columna titulopost_id del investigador
            if (!empty($request->tituloposts)) {
                $investigador->titulopost_id = $request->tituloposts[0];
                $investigador->save();
            }

            // Guardar los títulos en las relaciones
            if (!empty($request->titulos)) {
                foreach ($request->titulos as $item => $v) {
                    $investigador->titulos()->attach($request->titulos[$item], ['egreso'=> $request->egresos[$item], 'created_at' => now(), 'updated_at' => now()]);
                }
            }

            if (!empty($request->tituloposts)) {
                foreach ($request->tituloposts as $item => $v) {
                    $investigador->tituloposts()->attach($request->tituloposts[$item], ['egreso'=> $request->egresoposts[$item], 'created_at' => now(), 'updated_at' => now()]);
                }
            }
            $mayorCargo = null;
            $mayorDeddoc = null;
            $mayorFacultad = null;
            $mayorUniversidad = null;
            if (!empty($request->cargos)) {

                foreach ($request->cargos as $item => $v) {
                    $activo=0;
                    if (isset($request->activos[$item]) ) {
                        $activo=1;
                        if ($mayorDeddoc === null || $request->deddocs[$item] < $mayorDeddoc) {
                            $mayorDeddoc = $request->deddocs[$item];
                            $mayorCargo = $request->cargos[$item];
                            $mayorFacultad = $request->facultads[$item];
                            $mayorUniversidad = $request->universidads[$item];
                            if ($request->deddocs[$item] == $mayorDeddoc) {
                                if ($mayorCargo === null || $this->esMayorCargo($request->cargos[$item], $mayorCargo)) {
                                    $mayorCargo = $request->cargos[$item];
                                    $mayorFacultad = $request->facultads[$item];
                                    $mayorUniversidad = $request->universidads[$item];
                                }
                            }
                        }
                    }

                    // Inserta el registro en la tabla intermedia 'investigador_cargos'
                    DB::table('investigador_cargos')->insert([
                        'investigador_id' => $investigador->id, // Supongo que tienes un objeto $investigador disponible
                        'cargo_id' => $request->cargos[$item],
                        'deddoc' => $request->deddocs[$item],
                        'ingreso' => $request->ingresos[$item],
                        'facultad_id' => $request->facultads[$item],
                        'universidad_id' => $request->universidads[$item],
                        'activo' => $activo,
                        'created_at' => now(), // Establece la fecha y hora de creación
                        'updated_at' => now(), // Establece la fecha y hora de actualización
                    ]);
                }
            }
            // Guarda el mayor cargo encontrado en el investigador
            if ($mayorCargo !== null) {
                $investigador->cargo_id = $mayorCargo;
                $investigador->deddoc = $mayorDeddoc;
                $investigador->facultad_id = $mayorFacultad;
                $investigador->universidad_id = $mayorUniversidad;
                $investigador->save();
            }

            if (!empty($request->carrerainvs)) {
                $esActual=0;
                foreach ($request->carrerainvs as $item => $v) {
                    // Verifica si el radio "actual" está seleccionado para esta fila
                    /*$esActual = isset($request['actual_' . ($item + 1)]) && $request['actual_' . ($item + 1)] == 1;
                    if ($esActual){
                        $carrerainv_id = $request->carrerainvs[$item];
                        $organismo_id = $request->organismos[$item];
                    }*/

                    if ($request->actual == ($item + 1)) {
                        // Esta es la fila que se considera "actual"
                        $carrerainv_id = $request->carrerainvs[$item];
                        $organismo_id = $request->organismos[$item];
                        $esActual=1;

                    }


                    // Inserta el registro en la tabla intermedia 'investigador_cargos'
                    DB::table('investigador_carreras')->insert([
                        'investigador_id' => $investigador->id, // Supongo que tienes un objeto $investigador disponible
                        'carrerainv_id' => $request->carrerainvs[$item],
                        'organismo_id' => $request->organismos[$item],
                        'ingreso' => $request->carringresos[$item],

                        'actual' => $esActual,
                        'created_at' => now(), // Establece la fecha y hora de creación
                        'updated_at' => now(), // Establece la fecha y hora de actualización
                    ]);
                }
            }
            if (!empty($request->carrerainvs)) {
                $investigador->carrerainv_id = $carrerainv_id;
                $investigador->organismo_id = $organismo_id;
                $investigador->save();
            }

            if (!empty($request->categorias)) {
                $esCatActual=0;
                foreach ($request->categorias as $item => $v) {


                    if ($request->catactual == ($item + 1)) {
                        // Esta es la fila que se considera "actual"
                        $categoria_id = $request->categorias[$item];

                        $esCatActual=1;

                    }


                    // Inserta el registro en la tabla intermedia 'investigador_cargos'
                    DB::table('investigador_categorias')->insert([
                        'investigador_id' => $investigador->id, // Supongo que tienes un objeto $investigador disponible
                        'categoria_id' => $request->categorias[$item],
                        'universidad_id' => $request->catuniversidads[$item],
                        'notificacion' => $request->catnotificacions[$item],
                        'year' => $request->catyears[$item],
                        'actual' => $esCatActual,
                        'created_at' => now(), // Establece la fecha y hora de creación
                        'updated_at' => now(), // Establece la fecha y hora de actualización
                    ]);
                }
            }
            if (!empty($request->categorias)) {
                $investigador->categoria_id = $categoria_id;

                $investigador->save();
            }

            if (!empty($request->sicadis)) {
                $essicadiActual=0;
                foreach ($request->sicadis as $item => $v) {


                    if ($request->sicadiactual == ($item + 1)) {
                        // Esta es la fila que se considera "actual"
                        $sicadi_id = $request->sicadis[$item];

                        $essicadiActual=1;

                    }


                    // Inserta el registro en la tabla intermedia 'investigador_cargos'
                    DB::table('investigador_sicadis')->insert([
                        'investigador_id' => $investigador->id, // Supongo que tienes un objeto $investigador disponible
                        'sicadi_id' => $request->sicadis[$item],

                        'notificacion' => $request->sicadinotificacions[$item],
                        'year' => $request->sicadiyears[$item],
                        'actual' => $essicadiActual,
                        'created_at' => now(), // Establece la fecha y hora de creación
                        'updated_at' => now(), // Establece la fecha y hora de actualización
                    ]);
                }
            }
            if (!empty($request->sicadis)) {
                $investigador->sicadi_id = $sicadi_id;

                $investigador->save();
            }

            if (!empty($request->becas)) {
                $institucionSeleccionad='';
                $becaSeleccionada='';

                foreach ($request->becas as $item => $v) {


                    $esUnlp = 0;
                    if (isset($request->becaunlps[$item]) ) {
                        $esUnlp=1;
                    }
                    // Comprueba si el rango de fechas es actual o si no hay fecha de finalización
                    $fechaHasta = $request->becahastas[$item] ?? null;
                    $esRangoActual = ($fechaHasta === null || Carbon::parse($fechaHasta)->isFuture());

                    if ($esRangoActual) {
                        $institucionSeleccionad=$request->institucions[$item];
                        $becaSeleccionada=$request->becas[$item];
                    }



                    // Inserta el registro en la tabla intermedia 'investigador_cargos'
                    DB::table('investigador_becas')->insert([
                        'investigador_id' => $investigador->id, // Supongo que tienes un objeto $investigador disponible
                        'beca' => $request->becas[$item],

                        'institucion' => $request->institucions[$item],
                        'desde' => $request->becadesdes[$item],
                        'hasta' => $request->becahastas[$item],
                        'unlp' => $esUnlp,
                        'created_at' => now(), // Establece la fecha y hora de creación
                        'updated_at' => now(), // Establece la fecha y hora de actualización
                    ]);
                }
            }
            if (!empty($request->becas)) {
                $investigador->beca = $becaSeleccionada;
                $investigador->institucion = $institucionSeleccionad;
                $investigador->save();
            }

            DB::commit();
            $respuestaID = 'success';
            $respuestaMSJ = 'Investigador creado con éxito';
        }
        else{
            DB::rollback();
            $respuestaID='error';
            $respuestaMSJ=$error;
        }

        return redirect()->route('investigadors.index')->with($respuestaID, $respuestaMSJ);
    }

// Función para determinar si un cargo es mayor que otro
    function esMayorDecicacion($deddocActual, $deddocMayor)
    {
        $ordenDeddocs = ['Exclusiva', 'Semi Exclusiva', 'Simple'];
        $indiceActual = array_search($deddocActual, $ordenDeddocs);
        $indiceMayor = array_search($deddocMayor, $ordenDeddocs);
        Log::info("Actual: " . $deddocActual . " - Mayor: ".$deddocMayor);
        Log::info("Es mayor: " . ($indiceActual > $indiceMayor)?'SI':'NO');
        return $indiceActual > $indiceMayor;
    }

    function esMayorCargo($cargoActual, $cargoMayor)
    {
        // Obtener el orden de los cargos desde la base de datos
        // Obtener el orden del cargo actual
        $ordenCargoActual = Cargo::where('id', $cargoActual)->value('orden');

// Obtener el orden del cargo mayor
        $ordenCargoMayor = Cargo::where('id', $cargoMayor)->value('orden');

        // Si el orden es igual, compara directamente los nombres de los cargos
        if ($ordenCargoActual === $ordenCargoMayor) {
            return $cargoActual < $cargoMayor;
        }

        // Retorna verdadero si el orden del cargo actual es menor (mayor prioridad)
        return $ordenCargoActual < $ordenCargoMayor;
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $investigador = Investigador::find($id);
        //dd($investigador->titulos);
        $provincias = DB::table('provincias')->OrderBy('nombre')->pluck('nombre', 'id'); // Obtener todas las provincias
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
        return view('investigadors.show',compact('investigador','provincias','titulos','tituloposts','facultades','cargos','universidades','unidads','carrerainvs','sicadis','years','organismos','categorias','sicadis'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $investigador = Investigador::find($id);
        //dd($investigador->titulos);
        $provincias = DB::table('provincias')->OrderBy('nombre')->pluck('nombre', 'id'); // Obtener todas las provincias
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
        return view('investigadors.edit',compact('investigador','provincias','titulos','tituloposts','facultades','cargos','universidades','unidads','carrerainvs','sicadis','years','organismos','categorias','sicadis'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {


        // Definir las reglas de validación
        $rules = [
            'nombre' => 'required',
            'apellido' => 'required',
            'cuil' => 'required|regex:/^\d{2}-\d{8}-\d{1}$/',
            'email' => 'required|email',
            'documento' => 'required',
            'nacimiento' => 'nullable|date',
            'fallecimiento' => 'nullable|date',
            'titulos.*' => 'nullable', // Titulos puede estar vacío
            'egresos.*' => 'nullable|date_format:Y-m-d', // Validación del formato de fecha
        ];

        // Definir los mensajes de error personalizados
        $messages = [
            'cuil.regex' => 'El formato del CUIL es inválido.',
            'egresos.*.date_format' => 'Fecha inválida en el título de grado',
        ];


        // Crear el validador con las reglas y mensajes
        $validator = Validator::make($request->all(), $rules, $messages);
        // Validar y verificar si hay errores
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        // Recoger datos del request
        $input = $this->sanitizeInput($request->all());

        // Validación de fechas de egreso
        /*$errores = [];
        if (!empty($request->titulos) && !empty($request->egresos)) {
            foreach ($request->egresos as $index => $egreso) {
                if (!empty($egreso) && !Carbon::canBeParsed($egreso)) {
                    $errores[] = "Formato de fecha no válido para el título '{$request->titulos[$index]}'";
                }
            }
        }

        if (!empty($errores)) {
            return back()->withErrors($errores)->withInput();
        }*/

        // Manejo de la imagen
        $input['foto'] ='';
        if ($files = $request->file('foto')) {
            $image = $request->file('foto');
            $name = time().'.'.$image->getClientOriginalExtension();
            $destinationPath = public_path('/images');
            $image->move($destinationPath, $name);
            $input['foto'] = "$name";
        }
        $investigador = Investigador::find($id);
        DB::beginTransaction();
        $ok = 1;

        try {
            $investigador->update($input);

            $update['nombre'] = $request->get('nombre');
            $update['apellido'] = $request->get('apellido');
            $update['email'] = $request->get('email');
            $update['telefono'] = $request->get('telefono');
            $update['calle'] = $request->get('calle');
            $update['nro'] = $request->get('nro');
            $update['piso'] = $request->get('piso');
            $update['depto'] = $request->get('depto');
            $update['localidad'] = $request->get('localidad');
            $update['provincia_id'] = $request->get('provincia_id');
            $update['cp'] = $request->get('cp');
            $update['genero'] = $request->get('genero');
            if ($input['foto']){
                $update['foto'] = $input['foto'];
            }

            $update['observaciones'] = $request->get('observaciones');
            $update['cuil'] = $request->get('cuil');
            $update['documento'] = $request->get('documento');
            $update['nacimiento'] = $request->get('nacimiento');
            $update['fallecimiento'] = $request->get('fallecimiento');


            $investigador->persona()->update($update);
        }catch(QueryException $ex){


                if ($ex->errorInfo[1] == 1062) {
                    $error='El documento está en uso';
                } else {
                    // Si no es por una clave duplicada, maneja la excepción de manera general
                    $error=$ex->getMessage();
                }

                $ok=0;




        }

        if ($ok){
            $investigador->titulos()->detach();
            $investigador->tituloposts()->detach();
            $investigador->cargos()->detach();
            $investigador->carrerainvs()->detach();
            $investigador->categorias()->detach();
            $investigador->sicadis()->detach();
            $investigador->becas()->delete();
            // Guardar el primer título pasado en $request->titulo en la columna titulo_id del investigador
            if (!empty($request->titulos)) {
                $investigador->titulo_id = $request->titulos[0];
                $investigador->carrera = null;
                $investigador->total = null;
                $investigador->materias = null;
                $investigador->save();
            }

            // Guardar el primer título pasado en $request->titulopost en la columna titulopost_id del investigador
            if (!empty($request->tituloposts)) {
                $investigador->titulopost_id = $request->tituloposts[0];
                $investigador->save();
            }

            // Guardar los títulos en las relaciones
            if (!empty($request->titulos)) {
                foreach ($request->titulos as $item => $v) {
                    $investigador->titulos()->attach($request->titulos[$item], ['egreso'=> $request->egresos[$item], 'created_at' => now(), 'updated_at' => now()]);
                }
            }

            if (!empty($request->tituloposts)) {
                foreach ($request->tituloposts as $item => $v) {
                    $investigador->tituloposts()->attach($request->tituloposts[$item], ['egreso'=> $request->egresoposts[$item], 'created_at' => now(), 'updated_at' => now()]);
                }
            }
            $mayorCargo = null;
            $mayorDeddoc = null;
            $mayorFacultad = null;
            $mayorUniversidad = null;
            if (!empty($request->cargos)) {

                foreach ($request->cargos as $item => $v) {
                    $activo=0;
                    if (isset($request->activos[$item]) ) {
                        $activo=1;
                        if ($mayorDeddoc === null || $request->deddocs[$item] < $mayorDeddoc) {
                            $mayorDeddoc = $request->deddocs[$item];
                            $mayorCargo = $request->cargos[$item];
                            $mayorFacultad = $request->facultads[$item];
                            $mayorUniversidad = $request->universidads[$item];
                            if ($request->deddocs[$item] == $mayorDeddoc) {
                                if ($mayorCargo === null || $this->esMayorCargo($request->cargos[$item], $mayorCargo)) {
                                    $mayorCargo = $request->cargos[$item];
                                    $mayorFacultad = $request->facultads[$item];
                                    $mayorUniversidad = $request->universidads[$item];
                                }
                            }
                        }
                    }

                    // Inserta el registro en la tabla intermedia 'investigador_cargos'
                    DB::table('investigador_cargos')->insert([
                        'investigador_id' => $investigador->id, // Supongo que tienes un objeto $investigador disponible
                        'cargo_id' => $request->cargos[$item],
                        'deddoc' => $request->deddocs[$item],
                        'ingreso' => $request->ingresos[$item],
                        'facultad_id' => $request->facultads[$item],
                        'universidad_id' => $request->universidads[$item],
                        'activo' => $activo,
                        'created_at' => now(), // Establece la fecha y hora de creación
                        'updated_at' => now(), // Establece la fecha y hora de actualización
                    ]);
                }
            }
            // Guarda el mayor cargo encontrado en el investigador
            if ($mayorCargo !== null) {
                $investigador->cargo_id = $mayorCargo;
                $investigador->deddoc = $mayorDeddoc;
                $investigador->facultad_id = $mayorFacultad;
                $investigador->universidad_id = $mayorUniversidad;
                $investigador->save();
            }

            if (!empty($request->carrerainvs)) {
                $esActual=0;
                foreach ($request->carrerainvs as $item => $v) {
                    // Verifica si el radio "actual" está seleccionado para esta fila
                    /*$esActual = isset($request['actual_' . ($item + 1)]) && $request['actual_' . ($item + 1)] == 1;
                    if ($esActual){
                        $carrerainv_id = $request->carrerainvs[$item];
                        $organismo_id = $request->organismos[$item];
                    }*/

                    if ($request->actual == ($item + 1)) {
                        // Esta es la fila que se considera "actual"
                        $carrerainv_id = $request->carrerainvs[$item];
                        $organismo_id = $request->organismos[$item];
                        $esActual=1;

                    }


                    // Inserta el registro en la tabla intermedia 'investigador_cargos'
                    DB::table('investigador_carreras')->insert([
                        'investigador_id' => $investigador->id, // Supongo que tienes un objeto $investigador disponible
                        'carrerainv_id' => $request->carrerainvs[$item],
                        'organismo_id' => $request->organismos[$item],
                        'ingreso' => $request->carringresos[$item],

                        'actual' => $esActual,
                        'created_at' => now(), // Establece la fecha y hora de creación
                        'updated_at' => now(), // Establece la fecha y hora de actualización
                    ]);
                }
            }
            if (!empty($request->carrerainvs)) {
                $investigador->carrerainv_id = $carrerainv_id;
                $investigador->organismo_id = $organismo_id;
                $investigador->save();
            }

            if (!empty($request->categorias)) {
                $esCatActual=0;
                foreach ($request->categorias as $item => $v) {


                    if ($request->catactual == ($item + 1)) {
                        // Esta es la fila que se considera "actual"
                        $categoria_id = $request->categorias[$item];

                        $esCatActual=1;

                    }


                    // Inserta el registro en la tabla intermedia 'investigador_cargos'
                    DB::table('investigador_categorias')->insert([
                        'investigador_id' => $investigador->id, // Supongo que tienes un objeto $investigador disponible
                        'categoria_id' => $request->categorias[$item],
                        'universidad_id' => $request->catuniversidads[$item],
                        'notificacion' => $request->catnotificacions[$item],
                        'year' => $request->catyears[$item],
                        'actual' => $esCatActual,
                        'created_at' => now(), // Establece la fecha y hora de creación
                        'updated_at' => now(), // Establece la fecha y hora de actualización
                    ]);
                }
            }
            if (!empty($request->categorias)) {
                $investigador->categoria_id = $categoria_id;

                $investigador->save();
            }

            if (!empty($request->sicadis)) {
                $essicadiActual=0;
                foreach ($request->sicadis as $item => $v) {


                    if ($request->sicadiactual == ($item + 1)) {
                        // Esta es la fila que se considera "actual"
                        $sicadi_id = $request->sicadis[$item];

                        $essicadiActual=1;

                    }


                    // Inserta el registro en la tabla intermedia 'investigador_cargos'
                    DB::table('investigador_sicadis')->insert([
                        'investigador_id' => $investigador->id, // Supongo que tienes un objeto $investigador disponible
                        'sicadi_id' => $request->sicadis[$item],

                        'notificacion' => $request->sicadinotificacions[$item],
                        'year' => $request->sicadiyears[$item],
                        'actual' => $essicadiActual,
                        'created_at' => now(), // Establece la fecha y hora de creación
                        'updated_at' => now(), // Establece la fecha y hora de actualización
                    ]);
                }
            }
            if (!empty($request->sicadis)) {
                $investigador->sicadi_id = $sicadi_id;

                $investigador->save();
            }
            //dd($request->institucions);
            if (!empty($request->becas)) {
                $institucionSeleccionad='';
                $becaSeleccionada='';

                foreach ($request->becas as $item => $v) {


                    $esUnlp = 0;
                    if (isset($request->becaunlps[$item]) ) {
                        $esUnlp=1;
                    }
                    // Comprueba si el rango de fechas es actual o si no hay fecha de finalización
                    $fechaHasta = $request->becahastas[$item] ?? null;
                    $esRangoActual = ($fechaHasta === null || Carbon::parse($fechaHasta)->isFuture());

                    if ($esRangoActual) {
                        $institucionSeleccionad=$request->institucions[$item];
                        $becaSeleccionada=$request->becas[$item];
                    }



                    // Inserta el registro en la tabla intermedia 'investigador_cargos'
                    DB::table('investigador_becas')->insert([
                        'investigador_id' => $investigador->id, // Supongo que tienes un objeto $investigador disponible
                        'beca' => $request->becas[$item],

                        'institucion' => $request->institucions[$item],
                        'desde' => $request->becadesdes[$item],
                        'hasta' => $request->becahastas[$item],
                        'unlp' => $esUnlp,
                        'created_at' => now(), // Establece la fecha y hora de creación
                        'updated_at' => now(), // Establece la fecha y hora de actualización
                    ]);
                }
            }
            if (!empty($request->becas)) {
                if ($esRangoActual) {
                    $investigador->beca = $becaSeleccionada;
                    $investigador->institucion = $institucionSeleccionad;
                    $investigador->save();
                }
            }

            DB::commit();
            $respuestaID = 'success';
            $respuestaMSJ = 'Investigador modificado con éxito';
        }
        else{
            DB::rollback();
            $respuestaID='error';
            $respuestaMSJ=$error;
        }

        return redirect()->route('investigadors.index')->with($respuestaID, $respuestaMSJ);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $investigador = Investigador::findOrFail($id);







        // Elimina las relaciones
        $investigador->titulos()->detach();
        $investigador->tituloposts()->detach();
        $investigador->cargos()->detach();
        $investigador->carrerainvs()->detach();
        $investigador->categorias()->detach();
        $investigador->sicadis()->detach();
        $investigador->becas()->delete();

        // Elimina el investigador
        $investigador->delete();

        return redirect()->route('investigadors.index')
            ->with('success','Investigador eliminado con éxito');
    }
}
