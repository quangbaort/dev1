<?php

namespace App\Repositories;

use App\Models\UserSafety;
use App\Repositories\Concerns\BaseRepository;

class UserSafetyRepository extends BaseRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return UserSafety::class;
    }
}
