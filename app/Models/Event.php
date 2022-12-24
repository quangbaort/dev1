<?php

namespace App\Models;

use App\Models\Traits\Loggable;
use App\Models\Traits\Organable;
use App\Models\Traits\Recordable;
use App\Models\Traits\Uuid;
use App\Services\FileSystem\Contracts\FileSystemable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model implements FileSystemable
{
    use Uuid;
    use Recordable;
    use SoftDeletes;
    use HasFactory;
    use Loggable;
    use Organable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'events';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'organization_id',
        'department_id',
        'title',
        'is_allday',
        'is_general_meeting',
        'start',
        'end',
        'place',
        'place_url',
        'detail',
        'detail_url',
        'period',
        'ok_title',
        'cancel_title',
        'theme_color',
        'icon',
        'repeat',
        'repeat_week',
        'repeat_date',
        'repeat_start',
        'repeat_end'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        // 'created_by',
        // 'updated_by',
        'deleted_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_general_meeting' => 'boolean',
        'period' => 'datetime:Y-m-d H:i:s',
        'start' => 'datetime:Y-m-d H:i:s',
        'end' => 'datetime:Y-m-d H:i:s',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
     //protected $appends = ['can_answer'];

    /**
     * Get the class name of the parent model.
     *
     * @return string
     */
    public function getMorphClass()
    {
        return FILE_TYPE_EVENT;
    }

    /**
     * Get the events that the department charge
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param string | array $department
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
     * Builder get the event took place in the period
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
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param string $userId
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInvited($builder, $userId)
    {
        return $builder->whereHas('users', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        });
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $builder
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeMeetingType($builder)
    {
        return $builder->where('is_general_meeting', true);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $builder
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeEventType($builder)
    {
        return $builder->where('is_general_meeting', false);
    }

    /**
     * Response deadline/ User can response invitation
     *
     * @return bool
     */
    public function isAnswerTimeEnd()
    {
        return Carbon::now()->gt($this->period->endOfDay());
    }

    /**
     * Query relationship: List boards of the event
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function departments()
    {
        return $this->belongsToMany(Department::class, EventRefDepartment::class, 'event_id', 'department_id')
            ->withTimestamps();
    }

    /**
     * List of invited users and reply information
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class, EventInviteResponse::class, 'event_id', 'user_id')
            ->withPivot(['notified_at', 'answered_at', 'answer'])
            ->withTimestamps();
    }

    /**
     * List files of event
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function files()
    {
        return $this->hasMany(File::class, 'fileable_id', 'id');
    }
}
