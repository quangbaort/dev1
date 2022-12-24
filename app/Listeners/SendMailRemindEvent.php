<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Notifications\EventNotification;
use App\Repositories\EventRepository;

class SendMailRemindEvent
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public $repository;

    public function __construct(EventRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $event->event->eventNotifiesReplies->each(function ($user) use ($event) {
            $event->event->eventNotifiesReply->each(function ($reply) use ($event, $user) {
                if (is_null($reply->answered_at) && $user->id == $reply->user_id) {
                    $user->notify(new EventNotification($event->event));
                }
            });
        });
    }
}
