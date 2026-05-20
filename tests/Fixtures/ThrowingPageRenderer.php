<?php

declare(strict_types=1);

namespace CommonPHP\Web\Tests\Fixtures;

use CommonPHP\UI\Contracts\TemplateInterface;
use CommonPHP\UI\View;
use CommonPHP\Web\Contracts\PageInterface;
use CommonPHP\Web\Contracts\PageRendererInterface;
use CommonPHP\Web\PageRenderContext;
use RuntimeException;

final class ThrowingPageRenderer implements PageRendererInterface
{
    public function render(PageInterface|View|TemplateInterface $page, PageRenderContext $context): string
    {
        throw new RuntimeException('renderer failed');
    }
}
