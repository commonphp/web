<?php

declare(strict_types=1);


namespace CommonPHP\Web\Exceptions;

use Throwable;

/**
 * Class UndefinedRequestMethodException
 *
 * This exception is thrown when an undefined HTTP request method is encountered.
 */
class UndefinedRequestMethodException extends WebException
{
    public function __construct(string $requestMethod, ?Throwable $previous = null)
    {
        parent::__construct('The request method '.$requestMethod.' is not a known HTTP request method: https://developer.mozilla.org/en-US/docs/Web/HTTP/Methods', $previous);
        $this->code = 2303;
    }
}