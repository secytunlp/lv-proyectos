<?php

namespace App\Http\Controllers;

use App\Constants;
use App\Models\Carrerainv;
use App\Models\Categoria;
use App\Models\Integrante;
use App\Models\ViajeEstado;
use App\Models\Investigador;
use App\Models\Persona;
use App\Models\Proyecto;
use App\Models\Sicadi;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Models\Viaje;
use App\Models\Universidad;
use App\Models\Unidad;
use App\Models\Titulo;
use App\Models\Cargo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use ZipArchive;

use PDF;
use Illuminate\Support\Facades\Mail;
use App\Mail\ViajeEnviada;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
class ViajeController extends Controller

{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
        $this->middleware('permission:solicitud-listar|solicitud-crear|solicitud-editar|solicitud-eliminar', ['only' => ['index','store','dataTable']]);
        $this->middleware('permission:solicitud-crear', ['only' => ['create','store','buscarInvestigador']]);
        $this->middleware('permission:solicitud-editar', ['only' => ['edit','update']]);
        $this->middleware('permission:solicitud-eliminar', ['only' => ['destroy']]);
        //dd(session()->all());
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        //->where('nombre', Constants::YEAR_VIAJES)

        $periodos = DB::table('periodos')->orderBy('nombre','DESC')->get();


