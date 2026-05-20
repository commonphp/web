<?php

declare(strict_types=1);

namespace CommonPHP\Web\Exceptions;

use Throwable;

class PageRenderException extends WebException
{
    public static function forTarget(string $target, Throwable $previous): self
    {
        return new self('Unable to render web page "' . $target . '": ' . $previous->getMessage(), 0, $previous);
    }
}
