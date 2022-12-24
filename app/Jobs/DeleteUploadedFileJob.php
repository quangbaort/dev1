<?php

namespace App\Jobs;

use App\Events\FileDeletedEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Storage;

class DeleteUploadedFileJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @var string
     */
    protected $organId;

    /**
     * @var \App\Models\File
     */
    protected $files;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($organId, $files)
    {
        $this->organId = $organId;
        $this->files = $files;
    }

    /**
     * Execute the job.
     *
     * @return bool
     */
    public function handle()
    {
        if (is_string($this->files)) {
            return $this->deleteFile($this->files);
        }

        //Storage::delete(implode('/', $this->file->pathInfo()));

        return  true;
    }

    /**
     * Delete file by path
     *
     * @param string $file
     *
     * @return bool
     */
    public function deleteFile($file)
    {
        if (!Storage::exists($file)) {
            return false;
        }

        // Get size of file: format to Kb
        $size = Storage::size($file) / 1000;

        // Delete file
        Storage::delete($file);

        // Fire event for handle
        FileDeletedEvent::dispatch($this->organId, $file, $size);

        return true;
    }
}
