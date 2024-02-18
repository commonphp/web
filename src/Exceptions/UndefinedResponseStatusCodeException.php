<?php

declare(strict_types=1);

namespace CommonPHP\Web\Exceptions;

use Throwable;

/**
 * Exception thrown when an undefined HTTP response status code is encountered.
 *
 * This exception is thrown when an HTTP response status code is not recognized or not within the
 * standard HTTP status codes.
 */
class UndefinedResponseStatusCodeException extends WebException
{
    public function __construct(int $status, ?Throwable $previous = null)
    {
        parent::__construct('The status '.$status.' is not a known HTTP status: https://developer.mozilla.org/en-US/docs/Web/HTTP/Status', $previous);
        $this->code = 2301;
    }
}