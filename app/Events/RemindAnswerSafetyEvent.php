<?php

namespace App\Events;

use App\Events\Contracts\SafetyCheckInterface;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RemindAnswerSafetyEvent implements SafetyCheckInterface
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * @var \App\Models\SafetyCheck
     */
    public $safetyCheck;

    /**
     * @var null|string Date format: Y-m-d
     */
    public $dateRemind;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($safetyCheck, $dateRemind = null)
    {
        $this->safetyCheck = $safetyCheck;
        $this->dateRemind = $dateRemind;
    }
}
