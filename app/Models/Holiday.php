<?php

namespace App\Models;

use App\Models\Traits\Loggable;
use App\Models\Traits\Recordable;
use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Holiday extends Model
{
    use HasFactory;
    use Uuid;
    use Recordable;
    use SoftDeletes;
    use Loggable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'holidays';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'organization_id',
        'date',
        'name'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /**
     * Relationship with Organization
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function organization()
    {
        return $this->hasOne(Organization::class, 'id', 'organization_id');
    }
}
