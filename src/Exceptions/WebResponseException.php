<?php

declare(strict_types=1);

namespace CommonPHP\Web\Exceptions;

class WebResponseException extends WebException
{
    public static function invalidResult(string $type): self
    {
        return new self('Web route handlers must return a response, page, view, template, string, or null; received ' . $type . '.');
    }

    public static function emptyRedirectLocation(): self
    {
        return new self('Redirect locations cannot be empty.');
    }

    public static function invalidRedirectStatus(int $status): self
    {
        return new self('Redirect responses require a 3xx status code; received ' . $status . '.');
    }
}