        $periodo = DB::table('periodos')->where('nombre', Constants::YEAR_VIAJES)->first();
        if (empty($periodo)) {
            $id = DB::table('periodos')->insertGetId([
                'nombre' => Constants::YEAR_VIAJES,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $id = $periodo->id; // Si el registro ya existe, obtén el ID existente
        }
        $currentPeriodo = ($request->session()->get('periodo_filtro_viaje'))?$request->session()->get('periodo_filtro_viaje'):$id;
        $facultades = DB::table('facultads')->pluck('nombre', 'id')->prepend('Todas','-1');
        //dd($currentPeriodo);
        // Pasar los períodos y la constante a la vista
        return view('viajes.index', [
            'periodos' => $periodos,
            'facultades' => $facultades,
            'currentPeriod' =>  $currentPeriodo// Asume que la constante es un ID o valor relevante
        ]);
    }

    public function clearFilter(Request $request)
    {
        // Limpiar el valor del filtro en la sesión
        $request->session()->forget('nombre_filtro_viaje');
        /*$request->session()->forget('periodo_filtro_viaje');
        $request->session()->forget('estado_filtro_viaje');
        $request->session()->forget('area_filtro_viaje');
        $request->session()->forget('facultad_filtro_viaje');*/
        //Log::info('Sesion limpia:', $request->session()->all());
        return response()->json(['status' => 'success']);
    }

    public function dataTable(Request $request)
    {
        $columnas = ['personas.nombre','periodos.nombre', 'personas.apellido', 'viajes.fecha','viajes.estado', 'facultads.cat', 'facultads.nombre', '','viajes.diferencia', 'viajes.puntaje']; // Define las columnas disponibles
        $columnaOrden = $columnas[$request->input('order.0.column')];
        $orden = $request->input('order.0.dir');
        $busqueda = $request->input('search.value');
        $periodo = $request->input('periodo'); // Obtener el filtro de período de la solicitud
        $estado = $request->input('estado');
        $area = $request->input('area');
        $facultadplanilla = $request->input('facultadplanilla');

        // Consulta base
        $query = Viaje::select('viajes.id as id', 'personas.nombre as persona_nombre', 'periodos.nombre as periodo_nombre', DB::raw("CONCAT(personas.apellido, ', ', personas.nombre) as persona_apellido"),'viajes.fecha as fecha','viajes.estado as estado', 'facultads.cat as facultad_cat', 'facultads.nombre as facultad_nombre','viajes.diferencia','viajes.puntaje')
            ->leftJoin('periodos', 'viajes.periodo_id', '=', 'periodos.id')
            ->leftJoin('investigadors', 'viajes.investigador_id', '=', 'investigadors.id')
            ->leftJoin('personas', 'investigadors.persona_id', '=', 'personas.id')
            ->leftJoin('facultads', 'viajes.facultadplanilla_id', '=', 'facultads.id')
            ->with(['evaluacions' => function($query) {
                // Añadir select específico de los campos que deseas de los evaluadores
                $query->select('viaje_evaluacions.viaje_id', 'user_name', 'user_id', 'interno', 'viaje_evaluacions.estado', 'viaje_evaluacions.puntaje')
                    ->with('user:id,name'); // Carga el usuario solo si el user_id no es null
            }]);

        // Filtrar por período si se proporciona
        if (!empty($periodo)) {

            $request->session()->put('periodo_filtro_viaje', $periodo);

        }
        else{
            $periodo = $request->session()->get('periodo_filtro_viaje');

        }
        if ($periodo=='-1'){
            $request->session()->forget('periodo_filtro_viaje');
            $periodo='';
        }
        if (!empty($periodo)) {
            $query->where('viajes.periodo_id', $periodo);
        }
        if (!empty($estado)) {

            $request->session()->put('estado_filtro_viaje', $estado);

        }
        else{
            $estado = $request->session()->get('estado_filtro_viaje');

        }
        if ($estado=='-1'){
            $request->session()->forget('estado_filtro_viaje');
            $estado='';
        }
        if (!empty($estado)) {
            $query->where('viajes.estado', $estado);
        }
        if (!empty($area)) {

            $request->session()->put('area_filtro_viaje', $area);

        }
        else{
            $area = $request->session()->get('area_filtro_viaje');

        }
        if ($area=='-1'){
            $request->session()->forget('area_filtro_viaje');
            $area='';
        }
        if (!empty($area)) {
            $query->where('facultads.cat', $area);
        }
        if (!empty($facultadplanilla)) {

            $request->session()->put('facultad_filtro_viaje', $facultadplanilla);

        }
        else{
            $facultadplanilla = $request->session()->get('facultad_filtro_viaje');

        }
        if ($facultadplanilla=='-1'){
            $request->session()->forget('facultad_filtro_viaje');
            $facultadplanilla='';
        }
        if (!empty($facultadplanilla)) {
            $query->where('facultads.id', $facultadplanilla);
        }

        if (!empty($busqueda)) {


            $request->session()->put('nombre_filtro_viaje', $busqueda);

        }
        else{
            $busqueda = $request->session()->get('nombre_filtro_viaje');

        }


        // Aplicar la búsqueda
        if (!empty($busqueda)) {
            $query->where(function ($query) use ($columnas, $busqueda) {
                foreach ($columnas as $columna) {
                    if ($columna){
                        $query->orWhere($columna, 'like', "%$busqueda%");
                    }

                }
            });
        }

        $selectedRoleId = session('selected_rol');
        if ($selectedRoleId==2){
            $user = auth()->user();


            $query->where(function ($query) use ($user) {
                $query->where('personas.cuil', '=', $user->cuil)
                ;
            });
        }

        if ($selectedRoleId==4){
            $user = auth()->user();
            //$currentDate = date('Y-m-d');

            $query->where(function ($query) use ($user) {
                $query->where('viajes.facultadplanilla_id', '=', $user->facultad_id);
            });
        }

        // Solo para evaluadores
        if ($selectedRoleId == 6) {
            $user = auth()->user();

            $query->where(function ($query) use ($user) {
                $query->whereHas('evaluacions', function ($q) use ($user) {
                    $q->where('user_cuil', '=', $user->cuil)
                        ->orWhere('user_id', '=', $user->id);
                })->orWhere('viajes.investigador_id', '=', $user->id);
            })
                ->leftJoin('viaje_evaluacions', 'viajes.id', '=', 'viaje_evaluacions.viaje_id')
                ->addSelect('viaje_evaluacions.estado as evaluacion_estado')
                ->where(function ($query) use ($user) {
                    $query->where('viaje_evaluacions.user_cuil', '=', $user->cuil)
                        ->orWhere('viaje_evaluacions.user_id', '=', $user->id);
                });
        }




        // Obtener la cantidad total de registros después de aplicar el filtro de búsqueda
        $recordsFiltered = $query->count();

        // Obtener solo los elementos paginados
        $datos = $query->orderBy($columnaOrden, $orden)
            ->skip($request->input('start'))
            ->take($request->input('length'))
            ->get();

        // Formatear el resultado agrupando evaluadores por viaje
        $datosFormateados = $datos->map(function ($viaje) {
            // Obtener el estado y puntaje de los evaluadores
            $evaluadores = $viaje->evaluacions->map(function ($evaluacion) {
                $user_name = $evaluacion->user_id ? $evaluacion->user->name : $evaluacion->user_name;
                $interno = ($evaluacion->interno) ? 'Interno':'Externo';
                return  $user_name. ' / ' .$interno. ' / ' . $evaluacion->estado . ' / P. ' . number_format($evaluacion->puntaje, 2, ',', '.');
            })->toArray(); // Convertir a un array
            return [
                'id' => $viaje->id,
                'persona_nombre' => $viaje->persona_nombre,
                'periodo_nombre' => $viaje->periodo_nombre,
                'persona_apellido' => $viaje->persona_apellido,
                'fecha' => $viaje->fecha,
                'estado' => $viaje->estado,
                'facultad_cat' => $viaje->facultad_cat,
                'facultad_nombre' => $viaje->facultad_nombre,
                'diferencia' => $viaje->diferencia,
                'puntaje' => $viaje->puntaje,
                'evaluadores' => $evaluadores, // Guardar la lista de evaluadores formateada
                'evaluacion_estado' => $viaje->evaluacion_estado, // Agregar el estado de la evaluación
            ];
        });
        //dd($datosFormateados);
        // Obtener la cantidad total de registros sin filtrar
        $recordsTotal = Viaje::count();

        return response()->json([
            'data' => $datosFormateados,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'draw' => $request->draw,
        ]);
    }


    public function exportar(Request $request)
    {
        $columnas = ['personas.nombre','periodos.nombre', 'personas.apellido', 'viajes.fecha','viajes.estado', 'facultads.cat', 'facultads.nombre', '','viajes.diferencia', 'viajes.puntaje']; // Define las columnas disponibles
        // La lógica de exportación a Excel va aquí
        $filtros = $request->all();

        $query = Viaje::select('viajes.id as id', 'personas.nombre as persona_nombre', 'periodos.nombre as periodo_nombre', DB::raw("CONCAT(personas.apellido, ', ', personas.nombre) as persona_apellido"),'viajes.fecha as fecha','viajes.estado as estado', 'facultads.cat as facultad_cat', 'facultads.nombre as facultad_nombre','viajes.diferencia','viajes.puntaje','personas.cuil','viajes.email','viajes.nacimiento','viajes.disciplina')
            ->leftJoin('periodos', 'viajes.periodo_id', '=', 'periodos.id')
            ->leftJoin('investigadors', 'viajes.investigador_id', '=', 'investigadors.id')
            ->leftJoin('personas', 'investigadors.persona_id', '=', 'personas.id')
            ->leftJoin('facultads', 'viajes.facultadplanilla_id', '=', 'facultads.id')
            ->with(['evaluacions' => function($query) {
                // Añadir select específico de los campos que deseas de los evaluadores
                $query->select('viaje_evaluacions.viaje_id', 'user_name', 'user_id', 'interno', 'viaje_evaluacions.estado', 'viaje_evaluacions.puntaje')
                    ->with('user:id,name'); // Carga el usuario solo si el user_id no es null
            }]);



        if (!empty($filtros['periodo'])) {
            $query->where('viajes.periodo_id', $filtros['periodo']);
        }
        if (!empty($filtros['estado'])) {
            $query->where('viajes.estado', $filtros['estado']);
        }
        if (!empty($filtros['area'])) {
            $query->where('facultads.cat', $filtros['area']);
        }
        if (!empty($filtros['facultadplanilla'])) {
            $query->where('facultads.id', $filtros['facultadplanilla']);
        }

        if (!empty($filtros['busqueda'])) {


            $request->session()->put('nombre_filtro_viaje', $filtros['busqueda']);

        }
        else{
            $busqueda = $request->session()->get('nombre_filtro_viaje');

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
        $sheet->setCellValue('A1', 'Período');
        $sheet->setCellValue('B1', 'Solicitante');
        $sheet->setCellValue('C1', 'C.U.I.L.');
        $sheet->setCellValue('D1', 'Edad');
        $sheet->setCellValue('E1', 'E-mail');
        $sheet->setCellValue('F1', 'Fecha');
        $sheet->setCellValue('G1', 'Estado');
        $sheet->setCellValue('H1', 'Área');
        $sheet->setCellValue('I1', 'U. Académica');
        $sheet->setCellValue('J1', 'Disciplina');
        $sheet->setCellValue('K1', 'Proyecto');
        $sheet->setCellValue('L1', 'Monto');
        $sheet->setCellValue('M1', 'Evaluadores');
        $sheet->setCellValue('N1', 'Diferencia');
        $sheet->setCellValue('O1', 'Puntaje');

        // Llenar los datos
        $row = 2;
        foreach ($data as $item) {
            $fechaLimiteEdad = \Carbon\Carbon::parse(Constants::YEAR_VIAJES.'-'.Constants::MES_EDAD_VIAJEES.'-'.Constants::DIA_EDAD_VIAJEES);
            $nacimiento = Carbon::parse($item->nacimiento);
            $viaje = Viaje::find($item->id);
            $proyectos = $viaje->proyectos()->get(); // todos
            $strProyectos = '';
            if (!empty($proyectos)) {
                foreach ($proyectos as $proyecto) {
                    $coDirectorQuery = Integrante::select(DB::raw("CONCAT(personas.apellido, ', ', personas.nombre) as codirector_apellido"))
                        ->leftJoin('investigadors', 'integrantes.investigador_id', '=', 'investigadors.id')
                        ->leftJoin('proyectos', 'integrantes.proyecto_id', '=', 'proyectos.id')
                        ->leftJoin('personas', 'investigadors.persona_id', '=', 'personas.id');
                    $coDirectorQuery->where('integrantes.tipo', 'Codirector');
                    $coDirectorQuery->where('integrantes.proyecto_id', $proyecto->id);
                    $codirector = $coDirectorQuery->first();

                    $cordir = ($codirector)?' CODIR: '.$codirector->codirector_apellido:'';
                    if ($proyecto->proyecto_id){
                        $proy = Proyecto::find($proyecto->proyecto_id);
                        $especialidad = $proy->disciplina()->nombre;
                        if ($proy->especialidad()){
                            $especialidad .= ' - '.$proy->especialidad()->nombre;
                        }
                    }

                    $strProyectos .= $proyecto->codigo.' DIR: '.$proyecto->director.$cordir.' ('.date('d/m/Y', strtotime($proyecto->desde)) .'-'.date('d/m/Y', strtotime($proyecto->hasta)).') Especialidad: '.$especialidad.'---';
                }
            }
            $evaluacions = $viaje->evaluacions()->get(); // todos
            $strEvaluacions = '';
            if (!empty($evaluacions)) {
                foreach ($evaluacions as $evaluacion) {
                    $strInterno = ($evaluacion->interno)?'Interno':'Externo';
                    $evaluador = ($evaluacion->user_id)?$evaluacion->user->name:$evaluacion->user_name;
                    $strEvaluacions .= $evaluador.' / '.$strInterno.' / '.$evaluacion->estado.' / P. '.number_format ( $evaluacion->puntaje , 2 , ',', '.' ).'---';
                }
            }
            $sheet->setCellValue('A' . $row, $item->periodo_nombre);
            $sheet->setCellValue('B' . $row, $item->persona_apellido);
            $sheet->setCellValue('C' . $row, $item->cuil);
            $sheet->setCellValue('D' . $row, $nacimiento->diffInYears($fechaLimiteEdad));
            $sheet->setCellValue('E' . $row, $item->email);
            $sheet->setCellValue('F' . $row, $item->fecha);
            $sheet->setCellValue('G' . $row, $item->estado);
            $sheet->setCellValue('H' . $row, $item->facultad_cat);
            $sheet->setCellValue('I' . $row, $item->facultad_nombre);
            $sheet->setCellValue('J' . $row, $item->disciplina);
            $sheet->setCellValue('K' . $row, $strProyectos);
            $sheet->setCellValue('L' . $row, '$' . number_format($viaje->presupuestos()->sum('monto'), 2, ',', '.'));
            $sheet->setCellValue('M' . $row, $strEvaluacions);
            $sheet->setCellValue('N' . $row, $item->diferencia);
            $sheet->setCellValue('O' . $row, $item->puntaje);
            $row++;
        }


        // Guardar el archivo
        $writer = new Xlsx($spreadsheet);
        $filename = 'viajees_'.date('YmdHis').'.xlsx';

        // Establecer encabezados para la descarga
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        // Escribir el archivo
        $writer->save('php://output');
        exit;
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $today = now();
        $cierreDate = \Carbon\Carbon::parse(Constants::CIERRE_VIAJES);
        if ($today->gt($cierreDate)) {

            return redirect()->route('viajes.index')->withErrors(['message' => 'La convocatoria no está vigente.']);
        }

        $titulos=Titulo::where('nivel', 'Grado')->orderBy('nombre','ASC')->get();
        $titulos = $titulos->pluck('full_name', 'id')->prepend('','');

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
        $tipoPresupuestos = DB::table('tipo_presupuestos')->where('id', 2)->get();
        //dd($tipoPresupuestos);
        $currentYear = date('Y');
        $startYear = 1994;
        $years = range($currentYear, $startYear);
        $years = array_combine($years, $years); // Esto crea un array asociativo con los años como claves y valores
        $categorias = Categoria::orderBy('id')->pluck('nombre', 'id')->prepend('', '');

        $sicadis = Sicadi::orderBy('id')->pluck('nombre', 'id')->prepend('', '');

        $user = auth()->user();

        $cuil = $user->cuil;

        $investigador = Investigador::whereHas('persona', function ($query) use ($cuil) {
            $query->where('cuil', '=', $cuil);
        })
            ->first(); // Tomar el primer investigador encontrado

//dd($proyectosAnteriores);
        // Comprobar si no se encontró un investigador
        if (!$investigador) {
            return redirect()->route('viajes.index')->withErrors(['message' => 'No se encontró un investigador asociado al CUIL del usuario. Comuníquese al correo viajes.secyt@presi.unlp.edu.ar']);
        }
        $periodo = DB::table('periodos')->where('nombre', Constants::YEAR_VIAJES)->first();
        // Obtener el ID del periodo anterior
        $previousPeriodoId = $periodo->id - 1;
        // Verificar si el investigador obtuvo subsidio en el periodo anterior
        $investigadorId = $investigador->id;

// Verificar si existen viajes con estado "Otorgada" en el periodo anterior
        $subsidioObtenido = Viaje::where('investigador_id', $investigadorId)
            ->where('periodo_id', $previousPeriodoId) // Usamos el ID del periodo anterior
            ->whereIn('estado', [
                'Otorgada-No-Rendida',
                'Otorgada-Rendida',
                'Otorgada-Renunciada',
                'Otorgada-Devuelta'
            ])
            ->exists();  // Devuelve true si existe al menos un registro que cumpla con las condiciones

// Ahora puedes usar $subsidioObtenido en tu lógica
        if ($subsidioObtenido) {
            // El investigador obtuvo subsidio en el periodo anterior

            return redirect()->route('viajes.index')->withErrors(['message' => 'No podrá completar su solicitud por haber obtenido el subsidio en la convocatoria anterior según lo establecido en el punto 18 del Anexo I de las pautas del llamo  (Resolución N° 11/19) donde se establece que: “No se podrán presentar aquellos postulantes que hayan sido beneficiados con el subsidio en la convocatoria anterior (los que han renunciado se consideran como subsidios otorgados).No puede completar la Solicitud por haber obtenido el subsidio en el período anterior']);
        }


        $noRendidas = DB::table('viaje_no_rendidas')->where('documento', $investigador->persona->documento)->get();
        $errores = [];

        foreach ($noRendidas as $noRendida) {
            $errores[] = 'Ud. no ha rendido la solicitud del año ' . $noRendida->year;
        }

        $currentPeriodo = Constants::YEAR_VIAJES;

        // Obtener los años en los que el investigador tuvo solicitudes otorgadas en los últimos dos años
        $noRendidas = Viaje::where('investigador_id', $investigador->id)
            ->where('estado', 'Otorgada')
            ->join('periodos', 'viajes.periodo_id', '=', 'periodos.id')
            ->whereIn('periodos.nombre', [$currentPeriodo - 1, $currentPeriodo - 2])
            ->pluck('periodos.nombre')
            ->unique()
            ->sort()
            ->values();

        if ($noRendidas->isNotEmpty()) {
            // Crear un mensaje con los años en los que ya tiene solicitudes otorgadas
            $years = $noRendidas->join(', ', ' y ');
            //return redirect()->back()->with('error', "El investigador ya tiene solicitudes otorgadas en los años $years.");
            $errores[] = 'Ud. no ha rendido la solicitud del año ' . $years;
        }




        if (!empty($errores)) {
            return redirect()->route('viajes.index')->withErrors($errores);
        }
        //dd($noRendidas);
        $integrantes = Integrante::where('investigador_id', $investigador->id)
            ->where(function ($query) {
                $query->where('estado', '!=', 'Baja Creada')
                    ->where('estado', '!=', 'Baja Recibida')
                    ->orWhereNull('estado'); // Incluye estado = null
            })
            ->where(function ($q) {
                $q->where('baja', '>', Carbon::now()->format('Y-m-d')) // Asegurarse que la baja sea futura
                ->orWhereNull('baja')
                    ->orWhere('baja', '0000-00-00');
            })
            ->whereHas('proyecto', function ($query) {
                $query->where('estado', 'Acreditado')
                    ->where('fin', '>', Carbon::now()->subYear()->endOfYear()->format('Y-m-d')); // Proyectos vigentes
            })
            ->get();

        $aYear = 0;
        $YearAgo = intval(Constants::YEAR_VIAJES)-1;
        //dd($integrantes);
        $tieneProyecto = ($integrantes->isEmpty())?0:1;
        $proyectosActuales = array();
        foreach ($integrantes as $integrante){
            $proyectoActual = array();
            $proyecto = Proyecto::findOrFail($integrante->proyecto_id);
            //dd($proyecto);
            $directorQuery = Integrante::select(DB::raw("CONCAT(personas.apellido, ', ', personas.nombre) as director_apellido"))
                ->leftJoin('investigadors', 'integrantes.investigador_id', '=', 'investigadors.id')
                ->leftJoin('proyectos', 'integrantes.proyecto_id', '=', 'proyectos.id')
                ->leftJoin('personas', 'investigadors.persona_id', '=', 'personas.id');
            $directorQuery->where('integrantes.tipo', 'Director');
            $directorQuery->where('integrantes.proyecto_id', $integrante->proyecto_id);
            $director = $directorQuery->first();
            $proyectoActual['id'] = $proyecto->id;
            $proyectoActual['codigo'] = $proyecto->codigo;
            $proyectoActual['titulo'] = $proyecto->titulo;
            $proyectoActual['director'] = $director->director_apellido;
            $proyectoActual['inicio'] = $integrante->alta;
            $proyectoActual['fin'] = (($integrante->baja)&&($integrante->baja!='0000-00-00'))?$integrante->baja:$proyecto->fin;
            $proyectoActual['estado'] = $proyecto->estado;
            //dd($proyectoActual);
            $proyectosActuales[]=$proyectoActual;
            if ($proyectoActual['inicio']<=$YearAgo.'12-31') {
                $aYear=1;
            }
        }


        $tipoInvestigador='';

        if (in_array($investigador->categoria_id, explode(",",Constants::CATEGORIAS_FORMADOS))) {
            $tipoInvestigador='Investigador Formado';
        }
        elseif (in_array($investigador->sicadi_id, explode(",",Constants::CATEGORIAS_FORMADOS))) {
            $tipoInvestigador='Investigador Formado';
        }


        // Buscar el primer título, cargo actual, etc.
        $titulo = $investigador->titulos->first(); // Primer título

        $cargo = $investigador->cargos()->where('activo', true)->orderBy('deddoc', 'asc')->orderBy('orden', 'asc')->first(); // Cargo actual
        $carrerainv = $investigador->carrerainvs()->where('actual', true)->first(); // Carrera actual

        $hoy = Carbon::today();
        $beca = $investigador->becas()->where('desde', '<=',$hoy)->where('hasta', '>=',$hoy)->first(); // Beca actual
        //$becas = $investigador->becas()->where('hasta', '<', $hoy)->get(); // Becas anteriores
        if (!$aYear){
            if ((is_null($beca))||(!$beca->unlp)){
                return redirect()->route('viajes.index')->withErrors(['message' => 'Debe contar al menos con un año de antigüedad en proyectos en ejecución o ser becario UNLP.']);
            }
        }
        if (!$tieneProyecto){
            if ((is_null($beca))||(!$beca->unlp)){
                return redirect()->route('viajes.index')->withErrors(['message' => 'Debe contar al menos con un proyecto en ejecución o ser becario UNLP.']);
            }
        }




        // Verificar si ya existe una solicitud para el período actual
        $existeSolicitud = Viaje::where('investigador_id', $investigador->id)
            ->where('periodo_id', $periodo->id) // Suponiendo que tienes un campo 'periodo_id' en la tabla de solicitudes
            ->exists();

        if ($existeSolicitud) {
            return redirect()->route('viajes.index')->withErrors(['message' => 'Ya existe una solicitud para este período.']);
        }

        return view('viajes.create',compact('titulos','sicadis','facultades','cargos','universidades','unidads','carrerainvs','years','organismos','investigador','titulo','categorias','cargo','carrerainv','beca','proyectosActuales','tipoPresupuestos','periodo','tipoInvestigador'));
    }



    private function validateRequest(Request $request)
    {
        // Definir las reglas de validación
        $rules = [
            'email' => 'nullable|email',

            'curriculum' => 'file|mimes:pdf,doc,docx|max:4048',
            'trabajo' => 'file|mimes:pdf,doc,docx|max:4048',
            'aceptacion' => 'file|mimes:pdf,doc,docx|max:4048',
            'invitacion' => 'file|mimes:pdf,doc,docx|max:4048',
            'convenioB' => 'file|mimes:pdf,doc,docx|max:4048',
            'aval' => 'file|mimes:pdf,doc,docx|max:4048',
            'convenioC' => 'file|mimes:pdf,doc,docx|max:4048',


            'egresos.*' => 'nullable|date_format:Y-m-d', // Validación del formato de fecha

            'ingresos.*' => 'nullable|date_format:Y-m-d', // Validación del formato de fecha
            'carringresos.*' => 'nullable|date_format:Y-m-d', // Validación del formato de fecha
            'ambitodesdes.*' => 'nullable|date_format:Y-m-d', // Validación del formato de fecha
            'ambitohastas.*' => 'nullable|date_format:Y-m-d|after_or_equal:ambitodesdes.*', // Validación del formato de fecha
            // Usamos una validación con regex que permite solo números o vacío
            'monto' => 'nullable|regex:/^\d+(\.\d+)?$/', // Permite enteros y decimales
            'montomontos.*' => 'nullable|regex:/^\d+(\.\d+)?$/', // Permite enteros y decimales

        ];

        // Definir los mensajes de error personalizados
        $messages = [
            'curriculum.mimes' => 'El archivo de curriculum debe ser un documento de tipo: pdf, doc, docx.',
            'curriculum.max' => 'El archivo de curriculum no debe ser mayor a 4 MB.',
            'trabajo.mimes' => 'El archivo de copia del trabajo debe ser un documento de tipo: pdf, doc, docx.',
            'trabajo.max' => 'El archivo de copia del trabajo no debe ser mayor a 4 MB.',
            'aceptacion.mimes' => 'El archivo de aceptación debe ser un documento de tipo: pdf, doc, docx.',
            'aceptacion.max' => 'El archivo de aceptación no debe ser mayor a 4 MB.',
            'invitacion.mimes' => 'El archivo de invitación del grupo receptor debe ser un documento de tipo: pdf, doc, docx.',
            'invitacion.max' => 'El archivo de invitación del grupo receptor no debe ser mayor a 4 MB.',
            'convenioB.mimes' => 'El archivo de convenio debe ser un documento de tipo: pdf, doc, docx.',
            'convenioB.max' => 'El archivo de convenio no debe ser mayor a 4 MB.',
            'aval.mimes' => 'El archivo de aval debe ser un documento de tipo: pdf, doc, docx.',
            'aval.max' => 'El archivo de aval no debe ser mayor a 4 MB.',
            'convenioC.mimes' => 'El archivo de convenio debe ser un documento de tipo: pdf, doc, docx.',
            'convenioC.max' => 'El archivo de convenio no debe ser mayor a 4 MB.',

            'egresos.*.date_format' => 'Fecha inválida en el título de grado',

            'ingresos.*.date_format' => 'Fecha inválida en el ingreso al cargo docente',
            'carringresos.*.date_format' => 'Fecha inválida en el ingreso a la carrera de investigación',
            'ambitodesdes.*.date_format' => 'Fecha desde inválida en uno de los lugares',
            'ambitohastas.*.date_format' => 'Fecha hasta inválida en uno de los lugares',
            'ambitohastas.*.after_or_equal' => 'La fecha desde debe ser posterior a la fecha hasta en uno de los lugares.',
            'montomontos.*.regex' => 'Monto inválido en uno de los montos solicitados a otros organismos',

        ];

        // Crear el validador con las reglas y mensajes
        $validator = Validator::make($request->all(), $rules, $messages);

        // Añadir la validación personalizada para la fecha de cierre
        $validator->after(function ($validator) use ($request) {
            $today = now();
            $cierreDate = \Carbon\Carbon::parse(Constants::CIERRE_VIAJES);
            if ($today->gt($cierreDate)) {
                $validator->errors()->add('convocatoria', 'La convocatoria no está vigente.');
            }


        });

        return $validator;
    }

    private function validateAdditionalData(Request $request)
    {
        //dd($request);
        $errores = [];

        $esSimple=0;
        // Guarda el mayor cargo encontrado en el investigador
        if (!empty($request->cargos)) {
            $esSimple=($request->deddocs[0]=='Simple')?1:0;

        }

        if ($esSimple){
            if ((empty($request->carrerainvs[0]))&&(!$request->institucionActual)) {
                $errores[] = "Si tiene dedicación simple, debe ser becario o tener un cargo en la carrera de investigación";
            }



            if ($request->has('unidadbeca_id')) {

                // Validación de jerarquía de unidad
                $unidadId = $request->input('unidadbeca_id');
                $targetIds = [
                    Constants::ID_UNIDAD_UNLP, // Primer ID objetivo
                    Constants::ID_UNIDAD_UNLP_CONICET  // Segundo ID objetivo
                ];

                $unidad = Unidad::find($unidadId);

                if ($unidad) {
                    if (!$unidad->isInHierarchy($targetIds)) {
                        $errores[] = "Si tiene dedicación simple el lugar de trabajo de la beca debe ser en la UNLP.";
                    }
                }
            }

            if ($request->has('unidadcarrera_id')) {

                // Validación de jerarquía de unidad
                $unidadId = $request->input('unidadcarrera_id');
                $targetIds = [
                    Constants::ID_UNIDAD_UNLP, // Primer ID objetivo
                    Constants::ID_UNIDAD_UNLP_CONICET  // Segundo ID objetivo
                ];

                $unidad = Unidad::find($unidadId);

                if ($unidad) {
                    if (!$unidad->isInHierarchy($targetIds)) {
                        $errores[] = "Si tiene dedicación simple el lugar de trabajo de la carrera de investigador debe ser en la UNLP.";
                    }
                }
            }


        }

// Validación de fechas de beca
        /*$becaDesde = $request->input('becadesdeActual');
        $becaHasta = $request->input('becahastaActual');
        $fechaCierre = Carbon::parse(Constants::CIERRE);
        $fechaActual = Carbon::now(); // Obtener la fecha actual
        if ($becaDesde && $becaHasta) {
            $fechaDesde = Carbon::parse($becaDesde);
            $fechaHasta = Carbon::parse($becaHasta);

            if ($fechaHasta->lessThanOrEqualTo($fechaDesde)) {
                $errores[] = "La fecha 'hasta' de la beca debe ser mayor que la fecha 'desde'.";
            }

            if ($fechaHasta->lessThan($fechaCierre)) {
                $errores[] = "Beca no vigente";
            }
            if ($fechaDesde->greaterThan($fechaCierre)) {
                $errores[] = "Beca no vigente";
            }

        }*/










        // Tu lógica para calcular el monto total
        $totalMonto = 0;

        $tipoPresupuestos = DB::table('tipo_presupuestos')->where('activo', true)->get();
        foreach ($tipoPresupuestos as $tipoPresupuesto) {
            $fechas = $request->input('presupuesto' . $tipoPresupuesto->id . 'fechas');
            $importes = $request->input('presupuesto' . $tipoPresupuesto->id . 'importes');

            if (!empty($fechas)){
                foreach ($fechas as $index => $fecha) {
                    $importe = $importes[$index] ?? 0;
                    $totalMonto += $importe;
                    // Validar solo si la fecha no está vacía
                    if (!empty($fecha)) {
                        try {
                            $fechaValida = Carbon::createFromFormat('Y-m-d', $fecha);
                        } catch (\Exception $e) {
                            $errores[] = 'La fecha "' . $fecha . '" no tiene un formato válido.';
                        }
                    }
                }
            }

        }

        // Verifica que el monto total no supere el máximo permitido
        if ($totalMonto > Constants::MONTO_VIAJES) {
            $errores[] = 'El monto total no puede superar el límite máximo de $' . number_format(Constants::MONTO_VIAJES, 2, ',', '.');
        }

        $desdes = $request->input('ambitodesdes');
        $hastas = $request->input('ambitohastas');
        $rangoInicio = \DateTime::createFromFormat('d/m/Y', Constants::RANGO_INI_VIAJES.Constants::YEAR_VIAJES);
        $yearAgo = intval(Constants::YEAR_VIAJES)+1;
        $rangoFin = \DateTime::createFromFormat('d/m/Y', Constants::RANGO_FIN_VIAJES.$yearAgo);
        if (!empty($desdes)){
            foreach ($desdes as $index => $desde) {
                $fechaDesde = \DateTime::createFromFormat('Y-m-d', $desde);
                $fechaHasta = \DateTime::createFromFormat('Y-m-d', $hastas[$index]);

                // Verificar que ambas fechas estén dentro del rango
                if ($fechaDesde < $rangoInicio || $fechaDesde > $rangoFin) {
                    $errores["ambitodesdes.$index"] = 'Fecha desde fuera del rango '.Constants::RANGO_INI_VIAJES.Constants::YEAR_VIAJES.' - '.Constants::RANGO_FIN_VIAJES.$yearAgo.' en Lugar';
                }
                if ($fechaHasta < $rangoInicio || $fechaHasta > $rangoFin) {
                    $errores["ambitohastas.$index"] = 'Fecha hasta fuera del rango '.Constants::RANGO_INI_VIAJES.Constants::YEAR_VIAJES.' - '.Constants::RANGO_FIN_VIAJES.$yearAgo.' en Lugar';
                }
                if ($request->trabajodesde && $request->trabajohasta){
                    $desdeTrabajo = \DateTime::createFromFormat('Y-m-d', $request->trabajodesde);
                    $hastaTrabajo = \DateTime::createFromFormat('Y-m-d', $request->trabajohasta);

                    if ($desdeTrabajo<$fechaDesde||$hastaTrabajo>$fechaHasta) {
                        $errores[]='El período del congreso no está contenido en su totalidad en el período del lugar cargado';
                    }
                }



            }
        }

        return $errores;
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
        $errors = $this->validateAdditionalData($request);

        // Si hay errores, redirigir con errores
        if (!empty($errors)) {
            return redirect()->back()->withErrors($errors)->withInput();
        }

        $input = $request->all();
        // Asegurarse de que los checkbox tienen valor 0 si no se enviaron

        $input['nacional'] = ($request->nacional_id='Nacional') ? 1 : 0;
        $input['notificacion'] = isset($request->notificacion) ? 1 : 0;

        $input['fecha']=Carbon::now();

        $user = auth()->user();

        $cuil = $user->cuil;
        // Crear la carpeta si no existe
        $destinationPath = public_path('/files/viajes' . Constants::YEAR_VIAJES.'/'.$cuil);
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0777, true);
        }

        //$input['alta']= Constants::YEAR.'-01-01';
        $input['estado']= 'Creada';
        // Manejo de archivos
        $input['curriculum'] ='';
        if ($files = $request->file('curriculum')) {
            $file = $request->file('curriculum');
            $name = 'CV_'.time().'.'.$file->getClientOriginalExtension();

            $file->move($destinationPath, $name);
            $input['curriculum'] = "files/viajes".Constants::YEAR_VIAJES."/$cuil/$name";
        }
        $input['trabajo'] ='';
        if ($files = $request->file('trabajo')) {
            $file = $request->file('trabajo');
            $name = 'Trabajo_'.time().'.'.$file->getClientOriginalExtension();

            $file->move($destinationPath, $name);
            $input['trabajo'] = "files/viajes".Constants::YEAR_VIAJES."/$cuil/$name";
        }
        $input['aceptacion'] ='';
        if ($files = $request->file('aceptacion')) {
            $file = $request->file('aceptacion');
            $name = 'Aceptacion_'.time().'.'.$file->getClientOriginalExtension();

            $file->move($destinationPath, $name);
            $input['aceptacion'] = "files/viajes".Constants::YEAR_VIAJES."/$cuil/$name";
        }
        $input['invitacion'] ='';
        if ($files = $request->file('invitacion')) {
            $file = $request->file('invitacion');
            $name = 'Invitacion_'.time().'.'.$file->getClientOriginalExtension();

            $file->move($destinationPath, $name);
            $input['invitacion'] = "files/viajes".Constants::YEAR_VIAJES."/$cuil/$name";
        }
        $input['convenioB'] ='';
        if ($files = $request->file('convenioB')) {
            $file = $request->file('convenioB');
            $name = 'ConvenioB_'.time().'.'.$file->getClientOriginalExtension();

            $file->move($destinationPath, $name);
            $input['convenioB'] = "files/viajes".Constants::YEAR_VIAJES."/$cuil/$name";
        }
        $input['convenioC'] ='';
        if ($files = $request->file('convenioC')) {
            $file = $request->file('convenioC');
            $name = 'ConvenioC_'.time().'.'.$file->getClientOriginalExtension();

            $file->move($destinationPath, $name);
            $input['convenioC'] = "files/viajes".Constants::YEAR_VIAJES."/$cuil/$name";
        }
        $input['aval'] ='';
        if ($files = $request->file('aval')) {
            $file = $request->file('aval');
            $name = 'Aval_'.time().'.'.$file->getClientOriginalExtension();

            $file->move($destinationPath, $name);
            $input['aval'] = "files/viajes".Constants::YEAR_VIAJES."/$cuil/$name";
        }

        DB::beginTransaction();
        $ok = 1;

        try {



            $solicitud = Viaje::create($input);

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

            $this->guardarSolicitud($request,$solicitud);



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

        return redirect()->route('viajes.index')->with($respuestaID, $respuestaMSJ);
    }

    public function guardarSolicitud(Request $request, $solicitud,$actualizar=false)
    {
// Guardar el primer título pasado en $request->titulo en la columna titulo_id del investigador
        if (!empty($request->titulos)) {
            $solicitud->titulo_id = $request->titulos[0];
            $solicitud->egresogrado = $request->egresos[0];
            $titulo=Titulo::findOrFail($solicitud->titulo_id );
            $solicitud->titulogrado = $titulo->nombre.' ('.$titulo->universidad->nombre.')';
            $solicitud->save();
        }

        if (!empty($request->categorias)) {
            $solicitud->categoria_id = $request->categorias[0];

            $solicitud->save();
        }

        if (!empty($request->sicadis)) {
            $solicitud->sicadi_id = $request->sicadis[0];

            $solicitud->save();
        }


        // Guarda el mayor cargo encontrado en el investigador
        if (!empty($request->cargos)) {
            $solicitud->cargo_id = $request->cargos[0];
            $solicitud->deddoc = $request->deddocs[0];
            $solicitud->ingreso_cargo = $request->ingresos[0];
            $solicitud->facultad_id = $request->facultads[0];

            $solicitud->save();
        }


        if (!empty($request->carrerainvs)) {
            $solicitud->carrerainv_id = $request->carrerainvs[0];
            $solicitud->organismo_id = $request->organismos[0];
            $solicitud->ingreso_carrerainv = $request->carringresos[0];
            $solicitud->save();
        }






        //if (!$actualizar){
        $solicitud->proyectos()->delete();
            $integrantes = Integrante::where('investigador_id', $solicitud->investigador_id)
                ->where(function ($query) {
                    $query->where('estado', '!=', 'Baja Creada')
                        ->where('estado', '!=', 'Baja Recibida')
                        ->orWhereNull('estado'); // Incluye estado = null
                })
                ->where(function ($q) {
                    $q->where('baja', '>', Carbon::now()->format('Y-m-d')) // Asegurarse que la baja sea futura
                    ->orWhereNull('baja')
                        ->orWhere('baja', '0000-00-00');
                })
                ->whereHas('proyecto', function ($query) {
                    $query->where('estado', 'Acreditado')
                        ->where('fin', '>', Carbon::now()->subYear()->endOfYear()->format('Y-m-d')); // Proyectos vigentes
                })
                ->get();


            //dd($integrantes);

            foreach ($integrantes as $integrante){

                $proyecto = Proyecto::findOrFail($integrante->proyecto_id);
                //dd($proyecto);


                // Inserta el registro en la tabla intermedia 'investigador_cargos'
                DB::table('viaje_proyectos')->insert([
                    'viaje_id' => $solicitud->id,
                    'proyecto_id' => $proyecto->id,


                    'desde' => $integrante->alta,
                    'hasta' => (($integrante->baja)&&($integrante->baja!='0000-00-00'))?$integrante->baja:$proyecto->fin,

                    'seleccionado' => ($integrante->proyecto_id==$request->proyectoSeleccionado)?1:0,
                    'created_at' => now(), // Establece la fecha y hora de creación
                    'updated_at' => now(), // Establece la fecha y hora de actualización
                ]);




            }

       // }
        $solicitud->ambitos()->delete();
        if (!empty($request->ambitoinstitucions)) {

            foreach ($request->ambitoinstitucions as $item => $v) {





                // Inserta el registro en la tabla intermedia 'investigador_cargos'
                DB::table('viaje_ambitos')->insert([
                    'viaje_id' => $solicitud->id, // Supongo que tienes un objeto $investigador disponible
                    'desde' => $request->ambitodesdes[$item],
                    'hasta' => $request->ambitohastas[$item],
                    'institucion' => $request->ambitoinstitucions[$item],
                    'ciudad' => $request->ambitociudads[$item],
                    'pais' => $request->ambitopais[$item],
                    'created_at' => now(), // Establece la fecha y hora de creación
                    'updated_at' => now(), // Establece la fecha y hora de actualización
                ]);
            }
        }

        $solicitud->presupuestos()->delete();
        $tipoPresupuestos = DB::table('tipo_presupuestos')->where('activo', true)->get();
        foreach ($tipoPresupuestos as $tipoPresupuesto) {
            $ids = $request->input('presupuesto' . $tipoPresupuesto->id . 'ids');
            $fechas = $request->input('presupuesto' . $tipoPresupuesto->id . 'fechas');
            $importes = $request->input('presupuesto' . $tipoPresupuesto->id . 'importes');
            $detalles = $request->input('presupuesto' . $tipoPresupuesto->id . 'detalles');
            if (!empty($fechas)){
                if ($tipoPresupuesto->id == 2) {
                    $conceptos = $request->input('presupuesto' . $tipoPresupuesto->id . 'conceptos');
                    $dias = $request->input('presupuesto' . $tipoPresupuesto->id . 'dias');
                    $lugares = $request->input('presupuesto' . $tipoPresupuesto->id . 'lugar');
                    $pasajes = $request->input('presupuesto' . $tipoPresupuesto->id . 'pasajes');
                    $destinos = $request->input('presupuesto' . $tipoPresupuesto->id . 'destino');
                    $inscripciones = $request->input('presupuesto' . $tipoPresupuesto->id . 'inscripcion');
                    $otros = $request->input('presupuesto' . $tipoPresupuesto->id . 'otros');




                    foreach ($fechas as $index => $fecha) {

                        // Obtén los valores de cada campo, o usa una cadena vacía si no están definidos
                        $concepto = $conceptos[$index] ?? '';
                        $dia = $dias[$index] ?? '';
                        $lugar = $lugares[$index] ?? '';
                        $pasaje = $pasajes[$index] ?? '';
                        $destino = $destinos[$index] ?? '';
                        $inscripcion = $inscripciones[$index] ?? '';
                        $otro = $otros[$index] ?? '';
                        $importe = $importes[$index] ?? 0;




                        // Verifica si al menos uno de los campos no está vacío
                        if (!empty($concepto) || !empty($dia) || !empty($lugar) || !empty($pasaje) || !empty($destino) || !empty($inscripcion) || !empty($otro) || !empty($importe)) {
                            // Solo concatenar los campos no vacíos
                            $campos = array_filter([
                                $concepto,
                                $dia,
                                $lugar,
                                $pasaje,
                                $destino,
                                $inscripcion,
                                $otro
                            ]);
                            // Registrar en el log el contenido de $campos
                            //Log::info('Campos después del filtro:', $campos);
                            // Concatenar los valores con "|", solo incluyendo los campos no vacíos
                            if (!empty($campos)) {
                                $detalle = implode('|', $campos);
                                // Si hay un id existente, se actualiza en lugar de insertar
                                /*if (!empty($ids[$index])) {
                                    DB::table('viaje_presupuestos')
                                        ->where('id', $ids[$index])
                                        ->update([
                                            'viaje_id' => $solicitud->id,
                                            'tipo_presupuesto_id' => $tipoPresupuesto->id,
                                            'fecha' => $fecha,
                                            'detalle' => $detalle,
                                            'monto' => $importe,
                                            'updated_at' => now(),
                                        ]);
                                } else {*/


                                    // Guardar el presupuesto con el campo detalle concatenado
                                    DB::table('viaje_presupuestos')->insert([
                                        'viaje_id' => $solicitud->id,
                                        'tipo_presupuesto_id' => $tipoPresupuesto->id,
                                        'fecha' => $fecha,
                                        'detalle' => $detalle,
                                        'monto' => $importe,
                                        'created_at' => now(),
                                        'updated_at' => now(),
                                    ]);
                                //}
                            }
                        }



                    }
                } else {
                    // Guardar presupuestos sin concatenación de campos
                    foreach ($fechas as $index => $fecha) {
// Verifica si el detalle o el importe no están vacíos
                        if (!empty($detalles[$index]) || !empty($importes[$index])) {
                            /*if (!empty($ids[$index])) {
                                DB::table('viaje_presupuestos')
                                    ->where('id', $ids[$index])
                                    ->update([
                                        'viaje_id' => $solicitud->id,
                                        'tipo_presupuesto_id' => $tipoPresupuesto->id,
                                        'fecha' => $fecha,
                                        'detalle' => $detalles[$index],
                                        'monto' => $importes[$index] ?? 0,
                                        'updated_at' => now(),
                                    ]);
                            } else {*/
                                DB::table('viaje_presupuestos')->insert([
                                    'viaje_id' => $solicitud->id,
                                    'tipo_presupuesto_id' => $tipoPresupuesto->id,
                                    'fecha' => $fecha,
                                    'detalle' => $detalles[$index],
                                    'monto' => ($importes[$index]) ? $importes[$index] : 0,
                                    'created_at' => now(), // Establece la fecha y hora de creación
                                    'updated_at' => now(), // Establece la fecha y hora de actualización
                                ]);
                            //}
                        }

                    }
                }
            }

        }
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
        $viaje = Viaje::find($id);

        $today = now();
        $cierreDate = \Carbon\Carbon::parse(Constants::CIERRE_VIAJES);
        if ($today->gt($cierreDate)) {

            return redirect()->route('viajes.index')->withErrors(['message' => 'La convocatoria no está vigente.']);
        }

        $titulos=Titulo::where('nivel', 'Grado')->orderBy('nombre','ASC')->get();
        $titulos = $titulos->pluck('full_name', 'id')->prepend('','');

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
        $tipoPresupuestos = DB::table('tipo_presupuestos')->where('id', 2)->get();
        //dd($tipoPresupuestos);
        $currentYear = date('Y');
        $startYear = 1994;
        $years = range($currentYear, $startYear);
        $years = array_combine($years, $years); // Esto crea un array asociativo con los años como claves y valores
        $categorias = Categoria::orderBy('id')->pluck('nombre', 'id')->prepend('', '');

        $sicadis = Sicadi::orderBy('id')->pluck('nombre', 'id')->prepend('', '');


        $hoy = Carbon::today(); //Aquí se obtiene la fecha de hoy
        $proyectos = $viaje->proyectos()->get();

        $proyectosActuales = array();
        foreach ($proyectos as $proy){
            $proyectoActual = array();
            //dd($proy);
            $proyecto = Proyecto::findOrFail($proy->proyecto_id);
            //dd($proyecto);
            $directorQuery = Integrante::select(DB::raw("CONCAT(personas.apellido, ', ', personas.nombre) as director_apellido"))
                ->leftJoin('investigadors', 'integrantes.investigador_id', '=', 'investigadors.id')
                ->leftJoin('proyectos', 'integrantes.proyecto_id', '=', 'proyectos.id')
                ->leftJoin('personas', 'investigadors.persona_id', '=', 'personas.id');
            $directorQuery->where('integrantes.tipo', 'Director');
            $directorQuery->where('integrantes.proyecto_id', $proyecto->id);
            $director = $directorQuery->first();
            $proyectoActual['id'] = $proyecto->id;
            $proyectoActual['codigo'] = $proyecto->codigo;
            $proyectoActual['titulo'] = $proyecto->titulo;
            $proyectoActual['director'] = $director->director_apellido;
            $proyectoActual['inicio'] = $proy->desde;
            $proyectoActual['fin'] = $proy->hasta;
            $proyectoActual['estado'] = $proyecto->estado;
            $proyectoActual['seleccionado'] = ($proy->seleccionado)?1:0;
            //dd($proyectoActual);
            $proyectosActuales[]=$proyectoActual;

        }





        $periodo = DB::table('periodos')->where('nombre', Constants::YEAR_VIAJES)->first();




        return view('viajes.edit',compact('titulos','facultades','cargos','universidades','unidads','carrerainvs','years','organismos','viaje','proyectosActuales','tipoPresupuestos','periodo','categorias','sicadis'));
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
        $errors = $this->validateAdditionalData($request);

        // Si hay errores, redirigir con errores
        if (!empty($errors)) {
            return redirect()->back()->withErrors($errors)->withInput();
        }

        $input = $request->all();
        // Asegurarse de que los checkbox tienen valor 0 si no se enviaron
        $input['nacional'] = ($request->nacional_id='Nacional') ? 1 : 0;
        $input['notificacion'] = isset($request->notificacion) ? 1 : 0;

        $input['fecha']=Carbon::now();

        $user = auth()->user();

        $cuil = $user->cuil;
        // Crear la carpeta si no existe
        $destinationPath = public_path('/files/viajes' . Constants::YEAR_VIAJES.'/'.$cuil);
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0777, true);
        }

