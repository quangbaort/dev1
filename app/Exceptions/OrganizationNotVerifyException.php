<?php

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class OrganizationNotVerifyException extends Exception implements HttpExceptionInterface
{
    /**
     * Create a new organization verify data exception.
     *
     * @param string $message
     * @param int $code
     * @param \Throwable|null $previous
     */
    public function __construct($message = "Organization was not found", $code = 403, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Returns the status code.
     */
    public function getStatusCode(): int
    {
        return 403;
    }

    /**
     * Returns response headers.
     */
    public function getHeaders(): array
    {
        return [];
    }
}
