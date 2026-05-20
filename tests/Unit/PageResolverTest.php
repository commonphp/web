<?php

declare(strict_types=1);

namespace CommonPHP\Web\Tests\Unit;

use CommonPHP\HTTP\Request;
use CommonPHP\HTTP\Response;
use CommonPHP\Router\Enums\RouteMethod;
use CommonPHP\Router\Route;
use CommonPHP\Router\RouteMatch;
use CommonPHP\Web\Exceptions\WebDispatchException;
use CommonPHP\Web\PageRegistry;
use CommonPHP\Web\PageRenderContext;
use CommonPHP\Web\PageResolver;
use CommonPHP\Web\Tests\Fixtures\PageController;
use CommonPHP\Web\Tests\Fixtures\RecordingRouteHandler;
use CommonPHP\Web\Tests\Fixtures\SamplePage;
use CommonPHP\Web\Tests\Fixtures\StringPage;
use PHPUnit\Framework\TestCase;
use stdClass;

final class PageResolverTest extends TestCase
{
    public function testItExposesTheRegistryAndResolvesRegisteredPages(): void
    {
        $sample = new SamplePage();
        $registry = new PageRegistry([
            'sample' => $sample,
            'string' => StringPage::class,
        ]);
        $resolver = new PageResolver($registry);

        self::assertSame($registry, $resolver->pages());
        self::assertSame($sample, $resolver->resolve(...$this->match('sample')));
        self::assertInstanceOf(StringPage::class, $resolver->resolve(...$this->match('string')));
    }

    public function testItResolvesPageClassesDirectly(): void
    {
        $resolver = new PageResolver();

        self::assertInstanceOf(SamplePage::class, $resolver->resolve(...$this->match(SamplePage::class)));
    }

    public function testItCallsCallableHandlersWithRequestMatchAndOptionalContext(): void
    {
        $resolver = new PageResolver();
        $two = static fn (Request $request, RouteMatch $match): string => $request->path() . ':' . $match->label();
        $three = static fn (Request $request, RouteMatch $match, PageRenderContext $context): string
            => $request->path() . ':' . $match->label() . ':' . $context->routeParameter('id');
        $variadic = static fn (Request $request, RouteMatch $match, PageRenderContext ...$contexts): int => count($contexts);

        self::assertSame('/items/42:route "items.show"', $resolver->resolve(...$this->match($two)));
        self::assertSame('/items/42:route "items.show":42', $resolver->resolve(...$this->match($three)));
        self::assertSame(1, $resolver->resolve(...$this->match($variadic)));
    }

    public function testItResolvesRouteHandlersAndControllerShapes(): void
    {
        $resolver = new PageResolver();
        $routeHandler = new RecordingRouteHandler();
        $controller = new PageController();

        $routeHandlerResponse = $resolver->resolve(...$this->match($routeHandler));
        $arrayObjectResult = $resolver->resolve(...$this->match([$controller, 'show']));
        $arrayClassResult = $resolver->resolve(...$this->match([PageController::class, 'show']));
        $classAtResult = $resolver->resolve(...$this->match(PageController::class . '@show'));
        $classStaticSyntaxResult = $resolver->resolve(...$this->match(PageController::class . '::show'));
        $staticArrayResult = $resolver->resolve(...$this->match([PageController::class, 'staticShow']));

        self::assertInstanceOf(Response::class, $routeHandlerResponse);
        self::assertSame('route-handler:route "items.show"', $routeHandlerResponse->body());
        self::assertSame('/items/42', $routeHandler->request?->path());
        self::assertSame('controller:/items/42:route "items.show":42', $arrayObjectResult);
        self::assertSame('controller:/items/42:route "items.show":42', $arrayClassResult);
        self::assertSame('controller:/items/42:route "items.show":42', $classAtResult);
        self::assertSame('controller:/items/42:route "items.show":42', $classStaticSyntaxResult);
        self::assertSame('static:/items/42:route "items.show"', $staticArrayResult);
    }

    public function testItThrowsForInvalidHandlersMissingClassesAndHandlerFailures(): void
    {
        $resolver = new PageResolver();

        $this->expectException(WebDispatchException::class);
        $resolver->resolve(...$this->match(new stdClass()));
    }

    public function testItThrowsForMissingControllerClasses(): void
    {
        $resolver = new PageResolver();

        $this->expectException(WebDispatchException::class);
        $resolver->resolve(...$this->match('Missing\\Controller@show'));
    }

    public function testItWrapsHandlerFailures(): void
    {
        $resolver = new PageResolver();

        $this->expectException(WebDispatchException::class);
        $this->expectExceptionMessage('controller failed');
        $resolver->resolve(...$this->match(PageController::class . '@fail'));
    }

    /**
     * @return array{0: RouteMatch, 1: PageRenderContext}
     */
    private function match(mixed $handler): array
    {
        $request = new Request('GET', '/items/42');
        $route = Route::get('/items/{id}', $handler, 'items.show');
        $match = new RouteMatch($route, ['id' => '42'], '/items/42', RouteMethod::GET, 'http');

        return [$match, new PageRenderContext($request, $match)];
    }
}
