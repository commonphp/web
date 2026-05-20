<?php

declare(strict_types=1);

namespace CommonPHP\Web\Contracts;

use CommonPHP\Router\RouteMatch;
use CommonPHP\Web\PageRenderContext;

interface PageResolverInterface
{
    public function resolve(RouteMatch $match, PageRenderContext $context): mixed;
}
