<?php

namespace App\Repositories;

use App\Models\Department;
use App\Repositories\Concerns\BaseRepository;

class DepartmentRepository extends BaseRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Department::class;
    }
}
