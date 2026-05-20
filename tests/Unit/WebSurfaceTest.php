<?php

declare(strict_types=1);

namespace CommonPHP\Web\Tests\Unit;

use CommonPHP\HTTP\Enums\RequestMethod;
use CommonPHP\HTTP\Enums\ResponseStatus;
use CommonPHP\HTTP\Request;
use CommonPHP\HTTP\Response;
use CommonPHP\Router\Enums\RouteMethod;
use CommonPHP\Router\Route;
use CommonPHP\Router\RouteGroup;
use CommonPHP\Router\Router;
use CommonPHP\UI\Template;
use CommonPHP\UI\View;
use CommonPHP\Web\PageRegistry;
use CommonPHP\Web\PageResponse;
use CommonPHP\Web\PageResponseFactory;
use CommonPHP\Web\Tests\Fixtures\PageController;
use CommonPHP\Web\Tests\Fixtures\RecordingPageRenderer;
use CommonPHP\Web\Tests\Fixtures\SamplePage;
use CommonPHP\Web\WebSurface;
use PHPUnit\Framework\TestCase;
use stdClass;

final class WebSurfaceTest extends TestCase
{
    public function testItReportsDependenciesAndSupportsMountedPrefixes(): void
    {
        $router = new Router();
        $pages = new PageRegistry();
        $responses = new PageResponseFactory(new RecordingPageRenderer());
        $surface = new WebSurface($router, '/web/', $pages, responses: $responses);

        self::assertSame($router, $surface->router());
        self::assertSame($pages, $surface->pages());
        self::assertSame($responses, $surface->responses());
        self::assertSame('/web', $surface->pathPrefix());
        self::assertTrue($surface->supports(new Request('GET', '/web')));
        self::assertTrue($surface->supports(new Request('GET', '/web/pages')));
        self::assertFalse($surface->supports(new Request('GET', '/webbing')));
    }

    public function testRootMountedSurfacesSupportEveryRequestPath(): void
    {
        $surface = new WebSurface(pathPrefix: '/');
        $surface->get('/hello', static fn (): string => 'hello');

        self::assertSame('/', $surface->pathPrefix());
        self::assertTrue($surface->supports(new Request('GET', '/anything')));
        self::assertSame('hello', $surface->handle(new Request('GET', '/hello'))->body());
    }

    public function testItPrefixesRouteHelpersAndCanLookupNamedRoutes(): void
    {
        $surface = new WebSurface(pathPrefix: '/web');

        self::assertSame('/web/any', $surface->any('/any', static fn (): string => 'any', 'any')->path());
        self::assertSame('/web/get', $surface->get('/get', static fn (): string => 'get', 'get')->path());
        self::assertSame('/web/post', $surface->post('/post', static fn (): string => 'post', 'post')->path());
        self::assertSame('/web/put', $surface->put('/put', static fn (): string => 'put', 'put')->path());
        self::assertSame('/web/patch', $surface->patch('/patch', static fn (): string => 'patch', 'patch')->path());
        self::assertSame('/web/delete', $surface->delete('/delete', static fn (): string => 'delete', 'delete')->path());
        self::assertSame('/web/options', $surface->options('/options', static fn (): string => 'options', 'options')->path());
        self::assertSame('/web/custom', $surface->route(RouteMethod::GET, '/custom', static fn (): string => 'custom', 'custom')->path());
        self::assertSame('/web/already', $surface->get('/web/already', static fn (): string => 'already', 'already')->path());
        self::assertSame('/web/page', $surface->page('/page', static fn (): string => 'page', 'page')->path());
        self::assertSame('page', $surface->named('page')->name());
    }

    public function testItSupportsGroupsAndManuallyAddedRoutes(): void
    {
        $surface = new WebSurface(pathPrefix: '/web');
        $manual = Route::get('/web/manual', static fn (): string => 'manual', 'manual');

        self::assertSame($surface, $surface->add($manual));

        $group = $surface->group('/admin', static function (RouteGroup $group): void {
            $group->get('/dashboard', static fn (): string => 'dashboard', 'dashboard');
        }, 'admin.');

        self::assertSame('/web/admin', $group->prefix());
        self::assertSame('/web/manual', $surface->named('manual')->path());
        self::assertSame('/web/admin/dashboard', $surface->named('admin.dashboard')->path());
        self::assertSame('manual', $surface->handle(new Request('GET', '/web/manual'))->body());
        self::assertSame('dashboard', $surface->handle(new Request('GET', '/web/admin/dashboard'))->body());
    }

