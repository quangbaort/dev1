<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Resources\BaseResource;

class Role extends ApiController
{
    /**
     * Get list roles can access of system admin
     */
    public function index()
    {
        return new BaseResource([
            ['name' => trans('app.manager'), 'key' => ADMIN_ROLE],
            ['name' =>  trans('app.generally'), 'key' => USER_ROLE]
        ]);
    }
}
