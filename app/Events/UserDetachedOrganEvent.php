<?php

namespace App\Events;

use App\Contracts\WriteLogable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserDetachedOrganEvent implements WriteLogable
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * @var \App\Models\User
     */
    public $user;

    /**
     * @var string
     */
    public $organId;

    /**
     * The person who executed this action
     *
     * @var \App\Models\User
     */
    public $executor;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($user, $organId, $executor)
    {
        $this->user = $user;
        $this->organId = $organId;
        $this->executor = $executor;
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
        return $this->executor;
    }

    /**
     * CREATE_LOG_TYPE, EDIT_LOG_TYPE or DELETE_LOG_TYPE.
     * See constant
     *
     * @return int
     */
    public function logType()
    {
        return DELETE_LOG_TYPE;
    }
}
