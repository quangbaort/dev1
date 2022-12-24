<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Organization\ExportRequest;
use App\Http\Requests\Organization\StoreRequest;
use App\Http\Requests\Organization\DeleteMultipleRequest;
use App\Http\Resources\OrganizationResource;
use App\Services\OrganizationService;
use Illuminate\Http\Request;

class Organization extends ApiController
{
    /**
     * Get the list of resource methods which do not have model parameters.
     *
     * @return array
     */
    protected function resourceMethodsWithoutModels()
    {
        return ['index','show', 'destroy'];
    }

    /**
     * @var \App\Services\OrganizationService
     */
    protected $organizationService;

    /**
     * @param \App\Services\OrganizationService $organizationService
     */
    public function __construct(OrganizationService $organizationService)
    {
        $this->organizationService = $organizationService;
    }

    /**
     * List and search
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        return OrganizationResource::collection($this->organizationService->search());
    }

    /**
     * Store data from request to database
     *
     * @param \App\Http\Requests\Organization\StoreRequest $request
     *
     * @return \App\Http\Resources\OrganizationResource
     */
    public function store(StoreRequest $request)
    {
        return new OrganizationResource($this->organizationService->store($request));
    }

    /**
     * Display the specified resource.
     *
     * @param string $id UUID
     * @return \App\Http\Resources\OrganizationResource
     */
    public function show($id)
    {
        return new OrganizationResource($this->organizationService->find($id));
    }

    /**
     * delete: delete organization from request
     *
     * @param mixed $id
     * @return \Illuminate\Http\JsonResponse: if delete success then return message success
     */
    public function destroy($id)
    {
        if ($this->organizationService->delete($id)) {
            return $this->responseSuccess(trans('message.delete_success'));
        }
        return $this->responseError(trans('message.delete_error'));
    }

    /**
     * delete: delete organization from request
     *
     * @param mixed $ids
     * @throws \App\Exceptions\PermissionException
     */
    public function deleteMultiple(DeleteMultipleRequest $request)
    {
        if ($this->organizationService->deleteMultiple($request->organizationIds)) {
            return $this->responseSuccess(trans('message.delete_success'));
        }
        return $this->responseError(trans('message.delete_error'));
    }

    /**
     * Export csv
    */
    public function exportCSV(ExportRequest $request)
    {
        return $this->organizationService->export($request);
    }

    /**
     * check Organization has service
    */
    public function checkOrganization(Request $request)
    {
        return new OrganizationResource($this->organizationService->checkOrganization($request));
    }
}
