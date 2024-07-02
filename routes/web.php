<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;

use App\Http\Controllers\PersonalController;
use App\Http\Controllers\UniversidadController;
use App\Http\Controllers\TituloController;
use App\Http\Controllers\InvestigadorController;
use App\Http\Controllers\ProyectoController;
use App\Http\Controllers\IntegranteController;
use App\Http\Controllers\IntegranteEstadoController;
use App\Http\Controllers\SolicitudSicadiController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/', function () {
    return redirect(route('login'));
});

Route::get('/error-403', function () {
    return view('errors.403');
})->name('error-403');

Auth::routes();




Route::group(['middleware' => 'auth'], function() {
    Route::get('select-rol', [UserController::class, 'selectRol'])->name('select-rol');
    Route::post('save-selected-rol', [UserController::class, 'saveSelectedRol'])->name('save-selected-rol');
});

Route::group(['middleware' => ['auth', 'CheckSelectedRolePermissions']], function() {
    Route::get('/home', [ProyectoController::class, 'index'])->name('home');
    Route::resource('roles', RoleController::class);
    Route::resource('users', UserController::class);
    Route::get('perfil', [UserController::class, 'perfil'])->name('users.perfil');
    Route::post('updatePerfil', [UserController::class, 'updatePerfil'])->name('users.updatePerfil');
    Route::post('user-datatable', [UserController::class, 'dataTable'])->name('users.dataTable');



    Route::resource('personals', PersonalController::class);

    Route::resource('universidads', UniversidadController::class);

    Route::resource('titulos', TituloController::class);
    Route::post('titulo-datatable', [TituloController::class, 'dataTable'])->name('titulos.dataTable');

    Route::resource('investigadors', InvestigadorController::class);
    Route::post('investigador-datatable', [InvestigadorController::class, 'dataTable'])->name('investigadors.dataTable');

    Route::resource('solicitud_sicadis', SolicitudSicadiController::class);
    Route::post('solicitud_sicadi-datatable', [SolicitudSicadiController::class, 'dataTable'])->name('solicitud_sicadis.dataTable');



    Route::resource('proyectos', ProyectoController::class);
    Route::post('proyecto-datatable', [ProyectoController::class, 'dataTable'])->name('proyectos.dataTable');

    Route::resource('integrantes', IntegranteController::class);
    Route::post('integrante-datatable', [IntegranteController::class, 'dataTable'])->name('integrantes.dataTable');
    Route::get('buscar_investigador', [IntegranteController::class, 'buscarInvestigador'])->name('integrantes.buscarInvestigador');

    Route::resource('integrante_estados', IntegranteEstadoController::class);
    Route::post('integrante_estado-datatable', [IntegranteEstadoController::class, 'dataTable'])->name('integrante_estados.dataTable');


});
