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

    Route::get('integrantes/{integrante}/rechazar', [IntegranteController::class, 'rechazar'])->name('integrantes.rechazar');
    Route::put('integrantes/{integrante}/deny', [IntegranteController::class, 'saveDeny'])->name('integrantes.saveDeny');
    Route::get('integrantes/{integrante}/baja', [IntegranteController::class, 'baja'])->name('integrantes.baja');
    Route::put('integrantes/{integrante}/remove', [IntegranteController::class, 'remove'])->name('integrantes.remove');
    Route::get('integrantes/{integrante}/cambio', [IntegranteController::class, 'cambio'])->name('integrantes.cambio');
    Route::put('integrantes/{integrante}/cambiar', [IntegranteController::class, 'cambiar'])->name('integrantes.cambiar');
    Route::get('integrantes/{integrante}/rechazarBaja', [IntegranteController::class, 'rechazarBaja'])->name('integrantes.rechazarBaja');
    Route::put('integrantes/{integrante}/denyBaja', [IntegranteController::class, 'saveDenyBaja'])->name('integrantes.saveDenyBaja');
    Route::get('integrantes/{integrante}/rechazarCambio', [IntegranteController::class, 'rechazarCambio'])->name('integrantes.rechazarCambio');
    Route::put('integrantes/{integrante}/denyCambio', [IntegranteController::class, 'saveDenyCambio'])->name('integrantes.saveDenyCambio');
    Route::get('integrantes/{integrante}/cambioHS', [IntegranteController::class, 'cambioHS'])->name('integrantes.cambioHS');
    Route::put('integrantes/{integrante}/cambiarHS', [IntegranteController::class, 'cambiarHS'])->name('integrantes.cambiarHS');
    Route::get('integrantes/{integrante}/rechazarCambioHS', [IntegranteController::class, 'rechazarCambioHS'])->name('integrantes.rechazarCambioHS');
    Route::put('integrantes/{integrante}/denyCambioHS', [IntegranteController::class, 'saveDenyCambioHS'])->name('integrantes.saveDenyCambioHS');
    Route::get('integrantes/{integrante}/cambioTipo', [IntegranteController::class, 'cambioTipo'])->name('integrantes.cambioTipo');
    Route::put('integrantes/{integrante}/cambiarTipo', [IntegranteController::class, 'cambiarTipo'])->name('integrantes.cambiarTipo');
    Route::get('integrantes/{integrante}/rechazarCambioTipo', [IntegranteController::class, 'rechazarCambioTipo'])->name('integrantes.rechazarCambioTipo');
    Route::put('integrantes/{integrante}/denyCambioTipo', [IntegranteController::class, 'saveDenyCambioTipo'])->name('integrantes.saveDenyCambioTipo');
    Route::post('anular/{id}', [IntegranteController::class, 'anular'])->name('integrantes.anular');
    Route::post('anularCambio/{id}', [IntegranteController::class, 'anularCambio'])->name('integrantes.anularCambio');
    Route::post('anularHS/{id}', [IntegranteController::class, 'anularHS'])->name('integrantes.anularHS');
    Route::post('anularTipo/{id}', [IntegranteController::class, 'anularTipo'])->name('integrantes.anularTipo');
    Route::resource('integrantes', IntegranteController::class);
    Route::post('integrante-datatable', [IntegranteController::class, 'dataTable'])->name('integrantes.dataTable');
    Route::get('buscar_investigador', [IntegranteController::class, 'buscarInvestigador'])->name('integrantes.buscarInvestigador');
    Route::get('alta-pdf', [IntegranteController::class, 'generateAltaPDF'])->name('integrantes.alta-pdf');
    Route::get('baja-pdf', [IntegranteController::class, 'generateBajaPDF'])->name('integrantes.baja-pdf');
    Route::get('cambio-pdf', [IntegranteController::class, 'generateCambioPDF'])->name('integrantes.cambio-pdf');
    Route::get('cambioHS-pdf', [IntegranteController::class, 'generateCambioHSPDF'])->name('integrantes.cambioHS-pdf');
    Route::get('cambioTipo-pdf', [IntegranteController::class, 'generateCambioTipoPDF'])->name('integrantes.cambioTipo-pdf');
    Route::get('archivos', [IntegranteController::class, 'archivos'])->name('integrantes.archivos');
    Route::post('enviar/{id}', [IntegranteController::class, 'enviar'])->name('integrantes.enviar');
    Route::post('admitir/{id}', [IntegranteController::class, 'admitir'])->name('integrantes.admitir');
    Route::post('enviarBaja/{id}', [IntegranteController::class, 'enviarBaja'])->name('integrantes.enviarBaja');
    Route::post('admitirBaja/{id}', [IntegranteController::class, 'admitirBaja'])->name('integrantes.admitirBaja');
    Route::post('enviarCambio/{id}', [IntegranteController::class, 'enviarCambio'])->name('integrantes.enviarCambio');
    Route::post('admitirCambio/{id}', [IntegranteController::class, 'admitirCambio'])->name('integrantes.admitirCambio');
    Route::post('enviarCambioHS/{id}', [IntegranteController::class, 'enviarCambioHS'])->name('integrantes.enviarCambioHS');
    Route::post('admitirCambioHS/{id}', [IntegranteController::class, 'admitirCambioHS'])->name('integrantes.admitirCambioHS');
    Route::post('enviarCambioTipo/{id}', [IntegranteController::class, 'enviarCambioTipo'])->name('integrantes.enviarCambioTipo');
    Route::post('admitirCambioTipo/{id}', [IntegranteController::class, 'admitirCambioTipo'])->name('integrantes.admitirCambioTipo');

    Route::resource('integrante_estados', IntegranteEstadoController::class);
    Route::post('integrante_estado-datatable', [IntegranteEstadoController::class, 'dataTable'])->name('integrante_estados.dataTable');



});
