<?php

declare(strict_types=1);

namespace CommonPHP\Web\Tests\Fixtures;

use CommonPHP\HTTP\Request;
use CommonPHP\HTTP\Response;
use CommonPHP\Router\Contracts\RouteHandlerInterface;
use CommonPHP\Router\RouteMatch;

final class RecordingRouteHandler implements RouteHandlerInterface
{
    public ?Request $request = null;

    public ?RouteMatch $match = null;

    public function handle(Request $request, RouteMatch $match): Response
    {
        $this->request = $request;
        $this->match = $match;

        return new Response('route-handler:' . $match->label());
    }
}
