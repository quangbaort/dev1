<?php

namespace App\Models;

use App\Models\Traits\Loggable;
use App\Models\Traits\Organable;
use App\Models\Traits\Recordable;
use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\URL;

class SafetyCheck extends Model
{
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
    protected $table = 'safety_checks';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */

    protected $fillable = [
        'organization_id',
        'department_id',
        'title',
        'detail',
        'detail_url',
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
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /**
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param string $userId
     *
     * @return \Illuminate\Contracts\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder
     */
    public function scopeResponseOfUser($builder, $userId)
    {
        return $builder->whereHas('responses', function ($q) use ($userId) {
            return $q->ofUser($userId);
        });
    }

    public function scopeResponseInDateOfUser($builder, $userId, $notifiedAt)
    {
        return $builder->whereHas('responses', function ($q) use ($userId, $notifiedAt) {
            return $q->ofUser($userId)->where('notified_at', $notifiedAt);
        });
    }

    /**
     * @param $safetyId
     * @param $userId
     * @param $notifyAt
     * @param int $status
     *
     * @return string
     */
    public function answerUrl($safetyId, $userId, $notifyAt, $status = SAFETY_ANSWER_SAFE)
    {
        return URL::signedRoute(
            SAFETY_ANSWER_ROUTE,
            ['id' => $safetyId, 'uid' => $userId, 'nat' => $notifyAt, 'sts' => $status]
        );
    }

    /**
     * Relationship with department
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function department()
    {
        return $this->hasOne(Department::class, 'id', 'department_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function responses()
    {
        return $this->hasMany(SafetyCheckResponse::class, 'safety_check_id');
    }

    /**
     * Relationship with users and response safety
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class, SafetyCheckResponse::class, 'safety_check_id', 'user_id')
            ->groupBy('user_id')
            ->withTimestamps();
    }
}
