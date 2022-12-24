<?php

namespace App\Policies;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrganizationPolicy
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
        if ($user->isSupperAdmin()) {
            return true;
        }
    }

    /**
     * Determine whether the user can view any models.
     *
     * @param \App\Models\User $user
     *
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
     * @return bool
     */
    public function view(User $user)
    {
        return $user->isAdmin();
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
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Organization $organization
     *
     * @return bool
     */
    public function update(User $user, Organization $organization)
    {
        return $user->isAdmin();
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
        return $user->isSupperAdmin();
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Organization $organization
     *
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
     * Determine whether the user can custom method without model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Organization $organization
     *
     * @return bool
     */
    public function deleteMultiple(User $user, Organization $organization)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can custom method without model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Organization $organization
     *
     * @return bool
     */
    public function export(User $user, Organization $organization)
    {
        return $user->isAdmin();
    }
}
