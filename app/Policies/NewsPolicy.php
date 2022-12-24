<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class NewsPolicy
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
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
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
     * Determine whether the user can view statistic.
     *
     * @param \App\Models\User $user
     *
     * @return bool
     */
    public function statistic(User $user)
    {
        return $user->isAdminHigher();
    }
}
