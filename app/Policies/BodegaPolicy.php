<?php

namespace App\Policies;

use App\Models\Bodega;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BodegaPolicy
{
    use handlesAuthorization;
    /**
     * Realiza una comprobación previa para el super administrador.
     * Si el usuario es 'admin', se le concede acceso a todo, saltándose las demás comprobaciones.
     */
    public function before(User $user, string $ability): bool|null
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        return null; // Continúa con la comprobación normal del permiso
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_bodega');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Bodega $bodega): bool
    {
        return $user->can('view_bodega');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_bodega');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Bodega $bodega): bool
    {
        return $user->can('update_bodega');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Bodega $bodega): bool
    {
        return $user->can('delete_bodega');
    }

    /**
     * Determine whether the user can delete multiple models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_bodega');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Bodega $bodega): bool
    {
        return $user->can('restore_bodega');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Bodega $bodega): bool
    {
        return $user->can('force_delete_bodega');
    }
}
