<?php

namespace App\Models;

use App\Models\Traits\Loggable;
use App\Models\Traits\Organable;
use App\Models\Traits\Recordable;
use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kalnoy\Nestedset\NodeTrait;

class Department extends Model
{
    use HasFactory;
    use Uuid;
    use Recordable;
    use SoftDeletes;
    use NodeTrait;
    use Loggable;
    use Organable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'departments';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'organization_id',
        'parent_id',
        'name',
        'name_kana',
        'disp_order',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        '_lft',
        '_rgt',
        'pivot',
        'created_by',
        'updated_by',
        'deleted_by',
        'deleted_at',
    ];

    /**
     *
     */
    public function safety()
    {
        return $this->belongsToMany(Safety::class, 'users_safety');
    }

    /**
     *
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'users_safety');
    }
}
