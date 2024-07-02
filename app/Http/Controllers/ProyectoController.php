<?php

namespace App\Http\Controllers;

use App\Models\Proyecto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProyectoController extends Controller
{

    function __construct()
    {
        $this->middleware('permission:proyecto-listar|proyecto-crear|proyecto-editar|proyecto-eliminar', ['only' => ['index','store','dataTable']]);
        $this->middleware('permission:proyecto-crear', ['only' => ['create','store']]);
        $this->middleware('permission:proyecto-editar', ['only' => ['edit','update']]);
        $this->middleware('permission:proyecto-eliminar', ['only' => ['destroy']]);
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
        return view ('proyectos.index');
    }

    public function dataTable(Request $request)
    {
        $columnas = ['proyectos.tipo','proyectos.codigo',  'proyectos.titulo', 'personas.apellido', 'proyectos.inicio', 'proyectos.fin', 'facultads.nombre','proyectos.estado']; // Define las columnas disponibles
        $columnaOrden = $columnas[$request->input('order.0.column')];
        $orden = $request->input('order.0.dir');
        $busqueda = $request->input('search.value');



        // Consulta base
        $query = Proyecto::select('proyectos.id as id', 'proyectos.tipo as proyecto_tipo','proyectos.codigo',  'proyectos.titulo', DB::raw("CONCAT(personas.apellido, ', ', personas.nombre) as director"), 'proyectos.inicio', 'proyectos.fin', 'facultads.nombre as facultad_nombre','proyectos.estado')

            ->leftJoin('facultads', 'proyectos.facultad_id', '=', 'facultads.id')
        ->leftJoin('integrantes', function ($join) {
        $join->on('proyectos.id', '=', 'integrantes.proyecto_id')
            ->where('integrantes.tipo', '=', 'Director');
    })
        ->leftJoin('investigadors', 'integrantes.investigador_id', '=', 'investigadors.id')
        ->leftJoin('personas', 'investigadors.persona_id', '=', 'personas.id');

        // Aplicar la búsqueda
        if (!empty($busqueda)) {
            $query->where(function ($query) use ($columnas, $busqueda) {
                foreach ($columnas as $columna) {
                    $query->orWhere($columna, 'like', "%$busqueda%");
                }
            });
        }
        $selectedRoleId = session('selected_rol');
        if ($selectedRoleId==3){
            $user = auth()->user();
            $currentDate = date('Y-m-d');

            $query->where(function ($query) use ($user, $currentDate) {
                $query->where('personas.cuil', '=', $user->cuil)
                    ->where('proyectos.estado', '=', 'Acreditado')
                    ->where('proyectos.inicio', '<=', $currentDate)
                    ->where('proyectos.fin', '>=', $currentDate);
            });
        }

        if ($selectedRoleId==4){
            $user = auth()->user();
            //$currentDate = date('Y-m-d');

            $query->where(function ($query) use ($user) {
                $query->where('proyectos.facultad_id', '=', $user->facultad_id)
                    ->where('proyectos.estado', '=', 'Acreditado');
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
        $recordsTotal = Proyecto::count();

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
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
