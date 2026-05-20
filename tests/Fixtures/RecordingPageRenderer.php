<?php

declare(strict_types=1);

namespace CommonPHP\Web\Tests\Fixtures;

use CommonPHP\UI\Contracts\TemplateInterface;
use CommonPHP\UI\View;
use CommonPHP\Web\Contracts\PageInterface;
use CommonPHP\Web\Contracts\PageRendererInterface;
use CommonPHP\Web\PageRenderContext;

final class RecordingPageRenderer implements PageRendererInterface
{
    /**
     * @var list<string>
     */
    public array $calls = [];

    public function render(PageInterface|View|TemplateInterface $page, PageRenderContext $context): string
    {
        if ($page instanceof PageInterface) {
            $view = $page->view($context);
            $this->calls[] = 'page:' . $view->template()->name();

            return 'page:' . $view->template()->name() . ':' . $context->routeLabel();
        }

        if ($page instanceof View) {
            $this->calls[] = 'view:' . $page->template()->name();

            return 'view:' . $page->template()->name();
        }

        $this->calls[] = 'template:' . $page->name();

        return 'template:' . $page->name();
    }
}
