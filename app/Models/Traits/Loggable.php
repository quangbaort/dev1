<?php

namespace App\Models\Traits;

use App\Helpers\Facades\Helper;
use App\Jobs\SaveUserLogModelJob;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

trait Loggable
{
    /**
     * Booting listener events of model
     */
    public static function bootLoggable()
    {
        self::updated(function ($model) {
            self::logToDb($model, EDIT_LOG_TYPE);
        });

        self::deleted(function ($model) {
            self::logToDb($model, DELETE_LOG_TYPE);
        });

        self::created(function ($model) {
            self::logToDb($model, CREATE_LOG_TYPE);
        });
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param string $logType
     */
    public static function logToDb(Model $model, $logType)
    {
        if (is_null(app('request')->user())) {
            return;
        }

        SaveUserLogModelJob::dispatch(Helper::organId(), $model, $logType, app('request')->user());
    }
}
