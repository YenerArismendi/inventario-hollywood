<?php

namespace App\Policies;

use App\Models\Caja;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CajaPolicy
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
     * Determina si el usuario puede ver el listado de modelos.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_caja');
    }

    /**
     * Determina si el usuario puede ver un modelo específico.
     */
    public function view(User $user, Caja $caja): bool
    {
        return $user->can('view_caja');
    }

    /**
     * Determina si el usuario puede crear modelos.
     */
    public function create(User $user): bool
    {
        return $user->can('create_caja');
    }

/**
* Determina si el usuario puede actualizar un modelo.
*/
    public function update(User $user, Caja $caja): bool
    {
        return $user->can('update_caja');
    }

    /**
     * Determina si el usuario puede eliminar un modelo.
     */
    public function delete(User $user, Caja $caja): bool
    {
        return $user->can('delete_caja');
    }

    /**
     * Determina si el usuario puede eliminar múltiples modelos.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_caja');
    }
}
