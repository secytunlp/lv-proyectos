<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\ProyectoController;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
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
        $this->middleware('guest')->except('logout');
    }

    protected function authenticated(\Illuminate\Http\Request $request, $user)
    {

        //log::info('Roles: '.$user->roles->count() );




        if ($user->roles->count() > 1) {
            //dd($user);
            return redirect()->route('select-rol');
        } else {
            // Si el usuario tiene un solo rol, guardarlo en la sesión
            $role = $user->roles->first();
            if ($role->id==2){
                $proyectoController = new ProyectoController();
                $proyectoController->verificarDirector();
            }
            else{
                // Verificar si el rol tiene el permiso 'proyecto-listar'
                if ($role->permissions->contains('name', 'proyecto-listar')) {
                    session(['es_director' => 1]);
                }
            }
            session(['selected_rol' => $role->id]);
        }

        return redirect()->intended($this->redirectPath());

    }




}
