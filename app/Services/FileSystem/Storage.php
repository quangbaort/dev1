<?php

namespace App\Services\FileSystem;

use App\Events\FileUploadedEvent;
use App\Jobs\DeleteUploadedFileJob;
use App\Services\FileSystem\Contracts\FileSystemable;
use App\Services\FileSystem\Exceptions\UploadPathNotDefine;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Storage as FileSystem;
use Symfony\Component\HttpFoundation\File\File;

class Storage
{
    /**
     * @var FileSystemable|string
     */
    protected $pathable;

    /**
     * ID of user actions
     *
     * @var string
     */
    protected $userId;

    /**
     * ID of Organization when request handle with file
     *
     * @var string
     */
    protected $organizationId;

    /**
     * @var string
     */
    protected $fileableType;

    /**
     * Replace name when save to db
     *
     * @var string | array | null
     */
    protected $fileNames;

    /**
     * Check free space of storage
     *
     * @param int $spaceRequest size of file need upload, in bytes
     *
     * @return bool
     */
    public function canUpload($spaceRequest)
    {
        $storageAvail = app(Request::class)->organization->get('storage_avail');

        // convert byte to KB
        return (round($spaceRequest / 1000) < $storageAvail);
    }

    /**
     * Set path for free upload, no need to manage files in the database
     *
     * @param string|FileSystemable $pathName
     *
     * @return $this
     */
    public function path($pathName)
    {
        $this->pathable = $pathName;

        return $this;
    }

    /**
     * Set model
     *
     * @param FileSystemable $model
     *
     * @return $this
     */
    public function ofModel(FileSystemable $model)
    {
        return $this->path($model);
    }

    /**
     * @param string|array $name
     */
    public function withName($name)
    {
        $this->fileNames = $name;

        return $this;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\File\File $file
     *
     * @return string
     * @throws \Throwable
     */
    public function upload(File $file)
    {
        $this->bindings();

        $pathInfo = $this->makePathStructure();
        $pathUpload = implode('/', $pathInfo);
        $uploadName = $this->getFileName($file->guessExtension());

        //Make by: \Illuminate\Http\UploadedFile
        // TODO: Test other class
        $originalName = $file->getClientOriginalName();

        FileSystem::putFileAs($pathUpload, $file, $uploadName);

        FileUploadedEvent::dispatch(
            $pathInfo,
            [
                'size'          => round($file->getSize() / 1000),
                'name'          => $uploadName,
                'name_original' => $originalName,
                'fileable_type' => $this->fileableType,
                'fileable_id' => $this->getFileableId()
            ],
            optional(app('request')->user())->id
        );

        return $pathUpload . '/' . $uploadName;
    }

    /**
     * Upload new file and remove old file
     *
     * @param \Illuminate\Http\File $file
     * @param null|\App\Models\File $oldFiles
     *
     * @return string
     * @throws \Throwable
     */
    public function uploadAndRemove(File $file, $oldFiles)
    {
        $uploaded = $this->upload($file);

        if (!is_null($oldFiles)) {
            $this->delete($oldFiles);
        }

        return $uploaded;
    }

    /**
     * @param string|array $files
     */
    public function delete($files)
    {
        if (empty($files)) {
            return true;
        }

        if ($files instanceof Collection) {
            foreach ($files as $file) {
                try {
                    $this->delete($file);
                } catch (\Throwable $e) {
                    Log::error('Delete file error ' . $e->getMessage());
                }
            }
        }

        if ($files instanceof \App\Models\File) {
            // Remove db file
            $files->delete();
            $files = $files->file_path;
        }

        DeleteUploadedFileJob::dispatch($this->organizationId, $files);

        return true;
    }

    /**
     * @param string $idable
     * @param string $typeable
     *
     * @return bool
     */
    public function changeTypeable($idable, $typeable)
    {
        \App\Models\File::where('fileable_id', $idable)->update(['fileable_type' => $typeable]);

        return true;
    }

    /**
     * @return string
     */
    public function changeFileName($idable, $fileName)
    {
        \App\Models\File::where('fileable_id', $idable)->update(['name' => $fileName]);
        return true;
    }

    /**
     * Bind properties for actions
     *
     * @throws \Throwable
     */
    protected function bindings()
    {
        $this->setPath();
        $this->organizationId = $this->getOrganId();
    }

    /**
     * Get root path/ model type
     *
     * @return string
     * @throws \Throwable
     */
    protected function setPath()
    {
        throw_if(!$this->pathable, UploadPathNotDefine::class);

        if ($this->pathable instanceof FileSystemable) {
            return $this->fileableType = $this->pathable->getMorphClass();
        }

        return $this->fileableType = $this->pathable;
    }

    /**
     * Make path information. Do not change the order of keys
     *
     * @return array
     */
    protected function makePathStructure()
    {
        $rootPath = (is_string($this->pathable) ? 'uploads' : 'files');
        $uploadTime = Carbon::now();

        //Don't change the order of keys
        return [
            'organization_id' => $this->organizationId,
            'root'            => $rootPath,
            'year'            => $uploadTime->year,
            'month'           => $uploadTime->month,
        ];
    }

    /**
     * @return string|null
     */
    private function getOrganId()
    {
        return optional(app(Request::class)->organization)->get('id');
    }

    /**
     * @return string|null
     */
    private function getFileableId()
    {
        if ($this->pathable instanceof FileSystemable) {
            return $this->pathable->getKey();
        }

        return null;
    }

    /**
     * @param string $extension File extension
     *
     * @return string
     */
    private function getFileName($extension)
    {
        return Str::random(40) . '.' . $extension;
    }
}
