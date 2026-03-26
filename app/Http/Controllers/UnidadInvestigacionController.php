<?php

namespace App\Http\Controllers;

use App\Constants;

use App\Models\UnidadInvestigacion;
use App\Models\UnidadInvestigacionEstado;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Traits\SanitizesInput;
class UnidadInvestigacionController extends Controller
{
    use SanitizesInput;
    function __construct()
    {
        $this->middleware('permission:unidad-listar|unidad-crear|unidad-editar|unidad-eliminar', ['only' => ['index','store','dataTable']]);
        $this->middleware('permission:unidad-crear', ['only' => ['create','store']]);
        $this->middleware('permission:unidad-editar', ['only' => ['edit','update']]);
        $this->middleware('permission:unidad-eliminar', ['only' => ['destroy']]);
        //dd(session()->all());
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //dd(auth()->user());
        //dd(session('selected_rol'));
        $facultades = DB::table('facultads')->pluck('nombre', 'id')->prepend('Todas','-1');

        return view('unidad_investigacions.index',compact('facultades'));
    }



    public function dataTable(Request $request)
    {
        $columnas = ['unidad_investigacions.tipo','unidad_investigacions.denominacion','unidad_investigacions.sigla',  'unidad_investigacions.especialidad', 'facultads.nombre', 'unidad_investigacions.fecha_disposicion', 'unidad_investigacions.disposicion','unidad_investigacions.estado']; // Define las columnas disponibles
        $columnaOrden = $columnas[$request->input('order.0.column')];
        $orden = $request->input('order.0.dir');
        $busqueda = $request->input('search.value');
        $tipo = $request->input('tipo'); // Obtener el filtro de período de la solicitud
        $estado = $request->input('estado');

        $facultad = $request->input('facultad');


        // Consulta base
        $query = UnidadInvestigacion::select('unidad_investigacions.id as id', 'unidad_investigacions.tipo','unidad_investigacions.denominacion','unidad_investigacions.sigla',  'unidad_investigacions.especialidad', DB::raw("GROUP_CONCAT(DISTINCT facultads.nombre ORDER BY facultads.nombre SEPARATOR ' / ') as facultads"), 'unidad_investigacions.fecha_disposicion', 'unidad_investigacions.disposicion','unidad_investigacions.estado')
            ->leftJoin('unidad_facultads', 'unidad_investigacions.id', '=', 'unidad_facultads.unidad_id')
            ->leftJoin('facultads', 'unidad_facultads.facultad_id', '=', 'facultads.id')
            ->groupBy(
                'unidad_investigacions.id',
                'unidad_investigacions.tipo',
                'unidad_investigacions.denominacion',
                'unidad_investigacions.sigla',
                'unidad_investigacions.especialidad',
                'unidad_investigacions.fecha_disposicion',
                'unidad_investigacions.disposicion',
                'unidad_investigacions.estado'
            );

        if (!empty($tipo) && $tipo != '-1') {
            $query->where('unidad_investigacions.tipo', $tipo);
        }

        if (!empty($estado) && $estado != '-1') {
            $query->where('unidad_investigacions.estado', $estado);
        }



        if (!empty($facultad) && $facultad != '-1') {
            $query->where('facultads.id', $facultad);
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
        if ($selectedRoleId==2){
            $user = auth()->user();


            $query->where(function ($query) use ($user) {
                $query->where('personas.cuil', '=', $user->cuil);
            });
        }

        if ($selectedRoleId==4){
            $user = auth()->user();
            //$currentDate = date('Y-m-d');

            $query->where(function ($query) use ($user) {
                $query->where('unidad_investigacions.facultad_id', '=', $user->facultad_id);
            });
        }

        // Obtener cantidad filtrada correctamente
        $queryFiltered = clone $query;
        $recordsFiltered = $queryFiltered->count(DB::raw('DISTINCT unidad_investigacions.id'));

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
        $recordsTotal = UnidadInvestigacion::count();

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

        $facultades = DB::table('facultads')->pluck('nombre', 'id')->prepend('','');
        return view('unidad_investigacions.create',compact('facultades'));
    }

    private function safeRequest($request, $key, $default = null)
    {
        if (!isset($request->$key[0])) {
            return $default;
        }

        return $this->sanitizeInput($request->$key[0]);
    }


    private function validateRequest(Request $request)
    {
        // Definir las reglas de validación
        $rules = [
            'memorias' => 'file|mimes:pdf,doc,docx|max:4048',
            'reglamento' => 'file|mimes:pdf,doc,docx|max:4048',

        ];

        // Definir los mensajes de error personalizados
        $messages = [
            'memorias.mimes' => 'El archivo de Memorias o balances debe ser un documento de tipo: pdf, doc, docx.',
            'memorias.max' => 'El archivo de Memorias no debe ser mayor a 4 MB.',
            'reglamento.mimes' => 'El archivo de Reglamento interno debe ser un documento de tipo: pdf, doc, docx.',
            'reglamento.max' => 'El archivo de Reglamento interno no debe ser mayor a 4 MB.',

        ];

        // Crear el validador con las reglas y mensajes
        $validator = Validator::make($request->all(), $rules, $messages);

        // Añadir la validación personalizada para la fecha de cierre
        /*$validator->after(function ($validator) use ($request) {
            $today = now();
            $cierreDate = \Carbon\Carbon::parse(Constants::CIERRE_VIAJES);
            if ($today->gt($cierreDate)) {
                $validator->errors()->add('convocatoria', 'La convocatoria no está vigente.');
            }


        });*/

        return $validator;
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {


        //dd($request);
        // Llamar al método de validación
        $validator = $this->validateRequest($request);

        // Verificar si hay errores
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Llamar al método de lógica adicional para la validación
        //$errors = $this->validateAdditionalData($request);

        // Si hay errores, redirigir con errores
        /*if (!empty($errors)) {
            return redirect()->back()->withErrors($errors)->withInput();
        }*/


        $input = $this->sanitizeInput($request->all());


        $input['estado']= 'Creada';


        $input['memorias'] = '';
        if ($request->hasFile('memorias')) {
            $file = $request->file('memorias');
            $filename = 'memorias_' . Str::uuid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('public/files/unidades' . Constants::YEAR.'/', $filename);
            $input['memorias'] = Storage::url($path); // Genera URL tipo /storage/files/...
        }



        $input['reglamento'] = '';
        if ($request->hasFile('reglamento')) {
            $file = $request->file('reglamento');
            $filename = 'reglamento_' . Str::uuid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('public/files/unidades' . Constants::YEAR.'/', $filename);
            $input['reglamento'] = Storage::url($path); // Genera URL tipo /storage/files/...
        }


        DB::beginTransaction();
        $ok = 1;

        try {



            $unidad = UnidadInvestigacion::create($input);

        }catch(QueryException $ex){


            if ($ex->errorInfo[1] == 1062) {
                $error='Sólo se puede crear una solicitud por período';
            } else {
                // Si no es por una clave duplicada, maneja la excepción de manera general
                $error=$ex->getMessage();
            }

            $ok=0;

        }




        if ($ok){

            $this->guardarUnidad($request,$unidad);



            $this->cambiarEstado($unidad,'Nueva unidad');


            DB::commit();
            $respuestaID = 'success';
            $respuestaMSJ = 'Unidad creada con éxito';
        }
        else{
            DB::rollback();
            $respuestaID='error';
            $respuestaMSJ=$error;
        }


        return redirect()->route('unidad_investigacions.index')->with($respuestaID, $respuestaMSJ);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $unidad = UnidadInvestigacion::find($id);

        /*$today = now();
        $cierreDate = \Carbon\Carbon::parse(Constants::CIERRE_VIAJES);
        if ($today->gt($cierreDate)) {

            return redirect()->route('viajes.index')->withErrors(['message' => 'La convocatoria no está vigente.']);
        }*/

        $facultades = DB::table('facultads')->pluck('nombre', 'id')->prepend('','');



        return view('unidad_investigacions.edit',compact('facultades','unidad'));
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

        // Llamar al método de validación
        $validator = $this->validateRequest($request);

        // Verificar si hay errores
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Llamar al método de lógica adicional para la validación
        //$errors = $this->validateAdditionalData($request);

        // Si hay errores, redirigir con errores
        /*if (!empty($errors)) {
            return redirect()->back()->withErrors($errors)->withInput();
        }*/

        $input = $this->sanitizeInput($request->all());
        //dd($input);
        // Asegurarse de que los checkbox tienen valor 0 si no se enviaron

        $unidad = UnidadInvestigacion::find($id);
        if ($request->hasFile('memorias')) {
            // Eliminar memorias anterior si existe
            if (!empty($unidad->memorias)) {
                $rutaAnterior = str_replace('/storage/', 'public/', $unidad->memorias); // Ej: public/files/sicadi/2025/CV_123.pdf
                if (Storage::exists($rutaAnterior)) {
                    Storage::delete($rutaAnterior);
                }
            }

            $file = $request->file('memorias');
            $filename = 'memorias_' . Str::uuid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('public/files/unidades' . Constants::YEAR.'/', $filename);
            $input['memorias'] = Storage::url($path); // Genera URL tipo /storage/files/...
        }
        if ($request->has('delete_cv') && $unidad->memorias) {
            $rutaAnterior = str_replace('/storage/', 'public/', $unidad->memorias); // Ej: public/images/sicadi/foto_xyz.png
            if (Storage::exists($rutaAnterior)) {
                Storage::delete($rutaAnterior);
            }
            $input['memorias'] = null;
        }



        if ($request->hasFile('reglamento')) {
            // Eliminar reglamento anterior si existe
            if (!empty($unidad->reglamento)) {
                $rutaAnterior = str_replace('/storage/', 'public/', $unidad->reglamento); // Ej: public/files/sicadi/2025/CV_123.pdf
                if (Storage::exists($rutaAnterior)) {
                    Storage::delete($rutaAnterior);
                }
            }

            $file = $request->file('reglamento');
            $filename = 'reglamento_' . Str::uuid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('public/files/unidades' . Constants::YEAR.'/', $filename);
            $input['reglamento'] = Storage::url($path); // Genera URL tipo /storage/files/...
        }
        if ($request->has('delete_reglamento') && $unidad->reglamento) {
            $rutaAnterior = str_replace('/storage/', 'public/', $unidad->reglamento); // Ej: public/images/sicadi/foto_xyz.png
            if (Storage::exists($rutaAnterior)) {
                Storage::delete($rutaAnterior);
            }
            $input['reglamento'] = null;
        }






        DB::beginTransaction();
        $ok = 1;

        try {
            $unidad->update($input);


        }catch(QueryException $ex){


            if ($ex->errorInfo[1] == 1062) {
                $error='Sólo se puede crear una unidad por período';
            } else {
                // Si no es por una clave duplicada, maneja la excepción de manera general
                $error=$ex->getMessage();
            }

            $ok=0;




        }

        if ($ok){
            $this->guardarUnidad($request,$unidad,true);



            $this->cambiarEstado($unidad,'Modificar unidad');

            DB::commit();
            $respuestaID = 'success';
            $respuestaMSJ = 'Unidad modificada con éxito';
        }
        else{
            DB::rollback();
            $respuestaID='error';
            $respuestaMSJ=$error;
        }

        return redirect()->route('unidad_investigacions.index')->with($respuestaID, $respuestaMSJ);
    }

    public function guardarUnidad(Request $request, $unidad,$actualizar=false)
    {
        $unidad->facultads()->delete();
        if (!empty($request->facultads)) {

            foreach ($request->facultads as $item => $v) {
                // Inserta el registro en la tabla intermedia 'investigador_cargos'
                DB::table('unidad_facultads')->insert([
                    'unidad_id' => $unidad->id, // Supongo que tienes un objeto $investigador disponible

                    'facultad_id' => $request->facultads[$item],

                    'created_at' => now(), // Establece la fecha y hora de creación
                    'updated_at' => now(), // Establece la fecha y hora de actualización
                ]);
            }
        }


    }

    public function cambiarEstado($unidad, $comentarios)
    {

        // Actualizar el registro de estado existente donde 'hasta' es null
        UnidadInvestigacionEstado::where('unidad_id', $unidad->id)
            ->whereNull('hasta')
            ->update(['hasta' => Carbon::now()]);

        // Obtener el ID del usuario autenticado
        $userId = Auth::id();

        // Crear registro en integrante_estados con el estado "Alta Creada" y el user_id
        $unidad->estados()->create([
            'estado' => $unidad->estado,
            'user_id' => $userId,
            'comentarios' => $comentarios,
            'desde' => Carbon::now(), // Establecer 'desde' como la fecha actual

        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $unidad = UnidadInvestigacion::findOrFail($id);


        // Elimina las relaciones
        $unidad->facultads()->delete();

        $unidad->estados()->delete();

        // Elimina el unidad
        $unidad->delete();

        return redirect()->route('unidad_investigacions.index')
            ->with('success','Unidad eliminada con éxito');
    }

    public function show($id)
    {
        $unidad = UnidadInvestigacion::find($id);



        $facultades = DB::table('facultads')->pluck('nombre', 'id')->prepend('','');



        return view('unidad_investigacions.show',compact('facultades','unidad'));
    }
}
