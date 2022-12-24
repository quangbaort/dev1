<?php

namespace App\Services;

use App\Events\HolidayUpdated;
use App\Exports\HolidayExport;
use App\Repositories\HolidayRepository;
use App\Services\Concerns\BaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class HolidayService extends BaseService
{

    /**
     * @param \App\Repositories\HolidayRepository $repository
     */
    public function __construct(HolidayRepository $repository)
    {
        $this->repository = $repository;
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
        if ($this->filter->has('name')) {
            $this->builder->where('name', 'LIKE', '%' . $this->filter->get('name') . '%');
            // Remove condition after apply query builder
            $this->cleanFilterBuilder('name');
        }
        // sort by bewteen date
        if ($this->filter->has('start') && $this->filter->has('end')) {
            $this->builder->whereBetween('date', [$this->filter->get('start'), $this->filter->get('end')]);
            // Remove condition after apply query builder
            $this->cleanFilterBuilder('start');
            $this->cleanFilterBuilder('end');
        }
        // sort holiday
        if (!empty($this->filter->get('sort'))) {
            $this->builder->orderBy(
                implode(array_keys($this->filter->get('sort'))),
                implode($this->filter->get('sort'))
            );
            $this->cleanFilterBuilder('sort');
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
        HolidayUpdated::broadcast($recordUpdated);

        return $recordUpdated;
    }

    /**
     * Soft delete by holiday id
     */
    public function delete($id)
    {
        $record = $this->find($id);
        $record->delete();
        return true;
    }

    /**
     * Delete selected holiday in database from request
     *
     * @param uuid $id
     * @return true if delete success
     */
    public function deleteMulti($ids)
    {
        $holidays = $this->find($ids);
        $holidays->each(function ($holiday) {
            $holiday->delete();
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
        $holidays = $this->find($ids);
        $today = Carbon::now()->format('Y-m-d H:i:s');
        return Excel::download(new HolidayExport($holidays), 'holidays' . $today . '.csv');
    }
}
