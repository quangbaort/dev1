<?php

namespace App\Repositories;

use App\Models\Memo;
use App\Repositories\Concerns\BaseRepository;

class MemoRepository extends BaseRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Memo::class;
    }
}
