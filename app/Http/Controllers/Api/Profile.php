<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use Illuminate\Http\Request;

class Profile extends ApiController
{
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
     * Get user profile
     */
    public function index(Request $request)
    {
        return UserResource::make(
            $this->userService
                ->loadRelationships($request->user(), $request->organization->get('id'))
        );
    }
}
