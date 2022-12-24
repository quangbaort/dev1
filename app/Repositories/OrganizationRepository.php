<?php

namespace App\Repositories;

use App\Models\Organization;
use App\Repositories\Concerns\BaseRepository;

class OrganizationRepository extends BaseRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Organization::class;
    }
}
