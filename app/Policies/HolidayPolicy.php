<?php

namespace App\Policies;

use App\Models\Holiday;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class HolidayPolicy
{
    use HandlesAuthorization;

     /**
     * Perform pre-authorization checks.
     *
     * @param  \App\Models\User  $user
     * @param  string  $ability
     * @return void|bool
     */
    public function before(User $user)
    {
        if ($user->is_super_admin) {
            return true;
        }
    }

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        return $user->roles->where('role', ADMIN_ROLE);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Holiday  $holiday
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user)
    {
        return $user->roles->where('role', ADMIN_ROLE);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        // Todo: check role user
        return $user->roles->where('role', ADMIN_ROLE);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Holiday  $holiday
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Holiday $holiday)
    {
        return $user->roles->where('role', ADMIN_ROLE);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Holiday  $holiday
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user)
    {
        return $user->roles->where('role', ADMIN_ROLE);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Holiday  $holiday
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Holiday $holiday)
    {
        return $user->roles->where('role', ADMIN_ROLE);
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Holiday  $holiday
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Holiday $holiday)
    {
        return $user->roles->where('role', ADMIN_ROLE);
    }
}