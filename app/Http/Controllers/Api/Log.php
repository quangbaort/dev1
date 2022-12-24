<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Resources\LogResource;
use App\Services\LogService;
use Illuminate\Http\Request;

class Log extends ApiController
{
    /**
     * @var \App\Services\LogService
     */
    protected $logService;

    /**
     * @param \App\Services\LogService $logService
     */
    public function __construct(LogService $logService)
    {
        $this->logService = $logService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param \App\Http\Requests\Log\SearchRequest $request
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return LogResource::collection($this->logService->search());
    }

    /**
     * Delete one log
     *
     * @param  String $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->logService->delete($id);

        return $this->responseSuccess(trans('message.delete_success'));
    }

    /**
     * Delete multi logs
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroyMulti(Request $request)
    {
        $ids = $request->ids;
        $this->logService->deleteMultiLogs($ids);

        return $this->responseSuccess(trans('message.delete_success'));
    }
}
