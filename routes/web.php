<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;

use App\Http\Controllers\PersonalController;
use App\Http\Controllers\UniversidadController;
use App\Http\Controllers\TituloController;
use App\Http\Controllers\InvestigadorController;

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

Auth::routes();

Route::get('/home', [TituloController::class, 'index'])->name('home');

Route::group(['middleware' => ['auth']], function() {
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

});
