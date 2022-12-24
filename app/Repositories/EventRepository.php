<?php

namespace App\Repositories;

use App\Models\Event;
use App\Repositories\Concerns\BaseRepository;

class EventRepository extends BaseRepository
{
    /**
     * Event Model class name
     *
     * @return string
     */
    public function model()
    {
        return Event::class;
    }

    /**
     * @param $eventId
     * @param $organId
     *
     * @return mixed
     */
    public function detail($eventId, $organId)
    {
        return $this->model->ofOrgan($organId)->findOrFail($eventId);
    }

    /**
     * Get event and only user invited to it
     *
     * @param $eventId
     * @param $userId
     * @param null $organId
     * @param array $loadRelationships
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function findEventAndUserInvited($eventId, $userId, $organId = null, $loadRelationships = [])
    {
        $builder = $this->model->where('id', $eventId)->invited($userId)
            ->with($loadRelationships)
            ->with(['users' => function ($q) use ($userId) {
                $q->where('user_id', $userId);
            }]);

        if (!is_null($organId)) {
            $builder = $builder->ofOrgan($organId);
        }

        return $builder->firstOrFail();
    }

    /**
     * Counter unread news of invited user
     *
     * @param string $organId
     * @param string $userId
     *
     * @return mixed
     */
    public function totalUnread($organId, $userId)
    {
        return $this->model
            ->ofOrgan($organId)
            ->whereHas('users', function ($q) use ($userId) {
                $q->where('users.id', $userId)->whereNull('answered_at');
            })
            ->count();
    }
}
