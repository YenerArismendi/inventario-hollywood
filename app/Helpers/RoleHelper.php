<?php

// app/Helpers/RoleHelper.php
namespace App\Helpers;

use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;

class RoleHelper
{
    public static function groupedPermissions(): array
    {
        return Permission::all()
            ->pluck('name')
            ->mapWithKeys(fn ($permiso) => [
                $permiso => PermissionHelper::traducir($permiso)
            ])
            ->toArray();
    }
}
