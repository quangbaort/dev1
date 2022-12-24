<?php

namespace App\Models;

use App\Models\Traits\HasCompositePrimaryKey;
use App\Models\Traits\Recordable;
use Illuminate\Database\Eloquent\Model;

class SafetyCheckResponse extends Model
{
    use Recordable;
    use HasCompositePrimaryKey;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'safety_check_responses';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */

    protected $fillable = [
        'safety_check_id',
        'user_id',
        'notified_at',
        'answered_at',
        'answers',
        'comment',
        'response',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'safety_check_id',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'notified_at' => 'date',
    ];

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = ['safety_check_id', 'user_id', 'notified_at'];

    /**
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param string $userId
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfUser($builder, $userId)
    {
        return $builder->where('user_id', $userId);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $builder
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInToday($builder)
    {
        return $builder->checkAt(now()->toDateString());
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param string $notifiedAt Date string: Y-m-d
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCheckAt($builder, $notifiedAt)
    {
        return $builder->where('notified_at', $notifiedAt);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $builder
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUnAnswer($builder)
    {
        return $builder->whereNull('answered_at');
    }

    /**
     * Relationship with Safety
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function safety()
    {
        return $this->belongsTo(SafetyCheck::class, 'safety_check_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
