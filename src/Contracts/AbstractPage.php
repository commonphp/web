<?php

declare(strict_types=1);

namespace CommonPHP\Web\Contracts;

use CommonPHP\HTTP\Enums\ResponseStatus;
use CommonPHP\HTTP\HeaderBag;
use CommonPHP\UI\Contracts\LayoutInterface;
use CommonPHP\UI\Contracts\TemplateInterface;
use CommonPHP\UI\View;
use CommonPHP\UI\ViewData;
use CommonPHP\Web\PageRenderContext;

abstract class AbstractPage implements PageInterface
{
    abstract protected function template(PageRenderContext $context): TemplateInterface|string;

    public function view(PageRenderContext $context): View
    {
        return new View(
            $this->template($context),
            $this->data($context),
            $this->layout($context),
        );
    }

    public function status(PageRenderContext $context): ResponseStatus|int
    {
        return ResponseStatus::OK;
    }

    /**
     * @return array<string, mixed>|HeaderBag
     */
    public function headers(PageRenderContext $context): array|HeaderBag
    {
        return [];
    }

    /**
     * @return array<string, mixed>|ViewData
     */
    protected function data(PageRenderContext $context): array|ViewData
    {
        return [];
    }

    protected function layout(PageRenderContext $context): LayoutInterface|string|null
    {
        return null;
    }
}
