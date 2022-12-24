<?php

namespace App\Events;

use App\Services\FileSystem\Contracts\FileBagInterface;
use App\Services\FileSystem\UploadedFileBag;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FileUploadedEvent implements FileBagInterface
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * @var array
     */
    public $pathInfo;

    /**
     * @var array
     */
    public $fileInfo;

    /**
     * ID of User when upload
     *
     * @var string|null
     */
    public $userId;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($pathInfo, $fileInfo, $userId)
    {
        $this->pathInfo = $pathInfo;
        $this->fileInfo = $fileInfo;
        $this->userId   = $userId;
    }

    /**
     * @return \App\Services\FileSystem\UploadedFileBag
     */
    public function getFileBag()
    {
        return new UploadedFileBag($this->pathInfo, $this->fileInfo, $this->userId);
    }
}
