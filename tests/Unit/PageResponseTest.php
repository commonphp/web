<?php

declare(strict_types=1);

namespace CommonPHP\Web\Tests\Unit;

use CommonPHP\HTTP\Enums\ResponseStatus;
use CommonPHP\HTTP\HeaderBag;
use CommonPHP\HTTP\Request;
use CommonPHP\Router\Enums\RouteMethod;
use CommonPHP\Router\Route;
use CommonPHP\Router\RouteMatch;
use CommonPHP\UI\Template;
use CommonPHP\UI\View;
use CommonPHP\Web\Contracts\AbstractPage;
use CommonPHP\Web\Exceptions\PageRenderException;
use CommonPHP\Web\PageRenderContext;
use CommonPHP\Web\PageResponse;
use CommonPHP\Web\Tests\Fixtures\RecordingPageRenderer;
use CommonPHP\Web\Tests\Fixtures\SamplePage;
use CommonPHP\Web\Tests\Fixtures\ThrowingPageRenderer;
use PHPUnit\Framework\TestCase;

final class PageResponseTest extends TestCase
{
    public function testItBuildsResponsesFromPages(): void
    {
        $context = $this->context();
        $page = new SamplePage();
        $renderer = new RecordingPageRenderer();

        $response = PageResponse::fromPage($page, $renderer, $context);

        self::assertSame($page, $response->page());
        self::assertSame($context, $response->context());
        self::assertSame(202, $response->statusCode());
        self::assertSame('sample', $response->header('X-Page'));
        self::assertSame('text/html; charset=utf-8', $response->header('Content-Type'));
        self::assertSame((string) strlen($response->body()), $response->header('Content-Length'));
        self::assertSame('page:pages.sample:route "items.show"', $response->body());
        self::assertSame(['page:pages.sample'], $renderer->calls);
    }

    public function testItBuildsResponsesFromViewsAndTemplates(): void
    {
        $context = $this->context();
        $renderer = new RecordingPageRenderer();

        $viewResponse = PageResponse::fromPage(new View('pages.view'), $renderer, $context);
        $templateResponse = PageResponse::fromPage(new Template('pages.template'), $renderer, $context);

        self::assertNull($viewResponse->page());
        self::assertSame('view:pages.view', $viewResponse->body());
        self::assertSame('template:pages.template', $templateResponse->body());
        self::assertSame(['view:pages.view', 'template:pages.template'], $renderer->calls);
    }

    public function testItCanOmitBodiesWhileKeepingContentLength(): void
    {
        $response = PageResponse::fromPage(new SamplePage(), new RecordingPageRenderer(), $this->context(), false);

        self::assertSame('', $response->body());
        self::assertNotSame('0', $response->header('Content-Length'));
    }

    public function testNoBodyStatusesSuppressRenderedBodies(): void
    {
        $page = new class extends AbstractPage {
            protected function template(PageRenderContext $context): string
            {
                return 'pages.empty';
            }

            public function status(PageRenderContext $context): ResponseStatus|int
            {
                return ResponseStatus::NO_CONTENT;
            }
        };

        $response = PageResponse::fromPage($page, new RecordingPageRenderer(), $this->context());

        self::assertSame(204, $response->statusCode());
        self::assertSame('', $response->body());
        self::assertSame('0', $response->header('Content-Length'));
    }

    public function testItMergesHeaderBagHeaders(): void
    {
        $page = new class extends AbstractPage {
            protected function template(PageRenderContext $context): string
            {
                return 'pages.header-bag';
            }

            public function headers(PageRenderContext $context): HeaderBag
            {
                return new HeaderBag(['Content-Type' => 'text/custom', 'X-Custom' => 'yes']);
            }
        };

        $response = PageResponse::fromPage($page, new RecordingPageRenderer(), $this->context());

        self::assertSame('text/custom', $response->header('Content-Type'));
        self::assertSame('yes', $response->header('X-Custom'));
    }

    public function testItWrapsRendererFailures(): void
    {
        $this->expectException(PageRenderException::class);
        $this->expectExceptionMessage('renderer failed');

        PageResponse::fromPage(new SamplePage(), new ThrowingPageRenderer(), $this->context());
    }

    private function context(): PageRenderContext
    {
        $request = new Request('GET', '/items/42');
        $route = Route::get('/items/{id}', static fn (): string => 'item', 'items.show');
        $match = new RouteMatch($route, ['id' => '42'], '/items/42', RouteMethod::GET, 'http');

        return new PageRenderContext($request, $match);
    }
}
