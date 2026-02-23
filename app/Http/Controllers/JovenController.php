<?php

namespace App\Http\Controllers;

use App\Constants;
use App\Models\Carrerainv;
use App\Models\Categoria;
use App\Models\Integrante;
use App\Models\JovenEstado;
use App\Models\Investigador;
use App\Models\Persona;
use App\Models\Proyecto;
use App\Models\Sicadi;
use App\Models\User;
use App\Traits\SanitizesInput;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Models\Joven;
use App\Models\Universidad;
use App\Models\Unidad;
use App\Models\Titulo;
use App\Models\Cargo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use ZipArchive;

use PDF;
use Illuminate\Support\Facades\Mail;
use App\Mail\JovenEnviada;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
class JovenController extends Controller

{
    use SanitizesInput;
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

        //->where('nombre', Constants::YEAR_JOVENES)

        $periodos = DB::table('periodos')->orderBy('nombre','DESC')->get();


        $periodo = DB::table('periodos')->where('nombre', Constants::YEAR_JOVENES)->first();
        if (empty($periodo)) {
            $id = DB::table('periodos')->insertGetId([
                'nombre' => Constants::YEAR_JOVENES,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $id = $periodo->id; // Si el registro ya existe, obtén el ID existente
        }
        $currentPeriodo = ($request->session()->get('periodo_filtro_joven'))?$request->session()->get('periodo_filtro_joven'):$id;
        $facultades = DB::table('facultads')->pluck('nombre', 'id')->prepend('Todas','-1');
        //dd($currentPeriodo);
        // Pasar los períodos y la constante a la vista
        return view('jovens.index', [
            'periodos' => $periodos,
            'facultades' => $facultades,
            'currentPeriod' =>  $currentPeriodo// Asume que la constante es un ID o valor relevante
        ]);
    }



    public function dataTable(Request $request)
    {
        $columnas = ['personas.nombre','periodos.nombre', 'personas.apellido', 'jovens.fecha','jovens.estado', 'facultads.cat', 'facultads.nombre', '','jovens.diferencia', 'jovens.puntaje']; // Define las columnas disponibles
        $columnaOrden = $columnas[$request->input('order.0.column')];
        $orden = $request->input('order.0.dir');
        $busqueda = $request->input('search.value');
        $periodo = $request->input('periodo'); // Obtener el filtro de período de la solicitud
        $estado = $request->input('estado');
        $area = $request->input('area');
        $facultadplanilla = $request->input('facultadplanilla');

        // Consulta base
        $query = Joven::select('jovens.id as id', 'personas.nombre as persona_nombre', 'periodos.nombre as periodo_nombre', DB::raw("CONCAT(personas.apellido, ', ', personas.nombre) as persona_apellido"),'jovens.fecha as fecha','jovens.estado as estado', 'facultads.cat as facultad_cat', 'facultads.nombre as facultad_nombre','jovens.diferencia','jovens.puntaje')
            ->leftJoin('periodos', 'jovens.periodo_id', '=', 'periodos.id')
            ->leftJoin('investigadors', 'jovens.investigador_id', '=', 'investigadors.id')
            ->leftJoin('personas', 'investigadors.persona_id', '=', 'personas.id')
            ->leftJoin('facultads', 'jovens.facultadplanilla_id', '=', 'facultads.id')
            ->with(['evaluacions' => function($query) {
                // Añadir select específico de los campos que deseas de los evaluadores
                $query->select('joven_evaluacions.joven_id', 'user_name', 'user_id', 'interno', 'joven_evaluacions.estado', 'joven_evaluacions.puntaje')
                    ->with('user:id,name'); // Carga el usuario solo si el user_id no es null
            }]);



        if (!empty($periodo) && $periodo != '-1') {
            $query->where('jovens.periodo_id', $periodo);
        }

        if (!empty($estado) && $estado != '-1') {
            $query->where('jovens.estado', $estado);
        }

        if (!empty($area) && $area != '-1') {
            $query->where('facultads.cat', $area);
        }

        if (!empty($facultadplanilla) && $facultadplanilla != '-1') {
            $query->where('facultads.id', $facultadplanilla);
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
                $query->where('jovens.facultadplanilla_id', '=', $user->facultad_id);
            });
        }

