<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserRoleSeeder extends Seeder
{
    public function run()
    {
        $user = User::find(1); // Cambiá el ID según el usuario que quieras asignar
        $user->assignRole('admin');
    }
}
