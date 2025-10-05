<?php

namespace App\Policies;

use App\Models\Recetas;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class RecetasPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_recetas');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Recetas $recetas): bool
    {
        return $user->can('view_recetas');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_recetas');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Recetas $recetas): bool
    {
        return $user->can('update_recetas');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Recetas $recetas): bool
    {
        return $user->can('delete_recetas');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Recetas $recetas): bool
    {
        return $user->can('restore_recetas');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Recetas $recetas): bool
    {
        return $user->can('force_delete_recetas');
    }
}
