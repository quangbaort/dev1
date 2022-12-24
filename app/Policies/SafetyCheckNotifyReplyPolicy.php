<?php

namespace App\Policies;

use App\Models\User;
use App\Models\SafetyCheckResponse;
use App\Repositories\OrganizationRepository;
use App\Repositories\SafetiesCheckRepository;
use Illuminate\Auth\Access\HandlesAuthorization;

class SafetyCheckNotifyReplyPolicy
{
    use HandlesAuthorization;

    public function __construct(SafetiesCheckRepository $safetiesCheckRepository,OrganizationRepository $organizationRepository)
    {
        $this->organizationRepository = $organizationRepository;
        $this->safetiesCheckRepository = $safetiesCheckRepository;
    }

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
        return $user;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SafetyCheckResponse $safetyCheckNotifiesReply
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, SafetyCheckResponse $safetyCheckNotifiesReply)
    {
        return $user->code == ADMIN_ROLE;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->code == ADMIN_ROLE;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SafetyCheckResponse  $safetyCheckNotifiesReply
     *
     * @return bool
     */
    public function update(User $user, SafetyCheckResponse $safetyCheckNotifiesReply)
    {
        $safety = $this->safetiesCheckRepository->find($safetyCheckNotifiesReply->safety_check_id);
        $organization = $this->organizationRepository->find($safety->organization_id);
        return $user->code == ADMIN_ROLE && $organization->safety_check_enabled;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SafetyCheckResponse  $safetyCheckNotifiesReply
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, SafetyCheckResponse $safetyCheckNotifiesReply)
    {
        $safety = $this->safetiesCheckRepository->find($safetyCheckNotifiesReply->safety_check_id);
        $organization = $this->organizationRepository->find($safety->organization_id);
        return $user->code == ADMIN_ROLE && $organization->safety_check_enabled;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param \App\Models\SafetyCheckResponse  $safetyCheckNotifiesReply
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, SafetyCheckResponse $safetyCheckNotifiesReply)
    {
        $safety = $this->safetiesCheckRepository->find($safetyCheckNotifiesReply->safety_check_id);
        $organization = $this->organizationRepository->find($safety->organization_id);
        return $user->code == ADMIN_ROLE && $organization->safety_check_enabled;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param \App\Models\SafetyCheckResponse  $safetyCheckNotifiesReply
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, SafetyCheckResponse $safetyCheckNotifiesReply)
    {
        $safety = $this->safetiesCheckRepository->find($safetyCheckNotifiesReply->safety_check_id);
        $organization = $this->organizationRepository->find($safety->organization_id);
        return $user->code == ADMIN_ROLE && $organization->safety_check_enabled;
    }
}
