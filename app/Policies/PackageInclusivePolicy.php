<?php

namespace App\Policies;

use App\Models\User;
use App\Models\PackageInclusive;
use Illuminate\Auth\Access\HandlesAuthorization;

class PackageInclusivePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_package::inclusive');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, PackageInclusive $packageInclusive): bool
    {
        return $user->can('view_package::inclusive');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_package::inclusive');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PackageInclusive $packageInclusive): bool
    {
        return $user->can('update_package::inclusive');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PackageInclusive $packageInclusive): bool
    {
        return $user->can('delete_package::inclusive');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_package::inclusive');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, PackageInclusive $packageInclusive): bool
    {
        return $user->can('force_delete_package::inclusive');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_package::inclusive');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, PackageInclusive $packageInclusive): bool
    {
        return $user->can('restore_package::inclusive');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_package::inclusive');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, PackageInclusive $packageInclusive): bool
    {
        return $user->can('replicate_package::inclusive');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_package::inclusive');
    }
}
