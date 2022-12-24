<?php

namespace App\Repositories;

use App\Models\SafetyCheck;
use App\Repositories\Concerns\BaseRepository;

class SafetiesCheckRepository extends BaseRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return SafetyCheck::class;
    }

    /**
     * @param string $id
     * @param string $organId
     * @param string | array $withRelationship
     *
     * @return mixed
     */
    public function detail($id, $organId, $withRelationship = ['department', 'users'])
    {
        return $this->model
            ->with($withRelationship)
            ->ofOrgan($organId)
            ->findOrFail($id);
    }

    /**
     * @param string $id Safety check ID
     * @param string $userId
     * @param string $notifyAt Date format: Y-m-d
     *
     * @return mixed
     */
    public function responseOfUser($id, $userId, $notifyAt)
    {
        $safety = $this->model
            ->responseInDateOfUser($userId, $notifyAt)
            ->with(['responses' => function ($q) use ($userId, $notifyAt) {
                $q->checkAt($notifyAt)->ofUser($userId);
            }])
            ->findOrFail($id);

        return $safety->responses->first();
    }

    /**
     * Counter unread news of invited user
     *
     * @param string $organId
     * @param string $userId
     *
     * @return int
     */
    public function totalUnread($organId, $userId)
    {
        return $this->model
            ->ofOrgan($organId)
            ->whereHas('responses', function ($q) use ($userId) {
                return $q->ofUser($userId)->unAnswer()->inToday();
            })
            ->count();
    }
}
