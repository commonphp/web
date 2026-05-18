<?php

declare(strict_types=1);

namespace CommonPHP\Web;

use CommonPHP\HTTP\Contracts\HttpSurfaceInterface;
use CommonPHP\HTTP\Request;
use CommonPHP\HTTP\Response;
use CommonPHP\HTTP\ResponseFactory;

class WebSurface implements HttpSurfaceInterface
{
    public function supports(Request $request): bool
    {
        return true;
    }

    public function handle(Request $request): Response
    {
        return (new ResponseFactory())->html('<h1>Not Found</h1>', 404);
    }
}
