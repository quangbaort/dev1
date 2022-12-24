<?php

namespace App\Events;

use App\Contracts\WriteLogable;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrganAttachedUserEvent implements WriteLogable
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
     * Creator
     *
     * @var \App\Models\User
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

    /**
     * Action on model
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function onModel()
    {
        return $this->user;
    }

    /**
     * handle on organization. Return by ID string of organization
     *
     * @return string
     */
    public function organId()
    {
        return $this->organId;
    }

    /**
     * The person who executed this action
     *
     * @return \App\Models\User
     */
    public function executor()
    {
        return $this->createdBy;
    }

    /**
     * CREATE_LOG_TYPE, EDIT_LOG_TYPE or DELETE_LOG_TYPE.
     * See constant
     *
     * @return int
     */
    public function logType()
    {
        return CREATE_LOG_TYPE;
    }
}
