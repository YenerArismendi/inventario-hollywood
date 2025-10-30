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
                'name' => 'Super Admin',
                'password' => Hash::make('password'), // ¡Cambiar en producción!
            ]
        );
        // Asignar el rol 'super_admin', que ya fue creado y configurado por RolesAndPermissionsSeeder
        $adminUser->assignRole('super_admin');
    }
}
