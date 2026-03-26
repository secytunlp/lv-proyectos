<?php

namespace App\Http\Controllers;

use App\Constants;
use App\Models\Cargo;
use App\Models\Carrerainv;
use App\Models\Categoria;
use App\Models\MiembroEstado;
use App\Models\Miembro;
use App\Models\Investigador;
use App\Models\Persona;
use App\Models\Sicadi;
use App\Models\Titulo;
use App\Models\Unidad;
use App\Models\UnidadInvestigacion;
use App\Models\Universidad;
use App\Traits\SanitizesInput;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class MiembroController extends Controller
{
    use SanitizesInput;
    function __construct()
    {
        $this->middleware('permission:miembro-listar|miembro-crear|miembro-editar|miembro-eliminar', ['only' => ['index','store','dataTable','admitir']]);
        $this->middleware('permission:miembro-crear', ['only' => ['create','store','buscarInvestigador','generateAltaPDF','archivos']]);
        $this->middleware('permission:miembro-editar', ['only' => ['edit','update','enviar']]);
        $this->middleware('permission:miembro-eliminar', ['only' => ['destroy']]);
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
        return view('miembros.index', compact('unidad'));
    }




    public function dataTable(Request $request)
    {

        $unidadId = $request->input('unidad_id');
        $columnas = ['miembros.nombre','unidad_investigacions.denominacion','unidad_investigacions.sigla','miembros.tipo', 'apellido', 'cuil', 'facultads.nombre','horas', 'miembros.estado',DB::raw("CASE WHEN miembros.activo = 1 THEN 'SI' ELSE 'NO' END")]; // Define las columnas disponibles
        $columnaOrden = $columnas[$request->input('order.0.column')];
        $orden = $request->input('order.0.dir');


        $busqueda = $request->input('search.value');



        // Consulta base
        $query = Miembro::select('miembros.id as id', 'miembros.nombre as persona_nombre','unidad_investigacions.denominacion','unidad_investigacions.sigla','miembros.tipo as tipo', DB::raw("CONCAT(miembros.apellido, ', ', miembros.nombre) as persona_apellido"), 'cuil', 'facultads.nombre as facultad_nombre', 'miembros.horas as horas', 'miembros.estado as estado',DB::raw("CASE WHEN miembros.activo = 1 THEN 'SI' ELSE 'NO' END as activo"))


            ->leftJoin('unidad_investigacions', 'miembros.unidad_id', '=', 'unidad_investigacions.id')


            ->leftJoin('facultads', 'miembros.facultad_id', '=', 'facultads.id');


        // Aplicar filtro por unidad si se proporciona el ID del unidad
        if ($unidadId) {
            $query->where('miembros.unidad_id', $unidadId);
        }
        $user = auth()->user();
        $selectedRoleId = session('selected_rol');
        if ($selectedRoleId != 1) {
            $query->where(function ($query) {
                $query->whereColumn('miembros.alta', '!=', 'miembros.baja')
                    ->orWhereNull('miembros.alta')
                    ->orWhereNull('miembros.baja');
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
                        ->from('miembros as i')
                        ->join('unidad_investigacions as p', 'i.unidad_id', '=', 'p.id')
                        ->join('investigadors as inv', 'i.investigador_id', '=', 'inv.id')
                        ->join('personas as per', 'inv.persona_id', '=', 'per.id')
                        ->where('i.tipo', 'Director')
                        ->where('per.cuil', '=', $user->cuil)
                        ->where('p.inicio', '<=', $currentDate)
                        ->where('p.fin', '>=', $currentDate)
                        ->where('p.estado', '=', 'Acreditado')
                        ->whereColumn('i.unidad_id', 'miembros.unidad_id');
                });
            });
        }
        if ($selectedRoleId == 4) {

            $query->where(function ($query) use ($user) {
                $query->whereExists(function ($subquery) use ($user) {
                    $subquery->select(DB::raw(1))
                        ->from('miembros as i')
                        ->join('unidad_investigacions as p', 'i.unidad_id', '=', 'p.id')
                        ->join('investigadors as inv', 'i.investigador_id', '=', 'inv.id')
                        ->join('personas as per', 'inv.persona_id', '=', 'per.id')
                        ->where('i.tipo', 'Director')
                        ->where('p.facultad_id', '=', $user->facultad_id)

                        ->where('p.estado', '=', 'Acreditado')
                        ->whereColumn('i.unidad_id', 'miembros.unidad_id');
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
        $recordsTotal = Miembro::count();

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
        //$provincias = DB::table('provincias')->OrderBy('nombre')->pluck('nombre', 'id'); // Obtener todas las provincias

        $facultades = DB::table('facultads')->pluck('nombre', 'id')->prepend('','');// Obtener todas las facultades directamente desde la tabla
        // Obtener los cargos ordenados por el campo 'orden' y seleccionar solo los campos 'id' y 'nombre'
        $cargos = Cargo::orderBy('orden')->pluck('nombre', 'id')->prepend('', '');

        $carrerainvs = Carrerainv::where('activo','1')->orderBy('orden')->pluck('nombre', 'id')->prepend('', '');
        $organismos = DB::table('organismos')->where('activo','1')->pluck('codigo', 'id')->prepend('','');

        $categorias = Categoria::orderBy('id')->pluck('nombre', 'id')->prepend('', '');

        $sicadis = Sicadi::orderBy('id')->pluck('nombre', 'id')->prepend('', '');
        return view('miembros.create',compact('facultades','cargos','carrerainvs','sicadis','organismos','categorias','sicadis','unidad'));
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
            /*'curriculum' => 'file|mimes:pdf,doc,docx|max:4048',
            'actividades' => 'file|mimes:pdf,doc,docx|max:4048',
            'resolucion' => 'file|mimes:pdf,doc,docx|max:4048',*/
        ];

        // Definir los mensajes de error personalizados
        $messages = [
            'cuil.regex' => 'El formato del CUIL es inválido.',
            /*'curriculum.mimes' => 'El archivo de curriculum debe ser un documento de tipo: pdf, doc, docx.',
            'curriculum.max' => 'El archivo de curriculum no debe ser mayor a 4 MB.',
            'actividades.mimes' => 'El archivo de Plan de trabajo debe ser un documento de tipo: pdf, doc, docx.',
            'actividades.max' => 'El archivo de Plan de trabajo no debe ser mayor a 4 MB.',
            'resolucion.mimes' => 'El archivo de Resolución Beca ó Certificado de alumno de Doctorado/Maestría debe ser un documento de tipo: pdf, doc, docx.',
            'resolucion.max' => 'El archivo de Resolución Beca ó Certificado de alumno de Doctorado/Maestría no debe ser mayor a 4 MB.',*/
        ];

        // Crear el validador con las reglas y mensajes
        $validator = Validator::make($request->all(), $rules, $messages);

        // Añadir la validación personalizada para la fecha de cierre
        /*$validator->after(function ($validator) {
            $today = now();
            $cierreDate = \Carbon\Carbon::parse(Constants::CIERRE);
            if ($today->gt($cierreDate)) {
                $validator->errors()->add('convocatoria', 'La convocatoria no está vigente.');
            }
        });*/

        // Validar y verificar si hay errores
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }




        $input = $this->sanitizeInput($request->all());

        // Validar Director único por unidad
        if ($input['tipo'] == 'Director') {
            $existeDirector = Miembro::where('unidad_id', $input['unidad_id'])
                ->where('tipo', 'Director')
                ->where('estado', 'Activo')
                ->exists();

            if ($existeDirector) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Ya existe un Director activo en esta unidad.');
            }
        }

// Validar Codirector único por unidad
        if ($input['tipo'] == 'Codirector') {
            $existeCodirector = Miembro::where('unidad_id', $input['unidad_id'])
                ->where('tipo', 'Codirector')
                ->where('estado', 'Activo')
                ->exists();

            if ($existeCodirector) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Ya existe un Codirector activo en esta unidad.');
            }
        }

// Validar que la persona esté activa en una sola unidad
        $activoOtraUnidad = Miembro::where('cuil', $input['cuil'])
            ->where('estado', 'Activo')
            ->where('unidad_id', '!=', $input['unidad_id'])
            ->exists();

        if ($activoOtraUnidad) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'La persona ya se encuentra activa en otra unidad.');
        }

        $unidad_id=$input['unidad_id'];

        // Crear la carpeta si no existe
        /*$destinationPath = public_path('/files/' . Constants::YEAR.'/'.$unidad_id);
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0777, true);
        }*/


        //$input['alta']= Constants::YEAR.'-01-01';
        $input['estado']= 'Alta Creada';
        $input['activo'] = isset($request->activo) ? 1 : 0;
        $input['estudiante'] = isset($request->estudiante) ? 1 : 0;
        // Manejo de archivos


        /*$input['curriculum'] = '';
        if ($request->hasFile('curriculum')) {
            $file = $request->file('curriculum');
            $filename = 'CV_' . Str::uuid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('public/files/' . Constants::YEAR.'/'.$unidad_id, $filename);
            $input['curriculum'] = Storage::url($path); // Genera URL tipo /storage/files/...
        }




        $input['actividades'] = '';
        if ($request->hasFile('actividades')) {
            $file = $request->file('actividades');
            $filename = 'PLAN_' . Str::uuid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('public/files/' . Constants::YEAR.'/'.$unidad_id, $filename);
            $input['actividades'] = Storage::url($path); // Genera URL tipo /storage/files/...
        }



        $input['resolucion'] = '';
        if ($request->hasFile('resolucion')) {
            $file = $request->file('resolucion');
            $filename = 'RES_' . Str::uuid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('public/files/' . Constants::YEAR.'/'.$unidad_id, $filename);
            $input['resolucion'] = Storage::url($path); // Genera URL tipo /storage/files/...
        }*/





        DB::beginTransaction();
        $ok = 1;


        $persona = Persona::where('cuil','=',$input['cuil'])->first();
        if (!empty($persona)){
            $investigador = $persona->investigador;

        }






        if ($ok){
            try {

                $input['categoria_id']= $investigador->categoria_id;
                $input['sicadi_id']= $investigador->sicadi_id;
                // Si no se encuentra un miembro existente, crear uno nuevo
                $motivo='Nuevo miembro';
                $miembro = Miembro::create($input);

                $this->guardarMiembro($request,$miembro);


                $this->cambiarEstado($miembro,$motivo);


                DB::commit();
                $respuestaID = 'success';
                $respuestaMSJ = 'Alta creada con éxito';

            } catch (QueryException $ex) {
                // Manejar la excepción de la base de datos
                DB::rollback();
                if ($ex->errorInfo[1] == 1062) {
                    $respuestaID = 'error';
                    $respuestaMSJ = 'El/la miembro ya forma parte del unidad.';
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

        return redirect()->route('miembros.index', array('unidad_id' => $unidad_id))->with($respuestaID, $respuestaMSJ);

    }

    public function guardarMiembro(Request $request, $miembro)
    {
        //dd($request,$miembro);





        if ($request->cargos[0]) {
            //log::info('Lo carga');
            $miembro->cargo_id = $this->safeRequest($request, 'cargos');
        }
        else{
            //log::info('No lo carga');
            $miembro->cargo_id = null;
        }
        if ($request->deddocs[0]) {
            $miembro->deddoc = $this->safeRequest($request, 'deddocs');
        }
        else{
            $miembro->deddoc = null;
        }





        if ($request->carrerainvs[0]) {
            $miembro->carrerainv_id = $this->safeRequest($request, 'carrerainvs');
        }
        else {
            $miembro->carrerainv_id = null;
        }
        if ($request->organismos[0]) {
            $miembro->organismo_id = $this->safeRequest($request, 'organismos');
        }
        else {
            $miembro->organismo_id = null;
        }






        if ($request->becas[0]) {
            $miembro->beca = $this->safeRequest($request, 'becas');
        }
        else {
            $miembro->beca = null;
        }
        if ($request->institucions[0]) {
            $miembro->beca .= ' - '.$this->safeRequest($request, 'institucions');
        }

        if ($request->becadesdes[0]) {
            $alta_beca = $this->safeRequest($request, 'becadesdes');

            if ($alta_beca){
                // Verificar si $miembro->alta_beca es una cadena y convertirla a Carbon si es necesario
                if (is_string($alta_beca)) {
                    $miembro->beca .= ' ('.$miembro->alta_beca;
                }



            }
        }

        if ($request->becahastas[0]) {
            $baja_beca = $this->safeRequest($request, 'becahastas');

            if ($baja_beca){
                // Verificar si $miembro->baja_beca es una cadena y convertirla a Carbon si es necesario
                if (is_string($baja_beca)) {
                    $miembro->beca .= ' - '.$miembro->baja_beca.')';
                }



            }
        }

        $miembro->save();
    }

    public function cambiarEstado($miembro, $comentarios)
    {

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
            'comentarios' => $comentarios,
            'desde' => Carbon::now(), // Establecer 'desde' como la fecha actual

        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Proyecto  $proyecto
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $miembro = Miembro::find($id);

        $unidadId = $miembro->unidad_id;
        $unidad = null;

        // Si se proporciona un ID de unidad, buscalo en la base de datos
        if ($unidadId) {
            $unidad = UnidadInvestigacion::findOrFail($unidadId);
        }


        $facultades = DB::table('facultads')->pluck('nombre', 'id')->prepend('','');// Obtener todas las facultades directamente desde la tabla
        // Obtener los cargos ordenados por el campo 'orden' y seleccionar solo los campos 'id' y 'nombre'
        $cargos = Cargo::orderBy('orden')->pluck('nombre', 'id')->prepend('', '');
        $carrerainvs = Carrerainv::where('activo','1')->orderBy('orden')->pluck('nombre', 'id')->prepend('', '');
        $organismos = DB::table('organismos')->where('activo','1')->pluck('codigo', 'id')->prepend('','');
        $categorias = Categoria::orderBy('id')->pluck('nombre', 'id')->prepend('', '');

        $sicadis = Sicadi::orderBy('id')->pluck('nombre', 'id')->prepend('', '');

        return view('miembros.show',compact('facultades','cargos','carrerainvs','sicadis','organismos','categorias','sicadis','unidad','miembro'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Proyecto  $proyecto
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $miembro = Miembro::find($id);

        $unidadId = $miembro->unidad_id;
        $unidad = null;

        // Si se proporciona un ID de unidad, buscalo en la base de datos
        if ($unidadId) {
            $unidad = UnidadInvestigacion::findOrFail($unidadId);
        }


        $facultades = DB::table('facultads')->pluck('nombre', 'id')->prepend('','');// Obtener todas las facultades directamente desde la tabla
        // Obtener los cargos ordenados por el campo 'orden' y seleccionar solo los campos 'id' y 'nombre'
        $cargos = Cargo::orderBy('orden')->pluck('nombre', 'id')->prepend('', '');
        $carrerainvs = Carrerainv::where('activo','1')->orderBy('orden')->pluck('nombre', 'id')->prepend('', '');
        $organismos = DB::table('organismos')->where('activo','1')->pluck('codigo', 'id')->prepend('','');
        $categorias = Categoria::orderBy('id')->pluck('nombre', 'id')->prepend('', '');

        $sicadis = Sicadi::orderBy('id')->pluck('nombre', 'id')->prepend('', '');

        return view('miembros.edit',compact('facultades','cargos','carrerainvs','sicadis','organismos','categorias','sicadis','unidad','miembro'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Unidad  $unidad
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Definir las reglas de validación
        $rules = [
            'tipo' => 'required',
            'apellido' => 'required',
            'cuil' => 'required|regex:/^\d{2}-\d{8}-\d{1}$/',

        ];

        // Definir los mensajes de error personalizados
        $messages = [
            'cuil.regex' => 'El formato del CUIL es inválido.',

        ];

        // Crear el validador con las reglas y mensajes
        $validator = Validator::make($request->all(), $rules, $messages);

        // Añadir la validación personalizada para la fecha de cierre
        /*$validator->after(function ($validator) {
            $today = now();
            $cierreDate = \Carbon\Carbon::parse(Constants::CIERRE);
            if ($today->gt($cierreDate)) {
                $validator->errors()->add('convocatoria', 'La convocatoria no está vigente.');
            }
        });*/

        // Validar y verificar si hay errores
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }


        $input = $this->sanitizeInput($request->all());

        $unidad_id=$input['unidad_id'];






        $miembro = Miembro::find($id);



        DB::beginTransaction();
        $ok = 1;






        if ($ok){
            try {
                $input['categoria_id']= $miembro->categoria_id;
                $input['sicadi_id']= $miembro->sicadi_id;

                //
                $miembro->update($input);


                $this->guardarMiembro($request,$miembro);


                $this->cambiarEstado($miembro,'Modificación de alta');



                DB::commit();
                $respuestaID = 'success';
                $respuestaMSJ = 'Alta modificada con éxito';

            } catch (QueryException $ex) {
                // Manejar la excepción de la base de datos
                DB::rollback();
                if ($ex->errorInfo[1] == 1062) {
                    $respuestaID = 'error';
                    $respuestaMSJ = 'El/la miembro ya forma parte del proyecto.';
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

        return redirect()->route('miembros.index', array('unidad_id' => $unidad_id))->with($respuestaID, $respuestaMSJ);
    }

    public function destroy($id)
    {
        $miembro = Miembro::findOrFail($id);


        // Elimina las relaciones


        $miembro->estados()->delete();


        // Elimina el miembro
        $miembro->delete();

        return redirect()->route('miembros.index')
            ->with('success','Miembro eliminado con éxito');
    }

}
