<?php

declare(strict_types=1);

namespace CommonPHP\Web\Tests\Unit;

use CommonPHP\HTTP\Request;
use CommonPHP\Router\Enums\RouteMethod;
use CommonPHP\Router\Route;
use CommonPHP\Router\RouteMatch;
use CommonPHP\Web\Exceptions\PageNotFoundException;
use CommonPHP\Web\Exceptions\PageRenderException;
use CommonPHP\Web\Exceptions\WebDispatchException;
use CommonPHP\Web\Exceptions\WebException;
use CommonPHP\Web\Exceptions\WebResponseException;
use CommonPHP\Web\Exceptions\WebRouteException;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class ExceptionsTest extends TestCase
{
    public function testWebExceptionFactoryCreatesConcreteInstances(): void
    {
        $exception = WebException::because('Because.');

        self::assertSame('Because.', $exception->getMessage());
    }

    public function testPageNotFoundFactoriesDescribeMissingTargets(): void
    {
        self::assertStringContainsString('home', PageNotFoundException::forName('home')->getMessage());
        self::assertStringContainsString('/missing', PageNotFoundException::forPath('/missing')->getMessage());
        self::assertStringContainsString('/request', PageNotFoundException::forRequest(new Request('GET', '/request'))->getMessage());
    }

    public function testDispatchExceptionFactoriesDescribeHandlersAndFailures(): void
    {
        $match = $this->match();
        $previous = new RuntimeException('boom');

        self::assertStringContainsString('stdClass', WebDispatchException::invalidHandler($match, 'stdClass')->getMessage());
        self::assertStringContainsString('home', WebDispatchException::invalidPage('home', 'stdClass')->getMessage());
        self::assertSame($previous, WebDispatchException::failed($match, $previous)->getPrevious());
    }

    public function testRenderRouteAndResponseExceptionFactoriesDescribeFailures(): void
    {
        $previous = new RuntimeException('boom');

        self::assertSame($previous, PageRenderException::forTarget('home', $previous)->getPrevious());
        self::assertSame($previous, WebRouteException::fromRouter($previous)->getPrevious());
        self::assertStringContainsString('stdClass', WebResponseException::invalidResult('stdClass')->getMessage());
        self::assertStringContainsString('empty', WebResponseException::emptyRedirectLocation()->getMessage());
        self::assertStringContainsString('200', WebResponseException::invalidRedirectStatus(200)->getMessage());
    }

    private function match(): RouteMatch
    {
        $route = Route::get('/home', static fn (): string => 'home', 'home');

        return new RouteMatch($route, [], '/home', RouteMethod::GET, 'http');
    }
}