        //$input['alta']= Constants::YEAR.'-01-01';
        //$input['estado']= 'Creada';
        // Manejo de archivos
        //$input['curriculum'] ='';
        if ($files = $request->file('curriculum')) {
            $file = $request->file('curriculum');
            $name = 'CV_'.time().'.'.$file->getClientOriginalExtension();

            $file->move($destinationPath, $name);
            $input['curriculum'] = "files/viajees".Constants::YEAR_VIAJES."/$cuil/$name";
        }

        if ($files = $request->file('trabajo')) {
            $file = $request->file('trabajo');
            $name = 'Trabajo_'.time().'.'.$file->getClientOriginalExtension();

            $file->move($destinationPath, $name);
            $input['trabajo'] = "files/viajes".Constants::YEAR_VIAJES."/$cuil/$name";
        }

        if ($files = $request->file('aceptacion')) {
            $file = $request->file('aceptacion');
            $name = 'Aceptacion_'.time().'.'.$file->getClientOriginalExtension();

            $file->move($destinationPath, $name);
            $input['aceptacion'] = "files/viajes".Constants::YEAR_VIAJES."/$cuil/$name";
        }

        if ($files = $request->file('invitacion')) {
            $file = $request->file('invitacion');
            $name = 'Invitacion_'.time().'.'.$file->getClientOriginalExtension();

            $file->move($destinationPath, $name);
            $input['invitacion'] = "files/viajes".Constants::YEAR_VIAJES."/$cuil/$name";
        }

