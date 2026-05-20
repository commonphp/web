<?php

declare(strict_types=1);

namespace CommonPHP\Web\Tests\Unit;

use CommonPHP\HTTP\Request;
use CommonPHP\Router\Enums\RouteMethod;
use CommonPHP\Router\Route;
use CommonPHP\Router\RouteMatch;
use CommonPHP\Web\PageRenderContext;
use PHPUnit\Framework\TestCase;

final class PageRenderContextTest extends TestCase
{
    public function testItExposesRequestRouteMatchRouteAndParameters(): void
    {
        $request = new Request('GET', '/posts/42');
        $route = Route::get('/posts/{id}', static fn (): string => 'post', 'posts.show');
        $match = new RouteMatch($route, ['id' => '42'], '/posts/42', RouteMethod::GET, 'https');
        $context = new PageRenderContext($request, $match, ['section' => 'docs']);

        self::assertSame($request, $context->request());
        self::assertSame($match, $context->routeMatch());
        self::assertSame($route, $context->route());
        self::assertSame('posts.show', $context->routeName());
        self::assertSame('route "posts.show"', $context->routeLabel());
        self::assertSame(['id' => '42'], $context->routeParameters());
        self::assertSame('42', $context->routeParameter('id'));
        self::assertSame('fallback', $context->routeParameter('missing', 'fallback'));
        self::assertSame('42', $context->requiredRouteParameter('id'));
        self::assertSame(['section' => 'docs'], $context->attributes());
        self::assertTrue($context->hasAttribute('section'));
        self::assertSame('docs', $context->attribute('section'));
    }

    public function testAttributeWithersReturnClones(): void
    {
        $context = $this->context(['one' => 1]);
        $withTwo = $context->withAttribute('two', 2);
        $withMany = $withTwo->withAttributes(['three' => 3, 'one' => 'changed']);

        self::assertSame(['one' => 1], $context->attributes());
        self::assertSame(['one' => 1, 'two' => 2], $withTwo->attributes());
        self::assertSame(['one' => 'changed', 'two' => 2, 'three' => 3], $withMany->attributes());
        self::assertSame('fallback', $context->attribute('missing', 'fallback'));
    }

    /**
     * @param array<string, mixed> $attributes
     */
    private function context(array $attributes = []): PageRenderContext
    {
        $route = Route::get('/context', static fn (): string => 'context', 'context');

        return new PageRenderContext(
            new Request('GET', '/context'),
            new RouteMatch($route, [], '/context', RouteMethod::GET, 'http'),
            $attributes,
        );
    }
}
