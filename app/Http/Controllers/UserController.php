<?php

namespace App\Http\Controllers;


use App\Traits\SanitizesInput;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use DB;
use Hash;
use Illuminate\Support\Arr;
use App\Http\Controllers\ProyectoController;


class UserController extends Controller
{
    use SanitizesInput;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
        $this->middleware('permission:usuario-listar|usuario-crear|usuario-editar|usuario-eliminar', ['only' => ['index','store','dataTable']]);
        $this->middleware('permission:usuario-crear', ['only' => ['create','store']]);
        $this->middleware('permission:usuario-editar', ['only' => ['edit','update','perfil','updatePerfil']]);
        $this->middleware('permission:usuario-eliminar', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        /*$data = User::orderBy('id','DESC')->paginate(5);
        return view('users.index',compact('data'))
            ->with('i', ($request->input('page', 1) - 1) * 5);*/

        $users = User::all();
        return view ('users.index',compact('users'));
    }

    public function dataTable(Request $request)
    {
        $columnas = ['name', 'cuil', 'email']; // Define las columnas disponibles
        $columnaOrden = $columnas[$request->input('order.0.column')];
        $orden = $request->input('order.0.dir');
        $busqueda = $request->input('search.value');

        $query = User::query()->with('roles'); // Carga la relación de roles

        // Unir la tabla de universidades para poder ordenar y filtrar por el nombre de la universidad
        /*$query->select('titulos.id as id','titulos.nombre as titulo_nombre', 'nivel', 'universidads.nombre as universidad_nombre');
        $query->leftJoin('universidads', 'titulos.universidad_id', '=', 'universidads.id');*/

        foreach ($columnas as $columna) {
            $query->orWhere($columna, 'like', "%$busqueda%");
        }
        $query->orWhereHas('roles', function ($query) use ($busqueda) {
            $query->where('name', 'like', "%$busqueda%");
        });

        // Cargar la relación universidad
        //$query->with('universidad');

        // Aplica la paginación utilizando el método paginate
        //$datos = $query->orderBy($columnaOrden, $orden)->paginate($request->input('length'));


        // Obtener la cantidad total de registros después de aplicar el filtro de búsqueda
        $recordsFiltered = $query->count();


        $datos = $query->orderBy($columnaOrden, $orden)->skip($request->input('start'))->take($request->input('length'))->get();

        // Obtener la cantidad total de registros sin filtrar
        $recordsTotal = User::count();



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
        $roles = Role::pluck('name','name')->all();
        $facultades = DB::table('facultads')->pluck('nombre', 'id'); // Obtener todas las facultades directamente desde la tabla
        return view('users.create', compact('roles', 'facultades'));
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
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'email' => [
                'required',
                'regex:/^[^@]+@[^@]+\.[^@]+$/i',
                'unique:users,email',
            ],
            'password' => 'required|min:6|confirmed',
            'roles' => 'required',
            'facultad_id' => 'nullable|exists:facultads,id', // Validación de facultad_id
            'cuil' => 'nullable|regex:/^\d{2}-\d{8}-\d{1}$/', // Validación de cuil
            'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);






        $input = $this->sanitizeInput($request->all());
        $input['password'] = Hash::make($input['password']);

        if ($files = $request->file('image')) {
            $image = $request->file('image');
            $name = Str::uuid().'.'.$image->getClientOriginalExtension();
            $destinationPath = public_path('/images');
            $image->move($destinationPath, $name);
            $input['image'] = "$name";
        }

        $user = User::create($input);
        $user->assignRole($request->input('roles'));

        return redirect()->route('users.index')
            ->with('success','Usuario creado con éxito');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find($id);
        return view('users.show',compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::find($id);
        $roles = Role::pluck('name','name')->all();

        $userRole = $user->roles->pluck('name','name')->all();

        $facultades = DB::table('facultads')->pluck('nombre', 'id');
        $userFacultad = $user->facultad_id; // Facultad asignada al usuario
        return view('users.edit', compact('user', 'roles', 'userRole', 'facultades', 'userFacultad'));
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
            'name' => 'required',
            'email' => [
                'required',
                'email',
                'regex:/^[^@]+@[^@]+\.[^@]+$/i',
                'unique:users,email,'.$id,
            ],

            'password' => 'confirmed',
            'roles' => 'required',
            'facultad_id' => 'nullable|exists:facultads,id', // Validación de facultad_id
            'cuil' => 'nullable|regex:/^\d{2}-\d{8}-\d{1}$/', // Validación de cuil
            'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $input = $this->sanitizeInput($request->all());
        if(!empty($input['password'])){
            $input['password'] = Hash::make($input['password']);
        }else{
            $input = Arr::except($input,array('password'));
        }
        $user = User::find($id);
        if ($files = $request->file('image')) {
            $image = $request->file('image');
            $extension = strtolower($image->getClientOriginalExtension());

            if ($extension === 'svg') {
                return back()->withErrors(['image' => 'El formato SVG no está permitido.']);
            }

            // Eliminar imagen anterior
            if (!empty($user->image)) {
                $rutaAnterior = public_path('images/' . $user->image);
                if (file_exists($rutaAnterior)) {
                    unlink($rutaAnterior); // Borra físicamente la imagen anterior
                }
            }


            $name = Str::uuid().'.'.$extension;
            $destinationPath = public_path('/images');
            $image->move($destinationPath, $name);
            $input['image'] = "$name";
        }
        if ($request->has('delete_image') && $user->image) {
            $imagePath = public_path('images/' . $user->image);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
            $input['image'] = null;
        }


        $user->update($input);
        DB::table('model_has_roles')->where('model_id',$id)->delete();

        $user->assignRole($request->input('roles'));

        return redirect()->route('users.index')
            ->with('success','Usuario modificado con éxito');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        User::find($id)->delete();
        return redirect()->route('users.index')
            ->with('success','Usuario eliminado con éxito');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function perfil(Request $request)
    {
        $id=$request->get('idUser');
        if (auth()->id() != $id) {
            abort(403, 'No autorizado.');
        }
        $user = User::find($id);


        $facultades = DB::table('facultads')->pluck('nombre', 'id');
        $userFacultad = $user->facultad_id; // Facultad asignada al usuario
        return view('users.perfil',compact('user','facultades','userFacultad'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updatePerfil(Request $request)
    {
       $id=$request->get('idUser');
        if (auth()->id() != $id) {
            abort(403, 'No autorizado.');
        }
        $this->validate($request, [
            'name' => 'required',
            'email' => [
                'required',
                'email',
                'regex:/^[^@]+@[^@]+\.[^@]+$/i',
                'unique:users,email,'.$id,
            ],
            'password' => 'confirmed',
            'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $input = $this->sanitizeInput($request->all());
        if(!empty($input['password'])){
            $input['password'] = Hash::make($input['password']);
        }else{
            $input = Arr::except($input,array('password'));
        }
        $user = User::find($id);
        if ($files = $request->file('image')) {
            $image = $request->file('image');
            $extension = strtolower($image->getClientOriginalExtension());

            if ($extension === 'svg') {
                return back()->withErrors(['image' => 'El formato SVG no está permitido.']);
            }

            // Eliminar imagen anterior
            if (!empty($user->image)) {
                $rutaAnterior = public_path('images/' . $user->image);
                if (file_exists($rutaAnterior)) {
                    unlink($rutaAnterior); // Borra físicamente la imagen anterior
                }
            }


            $name = Str::uuid().'.'.$extension;
            $destinationPath = public_path('/images');
            $image->move($destinationPath, $name);
            $input['image'] = "$name";
        }
        if ($request->has('delete_image') && $user->image) {
            $imagePath = public_path('images/' . $user->image);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
            $input['image'] = null;
        }

        $user = User::find($id);
        $user->update($input);


        return redirect()->route('users.index')
            ->with('success','Perfil modificado con éxito');
    }

    public function selectRol()
    {
        //dd('Estoy dentro de selectRol');
        $user = auth()->user();
        $roles = $user->roles;
        //$roles = $user->roles->pluck('name','name')->all();

        return view('users.select-rol', compact('roles'));
    }

    public function saveSelectedRol(Request $request)
    {
        /*$request->validate([
            'rol_id' => 'required|exists:roles,id,user_id,' . auth()->id()
        ]);*/
        //dd($request->rol_id);
        session(['selected_rol' => $request->rol_id]);
        if ($request->rol_id==2){
            $proyectoController = new ProyectoController();
            $proyectoController->verificarDirector();
        }
        else{
            // Obtener el rol seleccionado
            $role = Role::find($request->rol_id);

            // Verificar si el rol tiene el permiso 'proyecto-listar'
            if ($role->permissions->contains('name', 'proyecto-listar')) {
                session(['es_director' => 1]);
            }
        }

        // Obtener el rol seleccionado por el usuario
        //$selectedRole = Role::find($request->rol_id);

        // Obtener el usuario autenticado
        //$user = auth()->user();

        // Sincronizar los permisos del usuario con los permisos del rol seleccionado
        //$user->syncPermissions($selectedRole->permissions);

        //dd(session('selected_rol'));
        // Redirigir al usuario a la ruta deseada
        return redirect()->route('home'); // Cambia 'home' por la ruta que desees
    }


}