        if ($files = $request->file('convenioB')) {
            $file = $request->file('convenioB');
            $name = 'ConvenioB_'.time().'.'.$file->getClientOriginalExtension();

            $file->move($destinationPath, $name);
            $input['convenioB'] = "files/viajes".Constants::YEAR_VIAJES."/$cuil/$name";
        }

        if ($files = $request->file('convenioC')) {
            $file = $request->file('convenioC');
            $name = 'ConvenioC_'.time().'.'.$file->getClientOriginalExtension();

            $file->move($destinationPath, $name);
            $input['convenioC'] = "files/viajes".Constants::YEAR_VIAJES."/$cuil/$name";
        }

        if ($files = $request->file('aval')) {
            $file = $request->file('aval');
            $name = 'Aval_'.time().'.'.$file->getClientOriginalExtension();

            $file->move($destinationPath, $name);
            $input['aval'] = "files/viajes".Constants::YEAR_VIAJES."/$cuil/$name";
        }



        $solicitud = Viaje::find($id);
        DB::beginTransaction();
        $ok = 1;

        try {
            $solicitud->update($input);


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
            $this->guardarSolicitud($request,$solicitud,true);



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

        return redirect()->route('viajes.index')->with($respuestaID, $respuestaMSJ);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $viaje = Viaje::findOrFail($id);


        // Elimina las relaciones
        $viaje->proyectos()->delete();
        $viaje->presupuestos()->delete();
        $viaje->estados()->delete();
        $viaje->ambitos()->delete();
        $viaje->montos()->delete();

        // Elimina el viaje
        $viaje->delete();

        return redirect()->route('viajes.index')
            ->with('success','Solicitud eliminada con éxito');
    }

