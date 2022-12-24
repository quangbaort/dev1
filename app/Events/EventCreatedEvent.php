<?php

namespace App\Events;

use App\Events\Contracts\TheEventInterface;
use App\Models\Event;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EventCreatedEvent implements TheEventInterface
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * @var \App\Models\Event
     */
    public $event;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Event $event)
    {
        $this->event = $event;
    }
}
