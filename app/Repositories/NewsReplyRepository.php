<?php

namespace App\Repositories;

use App\Models\NewsResponse;
use App\Repositories\Concerns\BaseRepository;

/**
 * Class NewsReplyRepository
 *
 * @package namespace App\Repositories;
 */
class NewsReplyRepository extends BaseRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return NewsResponse::class;
    }
    public function unreadUsers($news_id)
    {
        return NewsResponse::where(['news_id' => $news_id, 'read' => 0])->pluck('user_id');
    }
}
