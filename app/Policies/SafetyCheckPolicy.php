<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SafetyCheckPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user all models.
     *
     * @param \App\Models\User $user
     *
     * @return bool
     */
    public function before(User $user)
    {
        if ($user->isSupperAdmin()) {
            return true;
        }
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
        return $user->isAdminHigher();
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
     * @param \App\Models\User $user
     *
     * @return bool
     */
    public function update(User $user)
    {
        return $user->isAdminHigher();
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
        return $user->isAdminHigher();
    }
}
