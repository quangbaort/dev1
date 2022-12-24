<?php

namespace App\Services;

use App\Repositories\UserRepository;
use App\Services\Concerns\BaseService;
use Illuminate\Http\Request;
use App\Repositories\UserRoleRepository;
use App\Services\UserService;
class ProfileService extends BaseService
{
    public function __construct(UserRepository $repository, UserRoleRepository $userRoleRepository, UserService $userService)
    {
        $this->repository = $repository;
        $this->userRoleRepository = $userRoleRepository;
        $this->userService = $userService;
    }

    public function getDetailUser($request)
    {
        $request->user_id = $request->user()->id;
        $user = $this->userService->detail($request);
        $user->role = $user->roles->first()->role ?? 1;
        dd($user);
        return $user;
    }

    public function index(Request $request)
    {
        $user = $this->getDetailUser($request);
        return $user;
    }

    public function checkRole($request)
    {
        $roles = $this->getDetailUser($request)->roles;
        if($request->user()->is_super_admin) {
            return true;
        }elseif($roles->count() > 0) {
            return $roles->first()->role;
        }else{
            return false;
        }
    }
}
