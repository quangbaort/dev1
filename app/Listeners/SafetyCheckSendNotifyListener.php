<?php

namespace App\Listeners;

use App\Events\SafetyCheckUpdatedEvent;
use App\Jobs\SafetyCheckSendNotifyDaily;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SafetyCheckSendNotifyListener implements ShouldQueue
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
        if ($event instanceof SafetyCheckUpdatedEvent && !$event->resend) {
            return;
        }

        SafetyCheckSendNotifyDaily::dispatch($event->safetyCheck->id);
    }
}
