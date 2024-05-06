<?php

namespace App\Http\Controllers;

use App\Models\Persona;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Models\Investigador;
use App\Models\Universidad;
use App\Models\Titulo;
use DB;
use Illuminate\Support\Facades\Log;

class InvestigadorController extends Controller

{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
        $this->middleware('permission:investigador-listar|investigador-crear|investigador-editar|investigador-eliminar', ['only' => ['index','store','dataTable']]);
        $this->middleware('permission:investigador-crear', ['only' => ['create','store']]);
        $this->middleware('permission:investigador-editar', ['only' => ['edit','update']]);
        $this->middleware('permission:investigador-eliminar', ['only' => ['destroy']]);
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

    public function dataTable(Request $request)
    {
        $columnas = ['personas.nombre','ident', 'personas.apellido', 'cuil', 'categorias.nombre', 'sicadis.nombre', 'cargos.nombre','deddoc','beca','institucion', 'carrerainvs.nombre', 'organismos.codigo', 'facultads.nombre']; // Define las columnas disponibles
        $columnaOrden = $columnas[$request->input('order.0.column')];
        $orden = $request->input('order.0.dir');
        $busqueda = $request->input('search.value');

        $query = Investigador::query();

        // Unir la tabla de categoriaes para poder ordenar y filtrar por el nombre de la categoria
        $query->select('investigadors.id as id', 'personas.nombre as persona_nombre','ident', DB::raw("CONCAT(personas.apellido, ', ', personas.nombre) as persona_apellido"), 'cuil', 'categorias.nombre as categoria_nombre', 'sicadis.nombre as sicadi_nombre', 'cargos.nombre as cargo_nombre','deddoc', DB::raw("CONCAT(beca, ' ', institucion) as beca"),'institucion', DB::raw("CONCAT(carrerainvs.nombre, ' ', organismos.codigo) as carrerainv_nombre"), 'organismos.codigo as organismo_nombre', 'facultads.nombre as facultad_nombre');
        $query->leftJoin('categorias', 'investigadors.categoria_id', '=', 'categorias.id');
        $query->leftJoin('personas', 'investigadors.persona_id', '=', 'personas.id');
        $query->leftJoin('sicadis', 'investigadors.sicadi_id', '=', 'sicadis.id');
        $query->leftJoin('cargos', 'investigadors.cargo_id', '=', 'cargos.id');
        $query->leftJoin('carrerainvs', 'investigadors.carrerainv_id', '=', 'carrerainvs.id');
        $query->leftJoin('organismos', 'investigadors.organismo_id', '=', 'organismos.id');
        $query->leftJoin('facultads', 'investigadors.facultad_id', '=', 'facultads.id');


        foreach ($columnas as $columna) {
            $query->orWhere($columna, 'like', "%$busqueda%");
        }

        // Obtener la cantidad total de registros después de aplicar el filtro de búsqueda
        $recordsFiltered = $query->count();

        // Obtener solo los elementos paginados
        $datos = $query->orderBy($columnaOrden, $orden)
            ->skip($request->input('start'))
            ->take($request->input('length'))
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
        $cargos = DB::table('cargos')->orderBy('orden')->pluck('nombre', 'id')->prepend('','');
        return view('investigadors.create',compact('provincias','titulos','tituloposts','facultades','cargos'));
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
            'cuil' => 'nullable|regex:/^\d{2}-\d{8}-\d{1}$/', // Validación de cuil
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        $input = $request->all();

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

            if (!empty($request->cargos)) {
                $mayorCargo = null;
                $mayorDeddoc = null;
                foreach ($request->cargos as $item => $v) {
                    Log::info("Cargo: " . $request->cargos[$item] . " - Dedicacion: ".$request->deddocs[$item]);
                    Log::info("Mayor Cargo: " . $mayorCargo . " - Mayor Dedicacion: ".$mayorDeddoc);
                    if ($mayorDeddoc === null || $this->esMayorDecicacion($request->deddocs[$item], $mayorDeddoc)) {
                        $mayorDeddoc = $request->deddocs[$item];
                        $mayorCargo = $request->cargos[$item];
                        if ($request->deddocs[$item]==$mayorDeddoc){
                            if ($mayorCargo === null || $this->esMayorCargo($request->cargos[$item], $mayorCargo)) {
                                $mayorCargo = $request->cargos[$item];
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
                        'activo' => $request->has('activos') && $request->activos[$item] == '1', // Convierte a booleano
                        'created_at' => now(), // Establece la fecha y hora de creación
                        'updated_at' => now(), // Establece la fecha y hora de actualización
                    ]);
                }
            }
            // Guarda el mayor cargo encontrado en el investigador
            if ($mayorCargo !== null) {
                $investigador->cargo_id = $mayorCargo;
                $investigador->deddoc = $mayorDeddoc;
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
        $ordenDeddocs = ['Simple', 'Semi Exclusiva', 'Exclusiva'];
        $indiceActual = array_search($deddocActual, $ordenDeddocs);
        $indiceMayor = array_search($deddocMayor, $ordenDeddocs);
        Log::info("Actual: " . $deddocActual . " - Mayor: ".$deddocMayor);
        return $indiceActual < $indiceMayor;
    }

    function esMayorCargo($cargoActual, $cargoMayor)
    {
        // Obtener el orden de los cargos desde la base de datos
        // Obtener el orden del cargo actual
        $ordenCargoActual = DB::table('cargos')->where('nombre', $cargoActual)->value('orden');

// Obtener el orden del cargo mayor
        $ordenCargoMayor = DB::table('cargos')->where('nombre', $cargoMayor)->value('orden');

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
        return view('investigadors.show',compact('investigador'));
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

        $universidads=Universidad::orderBy('nombre','ASC')->get();
        $universidads = $universidads->pluck('nombre', 'id')->prepend('','');
        return view('investigadors.edit',compact('investigador','universidads'));
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
        $this->validate($request, [
            'nombre' => 'required'

        ]);

        $input = $request->all();




        $investigador = Investigador::find($id);
        $investigador->update($input);



        return redirect()->route('investigadors.index')
            ->with('success','Investigador modificado con éxito');
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

        // Eliminar las relaciones de los cargos del investigador
        DB::table('investigador_cargos')->where('investigador_id', $investigador->id)->delete();

        // Elimina las relaciones
        $investigador->titulos()->detach();
        $investigador->tituloposts()->detach();

        // Elimina el investigador
        $investigador->delete();

        return redirect()->route('investigadors.index')
            ->with('success','Investigador eliminado con éxito');
    }
}
