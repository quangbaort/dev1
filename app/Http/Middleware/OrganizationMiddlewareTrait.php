<?php

namespace App\Http\Middleware;

use App\Exceptions\OrganizationNotVerifyException;
use App\Repositories\OrganizationRepository;
use Illuminate\Http\Request;

trait OrganizationMiddlewareTrait
{
    /**
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $organization;

    /**
     * Get organization from database
     *
     * @param Request $request
     *
     * @return false | null | \App\Models\Organization
     * @throws \Throwable
     */
    protected function trustedOrgan($request)
    {
        $user = $request->user();
        $requestOrganId = $this->getRequestOrganId($request);
        if (!$user->isSupperAdmin()) {
            return $this->organization = $user->organizations->where('id', $requestOrganId)->first();
        }

        return $this->organization = app(OrganizationRepository::class)->find($requestOrganId);
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return array|string|null
     * @throws \Throwable
     */
    protected function getRequestOrganId(Request $request)
    {
        $organizationAccess = $request->header('OrganId', $request->input('organization_id', false));

        throw_if(!$organizationAccess, OrganizationNotVerifyException::class);

        return $organizationAccess;
    }
}
