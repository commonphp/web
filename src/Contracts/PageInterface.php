<?php

declare(strict_types=1);

namespace CommonPHP\Web\Contracts;

use CommonPHP\HTTP\Enums\ResponseStatus;
use CommonPHP\HTTP\HeaderBag;
use CommonPHP\UI\View;
use CommonPHP\Web\PageRenderContext;

interface PageInterface
{
    public function view(PageRenderContext $context): View;

    public function status(PageRenderContext $context): ResponseStatus|int;

    /**
     * @return array<string, mixed>|HeaderBag
     */
    public function headers(PageRenderContext $context): array|HeaderBag;
}
