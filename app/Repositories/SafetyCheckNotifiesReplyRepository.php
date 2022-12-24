<?php

namespace App\Repositories;

use App\Models\SafetyCheckResponse;
use App\Repositories\Concerns\BaseRepository;

class SafetyCheckNotifiesReplyRepository extends BaseRepository
{

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return SafetyCheckResponse::class;
    }
}
