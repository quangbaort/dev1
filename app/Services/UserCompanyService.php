<?php

namespace App\Services;

use App\Repositories\UserCompanyRepository;
use App\Services\Concerns\BaseService;

class UserCompanyService extends BaseService
{
    /**
     * @param \App\Repositories\UserCompanyRepository $repository
     */
    public function __construct(UserCompanyRepository $repository)
    {
        $this->repository = $repository;
    }
}
