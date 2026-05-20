<?php

declare(strict_types=1);

namespace CommonPHP\Web\Exceptions;

use Throwable;

class WebRouteException extends WebException
{
    public static function fromRouter(Throwable $previous): self
    {
        return new self('Web routing failed: ' . $previous->getMessage(), 0, $previous);
    }
}
