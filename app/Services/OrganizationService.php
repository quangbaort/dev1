<?php

namespace App\Services;

use App\Exports\OrganizationExport;
use App\Helpers\AppHelpers;
use App\Repositories\OrganizationRepository;
use App\Services\Concerns\BaseService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel as FacadesExcel;

class OrganizationService extends BaseService
{
    protected $helper;

    /**
     * @param \App\Repositories\OrganizationRepository $repository
     */
    public function __construct(OrganizationRepository $repository, UserRoleService $userRoleService, AppHelpers $helper)
    {
        $this->repository = $repository;
        $this->userRoleService = $userRoleService;
        $this->helper = $helper;
    }

    /**
     * Auto paginate with query parameters
     *
     * @param array $conditions
     *
     * @return mixed
     */
    public function search($conditions = [])
    {
        $this->makeBuilder($conditions);

        $this->builder->with('users');

        // When or$organizations search by email, need to search with LIKE condition and process all records
        if ($this->filter->has('name')) {
            $this->builder->where('name', 'LIKE', '%' . $this->filter->get('name') . '%');
            // Remove condition after apply query builder
            $this->cleanFilterBuilder('name');
        }

        $this->filter->when($this->filter->has('prefecture'), function () {
            $this->builder->where('prefecture', $this->filter->get('prefecture'));
        });

        if ($this->filter->has('city')) {
            $this->builder->where('city', 'LIKE', '%' . $this->filter->get('city') . '%');
            // Remove condition after apply query builder
            $this->cleanFilterBuilder('city');
        }

        if ($this->filter->has('tel')) {
            $this->builder->where('tel', 'LIKE', '%' . $this->filter->get('tel') . '%');
            // Remove condition after apply query builder
            $this->cleanFilterBuilder('tel');
        }

        if ($this->filter->has('user_id')) {
            $this->builder->whereHas('users', function (Builder $query) {
                $query->where('user_id', $this->filter->get('user_id'));
            });
            // Remove condition after apply query builder
            $this->cleanFilterBuilder('user_id');
        }

        if (!empty($this->filter->get('sort'))) {
            $this->builder->orderBy(
                implode(array_keys($this->filter->get('sort'))),
                implode($this->filter->get('sort'))
            );
            $this->cleanFilterBuilder('sort');
        } else {
            $this->builder->orderBy('created_at', 'DESC');
        }

        return $this->endFilter();
    }

    /**
     * Save data to database from request
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return updated Organization Object
     */
    public function store(Request $request)
    {
        $id = $request->input('id');
        // Check parameter name ID from request.
        // If ID is empty, action is create a new record
        $data = $request->toArray();

        if (!$request->user()->is_super_admin) {
            $data = $request->except(
                [
                    'sort', 'calendar_enabled', 'news_enabled', 'safety_check_enabled',
                    'library_enabled', 'account_limit', 'storage_limit'
                ]
            );
        }

        if (!$id) { // in case create new organization
            return $this->repository->create($data);
        }

        // in case update organization
        // Get record from DB and check exits
        $record = $this->find($id);
        // Handle update record to DB
        return $this->repository->updateByModel($record, $data);
    }

    /**
     * delete multiple organization
     *
     * @param  mixed $ids: array id of organization
     * @return true if delete success
     */
    public function deleteMultiple($ids)
    {
        foreach ($ids as $id) {
            $this->delete($id);
        }
        return true;
    }

    /**
     * export list of organization to csv file
     */
    public function export($request)
    {
        $organizations      = $this->find($request->organizationIds);
        $dataOrganizations  = [];

        foreach ($organizations as $organization) {
            $dataOrganization = [
                'name'          => $organization['name'],
                'prefecture'    => $this->helper->getPrefectureName($organization['prefecture']),
                'city'          => $organization['city'],
                'tel'           => $organization['tel'],
            ];
            array_push($dataOrganizations, $dataOrganization);
        }

        $today = Carbon::now()->format('YmdHis');
        return FacadesExcel::download(new OrganizationExport($dataOrganizations), 'organizations' . $today . '.csv');
    }

    /**
     * check organization
     */
    public function checkOrganization($request)
    {
        $adminRole = $this->userRoleService->makeBuilder($request)
            ->where('user_id', $request->user()->id)
            ->where('role', $request->user()->code)->first();
        return $this->find($adminRole->organization_id);
    }
}
