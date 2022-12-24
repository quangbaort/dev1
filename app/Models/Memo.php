<?php

namespace App\Models;

use Illuminate\Support\Str;
use Storage;
use App\Models\Traits\Loggable;
use App\Models\Traits\Recordable;
use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Memo extends Model
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
    protected $table = 'memos';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'organization_id',
        'start',
        'end',
        'title',
        'detail',
        'theme_color',
        'icon',
        'image_s3_url',
        'url',
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
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['image_url', 'name_image'];

    /**
     * Get full url of image file by relationship
     *
     * @return string
     */
    public function getImageUrlAttribute()
    {
        return is_null($this->image_s3_url) ? null : Storage::url($this->image_s3_url);
    }

    /**
     * get image name
     */
    public function getNameImageAttribute()
    {
        return is_null($this->image_s3_url) ? '' : Str::afterLast($this->image_s3_url, '/');
    }
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
