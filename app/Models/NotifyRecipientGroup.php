<?php

namespace App\Models;

use App\Models\Traits\Loggable;
use App\Models\Traits\Organable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\Recordable;

class NotifyRecipientGroup extends Model
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
    protected $table = 'notify_recipient_groups';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'organization_id',
        'name',
        'disp_order'
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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class, NotifyRecipient::class, 'group_id', 'user_id')->withTimestamps();
    }
}
