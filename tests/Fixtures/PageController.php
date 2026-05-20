<?php

declare(strict_types=1);

namespace CommonPHP\Web\Tests\Fixtures;

use CommonPHP\HTTP\Request;
use CommonPHP\HTTP\Response;
use CommonPHP\Router\RouteMatch;
use CommonPHP\UI\View;
use CommonPHP\Web\PageRenderContext;
use RuntimeException;

final class PageController
{
    public function show(Request $request, RouteMatch $match, PageRenderContext $context): string
    {
        return 'controller:' . $request->path() . ':' . $match->label() . ':' . $context->routeParameter('id', 'missing');
    }

    public function view(Request $request, RouteMatch $match, PageRenderContext $context): View
    {
        return new View('pages.controller', ['id' => $context->routeParameter('id')]);
    }

    public function response(Request $request, RouteMatch $match): Response
    {
        return new Response('response:' . $match->label(), 202, ['X-Controller' => 'yes']);
    }

    public function fail(Request $request, RouteMatch $match): never
    {
        throw new RuntimeException('controller failed');
    }

    public static function staticShow(Request $request, RouteMatch $match): string
    {
        return 'static:' . $request->path() . ':' . $match->label();
    }
}
