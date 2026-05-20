<?php

declare(strict_types=1);

namespace CommonPHP\Web\Contracts;

use CommonPHP\UI\Contracts\TemplateInterface;
use CommonPHP\UI\View;
use CommonPHP\Web\PageRenderContext;

interface PageRendererInterface
{
    public function render(PageInterface|View|TemplateInterface $page, PageRenderContext $context): string;
}
