<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ResendRemindNewsEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * @var \App\Models\News
     */
    public $news;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($news)
    {
        $this->news = $news;
    }
}
