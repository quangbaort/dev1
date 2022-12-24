<?php

namespace App\Services;

use App\Repositories\LogRepository;
use App\Services\Concerns\BaseService;

class LogService extends BaseService
{
    /**
     * @param \App\Repositories\LogRepository $repository
     */
    public function __construct(LogRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Auto paginate with query parameters
     *
     * @param  array  $conditions
     * @return mixed
     */
    public function search($conditions = [])
    {
        $this->makeBuilder($conditions);

        // Search by detail
        $this->filter->when($this->filter->has('detail'), function () {
            $this->builder->where('detail', 'LIKE', '%' . $this->filter->get('detail') . '%');
        });

        // Search by type
        $this->filter->when($this->filter->has('type'), function () {
            $this->builder->where('type', $this->filter->get('type'));
        });

        return $this->endFilter();
    }

    /**
     * delete one log
     *
     * @param  mixed $id: id of Log
     * @return true if delete success
     */
    public function deleteMultiLogs($ids)
    {
        foreach ($ids as $id) {
            $this->delete($id);
        }
        return true;
    }
}
