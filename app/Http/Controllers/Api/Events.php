<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Event\SearchRequest;
use App\Http\Requests\Event\StoreRequest;
use App\Http\Resources\BaseResource;
use App\Http\Resources\EventResource;
use App\Http\Resources\EventStatisticResource;
use App\Services\EventService;
use Illuminate\Http\Request;

class Events extends ApiController
{
    /**
     * @var \App\Services\EventService
     */
    protected $eventService;

    /**
     * @param \App\Services\EventService $eventService
     */
    public function __construct(EventService $eventService)
    {
        $this->eventService = $eventService;
    }

    /**
     * List and search
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(SearchRequest $request)
    {
        unset($request);

        return EventResource::collection($this->eventService->search());
    }

    /**
     * Store data from request to database
     *
     * @param \App\Http\Requests\Event\StoreRequest $request
     *
     * @return \App\Http\Resources\EventResource
     * @throws \Throwable
     */
    public function store(StoreRequest $request)
    {
        return new EventResource($this->eventService->store($request));
    }

    /**
     * Show data from request to database
     *
     * @param \Illuminate\Http\Request $request
     * @param string $id
     *
     * @return \App\Http\Resources\EventResource
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show(Request $request, $id)
    {
        return new EventResource($this->eventService->detail($id, $request->organization->get('id'), $request->user()));
    }

    /**
     * DELETE data from request to database
     */
    public function destroy($id)
    {
        if ($this->eventService->delete($id)) {
            return $this->responseSuccess(trans('message.delete_success'));
        }
        return $this->responseError(trans('message.delete_error'));
    }

    /**
     * Get data chart event
     */
    public function chart($id)
    {
        return new EventStatisticResource($this->eventService->chart($id));
    }

    /**
     * Copy an event
     */
    public function duplicateEvent(Request $request)
    {
        return new EventResource($this->eventService->duplicateEvent($request));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param string $id event Id
     * @param string $answer
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function userResponse(Request $request, $id, $answer)
    {
        $userIdResponse = $request->user()->id;
        if ($request->has('user_id') && $request->user()->isAdminHigher()) {
            $userIdResponse = $request->input('user_id');
        }

        if (!$this->eventService->inviteAnswer($id, $userIdResponse, $answer)) {
            return $this->responseError(trans('app.answer_fail'));
        }

        return $this->responseSuccess(trans('app.answer_success'));
    }

    /**
     * User answer event invited
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function inviteAnswer(Request $request)
    {
        $this->eventService->inviteAnswer($request->input('eid'), $request->input('uid'), $request->input('answer'));

        return view('notify');
    }

    /**
     * Resend notify for user
     *
     * @param $eventId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function remindAnswerInvited($eventId)
    {
        $this->eventService->remindAnswerInvited($eventId);

        return $this->responseSuccess(trans('message.resend_mail_unanswered_success'));
    }
}
