<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class AddPermisosInvestigadorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            'investigador-listar',
            'investigador-crear',
            'investigador-editar',
            'investigador-eliminar',

        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }
    }
}
