<?php

namespace App\Services;

use App\Events\DepartmentUpdated;
use App\Repositories\DepartmentRepository;
use App\Services\Concerns\BaseService;
use Illuminate\Http\Request;
use App\Repositories\UserRoleRepository;
use App\Repositories\UserRepository;

class DepartmentService extends BaseService
{
    /**
     * @var \App\Repositories\UserRoleRepository
     */
    protected $userRoleRepository;

    /**
     * @var \App\Repositories\UserRepository
     */
    protected $userRepository;

    /**
     * @param \App\Repositories\DepartmentRepository $repository
     * @param \App\Repositories\UserRoleRepository $userRoleRepository
     * @param \App\Repositories\UserRepository $userRepository
     */
    public function __construct(
        DepartmentRepository $repository,
        UserRoleRepository $userRoleRepository,
        UserRepository $userRepository
    ) {
        $this->repository = $repository;
        $this->userRoleRepository = $userRoleRepository;
        $this->userRepository = $userRepository;
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
            $this->builder->where(function ($q) {
                $q->where('name', 'LIKE', '%' . $this->filter->get('name') . '%')
                    ->orWhere('name_kana', 'LIKE', '%' . $this->filter->get('name') . '%');
            });
            // Remove condition after apply query builder
            $this->cleanFilterBuilder('name');
        });

        if (!$this->filter->has('parent_id') || $this->filter->get('parent_id') == null) {
            $this->builder->where('parent_id', '=', null);
            // Remove condition after apply query builder
            $this->cleanFilterBuilder('parent_id');
        }
        $this->builder->orderBy('disp_order', 'ASC');

        return $this->endFilter();
    }

    /**
     * Create filter and response listTree by conditions
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return mixed
     */
    public function searchTree(Request $request)
    {
        $currentDepartments = $this->repository->descendantsAndSelf($request->id)->pluck('id');
        $role = $this->userRoleRepository
            ->where(['user_id' => $request->user()->id, 'organization_id' => $request->organization_id])
            ->first('role');
        if (isset($role) && $role->role === USER_ROLE) {
            $departmentOfUser = $this->userRepository
            ->where('id', $request->user()->id)
            ->with('departments')->first()->departments->pluck('id');
            return $this->repository->all()->whereNotIn('id', $currentDepartments)
                    ->where('organization_id', $request->organization_id)
                    ->whereIn('id', $departmentOfUser)
                    ->whereNotIn('id', $currentDepartments)
                    ->sortByDesc('id')->sortByDesc('created_at')->toTree();
        }
        return $this->repository->all()->whereNotIn('id', $currentDepartments)
        ->where('organization_id', $request->organization_id)->sortByDesc('id')->sortBy('disp_order')->toTree();
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
            //update display order of other department
            $this->repository->where(['parent_id' => $request->input('parent_id')])->update(['disp_order' => \DB::raw('disp_order+1')]);
            return $this->repository->create($request->toArray());
        }

        // Get record from DB and check exits
        $record = $this->find($id);
        // Handle update record to DB
        $recordUpdated = $this->repository->updateByModel($record, $request->toArray());

        // Fire event after record updated
        DepartmentUpdated::broadcast($recordUpdated);

        return $recordUpdated;
    }

    /**
     * Delete department by id
     */
    public function delete($id)
    {
        $department = $this->find($id);
        $department->delete();
        return true;
    }

    /**
     * Delete multi departments by array of ids
     */
    public function deleteMulti($ids)
    {
        $departments = $this->find($ids);
        $departments->each(function ($department) {
            $department->delete();
        });
        return true;
    }

    /**
     * update display order
     */
    public function updateDispOrder($request)
    {
        $oldIndex = $request->input('old_index') + 1;
        $newIndex = $request->input('new_index') + 1;
        //move up
        if ($oldIndex > $newIndex) {
            $this->repository->where(['parent_id' => $request->input('parent_id')])
                ->where('disp_order', '<', $oldIndex)
                ->where('disp_order', '>=', $newIndex)
                ->update([
                'disp_order' => \DB::raw('disp_order+1')
            ]);
        } else {
            //move down
            $this->repository->where(['parent_id' => $request->input('parent_id')])
                ->where('disp_order', '>', $oldIndex)
                ->where('disp_order', '<=', $newIndex)
                ->update([
                    'disp_order' => \DB::raw('disp_order-1')
                ]);
        }
        return $this->repository->find($request->input('id'))->update(['disp_order' => $newIndex]);
    }
}
