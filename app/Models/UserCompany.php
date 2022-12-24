<?php

namespace App\Models;

use App\Models\Traits\Recordable;
use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserCompany extends Model
{
    use SoftDeletes;
    use HasFactory;

    /**
     * The table with the model.
     *
     * @var string
     */
    protected $table = 'user_companies';

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
        'company_id',
        'updated_by',
        'created_by',
    ];
}
