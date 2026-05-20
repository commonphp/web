<?php

declare(strict_types=1);

namespace CommonPHP\Web\Tests\Unit;

use CommonPHP\HTTP\Request;
use CommonPHP\Router\Enums\RouteMethod;
use CommonPHP\Router\Route;
use CommonPHP\Router\RouteMatch;
use CommonPHP\UI\Template;
use CommonPHP\UI\View;
use CommonPHP\UI\ViewFactory;
use CommonPHP\Web\PageRenderContext;
use CommonPHP\Web\Tests\Fixtures\RecordingRenderer;
use CommonPHP\Web\Tests\Fixtures\SamplePage;
use CommonPHP\Web\ViewPageRenderer;
use PHPUnit\Framework\TestCase;

final class ViewPageRendererTest extends TestCase
{
    public function testItRendersPagesViewsAndTemplatesThroughAViewFactory(): void
    {
        $uiRenderer = new RecordingRenderer();
        $viewFactory = new ViewFactory($uiRenderer);
        $renderer = new ViewPageRenderer($viewFactory);
        $context = $this->context();

        self::assertSame($viewFactory, $renderer->views());
        self::assertSame('rendered:pages.sample:42', $renderer->render(new SamplePage(), $context));
        self::assertSame('rendered:pages.custom:99', $renderer->render(new View('pages.custom', ['id' => '99']), $context));
        self::assertSame('rendered:pages.template:none', $renderer->render(new Template('pages.template'), $context));
        self::assertSame([
            'render:pages.sample',
            'render:pages.custom',
            'render:pages.template',
        ], $uiRenderer->calls);
    }

    private function context(): PageRenderContext
    {
        $request = new Request('GET', '/items/42');
        $route = Route::get('/items/{id}', static fn (): string => 'item', 'items.show');
        $match = new RouteMatch($route, ['id' => '42'], '/items/42', RouteMethod::GET, 'http');

        return new PageRenderContext($request, $match);
    }
}
