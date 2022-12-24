<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SafetyCheckUpdatedEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * @var \App\Models\SafetyCheck
     */
    public $safetyCheck;

    /**
     * @var bool
     */
    public $resend;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($safetyCheck, $resend)
    {
        $this->safetyCheck = $safetyCheck;
        $this->resend = $resend;
    }
}
