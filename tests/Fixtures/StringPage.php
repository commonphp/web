<?php

declare(strict_types=1);

namespace CommonPHP\Web\Tests\Fixtures;

use CommonPHP\UI\Contracts\TemplateInterface;
use CommonPHP\Web\Contracts\AbstractPage;
use CommonPHP\Web\PageRenderContext;

final class StringPage extends AbstractPage
{
    protected function template(PageRenderContext $context): TemplateInterface|string
    {
        return 'pages.string';
    }
}