        // Solo para evaluadores
        if ($selectedRoleId == 6) {
            $user = auth()->user();

            $query->where(function ($query) use ($user) {
                $query->whereHas('evaluacions', function ($q) use ($user) {
                    $q->where('user_cuil', '=', $user->cuil)
                        ->orWhere('user_id', '=', $user->id);
                })->orWhere('jovens.investigador_id', '=', $user->id);
            })
                ->leftJoin('joven_evaluacions', 'jovens.id', '=', 'joven_evaluacions.joven_id')
                ->addSelect('joven_evaluacions.estado as evaluacion_estado')
                ->where(function ($query) use ($user) {
                    $query->where('joven_evaluacions.user_cuil', '=', $user->cuil)
                        ->orWhere('joven_evaluacions.user_id', '=', $user->id);
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

        // Formatear el resultado agrupando evaluadores por joven
        $datosFormateados = $datos->map(function ($joven) {
            // Obtener el estado y puntaje de los evaluadores
            $evaluadores = $joven->evaluacions->map(function ($evaluacion) {
                $user_name = $evaluacion->user_id ? $evaluacion->user->name : $evaluacion->user_name;
                $interno = ($evaluacion->interno) ? 'Interno':'Externo';
                return  $user_name. ' / ' .$interno. ' / ' . $evaluacion->estado . ' / P. ' . number_format($evaluacion->puntaje, 2, ',', '.');
            })->toArray(); // Convertir a un array
            return [
                'id' => $joven->id,
                'persona_nombre' => $joven->persona_nombre,
                'periodo_nombre' => $joven->periodo_nombre,
                'persona_apellido' => $joven->persona_apellido,
                'fecha' => $joven->fecha,
                'estado' => $joven->estado,
                'facultad_cat' => $joven->facultad_cat,
                'facultad_nombre' => $joven->facultad_nombre,
                'diferencia' => $joven->diferencia,
                'puntaje' => $joven->puntaje,
                'evaluadores' => $evaluadores, // Guardar la lista de evaluadores formateada
                'evaluacion_estado' => $joven->evaluacion_estado, // Agregar el estado de la evaluación
            ];
        });
        //dd($datosFormateados);
        // Obtener la cantidad total de registros sin filtrar
        $recordsTotal = Joven::count();

        return response()->json([
            'data' => $datosFormateados,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'draw' => $request->draw,
        ]);
    }


    public function exportar(Request $request)
    {
        $columnas = ['personas.nombre','periodos.nombre', 'personas.apellido', 'jovens.fecha','jovens.estado', 'facultads.cat', 'facultads.nombre', '','jovens.diferencia', 'jovens.puntaje']; // Define las columnas disponibles
        // La lógica de exportación a Excel va aquí
        $filtros = $request->all();

        $query = Joven::select('jovens.id as id', 'personas.nombre as persona_nombre', 'periodos.nombre as periodo_nombre', DB::raw("CONCAT(personas.apellido, ', ', personas.nombre) as persona_apellido"),'jovens.fecha as fecha','jovens.estado as estado', 'facultads.cat as facultad_cat', 'facultads.nombre as facultad_nombre','jovens.diferencia','jovens.puntaje','personas.cuil','jovens.email','jovens.nacimiento','jovens.disciplina')
            ->leftJoin('periodos', 'jovens.periodo_id', '=', 'periodos.id')
            ->leftJoin('investigadors', 'jovens.investigador_id', '=', 'investigadors.id')
            ->leftJoin('personas', 'investigadors.persona_id', '=', 'personas.id')
            ->leftJoin('facultads', 'jovens.facultadplanilla_id', '=', 'facultads.id')
            ->with(['evaluacions' => function($query) {
                // Añadir select específico de los campos que deseas de los evaluadores
                $query->select('joven_evaluacions.joven_id', 'user_name', 'user_id', 'interno', 'joven_evaluacions.estado', 'joven_evaluacions.puntaje')
                    ->with('user:id,name'); // Carga el usuario solo si el user_id no es null
            }]);



        if (!empty($filtros['periodo'])&&$filtros['periodo']!=-1) {
            $query->where('jovens.periodo_id', $filtros['periodo']);
        }
        if (!empty($filtros['estado'])&&$filtros['estado']!=-1) {
            $query->where('jovens.estado', $filtros['estado']);
        }
        if (!empty($filtros['area'])&&$filtros['area']!=-1) {
            $query->where('facultads.cat', $filtros['area']);
        }
        if (!empty($filtros['facultadplanilla'])&&$filtros['facultadplanilla']!=-1) {
            $query->where('facultads.id', $filtros['facultadplanilla']);
        }

        if (!empty($filtros['busqueda'])) {


            $request->session()->put('nombre_filtro_joven', $filtros['busqueda']);

        }
        else{
            $busqueda = $request->session()->get('nombre_filtro_joven');

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
            $fechaLimiteEdad = \Carbon\Carbon::parse(Constants::YEAR_JOVENES.'-'.Constants::MES_EDAD_JOVENES.'-'.Constants::DIA_EDAD_JOVENES);
            $nacimiento = Carbon::parse($item->nacimiento);
            $joven = Joven::find($item->id);
            $proyectos = $joven->proyectos()->get(); // todos
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
                    if ($proyecto->proyecto_id) {
                        $proy = Proyecto::find($proyecto->proyecto_id);

                        $disciplina = $proy->disciplina(); // puede devolver null
                        $especialidadObj = $proy->especialidad(); // también puede devolver null

                        $especialidad = '';

                        if ($disciplina) {
                            $especialidad = $disciplina->nombre;
                        }

                        if ($especialidadObj) {
                            $especialidad .= ' - ' . $especialidadObj->nombre;
                        }
                    }

                    $strProyectos .= $proyecto->codigo.' DIR: '.$proyecto->director.$cordir.' ('.date('d/m/Y', strtotime($proyecto->desde)) .'-'.date('d/m/Y', strtotime($proyecto->hasta)).') Especialidad: '.$especialidad.'---';
                }
            }
            $evaluacions = $joven->evaluacions()->get(); // todos
            $strEvaluacions = '';
            if (!empty($evaluacions)) {
                foreach ($evaluacions as $evaluacion) {
                    $strInterno = ($evaluacion->interno)?'Interno':'Externo';
                    $evaluador = ($evaluacion->user_id)?$evaluacion->user->name:$evaluacion->user_name;
                    $strEvaluacions .= $evaluador.' / '.$strInterno.' / '.$evaluacion->estado.' / P. '.number_format ( $evaluacion->puntaje , 2 , ',', '.' ).'---';
                }
            }
            $fecha = \Carbon\Carbon::parse($item->fecha);
            $sheet->setCellValue('A' . $row, $item->periodo_nombre);
            $sheet->setCellValue('B' . $row, $item->persona_apellido);
            $sheet->setCellValue('C' . $row, $item->cuil);
            $sheet->setCellValue('D' . $row, $nacimiento->diffInYears($fechaLimiteEdad));
            $sheet->setCellValue('E' . $row, $item->email);
            $sheet->setCellValue('F' . $row, $fecha->format('d/m/Y'));
            $sheet->setCellValue('G' . $row, $item->estado);
            $sheet->setCellValue('H' . $row, $item->facultad_cat);
            $sheet->setCellValue('I' . $row, $item->facultad_nombre);
            $sheet->setCellValue('J' . $row, $item->disciplina);
            $sheet->setCellValue('K' . $row, $strProyectos);
            $sheet->setCellValue('L' . $row, '$' . number_format($joven->presupuestos()->sum('monto'), 2, ',', '.'));
            $sheet->setCellValue('M' . $row, $strEvaluacions);
            $sheet->setCellValue('N' . $row, $item->diferencia);
            $sheet->setCellValue('O' . $row, $item->puntaje);
            $row++;
        }


        // Guardar el archivo
        $writer = new Xlsx($spreadsheet);
        $filename = 'jovenes_'.date('YmdHis').'.xlsx';

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
        $cierreDate = \Carbon\Carbon::parse(Constants::CIERRE_JOVENES);
        if ($today->gt($cierreDate)) {

            return redirect()->route('jovens.index')->withErrors(['message' => 'La convocatoria no está vigente.']);
        }

        $titulos=Titulo::where('nivel', 'Grado')->orderBy('nombre','ASC')->get();
        $titulos = $titulos->pluck('full_name', 'id')->prepend('','');
        $tituloposts=Titulo::where('nivel', 'Posgrado')->orderBy('nombre','ASC')->get();
        $tituloposts = $tituloposts->pluck('full_name', 'id')->prepend('','');
        $facultades = DB::table('facultads')->pluck('nombre', 'id')->prepend('','');// Obtener todas las facultades directamente desde la tabla
        // Obtener los cargos ordenados por el campo 'orden' y seleccionar solo los campos 'id' y 'nombre'
        //$cargos = Cargo::orderBy('orden')->pluck('nombre', 'id')->prepend('', '');
        $idsExcluir = [6, 12, 13, 14];

        $cargos = Cargo::whereNotIn('id', $idsExcluir)
            ->orderBy('orden')
            ->pluck('nombre', 'id')
            ->prepend('', '');
        $universidades=Universidad::orderBy('nombre','ASC')->get();
        $universidades = $universidades->pluck('nombre', 'id')->prepend('','');
        $unidads=Unidad::orderBy('nombre','ASC')->get();
        $unidads->each->append('path_to_parent');
        $unidads = $unidads->pluck('path_to_parent', 'id')->prepend('','');
        $carrerainvs = Carrerainv::where('activo','1')->orderBy('orden')->pluck('nombre', 'id')->prepend('', '');
        $organismos = DB::table('organismos')->where('activo','1')->pluck('codigo', 'id')->prepend('','');
        $tipoPresupuestos = DB::table('tipo_presupuestos')->where('activo', true)->get();
        //dd($tipoPresupuestos);
        $currentYear = date('Y');
        $startYear = 1994;
        $years = range($currentYear, $startYear);
        $years = array_combine($years, $years); // Esto crea un array asociativo con los años como claves y valores
        /*$categorias = Categoria::orderBy('id')->pluck('nombre', 'id')->prepend('', '');

        $sicadis = Sicadi::orderBy('id')->pluck('nombre', 'id')->prepend('', '');*/

        $user = auth()->user();

        $cuil = $user->cuil;

        $investigador = Investigador::whereHas('persona', function ($query) use ($cuil) {
            $query->where('cuil', '=', $cuil);
        })
            ->first(); // Tomar el primer investigador encontrado

//dd($proyectosAnteriores);
        // Comprobar si no se encontró un investigador
        if (!$investigador) {
            return redirect()->route('jovens.index')->withErrors(['message' => 'No se encontró un investigador asociado al CUIL del usuario. Comuníquese al correo viajes.secyt@presi.unlp.edu.ar']);
        }

        $fueDirector = Integrante::where('investigador_id', $investigador->id)
            ->where(function ($query) {
                $query->where('tipo', 'Director')
                    ->orWhere('tipo', 'Codirector');


            })
            ->whereHas('proyecto', function ($query)  {
                $query->where('estado', 'Acreditado');
            })
            ->get();
        //dd($fueDirector);
        if (!$fueDirector->isEmpty()){
            return redirect()->route('jovens.index')->withErrors(['message' => 'No se pueden presentar los Directores y/o Codirectores de Proyectos de Acreditación.']);
        }

        $noRendidas = DB::table('joven_no_rendidas')->where('documento', $investigador->persona->documento)->get();
        $errores = [];

        foreach ($noRendidas as $noRendida) {
            $errores[] = 'Ud. no ha rendido la solicitud del año ' . $noRendida->year;
        }

        $currentPeriodo = Constants::YEAR_VIAJES;

        // Obtener los años en los que el investigador tuvo solicitudes otorgadas en los últimos dos años
        $noRendidas = Joven::where('investigador_id', $investigador->id)
            ->where('estado', 'Otorgada')
            ->join('periodos', 'jovens.periodo_id', '=', 'periodos.id')
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
            return redirect()->route('jovens.index')->withErrors($errores);
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
        }



