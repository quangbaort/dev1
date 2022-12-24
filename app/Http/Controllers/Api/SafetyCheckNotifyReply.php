<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Requests\SafetyCheckNotifiesReply\StoreRequest;
use App\Http\Resources\SafetyStatisticResource;
use App\Http\Resources\ChartReplySafetyResource;
use App\Services\SafetyRepliesService;
use Illuminate\Http\Request;

class SafetyCheckNotifyReply extends ApiController
{

    /**
     * @var \App\Services\SafetyRepliesService
     */
    protected $safetyRepliesService;

    /**
     * @param \App\Services\SafetyRepliesService $SafetyService
     */
    public function __construct(SafetyRepliesService $safetyRepliesService)
    {
        $this->safetyRepliesService = $safetyRepliesService;

    }

    /**
     * List and search
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        return SafetyStatisticResource::collection($this->safetyRepliesService->search($request));
    }

    /**
     * Store data from request to database
     *
     * @param \App\Http\Requests\SafetyCheckNotifiesReply\StoreRequest $request
     *
     * @return \App\Http\Resources\SafetyStatisticResource
     */
    public function store(StoreRequest $request)
    {
        $this->safetyRepliesService->store($request);
        return $this->responseSuccess(trans('message.answer_safety_success'));
    }

    /**
     * View data from request to database
     *
     * @param \App\Http\Requests
     *
     * @return \App\Http\Resources\SafetyStatisticResource
     */
    public function view(Request $request)
    {
        return new SafetyStatisticResource($this->safetyRepliesService->view($request));
    }

    /**
     * Get comment of safety reply
     *
     * @param \App\Http\Requests
     *
     * @return \App\Http\Resources\SafetyStatisticResource
     */
    public function comment(Request $request)
    {
        return new SafetyStatisticResource($this->safetyRepliesService->getComment($request));
    }

    /**
     * Response reply safety
     *
     * @param \App\Http\Requests
     * @return true if update success
     */
    public function updateResponseReply(Request $request)
    {
        if ($this->safetyRepliesService->updateResponseReply($request)) {
            return $this->responseSuccess(trans('message.answer_safety_success'));
        }
        return $this->responseError(trans('message.not_found'));
    }

    /**
     * Chart reply safety
     *
     * @param \App\Http\Requests
     *
     * @return \App\Http\Resources\SafetyStatisticResource
     */
    public function chart(Request $request)
    {
        return new ChartReplySafetyResource($this->safetyRepliesService->chart($request));
    }

    /**
     * Resend notifications to those who haven't responded
     */
    public function resendEmail(Request $request)
    { 
        if ($this->safetyRepliesService->resendEmail($request)) {
            return $this->responseSuccess(trans('message.resend_mail_unanswered_success'));
        }
        return $this->responseError(trans('message.resend_mail_unanswered_failed'));
    }

    /**
     * Delete safeties selected
     * @param \App\Http\Request
     * @return true
    */
    public function deleteMultiple(Request $request)
    {
        if ($this->safetyRepliesService->deleteMultiple($request)) {
            return $this->responseSuccess(trans('message.delete_success'));
        }
        return $this->responseError(trans('message.delete_error'));
    }

    /**
     * Delete safeties selected
     * @param \App\Http\Request
     * @return true
    */
    public function deleteOne(Request $request)
    {
        if ($this->safetyRepliesService->deleteOne($request)) {
            return $this->responseSuccess(trans('message.delete_success'));
        }
        return $this->responseError(trans('message.delete_error'));
    }

    public function answerSafety(Request $request)
    {
        if ($this->safetyRepliesService->answerSafety($request)) {
            return redirect('/notify')->with('success',trans('message.answer_safety_success'));
        }else{
            return redirect('/notify')->with('error',trans('message.answer_safety_fail'));
        }
    }

    public function updateAnswer(Request $request)
    {
        if ($this->safetyRepliesService->answerSafety($request)) {
            return $this->responseSuccess(trans('message.answer_safety_success'));
        }
        return $this->responseError(trans('message.not_found'));
    }
}
