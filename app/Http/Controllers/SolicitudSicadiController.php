<?php

namespace App\Http\Controllers;


use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Models\SolicitudSicadi;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;


class SolicitudSicadiController extends Controller

{
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


        return view ('solicitud_sicadis.index');
    }

    public function dataTable(Request $request)
    {
        $columnas = ['solicitud_sicadis.nombre', 'solicitud_sicadis.apellido', 'cuil','presentacion_ua','categoria_spu', 'categoria_solicitada','mecanismo','area']; // Define las columnas disponibles
        $columnaOrden = $columnas[$request->input('order.0.column')];
        $orden = $request->input('order.0.dir');
        $busqueda = $request->input('search.value');



        // Consulta base
        $query = SolicitudSicadi::select('solicitud_sicadis.id as id', 'solicitud_sicadis.nombre as persona_nombre', DB::raw("CONCAT(solicitud_sicadis.apellido, ', ', solicitud_sicadis.nombre) as persona_apellido"), 'cuil','presentacion_ua','categoria_spu', 'categoria_solicitada','mecanismo','area')
            ;

        // Aplicar la búsqueda
        if (!empty($busqueda)) {
            $query->where(function ($query) use ($columnas, $busqueda) {
                foreach ($columnas as $columna) {
                    $query->orWhere($columna, 'like', "%$busqueda%");
                }
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

        return view('solicitud_sicadis.create');
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
            'apellido' => 'required',
            'email_institucional' => 'required|email',
            'cuil' => 'required|regex:/^\d{2}-\d{8}-\d{1}$/', // Validación de cuil
            'foto' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        $input = $request->all();

        // Manejo de la imagen
        $input['foto'] ='';
        if ($files = $request->file('foto')) {
            $image = $request->file('foto');
            $name = $input['cuil'].'.'.$image->getClientOriginalExtension();
            $destinationPath = public_path('/images/sicadi');
            $image->move($destinationPath, $name);
            $input['foto'] = "$name";
        }

        DB::beginTransaction();
        $ok = 1;

        try {


            // Crear la persona y luego el investigador
            $investigador = SolicitudSicadi::create($input);

        }catch(QueryException $ex){


                    // Si no es por una clave duplicada, maneja la excepción de manera general
                    $error=$ex->getMessage();


                $ok=0;

            }




        if ($ok){


            DB::commit();
            $respuestaID = 'success';
            $respuestaMSJ = 'Investigador creado con éxito';
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
        //dd($investigador);
        return view('solicitud_sicadis.edit',compact('investigador'));
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
            'nombre' => 'required',
            'apellido' => 'required',
            'email_institucional' => 'required|email',
            'cuil' => 'required|regex:/^\d{2}-\d{8}-\d{1}$/', // Validación de cuil
            'foto' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        $input = $request->all();

        // Manejo de la imagen
        $input['foto'] ='';
        if ($files = $request->file('foto')) {
            $image = $request->file('foto');
            $name = $input['cuil'].'.'.$image->getClientOriginalExtension();
            $destinationPath = public_path('/images/sicadi');
            $image->move($destinationPath, $name);
            $input['foto'] = "$name";
        }
        $investigador = SolicitudSicadi::find($id);
        DB::beginTransaction();
        $ok = 1;

        try {
            $investigador->update($input);


        }catch(QueryException $ex){



                    // Si no es por una clave duplicada, maneja la excepción de manera general
                    $error=$ex->getMessage();


                $ok=0;




        }

        if ($ok){


            DB::commit();
            $respuestaID = 'success';
            $respuestaMSJ = 'Investigador modificado con éxito';
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
        $investigador = SolicitudSicadi::findOrFail($id);

        DB::beginTransaction();
        try {
            // Ruta de la imagen
            $imagePath = public_path('/images/sicadi/' . $investigador->foto);

            // Verificar si la ruta es un archivo y si existe
            if (!empty($investigador->foto) && file_exists($imagePath) && is_file($imagePath)) {
                unlink($imagePath);
            }

            // Eliminar el investigador
            $investigador->delete();

            DB::commit();
            return redirect()->route('solicitud_sicadis.index')
                ->with('success', 'Investigador eliminado con éxito');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('solicitud_sicadis.index')
                ->with('error', 'Error al eliminar el investigador: ' . $e->getMessage());
        }
    }

    public function getInvestigadorById($id)
    {
        try {
            $investigador = SolicitudSicadi::findOrFail($id);
            return response()->json(['success' => true, 'data' => $investigador], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        }
    }

    public function filterInvestigadores(Request $request)
    {
        $query = SolicitudSicadi::query();

        // Añade los filtros según los parámetros proporcionados
        if ($request->has('nombre')) {
            $query->where('nombre', 'like', '%' . $request->input('nombre') . '%');
        }

        if ($request->has('apellido')) {
            $query->where('apellido', 'like', '%' . $request->input('apellido') . '%');
        }

        if ($request->has('cuil')) {
            $query->where('cuil', $request->input('cuil'));
        }

        if ($request->has('presentacion_ua')) {
            $query->where('presentacion_ua', $request->input('presentacion_ua'));
        }

        // Añade más filtros según sea necesario

        try {
            $investigadores = $query->get();
            return response()->json(['success' => true, 'data' => $investigadores], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }


}
