<?php

namespace App\Models;

use App\Models\Traits\Recordable;
use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserDepartment extends Model
{
    use SoftDeletes;
    use HasFactory;

    /**
     * The table with the model.
     *
     * @var string
     */
    protected $table = 'user_departments';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'user_id',
        'department_id',
        'user_disp_order',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'deleted_at',
        'created_by',
        'updated_by',
        'deleted_by',
    ];
}
