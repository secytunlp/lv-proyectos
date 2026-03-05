<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class AddPermisosSolicitudSicadiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            'solicitud_sicadi-listar',
            'solicitud_sicadi-crear',
            'solicitud_sicadi-editar',
            'solicitud_sicadi-eliminar',

        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }
    }
}
