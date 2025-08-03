<?php

// app/Helpers/RoleHelper.php
namespace App\Helpers;

use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;

class RoleHelper
{
    public static function groupedPermissions(): array
    {
        $permissions = Permission::all();
        $grouped = [];

        foreach ($permissions as $permission) {
            // Determinar módulo (última palabra del permiso)
            $parts = explode('_', $permission->name);
            $modulo = Str::title(end($parts)); // Ej: "usuarios" => "Usuarios"

            // Agrupar por módulo, formato correcto: ID => 'string'
            $grouped[$modulo][$permission->id] = Str::title(str_replace('_', ' ', $permission->name));
        }

        return $grouped;
    }
}