        $integrantes = Integrante::where('investigador_id', $investigador->id)
            ->where(function ($query) {
                // Condiciones para la alta y baja
                $query->whereColumn('integrantes.alta', '!=', 'integrantes.baja')
                    ->orWhereNull('integrantes.alta')
                    ->orWhereNull('integrantes.baja');
            })
            ->whereHas('proyecto', function ($query) {
                $query->where('estado', 'Acreditado')
                    ->where(function ($q) {
                        // Proyectos anteriores o proyectos actuales con baja anterior
                        $q->where('fin', '<=', Carbon::now()->format('Y-m-d')) // Proyectos finalizados
                        ->orWhere(function ($subQuery) {
                            $subQuery->where('fin', '>', Carbon::now()->format('Y-m-d')) // Proyectos actuales
                            ->where('integrantes.baja', '<=', Carbon::now()->format('Y-m-d')); // Con baja anterior o igual a hoy
                        });
                    });
            })
            ->get();





        //dd($integrantes);
        $proyectosAnteriores = array();
        foreach ($integrantes as $integrante){
            $proyectoAnterior = array();
            $proyecto = Proyecto::findOrFail($integrante->proyecto_id);
            //dd($proyecto);
            $directorQuery = Integrante::select(DB::raw("CONCAT(personas.apellido, ', ', personas.nombre) as director_apellido"))
                ->leftJoin('investigadors', 'integrantes.investigador_id', '=', 'investigadors.id')
                ->leftJoin('proyectos', 'integrantes.proyecto_id', '=', 'proyectos.id')
                ->leftJoin('personas', 'investigadors.persona_id', '=', 'personas.id');
            $directorQuery->where('integrantes.tipo', 'Director');
            $directorQuery->where('integrantes.proyecto_id', $integrante->proyecto_id);
            $director = $directorQuery->first();
            $proyectoAnterior['id'] = $proyecto->id;
            $proyectoAnterior['codigo'] = $proyecto->codigo;
            $proyectoAnterior['titulo'] = $proyecto->titulo;
            $proyectoAnterior['director'] = $director->director_apellido;
            $proyectoAnterior['inicio'] = $integrante->alta;
            $proyectoAnterior['fin'] = (($integrante->baja)&&($integrante->baja!='0000-00-00'))?$integrante->baja:$proyecto->fin;
            //$proyectoActual['estado'] = $proyecto->estado;
            //dd($proyectoActual);
            $proyectosAnteriores[]=$proyectoAnterior;
        }



        // Buscar el primer título, cargo actual, etc.
        $titulo = $investigador->titulos->first(); // Primer título
        $titulopost = $investigador->tituloposts->first(); // Primer título de posgrado
        $cargo = $investigador->cargos()->where('activo', true)->orderBy('deddoc', 'asc')->orderBy('orden', 'asc')->first(); // Cargo actual
        $carrerainv = $investigador->carrerainvs()->where('actual', true)->first(); // Carrera actual
        /*$sicadi = $investigador->sicadis()->where('actual', true)->first(); // Sicadi actual
        $categoria = $investigador->categorias()->where('actual', true)->first(); // Categoría actual*/
        $hoy = Carbon::today();
        $beca = $investigador->becas()->where('desde', '<=',$hoy)->where('hasta', '>=',$hoy)->first(); // Beca actual
        $becas = $investigador->becas()->where('hasta', '<', $hoy)->get(); // Becas anteriores
        if (!$tieneProyecto){
            if ((is_null($beca))||(!$beca->unlp)){
                return redirect()->route('jovens.index')->withErrors(['message' => 'Debe contar al menos con un proyecto en ejecución o ser becario UNLP.']);
            }
        }

        $periodo = DB::table('periodos')->where('nombre', Constants::YEAR_JOVENES)->first();


        // Verificar si ya existe una solicitud para el período actual
        $existeSolicitud = Joven::where('investigador_id', $investigador->id)
            ->where('periodo_id', $periodo->id) // Suponiendo que tienes un campo 'periodo_id' en la tabla de solicitudes
            ->exists();

        if ($existeSolicitud) {
            return redirect()->route('jovens.index')->withErrors(['message' => 'Ya existe una solicitud para este período.']);
        }

