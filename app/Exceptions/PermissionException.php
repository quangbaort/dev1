<?php

namespace App\Exceptions;

use Exception;

class PermissionException extends Exception
{
    protected $message = 'You have not permission to delete log';
}
