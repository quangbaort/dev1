<?php

namespace App\Repositories;

use App\Models\NotifyRecipientGroup;
use App\Repositories\Concerns\BaseRepository;

class NotifyGroupRepository extends BaseRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return NotifyRecipientGroup::class;
    }

    /**
     * @param $id
     * @param $organId
     *
     * @return mixed
     */
    public function detail($id, $organId)
    {
        return $this->model
            ->ofOrgan($organId)
            ->with(['users' => function ($q) use ($organId) {
                $q->withCompany($organId);
            }])
            ->findOrFail($id);
    }
}
