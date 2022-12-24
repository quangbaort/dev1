<?php

namespace App\Repositories;

use App\Models\Holiday;
use App\Repositories\Concerns\BaseRepository;

/**
 * Class HolidayRepository.
 *
 * @package namespace App\Repositories;
 */
class HolidayRepository extends BaseRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Holiday::class;
    }
}
