<?php

namespace App\Http\Controllers;

use App\Constants;
use App\Models\Investigador;
use App\Models\Unidad;
use App\Traits\SanitizesInput;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Models\SolicitudSicadi;
use App\Models\User;
use App\Models\SicadiConvocatoria;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\SolicitudSicadiEstado;
use Illuminate\Support\Facades\Auth;
use App\Helpers\AreaHelper;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use ZipArchive;

use PDF;
use Illuminate\Support\Facades\Mail;
use App\Mail\SicadiEnviada;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;


class SolicitudSicadiController extends Controller

{

    use SanitizesInput;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
        $this->middleware('permission:solicitud_sicadi-listar|solicitud_sicadi-crear|solicitud_sicadi-editar|solicitud_sicadi-eliminar', ['only' => ['index','store','dataTable']]);
        $this->middleware('permission:solicitud_sicadi-crear', ['only' => ['create','store']]);
        $this->middleware('permission:solicitud_sicadi-editar', ['only' => ['edit','update']]);
        $this->middleware('permission:solicitud_sicadi-eliminar', ['only' => ['destroy']]);
        //dd(session()->all());
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        /*$convocatorias = DB::table('sicadi_convocatorias')->orderBy('year','DESC')->get();

        $currentConvocatoria = ($request->session()->get('convocatoria_filtro_sicadi'))?$request->session()->get('convocatoria_filtro_sicadi'):Constants::SICADI_ACTUAL;*/

