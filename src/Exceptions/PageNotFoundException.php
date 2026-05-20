<?php

declare(strict_types=1);

namespace CommonPHP\Web\Exceptions;

use CommonPHP\HTTP\Request;

class PageNotFoundException extends WebException
{
    public static function forName(string $name): self
    {
        return new self('No web page is registered with name "' . $name . '".');
    }

    public static function forPath(string $path): self
    {
        return new self('No web page matched "' . $path . '".');
    }

    public static function forRequest(Request $request): self
    {
        return self::forPath($request->path());
    }
}
