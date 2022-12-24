<?php

namespace App\Repositories;

use App\Models\UserCompany;
use App\Repositories\Concerns\BaseRepository;

class UserCompanyRepository extends BaseRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return UserCompany::class;
    }
}
