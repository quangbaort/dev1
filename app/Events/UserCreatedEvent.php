<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserCreatedEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * @var \App\Models\User
     */
    public $user;

    /**
     * Organization id
     *
     * @var string
     */
    public $organId;

    /**
     * User ID of creator
     *
     * @var string
     */
    public $createdBy;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($user, $organId, $createdBy)
    {
        $this->user = $user;
        $this->organId = $organId;
        $this->createdBy = $createdBy;
    }
}
