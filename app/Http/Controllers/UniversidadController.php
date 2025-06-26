<?php

namespace App\Http\Controllers;

use App\Traits\SanitizesInput;
use Illuminate\Http\Request;
use App\Models\Universidad;
class UniversidadController extends Controller
{
    use SanitizesInput;
/**
* Display a listing of the resource.
*
* @return \Illuminate\Http\Response
*/
    function __construct()
    {
        $this->middleware('permission:universidad-listar|universidad-crear|universidad-editar|universidad-eliminar', ['only' => ['index','store']]);
        $this->middleware('permission:universidad-crear', ['only' => ['create','store']]);
        $this->middleware('permission:universidad-editar', ['only' => ['edit','update']]);
        $this->middleware('permission:universidad-eliminar', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $universidads = Universidad::all();
        return view ('universidads.index',compact('universidads'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        return view('universidads.create');
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


        $input = $this->sanitizeInput($request->all());


        $universidad = Universidad::create($input);


        return redirect()->route('universidads.index')
            ->with('success','Universidad creada con éxito');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $universidad = Universidad::find($id);
        return view('universidads.show',compact('universidad'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $universidad = Universidad::find($id);


        return view('universidads.edit',compact('universidad'));
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




        $universidad = Universidad::find($id);
        $universidad->update($input);



        return redirect()->route('universidads.index')
            ->with('success','Universidad modificada con éxito');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {


        Universidad::find($id)->delete();

        return redirect()->route('universidads.index')
            ->with('success','Universidad eliminada con éxito');
    }
}
