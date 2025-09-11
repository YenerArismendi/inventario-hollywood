<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Crear el rol de 'admin' si no existe (es seguro ejecutarlo de nuevo)
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        // Crear el usuario administrador si no existe
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@hollywood.com'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('password'), // ¡Cambiar en producción!
            ]
        );

        // Asignar el rol de 'admin' al usuario
        $adminUser->assignRole($adminRole);
    }
}
