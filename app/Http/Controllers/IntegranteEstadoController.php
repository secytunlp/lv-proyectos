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

class IntegranteEstadoController extends Controller
{

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
        $columnas = ['personas.nombre','integrante_estados.estado','proyectos.codigo', 'personas.apellido','integrante_estados.tipo', 'categorias.nombre', 'sicadis.nombre', 'cargos.nombre','integrantes.deddoc','integrantes.beca','integrantes.institucion', 'carrerainvs.nombre', 'organismos.codigo','integrante_estados.alta','integrante_estados.baja','integrante_estados.cambio', 'facultads.nombre','integrante_estados.horas','desde','hasta','integrante_estados.comentarios',DB::raw("IFNULL(users.name, integrante_estados.user_name)") ]; // Define las columnas disponibles
        $columnaOrden = $columnas[$request->input('order.0.column')];
        $orden = $request->input('order.0.dir');
        $busqueda = $request->input('search.value');



        // Consulta base
        $query = IntegranteEstado::select('integrante_estados.id as id', 'personas.nombre as persona_nombre','integrante_estados.estado','proyectos.codigo as codigo', DB::raw("CONCAT(personas.apellido, ', ', personas.nombre) as persona_apellido"),'integrante_estados.tipo as tipo', 'categorias.nombre as categoria_nombre', 'sicadis.nombre as sicadi_nombre', 'cargos.nombre as cargo_nombre','integrantes.deddoc', DB::raw("CONCAT(integrantes.beca, ' ', integrantes.institucion) as beca"),'integrantes.institucion', DB::raw("CONCAT(carrerainvs.nombre, ' ', organismos.codigo) as carrerainv_nombre"), 'organismos.codigo as organismo_nombre','integrantes.alta as alta','integrantes.baja as baja','integrantes.cambio as cambio', 'facultads.nombre as facultad_nombre', 'integrantes.horas as horas', 'integrante_estados.desde as desde', 'integrante_estados.hasta as hasta', 'integrante_estados.comentarios as comentarios',
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

        $integrante_id=$input['integrante_id'];


        $integrante = Integrante::find($integrante_id);

        DB::beginTransaction();
        $ok = 1;







        if ($ok){
            try {

                //$integrante = Integrante::update($input);
                $integrante->update($input);
                $investigador = Investigador::find($integrante->investigador_id);
                // Guardar el primer título pasado en $request->titulo en la columna titulo_id del investigador
                if (!empty($request->titulos)) {
                    $integrante->titulo_id = $request->titulos[0];
                    $integrante->egresogrado = $request->egresos[0];
                    $integrante->carrera = null;
                    $integrante->total = null;
                    $integrante->materias = null;
                    $integrante->save();
                    if ($investigador->titulos->count()===0){

                        $investigador->titulos()->attach($integrante->titulo_id, ['egreso' => $integrante->egresogrado, 'created_at' => now(), 'updated_at' => now()]);
                    }
                    else {
                        foreach ($investigador->titulos as $titulo) {
                            //Log::info("Actual: " . $titulo->id . " - Nuevo: ".$integrante->titulo_id );
                            if ($titulo->id != $integrante->titulo_id) {
                                $investigador->titulos()->attach($integrante->titulo_id, ['egreso' => $integrante->egresogrado, 'created_at' => now(), 'updated_at' => now()]);
                            }
                        }
                    }
                }

                // Guardar el primer título pasado en $request->titulopost en la columna titulopost_id del investigador
                if (!empty($request->tituloposts)) {
                    $integrante->titulopost_id = $request->tituloposts[0];
                    $integrante->egresoposgrado = $request->egresoposts[0];
                    $integrante->save();
                    //Log::info("Nuevo titulo: ".$integrante->titulopost_id );
                    //dd($investigador->tituloposts);
                    if ($investigador->tituloposts->count()===0){
                        //Log::info("Nuevo titulo 2: ".$integrante->titulopost_id );
                        $investigador->tituloposts()->attach($integrante->titulopost_id, ['egreso'=> $integrante->egresoposgrado, 'created_at' => now(), 'updated_at' => now()]);
                    }
                    else{
                        foreach ($investigador->tituloposts as $titulo){
                            //Log::info("Actual: " . $titulo->id . " - Nuevo: ".$integrante->titulopost_id );
                            if ($titulo->id!=$integrante->titulopost_id){
                                $investigador->tituloposts()->attach($integrante->titulopost_id, ['egreso'=> $integrante->egresoposgrado, 'created_at' => now(), 'updated_at' => now()]);
                            }
                        }
                    }

                }




                if ($request->cargos[0]) {
                    $integrante->cargo_id = $request->cargos[0];
                    $integrante->deddoc = $request->deddocs[0];
                    $integrante->facultad_id = $request->facultads[0];
                    $integrante->universidad_id = $request->universidads[0];
                    $integrante->alta_cargo = $request->ingresos[0];

                    $integrante->save();
                    if ($investigador->cargos->count()===0){
                        // Inserta el registro en la tabla intermedia 'investigador_cargos'
                        DB::table('investigador_cargos')->insert([
                            'investigador_id' => $investigador->id, // Supongo que tienes un objeto $investigador disponible
                            'cargo_id' => $integrante->cargo_id,
                            'deddoc' => $integrante->deddoc,
                            'ingreso' => $integrante->alta_cargo,
                            'facultad_id' => $integrante->facultad_id,
                            'universidad_id' => $integrante->universidad_id,
                            'activo' => 1,
                            'created_at' => now(), // Establece la fecha y hora de creación
                            'updated_at' => now(), // Establece la fecha y hora de actualización
                        ]);
                    }
                    else{
                        foreach ($investigador->cargos as $cargo){
                            Log::info("Actual: " . $cargo->id . " - Nuevo: ".$integrante->cargo_id );
                            if (($cargo->id!=$integrante->cargo_id)&&($cargo->deddoc!=$integrante->deddoc)&&($cargo->ingreso!=$integrante->alta_cargo)&&($cargo->facultad_id!=$integrante->facultad_id)){
                                DB::table('investigador_cargos')->insert([
                                    'investigador_id' => $investigador->id, // Supongo que tienes un objeto $investigador disponible
                                    'cargo_id' => $integrante->cargo_id,
                                    'deddoc' => $integrante->deddoc,
                                    'ingreso' => $integrante->alta_cargo,
                                    'facultad_id' => $integrante->facultad_id,
                                    'universidad_id' => $integrante->universidad_id,
                                    'activo' => 1,
                                    'created_at' => now(), // Establece la fecha y hora de creación
                                    'updated_at' => now(), // Establece la fecha y hora de actualización
                                ]);
                            }
                        }
                    }
                }


                if ($request->carrerainvs[0]) {
                    $integrante->carrerainv_id = $request->carrerainvs[0];
                    $integrante->organismo_id = $request->organismos[0];
                    $integrante->ingreso_carrerainv = $request->carringresos[0];
                    $integrante->save();
                    if ($investigador->carrerainvs->count()===0){
                        // Inserta el registro en la tabla intermedia 'investigador_cargos'
                        DB::table('investigador_carreras')->insert([
                            'investigador_id' => $investigador->id, // Supongo que tienes un objeto $investigador disponible
                            'carrerainv_id' => $integrante->carrerainv_id,
                            'organismo_id' => $integrante->organismo_id,
                            'ingreso' => $integrante->ingreso_carrerainv,

                            'actual' => 1,
                            'created_at' => now(), // Establece la fecha y hora de creación
                            'updated_at' => now(), // Establece la fecha y hora de actualización
                        ]);
                    }
                    else{
                        foreach ($investigador->carrerainvs as $carrerainv){
                            //Log::info("Actual: " . $cargo->id . " - Nuevo: ".$integrante->cargo_id );
                            if ($carrerainv->id!=$integrante->carrerainv_id){
                                DB::table('investigador_carreras')->insert([
                                    'investigador_id' => $investigador->id, // Supongo que tienes un objeto $investigador disponible
                                    'carrerainv_id' => $integrante->carrerainv_id,
                                    'organismo_id' => $integrante->organismo_id,
                                    'ingreso' => $integrante->ingreso_carrerainv,

                                    'actual' => 1,
                                    'created_at' => now(), // Establece la fecha y hora de creación
                                    'updated_at' => now(), // Establece la fecha y hora de actualización
                                ]);
                            }
                        }
                    }
                }
                if ($request->categorias[0]) {
                    $integrante->categoria_id = $request->categorias[0];

                    $integrante->save();
                    if ($investigador->categorias->count()===0){

                        DB::table('investigador_categorias')->insert([
                            'investigador_id' => $investigador->id, // Supongo que tienes un objeto $investigador disponible
                            'categoria_id' => $integrante->categoria_id,

                            'actual' => 1,
                            'created_at' => now(), // Establece la fecha y hora de creación
                            'updated_at' => now(), // Establece la fecha y hora de actualización
                        ]);
                    }
                    else{
                        foreach ($investigador->categorias as $categoria){
                            //Log::info("Actual: " . $cargo->id . " - Nuevo: ".$integrante->cargo_id );
                            if ($categoria->id!=$integrante->categoria_id){

                                DB::table('investigador_categorias')->insert([
                                    'investigador_id' => $investigador->id, // Supongo que tienes un objeto $investigador disponible
                                    'categoria_id' => $integrante->categoria_id,

                                    'actual' => 1,
                                    'created_at' => now(), // Establece la fecha y hora de creación
                                    'updated_at' => now(), // Establece la fecha y hora de actualización
                                ]);
                            }
                        }
                    }
                }

                if ($request->sicadis[0]) {
                    $integrante->sicadi_id = $request->sicadis[0];

                    $integrante->save();
                    if ($investigador->sicadis->count()===0){

                        DB::table('investigador_categorias')->insert([
                            'investigador_id' => $investigador->id, // Supongo que tienes un objeto $investigador disponible
                            'sicadi_id' => $integrante->sicadi_id,

                            'actual' => 1,
                            'created_at' => now(), // Establece la fecha y hora de creación
                            'updated_at' => now(), // Establece la fecha y hora de actualización
                        ]);
                    }
                    else{
                        foreach ($investigador->sicadis as $sicadi){
                            //Log::info("Actual: " . $cargo->id . " - Nuevo: ".$integrante->cargo_id );
                            if ($sicadi->id!=$integrante->sicadi_id){

                                DB::table('investigador_sicadis')->insert([
                                    'investigador_id' => $investigador->id, // Supongo que tienes un objeto $investigador disponible
                                    'sicadi_id' => $integrante->sicadi_id,

                                    'actual' => 1,
                                    'created_at' => now(), // Establece la fecha y hora de creación
                                    'updated_at' => now(), // Establece la fecha y hora de actualización
                                ]);
                            }
                        }
                    }
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
                    if ($investigador->becas->count()===0){
                        // Inserta el registro en la tabla intermedia 'investigador_cargos'
                        DB::table('investigador_becas')->insert([
                            'investigador_id' => $investigador->id, // Supongo que tienes un objeto $investigador disponible
                            'beca' => $integrante->beca,

                            'institucion' => $integrante->institucion,
                            'desde' => $integrante->alta_beca,
                            'hasta' => $integrante->baja_beca,
                            'unlp' => 0,
                            'created_at' => now(), // Establece la fecha y hora de creación
                            'updated_at' => now(), // Establece la fecha y hora de actualización
                        ]);
                    }
                    else{
                        foreach ($investigador->becas as $beca){
                            //Log::info("Actual: " . $cargo->id . " - Nuevo: ".$integrante->cargo_id );
                            if (($beca->id!=$integrante->beca_id)&&($beca->institucion!=$integrante->institucion)){
                                DB::table('investigador_becas')->insert([
                                    'investigador_id' => $investigador->id, // Supongo que tienes un objeto $investigador disponible
                                    'beca' => $integrante->beca,

                                    'institucion' => $integrante->institucion,
                                    'desde' => $integrante->alta_beca,
                                    'hasta' => $integrante->baja_beca,
                                    'unlp' => 0,
                                    'created_at' => now(), // Establece la fecha y hora de creación
                                    'updated_at' => now(), // Establece la fecha y hora de actualización
                                ]);
                            }
                        }
                    }
                }

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
