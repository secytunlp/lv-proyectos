<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Titulo;
use App\Models\Universidad;
use App\Traits\SanitizesInput;
class TituloController extends Controller
{
    use SanitizesInput;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
        $this->middleware('permission:titulo-listar|titulo-crear|titulo-editar|titulo-eliminar', ['only' => ['index','store','dataTable']]);
        $this->middleware('permission:titulo-crear', ['only' => ['create','store']]);
        $this->middleware('permission:titulo-editar', ['only' => ['edit','update']]);
        $this->middleware('permission:titulo-eliminar', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        //$titulos = Titulo::all();
        return view ('titulos.index');
    }

    public function dataTable(Request $request)
    {
        $columnas = ['titulos.nombre', 'nivel', 'universidads.nombre']; // Define las columnas disponibles
        $columnaOrden = $columnas[$request->input('order.0.column')];
        $orden = $request->input('order.0.dir');
        $busqueda = $request->input('search.value');

        $query = Titulo::query();

        // Unir la tabla de universidades para poder ordenar y filtrar por el nombre de la universidad
        $query->select('titulos.id as id','titulos.nombre as titulo_nombre', 'nivel', 'universidads.nombre as universidad_nombre');
        $query->leftJoin('universidads', 'titulos.universidad_id', '=', 'universidads.id');

        foreach ($columnas as $columna) {
            $query->orWhere($columna, 'like', "%$busqueda%");
        }


        // Cargar la relación universidad
       //$query->with('universidad');

        // Aplica la paginación utilizando el método paginate
        //$datos = $query->orderBy($columnaOrden, $orden)->paginate($request->input('length'));


        // Obtener la cantidad total de registros después de aplicar el filtro de búsqueda
        $recordsFiltered = $query->count();


        $datos = $query->orderBy($columnaOrden, $orden)->skip($request->input('start'))->take($request->input('length'))->get();

        // Obtener la cantidad total de registros sin filtrar
        $recordsTotal = Titulo::count();



        return response()->json([
            'data' => $datos, // Obtener solo los elementos paginados
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
        $universidads=Universidad::orderBy('nombre','ASC')->get();
        $universidads = $universidads->pluck('nombre', 'id')->prepend('','');
        return view('titulos.create',compact('universidads'));
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
            'nivel' => 'required',
            'universidad_id' => 'required',
        ]);


        $input = $this->sanitizeInput($request->all());


        $titulo = Titulo::create($input);


        return redirect()->route('titulos.index')
            ->with('success','Titulo creado con éxito');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $titulo = Titulo::find($id);
        return view('titulos.show',compact('titulo'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $titulo = Titulo::find($id);

        $universidads=Universidad::orderBy('nombre','ASC')->get();
        $universidads = $universidads->pluck('nombre', 'id')->prepend('','');
        return view('titulos.edit',compact('titulo','universidads'));
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

        $input = $this->sanitizeInput($request->all());



        $titulo = Titulo::find($id);
        $titulo->update($input);



        return redirect()->route('titulos.index')
            ->with('success','Titulo modificado con éxito');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {


        Titulo::find($id)->delete();

        return redirect()->route('titulos.index')
            ->with('success','Titulo eliminado con éxito');
    }
}
