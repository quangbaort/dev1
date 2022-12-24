<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Holiday\StoreRequest;
use App\Http\Resources\HolidayResource;
use App\Services\HolidayService;
use Illuminate\Http\Request;

class Holiday extends ApiController
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

    /**
     *
     * @var \App\Services\HolidayService;
     */
    protected $holidayService;

    /**
     * __construct
     *
     * @param \App\Services\HolidayService $holidayService
     */
    public function __construct(HolidayService $holidayService)
    {
        $this->holidayService = $holidayService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        return HolidayResource::collection($this->holidayService->search());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Holiday\StoreRequest $request
     *
     * @return \App\Http\Resources\HolidayResource
     */
    public function store(StoreRequest $request)
    {
        return new HolidayResource($this->holidayService->store($request));
    }

    /**
     * Display the specified resource.
     *
     * @param $id
     *
     * @return \App\Http\Resources\HolidayResource
     */
    public function show($id)
    {
        return new HolidayResource($this->holidayService->find($id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $this->holidayService->delete($id);

        return $this->responseSuccess(trans('message.delete_success'));
    }

    /**
     * Remove selected holiday from storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroyMulti(Request $request)
    {
        $ids = $request->ids;
        $this->holidayService->deleteMulti($ids);
        return $this->responseSuccess(trans('message.delete_success'));
    }

    /**
     * Export holiday data to csv
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function exportCsv(Request $request)
    {
        $ids = $request->ids;
        return $this->holidayService->exportCsv($ids);
    }
}
