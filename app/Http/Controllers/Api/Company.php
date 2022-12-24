<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Company\StoreRequest;
use App\Http\Resources\CompanyResource;
use App\Services\CompanyService;
use Illuminate\Http\Request;

class Company extends ApiController
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
     * @var \App\Services\CompanyService
     */
    protected $companyService;

    /**
     * @param \App\Services\CompanyService $companyService
     */
    public function __construct(CompanyService $companyService)
    {
        $this->companyService = $companyService;
    }

    /**
     * List and search
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        return CompanyResource::collection($this->companyService->search());
    }

    /**
     * Store data from request to database
     *
     * @param \App\Http\Requests\Company\StoreRequest $request
     *
     * @return \App\Http\Resources\CompanyResource
     */
    public function store(StoreRequest $request)
    {
        return new CompanyResource($this->companyService->store($request, $request->user()));
    }

    /**
     * Show detail of company by id
     *
     * @param string $id
     *
     * @return \Illuminate\Http\Resources\Json\JsonResource|\Illuminate\Http\JsonResponse
     */

    public function show($id)
    {
        $company = $this->companyService->getDetail($id);
        if (is_null($company)) {
            return $this->responseError(trans('message.not_found', ['item' => 'Company']));
        }
        return new CompanyResource($company);
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
        $this->companyService->delete($id);
        return $this->responseSuccess(trans('message.delete_success'));
    }

    /**
     * Delete selected holiday in database from request
     *
     * @param Request $request
     * @return true if delete success
     */
    public function destroyMulti(Request $request)
    {
        $ids = $request->ids;
        $this->companyService->deleteMulti($ids);
        return $this->responseSuccess(trans('message.delete_success'));
    }

    /**
     * Export selected company
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function exportCsv(Request $request)
    {
        $ids = $request->ids;
        return $this->companyService->exportCsv($ids);
    }
}
