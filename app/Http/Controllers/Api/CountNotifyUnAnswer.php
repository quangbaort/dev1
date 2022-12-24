<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\EventService;
use App\Services\NewsService;
use App\Services\SafetyCheckService;
use Illuminate\Http\Request;

class CountNotifyUnAnswer extends Controller
{
    /**
     * @var \App\Services\NewsService
     */
    protected $newsService;

    /**
     * @var \App\Services\EventService
     */
    protected $eventService;

    /**
     * @var SafetyCheckService
     */
    protected $safetyCheckService;

    /**
     * @param \App\Services\NewsService $newsService
     * @param \App\Services\EventService $eventService
     * @param \App\Services\SafetyCheckService $safetyCheckService
     */
    public function __construct(
        NewsService $newsService,
        EventService $eventService,
        SafetyCheckService $safetyCheckService
    ) {
        $this->newsService  = $newsService;
        $this->eventService = $eventService;
        $this->safetyCheckService = $safetyCheckService;
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $organId = $request->organization->get('id');
        $userId  = $request->user()->id;

        $result = [
            'news'        => 0,
            'event'       => 0,
            'safetyCheck' => 0,
        ];

        if ($request->user()->isSupperAdmin()) {
            return response()->json(['count' => $result]);
        }

        $result['news']  = $this->newsService->countUnread($organId, $userId);
        $result['event'] = $this->eventService->countUnread($organId, $userId);
        $result['safetyCheck'] = $this->safetyCheckService->countUnread($organId, $userId);

        return response()->json(['count' => $result]);
    }
}
