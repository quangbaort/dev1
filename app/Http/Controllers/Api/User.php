<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Facades\Helper;
use App\Http\Controllers\ApiController;
use App\Http\Requests\User\DeleteMultipleRequest;
use App\Http\Requests\User\ExportUserRequest;
use App\Http\Requests\User\StoreRequest;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use Illuminate\Http\Request;
use App\Http\Resources\BaseResource;

class User extends ApiController
{
    /**
     * Get the list of resource methods which do not have model parameters.
     *
     * @return array
     */
    protected function resourceAbilityMap()
    {
        return array_merge(parent::resourceAbilityMap(), [
            'deleteMultiple' => 'deleteMultiple',
            'exportUser'     => 'exportUser'
        ]);
    }

    /**
     * @var \App\Services\UserService
     */
    protected $userService;

    /**
     * @param \App\Services\UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * List users and filter controller
     */
    public function index()
    {
        return UserResource::collection($this->userService->search());
    }

    /**
     * Add or Edit user
     *
     * @param \App\Http\Requests\User\StoreRequest $request
     *
     * @return \App\Http\Resources\UserResource
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     * @throws \Throwable
     */
    public function store(StoreRequest $request)
    {
        return new UserResource($this->userService->store($request));
    }

    /**
     * view detail users
     */
    public function show(Request $request, $id)
    {
        return new UserResource($this->userService->detail($id, Helper::organId()));
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \App\Http\Resources\UserResource
     */
    public function profile(Request $request)
    {
        if ($request->user()->isSupperAdmin()) {
            return new UserResource($request->user());
        }

        return new UserResource($this->userService->detail($request->user()->id, Helper::organId()));
    }

    /**
     * delete: Delete user
     *
     * @param string $id uuid
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        if ($this->userService->delete($id)) {
            return $this->responseSuccess(trans('message.delete_success'));
        }

        return $this->responseError(trans('message.delete_error'));
    }

    /**
     * Delete users
     */
    public function deleteMultiple(DeleteMultipleRequest $request)
    {
        if ($this->userService->deleteMultiple($request->input('userIds'))) {
            return $this->responseSuccess(trans('message.delete_success'));
        }

        return $this->responseError(trans('message.delete_error'));
    }

    /**
     * Export user data to csv file
     */
    public function exportUser(ExportUserRequest $request)
    {
        return $this->userService->export($request);
    }

    /**
     * Check email of user already exists on system
     */
    public function checkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = $this->userService->checkEmail($request->input('email'));

        if (is_null($user)) {
            return $this->responseSuccess('User does not exist');
        }

        return new UserResource($user);
    }

    /**
     * Check organization account creation limit
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function createAccountLimited()
    {
        if ($this->userService->createAccountLimited()) {
            return response()->json(['msg' => trans('message.account_limit'), 'disabled' => true]);
        }

        return response()->json(['msg' => '', 'disabled' => false]);
    }

    // import user

    public function importUser(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv',
        ]);

        $file = $request->file('file');
        return BaseResource::collection($this->userService->importUser($file));
    }
}
