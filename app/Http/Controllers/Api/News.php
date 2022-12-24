<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Requests\News\SearchRequest;
use App\Http\Requests\News\StoreRequest;
use App\Http\Resources\BaseResource;
use App\Http\Resources\NewsResource;
use App\Http\Resources\NewsUserResponseResource;
use App\Http\Resources\UserResource;
use App\Services\NewsService;
use Illuminate\Http\Request;

class News extends ApiController
{
    /**
     * @var NewsService
     */
    protected $newsService;

    /**
     * @param NewsService $newsService
     */
    public function __construct(NewsService $newsService)
    {
        $this->newsService = $newsService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(SearchRequest $request)
    {
        // Use validation request
        unset($request);

        return NewsResource::collection($this->newsService->search());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\News\StoreRequest $request
     *
     * @return \App\Http\Resources\NewsResource
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     * @throws \Throwable
     */
    public function store(StoreRequest $request)
    {
        return new NewsResource($this->newsService->store($request));
    }

    /**
     * Display the specified resource.
     *
     * @param string $id
     *
     * @return \Illuminate\Http\Resources\Json\JsonResource
     * @throws \Throwable
     */
    public function show(Request $request, $id)
    {
        return new NewsResource($this->newsService->detail($id, $request->organization->get('id')));
    }

    /**
     * Remove the specified resource from storage.
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $this->newsService->delete($id);

        return $this->responseSuccess(trans('message.delete_success'));
    }

    /**
     * Delete selected News.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroyMulti(Request $request)
    {
        $this->newsService->deleteMultiple($request->input('ids', []));

        return $this->responseSuccess(trans('message.delete_success'));
    }

    /**
     * Get statistic of news
     *
     * @param \Illuminate\Http\Request $request
     * @param string $id
     *
     * @return \Illuminate\Http\Resources\Json\JsonResource
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function chart(Request $request, $id)
    {
        return new BaseResource($this->newsService->chart($id, $request->organization->get('id')));
    }

    /**
     * Get all users and status's response
     *
     * @param \Illuminate\Http\Request $request
     * @param string $id
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function newsResponses(Request $request, $id)
    {
        return NewsUserResponseResource::collection($this->newsService->users($id, $request->organization->get('id')));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param string $id
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function resendRemind(Request $request, $id)
    {
        $this->newsService->resendRemind($id, $request->organization->get('id'));

        return $this->responseSuccess(trans('message.send_mail_to_user_unread_notify'));
    }
}
