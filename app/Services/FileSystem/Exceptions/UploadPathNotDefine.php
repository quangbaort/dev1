<?php

namespace App\Services\FileSystem\Exceptions;

use Exception;

class UploadPathNotDefine extends Exception
{
    /**
     * Create a new organization verify data exception.
     *
     * @param string $message
     * @param int $code
     * @param \Throwable|null $previous
     */
    public function __construct($message = 'Path for upload not specified!', $code = 500, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
