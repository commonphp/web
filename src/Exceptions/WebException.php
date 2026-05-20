<?php

declare(strict_types=1);

namespace CommonPHP\Web\Exceptions;

use RuntimeException;

class WebException extends RuntimeException
{
    public static function because(string $message): static
    {
        return new static($message);
    }
}
