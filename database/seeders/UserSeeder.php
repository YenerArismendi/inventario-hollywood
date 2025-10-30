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
        // Crear el usuario administrador si no existe
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@hollywood.com'],
            [
                'name' => 'admin',
                'password' => Hash::make('password'), // ¡Cambiar en producción!
            ]
        );

        // Asignar el rol de 'admin' al usuario
        $adminUser->assignRole('admin');
    }
}
