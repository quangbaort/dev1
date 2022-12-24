<?php

namespace App\Listeners;

use App\Contracts\WriteLogable;
use App\Jobs\SaveUserLogModelJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SaveActivityLogListener implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     *
     * @param object $event
     *
     * @return void
     */
    public function handle($event)
    {
        if (!$event instanceof WriteLogable) {
            return;
        }

        // Save log
        SaveUserLogModelJob::dispatch($event->organId(), $event->onModel(), $event->logType(), $event->executor());
    }
}
