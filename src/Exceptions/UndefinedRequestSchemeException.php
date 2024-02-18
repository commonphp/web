<?php

declare(strict_types=1);

namespace CommonPHP\Web\Exceptions;

use Throwable;

/**
 * Class UndefinedRequestSchemeException
 *
 * Exception that is thrown when the provided request scheme is not a known HTTP request scheme.
 * This class extends the WebException class.
 */
class UndefinedRequestSchemeException extends WebException
{
    public function __construct(string $requestScheme, ?Throwable $previous = null)
    {
        parent::__construct('The request scheme '.$requestScheme.' is not a known HTTP request scheme: http,https', $previous);
        $this->code = 2302;
    }
}