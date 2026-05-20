<?php

declare(strict_types=1);

namespace CommonPHP\Web\Tests\Fixtures;

use CommonPHP\HTTP\Enums\ResponseStatus;
use CommonPHP\UI\Contracts\TemplateInterface;
use CommonPHP\Web\Contracts\AbstractPage;
use CommonPHP\Web\PageRenderContext;

final class SamplePage extends AbstractPage
{
    protected function template(PageRenderContext $context): TemplateInterface|string
    {
        return 'pages.sample';
    }

    public function status(PageRenderContext $context): ResponseStatus|int
    {
        return ResponseStatus::ACCEPTED;
    }

    public function headers(PageRenderContext $context): array
    {
        return ['X-Page' => 'sample'];
    }

    protected function data(PageRenderContext $context): array
    {
        return [
            'title' => 'Sample',
            'id' => $context->routeParameter('id', 'none'),
        ];
    }

    protected function layout(PageRenderContext $context): string
    {
        return 'layouts.main';
    }
}
