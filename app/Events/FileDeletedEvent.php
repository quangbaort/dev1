<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FileDeletedEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * @var string
     */
    public $organId;

    /**
     * @var string
     */
    public $path;

    /**
     * Size of file: Kb
     *
     * @var integer
     */
    public $size;

    /**
     * PR key of table: files
     *
     * @var string|null
     */
    public $dbId;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($organId, $path, $size, $dbId = null)
    {
        $this->organId = $organId;
        $this->path = $path;
        $this->size = $size;
        $this->dbId = $dbId;
    }
}
