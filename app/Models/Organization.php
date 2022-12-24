<?php

namespace App\Models;

use App\Models\Traits\Loggable;
use App\Models\Traits\Recordable;
use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Organization extends Model
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
    protected $table = 'organizations';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'simple_name',
        'name_kana',
        'zip_code',
        'prefecture',
        'city',
        'street',
        'building',
        'tel',
        'fax',
        'sort',
        'account_limit',
        'storage_limit',
        'storage_used',
        's3_bucket_name',
        'calendar_enabled',
        'news_enabled',
        'safety_check_enabled',
        'library_enabled',
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
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['storage_avail'];

    /**
     * Get free space of storage for upload
     *
     * @return float|int|mixed
     */
    public function getStorageAvailAttribute()
    {
        // Format storage_limit from Gb to Kb
        return ($this->storage_limit * 1024 * 1024) - $this->storage_used;
    }

    /**
     * Get all user in organization by relationship
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class, UserRole::class);
    }

    public function userRole()
    {
        return $this->belongsTo(UserRole::class);
    }

    public function companies()
    {
        return $this->hasMany(Company::class);
    }
}
