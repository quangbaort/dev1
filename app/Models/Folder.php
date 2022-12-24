<?php

namespace App\Models;

use App\Models\Traits\Organable;
use App\Models\Traits\Recordable;
use App\Models\Traits\Uuid;
use App\Services\FileSystem\Contracts\FileSystemable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use JetBrains\PhpStorm\Pure;
use Kalnoy\Nestedset\NodeTrait;

class Folder extends Model implements FileSystemable
{
    use SoftDeletes;
    use Uuid;
    use Organable;
    use Recordable;
    use NodeTrait;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'folders';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'organization_id',
        'parent_id',
        'left',
        'right',
        'name',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'id',
        'organization_id',
        'left',
        'right',
        'updated_at',
        'deleted_at',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['path', 'type'];

    /**
     * Get the lft key name.
     *
     * @return  string
     */
    public function getLftName()
    {
        return 'left';
    }

    /**
     * Get the rgt key name.
     *
     * @return  string
     */
    public function getRgtName()
    {
        return 'right';
    }

    /**
     * Get the parent id key name.
     *
     * @return  string
     */
    public function getParentIdName()
    {
        return 'parent_id';
    }

    /**
     * @return string
     */
    public function getTypeAttribute()
    {
        return $this->getMorphClass();
    }

    /**
     * @return mixed
     */
    public function getPathAttribute()
    {
        return $this->id;
    }

    /**
     * Get root path when upload
     * @return string
     */
    public function getMorphClass()
    {
        return FILE_TYPE_FOLDER;
    }

    /**
     * List files in folders
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function files()
    {
        return $this->morphMany(File::class, 'fileable', FILE_TYPE_FOLDER);
    }
}
