<?php

namespace App\Helpers;

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
        ];

        foreach ($acciones as $key => $label) {
            if (str_starts_with($permission, $key)) {
                $entidad = str_replace($key . '_', '', $permission);
                $entidad = str_replace('_', ' ', $entidad);
                return "{$label} " . ucfirst($entidad);
            }
        }

        // Si no coincide, formatea el permiso crudo
        return ucfirst(str_replace('_', ' ', $permission));
    }

    /**
     * Traducir una lista de permisos.
     */
    public static function traducirLista(array $permissions): array
    {
        return array_map(fn ($permiso) => self::traducir($permiso), $permissions);
    }
}
