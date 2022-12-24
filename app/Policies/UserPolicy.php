<?php

namespace App\Policies;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Perform pre-authorization checks.
     *
     * @param \App\Models\User $user
     *
     * @return void|bool
     */
    public function before(User $user)
    {
        if ($user->isAdminHigher()) {
            return true;
        }
    }

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function viewAny(User $user)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param \App\Models\User $user
     *
     * @return \App\Models\User
     */

    public function view(User $user)
    {
        return $user;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param \App\Models\User $user
     *
     * @return bool
     */
    public function create(User $user)
    {
        return $user->isAdminHigher();
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $updateUser
     *
     * @return bool
     */
    public function update(User $user, User $updateUser)
    {
        return $user->isAdminHigher() || $user->id = $updateUser->id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param \App\Models\User $user
     *
     * @return bool
     */
    public function delete(User $user)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Organization  $organization
     * @return bool
     */
    public function restore(User $user, Organization $organization)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param \App\Models\User $user
     *
     * @return bool
     */
    public function forceDelete(User $user)
    {
        return $user->isSupperAdmin();
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function deleteMultiple(User $user)
    {
        return $user->isAdminHigher();
    }
    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function exportUser(User $user)
    {
        return $user->isAdmin();
    }
    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function checkEmail(User $user)
    {
        return $user->isAdmin();
    }
    /**
     * Determine whether the user can import the user.
     *
     * @param \App\Models\User $user
     *
     * @return bool
     */
    public function importUser(User $user)
    {
        return $user->isAdmin();
    }
}
