<?php

// database/seeders/RoleSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $roles = [
            'admin',
            'user', // Asegúrate de que el rol 'user' esté incluido
            'editor',
            'consulta',
            'Contratista',
            'Inspector',
            'Jefe de Obra',
            'Inspector Principal',
            'Asistente Contratista',
            'Asistente Inspección',
            'Especialista',
            'Jefe de Proyecto',
            'Visualizador'
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }
    }
}
