<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Requests\NotifyGroup\StoreRequest;
use App\Http\Resources\NotifyGroupResource;
use App\Services\NotifyGroupService;
use Illuminate\Http\Request;

class NotifyGroup extends ApiController
{
    /**
     * @var \App\Services\NotifyGroupService
     */
    protected $notifyGroupService;

    /**
     * @param \App\Services\NotifyGroupService $notifyGroupService
     */
    public function __construct(NotifyGroupService $notifyGroupService)
    {
        $this->notifyGroupService = $notifyGroupService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        return NotifyGroupResource::collection(
            $this->notifyGroupService->search(['sort' => ['disp_order' => 'ASC', 'created_at' => 'DESC']])
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\NotifyGroup\StoreRequest $request
     *
     * @return \App\Http\Resources\NotifyGroupResource
     * @throws \Throwable
     */
    public function store(StoreRequest $request)
    {
        return new NotifyGroupResource($this->notifyGroupService->store($request));
    }

    /**
     * Display the specified resource.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $id
     *
     * @return \App\Http\Resources\NotifyGroupResource
     */
    public function show(Request $request, $id)
    {
        return new NotifyGroupResource($this->notifyGroupService->detail($id, $request->organization->get('id')));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param string $id
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($id)
    {
        $this->notifyGroupService->delete($id);

        return $this->responseSuccess(trans('message.delete_success'));
    }

    /**
     * Delete selected NotifyGroup.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroyMulti(Request $request)
    {
        $this->notifyGroupService->deleteMultiple($request->input('ids', []));

        return $this->responseSuccess(trans('message.delete_success'));
    }

    /**
     * save display order after drag drop
     * @param Request $request
     * @return mixed
     */
    public function updateDispOrder(Request $request)
    {
        return $this->notifyGroupService->updateDispOrder($request);
    }
}
