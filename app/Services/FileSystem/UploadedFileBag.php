<?php

namespace App\Services\FileSystem;

use App\Models\File;

class UploadedFileBag
{
    /**
     * @var array
     */
    protected $pathInfo;

    /**
     * @var array
     */
    protected $fileInfo;

    /**
     * @var string
     */
    protected $userId;

    /**
     * @param array $pathInfo
     * @param array $fileInfo
     */
    public function __construct($pathInfo, $fileInfo, $userId = null)
    {
        $this->pathInfo = $pathInfo;
        $this->fileInfo = $fileInfo;
        $this->userId = $userId;
    }

    /**
     * @return string|null
     */
    public function organId()
    {
        return $this->pathInfo['organization_id'];
    }

    /**
     * Size of file uploaded, in Kb
     */
    public function fileSize()
    {
        return $this->fileInfo['size'] ?? null;
    }

    /**
     * Morpho ID
     *
     * @return string|null
     */
    public function fileableId()
    {
        return $this->fileInfo['fileable_id'] ?? null;
    }

    /**
     * Determined the data that can be stored into the database and manage it
     *
     * @return bool
     */
    public function needManage()
    {
        return !is_null($this->fileableId());
    }

    /**
     * Save uploaded file to datababse
     *
     * @return File
     */
    public function saveToDB()
    {
        $database = [
            'organization_id' => $this->organId(),
            'year'            => $this->pathInfo['year'],
            'month'           => $this->pathInfo['month'],
            'fileable_type'   => $this->fileInfo['fileable_type'],
            'fileable_id'     => $this->fileableId(),
            'name'            => $this->fileInfo['name'],
            'title'           => $this->fileInfo['name_original'],
            'file_size'       => $this->fileSize(),
            'created_by'      => $this->userId,
            'updated_by'      => $this->userId,
        ];

        return File::create($database);
    }
}
