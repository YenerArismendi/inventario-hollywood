<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Venta;
use Illuminate\Auth\Access\HandlesAuthorization;

class VentaPolicy
{
    use HandlesAuthorization;

    /**
     * Realiza una comprobación previa para el super administrador.
     */
    public function before(User $user, string $ability): bool|null
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_venta');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Venta $venta): bool
    {
        return $user->can('view_venta');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_venta');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Venta $venta): bool
    {
        return $user->can('update_venta');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Venta $venta): bool
    {
        return $user->can('delete_venta');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Venta $venta): bool
    {
        return $user->can('restore_venta');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Venta $venta): bool
    {
        return $user->can('force_delete_venta');
    }
}
