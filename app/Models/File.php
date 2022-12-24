<?php

namespace App\Models;

use App\Models\Traits\Organable;
use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Kirschbaum\PowerJoins\PowerJoins;

class File extends Model
{
    use SoftDeletes;
    use Uuid;
    use Organable;
    use PowerJoins;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'files';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'organization_id',
        'folder_id',
        'fileable_id',
        'fileable_type',
        'name',
        'title',
        'year',
        'month',
        'file_size',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'fileable_id',
        'fileable_type',
        'deleted_at',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'date:Y/m/d',
        'updated_at' => 'date:Y/m/d',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['is_new', 'url', 'download_url'];


    /**
     * Get full url
     *
     * @return string
     */
    public function getUrlAttribute()
    {
        return Storage::url($this->file_path);
    }

    /**
     * Get full url to download
     *
     * @return string
     */
    public function getDownloadUrlAttribute()
    {
        return URL::temporarySignedRoute(
            FILE_DOWNLOAD,
            now()->addMinutes(30),
            ['path' => $this->file_path, 'name' => $this->title]
        );
    }

    /**
     * Get string origin path of file
     *
     * @return string
     */
    public function getFilePathAttribute()
    {
        return implode('/', $this->pathInfo());
    }

    /**
     * Returns the status of newly uploaded files
     *
     * @return bool
     */
    public function getIsNewAttribute()
    {
        return now()->subWeek()->lte($this->created_at);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param string $type
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeType($builder, $type)
    {
        return $builder->where($this->table . '.fileable_type', ($type == FILE_TYPE_MEETING) ? FILE_TYPE_EVENT : $type);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param string $folder
     * @param string $ofType
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInFolder($builder, $folder, $ofType = FILE_TYPE_FOLDER)
    {
        $findByColumn = ($ofType == FILE_TYPE_FOLDER) ? 'fileable_id' : 'year';

        return $builder->where($findByColumn, $folder);
    }

    /**
     * File storage directory information is arranged in a fixed order
     *
     * @return array
     */
    public function pathInfo()
    {
        return [
            $this->organization_id,
            'files',
            $this->year,
            $this->month,
            $this->name,
        ];
    }

    /**
     * Get the parent of model
     */
    public function fileable()
    {
        return $this->morphTo();
    }

    /**
     * Get record of the notify that are assigned this file.
     */
    public function news()
    {
        return $this->hasOne(News::class, 'id', 'fileable_id');
    }

    /**
     * Get record of the event that are assigned this file.
     */
    public function event()
    {
        return $this->hasOne(Event::class, 'id', 'fileable_id')->where('is_general_meeting', false);
    }

    /**
     * Get record of the event that are assigned this file.
     */
    public function meeting()
    {
        return $this->hasOne(Event::class, 'id', 'fileable_id')->where('is_general_meeting', true);
    }

    /**
     * Get record of the folder
     */
    public function folder()
    {
        return $this->hasOne(Folder::class, 'id', 'fileable_id');
    }

    /**
     * Uploader information
     */
    public function creator()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }
}
