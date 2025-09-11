<?php

namespace App\Policies;

use App\Models\SesionCaja;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SesionCajaPolicy
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
        return $user->can('view_any_sesion_caja');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, SesionCaja $sesionCaja): bool
    {
        return $user->can('view_sesion_caja');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_sesion_caja');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, SesionCaja $sesionCaja): bool
    {
        return $user->can('update_sesion_caja');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, SesionCaja $sesionCaja): bool
    {
        return $user->can('delete_sesion_caja');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, SesionCaja $sesionCaja): bool
    {
        return $user->can('restore_sesion_caja');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, SesionCaja $sesionCaja): bool
    {
        return $user->can('force_delete_sesion_caja');
    }
}
