<?php

namespace App\Helpers;

use Illuminate\Support\Str;

class PermissionHelper
{
    public static function traducir(string $permission): string
    {
        $acciones = [
            'view_any' => 'Ver todos',
            'view' => 'Ver',
            'create' => 'Crear',
            'update' => 'Editar',
            'delete_any' => 'Eliminar todos',
            'delete' => 'Eliminar',
            'force_delete_any' => 'Eliminar permanentemente todos',
            'force_delete' => 'Eliminar permanentemente',
            'restore_any' => 'Restaurar todos',
            'restore' => 'Restaurar',
            'replicate' => 'Duplicar',
            'reorder' => 'Reordenar',
        ];

        foreach ($acciones as $key => $label) {
            if (str_starts_with($permission, $key)) {
                $entidad = str_replace($key . '_', '', $permission);
                return $label . ' ' . str_replace('_', ' ', $entidad);
            }
        }

        // Fallback si no matchea ningún prefijo
        return ucfirst(str_replace('_', ' ', $permission));
    }

}
