<?php

namespace App\Listeners;

use App\Events\Contracts\TheEventInterface;
use App\Events\EventCreatedEvent;
use App\Notifications\EventNotification;
use App\Repositories\EventRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendNotifyInviteToEvent implements ShouldQueue
{
    use InteractsWithQueue;
    use SerializesModels;

    public $repository;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(EventRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Handle the event.
     *
     * @param TheEventInterface $event
     *
     * @return void
     */
    public function handle(TheEventInterface $event)
    {
        $eventModel = $event->event;
        $users      = $eventModel->users;

        foreach ($users as $user) {
            try {
                $eventModel->users()->updateExistingPivot($user->id, ['notified_at' => now()]);
            } catch (\Throwable $e) {
                Log::error(
                    'SendNotifyInviteToEvent' . $user . ' ' . $e->getMessage(),
                    ['event' => $eventModel->id, 'user' => $user->id]
                );
            }

            $user->notify(new EventNotification($eventModel));
        }
    }
}
