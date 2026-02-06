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
use App\Traits\SanitizesInput;
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
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use ZipArchive;

use PDF;
use Illuminate\Support\Facades\Mail;
use App\Mail\ViajeEnviada;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
class ViajeController extends Controller

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



    public function dataTable(Request $request)
    {
        $columnas = ['personas.nombre','periodos.nombre', 'personas.apellido', 'viajes.fecha','viajes.estado', 'facultads.cat', 'facultads.nombre', '','viajes.diferencia', 'viajes.puntaje',DB::raw("CASE viajes.motivo WHEN 'A) Reuniones Científicas' THEN 'A' ELSE CASE viajes.motivo WHEN 'B) Estadía de trabajo para investigar en ámbitos académicos externos a la UNLP' THEN 'B' ELSE CASE viajes.motivo WHEN 'C) Estadía de Trabajo en la UNLP para un Investigador Invitado' THEN 'C' ELSE '' END END END"),'viajes.monto']; // Define las columnas disponibles
        $columnaOrden = $columnas[$request->input('order.0.column')];
        $orden = $request->input('order.0.dir');
        $busqueda = $request->input('search.value');
        $periodo = $request->input('periodo'); // Obtener el filtro de período de la solicitud
        $estado = $request->input('estado');
        $area = $request->input('area');
        $motivo = $request->input('motivo');
        $facultadplanilla = $request->input('facultadplanilla');
        $otorgadas = $request->input('otorgadas');
        //dd($otorgadas);
        // Consulta base
        $query = Viaje::select('viajes.id as id', 'personas.nombre as persona_nombre', 'periodos.nombre as periodo_nombre', DB::raw("CONCAT(personas.apellido, ', ', personas.nombre) as persona_apellido"),'viajes.fecha as fecha','viajes.estado as estado', 'facultads.cat as facultad_cat', 'facultads.nombre as facultad_nombre','viajes.diferencia','viajes.puntaje',DB::raw("CASE viajes.motivo WHEN 'A) Reuniones Científicas' THEN 'A' ELSE CASE viajes.motivo WHEN 'B) Estadía de trabajo para investigar en ámbitos académicos externos a la UNLP' THEN 'B' ELSE CASE viajes.motivo WHEN 'C) Estadía de Trabajo en la UNLP para un Investigador Invitado' THEN 'C' ELSE '' END END END AS motivo"),'viajes.monto')
            ->leftJoin('periodos', 'viajes.periodo_id', '=', 'periodos.id')
            ->leftJoin('investigadors', 'viajes.investigador_id', '=', 'investigadors.id')
            ->leftJoin('personas', 'investigadors.persona_id', '=', 'personas.id')
            ->leftJoin('facultads', 'viajes.facultadplanilla_id', '=', 'facultads.id')
            ->with(['evaluacions' => function($query) {
                // Añadir select específico de los campos que deseas de los evaluadores
                $query->select('viaje_evaluacions.viaje_id', 'user_name', 'user_id', 'interno', 'viaje_evaluacions.estado', 'viaje_evaluacions.puntaje')
                    ->with('user:id,name'); // Carga el usuario solo si el user_id no es null
            }])
            ->with(['ambitos' => function($query) {
                $query->select('viaje_ambitos.viaje_id', 'viaje_ambitos.ciudad', 'viaje_ambitos.pais', 'viaje_ambitos.desde', 'viaje_ambitos.hasta');
            }]);


        if (!empty($periodo) && $periodo != '-1') {
            $query->where('viajes.periodo_id', $periodo);
        }

        if (!empty($estado) && $estado != '-1') {
            $query->where('viajes.estado', $estado);
        }

        if (!empty($area) && $area != '-1') {
            $query->where('facultads.cat', $area);
        }

        if (!empty($facultadplanilla) && $facultadplanilla != '-1') {
            $query->where('facultads.id', $facultadplanilla);
        }

        if (!empty($motivo) && $motivo != '-1') {
            $query->where('viajes.motivo', $motivo);
        }



        if ($otorgadas) {
            $query->where('viajes.estado', 'like', '%otorgada%');
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

        // Formatear el resultado agrupando evaluadores por viaje
        $datosFormateados = $datos->map(function ($viaje) {
            // Obtener el estado y puntaje de los evaluadores
            $evaluadores = $viaje->evaluacions->map(function ($evaluacion) {
                $user_name = $evaluacion->user_id ? $evaluacion->user->name : $evaluacion->user_name;
                $interno = ($evaluacion->interno) ? 'Interno':'Externo';
                return  $user_name. ' / ' .$interno. ' / ' . $evaluacion->estado . ' / P. ' . number_format($evaluacion->puntaje, 2, ',', '.');
            })->toArray(); // Convertir a un array
            // Formatear los ámbitos
            $ambitos = $viaje->ambitos->map(function ($ambito) {
                $desde = \Carbon\Carbon::parse($ambito->desde); // Asegúrate de importar Carbon con 'use Carbon\Carbon;'
                $hasta = \Carbon\Carbon::parse($ambito->hasta);

                return $ambito->ciudad . ' / ' . $ambito->pais . ' / (' . $desde->format('d/m/Y') . ' - ' . $hasta->format('d/m/Y') . ')';
            })->toArray();


            return [
                'id' => $viaje->id,
                'persona_nombre' => $viaje->persona_nombre,
                'periodo_nombre' => $viaje->periodo_nombre,
                'persona_apellido' => $viaje->persona_apellido,
                'fecha' => $viaje->fecha,
                'estado' => $viaje->estado,
                'facultad_cat' => $viaje->facultad_cat,
                'facultad_nombre' => $viaje->facultad_nombre,
                'motivo' => $viaje->motivo,
                'monto' => '$' . number_format($viaje->monto, 2, ',', '.'),
                'diferencia' => $viaje->diferencia,
                'puntaje' => $viaje->puntaje,
                'evaluadores' => $evaluadores, // Guardar la lista de evaluadores formateada
                'evaluacion_estado' => $viaje->evaluacion_estado, // Agregar el estado de la evaluación
                'ambitos' => $ambitos, // Guardar los ámbitos formateados
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
        $columnas = ['personas.nombre','periodos.nombre', 'personas.apellido', 'viajes.fecha','viajes.estado', 'facultads.cat', 'facultads.nombre', '','viajes.motivo','viajes.diferencia', 'viajes.puntaje']; // Define las columnas disponibles
        // La lógica de exportación a Excel va aquí
        $filtros = $request->all();



        $query = Viaje::select('viajes.id as id', 'personas.nombre as persona_nombre', 'periodos.nombre as periodo_nombre', DB::raw("CONCAT(personas.apellido, ', ', personas.nombre) as persona_apellido"),'viajes.fecha as fecha','viajes.estado as estado', 'facultads.cat as facultad_cat', 'facultads.nombre as facultad_nombre','viajes.diferencia','viajes.puntaje','personas.cuil','viajes.email','viajes.disciplina',DB::raw("CASE viajes.motivo WHEN 'A) Reuniones Científicas' THEN 'A' ELSE CASE viajes.motivo WHEN 'B) Estadía de trabajo para investigar en ámbitos académicos externos a la UNLP' THEN 'B' ELSE CASE viajes.motivo WHEN 'C) Estadía de Trabajo en la UNLP para un Investigador Invitado' THEN 'C' ELSE '' END END END AS motivo"), 'cargos.nombre as cargo_nombre',DB::raw("
            CASE
                WHEN categorias.id = 1 THEN categorias.nombre
                ELSE CONCAT(categorias.nombre, '/', sicadis.nombre)
            END as categoria_nombre"),'viajes.tipo','viajes.deddoc',DB::raw("CONCAT(unidads.nombre, ' - ', unidads.sigla) as unidad_nombre"),DB::raw("CONCAT(unidad_beca.nombre, ' - ', unidad_beca.sigla) as unidadbeca_nombre"),DB::raw("CONCAT(unidad_carrera.nombre, ' - ', unidad_carrera.sigla) as unidadcarrera_nombre"),DB::raw("CONCAT(viajes.institucion, ' - ', viajes.beca) as becario"),'viajes.periodobeca',DB::raw("CONCAT(organismos.codigo, ' - ', carrerainvs.nombre) as carrera_inv"),'viajes.ingreso_carrerainv',DB::raw("CONCAT(viajes.calle, ' Nro. ', viajes.nro, '(',viajes.piso,' ',viajes.depto,')','CP ',viajes.cp) as personales"))
            ->leftJoin('periodos', 'viajes.periodo_id', '=', 'periodos.id')
            ->leftJoin('investigadors', 'viajes.investigador_id', '=', 'investigadors.id')
            ->leftJoin('personas', 'investigadors.persona_id', '=', 'personas.id')
            ->leftJoin('facultads', 'viajes.facultadplanilla_id', '=', 'facultads.id')
            ->leftJoin('cargos', 'viajes.cargo_id', '=', 'cargos.id')
            ->leftJoin('categorias', 'viajes.categoria_id', '=', 'categorias.id')
            ->leftJoin('sicadis', 'viajes.sicadi_id', '=', 'sicadis.id')
            ->leftJoin('unidads', 'viajes.unidad_id', '=', 'unidads.id')
            ->leftJoin('unidads as unidad_beca', 'viajes.unidadbeca_id', '=', 'unidad_beca.id')
            ->leftJoin('unidads as unidad_carrera', 'viajes.unidadcarrera_id', '=', 'unidad_carrera.id')
            ->leftJoin('carrerainvs', 'viajes.carrerainv_id', '=', 'carrerainvs.id')
            ->leftJoin('organismos', 'viajes.organismo_id', '=', 'organismos.id')
            ->with(['ambitos' => function($query) {
                $query->select('viaje_ambitos.viaje_id', 'viaje_ambitos.ciudad', 'viaje_ambitos.pais', 'viaje_ambitos.desde', 'viaje_ambitos.hasta');
            }])
            ->with(['evaluacions' => function($query) {
                // Añadir select específico de los campos que deseas de los evaluadores
                $query->select('viaje_evaluacions.viaje_id', 'user_name', 'user_id', 'interno', 'viaje_evaluacions.estado', 'viaje_evaluacions.puntaje')
                    ->with('user:id,name'); // Carga el usuario solo si el user_id no es null
            }]);



        if (!empty($filtros['periodo'])&&$filtros['periodo']!=-1) {
            $query->where('viajes.periodo_id', $filtros['periodo']);
        }
        if (!empty($filtros['estado'])&&$filtros['estado']!=-1) {
            $query->where('viajes.estado', $filtros['estado']);
        }
        if (!empty($filtros['area'])&&$filtros['area']!=-1) {
            $query->where('facultads.cat', $filtros['area']);
        }
        if (!empty($filtros['facultadplanilla'])&&$filtros['facultadplanilla']!=-1) {
            $query->where('facultads.id', $filtros['facultadplanilla']);
        }

        if (!empty($filtros['motivo'])&&$filtros['motivo']!=-1) {
            $query->where('viajes.motivo', $filtros['motivo']);
        }

        if ($filtros['otorgadas']) {
            $query->where('viajes.estado', 'like', '%otorgada%');
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
        $sheet->setCellValue('D1', 'E-mail');
        $sheet->setCellValue('E1', 'Fecha');
        $sheet->setCellValue('F1', 'Estado');
        $sheet->setCellValue('G1', 'Área');
        $sheet->setCellValue('H1', 'U. Académica');
        $sheet->setCellValue('I1', 'Debe cargar la Disciplina primaria - disciplina secundaria - especialidad (separadas por guiones) Esta información será tenida en cuenta en el proceso de evaluación');
        $sheet->setCellValue('J1', 'Motivo');
        $sheet->setCellValue('K1', 'Cat. de Doc. Inv.');
        $sheet->setCellValue('L1', 'Tipo de Inv.');
        $sheet->setCellValue('M1', 'Monto');
        $sheet->setCellValue('N1', 'Lugar');
        $sheet->setCellValue('O1', 'Cargo docente');
        $sheet->setCellValue('P1', 'Dedicación');
        $sheet->setCellValue('Q1', 'L. de Trab. en la UNLP');
        $sheet->setCellValue('R1', 'BECARIO');
        $sheet->setCellValue('S1', 'Período');
        $sheet->setCellValue('T1', 'Lugar de trabajo');
        $sheet->setCellValue('U1', 'INVESTIGADOR DE CARRERA');
        $sheet->setCellValue('V1', 'Lugar de trabajo');
        $sheet->setCellValue('W1', 'Ingreso');
        $sheet->setCellValue('X1', 'Proyectos');
        $sheet->setCellValue('Y1', 'Personales');
        $sheet->setCellValue('Z1', 'Evaluadores');
        $sheet->setCellValue('AA1', 'Diferencia');
        $sheet->setCellValue('AB1', 'Puntaje');

        // Llenar los datos
        $row = 2;
        foreach ($data as $item) {

            $viaje = Viaje::find($item->id);
            $proyectos = $viaje->proyectos()->get(); // todos
            $strProyectos = '';
            if (!empty($proyectos)) {
                foreach ($proyectos as $proyecto) {

                    $directorQuery = Integrante::select(DB::raw("CONCAT(personas.apellido, ', ', personas.nombre) as director_apellido"))
                        ->leftJoin('investigadors', 'integrantes.investigador_id', '=', 'investigadors.id')
                        ->leftJoin('proyectos', 'integrantes.proyecto_id', '=', 'proyectos.id')
                        ->leftJoin('personas', 'investigadors.persona_id', '=', 'personas.id');
                    $directorQuery->where('integrantes.tipo', 'Director');
                    $directorQuery->where('integrantes.proyecto_id', $proyecto->proyecto_id);
                    $director = $directorQuery->first();


                    $coDirectorQuery = Integrante::select(DB::raw("CONCAT(personas.apellido, ', ', personas.nombre) as codirector_apellido"))
                        ->leftJoin('investigadors', 'integrantes.investigador_id', '=', 'investigadors.id')
                        ->leftJoin('proyectos', 'integrantes.proyecto_id', '=', 'proyectos.id')
                        ->leftJoin('personas', 'investigadors.persona_id', '=', 'personas.id');
                    $coDirectorQuery->where('integrantes.tipo', 'Codirector');
                    $coDirectorQuery->where('integrantes.proyecto_id', $proyecto->proyecto_id);
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


                    $strProyectos .= $proy->codigo.' DIR: '.$director->director_apellido.$cordir.' ('.date('d/m/Y', strtotime($proyecto->desde)) .'-'.date('d/m/Y', strtotime($proyecto->hasta)).') Especialidad: '.$especialidad.'---';
                }
            }

            $ambitos = $viaje->ambitos()->get(); // todos
            $strambitos = '';
            if (!empty($ambitos)) {
                foreach ($ambitos as $ambito) {
                    $desde = \Carbon\Carbon::parse($ambito->desde); // Asegúrate de importar Carbon con 'use Carbon\Carbon;'
                    $hasta = \Carbon\Carbon::parse($ambito->hasta);
                    $strambitos .= $ambito->ciudad . ' / ' . $ambito->pais . ' / (' . $desde->format('d/m/Y') . ' - ' . $hasta->format('d/m/Y') . ')---';
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
            $ingreso_carrera = \Carbon\Carbon::parse($item->ingreso_carrerainv);
            $fecha = \Carbon\Carbon::parse($item->fecha);
            $sheet->setCellValue('A' . $row, $item->periodo_nombre);
            $sheet->setCellValue('B' . $row, $item->persona_apellido);
            $sheet->setCellValue('C' . $row, $item->cuil);
            $sheet->setCellValue('D' . $row, $item->email);
            $sheet->setCellValue('E' . $row, $fecha->format('d/m/Y'));
            $sheet->setCellValue('F' . $row, $item->estado);
            $sheet->setCellValue('G' . $row, $item->facultad_cat);
            $sheet->setCellValue('H' . $row, $item->facultad_nombre);
            $sheet->setCellValue('I' . $row, $item->disciplina);
            $sheet->setCellValue('J' . $row, $item->motivo);
            $sheet->setCellValue('K' . $row, $item->categoria_nombre);
            $sheet->setCellValue('L' . $row, $item->tipo);
            $sheet->setCellValue('M' . $row, '$' . number_format($viaje->presupuestos()->sum('monto'), 2, ',', '.'));
            $sheet->setCellValue('N' . $row, $strambitos);
            $sheet->setCellValue('O' . $row, $item->cargo_nombre);
            $sheet->setCellValue('P' . $row, $item->deddoc);
            $sheet->setCellValue('Q' . $row, $item->unidad_nombre);
            $sheet->setCellValue('R' . $row, $item->becario);
            $sheet->setCellValue('S' . $row, $item->periodobeca);
            $sheet->setCellValue('T' . $row, $item->unidadbeca_nombre);
            $sheet->setCellValue('U' . $row, $item->carrera_inv);
            $sheet->setCellValue('V' . $row, $item->unidadcarrera_nombre);
            $sheet->setCellValue('W' . $row, $ingreso_carrera->format('d/m/Y'));
            $sheet->setCellValue('X' . $row, $strProyectos);
            $sheet->setCellValue('Y' . $row, $item->personales);
            $sheet->setCellValue('Z' . $row, $strEvaluacions);
            $sheet->setCellValue('AA' . $row, $item->diferencia);
            $sheet->setCellValue('AB' . $row, $item->puntaje);
            $row++;
        }


        // Guardar el archivo
        $writer = new Xlsx($spreadsheet);
        $filename = 'viajes_'.date('YmdHis').'.xlsx';

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


        $YearAgo2 = intval(Constants::YEAR_VIAJES)-2;
        $fechaCorte = $YearAgo2.'-12-31'; // Último día de hace 2 años

        $integrantes = Integrante::where('investigador_id', $investigador->id)
            ->where(function ($query) {
                $query->where('estado', '!=', 'Baja Creada')
                    ->where('estado', '!=', 'Baja Recibida')
                    ->orWhereNull('estado'); // Incluye estado = null
            })
            ->where(function ($q) use ($fechaCorte) {
                $q->where('baja', '>', $fechaCorte) // Asegurarse que la baja sea futura
                ->orWhereNull('baja')
                    ->orWhere('baja', '0000-00-00');
            })
            ->whereHas('proyecto', function ($query) use ($fechaCorte) {
                $query->where('estado', 'Acreditado')
                    ->where('fin', '>', $fechaCorte); // Proyectos vigentes
            })
            ->get();

        $aYear = 0;
        $YearAgo = intval(Constants::YEAR_VIAJES)-1;
        //dd($integrantes);
        $tieneProyecto = 0;
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
            if ($proyectoActual['fin']>=Carbon::now()->format('Y-m-d')) {
                $tieneProyecto=1;
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

        $montosViajes = [];
        foreach (Constants::MONTOS_VIAJES as $key => $monto) {
            $label = Constants::MONTOS_VIAJES_LABELS[$key] ?? $key;
            $montosViajes[$monto] = $label . ' ($' . number_format($monto, 0, ',', '.') . ')';
        }

        return view('viajes.create',compact('titulos','sicadis','facultades','cargos','universidades','unidads','carrerainvs','years','organismos','investigador','titulo','categorias','cargo','carrerainv','beca','proyectosActuales','tipoPresupuestos','periodo','tipoInvestigador','montosViajes'));
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
            'cvprofesor' => 'file|mimes:pdf,doc,docx|max:4048',

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
            'cvprofesor.mimes' => 'El archivo de Currículum del profesor visitante debe ser un documento de tipo: pdf, doc, docx.',
            'cvprofesor.max' => 'El archivo de Currículum del profesor visitante no debe ser mayor a 4 MB.',
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
            if ((empty($request->carrerainvs[0]))&&(!$request->institucion)) {
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
        /*$montoSeleccionado = (float) $request->monto;

        if ($totalMonto > $montoSeleccionado) {
            $errores[] = 'El monto total no puede superar el límite máximo de $' . number_format($montoSeleccionado, 2, ',', '.');
        }*/

        $desdes = $request->input('ambitodesdes');
        $hastas = $request->input('ambitohastas');
        $rangoInicio = \DateTime::createFromFormat('!d/m/Y', Constants::RANGO_INI_VIAJES.Constants::YEAR_VIAJES);
        $yearAgo = intval(Constants::YEAR_VIAJES)+1;
        $rangoFin = \DateTime::createFromFormat('!d/m/Y', Constants::RANGO_FIN_VIAJES.$yearAgo);
        if (!empty($desdes)){
            foreach ($desdes as $index => $desde) {
                $fechaDesde = \DateTime::createFromFormat('Y-m-d', $desde);
                $fechaHasta = \DateTime::createFromFormat('Y-m-d', $hastas[$index]);
                //Log::info('desde: ', $fechaDesde.' rango: '.$rangoInicio.' - '.$rangoFin);

                // Verificar que ambas fechas estén dentro del rango
                if ($fechaDesde < $rangoInicio || $fechaDesde > $rangoFin) {
                    $errores["ambitodesdes.$index"] = 'Fecha desde fuera del rango '.Constants::RANGO_INI_VIAJES.Constants::YEAR_VIAJES.' - '.Constants::RANGO_FIN_VIAJES.$yearAgo.' en Lugar';
                }
                /*if ($fechaHasta < $rangoInicio || $fechaHasta > $rangoFin) {
                    $errores["ambitohastas.$index"] = 'Fecha hasta fuera del rango '.Constants::RANGO_INI_VIAJES.Constants::YEAR_VIAJES.' - '.Constants::RANGO_FIN_VIAJES.$yearAgo.' en Lugar';
                }*/

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

        $input = $this->sanitizeInput($request->all());
        // Asegurarse de que los checkbox tienen valor 0 si no se enviaron

        $input['nacional'] = ($request->nacional_id='Nacional') ? 1 : 2;
        $input['notificacion'] = isset($request->notificacion) ? 1 : 0;
        $input['congreso'] = 1; //NO hay conferencias

        $input['fecha']=Carbon::now();

        $user = auth()->user();

        $cuil = $user->cuil;
        // Crear la carpeta si no existe
        /*$destinationPath = public_path('/files/viajes/' . Constants::YEAR_VIAJES.'/'.$cuil);
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
            $input['curriculum'] = "files/viajes".Constants::YEAR_VIAJES."/$cuil/$name";
        }*/
        if ($request->hasFile('curriculum')) {
            $file = $request->file('curriculum');
            $filename = 'CV_' . Str::uuid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('public/files/viajes/' . Constants::YEAR_VIAJES.'/'.$cuil, $filename);
            $input['curriculum'] = Storage::url($path); // Genera URL tipo /storage/files/...
        }
        $input['trabajo'] ='';
        /*if ($files = $request->file('trabajo')) {
            $file = $request->file('trabajo');
            $name = 'Trabajo_'.time().'.'.$file->getClientOriginalExtension();

            $file->move($destinationPath, $name);
            $input['trabajo'] = "files/viajes".Constants::YEAR_VIAJES."/$cuil/$name";
        }*/
        if ($request->hasFile('trabajo')) {
            $file = $request->file('trabajo');
            $filename = 'Trabajo_' . Str::uuid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('public/files/viajes/' . Constants::YEAR_VIAJES.'/'.$cuil, $filename);
            $input['trabajo'] = Storage::url($path); // Genera URL tipo /storage/files/...
        }
        $input['aceptacion'] ='';
        /*if ($files = $request->file('aceptacion')) {
            $file = $request->file('aceptacion');
            $name = 'Aceptacion_'.time().'.'.$file->getClientOriginalExtension();

            $file->move($destinationPath, $name);
            $input['aceptacion'] = "files/viajes".Constants::YEAR_VIAJES."/$cuil/$name";
        }*/
        if ($request->hasFile('aceptacion')) {
            $file = $request->file('aceptacion');
            $filename = 'Aceptacion_' . Str::uuid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('public/files/viajes/' . Constants::YEAR_VIAJES.'/'.$cuil, $filename);
            $input['aceptacion'] = Storage::url($path); // Genera URL tipo /storage/files/...
        }
        $input['invitacion'] ='';
        /*if ($files = $request->file('invitacion')) {
            $file = $request->file('invitacion');
            $name = 'Invitacion_'.time().'.'.$file->getClientOriginalExtension();

            $file->move($destinationPath, $name);
            $input['invitacion'] = "files/viajes".Constants::YEAR_VIAJES."/$cuil/$name";
        }*/
        if ($request->hasFile('invitacion')) {
            $file = $request->file('invitacion');
            $filename = 'Invitacion_' . Str::uuid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('public/files/viajes/' . Constants::YEAR_VIAJES.'/'.$cuil, $filename);
            $input['invitacion'] = Storage::url($path); // Genera URL tipo /storage/files/...
        }
        $input['convenioB'] ='';
        /*if ($files = $request->file('convenioB')) {
            $file = $request->file('convenioB');
            $name = 'ConvenioB_'.time().'.'.$file->getClientOriginalExtension();

            $file->move($destinationPath, $name);
            $input['convenioB'] = "files/viajes".Constants::YEAR_VIAJES."/$cuil/$name";
        }*/
        if ($request->hasFile('convenioB')) {
            $file = $request->file('convenioB');
            $filename = 'ConvenioB_' . Str::uuid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('public/files/viajes/' . Constants::YEAR_VIAJES.'/'.$cuil, $filename);
            $input['convenioB'] = Storage::url($path); // Genera URL tipo /storage/files/...
        }
        $input['convenioC'] ='';
        /*if ($files = $request->file('convenioC')) {
            $file = $request->file('convenioC');
            $name = 'ConvenioC_'.time().'.'.$file->getClientOriginalExtension();

            $file->move($destinationPath, $name);
            $input['convenioC'] = "files/viajes".Constants::YEAR_VIAJES."/$cuil/$name";
        }*/
        if ($request->hasFile('convenioC')) {
            $file = $request->file('convenioC');
            $filename = 'ConvenioC_' . Str::uuid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('public/files/viajes/' . Constants::YEAR_VIAJES.'/'.$cuil, $filename);
            $input['convenioC'] = Storage::url($path); // Genera URL tipo /storage/files/...
        }
        $input['aval'] ='';
        /*if ($files = $request->file('aval')) {
            $file = $request->file('aval');
            $name = 'Aval_'.time().'.'.$file->getClientOriginalExtension();

            $file->move($destinationPath, $name);
            $input['aval'] = "files/viajes".Constants::YEAR_VIAJES."/$cuil/$name";
        }*/
        if ($request->hasFile('aval')) {
            $file = $request->file('aval');
            $filename = 'Aval_' . Str::uuid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('public/files/viajes/' . Constants::YEAR_VIAJES.'/'.$cuil, $filename);
            $input['aval'] = Storage::url($path); // Genera URL tipo /storage/files/...
        }
        $input['cvprofesor'] ='';
        /*if ($files = $request->file('cvprofesor')) {
            $file = $request->file('cvprofesor');
            $name = 'CVProfesor_'.time().'.'.$file->getClientOriginalExtension();

            $file->move($destinationPath, $name);
            $input['cvprofesor'] = "files/viajes".Constants::YEAR_VIAJES."/$cuil/$name";
        }*/
        if ($request->hasFile('cvprofesor')) {
            $file = $request->file('cvprofesor');
            $filename = 'CVProfesor_' . Str::uuid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('public/files/viajes/' . Constants::YEAR_VIAJES.'/'.$cuil, $filename);
            $input['cvprofesor'] = Storage::url($path); // Genera URL tipo /storage/files/...
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
        if (!empty($request->titulos)) {
            $solicitud->titulo_id = $this->safeRequest($request, 'titulos');
            $solicitud->egresogrado = $this->safeRequest($request, 'egresos');
            $titulo=Titulo::findOrFail($solicitud->titulo_id );
            $solicitud->titulogrado = $titulo->nombre.' ('.$titulo->universidad->nombre.')';
            $solicitud->save();
        }

        if (!empty($request->categorias)) {
            $solicitud->categoria_id = $this->safeRequest($request, 'categorias');

            $solicitud->save();
        }

        if (!empty($request->sicadis)) {
            $solicitud->sicadi_id = $this->safeRequest($request, 'sicadis');

            $solicitud->save();
        }


        // Guarda el mayor cargo encontrado en el investigador
        if (!empty($request->cargos)) {
            $solicitud->cargo_id = $this->safeRequest($request, 'cargos');
            $solicitud->deddoc = $this->safeRequest($request, 'deddocs');
            $solicitud->ingreso_cargo = $this->safeRequest($request, 'ingresos');
            $solicitud->facultad_id = $this->safeRequest($request, 'facultads');

            $solicitud->save();
        }


        if (!empty($request->carrerainvs)) {
            $solicitud->carrerainv_id = $this->safeRequest($request, 'carrerainvs');
            $solicitud->organismo_id = $this->safeRequest($request, 'organismos');
            $solicitud->ingreso_carrerainv = $this->safeRequest($request, 'carringresos');
            $solicitud->save();
        }






        //if (!$actualizar){
        $solicitud->proyectos()->delete();
        $YearAgo2 = intval(Constants::YEAR_VIAJES)-2;
        $fechaCorte = $YearAgo2.'-12-31'; // Último día de hace 2 años

        $integrantes = Integrante::where('investigador_id', $solicitud->investigador_id)
            ->where(function ($query) {
                $query->where('estado', '!=', 'Baja Creada')
                    ->where('estado', '!=', 'Baja Recibida')
                    ->orWhereNull('estado'); // Incluye estado = null
            })
            ->where(function ($q) use ($fechaCorte) {
                $q->where('baja', '>', $fechaCorte) // Asegurarse que la baja sea futura
                ->orWhereNull('baja')
                    ->orWhere('baja', '0000-00-00');
            })
            ->whereHas('proyecto', function ($query) use ($fechaCorte) {
                $query->where('estado', 'Acreditado')
                    ->where('fin', '>', $fechaCorte); // Proyectos vigentes
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

        $solicitud->montos()->delete();
        if (!empty($request->montoinstitucions)) {

            foreach ($request->montoinstitucions as $index => $institucion) {

                $caracter = $request->montocaracters[$index] ?? null;
                $importe  = $request->montomontos[$index] ?? null;

                // Evita insertar filas vacías
                if (!empty($institucion) || !empty($caracter) || !empty($importe)) {

                    DB::table('viaje_montos')->insert([
                        'viaje_id'   => $solicitud->id,
                        'institucion'=> $this->sanitizeInput($institucion),
                        'caracter'   => $this->sanitizeInput($caracter),
                        'monto'    => $this->sanitizeInput($importe),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
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


        $montosViajes = [];
        foreach (Constants::MONTOS_VIAJES as $key => $monto) {
            $label = Constants::MONTOS_VIAJES_LABELS[$key] ?? $key;
            $montosViajes[$monto] = $label . ' ($' . number_format($monto, 0, ',', '.') . ')';
        }



        return view('viajes.edit',compact('titulos','facultades','cargos','universidades','unidads','carrerainvs','years','organismos','viaje','proyectosActuales','tipoPresupuestos','periodo','categorias','sicadis','montosViajes'));
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
        //dd($input);
        // Asegurarse de que los checkbox tienen valor 0 si no se enviaron
        $input['nacional'] = ($request->nacional_id='Nacional') ? 1 : 0;
        $input['notificacion'] = isset($request->notificacion) ? 1 : 0;

        $input['fecha']=Carbon::now();

        $user = auth()->user();

        $cuil = $user->cuil;
        // Crear la carpeta si no existe
        /*$destinationPath = public_path('/files/viajes/' . Constants::YEAR_VIAJES.'/'.$cuil);
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
            $input['curriculum'] = "files/viajes".Constants::YEAR_VIAJES."/$cuil/$name";
        }*/
        $solicitud = Viaje::find($id);
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
            $path = $file->storeAs('public/files/viajes/' . Constants::YEAR_VIAJES.'/'.$cuil, $filename);
            $input['curriculum'] = Storage::url($path); // Genera URL tipo /storage/files/...
        }
        if ($request->has('delete_cv') && $solicitud->curriculum) {
            $rutaAnterior = str_replace('/storage/', 'public/', $solicitud->curriculum); // Ej: public/images/sicadi/foto_xyz.png
            if (Storage::exists($rutaAnterior)) {
                Storage::delete($rutaAnterior);
            }
            $input['curriculum'] = null;
        }

        /*if ($files = $request->file('trabajo')) {
            $file = $request->file('trabajo');
            $name = 'Trabajo_'.time().'.'.$file->getClientOriginalExtension();

            $file->move($destinationPath, $name);
            $input['trabajo'] = "files/viajes".Constants::YEAR_VIAJES."/$cuil/$name";
        }*/

        if ($request->hasFile('trabajo')) {
            // Eliminar trabajo anterior si existe
            if (!empty($solicitud->trabajo)) {
                $rutaAnterior = str_replace('/storage/', 'public/', $solicitud->trabajo); // Ej: public/files/sicadi/2025/CV_123.pdf
                if (Storage::exists($rutaAnterior)) {
                    Storage::delete($rutaAnterior);
                }
            }

            $file = $request->file('trabajo');
            $filename = 'Trabajo_' . Str::uuid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('public/files/viajes/' . Constants::YEAR_VIAJES.'/'.$cuil, $filename);
            $input['trabajo'] = Storage::url($path); // Genera URL tipo /storage/files/...
        }
        if ($request->has('delete_trabajo') && $solicitud->trabajo) {
            $rutaAnterior = str_replace('/storage/', 'public/', $solicitud->trabajo); // Ej: public/images/sicadi/foto_xyz.png
            if (Storage::exists($rutaAnterior)) {
                Storage::delete($rutaAnterior);
            }
            $input['trabajo'] = null;
        }

        /*if ($files = $request->file('aceptacion')) {
            $file = $request->file('aceptacion');
            $name = 'Aceptacion_'.time().'.'.$file->getClientOriginalExtension();

            $file->move($destinationPath, $name);
            $input['aceptacion'] = "files/viajes".Constants::YEAR_VIAJES."/$cuil/$name";
        }*/

        if ($request->hasFile('aceptacion')) {
            // Eliminar aceptacion anterior si existe
            if (!empty($solicitud->aceptacion)) {
                $rutaAnterior = str_replace('/storage/', 'public/', $solicitud->aceptacion); // Ej: public/files/sicadi/2025/CV_123.pdf
                if (Storage::exists($rutaAnterior)) {
                    Storage::delete($rutaAnterior);
                }
            }

            $file = $request->file('aceptacion');
            $filename = 'Aceptacion_' . Str::uuid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('public/files/viajes/' . Constants::YEAR_VIAJES.'/'.$cuil, $filename);
            $input['aceptacion'] = Storage::url($path); // Genera URL tipo /storage/files/...
        }
        if ($request->has('delete_aceptacion') && $solicitud->aceptacion) {
            $rutaAnterior = str_replace('/storage/', 'public/', $solicitud->aceptacion); // Ej: public/images/sicadi/foto_xyz.png
            if (Storage::exists($rutaAnterior)) {
                Storage::delete($rutaAnterior);
            }
            $input['aceptacion'] = null;
        }

        /*if ($files = $request->file('invitacion')) {
            $file = $request->file('invitacion');
            $name = 'Invitacion_'.time().'.'.$file->getClientOriginalExtension();

            $file->move($destinationPath, $name);
            $input['invitacion'] = "files/viajes".Constants::YEAR_VIAJES."/$cuil/$name";
        }*/
        if ($request->hasFile('invitacion')) {
            // Eliminar invitacion anterior si existe
            if (!empty($solicitud->invitacion)) {
                $rutaAnterior = str_replace('/storage/', 'public/', $solicitud->invitacion); // Ej: public/files/sicadi/2025/CV_123.pdf
                if (Storage::exists($rutaAnterior)) {
                    Storage::delete($rutaAnterior);
                }
            }

            $file = $request->file('invitacion');
            $filename = 'Invitacion_' . Str::uuid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('public/files/viajes/' . Constants::YEAR_VIAJES.'/'.$cuil, $filename);
            $input['invitacion'] = Storage::url($path); // Genera URL tipo /storage/files/...
        }
        if ($request->has('delete_invitacion') && $solicitud->invitacion) {
            $rutaAnterior = str_replace('/storage/', 'public/', $solicitud->invitacion); // Ej: public/images/sicadi/foto_xyz.png
            if (Storage::exists($rutaAnterior)) {
                Storage::delete($rutaAnterior);
            }
            $input['invitacion'] = null;
        }

        /*if ($files = $request->file('convenioB')) {
            $file = $request->file('convenioB');
            $name = 'ConvenioB_'.time().'.'.$file->getClientOriginalExtension();

            $file->move($destinationPath, $name);
            $input['convenioB'] = "files/viajes".Constants::YEAR_VIAJES."/$cuil/$name";
        }*/

        if ($request->hasFile('convenioB')) {
            // Eliminar convenioB anterior si existe
            if (!empty($solicitud->convenioB)) {
                $rutaAnterior = str_replace('/storage/', 'public/', $solicitud->convenioB); // Ej: public/files/sicadi/2025/CV_123.pdf
                if (Storage::exists($rutaAnterior)) {
                    Storage::delete($rutaAnterior);
                }
            }

            $file = $request->file('convenioB');
            $filename = 'ConvenioB_' . Str::uuid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('public/files/viajes/' . Constants::YEAR_VIAJES.'/'.$cuil, $filename);
            $input['convenioB'] = Storage::url($path); // Genera URL tipo /storage/files/...
        }
        if ($request->has('delete_convenioB') && $solicitud->convenioB) {
            $rutaAnterior = str_replace('/storage/', 'public/', $solicitud->convenioB); // Ej: public/images/sicadi/foto_xyz.png
            if (Storage::exists($rutaAnterior)) {
                Storage::delete($rutaAnterior);
            }
            $input['convenioB'] = null;
        }

        /*if ($files = $request->file('convenioC')) {
            $file = $request->file('convenioC');
            $name = 'ConvenioC_'.time().'.'.$file->getClientOriginalExtension();

            $file->move($destinationPath, $name);
            $input['convenioC'] = "files/viajes".Constants::YEAR_VIAJES."/$cuil/$name";
        }*/

        if ($request->hasFile('convenioC')) {
            // Eliminar convenioC anterior si existe
            if (!empty($solicitud->convenioC)) {
                $rutaAnterior = str_replace('/storage/', 'public/', $solicitud->convenioC); // Ej: public/files/sicadi/2025/CV_123.pdf
                if (Storage::exists($rutaAnterior)) {
                    Storage::delete($rutaAnterior);
                }
            }

            $file = $request->file('convenioC');
            $filename = 'ConvenioC_' . Str::uuid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('public/files/viajes/' . Constants::YEAR_VIAJES.'/'.$cuil, $filename);
            $input['convenioC'] = Storage::url($path); // Genera URL tipo /storage/files/...
        }
        if ($request->has('delete_convenioC') && $solicitud->convenioC) {
            $rutaAnterior = str_replace('/storage/', 'public/', $solicitud->convenioC); // Ej: public/images/sicadi/foto_xyz.png
            if (Storage::exists($rutaAnterior)) {
                Storage::delete($rutaAnterior);
            }
            $input['convenioC'] = null;
        }

        /*if ($files = $request->file('aval')) {
            $file = $request->file('aval');
            $name = 'Aval_'.time().'.'.$file->getClientOriginalExtension();

            $file->move($destinationPath, $name);
            $input['aval'] = "files/viajes".Constants::YEAR_VIAJES."/$cuil/$name";
        }*/

        if ($request->hasFile('aval')) {
            // Eliminar aval anterior si existe
            if (!empty($solicitud->aval)) {
                $rutaAnterior = str_replace('/storage/', 'public/', $solicitud->aval); // Ej: public/files/sicadi/2025/CV_123.pdf
                if (Storage::exists($rutaAnterior)) {
                    Storage::delete($rutaAnterior);
                }
            }

            $file = $request->file('aval');
            $filename = 'Aval_' . Str::uuid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('public/files/viajes/' . Constants::YEAR_VIAJES.'/'.$cuil, $filename);
            $input['aval'] = Storage::url($path); // Genera URL tipo /storage/files/...
        }
        if ($request->has('delete_aval') && $solicitud->aval) {
            $rutaAnterior = str_replace('/storage/', 'public/', $solicitud->aval); // Ej: public/images/sicadi/foto_xyz.png
            if (Storage::exists($rutaAnterior)) {
                Storage::delete($rutaAnterior);
            }
            $input['aval'] = null;
        }

        /*if ($files = $request->file('cvprofesor')) {
            $file = $request->file('cvprofesor');
            $name = 'CVProfesor_'.time().'.'.$file->getClientOriginalExtension();

            $file->move($destinationPath, $name);
            $input['cvprofesor'] = "files/viajes".Constants::YEAR_VIAJES."/$cuil/$name";
        }*/

        if ($request->hasFile('cvprofesor')) {
            // Eliminar cvprofesor anterior si existe
            if (!empty($solicitud->cvprofesor)) {
                $rutaAnterior = str_replace('/storage/', 'public/', $solicitud->cvprofesor); // Ej: public/files/sicadi/2025/CV_123.pdf
                if (Storage::exists($rutaAnterior)) {
                    Storage::delete($rutaAnterior);
                }
            }

            $file = $request->file('cvprofesor');
            $filename = 'CVProfesor_' . Str::uuid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('public/files/viajes/' . Constants::YEAR_VIAJES.'/'.$cuil, $filename);
            $input['cvprofesor'] = Storage::url($path); // Genera URL tipo /storage/files/...
        }
        if ($request->has('delete_cvprofesor') && $solicitud->cvprofesor) {
            $rutaAnterior = str_replace('/storage/', 'public/', $solicitud->cvprofesor); // Ej: public/images/sicadi/foto_xyz.png
            if (Storage::exists($rutaAnterior)) {
                Storage::delete($rutaAnterior);
            }
            $input['cvprofesor'] = null;
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

        $viaje = Viaje::find($viajeId);
        $selectedRoleId = session('selected_rol');
        if ($selectedRoleId==2){
            $user = auth()->user();

            if ($viaje->investigador->persona->cuil!=$user->cuil){
                abort(403, 'No autorizado.');
            }

        }

        // Consulta base


        $query = Viaje::select('viajes.id as id','viajes.estado as estado','viajes.fecha as fecha', 'personas.nombre as persona_nombre', 'periodos.nombre as periodo_nombre', DB::raw("CONCAT(personas.apellido, ', ', personas.nombre) as persona_apellido"), 'cuil','viajes.calle','viajes.nro','viajes.piso','viajes.depto','viajes.cp','viajes.email','viajes.telefono','viajes.notificacion','viajes.scholar','titulos.nombre as titulo', 'viajes.egresogrado', DB::raw("CONCAT(unidads.nombre, ' - ', unidads.sigla) as unidad"),'unidads.direccion as direccion_unidad','unidads.telefono as telefono_unidad', 'facultads.cat as facultad_cat','cargos.nombre as cargo','viajes.deddoc as dedicacion', 'facultads.nombre as facultad_nombre', 'facultadplanilla.nombre as facultadplanilla_nombre', 'viajes.institucion as beca_institucion', 'viajes.beca as beca_beca', 'viajes.periodobeca as beca_periodo', 'viajes.unlp as beca_unlp',DB::raw("CONCAT(unidadbeca.nombre, ' - ', unidadbeca.sigla) as unidadbeca"),'viajes.objetivo', 'carrerainvs.nombre as carrerainv_nombre', 'organismos.nombre as organismo_nombre','viajes.ingreso_carrerainv', DB::raw("CONCAT(unidadcarrera.nombre, ' - ', unidadcarrera.sigla) as unidadcarrera"), 'categorias.nombre as categoria_nombre', 'sicadis.nombre as sicadi_nombre','viajes.tipo','viajes.motivo','viajes.monto','viajes.objetivo','viajes.relevanciaA','viajes.congreso','viajes.congresonombre','viajes.titulotrabajo','viajes.autores','viajes.nacional','viajes.lugartrabajo','viajes.trabajodesde','viajes.trabajohasta','viajes.relevancia','viajes.link','viajes.resumen','viajes.modalidad','viajes.profesor','viajes.lugarprofesor','viajes.libros','viajes.compilados','viajes.capitulos','viajes.articulos','viajes.congresos','viajes.patentes','viajes.intelectuales','viajes.informes','viajes.tesis','viajes.becas','viajes.tesinas','viajes.generalB','viajes.especificoB','viajes.actividadesB','viajes.cronogramaB','viajes.justificacionB','viajes.aportesB','viajes.relevanciaB','viajes.objetivosC','viajes.planC','viajes.relacionProyectoC','viajes.aportesC','viajes.actividadesC')
            ->leftJoin('periodos', 'viajes.periodo_id', '=', 'periodos.id')
            ->leftJoin('investigadors', 'viajes.investigador_id', '=', 'investigadors.id')
            ->leftJoin('personas', 'investigadors.persona_id', '=', 'personas.id')
            ->leftJoin('facultads', 'viajes.facultad_id', '=', 'facultads.id')
            ->leftJoin('facultads as facultadplanilla', 'viajes.facultadplanilla_id', '=', 'facultadplanilla.id')
            ->leftJoin('titulos', 'viajes.titulo_id', '=', 'titulos.id')
            ->leftJoin('categorias', 'viajes.categoria_id', '=', 'categorias.id')
            ->leftJoin('sicadis', 'viajes.sicadi_id', '=', 'sicadis.id')

            ->leftJoin('unidads', 'viajes.unidad_id', '=', 'unidads.id')
            ->leftJoin('unidads as unidadcarrera', 'viajes.unidadcarrera_id', '=', 'unidadcarrera.id')
            ->leftJoin('unidads as unidadbeca', 'viajes.unidadbeca_id', '=', 'unidadbeca.id')
            ->leftJoin('cargos', 'viajes.cargo_id', '=', 'cargos.id')
            ->leftJoin('carrerainvs', 'viajes.carrerainv_id', '=', 'carrerainvs.id')
            ->leftJoin('organismos', 'viajes.organismo_id', '=', 'organismos.id');

        $query->where('viajes.id', $viajeId);



        $investigador = Investigador::find($viaje->investigador_id);
        $inicioYear = Carbon::create(Constants::YEAR_VIAJES, 1, 1); // 1 de enero del año
        $finYear = Carbon::create(Constants::YEAR_VIAJES, 12, 31); // 31 de diciembre del año

        $beca = $investigador->becas()
            ->where('desde', '<=', $inicioYear)
            ->where('hasta', '>=', $finYear)
            ->first(); // Obtiene la primera beca vigente en el año

        $resumen_beca='';
        if (!empty($beca)){
            $resumen_beca = $beca->resumen;
        }


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
            $proyectoActual['resumen'] = $proyecto->resumen;
            $proyectoActual['seleccionado'] = ($proy->seleccionado)?1:0;
            //dd($proyectoActual);
            $proyectosActuales[]=$proyectoActual;

        }

        // Obtener solo los elementos paginados
        $datos = $query->first();
        //dd($datos);
        //$integrante = Integrante::findOrFail($integranteId);
        //dd($datos);

        $ambitos = $viaje->ambitos()->get();
        $montos = $viaje->montos()->get();

        $tipoPresupuestos = DB::table('tipo_presupuestos')->where('id', 2)->get();
        $template = 'viajes.pdfsolicitud';
        $presupuestos = $viaje->presupuestos()->get();

        $data = [
            'estado' => $datos->estado,
            'year' => $datos->periodo_nombre,
            'mes_desde' => Constants::MES_DESDE_VIAJES,
            'mes_hasta' => Constants::MES_HASTA_VIAJES,
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
            'scholar' => $datos->scholar,
            'titulo' => $datos->titulo,
            'egreso' => $datos->egresogrado,

            'unidad' => $datos->unidad,
            'direccion_unidad' => $datos->direccion_unidad,
            'telefono_unidad' => $datos->telefono_unidad,
            'cargo' => $datos->cargo,
            'dedicacion' => $datos->dedicacion,
            'beca_institucion' => $datos->beca_institucion,
            'beca_beca' => $datos->beca_beca,
            'beca_periodo' => $datos->beca_periodo,
            'beca_unlp' => $datos->beca_unlp,
            'unidadbeca' => $datos->unidadbeca,
            'resumen_beca' => $resumen_beca,

            'proyectosActuales' => $proyectosActuales,

            'facultadplanilla' => $datos->facultadplanilla_nombre,
            'objetivo' => $datos->objetivo,

            'tipoPresupuestos' => $tipoPresupuestos,
            'presupuestos' => $presupuestos,
            'carrerainv' => $datos->carrerainv_nombre,
            'organismo' => $datos->organismo_nombre,
            'categoria' => $datos->categoria_nombre,
            'sicadi' => $datos->sicadi_nombre,
            'tipo' => $datos->tipo,
            'ingreso_carrera' => $datos->ingreso_carrerainv,
            'unidadcarrera' => $datos->unidadcarrera,
            'motivo' => $datos->motivo,
            'ambitos' => $ambitos,
            'monto' => $datos->monto,
            'montos' => $montos,
            'objetivo' => $datos->objetivo,
            'relevanciaA' => $datos->relevanciaA,
            'congreso' => $datos->congreso,
            'congresonombre' => $datos->congresonombre,
            'titulotrabajo' => $datos->titulotrabajo,
            'autores' => $datos->autores,
            'nacional' => $datos->nacional,
            'lugartrabajo' => $datos->lugartrabajo,
            'trabajodesde' => $datos->trabajodesde,
            'trabajohasta' => $datos->trabajohasta,
            'relevancia' => $datos->relevancia,
            'resumen' => $datos->resumen,
            'link' => $datos->link,
            'modalidad' => $datos->modalidad,
            'profesor' => $datos->profesor,
            'lugarprofesor' => $datos->lugarprofesor,
            'libros' => $datos->libros,
            'compilados' => $datos->compilados,
            'capitulos' => $datos->capitulos,
            'articulos' => $datos->articulos,
            'congresos' => $datos->congresos,
            'patentes' => $datos->patentes,
            'intelectuales' => $datos->intelectuales,
            'informes' => $datos->informes,
            'tesis' => $datos->tesis,
            'becas' => $datos->becas,
            'tesinas' => $datos->tesinas,
            'generalB' => $datos->generalB,
            'especificoB' => $datos->especificoB,
            'actividadesB' => $datos->actividadesB,
            'cronogramaB' => $datos->cronogramaB,
            'justificacionB' => $datos->justificacionB,
            'aportesB' => $datos->aportesB,
            'relevanciaB' => $datos->relevanciaB,
            'objetivosC' => $datos->objetivosC,
            'planC' => $datos->planC,
            'relacionProyectoC' => $datos->relacionProyectoC,
            'aportesC' => $datos->aportesC,
            'actividadesC' => $datos->actividadesC,
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
        /*Log::debug('Valores de la solicitud:', [
            'carrerainv_id' => $solicitud->carrerainv_id,
            'organismo_id' => $solicitud->organismo_id,
            'ingreso_carrera' => $solicitud->ingreso_carrera,
            'unidadcarrera_id' => $solicitud->unidadcarrera_id,
        ]);*/

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

        $esBecario=0;
        //$beca = $solicitud->becas()->where('actual', true)->first(); // Beca actual
        $esBecarioUNLP=0;
        if ($solicitud->beca){
            $esBecario=1;
            $esBecarioUNLP=($solicitud->unlp)?1:0;
            if (empty($solicitud->institucion) || empty($solicitud->beca)|| empty($solicitud->periodobeca) || empty($solicitud->unidadbeca_id)) {
                $errores[] = 'Complete todos los campos de la Beca Actual en la pestaña Becas';
            }
            if ($esBecario && $esCarrera) {
                $errores[] = 'No puede ser becario/a y miembro de la carrera al mismo tiempo';
            }
            if (!$tieneCargo && !$esBecarioUNLP){
                $errores[] = 'Si no posee Cargo Docente, debe ser becario/a UNLP';
            }

        }

        $proyecto = $solicitud->proyectos()->where('seleccionado',1)->get();
        if ((!$esBecarioUNLP)&&(empty($proyecto))) {
            $errores[] = 'Debe seleccionar un proyecto';
        }


        if (empty($solicitud->facultadplanilla_id) || empty($solicitud->tipo) ) {
            $errores[] = 'Complete todos los campos de la pestaña Tipo';
        }

        if (empty($solicitud->motivo) ) {
            $errores[] = 'Debe seleccionar el motivo';
        }
        $ambitos = $solicitud->ambitos()->get();
        if ($ambitos->count()!=1) {
            $errores[] = 'Debe ingresar un solo lugar';
        }
        // Tu lógica para calcular el monto total
        /*$totalMonto = 0;
        $presupuestos = $solicitud->presupuestos()->get(); // Beca actual
        foreach ($presupuestos as $presupuesto) {
            $totalMonto += $presupuesto->monto;
        }*/
        // Validamos que haya seleccionado un monto
        if ($solicitud->monto <= 0) {
            $errores[] = 'Debe seleccionar un monto en la pestaña Montos.';
        }
// Validamos que el total de presupuestos coincida con el monto seleccionado
        /*elseif ($solicitud->monto != $totalMonto) {
            $errores[] = 'El total de la pestaña presupuesto debe ser igual al monto declarado en la pestaña Montos.';
        }*/


        if (empty($solicitud->curriculum)) {
            $errores[] = 'Falta subir el Curriculum';
        } else {
            $filePath = public_path($solicitud->curriculum);
            if (!file_exists($filePath)) {
                $errores[] = 'Hubo un error al subir el Curriculum, intente nuevamente, si el problema persiste envíenos un mail a viajes.secyt@presi.unlp.edu.ar';
            }
        }

        if ($solicitud->motivo == 'A) Reuniones Científicas'){

            if (empty($solicitud->objetivo) ) {
                $errores[] = 'Complete el campo OBJETIVOS DEL VIAJE en la pestaña Motivo';
            }
            if (empty($solicitud->relevanciaA) ) {
                $errores[] = 'Complete el campo Relevancia Institucional en la pestaña Motivo';
            }
            if (empty($solicitud->congresonombre) ) {
                $errores[] = 'Complete el campo Nombre de la Reunión Científica en la pestaña Motivo';
            }
            if (empty($solicitud->link) ) {
                $errores[] = 'Complete el campo Link de la Reunión Científica en la pestaña Motivo';
            }
            if (empty($solicitud->lugartrabajo) ) {
                $errores[] = 'Complete el campo Lugar en la pestaña Motivo';
            }
            if (empty($solicitud->trabajodesde) ) {
                $errores[] = 'Complete el campo Inicio en la pestaña Motivo';
            }
            if (empty($solicitud->trabajohasta) ) {
                $errores[] = 'Complete el campo Fin en la pestaña Motivo';
            }
            if (empty($solicitud->relevancia) ) {
                $errores[] = 'Complete el campo Relevancia del evento en la pestaña Motivo';
            }
            if (empty($solicitud->titulotrabajo) ) {
                $errores[] = 'Complete el campo Título del Trabajo en la pestaña Motivo';
            }
            if (empty($solicitud->modalidad) ) {
                $errores[] = 'Complete el campo Modalidad de la presentación en la pestaña Motivo';
            }

            $fechaDesde = Carbon::parse($solicitud->trabajodesde);
            $fechaHasta = Carbon::parse($solicitud->trabajohasta);

            if ($fechaHasta->lessThanOrEqualTo($fechaDesde)) {
                $errores[] = "La fecha 'Inicio' debe ser mayor que la fecha 'Fin'.";
            }

            foreach ($ambitos as $ambito){
                //dd($ambito);
                $rangoInicio = \DateTime::createFromFormat('!d/m/Y', Constants::RANGO_INI_VIAJES.Constants::YEAR_VIAJES);
                $yearAgo = intval(Constants::YEAR_VIAJES)+1;
                $rangoFin = \DateTime::createFromFormat('!d/m/Y', Constants::RANGO_FIN_VIAJES.$yearAgo);
                //dd($ambito->desde);
                $desdeAmbito = \DateTime::createFromFormat('Y-m-d H:i:s', $ambito->desde);
                $hastaAmbito = \DateTime::createFromFormat('Y-m-d H:i:s', $ambito->hasta);

                /*Log::debug('Valores iniciales: ', [
                    'fechaDesde' => var_export($desdeAmbito, true),
                    'rangoInicio' => var_export($rangoInicio, true),
                    'rangoFin' => var_export($rangoFin, true),
                ]);*/

                // Verificar que ambas fechas estén dentro del rango
                if ($desdeAmbito < $rangoInicio || $desdeAmbito > $rangoFin) {
                    $errores[] = 'Fecha desde fuera del rango '.Constants::RANGO_INI_VIAJES.Constants::YEAR_VIAJES.' - '.Constants::RANGO_FIN_VIAJES.$yearAgo.' en Lugar';
                }
                /*if ($hastaAmbito < $rangoInicio || $hastaAmbito > $rangoFin) {
                    $errores[] = 'Fecha hasta fuera del rango '.Constants::RANGO_INI_VIAJES.Constants::YEAR_VIAJES.' - '.Constants::RANGO_FIN_VIAJES.$yearAgo.' en Lugar';
                }*/
                if ($solicitud->trabajodesde && $solicitud->trabajohasta){
                    $desdeTrabajo = \DateTime::createFromFormat('Y-m-d H:i:s', $solicitud->trabajodesde);
                    $hastaTrabajo = \DateTime::createFromFormat('Y-m-d H:i:s', $solicitud->trabajohasta);

                    if ($desdeTrabajo<$desdeAmbito||$hastaTrabajo>$hastaAmbito) {
                        $errores[]='El período del congreso no está contenido en su totalidad en el período del lugar cargado';
                    }
                }

            }
            if (str_word_count($solicitud->resumen,0)<Constants::MAX_PALABRAS_RESUMEN_VIAJES) {

                $errores[]='El texto del resumen en la pestaña motivo debe tener al menos '.Constants::MAX_PALABRAS_RESUMEN_VIAJES.' palabras';
            }


        }

        if ($solicitud->motivo == 'B) Estadía de trabajo para investigar en ámbitos académicos externos a la UNLP'){

            if (empty($solicitud->generalB) ) {
                $errores[] = 'Complete el campo Objetivo general de la estadía en la pestaña Motivo';
            }
            if (empty($solicitud->especificoB) ) {
                $errores[] = 'Complete el campo Objetivos específicos de la estadía en la pestaña Motivo';
            }
            if (empty($solicitud->actividadesB) ) {
                $errores[] = 'Complete el campo Plan de trabajo de investigación a realizar en el período en la pestaña Motivo';
            }
            if (empty($solicitud->cronogramaB) ) {
                $errores[] = 'Complete el campo Cronograma en la pestaña Motivo';
            }
            if (empty($solicitud->justificacionB) ) {
                $errores[] = 'Complete el campo Justificación de la realización de la estadía y relación con el proyecto de investigación en el que participa en la pestaña Motivo';
            }
            if (empty($solicitud->aportesB) ) {
                $errores[] = 'Complete el campo Relevancia Institucional en la pestaña Motivo';
            }

            if (empty($solicitud->relevanciaB) ) {
                $errores[] = 'Complete el campo Relevancia del lugar donde realiza la estadía. Justifique la elección del lugar en la pestaña Motivo';
            }

            if (empty($solicitud->invitacion)) {
                $errores[] = 'Falta subir la Invitación del grupo receptor';
            } else {
                $filePath = public_path($solicitud->invitacion);
                if (!file_exists($filePath)) {
                    $errores[] = 'Hubo un error al subir la Invitación del grupo receptor, intente nuevamente, si el problema persiste envíenos un mail a viajes.secyt@presi.unlp.edu.ar';
                }
            }



        }

        if ($solicitud->motivo == 'C) Estadía de Trabajo en la UNLP para un Investigador Invitado'){
            if ($solicitud->tipo == 'Investigador En Formación'){
                $errores[] = 'Solo pueden solicitar subsidios Tipo C los Investigadores Formados';
            }
            if (empty($solicitud->profesor) ) {
                $errores[] = 'Complete el campo Profesor Visitante en la pestaña Motivo';
            }
            if (empty($solicitud->lugarprofesor) ) {
                $errores[] = 'Complete el campo Lugar de Origen del Profesor Visitante en la pestaña Motivo';
            }
            if (empty($solicitud->objetivosC) ) {
                $errores[] = 'Complete el campo Objetivo de investigación de la estadía en la pestaña Motivo';
            }
            if (empty($solicitud->planC) ) {
                $errores[] = 'Complete el campo Plan de trabajo de investigación a realizar en el período en la pestaña Motivo';
            }
            if (empty($solicitud->relacionProyectoC) ) {
                $errores[] = 'Complete el campo Relación del plan de trabajo del investigador invitado con el proyecto de investigación acreditado del grupo receptor en la pestaña Motivo';
            }
            if (empty($solicitud->aportesC) ) {
                $errores[] = 'Complete el campo Aportes del desarrollo del plan de trabajo al grupo de investigación, Unidad de Investigación y/o Unidad Académica en la pestaña Motivo';
            }

            if (empty($solicitud->actividadesC) ) {
                $errores[] = 'Complete el campo Otras actividades en la pestaña Motivo';
            }

            if (empty($solicitud->cvprofesor)) {
                $errores[] = 'Falta subir el Currículum del profesor visitante';
            } else {
                $filePath = public_path($solicitud->cvprofesor);
                if (!file_exists($filePath)) {
                    $errores[] = 'Hubo un error al subir el Currículum del profesor visitante, intente nuevamente, si el problema persiste envíenos un mail a viajes.secyt@presi.unlp.edu.ar';
                }
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
                    'asunto' => 'Solicitud de Subsidios para Viajes y/o Estadías ('.Constants::MES_DESDE_VIAJES.' '.$year.' - '.Constants::MES_HASTA_VIAJES.' '.(intval($year)+1).')',
                    'year' => $year,
                    'mes_desde' => Constants::MES_DESDE_VIAJES,
                    'mes_hasta' => Constants::MES_HASTA_VIAJES,
                    'investigador' => $solicitud->investigador->persona->apellido.', '.$solicitud->investigador->persona->nombre.' ('.$solicitud->investigador->persona->cuil.')',
                    'motivo' => $solicitud->motivo,
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
                'asunto' => 'Admisión de Solicitud de Subsidios para Viajes y/o Estadías ('.Constants::MES_DESDE_VIAJES.' '.$year.' - '.Constants::MES_HASTA_VIAJES.' '.(intval($year)+1).')',
                'year' => $year,
                'mes_desde' => Constants::MES_DESDE_VIAJES,
                'mes_hasta' => Constants::MES_HASTA_VIAJES,
                'investigador' => $viaje->investigador->persona->apellido.', '.$viaje->investigador->persona->nombre.' ('.$viaje->investigador->persona->cuil.')',
                'motivo' => $viaje->motivo,
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

        $input = $this->sanitizeInput($request->all());

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
                'asunto' => 'NO Admisión de Solicitud de Subsidios para Viajes y/o Estadías ('.Constants::MES_DESDE_VIAJES.' '.$year.' - '.Constants::MES_HASTA_VIAJES.' '.(intval($year)+1).')',
                'year' => $year,
                'mes_desde' => Constants::MES_DESDE_VIAJES,
                'mes_hasta' => Constants::MES_HASTA_VIAJES,
                'investigador' => $viaje->investigador->persona->apellido.', '.$viaje->investigador->persona->nombre.' ('.$viaje->investigador->persona->cuil.')',
                'motivo' => $viaje->motivo,
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

            $investigador->titulo_id = $viaje->titulo_id;
            //$investigador->titulopost_id = $viaje->titulopost_id;
            $investigador->unidad_id = $viaje->unidad_id;
            /*$beca = $viaje->becas()->where('actual', true)->first(); // Beca actual
            if (!empty($beca)){
                $investigador->institucion = $beca->institucion;
                $investigador->beca = $beca->beca;
            }
            else{
                $investigador->institucion = null;
                $investigador->beca = null;
            }*/



            $investigador->save();
        }
        $persona = $investigador->persona;  // Obtener la persona asociada

        // Actualizar los datos de la persona aquí
        if ($persona) {

            $persona->email = $viaje->email;
            //$persona->nacimiento = $viaje->nacimiento;
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

        /*$becas = $viaje->becas()->get(); // todas

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



        }*/
    }

}
