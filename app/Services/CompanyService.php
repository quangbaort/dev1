<?php

namespace App\Services;

use App\Events\CompanyUpdated;
use App\Helpers\AppHelpers;
use App\Exports\CompanyExport;
use App\Helpers\Facades\Helper;
use App\Repositories\CompanyRepository;
use App\Services\Concerns\BaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Database\Eloquent\Builder;

class CompanyService extends BaseService
{
    protected $helper;

    /**
     * @param \App\Repositories\CompanyRepository $repository
     */
    public function __construct(CompanyRepository $repository, AppHelpers $helper)
    {
        $this->repository = $repository;
        $this->helper = $helper;
    }

    /**
     * Create filter and response list by conditions
     *
     * @param array $conditions
     *
     * @return mixed
     */
    public function search($conditions = [])
    {
        $this->makeBuilder($conditions);
        $this->filter->when('name', function () {
            $this->builder->where(function (Builder $query) {
                $query->where('name', 'LIKE', '%' . $this->filter->get('name') . '%');
                $query->orWhere('name_kana', 'LIKE', '%' . $this->filter->get('name') . '%');
            });
            $this->cleanFilterBuilder('name');
        });

        $this->filter->when($this->filter->has('prefecture'), function () {
            $this->builder->where('prefecture', $this->filter->get('prefecture'));
            $this->cleanFilterBuilder('prefecture');
        });

        $this->filter->when('city', function () {
            $this->builder->where('city', 'LIKE', '%' . $this->filter->get('city') . '%');
            $this->cleanFilterBuilder('city');
        });

        if ($this->filter->has('tel')) {
            $this->builder->where('tel', 'LIKE', '%' . $this->filter->get('tel') . '%');
            $this->cleanFilterBuilder('tel');
        }

        return $this->endFilter();
    }

    /**
     * Save data to database from request
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection|mixed|void
     */
    public function store(Request $request)
    {
        $id = $request->input('id');
        // Check parameter name ID from request.
        // If ID is empty, action is create a new record
        if (!$id) {
            return $this->repository->create($request->toArray());
        }

        // Get record from DB and check exits
        $record = $this->find($id);

        // Handle update record to DB
        $recordUpdated = $this->repository->updateByModel($record, $request->toArray());

        // Fire event after record updated
        CompanyUpdated::broadcast($recordUpdated);

        return $recordUpdated;
    }

    /**
     * @param string $id
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function getDetail($id)
    {
        return $this->repository
            ->with(['user' => function ($q) {
                $q->withDepartment(Helper::organId());
            }])
            ->findOrFail($id);
    }

    /**
     * delete company by id
     *
     * @param  uuid $id
     * @return true delete success
     * @throws \App\Exceptions\PermissionException
     */
    public function delete($id)
    {
        $record = $this->find($id);
        $record->delete();
        return true;
    }

    /**
     * delete multi company by list id
     *
     * @param  array $id
     * @return true delete success
     */
    public function deleteMulti($ids)
    {
        $companies = $this->find($ids);
        $companies->each(function ($company) {
            $company->delete();
        });
        return true;
    }

    /**
     * export company by ids
     *
     * @param  array $id
     * @return true export success
     */
    public function exportCsv($ids)
    {
        $companies = $this->find($ids);
        $dataCompanies  = [];
        foreach ($companies as $company) {
            $dataCompany = [
                'name'          => $company['name'],
                'prefecture'    => $this->helper->getPrefectureName($company['prefecture']),
                'city'          => $company['city'],
                'tel'           => $company['tel'],
            ];
            array_push($dataCompanies, $dataCompany);
        }
        $name = Carbon::now()->format('YmdHis');
        return Excel::download(new CompanyExport($dataCompanies), 'companies' . $name . '.csv');
    }
}
