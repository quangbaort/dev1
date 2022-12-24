<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Memo\StoreRequest;
use App\Http\Resources\MemoResource;
use App\Services\MemoService;
use Illuminate\Http\Request;

class Memo extends ApiController
{
    /**
     * Get the list of resource methods which do not have model parameters.
     *
     * @return array
     */
    protected function resourceMethodsWithoutModels()
    {
        return ['show', 'destroy'];
    }

    protected $memoService;

    public function __construct(MemoService $memoService)
    {
        $this->memoService = $memoService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        return MemoResource::collection($this->memoService->search());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Memo\StoreRequest $request
     *
     * @return \App\Http\Resources\MemoResource
     */
    public function store(StoreRequest $request)
    {
        return new MemoResource($this->memoService->store($request));
    }

    /**
     * Display the specified resource.
     *
     * @param string $id
     *
     * @return \App\Http\Resources\MemoResource
     */
    public function show($id)
    {
        return new MemoResource($this->memoService->find($id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param string $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $this->memoService->deleteById($id, request()->user());

        return $this->responseSuccess(trans('message.delete_success'));
    }

    /**
     * Remove selected memos from storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroyMulti(Request $request)
    {
        $ids = $request->ids;
        $this->memoService->deleteMulti($ids);
        return $this->responseSuccess(trans('message.delete_success'));
    }
}
