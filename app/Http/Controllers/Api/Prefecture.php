<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Facades\Helper;
use App\Http\Controllers\ApiController;
use App\Http\Resources\BaseResource;

class Prefecture extends ApiController
{
    
    /**
     * Get list of prefectures in Japanese
     *
     * @return \App\Http\Resources\BaseResource
     */
    public function index()
    {
        return new BaseResource(Helper::prefectures());
    }
}
