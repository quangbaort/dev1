<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Department\StoreRequest;
use App\Http\Requests\Department\SearchRequest;
use App\Http\Resources\DepartmentResource;
use App\Services\DepartmentService;
use Illuminate\Http\Request;

class Department extends ApiController
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
     * @var \App\Services\DepartmentService
     */
    protected $departmentService;

    /**
     * @param \App\Services\DepartmentService $departmentService
     */
    public function __construct(DepartmentService $departmentService)
    {
        $this->departmentService = $departmentService;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function index()
    {
        return DepartmentResource::collection($this->departmentService->search());
    }

    /**
     * Return tree list
     * @return tree Department
     */
    public function tree(SearchRequest $request)
    {
        return $this->departmentService->searchTree($request);
    }

    /**
     *Get all department of organization
     */
    public function all()
    {
        return DepartmentResource::collection($this->departmentService->search());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        return new DepartmentResource($this->departmentService->store($request));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Department  $department
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return new DepartmentResource($this->departmentService->find($id));
    }

    /**
     * Delete the specified resource.
     *
     * @param  \App\Models\Department  $department
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->departmentService->delete($id);
        return $this->responseSuccess(trans('message.delete_success'));
    }

    /**
     * Delete selected department.
     *
     * @param  \App\Models\Department  $department
     * @return \Illuminate\Http\Response
     */
    public function destroyMulti(Request $request)
    {
        $ids = $request->ids;
        $this->departmentService->deleteMulti($ids);
        return $this->responseSuccess(trans('message.delete_success'));
    }

    /**
     * save display order after drag drop
     * @param Request $request
     * @return mixed
     */
    public function updateDispOrder(Request $request)
    {
        return $this->departmentService->updateDispOrder($request);
    }
}