        return view('jovens.create',compact('titulos','tituloposts','facultades','cargos','universidades','unidads','carrerainvs','years','organismos','investigador','titulo','titulopost','cargo','carrerainv','beca','becas','proyectosActuales','proyectosAnteriores','tipoPresupuestos','periodo'));
    }



    private function validateRequest(Request $request)
    {
        // Definir las reglas de validación
        $rules = [
            'email' => 'nullable|email',
            'curriculum' => 'file|mimes:pdf,doc,docx|max:4048',
            'nacimiento' => 'nullable|date',
            'egresos.*' => 'nullable|date_format:Y-m-d', // Validación del formato de fecha
            'egresoposts.*' => 'nullable|date_format:Y-m-d', // Validación del formato de fecha
            'ingresos.*' => 'nullable|date_format:Y-m-d', // Validación del formato de fecha
            'carringresos.*' => 'nullable|date_format:Y-m-d', // Validación del formato de fecha
            'becadesdeActual' => 'nullable|date',
            'becahastaActual' => 'nullable|date',
            'becadesdes.*' => 'nullable|date_format:Y-m-d', // Validación del formato de fecha
            'becahastas.*' => 'nullable|date_format:Y-m-d', // Validación del formato de fecha
            'inicioAnterior.*' => 'nullable|date_format:Y-m-d', // Validación del formato de fecha
            'finAnterior.*' => 'nullable|date_format:Y-m-d', // Validación del formato de fecha

        ];

        // Definir los mensajes de error personalizados
        $messages = [
            'curriculum.mimes' => 'El archivo de curriculum debe ser un documento de tipo: pdf, doc, docx.',
            'curriculum.max' => 'El archivo de curriculum no debe ser mayor a 4 MB.',
            'nacimiento.date' => 'La fecha de nacimiento debe ser una fecha válida.',
            'egresos.*.date_format' => 'Fecha inválida en el título de grado',
            'egresoposts.*.date_format' => 'Fecha inválida en el título de posgrado',
            'ingresos.*.date_format' => 'Fecha inválida en el ingreso al cargo docente',
            'carringresos.*.date_format' => 'Fecha inválida en el ingreso a la carrera de investigación',
            'becadesdeActual.date' => 'Fecha desde inválida en la beca actual',
            'becahastaActual.date' => 'Fecha hasta inválida en la beca actual',
            'becadesdes.*.date_format' => 'Fecha desde inválida en una de las becas anteiores',
            'becahastas.*.date_format' => 'Fecha desde inválida en una de las becas anteiores',
            'inicioAnterior.*.date_format' => 'Fecha incio inválida en un de los proyectos anteriores',
            'finAnterior.*.date_format' => 'Fecha fin inválida en un de los proyectos anteriores',
        ];

        // Crear el validador con las reglas y mensajes
        $validator = Validator::make($request->all(), $rules, $messages);

        // Añadir la validación personalizada para la fecha de cierre
        $validator->after(function ($validator) use ($request) {
            $today = now();
            $cierreDate = \Carbon\Carbon::parse(Constants::CIERRE_JOVENES);
            if ($today->gt($cierreDate)) {
                $validator->errors()->add('convocatoria', 'La convocatoria no está vigente.');
            }

            // Validar la edad solo si 'unlpActual' no es 1
            if ($request->input('unlpActual') != 1) {
                // Validar la edad si la fecha de nacimiento está presente
                if ($request->has('nacimiento')) {
                    $fechaNacimiento = \Carbon\Carbon::parse($request->input('nacimiento'));
                    $fechaLimiteEdad = \Carbon\Carbon::create(Constants::YEAR_JOVENES, Constants::MES_EDAD_JOVENES, Constants::DIA_EDAD_JOVENES);
                    $edad = $fechaNacimiento->age;
                //Log::info('Nacimiento: '. $fechaNacimiento.' limite: '.$fechaLimiteEdad.' edad '.$edad);
                    if ($edad >= intval(Constants::TOPE_EDAD_JOVENES) ) {

                        $validator->errors()->add('fecha_nacimiento', 'Solicitante no menor a '.Constants::TOPE_EDAD_JOVENES.' años al ' . Constants::DIA_EDAD_JOVENES . '/' . Constants::MES_EDAD_JOVENES . '/' . Constants::YEAR_JOVENES . ' y no Becario UNLP');
                    }
                }
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
        $becaDesde = $request->input('becadesdeActual');
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

        }

        // Validar becas agregadas
        $becasAgregadasDesde = $request->input('becadesdes', []); // Becas agregadas (desde)
        $becasAgregadasHasta = $request->input('becahastas', []); // Becas agregadas (hasta)

        foreach ($becasAgregadasDesde as $index => $becaDesdeAgregada) {
            $becaHastaAgregada = $becasAgregadasHasta[$index] ?? null;

            if ($becaDesdeAgregada && $becaHastaAgregada) {
                $fechaDesdeAgregada = Carbon::parse($becaDesdeAgregada);
                $fechaHastaAgregada = Carbon::parse($becaHastaAgregada);

                // Validar que 'hasta' sea mayor que 'desde'
                if ($fechaHastaAgregada->lessThanOrEqualTo($fechaDesdeAgregada)) {
                    $errores[] = "La fecha 'hasta' de la becas agregadas debe ser mayor que la fecha 'desde'.";
                }

                // Validar que la beca no sea vigente (la fecha 'hasta' debe ser anterior a la fecha actual)
                if ($fechaHastaAgregada->greaterThan($fechaActual)) {
                    $errores[] = "Beca anterior no válida porque aún está vigente.";
                }
            }
        }


// Obtener el array de valores del checkbox

        $doctorado = isset($request->doctorados[0]) ? 1 : 0;
        // Verificar si el array contiene el valor esperado
        if ($doctorado) {

            if (!empty($request->egresoposts)) {

                $egresoPosgrado = $request->egresoposts[0];
                $rango = intval(Constants::YEAR_JOVENES)-intval(Constants::YEAR_INGRESO_ATRAS_JOVENES);
                $fechaDesde = Carbon::parse($egresoPosgrado);
                $fechaHasta = Carbon::parse(Constants::RANGO_INGRESO_JOVENES.$rango);

                //Log::info('Doctorado Desde: '. $fechaDesde.' hasta: '.$fechaHasta);
                if ($fechaDesde->lessThanOrEqualTo($fechaHasta)) {
                    $errores[] = "Doctorado anterior al ".Constants::RANGO_INGRESO_JOVENES.$rango;
                }

            }

        }

        if (!empty($request->carringresos[0])) {

            $ingreso = $request->carringresos[0];
            $rango = intval(Constants::YEAR_JOVENES)-intval(Constants::YEAR_INGRESO_ATRAS_JOVENES);
            $fechaDesde = Carbon::parse($ingreso);
            $fechaHasta = Carbon::parse(Constants::RANGO_INGRESO_JOVENES.$rango);
            $fechaCierre = Carbon::parse(Constants::CIERRE_JOVENES);

            // Verificar que el ingreso sea posterior a la fecha de cierre
            if ($fechaDesde->greaterThan($fechaCierre)) {
                $errores[] = "El ingreso a la carrera debe ser anterior al " . Carbon::parse(Constants::CIERRE_JOVENES  )->format('d/m/Y');
            }

            //Log::info('Desde: '. $fechaDesde.' hasta: '.$fechaHasta);
            if ($fechaDesde->lessThanOrEqualTo($fechaHasta)) {
                $errores[] = "Ingreso a la carrera anterior al ".Constants::RANGO_INGRESO_JOVENES.$rango;
            }

        }

        // Obtener el valor del checkbox
        $director = $request->input('director', null);

        // Verificar si el checkbox fue marcado
        if ($director) {
            $errores[] = "No se pueden presentar los Directores y/o Codirectores de Proyectos de Acreditación.";
        }


            // Recuperar las entradas del formulario
        $codigos = $request->input('codigoAnterior', []);
        $titulos = $request->input('tituloAnterior', []);
        $directores = $request->input('directorAnterior', []);
        $inicios = $request->input('inicioAnterior', []);
        $fins = $request->input('finAnterior', []);

        // Fecha límite 31/12/2009
        $fechaLimite = Carbon::create(2009, 12, 31);
        //Log::info('Proyectos:', $codigos);
        // Verificar solo si se ha agregado alguna fila
        if (is_array($codigos) && count($codigos) > 0) {
            // Recorrer cada fila y verificar que los campos estén completos y que la fecha fin sea válida
            for ($i = 0; $i < count($codigos); $i++) {

                if (empty($codigos[$i]) || empty($titulos[$i]) || empty($directores[$i]) || empty($inicios[$i]) || empty($fins[$i])) {
                    $errores[] = "Si agrega un proyecto debe completar todos los campos";
                }

                // Verificar la fecha de fin
                $fechaFin = Carbon::parse($fins[$i]);
                if ($fechaFin->greaterThan($fechaLimite)) {
                    $errores[] = "Solo se pueden agregar proyectos que finalizaron antes del 31/12/2009.";
                }
            }
        }

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
        if ($totalMonto > Constants::MONTO_JOVENES) {
            $errores[] = 'El monto total no puede superar el límite máximo de $' . number_format(Constants::MONTO_JOVENES, 2, ',', '.');
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

        $input = $this->sanitizeInput($request->all());
        // Asegurarse de que los checkbox tienen valor 0 si no se enviaron
        $input['doctorado'] = isset($request->doctorados[0]) ? 1 : 0;
        $input['director'] = isset($request->director) ? 1 : 0;
        $input['notificacion'] = isset($request->notificacion) ? 1 : 0;

        $input['fecha']=Carbon::now();

        $user = auth()->user();

        $cuil = $user->cuil;
        // Crear la carpeta si no existe
        /*$destinationPath = public_path('/files/jovenes/' . Constants::YEAR_JOVENES.'/'.$cuil);
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0777, true);
        }*/

        //$input['alta']= Constants::YEAR.'-01-01';
        $input['estado']= 'Creada';
        // Manejo de archivos
        $input['curriculum'] ='';
        /*if ($files = $request->file('curriculum')) {
            $file = $request->file('curriculum');
            $name = 'CV_'.time().'.'.$file->getClientOriginalExtension();

            $file->move($destinationPath, $name);
            $input['curriculum'] = "files/jovenes".Constants::YEAR_JOVENES."/$cuil/$name";
        }*/
        if ($request->hasFile('curriculum')) {
            $file = $request->file('curriculum');
            $filename = 'CV_' . Str::uuid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('public/files/jovenes/' . Constants::YEAR_JOVENES.'/'.$cuil, $filename);
            $input['curriculum'] = Storage::url($path); // Genera URL tipo /storage/files/...
        }

        DB::beginTransaction();
        $ok = 1;

        try {



            $solicitud = Joven::create($input);

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

        return redirect()->route('jovens.index')->with($respuestaID, $respuestaMSJ);
    }

    private function safeRequest($request, $key, $default = null)
    {
        if (!isset($request->$key[0])) {
            return $default;
        }

        return $this->sanitizeInput($request->$key[0]);
    }


    public function guardarSolicitud(Request $request, $solicitud,$actualizar=false)
    {
// Guardar el primer título pasado en $request->titulo en la columna titulo_id del investigador

        $titulos = collect($request->titulos)
            ->filter()
            ->values();

        if ($titulos->isNotEmpty()) {

            $solicitud->titulo_id = $this->safeRequest($request, 'titulos');
            $solicitud->egresogrado = $this->safeRequest($request, 'egresos');


        }
        else {
            // 👇 SI LO BORRÓ, LIMPIAR
            $solicitud->titulo_id = null;
            $solicitud->egresogrado = null;
        }



        $tituloposts = collect($request->tituloposts)
            ->filter()
            ->values();

        if ($tituloposts->isNotEmpty()) {
            $solicitud->titulopost_id = $this->safeRequest($request, 'tituloposts');
            $solicitud->egresoposgrado = $this->safeRequest($request, 'egresoposts');

        }
        else {
            // 👇 SI LO BORRÓ, LIMPIAR
            $solicitud->titulopost_id = null;
            $solicitud->egresoposgrado = null;
        }


        $cargos = collect($request->cargos)
            ->filter()
            ->values();

        if ($cargos->isNotEmpty()) {
            $solicitud->cargo_id = $this->safeRequest($request, 'cargos');
            $solicitud->deddoc = $this->safeRequest($request, 'deddocs');
            $solicitud->ingreso_cargo = $this->safeRequest($request, 'ingresos');
            $solicitud->facultad_id = $this->safeRequest($request, 'facultads');

        }
        else {
            // 👇 SI LO BORRÓ, LIMPIAR
            $solicitud->cargo_id = null;
            $solicitud->deddoc = null;
            $solicitud->ingreso_cargo = null;
            $solicitud->facultad_id = null;
        }

        $carrerainvs = collect($request->carrerainvs)
            ->filter()
            ->values();

        if ($carrerainvs->isNotEmpty()) {
            $solicitud->carrerainv_id = $this->safeRequest($request, 'carrerainvs');
            $solicitud->organismo_id = $this->safeRequest($request, 'organismos');
            $solicitud->ingreso_carrerainv = $this->safeRequest($request, 'carringresos');

        }
        else {
            // 👇 SI LO BORRÓ, LIMPIAR
            $solicitud->carrerainv_id = null;;
            $solicitud->organismo_id = null;;
            $solicitud->ingreso_carrerainv = null;;
        }


        $solicitud->save();
        if ($request->institucionActual) {

            if (!empty($request->idBecaActual)) {
                // Si hay un idBecaActual, actualiza el registro correspondiente
                DB::table('joven_becas')
                    ->where('id', $request->idBecaActual)
                    ->update([
                        'beca' => $this->sanitizeInput($request->becaActual),
                        'institucion' => $this->sanitizeInput($request->institucionActual),
                        'desde' => $this->sanitizeInput($request->becadesdeActual),
                        'hasta' => $this->sanitizeInput($request->becahastaActual),
                        'unlp' => $this->sanitizeInput($request->unlpActual),
                        'actual' => 1,
                        'updated_at' => now(), // Actualiza la fecha de actualización
                    ]);
            } else {
                // Si no hay idBecaActual, inserta un nuevo registro
                DB::table('joven_becas')->insert([
                    'joven_id' => $solicitud->id,
                    'beca' => $this->sanitizeInput($request->becaActual),
                    'institucion' => $this->sanitizeInput($request->institucionActual),
                    'desde' => $this->sanitizeInput($request->becadesdeActual),
                    'hasta' => $this->sanitizeInput($request->becahastaActual),
                    'unlp' => $this->sanitizeInput($request->unlpActual),
                    'actual' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

        }else {
            // 🔥 no viene beca actual → borrar la actual
            DB::table('joven_becas')
                ->where('joven_id', $solicitud->id)
                ->where('actual', 1)
                ->delete();
        }

        if (!empty($request->becas)) {


            foreach ($request->becas as $item => $v) {
                // Verifica si existe un becas_id para el registro actual
                if (!empty($request->becas_id[$item])) {
                    // Actualiza el registro si existe becas_id
                    DB::table('joven_becas')
                        ->where('id', $request->becas_id[$item])
                        ->update([
                            'beca' => $this->sanitizeInput($request->becas[$item]),
                            'institucion' => $this->sanitizeInput($request->institucions[$item]),
                            'desde' => $this->sanitizeInput($request->becadesdes[$item]),
                            'hasta' => $this->sanitizeInput($request->becahastas[$item]),
                            'unlp' => ($request->institucions[$item] == 'UNLP') ? 1 : 0,
                            'agregada' => $this->sanitizeInput($request->becaagregadas[$item]),
                            'updated_at' => now(), // Actualiza la fecha de actualización
                        ]);
                } else {
                    // Inserta un nuevo registro si no hay becas_id
                    DB::table('joven_becas')->insert([
                        'joven_id' => $solicitud->id,
                        'beca' => $this->sanitizeInput($request->becas[$item]),
                        'institucion' => $this->sanitizeInput($request->institucions[$item]),
                        'desde' => $this->sanitizeInput($request->becadesdes[$item]),
                        'hasta' => $this->sanitizeInput($request->becahastas[$item]),
                        'unlp' => ($request->institucions[$item] == 'UNLP') ? 1 : 0,
                        'agregada' => $this->sanitizeInput($request->becaagregadas[$item]),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
        if (!$actualizar){

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
                $directorQuery = Integrante::select(DB::raw("CONCAT(personas.apellido, ', ', personas.nombre) as director_apellido"))
                    ->leftJoin('investigadors', 'integrantes.investigador_id', '=', 'investigadors.id')
                    ->leftJoin('proyectos', 'integrantes.proyecto_id', '=', 'proyectos.id')
                    ->leftJoin('personas', 'investigadors.persona_id', '=', 'personas.id');
                $directorQuery->where('integrantes.tipo', 'Director');
                $directorQuery->where('integrantes.proyecto_id', $integrante->proyecto_id);
                $director = $directorQuery->first();

                // Inserta el registro en la tabla intermedia 'investigador_cargos'
                DB::table('joven_proyectos')->insert([
                    'joven_id' => $solicitud->id,
                    'proyecto_id' => $proyecto->id,


                    'desde' => $integrante->alta,
                    'hasta' => (($integrante->baja)&&($integrante->baja!='0000-00-00'))?$integrante->baja:$proyecto->fin,
                    'codigo' => $proyecto->codigo,
                    'director' => $director->director_apellido,
                    'titulo' => $proyecto->titulo,
                    'estado' => $proyecto->estado,
                    'agregado' => 0,
                    'actual' => 1,
                    'created_at' => now(), // Establece la fecha y hora de creación
                    'updated_at' => now(), // Establece la fecha y hora de actualización
                ]);




            }
            $integrantes = Integrante::where('investigador_id', $solicitud->investigador_id)
                ->where(function ($query) {
                    // Condiciones para la alta y baja
                    $query->whereColumn('integrantes.alta', '!=', 'integrantes.baja')
                        ->orWhereNull('integrantes.alta')
                        ->orWhereNull('integrantes.baja');
                })
                ->whereHas('proyecto', function ($query) {
                    $query->where('estado', 'Acreditado')
                        ->where(function ($q) {
                            // Proyectos anteriores o proyectos actuales con baja anterior
                            $q->where('fin', '<=', Carbon::now()->format('Y-m-d')) // Proyectos finalizados
                            ->orWhere(function ($subQuery) {
                                $subQuery->where('fin', '>', Carbon::now()->format('Y-m-d')) // Proyectos actuales
                                ->where('integrantes.baja', '<=', Carbon::now()->format('Y-m-d')); // Con baja anterior o igual a hoy
                            });
                        });
                })
                ->get();


            //dd($integrantes);

            foreach ($integrantes as $integrante){

                $proyecto = Proyecto::findOrFail($integrante->proyecto_id);
                //dd($proyecto);
                $directorQuery = Integrante::select(DB::raw("CONCAT(personas.apellido, ', ', personas.nombre) as director_apellido"))
                    ->leftJoin('investigadors', 'integrantes.investigador_id', '=', 'investigadors.id')
                    ->leftJoin('proyectos', 'integrantes.proyecto_id', '=', 'proyectos.id')
                    ->leftJoin('personas', 'investigadors.persona_id', '=', 'personas.id');
                $directorQuery->where('integrantes.tipo', 'Director');
                $directorQuery->where('integrantes.proyecto_id', $integrante->proyecto_id);
                $director = $directorQuery->first();

                // Inserta el registro en la tabla intermedia 'investigador_cargos'
                DB::table('joven_proyectos')->insert([
                    'joven_id' => $solicitud->id,
                    'proyecto_id' => $proyecto->id,


                    'desde' => $integrante->alta,
                    'hasta' => (($integrante->baja)&&($integrante->baja!='0000-00-00'))?$integrante->baja:$proyecto->fin,
                    'codigo' => $proyecto->codigo,
                    'director' => $director->director_apellido,
                    'titulo' => $proyecto->titulo,
                    'estado' => $proyecto->estado,
                    'agregado' => 0,
                    'actual' => 0,
                    'created_at' => now(), // Establece la fecha y hora de creación
                    'updated_at' => now(), // Establece la fecha y hora de actualización
                ]);




            }
        }

        $ids = $request->input('idAnterior', []);
        $codigos = $request->input('codigoAnterior', []);
        $titulos = $request->input('tituloAnterior', []);
        $directores = $request->input('directorAnterior', []);
        $inicios = $request->input('inicioAnterior', []);
        $fins = $request->input('finAnterior', []);
        for ($i = 0; $i < count($codigos); $i++) {

            if (!empty($ids[$i])) {
                // Actualiza el registro si existe becas_id
                DB::table('joven_proyectos')
                    ->where('id', $ids[$i])
                    ->update([
                        'desde' => $this->sanitizeInput($inicios[$i]),
                        'hasta' => $this->sanitizeInput($fins[$i]),
                        'codigo' => $this->sanitizeInput($codigos[$i]),
                        'director' => $this->sanitizeInput($directores[$i]),
                        'titulo' => $this->sanitizeInput($titulos[$i]),
                        'updated_at' => now(), // Actualiza la fecha de actualización
                    ]);
            } else {


                DB::table('joven_proyectos')->insert([
                    'joven_id' => $solicitud->id,
                    'proyecto_id' => null,
                    'desde' => $this->sanitizeInput($inicios[$i]),
                    'hasta' => $this->sanitizeInput($fins[$i]),
                    'codigo' => $this->sanitizeInput($codigos[$i]),
                    'director' => $this->sanitizeInput($directores[$i]),
                    'titulo' => $this->sanitizeInput($titulos[$i]),
                    'agregado' => 1,
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
                                    DB::table('joven_presupuestos')
                                        ->where('id', $ids[$index])
                                        ->update([
                                            'joven_id' => $solicitud->id,
                                            'tipo_presupuesto_id' => $tipoPresupuesto->id,
                                            'fecha' => $fecha,
                                            'detalle' => $detalle,
                                            'monto' => $importe,
                                            'updated_at' => now(),
                                        ]);
                                } else {*/


                                    // Guardar el presupuesto con el campo detalle concatenado
                                    DB::table('joven_presupuestos')->insert([
                                        'joven_id' => $solicitud->id,
                                        'tipo_presupuesto_id' => $tipoPresupuesto->id,
                                        'fecha' => $this->sanitizeInput($fecha),
                                        'detalle' => $this->sanitizeInput($detalle),
                                        'monto' => $this->sanitizeInput($importe),
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
                                DB::table('joven_presupuestos')
                                    ->where('id', $ids[$index])
                                    ->update([
                                        'joven_id' => $solicitud->id,
                                        'tipo_presupuesto_id' => $tipoPresupuesto->id,
                                        'fecha' => $fecha,
                                        'detalle' => $detalles[$index],
                                        'monto' => $importes[$index] ?? 0,
                                        'updated_at' => now(),
                                    ]);
                            } else {*/
                                DB::table('joven_presupuestos')->insert([
                                    'joven_id' => $solicitud->id,
                                    'tipo_presupuesto_id' => $tipoPresupuesto->id,
                                    'fecha' => $this->sanitizeInput($fecha),
                                    'detalle' => $this->sanitizeInput($detalles[$index]),
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
        $joven = Joven::find($id);

        $today = now();
        $cierreDate = \Carbon\Carbon::parse(Constants::CIERRE_JOVENES);
        if ($today->gt($cierreDate)) {

            return redirect()->route('jovens.index')->withErrors(['message' => 'La convocatoria no está vigente.']);
        }

        $titulos=Titulo::where('nivel', 'Grado')->orderBy('nombre','ASC')->get();
        $titulos = $titulos->pluck('full_name', 'id')->prepend('','');
        $tituloposts=Titulo::where('nivel', 'Posgrado')->orderBy('nombre','ASC')->get();
        $tituloposts = $tituloposts->pluck('full_name', 'id')->prepend('','');
        $facultades = DB::table('facultads')->pluck('nombre', 'id')->prepend('','');// Obtener todas las facultades directamente desde la tabla
        // Obtener los cargos ordenados por el campo 'orden' y seleccionar solo los campos 'id' y 'nombre'
        //$cargos = Cargo::orderBy('orden')->pluck('nombre', 'id')->prepend('', '');
        $idsExcluir = [6, 12, 13, 14];

        $cargos = Cargo::whereNotIn('id', $idsExcluir)
            ->orderBy('orden')
            ->pluck('nombre', 'id')
            ->prepend('', '');
        $universidades=Universidad::orderBy('nombre','ASC')->get();
        $universidades = $universidades->pluck('nombre', 'id')->prepend('','');
        $unidads=Unidad::orderBy('nombre','ASC')->get();
        $unidads->each->append('path_to_parent');
        $unidads = $unidads->pluck('path_to_parent', 'id')->prepend('','');
        $carrerainvs = Carrerainv::where('activo','1')->orderBy('orden')->pluck('nombre', 'id')->prepend('', '');
        $organismos = DB::table('organismos')->where('activo','1')->pluck('codigo', 'id')->prepend('','');
        $tipoPresupuestos = DB::table('tipo_presupuestos')->where('activo', true)->get();
        //dd($tipoPresupuestos);
        $currentYear = date('Y');
        $startYear = 1994;
        $years = range($currentYear, $startYear);
        $years = array_combine($years, $years); // Esto crea un array asociativo con los años como claves y valores
        /*$categorias = Categoria::orderBy('id')->pluck('nombre', 'id')->prepend('', '');

        $sicadis = Sicadi::orderBy('id')->pluck('nombre', 'id')->prepend('', '');*/


        $hoy = Carbon::today(); //Aquí se obtiene la fecha de hoy
        $proyectosActuales = $joven->proyectos()->where('desde', '<=',$hoy)->where('hasta', '>=',$hoy)->get();

        $proyectosAnteriores = $joven->proyectos()->where('hasta', '<', $hoy)->where('agregado', false)->get();

        $proyectosAnterioresAgregados = $joven->proyectos()->where('hasta', '<', $hoy)->where('agregado', true)->get();


        $beca = $joven->becas()->where('actual', true)->first(); // Beca actual
        $becas = $joven->becas()->where('actual', false)->get(); // Becas anteriores


        $periodo = DB::table('periodos')->where('nombre', Constants::YEAR_JOVENES)->first();




        return view('jovens.edit',compact('titulos','tituloposts','facultades','cargos','universidades','unidads','carrerainvs','years','organismos','joven','beca','becas','proyectosActuales','proyectosAnteriores','proyectosAnterioresAgregados','tipoPresupuestos','periodo'));
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

        $input = $this->sanitizeInput($request->all());
        // Asegurarse de que los checkbox tienen valor 0 si no se enviaron
        $input['doctorado'] = isset($request->doctorados[0]) ? 1 : 0;
        $input['director'] = isset($request->director) ? 1 : 0;
        $input['notificacion'] = isset($request->notificacion) ? 1 : 0;

        $input['fecha']=Carbon::now();

        $user = auth()->user();

        $cuil = $user->cuil;
        // Crear la carpeta si no existe
        /*$destinationPath = public_path('/files/jovenes/' . Constants::YEAR_JOVENES.'/'.$cuil);
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0777, true);
        }*/

        //$input['alta']= Constants::YEAR.'-01-01';
        //$input['estado']= 'Creada';
        // Manejo de archivos
        //$input['curriculum'] ='';
        /*if ($files = $request->file('curriculum')) {
            $file = $request->file('curriculum');
            $name = 'CV_'.time().'.'.$file->getClientOriginalExtension();

            $file->move($destinationPath, $name);
            $input['curriculum'] = "files/jovenes".Constants::YEAR_JOVENES."/$cuil/$name";
        }*/
        $solicitud = Joven::find($id);
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
            $path = $file->storeAs('public/files/jovenes/' . Constants::YEAR_JOVENES.'/'.$cuil, $filename);
            $input['curriculum'] = Storage::url($path); // Genera URL tipo /storage/files/...
        }
        if ($request->has('delete_cv') && $solicitud->curriculum) {
            $rutaAnterior = str_replace('/storage/', 'public/', $solicitud->curriculum); // Ej: public/images/sicadi/foto_xyz.png
            if (Storage::exists($rutaAnterior)) {
                Storage::delete($rutaAnterior);
            }
            $input['curriculum'] = null;
        }

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

        return redirect()->route('jovens.index')->with($respuestaID, $respuestaMSJ);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $joven = Joven::findOrFail($id);


        // Elimina las relaciones
        $joven->proyectos()->delete();
        $joven->becas()->delete();
        $joven->presupuestos()->delete();
        $joven->estados()->delete();

        // Elimina el joven
        $joven->delete();

        return redirect()->route('jovens.index')
            ->with('success','Solicitud eliminada con éxito');
    }

    public function cambiarEstado($joven, $comentarios)
    {

        // Actualizar el registro de estado existente donde 'hasta' es null
        JovenEstado::where('joven_id', $joven->id)
            ->whereNull('hasta')
            ->update(['hasta' => Carbon::now()]);

        // Obtener el ID del usuario autenticado
        $userId = Auth::id();

        // Crear registro en integrante_estados con el estado "Alta Creada" y el user_id
        $joven->estados()->create([
            'estado' => $joven->estado,
            'user_id' => $userId,
            'comentarios' => $comentarios,
            'desde' => Carbon::now(), // Establecer 'desde' como la fecha actual

        ]);
    }

    public function generatePDF(Request $request,$attach = false)
    {
        $jovenId = $request->query('joven_id');

        $joven = Joven::find($jovenId);

        $selectedRoleId = session('selected_rol');
        if ($selectedRoleId==2){
            $user = auth()->user();

            if ($joven->investigador->persona->cuil!=$user->cuil){
                abort(403, 'No autorizado.');
            }

        }

        // Consulta base


        $query = Joven::select('jovens.id as id','jovens.estado as estado','jovens.fecha as fecha', 'personas.nombre as persona_nombre', 'periodos.nombre as periodo_nombre', DB::raw("CONCAT(personas.apellido, ', ', personas.nombre) as persona_apellido"), 'cuil','jovens.calle','jovens.nro','jovens.piso','jovens.depto','jovens.cp','jovens.email','jovens.telefono','jovens.notificacion','titulos.nombre as titulo', 'jovens.egresogrado','tituloposgrado.nombre as tituloposgrado', 'jovens.egresoposgrado', DB::raw("CONCAT(unidads.nombre, ' - ', unidads.sigla) as unidad"), 'facultads.cat as facultad_cat','cargos.nombre as cargo','jovens.deddoc as dedicacion', 'facultads.nombre as facultad_nombre', 'facultadplanilla.nombre as facultadplanilla_nombre', DB::raw("CONCAT(unidadbeca.nombre, ' - ', unidadbeca.sigla) as unidadbeca"),'jovens.director','jovens.objetivo','jovens.justificacion', 'carrerainvs.nombre as carrerainvs_nombre', 'organismos.nombre as organismo_nombre','jovens.ingreso_carrerainv', DB::raw("CONCAT(unidadcarrera.nombre, ' - ', unidadcarrera.sigla) as unidadcarrera"))
            ->leftJoin('periodos', 'jovens.periodo_id', '=', 'periodos.id')
            ->leftJoin('investigadors', 'jovens.investigador_id', '=', 'investigadors.id')
            ->leftJoin('personas', 'investigadors.persona_id', '=', 'personas.id')
            ->leftJoin('facultads', 'jovens.facultad_id', '=', 'facultads.id')
            ->leftJoin('facultads as facultadplanilla', 'jovens.facultadplanilla_id', '=', 'facultadplanilla.id')
            ->leftJoin('titulos', 'jovens.titulo_id', '=', 'titulos.id')
            ->leftJoin('titulos as tituloposgrado', 'jovens.titulopost_id', '=', 'tituloposgrado.id')
            ->leftJoin('unidads', 'jovens.unidad_id', '=', 'unidads.id')
            ->leftJoin('unidads as unidadcarrera', 'jovens.unidadcarrera_id', '=', 'unidadcarrera.id')
            ->leftJoin('unidads as unidadbeca', 'jovens.unidadbeca_id', '=', 'unidadbeca.id')
            ->leftJoin('cargos', 'jovens.cargo_id', '=', 'cargos.id')
            ->leftJoin('carrerainvs', 'jovens.carrerainv_id', '=', 'carrerainvs.id')
            ->leftJoin('organismos', 'jovens.organismo_id', '=', 'organismos.id');

        $query->where('jovens.id', $jovenId);




        $beca = $joven->becas()->where('actual', true)->first(); // Beca actual
        $becas = $joven->becas()->where('actual', false)->get(); // Becas anteriores

        $proyectosActuales = $joven->proyectos()->where('actual', true)->get();
        $proyectosAnteriores = $joven->proyectos()->where('actual', false)->get();

        // Obtener solo los elementos paginados
        $datos = $query->first();
        //dd($datos);
        //$integrante = Integrante::findOrFail($integranteId);
        //dd($datos);

        $tipoPresupuestos = DB::table('tipo_presupuestos')->where('activo', true)->get();
        $template = 'jovens.pdfsolicitud';
        $presupuestos = $joven->presupuestos()->get();

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
            'carrerainv' => $datos->carrerainvs_nombre,
            'organismo' => $datos->organismo_nombre,
            'ingreso_carrera' => $datos->ingreso_carrerainv,
            'unidadcarrera' => $datos->unidadcarrerainv,
        ];
        //dd($data);




        $pdf = PDF::loadView($template, $data);

        $pdfPath = 'Joven_' . $datos->cuil . '.pdf';

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
        $jovenId = $request->query('joven_id');

        $joven = Joven::findOrFail($jovenId);

        $files = [
            'curriculum' => $joven->curriculum,
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
        $zipFileName = 'archivos_joven_' . $jovenId . '.zip';
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
        $solicitud = Joven::findOrFail($id);

        $error='';
        $ok = 1;

        $errores = [];

        $today = now();
        $cierreDate = \Carbon\Carbon::parse(Constants::CIERRE_JOVENES);
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
            $rango = intval(Constants::YEAR_JOVENES)-intval(Constants::YEAR_INGRESO_ATRAS_JOVENES);
            $fechaDesde = Carbon::parse($solicitud->egresoposgrado);
            $fechaHasta = Carbon::parse(Constants::RANGO_INGRESO_JOVENES.$rango);

            if ($fechaDesde->lessThanOrEqualTo($fechaHasta)) {
                $errores[] = "Doctorado anterior al ".Constants::RANGO_INGRESO_JOVENES.$rango;
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
            (empty($solicitud->carrerainv_id) && empty($solicitud->organismo_id) && empty($solicitud->ingreso_carrerainv) && empty($solicitud->unidadcarrera_id)) ||
            (!empty($solicitud->carrerainv_id) && !empty($solicitud->organismo_id) && !empty($solicitud->ingreso_carrerainv) && !empty($solicitud->unidadcarrera_id) )
        ) {

        }else{

            $errores[] = 'Complete todos los campos de la Carrera de Investigación en la pestaña Investigación';
        }
        if (!empty($solicitud->carrerainv_id)){
            $esCarrera=1;
        }
        if (!empty($solicitud->ingreso_carrerainv) ) {
            $ingreso = $solicitud->ingreso_carrerainv;
            $rango = intval(Constants::YEAR_JOVENES)-intval(Constants::YEAR_INGRESO_ATRAS_JOVENES);
            $fechaDesde = Carbon::parse($ingreso);
            $fechaHasta = Carbon::parse(Constants::RANGO_INGRESO_JOVENES.$rango);
            $fechaCierre = Carbon::parse(Constants::CIERRE_JOVENES);

            // Verificar que el ingreso sea posterior a la fecha de cierre
            if ($fechaDesde->greaterThan($fechaCierre)) {
                $errores[] = "El ingreso a la carrera debe ser anterior al " . Carbon::parse(Constants::CIERRE_JOVENES  )->format('d/m/Y');
            }

            //Log::info('Desde: '. $fechaDesde.' hasta: '.$fechaHasta);
            if ($fechaDesde->lessThanOrEqualTo($fechaHasta)) {
                $errores[] = "Ingreso a la carrera anterior al ".Constants::RANGO_INGRESO_JOVENES.$rango;
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
                $fechaLimiteEdad = \Carbon\Carbon::create(Constants::YEAR_JOVENES, Constants::MES_EDAD_JOVENES, Constants::DIA_EDAD_JOVENES);
                $edad = $fechaNacimiento->age;
                //Log::info('Nacimiento: '. $fechaNacimiento.' limite: '.$fechaLimiteEdad.' edad '.$edad);
                if ($edad >= intval(Constants::TOPE_EDAD_JOVENES) ) {
                    $errores[] = 'Solicitante no menor a '.Constants::TOPE_EDAD_JOVENES.' años al ' . Constants::DIA_EDAD_JOVENES . '/' . Constants::MES_EDAD_JOVENES . '/' . Constants::YEAR_JOVENES . ' y no Becario UNLP';

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


        if (($totalMonto<=0)||($totalMonto>Constants::MONTO_JOVENES))  {

            $errores[] = 'El monto total debe ser mayor que 0 (cero) y no puede superar el límite máximo de $' . number_format(Constants::MONTO_JOVENES, 2, ',', '.');
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
                $pdfPath = $this->generatePDF(new Request(['joven_id' => $solicitud->id]), true);

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

        return redirect()->route('jovens.index')->with($respuestaID, $respuestaMSJ);
    }



    public function enviarCorreosAlUsuario($datosCorreo, $joven,$userId, $adjuntarArchivos, $adjuntarPlanilla)
    {

        $user = User::find($userId); // Obtener el usuario por su ID

        // Enviar correo electrónico al usuario
        Mail::to($user->email)->send(new JovenEnviada($datosCorreo,$joven, $adjuntarArchivos, $adjuntarPlanilla));

        // Enviar correo electrónico a tu servidor (ejemplo)
        Mail::to('marcosp@presi.unlp.edu.ar')->send(new JovenEnviada($datosCorreo,$joven, $adjuntarArchivos, $adjuntarPlanilla));

        // Obtener el nombre del rol correspondiente al id 4
        $roleName = Role::find(Constants::ID_ADMIN_FACULTAD_PROYECTOS)->name;

        // Obtener usuarios que pertenecen a la facultad especificada y tienen el rol con id 4
        $usuarios = User::where('facultad_id', $joven->facultad_id)
            ->role($roleName)
            ->get();

        // Enviar correo electrónico a cada usuario
        foreach ($usuarios as $usuario) {
            Mail::to($usuario->email)->send(new JovenEnviada($datosCorreo, $joven, $adjuntarArchivos, $adjuntarPlanilla));
        }
    }

    public function enviarCorreos( $datosCorreo, $joven)
    {



            $user = User::where('cuil', $joven->investigador->persona->cuil)->first();
            if ($user) {
                // Enviar correo electrónico al usuario del director
                Mail::to($user->email)->send(new JovenEnviada($datosCorreo, $joven));
            }


        // Obtener el nombre del rol correspondiente al id 4
        $roleName = Role::find(Constants::ID_ADMIN_FACULTAD_PROYECTOS)->name;

        // Obtener usuarios que pertenecen a la facultad especificada y tienen el rol con id 4
        $usuarios = User::where('facultad_id', $joven->facultadplanilla_id)
            ->role($roleName)
            ->get();

        // Enviar correo electrónico a cada usuario
        foreach ($usuarios as $usuario) {
            Mail::to($usuario->email)->send(new JovenEnviada($datosCorreo, $joven));
        }
    }

    public function admitir($id)
    {
        $joven = Joven::findOrFail($id);


        DB::beginTransaction();
        try {

            $joven->estado = 'Admitida';

            $joven->save();
            $investigador = Investigador::find($joven->investigador_id);


            $this->actualizarInvestigador($joven,$investigador);


            $this->cambiarEstado($joven,'Solicitud admitida');


            $year = $joven->periodo->nombre;

            // Preparar datos para el correo
            $datosCorreo = [
                'from_email' => Constants::MAIL_JOVENES,
                'from_name' => Constants::NOMBRE_JOVENES,
                'asunto' => 'Admisión de Solicitud de Subsidios para Jóvenes Investigadores '.$year,
                'year' => $year,

                'investigador' => $joven->investigador->persona->apellido.', '.$joven->investigador->persona->nombre.' ('.$joven->investigador->persona->cuil.')',
                'comment' => 'La solicitud fue admitida para su evaluación',
            ];



            // Llama a la función enviarCorreos
            $this->enviarCorreos($datosCorreo, $joven);



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
        return redirect()->route('jovens.index')->with($respuestaID, $respuestaMSJ);
    }

    public function rechazar($id)
    {
        $joven = Joven::find($id);




        return view('jovens.deny',compact('joven'));

    }

    public function saveDeny(Request $request, $id)
    {
        $this->validate($request, [
            'comentarios' => 'required'
        ]);

        $input = $this->sanitizeInput($request->all());

        $joven = Joven::findOrFail($id);


        DB::beginTransaction();
        try {

            $joven->estado = 'No Admitida';

            $joven->save();

            $this->cambiarEstado($joven,$input['comentarios']);



            $year = $joven->periodo->nombre;

            // Preparar datos para el correo
            $datosCorreo = [
                'from_email' => Constants::MAIL_JOVENES,
                'from_name' => Constants::NOMBRE_JOVENES,
                'asunto' => 'NO Admisión de Solicitud de Subsidios para Jóvenes Investigadores '.$year,
                'year' => $year,

                'investigador' => $joven->investigador->persona->apellido.', '.$joven->investigador->persona->nombre.' ('.$joven->investigador->persona->cuil.')',
                'comment' => '<strong>Motivos de la no admisión</strong>: '.$input['comentarios'],
            ];



            // Llama a la función enviarCorreos
            $this->enviarCorreos($datosCorreo, $joven);


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
        return redirect()->route('jovens.index')->with($respuestaID, $respuestaMSJ);
    }

    public function actualizarInvestigador($joven, $investigador)
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
            $investigador->titulo_id = $joven->titulo_id;
            $investigador->titulopost_id = $joven->titulopost_id;
            $investigador->unidad_id = $joven->unidad_id;
            $beca = $joven->becas()->where('actual', true)->first(); // Beca actual
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

            $persona->email = $joven->email;
            $persona->nacimiento = $joven->nacimiento;
            $persona->telefono = $joven->telefono;
            $persona->calle = $joven->calle;
            $persona->nro = $joven->nro;
            $persona->piso = $joven->piso;
            $persona->cp = $joven->cp;
            $persona->depto = $joven->depto;
            $persona->save();
        }
        if (!empty($joven->titulo_id)) {


            // Verificar si el título ya está asociado
            $tituloId = $joven->titulo_id;
            $egresoGrado = $joven->egresogrado;

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
        if (!empty($joven->titulopost_id)) {

            // Verificar si el título ya está asociado
            $tituloId = $joven->titulopost_id;
            $egresoPosgrado = $joven->egresoposgrado;

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







        if (!empty($joven->carrerainv_id)) {

            // Datos de la nueva carrera
            $nuevaCarrera = [
                'carrerainv_id' => $joven->carrerainv_id,
                'organismo_id' => $joven->organismo_id,
                'ingreso' => $joven->ingreso_carrerainv,
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

        $becas = $joven->becas()->get(); // todas

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