    public function cambiarEstado($viaje, $comentarios)
    {

        // Actualizar el registro de estado existente donde 'hasta' es null
        ViajeEstado::where('viaje_id', $viaje->id)
            ->whereNull('hasta')
            ->update(['hasta' => Carbon::now()]);

        // Obtener el ID del usuario autenticado
        $userId = Auth::id();

        // Crear registro en integrante_estados con el estado "Alta Creada" y el user_id
        $viaje->estados()->create([
            'estado' => $viaje->estado,
            'user_id' => $userId,
            'comentarios' => $comentarios,
            'desde' => Carbon::now(), // Establecer 'desde' como la fecha actual

        ]);
    }

    public function generatePDF(Request $request,$attach = false)
    {
        $viajeId = $request->query('viaje_id');

        // Consulta base


        $query = Viaje::select('viajes.id as id','viajes.estado as estado','viajes.fecha as fecha', 'personas.nombre as persona_nombre', 'periodos.nombre as periodo_nombre', DB::raw("CONCAT(personas.apellido, ', ', personas.nombre) as persona_apellido"), 'cuil','viajes.calle','viajes.nro','viajes.piso','viajes.depto','viajes.cp','viajes.email','viajes.telefono','viajes.notificacion','titulos.nombre as titulo', 'viajes.egresogrado','tituloposgrado.nombre as tituloposgrado', 'viajes.egresoposgrado', DB::raw("CONCAT(unidads.nombre, ' - ', unidads.sigla) as unidad"), 'facultads.cat as facultad_cat','cargos.nombre as cargo','viajes.deddoc as dedicacion', 'facultads.nombre as facultad_nombre', 'facultadplanilla.nombre as facultadplanilla_nombre', DB::raw("CONCAT(unidadbeca.nombre, ' - ', unidadbeca.sigla) as unidadbeca"),'viajes.director','viajes.objetivo','viajes.justificacion', 'carrerainvs.nombre as carrerainvs_nombre', 'organismos.nombre as organismos_nombre','viajes.ingreso_carrerainv', DB::raw("CONCAT(unidadcarrera.nombre, ' - ', unidadcarrera.sigla) as unidadcarrera"))
            ->leftJoin('periodos', 'viajes.periodo_id', '=', 'periodos.id')
            ->leftJoin('investigadors', 'viajes.investigador_id', '=', 'investigadors.id')
            ->leftJoin('personas', 'investigadors.persona_id', '=', 'personas.id')
            ->leftJoin('facultads', 'viajes.facultad_id', '=', 'facultads.id')
            ->leftJoin('facultads as facultadplanilla', 'viajes.facultadplanilla_id', '=', 'facultadplanilla.id')
            ->leftJoin('titulos', 'viajes.titulo_id', '=', 'titulos.id')
            ->leftJoin('titulos as tituloposgrado', 'viajes.titulopost_id', '=', 'tituloposgrado.id')
            ->leftJoin('unidads', 'viajes.unidad_id', '=', 'unidads.id')
            ->leftJoin('unidads as unidadcarrera', 'viajes.unidadcarrera_id', '=', 'unidadcarrera.id')
            ->leftJoin('unidads as unidadbeca', 'viajes.unidadbeca_id', '=', 'unidadbeca.id')
            ->leftJoin('cargos', 'viajes.cargo_id', '=', 'cargos.id')
            ->leftJoin('carrerainvs', 'viajes.carrerainv_id', '=', 'carrerainvs.id')
            ->leftJoin('organismos', 'viajes.organismo_id', '=', 'organismos.id');

        $query->where('viajes.id', $viajeId);

        $viaje = Viaje::find($viajeId);


        $beca = $viaje->becas()->where('actual', true)->first(); // Beca actual
        $becas = $viaje->becas()->where('actual', false)->get(); // Becas anteriores

        $proyectosActuales = $viaje->proyectos()->where('actual', true)->get();
        $proyectosAnteriores = $viaje->proyectos()->where('actual', false)->get();

        // Obtener solo los elementos paginados
        $datos = $query->first();
        //dd($datos);
        //$integrante = Integrante::findOrFail($integranteId);
        //dd($datos);

        $tipoPresupuestos = DB::table('tipo_presupuestos')->where('activo', true)->get();
        $template = 'viajes.pdfsolicitud';
        $presupuestos = $viaje->presupuestos()->get();

        $data = [
            'estado' => $datos->estado,
            'year' => $datos->periodo_nombre,
            'facultad' => $datos->facultad_nombre,
            'solicitante' => $datos->persona_apellido,
            'cuil' => $datos->cuil,
            'calle' => $datos->calle,
            'nro' => $datos->nro,
            'piso' => $datos->piso,
            'depto' => $datos->depto,
            'cp' => $datos->cp,
            'email' => $datos->email,
            'telefono' => $datos->telefono,
            'notificacion' => ($datos->notificacion)?'SI':'NO',
            'titulo' => $datos->titulo,
            'egreso' => $datos->egresogrado,
            'tituloposgrado' => $datos->tituloposgrado,
            'egresoposgrado' => $datos->egresoposgrado,
            'unidad' => $datos->unidad,
            'cargo' => $datos->cargo,
            'dedicacion' => $datos->dedicacion,
            'beca' => $beca,
            'becas' => $becas,
            'unidadbeca' => $datos->unidadbeca,
            'director' => ($datos->director)?'SI':'NO',
            'proyectosActuales' => $proyectosActuales,
            'proyectosAnteriores' => $proyectosAnteriores,
            'facultadplanilla' => $datos->facultadplanilla_nombre,
            'objetivo' => $datos->objetivo,
            'justificacion' => $datos->justificacion,
            'tipoPresupuestos' => $tipoPresupuestos,
            'presupuestos' => $presupuestos,
            'carrerainv' => $datos->carrerainv_nombre,
            'organismo' => $datos->organismo_nombre,
            'ingreso_carrera' => $datos->ingreso_carrera,
            'unidadcarrera' => $datos->unidadcarrerainv,
        ];
        //dd($data);




        $pdf = PDF::loadView($template, $data);

        $pdfPath = 'Viaje_' . $datos->cuil . '.pdf';

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
        $viajeId = $request->query('viaje_id');

        $viaje = Viaje::findOrFail($viajeId);

        $files = [
            'curriculum' => $viaje->curriculum,
            'trabajo' => $viaje->trabajo,
            'aceptacion' => $viaje->aceptacion,
            'invitacion' => $viaje->invitacion,
            'aval' => $viaje->aval,
            'convenioB' => $viaje->convenioB,
            'convenioC' => $viaje->convenioC,
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
        $zipFileName = 'archivos_viaje_' . $viajeId . '.zip';
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
        $solicitud = Viaje::findOrFail($id);

        $error='';
        $ok = 1;

        $errores = [];

        $today = now();
        $cierreDate = \Carbon\Carbon::parse(Constants::CIERRE_VIAJES);
        if ($today->gt($cierreDate)) {
            $errores[] ='La convocatoria no está vigente';
        }


        if (empty($solicitud->email) ) {
            $errores[] = 'Complete el campo Email en la pestaña Datos Personales';
        }
        if (empty($solicitud->nacimiento) ) {
            $errores[] = 'Complete el campo Nacimiento en la pestaña Datos Personales';
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

        if (empty($solicitud->titulo_id) || empty($solicitud->egresogrado)) {
            $errores[] = 'Complete todos los campos del Título de Grado en la pestaña Universidad';
        }
        if (
            (empty($solicitud->titulopost_id) && empty($solicitud->egresoposgrado)) ||
            (!empty($solicitud->titulopost_id) && !empty($solicitud->egresoposgrado) )
        ) {

        }else{
            $errores[] = 'Complete todos los campos del título de posgrado en la pestaña Universidad';
        }
        if (($solicitud->doctorado)&&($solicitud->egresoposgrado)) {
            $rango = intval(Constants::YEAR_VIAJES)-intval(Constants::YEAR_INGRESO_ATRAS_VIAJEES);
            $fechaDesde = Carbon::parse($solicitud->egresoposgrado);
            $fechaHasta = Carbon::parse(Constants::RANGO_INGRESO_VIAJEES.$rango);

            if ($fechaDesde->lessThanOrEqualTo($fechaHasta)) {
                $errores[] = "Doctorado anterior al ".Constants::RANGO_INGRESO_VIAJEES.$rango;
            }
        }
        $tieneCargo=0;

        if (
            (empty($solicitud->cargo_id) && empty($solicitud->deddoc) && empty($solicitud->ingreso_cargo) && empty($solicitud->facultad_id)) ||
            (!empty($solicitud->cargo_id) && !empty($solicitud->deddoc) && !empty($solicitud->ingreso_cargo) && !empty($solicitud->facultad_id) )
        ) {

        }else{

            $errores[] = 'Complete todos los campos del Cargo Docente en la pestaña Universidad';
        }
        if (!empty($solicitud->cargo_id)){
            $tieneCargo=1;
        }
        if (empty($solicitud->unidad_id)) {
            $errores[] = 'Falta seleccionar el Lugar de Trabajo en la pestaña Universidad';
        }

        if (empty($solicitud->disciplina) ) {
            $errores[] = 'Complete el campo Disciplina en la pestaña Universidad';
        }
        $esCarrera=0;
        if (
            (empty($solicitud->carrerainv_id) && empty($solicitud->organismo_id) && empty($solicitud->ingreso_carrera) && empty($solicitud->unidadcarrera_id)) ||
            (!empty($solicitud->carrerainv_id) && !empty($solicitud->organismo_id) && !empty($solicitud->ingreso_carrera) && !empty($solicitud->unidadcarrera_id) )
        ) {

        }else{

            $errores[] = 'Complete todos los campos de la Carrera de Investigación en la pestaña Investigación';
        }
        if (!empty($solicitud->carrerainv_id)){
            $esCarrera=1;
        }
        if (!empty($solicitud->ingreso_carrera) ) {
            $ingreso = $solicitud->ingreso_carrera;
            $rango = intval(Constants::YEAR_VIAJES)-intval(Constants::YEAR_INGRESO_ATRAS_VIAJEES);
            $fechaDesde = Carbon::parse($ingreso);
            $fechaHasta = Carbon::parse(Constants::RANGO_INGRESO_VIAJEES.$rango);
            $fechaCierre = Carbon::parse(Constants::CIERRE_VIAJES);

            // Verificar que el ingreso sea posterior a la fecha de cierre
            if ($fechaDesde->greaterThan($fechaCierre)) {
                $errores[] = "El ingreso a la carrera debe ser anterior al " . Carbon::parse(Constants::CIERRE_VIAJES  )->format('d/m/Y');
            }

            //Log::info('Desde: '. $fechaDesde.' hasta: '.$fechaHasta);
            if ($fechaDesde->lessThanOrEqualTo($fechaHasta)) {
                $errores[] = "Ingreso a la carrera anterior al ".Constants::RANGO_INGRESO_VIAJEES.$rango;
            }
        }
        $esBecario=0;
        $beca = $solicitud->becas()->where('actual', true)->first(); // Beca actual
        $esBecarioUNLP=0;
        if (!empty($beca)){
            $esBecario=1;
            if ($beca->unlp){
                $esBecarioUNLP=1;
            }
            if (empty($beca->institucion) || empty($beca->beca)|| empty($beca->desde)|| empty($beca->hasta) || empty($solicitud->unidadbeca_id)) {
                $errores[] = 'Complete todos los campos de la Beca Actual en la pestaña Becas';
            }
            if ($esBecario && $esCarrera) {
                $errores[] = 'No puede ser becario/a y miembro de la carrera al mismo tiempo';
            }
            if (!$tieneCargo && !$esBecarioUNLP){
                $errores[] = 'Si no posee Cargo Docente, debe ser becario/a UNLP';
            }
            $becaDesde = $beca->desde;
            $becaHasta = $beca->hasta;
            $fechaCierre = Carbon::parse(Constants::CIERRE);
            $fechaActual = Carbon::now(); // Obtener la fecha actual
            if ($becaDesde && $becaHasta) {
                $fechaDesde = Carbon::parse($becaDesde);
                $fechaHasta = Carbon::parse($becaHasta);

                if ($fechaHasta->lessThanOrEqualTo($fechaDesde)) {
                    $errores[] = "La fecha 'hasta' de la beca debe ser mayor que la fecha 'desde'.";
                }

                if ($fechaHasta->lessThan($fechaCierre)) {
                    $errores[] = "Beca no vigente";
                }
                if ($fechaDesde->greaterThan($fechaCierre)) {
                    $errores[] = "Beca no vigente";
                }

            }
        }


        if (!$esBecarioUNLP) {
            // Validar la edad si la fecha de nacimiento está presente
            if ($solicitud->nacimiento) {
                $fechaNacimiento = \Carbon\Carbon::parse($solicitud->nacimiento);
                $fechaLimiteEdad = \Carbon\Carbon::create(Constants::YEAR_VIAJES, Constants::MES_EDAD_VIAJEES, Constants::DIA_EDAD_VIAJEES);
                $edad = $fechaNacimiento->age;
                //Log::info('Nacimiento: '. $fechaNacimiento.' limite: '.$fechaLimiteEdad.' edad '.$edad);
                if ($edad >= intval(Constants::TOPE_EDAD_VIAJEES) ) {
                    $errores[] = 'Solicitante no menor a '.Constants::TOPE_EDAD_VIAJEES.' años al ' . Constants::DIA_EDAD_VIAJEES . '/' . Constants::MES_EDAD_VIAJEES . '/' . Constants::YEAR_VIAJES . ' y no Becario UNLP';

                }
            }
        }
        $esSimple=($solicitud->deddoc=='Simple')?1:0;



        if ($esSimple){
            if (!$esCarrera && !$esBecario) {
                $errores[] = "Si tiene dedicación simple, debe ser becario o tener un cargo en la carrera de investigación";
            }



            if ($solicitud->unidadbeca_id) {

                // Validación de jerarquía de unidad
                $unidadId = $solicitud->unidadbeca_id;
                $targetIds = [
                    Constants::ID_UNIDAD_UNLP, // Primer ID objetivo
                    Constants::ID_UNIDAD_UNLP_CONICET  // Segundo ID objetivo
                ];

                $unidad = Unidad::find($unidadId);

                if ($unidad) {
                    if (!$unidad->isInHierarchy($targetIds)) {
                        $errores[] = "Si tiene dedicación simple el lugar de trabajo de la beca debe ser en la UNLP.";
                    }
                }
            }

            if ($solicitud->unidadcarrera_id) {

                // Validación de jerarquía de unidad
                $unidadId = $solicitud->unidadcarrera_id;
                $targetIds = [
                    Constants::ID_UNIDAD_UNLP, // Primer ID objetivo
                    Constants::ID_UNIDAD_UNLP_CONICET  // Segundo ID objetivo
                ];

                $unidad = Unidad::find($unidadId);

                if ($unidad) {
                    if (!$unidad->isInHierarchy($targetIds)) {
                        $errores[] = "Si tiene dedicación simple el lugar de trabajo de la carrera de investigador debe ser en la UNLP.";
                    }
                }
            }


        }

        if ($solicitud->director) {
            $errores[] = "No se pueden presentar los Directores y/o Codirectores de Proyectos de Acreditación.";
        }

        $totalDiasInvestigacion = 0;
        $becas = $solicitud->becas()->get(); // todas
        // Sumar días de las becas
        if (!empty($becas)) {
            foreach ($becas as $beca) {
                if ($beca->unlp){
                    $fechaDesde = Carbon::parse($beca->desde);
                    $fechaHasta = Carbon::parse($beca->hasta);

                    // Solo suma si ambas fechas son válidas
                    if ($fechaDesde && $fechaHasta) {
                        $diasBeca = $fechaHasta->diffInDays($fechaDesde);
                        $totalDiasInvestigacion += $diasBeca;
                    }
                }

            }
        }
        $proyectos = $solicitud->proyectos()->get(); // todos
        // Sumar días de los proyectos
        if (!empty($proyectos)) {
            foreach ($proyectos as $proyecto) {
                $fechaInicio = Carbon::parse($proyecto->desde);
                $fechaFin = Carbon::parse($proyecto->hasta);

                // Solo suma si ambas fechas son válidas
                if ($fechaInicio && $fechaFin) {
                    $diasProyecto = $fechaFin->diffInDays($fechaInicio);
                    $totalDiasInvestigacion += $diasProyecto;
                }
            }
        }

        $diasYear = intval(Constants::YEAR_PROYECTOS)*intval(Constants::DIAS_YEAR);

        // Convertir el total de días en años (365 días = 1 año)
        $yearInvestigacion = $totalDiasInvestigacion / $diasYear;

        // Verificar si el investigador tiene al menos 1 año de investigación
        if ($yearInvestigacion < intval(Constants::YEAR_PROYECTOS)) {
            $errores[] = "Menos de ".intval(Constants::YEAR_PROYECTOS)." años de participación en proyectos UNLP / Beca UNLP";
        }

        // Tu lógica para calcular el monto total
        $totalMonto = 0;
        $presupuestos = $solicitud->presupuestos()->get(); // Beca actual
        foreach ($presupuestos as $presupuesto) {
            $totalMonto += $presupuesto->monto;
        }


        if (($totalMonto<=0)||($totalMonto>Constants::MONTO_VIAJES))  {

            $errores[] = 'El monto total debe ser mayor que 0 (cero) y no puede superar el límite máximo de $' . number_format(Constants::MONTO_VIAJES, 2, ',', '.');
        }



        if (empty($solicitud->facultadplanilla_id) || empty($solicitud->objetivo) || empty($solicitud->justificacion) || empty($solicitud->curriculum)) {
            $errores[] = 'Complete todos los campos de la pestaña Descripción';
        }

        if (empty($solicitud->curriculum)) {
            $errores[] = 'Falta subir el Curriculum';
        } else {
            $filePath = public_path($solicitud->curriculum);
            if (!file_exists($filePath)) {
                $errores[] = 'Hubo un error al subir el Curriculum, intente nuevamente, si el problema persiste envíenos un mail a viajes.secyt@presi.unlp.edu.ar';
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



                $year = $solicitud->periodo->nombre;

                $userId = Auth::id();
                $user = User::find($userId); // Obtener el usuario por su ID
                // Preparar datos para el correo
                $datosCorreo = [
                    'from_email' => $user->email,
                    'from_name' => $user->name,
                    'asunto' => 'Solicitud de Subsidios para Jóvenes Investigadores '.$year,
                    'year' => $year,

                    'investigador' => $solicitud->investigador->persona->apellido.', '.$solicitud->investigador->persona->nombre.' ('.$solicitud->investigador->persona->cuil.')',
                    'comment' => '',
                ];



                // Generar el PDF y obtener la ruta
                $pdfPath = $this->generatePDF(new Request(['viaje_id' => $solicitud->id]), true);

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

        return redirect()->route('viajes.index')->with($respuestaID, $respuestaMSJ);
    }



    public function enviarCorreosAlUsuario($datosCorreo, $viaje,$userId, $adjuntarArchivos, $adjuntarPlanilla)
    {

        $user = User::find($userId); // Obtener el usuario por su ID

        // Enviar correo electrónico al usuario
        Mail::to($user->email)->send(new ViajeEnviada($datosCorreo,$viaje, $adjuntarArchivos, $adjuntarPlanilla));

        // Enviar correo electrónico a tu servidor (ejemplo)
        Mail::to('marcosp@presi.unlp.edu.ar')->send(new ViajeEnviada($datosCorreo,$viaje, $adjuntarArchivos, $adjuntarPlanilla));

        // Obtener el nombre del rol correspondiente al id 4
        $roleName = Role::find(Constants::ID_ADMIN_FACULTAD_PROYECTOS)->name;

        // Obtener usuarios que pertenecen a la facultad especificada y tienen el rol con id 4
        $usuarios = User::where('facultad_id', $viaje->facultad_id)
            ->role($roleName)
            ->get();

        // Enviar correo electrónico a cada usuario
        foreach ($usuarios as $usuario) {
            Mail::to($usuario->email)->send(new ViajeEnviada($datosCorreo, $viaje, $adjuntarArchivos, $adjuntarPlanilla));
        }
    }

    public function enviarCorreos( $datosCorreo, $viaje)
    {



            $user = User::where('cuil', $viaje->investigador->persona->cuil)->first();
            if ($user) {
                // Enviar correo electrónico al usuario del director
                Mail::to($user->email)->send(new ViajeEnviada($datosCorreo, $viaje));
            }


        // Obtener el nombre del rol correspondiente al id 4
        $roleName = Role::find(Constants::ID_ADMIN_FACULTAD_PROYECTOS)->name;

        // Obtener usuarios que pertenecen a la facultad especificada y tienen el rol con id 4
        $usuarios = User::where('facultad_id', $viaje->facultadplanilla_id)
            ->role($roleName)
            ->get();

        // Enviar correo electrónico a cada usuario
        foreach ($usuarios as $usuario) {
            Mail::to($usuario->email)->send(new ViajeEnviada($datosCorreo, $viaje));
        }
    }

    public function admitir($id)
    {
        $viaje = Viaje::findOrFail($id);


        DB::beginTransaction();
        try {

            $viaje->estado = 'Admitida';

            $viaje->save();
            $investigador = Investigador::find($viaje->investigador_id);


            $this->actualizarInvestigador($viaje,$investigador);


            $this->cambiarEstado($viaje,'Solicitud admitida');


            $year = $viaje->periodo->nombre;

            // Preparar datos para el correo
            $datosCorreo = [
                'from_email' => Constants::MAIL_VIAJES,
                'from_name' => Constants::NOMBRE_VIAJES,
                'asunto' => 'Admisión de Solicitud de Subsidios para Jóvenes Investigadores '.$year,
                'year' => $year,

                'investigador' => $viaje->investigador->persona->apellido.', '.$viaje->investigador->persona->nombre.' ('.$viaje->investigador->persona->cuil.')',
                'comment' => 'La solicitud fue admitida para su evaluación',
            ];



            // Llama a la función enviarCorreos
            $this->enviarCorreos($datosCorreo, $viaje);



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
        return redirect()->route('viajes.index')->with($respuestaID, $respuestaMSJ);
    }

    public function rechazar($id)
    {
        $viaje = Viaje::find($id);




        return view('viajes.deny',compact('viaje'));

    }

    public function saveDeny(Request $request, $id)
    {
        $this->validate($request, [
            'comentarios' => 'required'
        ]);

        $input = $request->all();

        $viaje = Viaje::findOrFail($id);


        DB::beginTransaction();
        try {

            $viaje->estado = 'No Admitida';

            $viaje->save();

            $this->cambiarEstado($viaje,$input['comentarios']);



            $year = $viaje->periodo->nombre;

            // Preparar datos para el correo
            $datosCorreo = [
                'from_email' => Constants::MAIL_VIAJES,
                'from_name' => Constants::NOMBRE_VIAJES,
                'asunto' => 'NO Admisión de Solicitud de Subsidios para Jóvenes Investigadores '.$year,
                'year' => $year,

                'investigador' => $viaje->investigador->persona->apellido.', '.$viaje->investigador->persona->nombre.' ('.$viaje->investigador->persona->cuil.')',
                'comment' => '<strong>Motivos de la no admisión</strong>: '.$input['comentarios'],
            ];



            // Llama a la función enviarCorreos
            $this->enviarCorreos($datosCorreo, $viaje);


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
        return redirect()->route('viajes.index')->with($respuestaID, $respuestaMSJ);
    }

    public function actualizarInvestigador($viaje, $investigador)
    {
        if ($investigador) {
            /*$investigador->categoria_id = $integrante->categoria_id;
            $investigador->sicadi_id = $integrante->sicadi_id;
            $investigador->carrerainv_id = $integrante->carrerainv_id;
            $investigador->organismo_id = $integrante->organismo_id;
            $investigador->facultad_id = $integrante->facultad_id;
            $investigador->cargo_id = $integrante->cargo_id;
            $investigador->deddoc = $integrante->deddoc;
            $investigador->universidad_id = $integrante->universidad_id;*/
            $investigador->titulo_id = $viaje->titulo_id;
            $investigador->titulopost_id = $viaje->titulopost_id;
            $investigador->unidad_id = $viaje->unidad_id;
            $beca = $viaje->becas()->where('actual', true)->first(); // Beca actual
            if (!empty($beca)){
                $investigador->institucion = $beca->institucion;
                $investigador->beca = $beca->beca;
            }
            else{
                $investigador->institucion = null;
                $investigador->beca = null;
            }

           /* $investigador->materias = $integrante->materias;
            $investigador->total = $integrante->total;
            $investigador->carrera = $integrante->carrera;*/

            $investigador->save();
        }
        $persona = $investigador->persona;  // Obtener la persona asociada

        // Actualizar los datos de la persona aquí
        if ($persona) {

            $persona->email = $viaje->email;
            $persona->nacimiento = $viaje->nacimiento;
            $persona->telefono = $viaje->telefono;
            $persona->calle = $viaje->calle;
            $persona->nro = $viaje->nro;
            $persona->piso = $viaje->piso;
            $persona->cp = $viaje->cp;
            $persona->depto = $viaje->depto;
            $persona->save();
        }
        if (!empty($viaje->titulo_id)) {


            // Verificar si el título ya está asociado
            $tituloId = $viaje->titulo_id;
            $egresoGrado = $viaje->egresogrado;

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
        if (!empty($viaje->titulopost_id)) {

            // Verificar si el título ya está asociado
            $tituloId = $viaje->titulopost_id;
            $egresoPosgrado = $viaje->egresoposgrado;

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







        if (!empty($viaje->carrerainv_id)) {

            // Datos de la nueva carrera
            $nuevaCarrera = [
                'carrerainv_id' => $viaje->carrerainv_id,
                'organismo_id' => $viaje->organismo_id,
                'ingreso' => $viaje->ingreso_carrerainv,
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

        $becas = $viaje->becas()->get(); // todas

        if (!empty($becas)) {


            // Obtener los IDs e instituciones de las becas existentes del investigador
            $existingBecas = $investigador->becas->map(function($beca) {
                return [
                    'beca' => $beca->beca,
                    'institucion' => $beca->institucion,
                    'desde' => $beca->desde,
                    'hasta' => $beca->hasta,
                ];
            })->toArray();

            foreach ($becas as $beca){
                $existingBeca = collect($existingBecas)->first(function ($existingBeca) use ($beca) {
                    return $existingBeca['beca'] == $beca->beca && $existingBeca['institucion'] == $beca->institucion;
                });

                if ($existingBeca) {
                    // Si la beca existe, verificar si las fechas 'desde' y 'hasta' son distintas
                    if ($existingBeca['desde'] != $beca->desde || $existingBeca['hasta'] != $beca->hasta) {
                        // Actualizar las fechas en la beca existente
                        DB::table('investigador_becas')
                            ->where('investigador_id', $investigador->id)
                            ->where('beca', $beca->beca)
                            ->where('institucion', $beca->institucion)
                            ->update([
                                'desde' => $beca->desde,
                                'hasta' => $beca->hasta,
                                'updated_at' => now(),
                            ]);

                        Log::info("Fechas de Beca Actualizadas: " . $beca->beca . " - Institución: " . $beca->institucion);
                    } else {
                        Log::info("La beca ya existe y las fechas son las mismas: " . $beca->beca . " - Institución: " . $beca->institucion);
                    }
                } else {
                    // Si la beca no existe, insertarla en la tabla 'investigador_becas'
                    DB::table('investigador_becas')->insert([
                        'investigador_id' => $investigador->id,
                        'beca' => $beca->beca,
                        'institucion' => $beca->institucion,
                        'desde' => $beca->desde,
                        'hasta' => $beca->hasta,
                        'unlp' => 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    Log::info("Nueva Beca Insertada: " . $beca->beca . " - Institución: " . $beca->institucion);
                }
            }
// Verificar si la nueva beca ya existe en las becas del investigador



        }
    }

}