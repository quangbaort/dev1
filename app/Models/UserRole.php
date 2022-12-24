<?php

namespace App\Models;

use App\Models\Traits\Loggable;
use App\Models\Traits\Recordable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserRole extends Model
{
    use Recordable;
    use SoftDeletes;
    use Loggable;

    /**
     * The table with the model.
     *
     * @var string
     */
    protected $table = 'user_roles';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'user_id',
        'organization_id',
        'role',
        'updated_by',
        'created_by',
    ];

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Records only by organization
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param string $organId
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfOrgan($builder, $organId)
    {
        return $builder->where($this->table . '.organization_id', $organId);
    }
}
