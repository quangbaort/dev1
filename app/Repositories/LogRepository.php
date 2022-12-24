<?php

namespace App\Repositories;

use App\Models\Log;
use App\Repositories\Concerns\BaseRepository;

class LogRepository extends BaseRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Log::class;
    }
}
