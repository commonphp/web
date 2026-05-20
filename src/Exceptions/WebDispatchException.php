<?php

declare(strict_types=1);

namespace CommonPHP\Web\Exceptions;

use CommonPHP\Router\RouteMatch;
use Throwable;

class WebDispatchException extends WebException
{
    public static function invalidHandler(RouteMatch $match, string $type): self
    {
        return new self('Invalid web route handler ' . $type . ' for ' . $match->label() . '.');
    }

    public static function invalidPage(string $name, string $type): self
    {
        return new self('Registered web page "' . $name . '" resolved to ' . $type . ' instead of a page.');
    }

    public static function failed(RouteMatch $match, Throwable $previous): self
    {
        return new self('Web route handler failed for ' . $match->label() . ': ' . $previous->getMessage(), 0, $previous);
    }
}
