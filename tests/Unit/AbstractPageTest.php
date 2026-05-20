<?php

declare(strict_types=1);

namespace CommonPHP\Web\Tests\Unit;

use CommonPHP\HTTP\Enums\ResponseStatus;
use CommonPHP\HTTP\Request;
use CommonPHP\Router\Enums\RouteMethod;
use CommonPHP\Router\Route;
use CommonPHP\Router\RouteMatch;
use CommonPHP\Web\PageRenderContext;
use CommonPHP\Web\Tests\Fixtures\SamplePage;
use CommonPHP\Web\Tests\Fixtures\StringPage;
use PHPUnit\Framework\TestCase;

final class AbstractPageTest extends TestCase
{
    public function testItBuildsViewsFromTemplateDataAndLayoutHooks(): void
    {
        $context = $this->context(['id' => '42']);
        $page = new SamplePage();
        $view = $page->view($context);

        self::assertSame('pages.sample', $view->template()->name());
        self::assertSame('Sample', $view->data()->get('title'));
        self::assertSame('42', $view->data()->get('id'));
        self::assertSame('layouts.main', $view->layout()?->name());
        self::assertSame(ResponseStatus::ACCEPTED, $page->status($context));
        self::assertSame(['X-Page' => 'sample'], $page->headers($context));
    }

    public function testDefaultPageHooksAreConservative(): void
    {
        $context = $this->context();
        $page = new StringPage();
        $view = $page->view($context);

        self::assertSame('pages.string', $view->template()->name());
        self::assertTrue($view->data()->isEmpty());
        self::assertNull($view->layout());
        self::assertSame(ResponseStatus::OK, $page->status($context));
        self::assertSame([], $page->headers($context));
    }

    /**
     * @param array<string, mixed> $parameters
     */
    private function context(array $parameters = []): PageRenderContext
    {
        $route = Route::get('/pages/{id}', static fn (): string => 'page', 'pages.show');

        return new PageRenderContext(
            new Request('GET', '/pages/42'),
            new RouteMatch($route, $parameters, '/pages/42', RouteMethod::GET, 'http'),
        );
    }
}