    public function testItRegistersAndServesNamedPages(): void
    {
        $renderer = new RecordingPageRenderer();
        $surface = new WebSurface(
            pathPrefix: '/web',
            responses: new PageResponseFactory($renderer),
        );

        self::assertSame($surface, $surface->registerPage('sample', new SamplePage()));
        $surface->get('/items/{id}', 'sample', 'items.show');

        $response = $surface->handle(new Request('GET', '/web/items/42'));

        self::assertInstanceOf(PageResponse::class, $response);
        self::assertSame(202, $response->statusCode());
        self::assertSame('page:pages.sample:route "items.show"', $response->body());
        self::assertSame('sample', $response->header('X-Page'));
    }

    public function testItNormalizesCommonHandlerReturnTypes(): void
    {
        $renderer = new RecordingPageRenderer();
        $surface = new WebSurface(
            pathPrefix: '/web',
            responses: new PageResponseFactory($renderer),
        );

        $surface->get('/string', static fn (): string => '<b>string</b>');
        $surface->get('/response', static fn (): Response => new Response('response', 201));
        $surface->get('/null', static fn (): null => null);
        $surface->get('/page', static fn (): SamplePage => new SamplePage());
        $surface->get('/view', static fn (): View => new View('pages.view'));
        $surface->get('/template', static fn (): Template => new Template('pages.template'));

        self::assertSame('<b>string</b>', $surface->handle(new Request('GET', '/web/string'))->body());
        self::assertSame(201, $surface->handle(new Request('GET', '/web/response'))->statusCode());
        self::assertSame(204, $surface->handle(new Request('GET', '/web/null'))->statusCode());
        self::assertSame('page:pages.sample:GET /web/page', $surface->handle(new Request('GET', '/web/page'))->body());
        self::assertSame('view:pages.view', $surface->handle(new Request('GET', '/web/view'))->body());
        self::assertSame('template:pages.template', $surface->handle(new Request('GET', '/web/template'))->body());
    }

    public function testItServesControllerHandlerResults(): void
    {
        $surface = new WebSurface(pathPrefix: '/web');
        $surface->get('/controller/{id}', PageController::class . '@show', 'controller.show');
        $surface->get('/response', PageController::class . '@response', 'controller.response');

        self::assertSame(
            'controller:/web/controller/42:route "controller.show":42',
            $surface->handle(new Request('GET', '/web/controller/42'))->body(),
        );
        self::assertSame(202, $surface->handle(new Request('GET', '/web/response'))->statusCode());
    }

    public function testItReturnsWebErrorResponsesForRoutingAndDispatchFailures(): void
    {
        $surface = new WebSurface(pathPrefix: '/web');
        $surface->get('/hello', static fn (): string => 'hello');
        $surface->get('/https', static fn (): string => 'secure')->httpsOnly();
        $surface->get('/bad-result', static fn (): stdClass => new stdClass());
        $surface->get('/handler-fails', PageController::class . '@fail');

        $outsidePrefix = $surface->handle(new Request('GET', '/api/hello'));
        $missing = $surface->handle(new Request('GET', '/web/missing'));
        $methodNotAllowed = $surface->handle(new Request('POST', '/web/hello'));
        $schemeNotAllowed = $surface->handle(new Request('GET', '/web/https', scheme: 'http'));
        $badResult = $surface->handle(new Request('GET', '/web/bad-result'));
        $handlerFails = $surface->handle(new Request('GET', '/web/handler-fails'));

        self::assertSame(404, $outsidePrefix->statusCode());
        self::assertSame(404, $missing->statusCode());
        self::assertSame(405, $methodNotAllowed->statusCode());
        self::assertSame('GET, HEAD', $methodNotAllowed->header('Allow'));
        self::assertSame(400, $schemeNotAllowed->statusCode());
        self::assertSame(500, $badResult->statusCode());
        self::assertSame(500, $handlerFails->statusCode());
    }

    public function testHeadRequestsOmitTheResponseBody(): void
    {
        $surface = new WebSurface(pathPrefix: '/web');
        $surface->get('/hello', static fn (): string => '<p>Hello</p>');

        $response = $surface->handle(new Request(RequestMethod::HEAD, '/web/hello'));

        self::assertSame(200, $response->statusCode());
        self::assertSame('', $response->body());
        self::assertSame((string) strlen('<p>Hello</p>'), $response->header('Content-Length'));
    }
}
