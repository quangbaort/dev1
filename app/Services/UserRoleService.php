<?php

namespace App\Services;

use App\Repositories\UserRoleRepository;
use App\Services\Concerns\BaseService;

class UserRoleService extends BaseService
{

    /**
     * @param \App\Repositories\UserRoleRepository $repository
     */
    public function __construct(UserRoleRepository $repository)
    {
        $this->repository = $repository;
    }
}
