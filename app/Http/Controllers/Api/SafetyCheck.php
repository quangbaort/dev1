<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Facades\Helper;
use App\Http\Controllers\ApiController;
use App\Http\Requests\SafetyCheck\DeleteMultipleRequest;
use App\Http\Requests\SafetyCheck\FilterChartRequest;
use App\Http\Requests\SafetyCheck\StoreRequest;
use App\Http\Resources\SafetyResource;
use App\Http\Resources\SafetyStatisticResource;
use App\Services\SafetyCheckService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SafetyCheck extends ApiController
{
    /**
     * @var \App\Services\SafetyCheckService
     */
    protected $safetyService;

    /**
     * @param \App\Services\SafetyCheckService $safetyCheckService
     */
    public function __construct(SafetyCheckService $safetyCheckService)
    {
        $this->safetyService = $safetyCheckService;
    }

    /**
     * List and search
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        return SafetyResource::collection($this->safetyService->search());
    }

    /**
     * Store data from request to database
     *
     * @param \App\Http\Requests\SafetyCheck\StoreRequest $request
     *
     * @return \App\Http\Resources\SafetyResource
     * @throws \Illuminate\Auth\Access\AuthorizationException|\Throwable
     */
    public function store(StoreRequest $request)
    {
        return new SafetyResource($this->safetyService->store($request));
    }

    /**
     * Display the specified resource.
     *
     * @param string $id UUID
     *
     * @return \Illuminate\Http\Resources\Json\JsonResource
     * @throws \Throwable
     */
    public function show($id)
    {
        return new SafetyResource($this->safetyService->detail($id, Helper::organId()));
    }

    /**
     * @param \App\Http\Requests\SafetyCheck\FilterChartRequest $request
     * @param $id
     *
     * @return \Illuminate\Http\Resources\Json\JsonResource
     * @throws \Throwable
     */
    public function chart(FilterChartRequest $request, $id)
    {
        return new SafetyStatisticResource($this->safetyService->chart($id, Helper::organId()));
    }

    /**
     * delete: delete Safety from request
     *
     * @param mixed $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $this->safetyService->destroy($id, Helper::organId());

        return $this->responseSuccess(trans('message.delete_success'));
    }

    /**
     * delete multiple safeties selected
     *
     * @param mixed array id of safeties
     *
     * @return \Illuminate\Http\JsonResponse if delete success
     */
    public function destroyMulti(DeleteMultipleRequest $request)
    {
        $this->safetyService->destroyMulti($request);

        return $this->responseSuccess(trans('message.delete_success'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param $safetyId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function answer(Request $request, $safetyId)
    {
        $this->safetyService->answer(
            $safetyId,
            $request->user()->id,
            $request->input('notified_at'),
            $request->input('answer'),
            $request->input('comment'),
        );

        return $this->responseSuccess(trans('message.answer_safety_success'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param $safetyId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function responseAnswer(Request $request, $safetyId)
    {
        $this->safetyService->responseAnswer(
            $safetyId,
            $request->input('user_id'),
            $request->input('notified_at'),
            $request->input('response'),
        );

        return $this->responseSuccess(trans('message.answer_safety_success'));
    }

    /**
     * @param $safetyId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function remindAnswer($safetyId)
    {
        $this->safetyService->remindAnswer($safetyId, Carbon::now()->toDateString());

        return $this->responseSuccess(trans('message.resend_mail_unanswered_success'));
    }
}
