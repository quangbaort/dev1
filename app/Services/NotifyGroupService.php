<?php

namespace App\Services;

use App\Repositories\NotifyGroupRepository;
use App\Services\Concerns\BaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotifyGroupService extends BaseService
{
    /**
     * @param \App\Repositories\NotifyGroupRepository $repository
     */
    public function __construct(NotifyGroupRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Save data to database from request
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection|mixed|void
     * @throws \Throwable
     */
    public function store(Request $request)
    {
        $requester = $request->user();
        $id = $request->input('id');

        // Check parameter name ID from request.
        // If ID is empty, action is create a new record
        if (!$id) {
            $this->authorize('create', $requester);

            $group = $this->repository->create($request->toArray());
            $group->users()->attach($request->user_id);

            return $group;
        }

        // Get record from DB and check exits
        $group = $this->find($id);
        $this->authorize('update', $requester);

        // Handle update record to DB
        $group = $this->repository->updateByModel($group, $request->toArray());

        $group->users()->sync($request->user_id);

        return $group;
    }

    /**
     * @param $groupId
     * @param $organId
     *
     * @return mixed
     */
    public function detail($groupId, $organId)
    {
        return $this->repository->detail($groupId, $organId);
    }

    /**
     * Delete by notify group id
     *
     * @param string $id
     *
     * @return true if delete success
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function delete($id)
    {
        $requester = app('request')->user();
        $this->authorize('delete', $requester);

        $group = $this->find($id);

        $group->users()->delete();

        return $group->delete();
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
            $this->repository->where('disp_order', '<', $oldIndex)
                ->where('disp_order', '>=', $newIndex)
                ->update([
                    'disp_order' => \DB::raw('disp_order+1')
                ]);
        } else {
            //move down
            $this->repository->where('disp_order', '>', $oldIndex)
                ->where('disp_order', '<=', $newIndex)
                ->update([
                    'disp_order' => \DB::raw('disp_order-1')
                ]);
        }
        return $this->repository->find($request->input('id'))->update(['disp_order' => $newIndex]);
    }
}
