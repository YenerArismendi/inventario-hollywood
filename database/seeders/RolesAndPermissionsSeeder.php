<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Limpiar cache de permisos
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Permisos base para todos los módulos
        $acciones = [
            'view',          // Ver un recurso individual
            'view_any',      // Ver listado de recursos
            'create',        // Crear nuevo recurso
            'update',        // Editar recurso
            'delete',        // Eliminar recurso individual
            'delete_any',    // Eliminar múltiples recursos
        ];

        // Obtener lista de archivos de recursos Filament
        $resourceFiles = File::allFiles(app_path('Filament/Resources'));

        // Extraer el nombre del módulo desde cada archivo
        $modulos = [];

        foreach ($resourceFiles as $file) {
            $fileName = $file->getFilenameWithoutExtension();

            if (Str::endsWith($fileName, 'Resource')) {
                $modulo = Str::snake(str_replace('Resource', '', $fileName));
                $modulos[] = $modulo;
            }
        }

        // Crear permisos dinámicos por módulo
        foreach ($modulos as $modulo) {
            foreach ($acciones as $accion) {
                Permission::firstOrCreate(['name' => "{$accion}_{$modulo}"]);
            }
        }

        // --- Permisos Personalizados ---
        Permission::firstOrCreate(['name' => 'change_bodega']);

        // Crear o encontrar el rol super_admin
        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);

        // Asignar todos los permisos disponibles al rol super_admin
        $superAdminRole->syncPermissions(Permission::all());
    }
}
