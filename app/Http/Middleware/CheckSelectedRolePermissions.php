<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class CheckSelectedRolePermissions
{
    public function handle($request, Closure $next)
    {

        $permissionMap = [
            'roles.index' => 'rol-listar',
            'roles.create' => 'rol-crear',
            'roles.store' => 'rol-crear',
            'roles.edit' => 'rol-editar',
            'roles.update' => 'rol-editar',
            'roles.destroy' => 'rol-eliminar',

            'users.index' => 'usuario-listar',
            'users.dataTable' => 'usuario-listar',
            'users.create' => 'usuario-crear',
            'users.store' => 'usuario-crear',
            'users.edit' => 'usuario-editar',
            'users.update' => 'usuario-editar',
            'users.destroy' => 'usuario-eliminar',
            'users.perfil' => 'usuario-editar',
            'users.updatePerfil' => 'usuario-editar',

            'universidads.index' => 'universidad-listar',
            'universidads.dataTable' => 'universidad-listar',
            'universidads.create' => 'universidad-crear',
            'universidads.store' => 'universidad-crear',
            'universidads.edit' => 'universidad-editar',
            'universidads.update' => 'universidad-editar',
            'universidads.destroy' => 'universidad-eliminar',

            'home' => 'proyecto-listar',
            'titulos.index' => 'titulo-listar',
            'titulos.dataTable' => 'titulo-listar',
            'titulos.create' => 'titulo-crear',
            'titulos.store' => 'titulo-crear',
            'titulos.edit' => 'titulo-editar',
            'titulos.update' => 'titulo-editar',
            'titulos.destroy' => 'titulo-eliminar',

            'investigadors.index' => 'investigador-listar',
            'investigadors.dataTable' => 'investigador-listar',
            'investigadors.create' => 'investigador-crear',
            'investigadors.store' => 'investigador-editar',
            'investigadors.edit' => 'investigador-editar',
            'investigadors.update' => 'investigador-editar',
            'investigadors.destroy' => 'investigador-eliminar',

            'proyectos.index' => 'proyecto-listar',
            'proyectos.dataTable' => 'proyecto-listar',
            'proyectos.create' => 'proyecto-crear',
            'proyectos.store' => 'proyecto-editar',
            'proyectos.edit' => 'proyecto-editar',
            'proyectos.update' => 'proyecto-editar',
            'proyectos.destroy' => 'proyecto-eliminar',

            'integrantes.index' => 'integrante-listar',
            'integrantes.dataTable' => 'integrante-listar',
            'integrantes.create' => 'integrante-crear',
            'integrantes.store' => 'integrante-editar',
            'integrantes.edit' => 'integrante-editar',
            'integrantes.update' => 'integrante-editar',
            'integrantes.destroy' => 'integrante-eliminar',
            'integrantes.buscarInvestigador' => 'integrante-crear',
            'integrantes.alta-pdf' => 'integrante-crear',
            'integrantes.archivos' => 'integrante-listar',
            'integrantes.enviar' => 'integrante-editar',
            'integrantes.admitir' => 'solicitud-admitir',
            'integrantes.rechazar' => 'solicitud-rechazar',
            'integrantes.saveDeny' => 'solicitud-rechazar',
            'integrantes.baja' => 'integrante-eliminar',
            'integrantes.remove' => 'integrante-eliminar',
            'integrantes.anular' => 'integrante-eliminar',
            'integrantes.baja-pdf' => 'integrante-listar',
            'integrantes.enviarBaja' => 'integrante-eliminar',
            'integrantes.admitirBaja' => 'solicitud-admitir',
            'integrantes.rechazarBaja' => 'solicitud-rechazar',
            'integrantes.saveDenyBaja' => 'solicitud-rechazar',
            'integrantes.cambio' => 'integrante-editar',
            'integrantes.cambiar' => 'integrante-editar',
            'integrantes.cambio-pdf' => 'integrante-listar',
            'integrantes.enviarCambio' => 'integrante-editar',
            'integrantes.anularCambio' => 'integrante-editar',
            'integrantes.admitirCambio' => 'solicitud-admitir',
            'integrantes.rechazarCambio' => 'solicitud-rechazar',
            'integrantes.saveDenyCambio' => 'solicitud-rechazar',
            'integrantes.cambioHS' => 'integrante-editar',
            'integrantes.cambiarHS' => 'integrante-editar',
            'integrantes.cambioHS-pdf' => 'integrante-listar',
            'integrantes.enviarCambioHS' => 'integrante-editar',
            'integrantes.anularHS' => 'integrante-editar',
            'integrantes.admitirCambioHS' => 'solicitud-admitir',
            'integrantes.rechazarCambioHS' => 'solicitud-rechazar',
            'integrantes.saveDenyCambioHS' => 'solicitud-rechazar',
            'integrantes.cambioTipo' => 'integrante-editar',
            'integrantes.cambiarTipo' => 'integrante-editar',
            'integrantes.cambioTipo-pdf' => 'integrante-listar',
            'integrantes.enviarCambioTipo' => 'integrante-editar',
            'integrantes.anularTipo' => 'integrante-editar',
            'integrantes.admitirCambioTipo' => 'solicitud-admitir',
            'integrantes.rechazarCambioTipo' => 'solicitud-rechazar',
            'integrantes.saveDenyCambioTipo' => 'solicitud-rechazar',

            'integrante_estados.index' => 'integrante_estado-listar',
            'integrante_estados.dataTable' => 'integrante_estado-listar',
            'integrante_estados.create' => 'integrante_estado-crear',
            'integrante_estados.store' => 'integrante_estado-editar',
            'integrante_estados.edit' => 'integrante_estado-editar',
            'integrante_estados.update' => 'integrante_estado-editar',
            'integrante_estados.destroy' => 'integrante_estado-eliminar',

            'solicitud_sicadis.index' => 'solicitud_sicadi-listar',
            'solicitud_sicadis.dataTable' => 'solicitud_sicadi-listar',
            'solicitud_sicadis.create' => 'solicitud_sicadi-crear',
            'solicitud_sicadis.store' => 'solicitud_sicadi-editar',
            'solicitud_sicadis.edit' => 'solicitud_sicadi-editar',
            'solicitud_sicadis.update' => 'solicitud_sicadi-editar',
            'solicitud_sicadis.destroy' => 'solicitud_sicadi-eliminar',



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
        \Log::info('Permisos del rol seleccionado:', $rolePermissions->pluck('name')->toArray());

        // Verificar si el usuario tiene permiso para acceder a la ruta actual
        $currentRouteName = $request->route()->getName();
        \Log::info('Ruta:'. $currentRouteName);
        if (!isset($permissionMap[$currentRouteName]) || !$rolePermissions->contains('name', $permissionMap[$currentRouteName])) {
            // Redireccionar a la página de error 403
            return redirect()->route('error-403');
        }

        return $next($request);
    }
}





