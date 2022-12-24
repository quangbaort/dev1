<?php

namespace App\Models;

use App\Models\Traits\Loggable;
use App\Models\Traits\Organable;
use App\Models\Traits\Recordable;
use App\Models\Traits\Uuid;
use App\Services\FileSystem\Contracts\FileSystemable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class News extends Model implements FileSystemable
{
    use HasFactory;
    use Uuid;
    use Recordable;
    use SoftDeletes;
    use Loggable;
    use Organable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'news';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'organization_id',
        'department_id',
        'title',
        'image_s3_url',
        'start',
        'end',
        'place',
        'place_url',
        'detail',
        'detail_url',
        'theme_color',
        'icon',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'start' => 'datetime:Y-m-d H:i:s',
        'end' => 'datetime:Y-m-d H:i:s',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['image_url', 'name_image'];

    /**
     * get absolute url
     */
    public function getImageUrlAttribute()
    {
        return is_null($this->image_s3_url) ? null : Storage::url($this->image_s3_url);
    }

    /**
     * get file name
     */
    public function getNameImageAttribute()
    {
        return is_null($this->image_s3_url) ? '' : Str::afterLast($this->image_s3_url, '/');
    }

    /**
     * Query builder get news in department(s)
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param string|array $department
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfDepartment($builder, $department)
    {
        if (is_array($department)) {
            return $builder->whereIn('department_id', $department);
        }

        return $builder->where('department_id', $department);
    }

    /**
     * Builder get the news took place in the period
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param \Carbon\Carbon $startTime
     * @param \Carbon\Carbon $endTime
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInPeriod($builder, $startTime, $endTime)
    {
        return $builder
            ->where('start', '<=', $endTime->toDateTimeString())
            ->where('end', '>=', $startTime->toDateTimeString());
    }

    /**
     * Builder get news has invited user
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param string $userId
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfUser($builder, $userId)
    {
        return $builder->whereHas('users', function ($q) use ($userId) {
            $q->where('users.id', $userId);
        });
    }

    /**
     * Get response info of one user by relationship with() method
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param string $userId
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithUser($builder, $userId)
    {
        return $builder->with(['users' => function ($q) use ($userId) {
            $q->where('users.id', $userId);
        }]);
    }

    /**
     * Get URL mask as read news of user
     *
     * @param $newsId
     * @param $userId
     *
     * @return string
     */
    public function urlMarkAsRead($newsId, $userId)
    {
        return URL::signedRoute(NEWS_MARK_READ_ROUTE, ['nid' => $newsId, 'uid' => $userId]);
    }

    /**
     * Relationship with Department
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function department()
    {
        return $this->hasOne(Department::class, 'id', 'department_id');
    }

    /**
     * Relationship with users need response
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class, NewsResponse::class)->withPivot('read_at')->withTimestamps();
    }

    /**
     * Get the file
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function file()
    {
        return $this->morphOne(File::class, 'fileable');
    }

    /**
     * Get root path when upload
     *
     * @return string
     */
    public function getMorphClass()
    {
        return FILE_TYPE_NEWS;
    }
}
