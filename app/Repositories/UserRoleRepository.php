<?php

namespace App\Repositories;

use App\Models\UserRole;
use App\Repositories\Concerns\BaseRepository;

class UserRoleRepository extends BaseRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return UserRole::class;
    }
}
