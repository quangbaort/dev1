<?php

namespace App\Services;

use App\Helpers\Facades\FileManager;
use App\Http\Requests\Memo\StoreRequest;
use App\Repositories\MemoRepository;
use App\Services\Concerns\BaseService;

class MemoService extends BaseService
{

    /**
     * @param \App\Repositories\MemoRepository $repository
     */
    public function __construct(MemoRepository $repository)
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
        if ($this->filter->has('title')) {
            $this->builder->where('title', 'LIKE', '%' . $this->filter->get('title') . '%');
            // Remove condition after apply query builder
            $this->cleanFilterBuilder('title');
        }

        // sort by start date
        if ($this->filter->has('start') && $this->filter->has('end')) {
            $this->builder->where('start', '<=', $this->filter->get('end'))
                ->where('end', '>=', $this->filter->get('start'));
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
     * @param \App\Http\Requests\Memo\StoreRequest $request
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection|mixed|void
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     * @throws \Prettus\Validator\Exceptions\ValidatorException|\Throwable
     */
    public function store(StoreRequest $request)
    {
        $id = $request->input('id');
        $requestData = $request->except('file');
        // TODO: check organization_id with $user->department->organization_id
        // Check parameter name ID from request.
        // If ID is empty, action is create a new record
        if (!$id) {
            if ($request->file) {
                $requestData['image_s3_url'] = FileManager::path(FILE_TYPE_MEMO)->upload($request->file);
            }
            return $this->repository->create($requestData);
        }

        // Get record from DB and check exits
        $record = $this->find($id);

        // if image has change
        if ($request->hasFile('file')) {
            $requestData['image_s3_url'] = FileManager::path(FILE_TYPE_MEMO)
                ->uploadAndRemove($request->file, $record->image_s3_url);
        } elseif (!$request->filled('old_image')) {
            FileManager::delete($record->image_s3_url);
            $requestData['image_s3_url'] = null;
        }

        // Handle update record to DB
        return $this->repository->updateByModel($record, $requestData);
    }

    /**
     * Delete record in database from request
     *
     * @param uuid $id
     *
     * @return true if delete success
     */
    public function deleteById($id)
    {
        $memo = $this->find($id);

        // Remove file
        FileManager::delete($memo->image_s3_url);

        $memo->delete();

        return true;
    }

    /**
     * Delete selected memos in database from request
     *
     * @param uuid $id
     * @return true if delete success
     */
    public function deleteMulti($ids)
    {
        $memos = $this->find($ids);
        $memos->each(function ($memo) {
            $memo->delete();
        });
        return true;
    }
}
