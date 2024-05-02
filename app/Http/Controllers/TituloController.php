<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Titulo;

class TituloController extends Controller
{
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

        $titulos = Titulo::all();
        return view ('titulos.index',compact('titulos'));
    }

    public function dataTable(Request $request)
    {
        $columnas = ['nombre', 'nivel']; // Define las columnas disponibles
        $columnaOrden = $columnas[$request->input('order.0.column')];
        $orden = $request->input('order.0.dir');
        $busqueda = $request->input('search.value');

        $query = Titulo::query();

        foreach ($columnas as $columna) {
            $query->orWhere($columna, 'like', "%$busqueda%");
        }

        $datos = $query->orderBy($columnaOrden, $orden)
            ->paginate($request->input('length'));

        return response()->json($datos);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        return view('titulos.create');
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
            'nombre' => 'required'

        ]);


        $input = $request->all();


        $titulo = Titulo::create($input);


        return redirect()->route('titulos.index')
            ->with('success','Titulo creada con éxito');
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


        return view('titulos.edit',compact('titulo'));
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




        $titulo = Titulo::find($id);
        $titulo->update($input);



        return redirect()->route('titulos.index')
            ->with('success','Titulo modificada con éxito');
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
            ->with('success','Titulo eliminada con éxito');
    }
}
