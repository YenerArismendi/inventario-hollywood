<?php

namespace App\Policies;

use App\Models\Compra;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CompraPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_compra');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Compra $compra): bool
    {
        return $user->can('view_compra');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_compra');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Compra $compra): bool
    {
        return $user->can('update_compra');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Compra $compra): bool
    {
        return $user->can('delete_compra');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Compra $compra): bool
    {
        return $user->can('restore_compra');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Compra $compra): bool
    {
        return $user->can('force_delete_compra');
    }
}