        $currentAnio = request('filtroYear') ?? Constants::YEAR_SICADI;
        return view('solicitud_sicadis.index', [

            'currentAnio' =>  $currentAnio
        ]);
    }

    public function clearFilter(Request $request)
    {
        // Limpiar el valor del filtro en la sesión
        $request->session()->forget('nombre_filtro_sicadi');
        /*$request->session()->forget('periodo_filtro_sicadi');
        $request->session()->forget('estado_filtro_sicadi');
        $request->session()->forget('area_filtro_sicadi');
        $request->session()->forget('facultad_filtro_sicadi');*/
        //Log::info('Sesion limpia:', $request->session()->all());
        return response()->json(['status' => 'success']);
    }

    public function dataTable(Request $request)
    {

        $columnas = ['solicitud_sicadis.nombre', 'solicitud_sicadis.apellido', 'cuil','solicitud_sicadis.fecha','presentacion_ua','sicadi_convocatorias.nombre','solicitud_sicadis.estado', 'categoria_solicitada', 'categoria_asignada']; // Define las columnas disponibles
        $columnaOrden = $columnas[$request->input('order.0.column')];
        $orden = $request->input('order.0.dir');
        $busqueda = $request->input('search.value');
        $filtroYear = $request->input('filtroYear');
        $estado = $request->input('estado');
        $tipo = $request->input('tipo');
        $mecanismo = $request->input('mecanismo');
        $solicitada = $request->input('solicitada');
        $asignada = $request->input('asignada');

        $presentacion_ua = $request->input('presentacion_ua');
        $otorgadas = $request->input('otorgadas');


        // Consulta base
        $query = SolicitudSicadi::select('solicitud_sicadis.id as id', 'solicitud_sicadis.nombre as persona_nombre', 'cuil','solicitud_sicadis.fecha','presentacion_ua', DB::raw("CONCAT(sicadi_convocatorias.nombre, ' ', sicadi_convocatorias.tipo, ' ', sicadi_convocatorias.year) as convocatoria"), DB::raw("CONCAT(solicitud_sicadis.apellido, ', ', solicitud_sicadis.nombre) as persona_apellido"),'solicitud_sicadis.estado as estado', 'categoria_solicitada', 'categoria_asignada')
            ->leftJoin('sicadi_convocatorias', 'solicitud_sicadis.convocatoria_id', '=', 'sicadi_convocatorias.id')
            ;

        if (!empty($filtroYear)) {

            $request->session()->put('year_filtro_sicadi', $filtroYear);

        }
        else{
            $filtroYear = $request->session()->get('year_filtro_sicadi');

        }
        if ($filtroYear=='-1'){
            $request->session()->forget('filtroYear_filtro_sicadi');
            $filtroYear='';
        }
        if (!empty($filtroYear)) {
            $query->where('sicadi_convocatorias.year', $filtroYear);
        }

        if (!empty($tipo)) {

            $request->session()->put('tipo_filtro_sicadi', $tipo);

        }
        else{
            $tipo = $request->session()->get('tipo_filtro_sicadi');

        }
        if ($tipo=='-1'){
            $request->session()->forget('tipo_filtro_sicadi');
            $tipo='';
        }
        if (!empty($tipo)) {
            $query->where('sicadi_convocatorias.tipo', $tipo);
        }

        if (!empty($mecanismo)) {

            $request->session()->put('mecanismo_filtro_sicadi', $mecanismo);

        }
        else{
            $mecanismo = $request->session()->get('mecanismo_filtro_sicadi');

        }
        if ($mecanismo=='-1'){
            $request->session()->forget('mecanismo_filtro_sicadi');
            $mecanismo='';
        }
        if (!empty($mecanismo)) {
            $query->where('solicitud_sicadis.mecanismo', $mecanismo);
        }

        if (!empty($solicitada)) {

            $request->session()->put('solicitada_filtro_sicadi', $solicitada);

        }
        else{
            $solicitada = $request->session()->get('solicitada_filtro_sicadi');

        }
        if ($solicitada=='-1'){
            $request->session()->forget('solicitada_filtro_sicadi');
            $solicitada='';
        }
        if (!empty($solicitada)) {
            $query->where('solicitud_sicadis.categoria_solicitada', $solicitada);
        }

        if (!empty($estado)) {

            $request->session()->put('estado_filtro_sicadi', $estado);

        }
        else{
            $estado = $request->session()->get('estado_filtro_sicadi');

        }
        if ($estado=='-1'){
            $request->session()->forget('estado_filtro_sicadi');
            $estado='';
        }
        if (!empty($estado)) {
            $query->where('solicitud_sicadis.estado', $estado);
        }

        if (!empty($presentacion_ua)) {

            $request->session()->put('facultad_filtro_sicadi', $presentacion_ua);

        }
        else{
            $presentacion_ua = $request->session()->get('facultad_filtro_sicadi');

        }
        if ($presentacion_ua=='-1'){
            $request->session()->forget('facultad_filtro_sicadi');
            $presentacion_ua='';
        }
        if (!empty($presentacion_ua)) {
            $query->where('solicitud_sicadis.presentacion_ua', $presentacion_ua);
        }

        if (!empty($asignada)) {

            $request->session()->put('asignada_filtro_sicadi', $asignada);

        }
        else{
            $asignada = $request->session()->get('asignada_filtro_sicadi');

        }
        if ($asignada=='-1'){
            $request->session()->forget('asignada_filtro_sicadi');
            $asignada='';
        }
        if (!empty($asignada)) {
            $query->where('solicitud_sicadis.categoria_asignada', $asignada);
        }

        if ($otorgadas) {
            $query->where('solicitud_sicadis.estado', 'like', '%otorgada%');
        }



        if (!empty($busqueda)) {


            $request->session()->put('nombre_filtro_sicadi', $busqueda);

        }
        else{
            $busqueda = $request->session()->get('nombre_filtro_sicadi');

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
                $query->where('solicitud_sicadis.cuil', '=', $user->cuil)
                ;
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
        $recordsTotal = SolicitudSicadi::count();

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
        $user = auth()->user();

        $cuil = $user->cuil;

        $investigador=null;
        $cargo=null;
        $dedicacion=null;
        $cargo_ua=null;
        $instituciones = config('ui');
        $ui = null;

        $selectedRoleId = session('selected_rol');
        if ($selectedRoleId==2){
        //if (!$user->can('administrador-general')) {
            $convocatorias = SicadiConvocatoria::where('year',Constants::YEAR_SICADI)->pluck('tipo', 'id')->prepend('Seleccionar...', '');
            $today = now();
            $cierreDate = \Carbon\Carbon::parse(Constants::CIERRE_SICADI);
            if ($today->gt($cierreDate)) {

                return redirect()->route('solicitud_sicadis.index')->withErrors(['message' => 'La convocatoria no está vigente.']);
            }
            $investigador = Investigador::whereHas('persona', function ($query) use ($cuil) {
                $query->where('cuil', '=', $cuil);
            })
                ->first();


            $yearActual =Constants::YEAR_SICADI;

            // Verificar si ya existe una solicitud para el período actual
            $existeSolicitud = SolicitudSicadi::where('cuil', $cuil)
                ->whereHas('convocatoria', function ($query) use ($yearActual) {
                    $query->where('year', $yearActual); // o 'año' según sea el nombre real del campo
                })
                ->exists();


            if ($existeSolicitud) {
                return redirect()->route('solicitud_sicadis.index')->withErrors(['message' => 'Ya existe una solicitud para este período.']);
            }

            $yearAnterior = $yearActual - 1;

            // Verificar si el año anterior tuvo una solicitud otorgada con convocatoria tipo "evaluacion"
            $tuvoOtorgadaAnterior = SolicitudSicadi::where('cuil', $cuil)
                ->where('estado', 'Otorgada') // o el valor que uses para "Otorgada"
                ->whereHas('convocatoria', function ($query) use ($yearAnterior) {
                    $query->where('year', $yearAnterior)
                        ->where('tipo', 'Evaluación'); // asegurate de que 'tipo' sea el nombre del campo
                })
                ->exists();

            if ($tuvoOtorgadaAnterior) {
                return redirect()->route('solicitud_sicadis.index')->withErrors(['message' => 'Ud. se presentó a la Convocatoria de Evaluación de la año anterior']);
            }


            if ($investigador){
                //dd($investigador);
                // Buscar en cargos_alfabetico donde el documento esté dentro del CUIL
                $registros = DB::table('cargos_alfabetico as C')
                    ->join(DB::raw("
                                                (
                                                    SELECT C1.dni, C1.cd_deddoc, MIN(C1.cd_cargo) as maxcargo
                                                    FROM cargos_alfabetico as C1
                                                    JOIN (
                                                        SELECT dni, MIN(cd_deddoc) as maxded
                                                        FROM cargos_alfabetico
                                                        WHERE cd_deddoc IN (1,2,3)
                                                            AND escalafon = 'Docente'
                                                            AND situacion NOT IN ('Licencia sin goce de sueldos', 'Renuncia', 'Jubilación')
                                                        GROUP BY dni
                                                    ) as dmax
                                                    ON C1.dni = dmax.dni AND C1.cd_deddoc = dmax.maxded
                                                    WHERE C1.cd_cargo IN (1,2,3,4,5,7,8,9,10,11,12,13)
                                                        AND C1.escalafon = 'Docente'
                                                        AND C1.situacion NOT IN ('Licencia sin goce de sueldos', 'Renuncia', 'Jubilación')
                                                    GROUP BY C1.dni, C1.cd_deddoc
                                                ) as cmax
                                            "), function ($join) {
                        $join->on('C.dni', '=', 'cmax.dni')
                            ->on('C.cd_cargo', '=', 'cmax.maxcargo')
                            ->on('C.cd_deddoc', '=', 'cmax.cd_deddoc');
                    })
                    ->join('cargos as cargo', 'cargo.id', '=', 'C.cd_cargo')
                    ->where('C.escalafon', 'Docente')
                    ->whereNotIn('C.situacion', ['Licencia sin goce de sueldos', 'Renuncia', 'Jubilación'])
                    ->whereRaw('? LIKE CONCAT(\'%\', C.dni, \'%\')', [$cuil])
                    ->select(
                        'C.dni',
                        'cargo.nombre as cargo','cd_facultad',
                        DB::raw("
                                                    CASE C.cd_deddoc
                                                        WHEN 1 THEN 'Exclusiva'
                                                        WHEN 2 THEN 'Semi Exclusiva'
                                                        WHEN 3 THEN 'Simple'
                                                        ELSE 'Sin datos'
                                                    END as deddoc
                                                ")
                    )
                    ->get();


                if ($registros->isNotEmpty()) {
                    foreach ($registros as $registro) {


                        $cargoLimpio = str_replace('Ordinario', '', $registro->cargo);
                        $cargoLimpio = trim($cargoLimpio);
                        $cargo = $cargoLimpio;
                        $dedicacion = $registro->deddoc;
                        $cargo_ua = config('id_facultades.' . $registro->cd_facultad);


                    }

                }
                $unidad = Unidad::find($investigador->unidad_id);
                if (!empty($unidad)){
                    $key_found = $this->findKeyInsensitiveWithSpaces($instituciones, $unidad->sigla);

                    if ($key_found !== null) {

                        $ui = $key_found.' - '.$instituciones[$key_found];

                    }
                }

                //dd($unidad);

            }
        }
        else{
            //$convocatorias = SicadiConvocatoria::where('year',Constants::YEAR_SICADI)->pluck('tipo', 'id')->prepend('Seleccionar...', '');
            $convocatorias = SicadiConvocatoria::where('year', 2024)
                ->get()
                ->mapWithKeys(function ($item) {
                    return [$item->id => $item->tipo . ' ' . $item->year];
                })
                ->prepend('Seleccionar...', '');


        }

        $areas = AreaHelper::areas();
        $subareas = AreaHelper::subareas();

        $selectedArea = null;
        $selectedSubarea = null;
        return view('solicitud_sicadis.create', compact('investigador','cargo','dedicacion','cargo_ua','ui','areas', 'subareas','instituciones','selectedArea',
        'selectedSubarea','convocatorias'));
    }

    private function validateRequest($data)
    {
        // Si es una instancia de Request, convertir a array
        if ($data instanceof \Illuminate\Http\Request) {
            $data = $data->all();
        }
        // Definir las reglas de validación
        $rules = [
            'nombre' => 'required',
            'apellido' => 'required',
            'convocatoria_id' => 'required',
            'email_institucional' => [
                'required',
                'email',
                'regex:/^[^@]+@[^@]+\.[^@]+$/i'
            ],
            'email_alternativo' => [
                'nullable',
                'email',
                'regex:/^[^@]+@[^@]+\.[^@]+$/i'
            ],
            'cuil' => 'required|regex:/^\d{2}-\d{8}-\d{1}$/', // Validación de cuil
            'foto' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'curriculum' => 'file|mimes:pdf,doc,docx|max:4096',
            'nacimiento' => 'nullable|date|before:today',
            'carrera_ingreso' => 'nullable|date|before:today',
            'beca_inicio' => 'nullable|date',
            'beca_fin' => 'nullable|date|after:beca_inicio',
            'proyecto_fin' => 'nullable|date|after:proyecto_inicio',
        ];

        // Definir los mensajes de error personalizados
        $messages = [
            'convocatoria_id.required' => 'Debe seleccionar una Convocatoria en la pestaña Categorización ',
            'curriculum.mimes' => 'El archivo de curriculum debe ser un documento de tipo: pdf, doc, docx.',
            'curriculum.max' => 'El archivo de curriculum no debe ser mayor a 4 MB.',
            'nacimiento.date' => 'La fecha de nacimiento debe ser una fecha válida.',
            'nacimiento.before' => 'La fecha de nacimiento debe ser una fecha anterior a hoy.',
            'carrera_ingreso.before' => 'La fecha de ingreso a la carrera en investigación debe ser una fecha anterior a hoy.',
        ];

        // Crear el validador con las reglas y mensajes
        $validator = Validator::make($data, $rules, $messages);

        // Añadir la validación personalizada para la fecha de cierre
        $validator->after(function ($validator) use ($data) {
            $today = now();
            $cierreDate = \Carbon\Carbon::parse(Constants::CIERRE_SICADI);
            if ($today->gt($cierreDate)) {
                $validator->errors()->add('convocatoria', 'La convocatoria no está vigente.');
            }
        });

        return $validator;
    }

    private function validateImport($data)
    {
        // Si es una instancia de Request, convertir a array
        if ($data instanceof \Illuminate\Http\Request) {
            $data = $data->all();
        }
        // Definir las reglas de validación
        $rules = [
            'nombre' => 'required',
            'apellido' => 'required',
            'convocatoria_id' => 'required',
            'email_institucional' => 'nullable|email',
            'email_alternativo' => 'nullable|email',
            'cuil' => 'required|regex:/^\d{2}-\d{8}-\d{1}$/', // Validación de cuil
            'nacimiento' => 'nullable|date',

        ];

        // Definir los mensajes de error personalizados
        $messages = [
            'convocatoria_id.required' => 'Debe seleccionar una Convocatoria en la pestaña Categorización ',
            'curriculum.mimes' => 'El archivo de curriculum debe ser un documento de tipo: pdf, doc, docx.',
            'curriculum.max' => 'El archivo de curriculum no debe ser mayor a 4 MB.',
            'nacimiento.date' => 'La fecha de nacimiento debe ser una fecha válida.',

        ];

        // Crear el validador con las reglas y mensajes
        $validator = Validator::make($data, $rules, $messages);

        // Añadir la validación personalizada para la fecha de cierre
        $validator->after(function ($validator) use ($data) {
            $today = now();
            $cierreDate = \Carbon\Carbon::parse(Constants::CIERRE_SICADI);
            if ($today->gt($cierreDate)) {
                $validator->errors()->add('convocatoria', 'La convocatoria no está vigente.');
            }
        });

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


        $input = $this->sanitizeInput($request->all());

        $input['fecha']=Carbon::now();
        $input['notificacion'] = isset($request->notificacion) ? 1 : 0;

        $selectedRoleId = session('selected_rol');
        if ($selectedRoleId==2) {

            $user = auth()->user();

            //$cuil = $user->cuil;
            // Crear la carpeta si no existe
            /*$destinationPath = public_path('/files/sicadi/' . Constants::YEAR_SICADI . '/' . $cuil);
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }*/

            //$input['alta']= Constants::YEAR.'-01-01';
            $input['estado'] = 'Creada';
            // Manejo de archivos
            $input['curriculum'] = '';
            if ($request->hasFile('curriculum')) {
                $file = $request->file('curriculum');
                $filename = 'CV_' . Str::uuid() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('public/files/sicadi/' . Constants::YEAR_SICADI, $filename);
                $input['curriculum'] = Storage::url($path); // Genera URL tipo /storage/files/...
            }
        }
        else{
            $input['estado'] = 'Otorgada';
        }


        // Manejo de la imagen
        $input['foto'] ='';
        if ($request->hasFile('foto')) {
            $image = $request->file('foto');

            // Validar que no sea SVG (por seguridad)
            if ($image->getClientOriginalExtension() === 'svg') {
                return redirect()->back()
                    ->withErrors(['foto' => 'No se permiten archivos SVG por razones de seguridad.'])
                    ->withInput();
            }

            $filename = 'foto_' . Str::uuid() . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('public/images/sicadi', $filename);
            $input['foto'] = Storage::url($path);
        }

        DB::beginTransaction();
        $ok = 1;

        try {

            if (!empty($input['investigacion_ui'])) {
                [$clave, $valor] = explode(' - ', $input['investigacion_ui'], 2);
                $input['ui_sigla'] = $clave;
                $input['ui_nombre'] = $valor;
            }
            if (!empty($input['carrera_ui'])) {
                [$clave, $valor] = explode(' - ', $input['carrera_ui'], 2);
                $input['carrera_ui_sigla'] = $clave;
                $input['carrera_ui_nombre'] = $valor;
            }
            if (!empty($input['beca_ui'])) {
                [$clave, $valor] = explode(' - ', $input['beca_ui'], 2);
                $input['beca_ui_sigla'] = $clave;
                $input['beca_ui_nombre'] = $valor;
            }
            // Crear la persona y luego el investigador
            $solicitud = SolicitudSicadi::create($input);

        }catch(QueryException $ex){


                    // Si no es por una clave duplicada, maneja la excepción de manera general
                    $error=$ex->getMessage();


                $ok=0;

            }




        if ($ok){
            $this->cambiarEstado($solicitud,'Nueva solicitud');

            DB::commit();
            $respuestaID = 'success';
            $respuestaMSJ = 'Solicitud creada con éxito';
        }
        else{
            DB::rollback();
            $respuestaID='error';
            $respuestaMSJ=$error;
        }

        return redirect()->route('solicitud_sicadis.index')->with($respuestaID, $respuestaMSJ);
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
        $investigador = SolicitudSicadi::find($id);
        $instituciones = config('ui');
        $areas = AreaHelper::areas();
        $subareas = AreaHelper::subareas();
        $selectedArea = $investigador->area;
        $selectedSubarea = $investigador->subarea;
        $selectedRoleId = session('selected_rol');
        if ($selectedRoleId==2) {
            $convocatorias = SicadiConvocatoria::where('year', Constants::YEAR_SICADI)->pluck('tipo', 'id')->prepend('Seleccionar...', '');
        }
        else{

            $convocatorias = SicadiConvocatoria::all()
                ->mapWithKeys(function ($item) {
                    return [$item->id => $item->tipo . ' ' . $item->year];
                })
                ->prepend('Seleccionar...', '');

        }
        return view('solicitud_sicadis.edit',compact('investigador','instituciones','areas','subareas','selectedArea',
            'selectedSubarea','convocatorias'));


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
        $validator = $this->validateRequest($request);

        // Verificar si hay errores
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $input = $this->sanitizeInput($request->all());

        $solicitud = SolicitudSicadi::find($id);

        // Manejo de la imagen
        //$input['foto'] ='';
        if ($request->hasFile('foto')) {
            $image = $request->file('foto');

            // Validar que no sea SVG (por seguridad)
            if ($image->getClientOriginalExtension() === 'svg') {
                return redirect()->back()
                    ->withErrors(['foto' => 'No se permiten archivos SVG por razones de seguridad.'])
                    ->withInput();
            }

            // Eliminar imagen anterior si existe
            if (!empty($solicitud->foto)) {
                $rutaAnterior = str_replace('/storage/', 'public/', $solicitud->foto); // Ej: public/images/sicadi/foto_xyz.png
                if (Storage::exists($rutaAnterior)) {
                    Storage::delete($rutaAnterior);
                }
            }


            $filename = 'foto_' . Str::uuid() . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('public/images/sicadi', $filename);
            $input['foto'] = Storage::url($path);
        }
        if ($request->has('delete_image') && $solicitud->foto) {
            $rutaAnterior = str_replace('/storage/', 'public/', $solicitud->foto); // Ej: public/images/sicadi/foto_xyz.png
            if (Storage::exists($rutaAnterior)) {
                Storage::delete($rutaAnterior);
            }
            $input['foto'] = null;
        }
        $selectedRoleId = session('selected_rol');
        if ($selectedRoleId==2) {
            $input['fecha'] = Carbon::now();
            $input['notificacion'] = isset($request->notificacion) ? 1 : 0;
            /*$user = auth()->user();

            $cuil = $user->cuil;*/
            // Crear la carpeta si no existe
            /*$destinationPath = public_path('/files/sicadi/' . Constants::YEAR_SICADI . '/' . $cuil);
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }*/

            //$input['alta']= Constants::YEAR.'-01-01';
            //$input['estado']= 'Creada';
            // Manejo de archivos
            //$input['curriculum'] ='';
            if ($request->hasFile('curriculum')) {
                // Eliminar curriculum anterior si existe
                if (!empty($solicitud->curriculum)) {
                    $rutaAnterior = str_replace('/storage/', 'public/', $solicitud->curriculum); // Ej: public/files/sicadi/2025/CV_123.pdf
                    if (Storage::exists($rutaAnterior)) {
                        Storage::delete($rutaAnterior);
                    }
                }

                $file = $request->file('curriculum');
                $filename = 'CV_' . Str::uuid() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('public/files/sicadi/' . Constants::YEAR_SICADI, $filename);
                $input['curriculum'] = Storage::url($path); // Genera URL tipo /storage/files/...
            }
            if ($request->has('delete_cv') && $solicitud->curriculum) {
                $rutaAnterior = str_replace('/storage/', 'public/', $solicitud->curriculum); // Ej: public/images/sicadi/foto_xyz.png
                if (Storage::exists($rutaAnterior)) {
                    Storage::delete($rutaAnterior);
                }
                $input['curriculum'] = null;
            }
        }


        DB::beginTransaction();
        $ok = 1;

        try {
            if (!empty($input['investigacion_ui'])) {
                [$clave, $valor] = explode(' - ', $input['investigacion_ui'], 2);
                $input['ui_sigla'] = $clave;
                $input['ui_nombre'] = $valor;
            }
            if (!empty($input['carrera_ui'])) {
                [$clave, $valor] = explode(' - ', $input['carrera_ui'], 2);
                $input['carrera_ui_sigla'] = $clave;
                $input['carrera_ui_nombre'] = $valor;
            }
            if (!empty($input['beca_ui'])) {
                [$clave, $valor] = explode(' - ', $input['beca_ui'], 2);
                $input['beca_ui_sigla'] = $clave;
                $input['beca_ui_nombre'] = $valor;
            }
            $solicitud->update($input);


        }catch(QueryException $ex){



                if ($ex->errorInfo[1] == 1062) {
                    $error='Sólo se puede crear una solicitud por convocatoria';
                } else {
                    // Si no es por una clave duplicada, maneja la excepción de manera general
                    $error=$ex->getMessage();
                }


                $ok=0;




        }

        if ($ok){

            $this->cambiarEstado($solicitud,'Modificar solicitud');
            DB::commit();
            $respuestaID = 'success';
            $respuestaMSJ = 'Solicitud modificada con éxito';
        }
        else{
            DB::rollback();
            $respuestaID='error';
            $respuestaMSJ=$error;
        }

        return redirect()->route('solicitud_sicadis.index')->with($respuestaID, $respuestaMSJ);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $solicitud = SolicitudSicadi::findOrFail($id);

        DB::beginTransaction();
        try {
            // Ruta de la imagen
            $imagePath = public_path('/images/sicadi/' . $solicitud->foto);

            // Verificar si la ruta es un archivo y si existe
            if (!empty($solicitud->foto) && file_exists($imagePath) && is_file($imagePath)) {
                unlink($imagePath);
            }

            // Eliminar el solicitud
            $solicitud->delete();

            DB::commit();
            return redirect()->route('solicitud_sicadis.index')
                ->with('success', 'Solicitud eliminada con éxito');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('solicitud_sicadis.index')
                ->with('error', 'Error al eliminar la solicitud: ' . $e->getMessage());
        }
    }

    public function getUnidadesAcademicas()
    {
        $facultades = config('facultades');
        return response()->json($facultades);
    }

    public function getCategorias()
    {
        $categorias = config('categorias');
        return response()->json($categorias);
    }

    public function getSubareas()
    {
        $subareas = AreaHelper::subareas(); // Obtener subáreas desde el helper
        return response()->json($subareas);
    }


    public function getInvestigadorById($id)
    {
        if (!is_numeric($id)) {
            return response()->json(['success' => false, 'message' => 'ID inválido'], 400);
        }
        try {
            $investigador = SolicitudSicadi::where('estado', 'Otorgada')->findOrFail($id);

            // Construir la URL completa de la imagen
            if ($investigador->foto) {
                $investigador->foto = asset($investigador->foto);
            }
            else{
                $investigador->foto = url('/images/sicadi/nofoto.png');
            }

            return response()->json(['success' => true, 'data' => $investigador], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        }
    }

    public function filterInvestigadores(Request $request)
    {
        // Validación de parámetros opcionales
        $validator = Validator::make($request->all(), [
            'nombre' => 'nullable|string|max:255',
            'apellido' => 'nullable|string|max:255',
            'cuil' => 'nullable|string|max:20',
            'cargo_ua' => 'nullable|string|max:255',
            'categoria_asignada' => 'nullable|string|max:100',
            'ui_sigla' => 'nullable|string|max:100',
            'subarea' => 'nullable|string|max:255',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Parámetros inválidos.',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $query = SolicitudSicadi::where('estado', 'Otorgada');

            // Filtros dinámicos
            if ($request->filled('nombre')) {
                $query->where(function ($q) use ($request) {
                    $q->where('nombre', 'like', '%' . $request->nombre . '%')
                        ->orWhere('apellido', 'like', '%' . $request->nombre . '%');
                });
            }

            if ($request->filled('apellido')) {
                $query->where(function ($q) use ($request) {
                    $q->where('nombre', 'like', '%' . $request->apellido . '%')
                        ->orWhere('apellido', 'like', '%' . $request->apellido . '%');
                });
            }

            if ($request->filled('cuil')) {
                $query->where('cuil', 'like', '%' . $request->cuil . '%');
            }

            if ($request->filled('cargo_ua')) {
                $query->where('cargo_ua', 'like', '%' . $request->cargo_ua . '%');
            }

            if ($request->filled('categoria_asignada')) {
                $query->where('categoria_asignada', $request->categoria_asignada);
            }

            if ($request->filled('ui_sigla')) {
                $query->where(function ($q) use ($request) {
                    $q->where('ui_sigla', 'like', '%' . $request->ui_sigla . '%')
                        ->orWhere('ui_nombre', 'like', '%' . $request->ui_sigla . '%');
                });
            }

            if ($request->filled('subarea')) {
                $query->where('subarea', 'like', '%' . $request->subarea . '%');
            }

            // Paginación
            $perPage = (int) $request->input('per_page', 20);
            if ($perPage > 100 || $perPage < 1) {
                $perPage = 100;
            }
            $investigadores = $query->paginate($perPage);

            // Reemplazo de foto por la URL completa
            $investigadores->getCollection()->transform(function ($investigador) {
                $investigador->foto = $investigador->foto
                    ? asset($investigador->foto)
                    : url('/images/sicadi/nofoto.png');
                return $investigador;
            });

            return response()->json([
                'success' => true,
                'data' => $investigadores->items(),
                'pagination' => [
                    'total' => $investigadores->total(),
                    'per_page' => $investigadores->perPage(),
                    'current_page' => $investigadores->currentPage(),
                    'last_page' => $investigadores->lastPage(),
                ],
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Error al filtrar investigadores: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error inesperado al obtener los investigadores.'
            ], 500);
        }
    }

    public function importar(Request $request)
    {

        return view('solicitud_sicadis.importar');
    }

    function findKeyInsensitiveWithSpaces($UI, $valor) {
        // Eliminamos los espacios del valor de búsqueda
        $valor = str_replace(' ', '', $valor);

        // Convertimos el valor de búsqueda a minúsculas
        $valor_lower = strtolower($valor);

        foreach ($UI as $key => $value) {
            // Eliminamos los espacios de la clave y comparamos insensiblemente a mayúsculas/minúsculas
            if (strtolower(str_replace(' ', '', $key)) === $valor_lower) {
                return $key;  // Devolvemos la clave encontrada
            }
        }

        return null;  // Si no se encuentra la clave, devolvemos null
    }


    public function importprocess(Request $request)
    {

        set_time_limit(0);
        ini_set('memory_limit', '1024M');


        $file = $request->file('archivoCSV');

        // File Details
        $filename = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $tempPath = $file->getRealPath();
        $fileSize = $file->getSize();
        $mimeType = $file->getMimeType();


        // Valid File Extensions
        $valid_extension = array("csv");

        // 2MB in Bytes
        $maxFileSize = 2097152;

        // Check file extension
        if (in_array(strtolower($extension), $valid_extension)) {

            // Check file size
            if ($fileSize <= $maxFileSize) {

                // File upload location
                $location = 'uploads';

                // Upload file
                $file->move($location, $filename);

                // Import CSV to Database
                $filepath = public_path($location . "/" . $filename);

                // Reading file
                $file = fopen($filepath, "r");

                $importData_arr = array();
                $i = 0;

                while (($filedata = fgetcsv($file, 10000, ";")) !== FALSE) {
                    $num = count($filedata);

                    // Skip first row (Remove below comment if you want to skip the first row)
                    /*if($i == 0){
                       $i++;
                       continue;
                    }*/
                    for ($c = 0; $c < $num; $c++) {
                        $importData_arr[$i][] = $filedata [$c];
                    }
                    $i++;
                }
                //dd($importData_arr);
                fclose($file);
                DB::beginTransaction();
                $ok=1;
                $success='';
                $error='';
                $campos = [
                    'apellido',               // 0
                    'nombre',                 // 1
                    'genero',                 // 2
                    'cuil',                   // 3
                    'nacimiento',             // 4
                    'nacionalidad',           // 5
                    'calle',                  // 6
                    'nro',                    // 7
                    'piso',                   // 8
                    'dpto',                   // 9
                    'cp',                     // 10
                    'celular',                // 11
                    'email_institucional',    // 12
                    'email_alternativo',      // 13
                    'sedici',                 // 14
                    'orcid',                  // 15
                    'scholar',                // 16
                    'scopus',                 // 17
                    'titulo',                 // 18
                    'titulo_entidad',         // 19
                    'tipo_posgrado',          // 20
                    'posgrado',               // 21
                    'posgrado_entidad',       // 22
                    'cargo_docente',          // 23
                    'cargo_dedicacion',       // 24
                    'cargo_ua',               // 25
                    'ui_sigla',               // 26
                    'ui_nombre',              // 27
                    'beca_tipo',              // 28
                    'beca_entidad',           // 29
                    'beca_inicio',            // 30
                    'beca_fin',               // 31
                    'beca_ui_sigla',          // 32
                    'beca_ui_nombre',         // 33
                    'carrera_cargo',          // 34
                    'carrera_empleador',      // 35
                    'carrera_ingreso',        // 36
                    'carrera_ui_sigla',       // 37
                    'carrera_ui_nombre',      // 38
                    'proyecto_entidad',       // 39
                    'proyecto_codigo',        // 40
                    'proyecto_titulo',        // 41
                    'proyecto_director',      // 42
                    'proyecto_inicio',        // 43
                    'proyecto_fin',           // 44
                    'presentacion_ua',        // 45
                    'categoria_spu',          // 46
                    'categoria_solicitada',   // 47
                    'mecanismo',              // 48
                    'area',                   // 49
                    'subarea',                // 50
                    'foto',                   // 51
                    'categoria_asignada',     // 52
                ];


                $UI = config('ui');



                foreach ($importData_arr as $i => $fila) {
                    $input = [];
                    $sigla_desconocida = '';
                    $sin_cargo = '';
                    foreach ($campos as $index => $nombreCampo) {
                        $valor = trim(utf8_encode($fila[$index]));
                        $valor = ($valor === '') ? null : $valor;


                        // Si el campo es una fecha, convertir el formato
                        if (in_array($nombreCampo, ['nacimiento', 'beca_inicio', 'beca_fin', 'proyecto_inicio', 'proyecto_fin','carrera_ingreso'])) {
                            if ($valor && $valor !== '00/00/0000') {  // Verificamos si la fecha no es vacía ni inválida
                                try {
                                    $input[$nombreCampo] = Carbon::createFromFormat('d/m/Y', $valor)->format('Y-m-d');
                                } catch (\Exception $e) {
                                    // Si no se puede convertir, asignamos null (o podrías asignar otra cosa como '0000-00-00')
                                    $input[$nombreCampo] = null;
                                }
                            } else {
                                $input[$nombreCampo] = null;  // Asignamos null si la fecha no es válida
                            }
                        } else {

                            // Si el campo es una sigla específica, verificar y asignar el nombre correspondiente
                            if ($nombreCampo === 'ui_sigla') {
                                if (!empty($valor)) {
                                    $key_found = $this->findKeyInsensitiveWithSpaces($UI, $valor);

                                    if ($key_found !== null) {
                                        $input['ui_nombre'] = $UI[$key_found];
                                        $input[$nombreCampo] = $valor;
                                    }

                                     else {
                                        $sigla_desconocida = ' Sigla UI desconocida ' . $valor;
                                        $input[$nombreCampo] = null;
                                         $input['ui_nombre'] = null;
                                    }
                                }
                                else{
                                    $sigla_desconocida = ' No tiene sigla';
                                }
                            } elseif ($nombreCampo === 'beca_ui_sigla') {
                                if (!empty($valor)) {
                                    $key_found = $this->findKeyInsensitiveWithSpaces($UI, $valor);

                                    if ($key_found !== null) {
                                        $input['ui_nombre'] = $UI[$key_found];
                                        $input[$nombreCampo] = $valor;
                                    } else {
                                        $sigla_desconocida = ' Sigla Beca UI desconocida ' . $valor;
                                        $input[$nombreCampo] = null;
                                        $input['ui_nombre'] = null;
                                    }
                                }
                            } elseif ($nombreCampo === 'carrera_ui_sigla') {
                                if (!empty($valor)) {
                                    $key_found = $this->findKeyInsensitiveWithSpaces($UI, $valor);

                                    if ($key_found !== null) {
                                        $input['ui_nombre'] = $UI[$key_found];
                                        $input[$nombreCampo] = $valor;
                                    } else {
                                        $sigla_desconocida = ' Sigla Carrera UI desconocida ' . $valor;
                                        $input[$nombreCampo] = null;
                                        $input['ui_nombre'] = null;
                                    }
                                }
                            }
                            else {
                                if ($nombreCampo === 'cuil') {
                                    if ($valor) {
                                        $input[$nombreCampo] = $valor;
                                        // Buscar en cargos_alfabetico donde el documento esté dentro del CUIL
                                        $registros = DB::table('cargos_alfabetico as C')
                                            ->join(DB::raw("
                                                (
                                                    SELECT C1.dni, C1.cd_deddoc, MIN(C1.cd_cargo) as maxcargo
                                                    FROM cargos_alfabetico as C1
                                                    JOIN (
                                                        SELECT dni, MIN(cd_deddoc) as maxded
                                                        FROM cargos_alfabetico
                                                        WHERE cd_deddoc IN (1,2,3)
                                                            AND escalafon = 'Docente'
                                                            AND situacion NOT IN ('Licencia sin goce de sueldos', 'Renuncia', 'Jubilación')
                                                        GROUP BY dni
                                                    ) as dmax
                                                    ON C1.dni = dmax.dni AND C1.cd_deddoc = dmax.maxded
                                                    WHERE C1.cd_cargo IN (1,2,3,4,5,7,8,9,10,11,12,13)
                                                        AND C1.escalafon = 'Docente'
                                                        AND C1.situacion NOT IN ('Licencia sin goce de sueldos', 'Renuncia', 'Jubilación')
                                                    GROUP BY C1.dni, C1.cd_deddoc
                                                ) as cmax
                                            "), function ($join) {
                                                        $join->on('C.dni', '=', 'cmax.dni')
                                                            ->on('C.cd_cargo', '=', 'cmax.maxcargo')
                                                            ->on('C.cd_deddoc', '=', 'cmax.cd_deddoc');
                                                    })
                                                    ->join('cargos as cargo', 'cargo.id', '=', 'C.cd_cargo')
                                                    ->where('C.escalafon', 'Docente')
                                                    ->whereNotIn('C.situacion', ['Licencia sin goce de sueldos', 'Renuncia', 'Jubilación'])
                                                    ->whereRaw('? LIKE CONCAT(\'%\', C.dni, \'%\')', [$valor])
                                                    ->select(
                                                        'C.dni',
                                                        'cargo.nombre as cargo',
                                                        DB::raw("
                                                    CASE C.cd_deddoc
                                                        WHEN 1 THEN 'Exclusiva'
                                                        WHEN 2 THEN 'Semi Exclusiva'
                                                        WHEN 3 THEN 'Simple'
                                                        ELSE 'Sin datos'
                                                    END as deddoc
                                                ")
                                            )
                                            ->get();


                                        if ($registros->isNotEmpty()) {
                                            foreach ($registros as $registro) {


                                                $cargoLimpio = str_replace('Ordinario', '', $registro->cargo);
                                                $cargoLimpio = trim($cargoLimpio);

                                                // Log
                                                //Log::info("CUIL '$valor' contiene documento '{$registro->dni}' con cd_cargo '{$registro->cargo}' y cd_deddoc '$registro->deddoc'");


                                                $input['cargo_docente'] = $cargoLimpio;
                                                $input['cargo_dedicacion'] = $registro->deddoc;


                                            }

                                        } else {

                                            $sin_cargo = ' no tiene cargo';
                                        }
                                    }
                                }
                                else{
                                    if (($nombreCampo !== 'cargo_docente')&&($nombreCampo !== 'cargo_dedicacion')&&($nombreCampo !== 'ui_nombre')&&($nombreCampo !== 'beca_ui_nombre')&&($nombreCampo !== 'carrera_ui_nombre')) {
                                        $input[$nombreCampo] = $valor;
                                    }
                                }


                            }
                        }
                    }
                    if (isset($input['cuil']) && !empty($input['cuil'])) {
                        $input['estado']= 'Otorgada';
                        $input['convocatoria_id'] = (isset($input['mecanismo']) && !empty($input['mecanismo']))?1:2;

                        // VALIDACIÓN antes de guardar o actualizar
                        $validator = $this->validateImport($input);
                        if ($validator->fails()) {
                            Log::warning('Validación fallida: ' . json_encode($validator->errors()->all()));
                            $error .= 'Error de validación en ' . $input['apellido'] . ', ' . $input['nombre'] . ': ' . implode(', ', $validator->errors()->all()) . '<br>';
                            $ok = 0;
                            continue; // Salta al siguiente registro si estás dentro de un foreach
                        }

                        try {
                            //Log::info("Inv.: " . print_r($input, true));

                            //$input['observaciones'] = $sin_cargo.'\n'.$sigla_desconocida;
                            //dd($input);

                            $investigador = SolicitudSicadi::create($input);
                            $this->cambiarEstado($investigador,'Nueva solicitud');
                            Log::info('Creado: ' . $input['apellido'] . ', ' . $input['nombre'] . ' - ' . (isset($input['cuil']) ? $input['cuil'] : '') . $sigla_desconocida . $sin_cargo);
                            $success .= 'Creado: ' . $input['apellido'] . ', ' . $input['nombre'] . $sigla_desconocida . $sin_cargo . '<br>';
                        } catch (QueryException $ex) {
                            //Log::error("Crear: " . $ex->getMessage());
                            if ($ex->errorInfo[1] === 1062) {
                                $investigador = SolicitudSicadi::where('cuil', '=', $input['cuil'])->first();
                                if (!empty($investigador)) {
                                    // Eliminamos el campo 'foto' de los datos para no actualizarlo
                                    unset($input['foto']);

                                    //Log::info("Inv. actualizado: " . print_r($input, true));
                                    try {

                                        $investigador->update($input);
                                        $this->cambiarEstado($investigador,'Actualizar solicitud');
                                        Log::info('Actualizado: ' . $input['apellido'] . ', ' . $input['nombre'] . ' - ' . (isset($input['cuil']) ? $input['cuil'] : '') . $sigla_desconocida . $sin_cargo);
                                        $success .= 'Actualizado: ' . $input['apellido'] . ', ' . $input['nombre'] . $sigla_desconocida . $sin_cargo . '<br>';

                                    } catch (QueryException $ex) {
                                        //Log::error("Actualizar: " . print_r($ex, true));
                                        // Si no es por una clave duplicada, maneja la excepción de manera general
                                        $error .= $ex->getMessage() . '<br>';
                                        $ok = 0;
                                    }

                                }
                            } else {
                                // Si no es por una clave duplicada, maneja la excepción de manera general
                                $error .= $ex->getMessage() . '<br>';
                                $ok = 0;
                            }
                        }
                    }
                    else{
                        Log::info('Sin CUIL: ' . $input['apellido'] . ', ' . $input['nombre'] . ' - ' . (isset($input['cuil']) ? $input['cuil'] : '') . $sigla_desconocida . $sin_cargo);
                    }
                    //dd($input);
                }

            }
            else{


                $error='Archivo demasiado grande. El archivo debe ser menor que 2MB.';
                $ok=0;

            }

        }else{

            $error='Extensión de archivo no válida.';
            $ok=0;

        }

        if ($ok){



            DB::commit();
            $respuestaID='success';
            $respuestaMSJ='Importación exitosa';
        }
        else{
            DB::rollback();
            $respuestaID='error';
            $respuestaMSJ=$error;
        }

        //
        return redirect()->route('solicitud_sicadis.index')->with($respuestaID,$respuestaMSJ);
        }

    public function cambiarEstado($solicitud, $comentarios)
    {

        // Actualizar el registro de estado existente donde 'hasta' es null
        SolicitudSicadiEstado::where('solicitud_sicadi_id', $solicitud->id)
            ->whereNull('hasta')
            ->update(['hasta' => Carbon::now()]);

        // Obtener el ID del usuario autenticado
        $userId = Auth::id();

        // Crear registro en integrante_estados con el estado "Alta Creada" y el user_id
        $solicitud->estados()->create([
            'estado' => $solicitud->estado,
            'user_id' => $userId,
            'comentarios' => $comentarios,
            'desde' => Carbon::now(), // Establecer 'desde' como la fecha actual

        ]);
    }

    public function archivos(Request $request)
    {
        $solicitud_sicadiId = $request->query('solicitud_sicadi_id');

        $solicitud_sicadi = SolicitudSicadi::findOrFail($solicitud_sicadiId);

        $files = [
            'curriculum' => $solicitud_sicadi->curriculum,
            // Agrega aquí otros archivos que necesites
        ];

        $validFiles = [];

        foreach ($files as $key => $url) {
            if ($url) {
                // Remover "/storage/" del inicio
                $relativePath = str_replace('/storage/', '', $url);
                $fullPath = storage_path('app/public/' . $relativePath);

                if (file_exists($fullPath)) {
                    $validFiles[$key] = $fullPath;
                }
            }
        }

        if (empty($validFiles)) {
            return response()->json(['message' => 'No hay archivos para descargar.'], 404);
        }

        $zip = new ZipArchive;
        $zipFileName = 'archivos_sicadi_' . $solicitud_sicadiId . '.zip';
        $zipFilePath = public_path('temp/' . $zipFileName);

        // Crear carpeta temp si no existe
        if (!file_exists(public_path('temp'))) {
            mkdir(public_path('temp'), 0777, true);
        }

        if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            foreach ($validFiles as $fileKey => $fullPath) {
                $zip->addFile($fullPath, $fileKey . '_' . basename($fullPath));
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
        $solicitud = SolicitudSicadi::findOrFail($id);
        //dd($solicitud);

        $error='';
        $ok = 1;

        $errores = [];

        $today = now();
        $cierreDate = \Carbon\Carbon::parse(Constants::CIERRE_SICADI);
        if ($today->gt($cierreDate)) {
            $errores[] ='La convocatoria no está vigente';
        }

        if (empty($solicitud->apellido) ) {
            $errores[] = 'Complete el campo Apellido en la pestaña Datos Personales';
        }

        if (empty($solicitud->nombre) ) {
            $errores[] = 'Complete el campo Nombre en la pestaña Datos Personales';
        }

        if (empty($solicitud->cuil) ) {
            $errores[] = 'Complete el campo CUIL en la pestaña Datos Personales';
        }

        if (empty($solicitud->genero) ) {
            $errores[] = 'Complete el campo Género en la pestaña Datos Personales';
        }

        if (empty($solicitud->nacimiento) ) {
            $errores[] = 'Complete el campo Nacimiento en la pestaña Datos Personales';
        }

        if (empty($solicitud->nacionalidad) ) {
            $errores[] = 'Complete el campo Nacionalidad en la pestaña Datos Personales';
        }

        if (empty($solicitud->email_institucional) ) {
            $errores[] = 'Complete el campo Email institucional en la pestaña Datos Personales';
        }

        if (empty($solicitud->calle) ) {
            $errores[] = 'Complete el campo Calle en la pestaña Datos Personales';
        }

        if (empty($solicitud->nro) ) {
            $errores[] = 'Complete el campo Número en la pestaña Datos Personales';
        }

        if (empty($solicitud->cp) ) {
            $errores[] = 'Complete el campo Código Postal en la pestaña Datos Personales';
        }

        if (empty($solicitud->celular) ) {
            $errores[] = 'Complete el campo Celular en la pestaña Datos Personales';
        }

        if (empty($solicitud->sedici) ) {
            $errores[] = 'Complete el campo Perfil SEDICI en la pestaña Datos Personales';
        }

        if (empty($solicitud->orcid) ) {
            $errores[] = 'Complete el campo Número ORCID en la pestaña Datos Personales';
        }

        if (empty($solicitud->scholar) ) {
            $errores[] = 'Complete el campo Perfil de Google Académico en la pestaña Datos Personales';
        }

        if (empty($solicitud->foto)) {
            $errores[] = 'Falta subir la foto';
        } else {
            $filePath = public_path('/images/sicadi/' . $solicitud->foto);
            if (!file_exists($filePath)) {
                $errores[] = 'Hubo un error al subir la foto, intente nuevamente, si el problema persiste envíenos un mail a categorizacion1@presi.unlp.edu.ar';
            }
        }

        if (empty($solicitud->titulo) ) {
            $errores[] = 'Complete el campo Título de grado en la pestaña Datos Académicos';
        }

        if (empty($solicitud->cargo_docente) ) {
            $errores[] = 'Complete el campo Cargo docente en la pestaña Datos Académicos';
        }

        if (empty($solicitud->cargo_dedicacion) ) {
            $errores[] = 'Complete el campo Dedicación en la pestaña Datos Académicos';
        }

        if (empty($solicitud->cargo_ua) ) {
            $errores[] = 'Complete el campo U. Académica en la pestaña Datos Académicos';
        }

        if ((empty($solicitud->ui_sigla)||(empty($solicitud->ui_nombre))) ) {
            $errores[] = 'Complete el campo Lugar de trabajo en la pestaña Datos Académicos';
        }

        if (empty($solicitud->proyecto_entidad) ) {
            $errores[] = 'Complete el campo Entidad en la pestaña Proyecto';
        }

        if (empty($solicitud->proyecto_codigo) ) {
            $errores[] = 'Complete el campo Código en la pestaña Proyecto';
        }

        if (empty($solicitud->proyecto_director) ) {
            $errores[] = 'Complete el campo Director en la pestaña Proyecto';
        }

        if (empty($solicitud->proyecto_inicio) ) {
            $errores[] = 'Complete el campo Inicio en la pestaña Proyecto';
        }

        if (empty($solicitud->proyecto_fin) ) {
            $errores[] = 'Complete el campo Fin en la pestaña Proyecto';
        }

        if (empty($solicitud->proyecto_titulo) ) {
            $errores[] = 'Complete el campo Título en la pestaña Proyecto';
        }

        if (empty($solicitud->convocatoria_id) ) {
            $errores[] = 'Complete el campo Convocatoria en la pestaña Categorización';
        }

        if (($solicitud->convocatoria->tipo === 'Equivalencia') && (empty($solicitud->mecanismo) )  ) {
            $errores[] = 'Complete el campo Mecanismo en la pestaña Categorización';
        }

        if (empty($solicitud->presentacion_ua) ) {
            $errores[] = 'Complete el campo U. Académica en la pestaña Categorización';
        }

        if (empty($solicitud->categoria_spu) ) {
            $errores[] = 'Complete el campo Categoría SPU en la pestaña Categorización';
        }

        if (empty($solicitud->categoria_solicitada) ) {
            $errores[] = 'Complete el campo Categoría Solicitada en la pestaña Categorización';
        }

        if ($solicitud->convocatoria->tipo === 'Equivalencia') {
            $mecanismo = $solicitud->mecanismo;
            $categoriaSolicitada = $solicitud->categoria_solicitada;

            // Excluir el caso especial
            if ($mecanismo !== 'Cat. SPU/PRINUAR - DI1 a DI5') {
                // Extraer la categoría esperada desde el mecanismo (después del último guion)
                $categoriaEsperada = trim(substr($mecanismo, strrpos($mecanismo, '-') + 1));

                if ($categoriaSolicitada !== $categoriaEsperada) {
                    $errores[] = 'Si el mecanismo es "' . $mecanismo . '", la Categoría Solicitada debe ser "' . $categoriaEsperada . '".';
                }
            }
        }



        if (empty($solicitud->area) ) {
            $errores[] = 'Complete el campo Área en la pestaña Categorización';
        }

        if (empty($solicitud->subarea) ) {
            $errores[] = 'Complete el campo Subárea en la pestaña Categorización';
        }

        if (empty($solicitud->curriculum)) {
            $errores[] = 'Falta subir el Curriculum';
        } else {
            $filePath = public_path($solicitud->curriculum);
            if (!file_exists($filePath)) {
                $errores[] = 'Hubo un error al subir el Curriculum, intente nuevamente, si el problema persiste envíenos un mail a categorizacion1@presi.unlp.edu.ar';
            }
        }



        //$this->validarEnviar($integrante,$errores);



        if (!empty($errores)) {
            return redirect()->back()->withErrors($errores)->withInput();
        }


        DB::beginTransaction();







        if ($ok){
            try {

                $solicitud->estado = 'Recibida';
                $solicitud->save();

                $this->cambiarEstado($solicitud,'Envío de solicitud');



                $year = $solicitud->convocatoria->year;
                $convocatoria = $solicitud->convocatoria->nombre.' '.$solicitud->convocatoria->tipo.' '.$year;

                $userId = Auth::id();
                $user = User::find($userId); // Obtener el usuario por su ID
                // Preparar datos para el correo
                $datosCorreo = [
                    'from_email' => $user->email,
                    'from_name' => $user->name,
                    'asunto' => 'Solicitud de Categorización SICADI '.$year,
                    'convocatoria' => $convocatoria,
                    'categoria_solicitada' => $solicitud->categoria_solicitada,
                    'investigador' => $solicitud->apellido.', '.$solicitud->nombre.' ('.$solicitud->cuil.')',
                    'comment' => ($solicitud->convocatoria->tipo==='Equivalencia')?'Mecanismo: '.$solicitud->mecanismo:'',
                ];



                // Generar el PDF y obtener la ruta
                $pdfPath = $this->generatePDF(new Request(['solicitud_sicadi_id' => $solicitud->id]), true);

                $this->enviarCorreosAlUsuario($datosCorreo,$solicitud,$userId,true,$pdfPath);


                DB::commit();
                // Eliminar el archivo PDF temporal
                if (file_exists($pdfPath)) {
                    unlink($pdfPath);
                }
                $respuestaID = 'success';
                $respuestaMSJ = 'Solicitud enviada con éxito';

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

        return redirect()->route('solicitud_sicadis.index')->with($respuestaID, $respuestaMSJ);
    }

    public function generatePDF(Request $request,$attach = false)
    {
        $solicitud_sicadiId = $request->query('solicitud_sicadi_id');

        $solicitud_sicadi = SolicitudSicadi::find($solicitud_sicadiId);
        $selectedRoleId = session('selected_rol');
        if ($selectedRoleId==2){
            $user = auth()->user();

            if ($solicitud_sicadi->cuil!=$user->cuil){
                abort(403, 'No autorizado.');
            }

        }






        $data = [
            'estado' => $solicitud_sicadi->estado,
            'fecha' => $solicitud_sicadi->fecha,
            'year' => $solicitud_sicadi->convocatoria->year,
            'convocatoria' => $solicitud_sicadi->convocatoria->nombre.' '.$solicitud_sicadi->convocatoria->tipo.' '.$solicitud_sicadi->convocatoria->year,
            'tipo' => $solicitud_sicadi->convocatoria->tipo,
            'foto' => $solicitud_sicadi->foto,
            'facultad' => $solicitud_sicadi->cargo_ua,
            'solicitante' => $solicitud_sicadi->apellido.', '.$solicitud_sicadi->nombre,
            'genero' => $solicitud_sicadi->genero,
            'nacimiento' => $solicitud_sicadi->nacimiento,
            'nacionalidad' => $solicitud_sicadi->nacionalidad,
            'cuil' => $solicitud_sicadi->cuil,
            'calle' => $solicitud_sicadi->calle,
            'nro' => $solicitud_sicadi->nro,
            'piso' => $solicitud_sicadi->piso,
            'depto' => $solicitud_sicadi->depto,
            'cp' => $solicitud_sicadi->cp,
            'email' => $solicitud_sicadi->email_institucional,
            'alternativo' => $solicitud_sicadi->email_alternativo,

            'telefono' => $solicitud_sicadi->celular,
            'notificacion' => ($solicitud_sicadi->notificacion)?'SI':'NO',
            'sedici' => $solicitud_sicadi->sedici,
            'orcid' => $solicitud_sicadi->orcid,
            'scholar' => $solicitud_sicadi->scholar,
            'scopus' => $solicitud_sicadi->scopus,
            'titulo' => $solicitud_sicadi->titulo,
            'titulo_entidad' => $solicitud_sicadi->titulo_entidad,

            'tituloposgrado' => $solicitud_sicadi->posgrado,
            'posgrado_entidad' => $solicitud_sicadi->posgrado_entidad,

            'unidad' => $solicitud_sicadi->ui_sigla.' - '.$solicitud_sicadi->ui_nombre,
            'cargo' => $solicitud_sicadi->cargo_docente,
            'dedicacion' => $solicitud_sicadi->cargo_dedicacion,

            'beca_tipo' => $solicitud_sicadi->beca_tipo,
            'beca_entidad' => $solicitud_sicadi->beca_entidad,
            'beca_inicio' => $solicitud_sicadi->beca_inicio,
            'beca_fin' => $solicitud_sicadi->beca_fin,
            'unidad_beca' => $solicitud_sicadi->beca_ui_sigla.' - '.$solicitud_sicadi->beca_ui_nombre,

            'carrera_cargo' => $solicitud_sicadi->carrera_cargo,
            'carrera_empleador' => $solicitud_sicadi->carrera_empleador,
            'carrera_ingreso' => $solicitud_sicadi->carrera_ingreso,

            'unidad_carrera' => $solicitud_sicadi->carrera_ui_sigla.' - '.$solicitud_sicadi->carrera_ui_nombre,


            'proyecto_entidad' => $solicitud_sicadi->proyecto_entidad,
            'proyecto_codigo' => $solicitud_sicadi->proyecto_codigo,
            'proyecto_titulo' => $solicitud_sicadi->proyecto_titulo,
            'proyecto_director' => $solicitud_sicadi->proyecto_director,
            'proyecto_inicio' => $solicitud_sicadi->proyecto_inicio,
            'proyecto_fin' => $solicitud_sicadi->proyecto_fin,

            'presentacion_ua' => $solicitud_sicadi->presentacion_ua,
            'categoria_spu' => $solicitud_sicadi->categoria_spu,
            'categoria_solicitada' => $solicitud_sicadi->categoria_solicitada,
            'mecanismo' => $solicitud_sicadi->mecanismo,
            'area' => $solicitud_sicadi->area,
            'subarea' => $solicitud_sicadi->subarea,

        ];
        //dd($data);


        $template = 'solicitud_sicadis.pdfsolicitud';

        $pdf = PDF::loadView($template, $data);

        $pdfPath = 'Sicadi_' . $solicitud_sicadi->cuil . '.pdf';

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

    public function enviarCorreosAlUsuario($datosCorreo, $solicitud,$userId, $adjuntarArchivos, $adjuntarPlanilla)
    {

        $user = User::find($userId); // Obtener el usuario por su ID

        // Enviar correo electrónico al usuario
        Mail::to($user->email)->send(new SicadiEnviada($datosCorreo,$solicitud, $adjuntarArchivos, $adjuntarPlanilla));

        // Enviar correo electrónico a tu servidor (ejemplo)
        Mail::to('marcosp@presi.unlp.edu.ar')->send(new SicadiEnviada($datosCorreo,$solicitud, $adjuntarArchivos, $adjuntarPlanilla));

        // Obtener el nombre del rol correspondiente al id 4
        $roleName = Role::find(Constants::ID_ADMIN_FACULTAD_PROYECTOS)->name;

        // Obtener usuarios que pertenecen a la facultad especificada y tienen el rol con id 4
        $usuarios = User::where('facultad_id', $solicitud->presentacion_ui)
            ->role($roleName)
            ->get();

        // Enviar correo electrónico a cada usuario
        foreach ($usuarios as $usuario) {
            Mail::to($usuario->email)->send(new SicadiEnviada($datosCorreo, $solicitud, $adjuntarArchivos, $adjuntarPlanilla));
        }
    }

    public function enviarCorreos( $datosCorreo, $solicitud)
    {



        $user = User::where('cuil', $solicitud->cuil)->first();
        if ($user) {
            // Enviar correo electrónico al usuario del director
            Mail::to($user->email)->send(new SicadiEnviada($datosCorreo, $solicitud));
        }


        // Obtener el nombre del rol correspondiente al id 4
        $roleName = Role::find(Constants::ID_ADMIN_FACULTAD_PROYECTOS)->name;

        // Obtener usuarios que pertenecen a la facultad especificada y tienen el rol con id 4
        $usuarios = User::where('facultad_id', $solicitud->presentacion_ui)
            ->role($roleName)
            ->get();

        // Enviar correo electrónico a cada usuario
        foreach ($usuarios as $usuario) {
            Mail::to($usuario->email)->send(new SicadiEnviada($datosCorreo, $solicitud));
        }
    }

    public function admitir($id)
    {
        $solicitud = SolicitudSicadi::findOrFail($id);


        DB::beginTransaction();
        try {

            $solicitud->estado = 'Admitida';

            $solicitud->save();
            /*$investigador = Investigador::find($viaje->investigador_id);


            $this->actualizarInvestigador($viaje,$investigador);*/


            $this->cambiarEstado($solicitud,'Solicitud admitida');


            $convocatoria = $solicitud->convocatoria->nombre.' '.$solicitud->convocatoria->tipo.' '.$solicitud->convocatoria->year;

            // Preparar datos para el correo
            $datosCorreo = [
                'from_email' => Constants::MAIL_SICADI,
                'from_name' => Constants::NOMBRE_SICADI,
                'asunto' => 'Admisión de Solicitud de Categorización SICADI '.$convocatoria,
                'convocatoria' => $convocatoria,

                'investigador' => $solicitud->apellido.', '.$solicitud->nombre.' ('.$solicitud->cuil.')',
                'categoria_solicitada' => $solicitud->categoria_solicitada,
                'comment' => 'La solicitud fue admitida para su evaluación',
            ];



            // Llama a la función enviarCorreos
            $this->enviarCorreos($datosCorreo, $solicitud);



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
        return redirect()->route('solicitud_sicadis.index')->with($respuestaID, $respuestaMSJ);
    }

    public function rechazar($id)
    {
        $solicitud_sicadi = SolicitudSicadi::find($id);




        return view('solicitud_sicadis.deny',compact('solicitud_sicadi'));

    }

    public function saveDeny(Request $request, $id)
    {
        $this->validate($request, [
            'comentarios' => 'required'
        ]);

        $input = $this->sanitizeInput($request->all());

        $solicitud = SolicitudSicadi::findOrFail($id);


        DB::beginTransaction();
        try {

            $solicitud->estado = 'No Admitida';

            $solicitud->save();

            $this->cambiarEstado($solicitud,$input['comentarios']);


            $convocatoria = $solicitud->convocatoria->nombre.' '.$solicitud->convocatoria->tipo.' '.$solicitud->convocatoria->year;

            // Preparar datos para el correo
            $datosCorreo = [
                'from_email' => Constants::MAIL_SICADI,
                'from_name' => Constants::NOMBRE_SICADI,
                'asunto' => 'NO Admisión de Solicitud de Categorización SICADI '.$convocatoria,
                'convocatoria' => $convocatoria,

                'investigador' => $solicitud->apellido.', '.$solicitud->nombre.' ('.$solicitud->cuil.')',
                'categoria_solicitada' => $solicitud->categoria_solicitada,
                'comment' => '<strong>Motivos de la no admisión</strong>: '.$input['comentarios'],
            ];


            // Llama a la función enviarCorreos
            $this->enviarCorreos($datosCorreo, $solicitud);


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
        return redirect()->route('solicitud_sicadis.index')->with($respuestaID, $respuestaMSJ);
    }

    public function rectificar($id)
    {
        $solicitud_sicadi = SolicitudSicadi::find($id);




        return view('solicitud_sicadis.rect',compact('solicitud_sicadi'));

    }

    public function saveRect(Request $request, $id)
    {
        $this->validate($request, [
            'comentarios' => 'required'
        ]);

        $input = $this->sanitizeInput($request->all());

        $solicitud = SolicitudSicadi::findOrFail($id);


        DB::beginTransaction();
        try {

            $solicitud->estado = 'Creada';

            $solicitud->save();

            $this->cambiarEstado($solicitud,$input['comentarios']);


            $convocatoria = $solicitud->convocatoria->nombre.' '.$solicitud->convocatoria->tipo.' '.$solicitud->convocatoria->year;

            // Preparar datos para el correo
            $datosCorreo = [
                'from_email' => Constants::MAIL_SICADI,
                'from_name' => Constants::NOMBRE_SICADI,
                'asunto' => 'Rectificación de Solicitud de Categorización SICADI '.$convocatoria,
                'convocatoria' => $convocatoria,

                'investigador' => $solicitud->apellido.', '.$solicitud->nombre.' ('.$solicitud->cuil.')',
                'categoria_solicitada' => $solicitud->categoria_solicitada,

                'comment' => '<strong>Motivos de la rectificación</strong>: '.$input['comentarios'],
            ];


            // Llama a la función enviarCorreos
            $this->enviarCorreos($datosCorreo, $solicitud);


            DB::commit();
            $respuestaID = 'success';
            $respuestaMSJ = 'Rectificada con éxito';

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
        return redirect()->route('solicitud_sicadis.index')->with($respuestaID, $respuestaMSJ);
    }


    public function exportar(Request $request)
    {
        $columnas = ['solicitud_sicadis.nombre', 'solicitud_sicadis.apellido', 'cuil','solicitud_sicadis.fecha','presentacion_ua','sicadi_convocatorias.nombre','solicitud_sicadis.estado', 'categoria_solicitada', 'categoria_asignada']; // Define las columnas disponibles
        // La lógica de exportación a Excel va aquí
        $filtros = $request->all();

        $query = SolicitudSicadi::select('solicitud_sicadis.id as id', 'solicitud_sicadis.nombre as persona_nombre', 'cuil','solicitud_sicadis.fecha','presentacion_ua', DB::raw("CONCAT(sicadi_convocatorias.nombre, ' ', sicadi_convocatorias.tipo, ' ', sicadi_convocatorias.year) as convocatoria"), DB::raw("CONCAT(solicitud_sicadis.apellido, ', ', solicitud_sicadis.nombre) as persona_apellido"),'solicitud_sicadis.estado as estado', 'categoria_solicitada', 'categoria_asignada')
            ->leftJoin('sicadi_convocatorias', 'solicitud_sicadis.convocatoria_id', '=', 'sicadi_convocatorias.id')
        ;



        if (!empty($filtros['filtroYear'])&&$filtros['filtroYear']!=-1) {
            $query->where('sicadi_convocatorias.year', $filtros['filtroYear']);
        }
        if (!empty($filtros['tipo'])&&$filtros['tipo']!=-1) {
            $query->where('sicadi_convocatorias.tipo', $filtros['tipo']);
        }
        if (!empty($filtros['mecanismo'])&&$filtros['mecanismo']!=-1) {
            $query->where('solicitud_sicadis.mecanismo', $filtros['mecanismo']);
        }
        if (!empty($filtros['solicitada'])&&$filtros['solicitada']!=-1) {
            $query->where('solicitud_sicadis.categoria_solicitada', $filtros['solicitada']);
        }

        if (!empty($filtros['estado'])&&$filtros['estado']!=-1) {
            $query->where('solicitud_sicadis.estado', $filtros['estado']);
        }
        if (!empty($filtros['presentacion_ua'])&&$filtros['presentacion_ua']!=-1) {
            $query->where('solicitud_sicadis.presentacion_ua', $filtros['presentacion_ua']);
        }

        if (!empty($filtros['asignada'])&&$filtros['asignada']!=-1) {
            $query->where('solicitud_sicadis.categoria_asignada', $filtros['asignada']);
        }

        if (!empty($filtros['busqueda'])) {


            $request->session()->put('nombre_filtro_joven', $filtros['busqueda']);

        }
        else{
            $busqueda = $request->session()->get('nombre_filtro_sicadi');

        }


        // Aplicar la búsqueda
        if (!empty($busqueda)) {
            $query->where(function ($query) use ($columnas, $busqueda) {
                foreach ($columnas as $columna) {
                    $query->orWhere($columna, 'like', "%$busqueda%");
                }
            });
        }

        $data = $query->get();

        // Crear el archivo Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Establecer encabezados
        $sheet->setCellValue('A1', 'Convocatoria');
        $sheet->setCellValue('B1', 'Investigador');
        $sheet->setCellValue('C1', 'C.U.I.L.');
        $sheet->setCellValue('D1', 'Fecha');
        $sheet->setCellValue('E1', 'E-mail');
        $sheet->setCellValue('F1', 'Estado');
        $sheet->setCellValue('G1', 'U. Académica');
        $sheet->setCellValue('H1', 'Solicitada');
        $sheet->setCellValue('I1', 'Asignada');



        // Llenar los datos
        $row = 2;
        foreach ($data as $item) {


            $fecha = \Carbon\Carbon::parse($item->fecha);
            $sheet->setCellValue('A' . $row, $item->convocatoria);
            $sheet->setCellValue('B' . $row, $item->persona_apellido);
            $sheet->setCellValue('C' . $row, $item->cuil);
            $sheet->setCellValue('D' . $row, $fecha->format('d/m/Y H:i:s'));
            $sheet->setCellValue('E' . $row, $item->email);
            $sheet->setCellValue('F' . $row, $item->estado);
            $sheet->setCellValue('G' . $row, $item->presentacion_ua);
            $sheet->setCellValue('H' . $row, $item->categoria_solicitada);
            $sheet->setCellValue('I' . $row, $item->categoria_asignada);

            $row++;
        }


        // Guardar el archivo
        $writer = new Xlsx($spreadsheet);
        $filename = 'sicadi_'.date('YmdHis').'.xlsx';

        // Establecer encabezados para la descarga
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        // Escribir el archivo
        $writer->save('php://output');
        exit;
    }

    public function migrarFotosSicadi()
    {
        $solicitudes = SolicitudSicadi::all();
        $migradas = 0;

        foreach ($solicitudes as $solicitud) {
            $fotoNombre = $solicitud->foto;

            if (empty($fotoNombre) || str_contains($fotoNombre, '/storage/')) {
                continue;
            }

            $rutaActual = public_path('images/sicadi/' . $fotoNombre);

            if (!file_exists($rutaActual)) {
                continue;
            }

            $extension = pathinfo($fotoNombre, PATHINFO_EXTENSION);
            $nuevoNombre = 'foto_' . Str::uuid() . '.' . $extension;
            $nuevaRuta = 'public/images/sicadi/' . $nuevoNombre;

            // Mover
            Storage::put($nuevaRuta, file_get_contents($rutaActual));

            // Actualizar DB
            $solicitud->foto = Storage::url($nuevaRuta); // guarda como /storage/images/sicadi/...
            $solicitud->save();

            // Borrar original
            unlink($rutaActual);
            $migradas++;
        }

        return response()->json(['migradas' => $migradas]);
    }
}
