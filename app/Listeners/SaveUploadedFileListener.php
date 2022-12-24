<?php

namespace App\Listeners;

use App\Services\FileSystem\Contracts\FileBagInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SaveUploadedFileListener implements ShouldQueue
{
    use InteractsWithQueue;
    use SerializesModels;

    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle(FileBagInterface $event)
    {
        $fileBag = $event->getFileBag();

        if (!$fileBag->needManage()) {
            return;
        }

        $fileBag->saveToDB();
    }
}
