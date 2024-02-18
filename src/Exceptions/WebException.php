<?php

declare(strict_types=1);

namespace CommonPHP\Web\Exceptions;

use Exception;
use Throwable;

/**
 * Class WebException
 *
 * This class represents an exception related to web operations.
 * It extends the base Exception class.
 */
class WebException extends Exception
{
    public function __construct(string $message = "", ?Throwable $previous = null)
    {
        parent::__construct($message, 2300, $previous);
    }
}