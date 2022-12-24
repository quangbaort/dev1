<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\NewsService;
use Illuminate\Http\Request;

class News extends Controller
{
    /**
     * @var \App\Services\NewsService
     */
    protected $newsService;

    /**
     * @param \App\Services\NewsService $newsService
     */
    public function __construct(NewsService $newsService)
    {
        $this->newsService = $newsService;
    }

    /**
     * User mark as read news
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function markAsRead(Request $request)
    {
        $this->newsService->markAsRead($request->input('nid'), $request->input('uid'));

        return view('notify');
    }
}
