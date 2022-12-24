<?php

namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class FeatureDisabledException extends HttpException
{
    public function __construct(
        string $message = 'Feature has been disabled',
        \Throwable $previous = null,
        int $code = 0,
        array $headers = []
    ) {
        parent::__construct(403, $message, $previous, $headers, $code);
    }
}
