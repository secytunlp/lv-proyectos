<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class AddPermisosSolicitudSicadiEstadoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            'solicitud_sicadi_estado-listar',
            'solicitud_sicadi_estado-crear',
            'solicitud_sicadi_estado-editar',
            'solicitud_sicadi_estado-eliminar',

        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }
    }
}
