<?php

declare(strict_types=1);

namespace CommonPHP\Web;

use CommonPHP\UI\Contracts\TemplateInterface;
use CommonPHP\UI\View;
use CommonPHP\UI\ViewFactory;
use CommonPHP\Web\Contracts\PageInterface;
use CommonPHP\Web\Contracts\PageRendererInterface;

final class ViewPageRenderer implements PageRendererInterface
{
    private ViewFactory $views;

    public function __construct(?ViewFactory $views = null)
    {
        $this->views = $views ?? new ViewFactory();
    }

    public function views(): ViewFactory
    {
        return $this->views;
    }

    public function render(PageInterface|View|TemplateInterface $page, PageRenderContext $context): string
    {
        $view = match (true) {
            $page instanceof PageInterface => $page->view($context),
            $page instanceof TemplateInterface => new View($page),
            default => $page,
        };

        return $this->views->render($view);
    }
}
