<?php

namespace App\Repositories;

use App\Models\News;
use App\Repositories\Concerns\BaseRepository;

class NewsRepository extends BaseRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return News::class;
    }

    /**
     * Get detail news of organization
     *
     * @param string $newsId
     * @param string $organId
     * @param string | array $withRelations
     *
     * @return mixed
     */
    public function detail($newsId, $organId, $withRelations = ['file', 'department', 'users'])
    {
        return $this->model
            ->with($withRelations)
            ->ofOrgan($organId)
            ->findOrFail($newsId);
    }

    /**
     * Get user's response status in news
     *
     * @param string $newsId
     * @param string $userId
     *
     * @return \Illuminate\Database\Eloquent\Relations\Pivot
     */
    public function responseOfUser($newsId, $userId)
    {
        $user = $this->model
            ->ofUser($userId)
            ->withUser($userId)
            ->findOrFail($newsId)
            ->users->first();

        abort_if(is_null($user), 404, trans('message.news_uninvited_user'));

        return $user->pivot;
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
                $q->where('users.id', $userId)->whereNull('news_responses.read_at');
            })
            ->count();
    }
}
