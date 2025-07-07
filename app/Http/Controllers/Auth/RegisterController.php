<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use App\Traits\SanitizesInput;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use DB;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request; // Importar la clase Request
use Spatie\Permission\Models\Role; // Importa la clase Role
use Illuminate\Support\Facades\Log;

class RegisterController extends Controller
{

    use SanitizesInput;
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    // Método para mostrar el formulario de registro
    public function showRegistrationForm()
    {
        // Aquí podrías cargar los datos necesarios para el campo "facultad_id" si es necesario
        $facultades = DB::table('facultads')->pluck('nombre', 'id'); // Obtener todas las facultades directamente desde la tabla

        return view('auth.register', compact('facultades'));
    }


    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'max:255',
                'unique:users',
                'regex:/^[^@]+@[^@]+\.[^@]+$/i',
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*#?&.,;:])[A-Za-z\d@$!%*#?&.,;:]{8,}$/'
            ],
            'cuil' => 'nullable|regex:/^\d{2}-\d{8}-\d{1}$/',
            'facultad_id' => 'nullable|exists:facultads,id',
        ], [
            'password.regex' => 'La contraseña debe tener al menos 8 caracteres, incluyendo una letra mayúscula, una minúscula, un número y un carácter especial.',
        ]);
    }


// Método para registrar un nuevo usuario
    public function register(Request $request)
    {
        try {
            $this->validator($request->all())->validate();

            $user = $this->create($this->sanitizeInput($request->all()));

            // Autenticar al usuario después de registrarse si es necesario
            // Auth::login($user);

            return redirect()->route('home'); // Redireccionar a donde sea apropiado después del registro
        } catch (QueryException $e) {
            // Si se produce un error de duplicación de entrada, manejarlo aquí
            if ($e->errorInfo[1] == 1062) {
                //og::Info($e->getMessage());
                // Verificar si el error es debido a duplicación de correo electrónico
                if (strpos($e->getMessage(), 'for key \'email\'') !== false) {
                    return redirect()->back()->withInput($request->except('password'))->withErrors(['email' => 'El correo electrónico ya está en uso']);
                }
                // Verificar si el error es debido a duplicación de cuil
                elseif (strpos($e->getMessage(), 'for key \'cuil\'') !== false) {
                    return redirect()->back()->withInput($request->except('password'))->withErrors(['cuil' => 'El CUIL ya está en uso']);
                }
            }

            // En caso de otros errores de consulta, manejarlos según sea necesario
            return redirect()->back()->withErrors(['error' => 'Hubo un error al procesar la solicitud. Por favor, inténtelo de nuevo más tarde.']);
        }
    }
    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        // Crea el usuario
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'cuil' => $data['cuil'],
            'facultad_id' => $data['facultad_id'],
        ]);

        // Asigna el rol "solicitante" al usuario recién creado
        $role = Role::where('name', 'Solicitante')->first();
        $user->assignRole($role);

        // Asigna el rol "director" al usuario recién creado
        /*$role = Role::where('name', 'Director')->first();
        $user->assignRole($role);*/

        return $user;
    }
}
