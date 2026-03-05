<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;

class CheckSelectedRolePermissions
{
    public function handle($request, Closure $next)
    {

        $permissionMap = [
            'roles.index' => ['rol-listar'],
            'roles.create' => ['rol-crear'],
            'roles.store' => ['rol-crear'],
            'roles.edit' => ['rol-editar'],
            'roles.update' => ['rol-editar'],
            'roles.destroy' => ['rol-eliminar'],

            'users.index' => ['usuario-listar'],
            'users.dataTable' => ['usuario-listar'],
            'users.create' => ['usuario-crear'],
            'users.store' => ['usuario-crear'],
            'users.edit' => ['usuario-editar'],
            'users.update' => ['usuario-editar'],
            'users.destroy' => ['usuario-eliminar'],
            'users.perfil' => ['usuario-editar'],
            'users.updatePerfil' => ['usuario-editar'],

            'universidads.index' => ['universidad-listar'],
            'universidads.dataTable' => ['universidad-listar'],
            'universidads.create' => ['universidad-crear'],
            'universidads.store' => ['universidad-crear'],
            'universidads.edit' => ['universidad-editar'],
            'universidads.update' => ['universidad-editar'],
            'universidads.destroy' => ['universidad-eliminar'],

            'home' => ['solicitud-listar'],
            'titulos.index' => ['titulo-listar'],
            'titulos.dataTable' => ['titulo-listar'],
            'titulos.create' => ['titulo-crear'],
            'titulos.store' => ['titulo-crear'],
            'titulos.edit' => ['titulo-editar'],
            'titulos.update' => ['titulo-editar'],
            'titulos.destroy' => ['titulo-eliminar'],

            'investigadors.index' => ['investigador-listar'],
            'investigadors.show' => ['investigador-listar'],
            'investigadors.dataTable' => ['investigador-listar'],
            'investigadors.create' => ['investigador-crear'],
            'investigadors.store' => ['investigador-editar'],
            'investigadors.edit' => ['investigador-editar'],
            'investigadors.update' => ['investigador-editar'],
            'investigadors.destroy' => ['investigador-eliminar'],
            'investigadors.clearFilter' => ['investigador-listar'],

            'proyectos.index' => ['proyecto-listar'],
            'proyectos.show' => ['proyecto-listar'],
            'proyectos.dataTable' => ['proyecto-listar'],
            'proyectos.create' => ['proyecto-crear'],
            'proyectos.store' => ['proyecto-editar'],
            'proyectos.edit' => ['proyecto-editar'],
            'proyectos.update' => ['proyecto-editar'],
            'proyectos.destroy' => ['proyecto-eliminar'],
            'proyectos.clearFilter' => ['proyecto-listar'],

            'integrantes.index' => ['integrante-listar'],
            'integrantes.clearFilter' => ['integrante-listar'],
            'integrantes.show' => ['integrante-listar'],
            'integrantes.dataTable' => ['integrante-listar'],
            'integrantes.create' => ['integrante-crear'],
            'integrantes.store' => ['integrante-crear'],
            'integrantes.edit' => ['integrante-editar'],
            'integrantes.update' => ['integrante-editar'],
            'integrantes.destroy' => ['integrante-eliminar'],
            'integrantes.buscarInvestigador' => ['integrante-crear'],
            'integrantes.alta-pdf' => ['integrante-listar'],
            'integrantes.archivos' => ['integrante-listar'],
            'integrantes.enviar' => ['integrante-editar'],
            'integrantes.admitir' => ['solicitud-admitir'],
            'integrantes.rechazar' => ['solicitud-rechazar'],
            'integrantes.saveDeny' => ['solicitud-rechazar'],
            'integrantes.baja' => ['integrante-eliminar'],
            'integrantes.remove' => ['integrante-eliminar'],
            'integrantes.anular' => ['integrante-eliminar'],
            'integrantes.baja-pdf' => ['integrante-listar'],
            'integrantes.enviarBaja' => ['integrante-eliminar'],
            'integrantes.admitirBaja' => ['solicitud-admitir'],
            'integrantes.rechazarBaja' => ['solicitud-rechazar'],
            'integrantes.saveDenyBaja' => ['solicitud-rechazar'],
            'integrantes.cambio' => ['integrante-editar'],
            'integrantes.cambiar' => ['integrante-editar'],
            'integrantes.cambio-pdf' => ['integrante-listar'],
            'integrantes.enviarCambio' => ['integrante-editar'],
            'integrantes.anularCambio' => ['integrante-editar'],
            'integrantes.admitirCambio' => ['solicitud-admitir'],
            'integrantes.rechazarCambio' => ['solicitud-rechazar'],
            'integrantes.saveDenyCambio' => ['solicitud-rechazar'],
            'integrantes.cambioHS' => ['integrante-editar'],
            'integrantes.cambiarHS' => ['integrante-editar'],
            'integrantes.cambioHS-pdf' => ['integrante-listar'],
            'integrantes.enviarCambioHS' => ['integrante-editar'],
            'integrantes.anularHS' => ['integrante-editar'],
            'integrantes.admitirCambioHS' => ['solicitud-admitir'],
            'integrantes.rechazarCambioHS' => ['solicitud-rechazar'],
            'integrantes.saveDenyCambioHS' => ['solicitud-rechazar'],
            'integrantes.cambioTipo' => ['integrante-editar'],
            'integrantes.cambiarTipo' => ['integrante-editar'],
            'integrantes.cambioTipo-pdf' => ['integrante-listar'],
            'integrantes.enviarCambioTipo' => ['integrante-editar'],
            'integrantes.anularTipo' => ['integrante-editar'],
            'integrantes.admitirCambioTipo' => ['solicitud-admitir'],
            'integrantes.rechazarCambioTipo' => ['solicitud-rechazar'],
            'integrantes.saveDenyCambioTipo' => ['solicitud-rechazar'],

            'integrante_estados.index' => ['integrante_estado-listar'],
            'integrante_estados.dataTable' => ['integrante_estado-listar'],
            'integrante_estados.create' => ['integrante_estado-crear'],
            'integrante_estados.store' => ['integrante_estado-crear'],


            'solicitud_sicadis.index' => ['solicitud_sicadi-listar'],
            'solicitud_sicadis.dataTable' => ['solicitud_sicadi-listar'],
            'solicitud_sicadis.create' => ['solicitud_sicadi-crear'],
            'solicitud_sicadis.store' => ['solicitud_sicadi-editar'],
            'solicitud_sicadis.edit' => ['solicitud_sicadi-editar'],
            'solicitud_sicadis.update' => ['solicitud_sicadi-editar'],
            'solicitud_sicadis.destroy' => ['solicitud_sicadi-eliminar'],
            'solicitud_sicadis.importar' => ['solicitud_sicadi-crear'],
            'solicitud_sicadis.importprocess' => ['solicitud_sicadi-crear'],
            'solicitud_sicadis.clearFilter' => ['solicitud_sicadi-listar'],
            'solicitud_sicadis.solicitud-pdf' => ['solicitud-listar'],
            'solicitud_sicadis.archivos' => ['solicitud-listar'],
            'solicitud_sicadis.enviar' => ['solicitud-editar'],
            'solicitud_sicadis.admitir' => ['solicitud-admitir'],
            'solicitud_sicadis.rechazar' => ['solicitud-rechazar'],
            'solicitud_sicadis.saveDeny' => ['solicitud-rechazar'],
            'solicitud_sicadis.rectificar' => ['solicitud-rechazar'],
            'solicitud_sicadis.saveRect' => ['solicitud-rechazar'],
            'solicitud_sicadis.exportar' => ['solicitud-rechazar'],
            'solicitud_sicadis.migrarFotos' => ['solicitud-rechazar'],

            'solicitud_sicadi_estados.index' => ['solicitud_sicadi_estado-listar'],
            'solicitud_sicadi_estados.dataTable' => ['solicitud_sicadi_estado-listar'],
            'solicitud_sicadi_estados.create' => ['solicitud_sicadi_estado-crear'],
            'solicitud_sicadi_estados.store' => ['solicitud_sicadi_estado-crear'],

            'jovens.index' => ['solicitud-listar'],
            'jovens.dataTable' => ['solicitud-listar'],
            'jovens.create' => ['solicitud-crear'],
            'jovens.store' => ['solicitud-editar'],
            'jovens.edit' => ['solicitud-editar'],
            'jovens.update' => ['solicitud-editar'],
            'jovens.destroy' => ['solicitud-eliminar'],
            'jovens.clearFilter' => ['solicitud-listar'],
            'jovens.solicitud-pdf' => ['solicitud-listar'],
            'jovens.archivos' => ['solicitud-listar'],
            'jovens.enviar' => ['solicitud-editar'],
            'jovens.admitir' => ['solicitud-admitir'],
            'jovens.rechazar' => ['solicitud-rechazar'],
            'jovens.saveDeny' => ['solicitud-rechazar'],
            'jovens.exportar' => ['solicitud-rechazar'],

            'joven_estados.index' => ['joven_estado-listar'],
            'joven_estados.dataTable' => ['joven_estado-listar'],
            'joven_estados.create' => ['joven_estado-crear'],
            'joven_estados.store' => ['joven_estado-crear'],

            'joven_evaluacions.index' => ['evaluacion-listar'],
            'joven_evaluacions.dataTable' => ['evaluacion-listar'],
            'joven_evaluacions.clearFilter' => ['evaluacion-listar'],
            'joven_evaluacions.create' => ['evaluacion-crear'],
            'joven_evaluacions.store' => ['evaluacion-crear'],
            'joven_evaluacions.enviar' => ['evaluacion-crear'],
            'joven_evaluacions.aceptar' => ['evaluacion-evaluar'],
            'joven_evaluacions.rechazar' => ['evaluacion-evaluar'],
            'joven_evaluacions.saveDeny' => ['evaluacion-evaluar'],
            'joven_evaluacions.evaluar' => ['evaluacion-evaluar'],
            'joven_evaluacions.saveEvaluar' => ['evaluacion-evaluar'],
            'joven_evaluacions.evaluacion-pdf' => ['evaluacion-listar','evaluacion-evaluar'],
            'joven_evaluacions.send' => ['evaluacion-evaluar'],
            'joven_evaluacions.actualizar' => ['integrante_estado-crear'],



            'joven_evaluacion_estados.index' => ['joven_evaluacion_estado-listar'],
            'joven_evaluacion_estados.dataTable' => ['joven_evaluacion_estado-listar'],
            'joven_evaluacion_estados.create' => ['joven_evaluacion_estado-crear'],
            'joven_evaluacion_estados.store' => ['joven_evaluacion_estado-crear'],

            'viajes.index' => ['solicitud-listar'],
            'viajes.dataTable' => ['solicitud-listar'],

            'viajes.create' => ['solicitud-crear'],
            'viajes.store' => ['solicitud-editar'],
            'viajes.edit' => ['solicitud-editar'],
            'viajes.update' => ['solicitud-editar'],
            'viajes.destroy' => ['solicitud-eliminar'],
            'viajes.clearFilter' => ['solicitud-listar'],
            'viajes.solicitud-pdf' => ['solicitud-listar'],
            'viajes.archivos' => ['solicitud-listar'],
            'viajes.enviar' => ['solicitud-editar'],
            'viajes.admitir' => ['solicitud-admitir'],
            'viajes.rechazar' => ['solicitud-rechazar'],
            'viajes.saveDeny' => ['solicitud-rechazar'],
            'viajes.exportar' => ['solicitud-rechazar'],

            'viaje_estados.index' => ['joven_estado-listar'],
            'viaje_estados.dataTable' => ['joven_estado-listar'],
            'viaje_estados.create' => ['joven_estado-crear'],
            'viaje_estados.store' => ['joven_estado-crear'],

            'viaje_evaluacions.index' => ['evaluacion-listar'],
            'viaje_evaluacions.dataTable' => ['evaluacion-listar'],
            'viaje_evaluacions.clearFilter' => ['evaluacion-listar'],
            'viaje_evaluacions.create' => ['evaluacion-crear'],
            'viaje_evaluacions.store' => ['evaluacion-crear'],
            'viaje_evaluacions.enviar' => ['evaluacion-crear'],
            'viaje_evaluacions.aceptar' => ['evaluacion-evaluar'],
            'viaje_evaluacions.rechazar' => ['evaluacion-evaluar'],
            'viaje_evaluacions.saveDeny' => ['evaluacion-evaluar'],
            'viaje_evaluacions.evaluar' => ['evaluacion-evaluar'],
            'viaje_evaluacions.saveEvaluar' => ['evaluacion-evaluar'],
            'viaje_evaluacions.evaluacion-pdf' => ['evaluacion-listar','evaluacion-evaluar'],
            'viaje_evaluacions.send' => ['evaluacion-evaluar'],
            'viaje_evaluacions.actualizar' => ['integrante_estado-crear'],



            'viaje_evaluacion_estados.index' => ['joven_evaluacion_estado-listar'],
            'viaje_evaluacion_estados.dataTable' => ['joven_evaluacion_estado-listar'],
            'viaje_evaluacion_estados.create' => ['joven_evaluacion_estado-crear'],
            'viaje_evaluacion_estados.store' => ['joven_evaluacion_estado-crear'],


        ];
        $user = Auth::user();

        $selectedRoleId = session('selected_rol');

        if (!$selectedRoleId) {
            return redirect()->route('select-rol')->with('error', 'Debe seleccionar un rol.');
        }

        // Obtener los permisos del rol seleccionado
        $selectedRole = Role::findById($selectedRoleId);
        $rolePermissions = $selectedRole->permissions;

        // Reemplazar los permisos del usuario por los permisos del rol seleccionado
        $user->setRelation('permissions', $rolePermissions);

        // Registro de depuración
        //Log::info('Permisos del rol seleccionado:', $rolePermissions->pluck('name')->toArray());

        // Verificar si el usuario tiene permiso para acceder a la ruta actual
        $currentRouteName = $request->route()->getName();
        // Comprobar si hay permisos definidos para la ruta actual
        if (!isset($permissionMap[$currentRouteName])) {
            // Redireccionar a la página de error 403 si no hay permisos definidos para la ruta
            return redirect()->route('error-403');
        }

        // Verificar si el usuario tiene al menos uno de los permisos para la ruta actual
        $requiredPermissions = $permissionMap[$currentRouteName];
        $hasPermission = $rolePermissions->whereIn('name', $requiredPermissions)->isNotEmpty();

        if (!$hasPermission) {
            // Redireccionar a la página de error 403 si no tiene los permisos requeridos
            return redirect()->route('error-403');
        }

        return $next($request);
    }
}





