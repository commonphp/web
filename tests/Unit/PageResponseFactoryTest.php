<?php

declare(strict_types=1);

namespace CommonPHP\Web\Tests\Unit;

use CommonPHP\HTTP\Enums\ResponseStatus;
use CommonPHP\HTTP\HeaderBag;
use CommonPHP\HTTP\Request;
use CommonPHP\HTTP\Response;
use CommonPHP\Router\Enums\RouteMethod;
use CommonPHP\Router\Route;
use CommonPHP\Router\RouteMatch;
use CommonPHP\UI\Template;
use CommonPHP\UI\View;
use CommonPHP\Web\Exceptions\WebResponseException;
use CommonPHP\Web\PageRenderContext;
use CommonPHP\Web\PageResponse;
use CommonPHP\Web\PageResponseFactory;
use CommonPHP\Web\RedirectResponse;
use CommonPHP\Web\Tests\Fixtures\RecordingPageRenderer;
use CommonPHP\Web\Tests\Fixtures\SamplePage;
use PHPUnit\Framework\TestCase;
use stdClass;
use Stringable;

final class PageResponseFactoryTest extends TestCase
{
    public function testItNormalizesRouteResults(): void
    {
        $renderer = new RecordingPageRenderer();
        $factory = new PageResponseFactory($renderer);
        $context = $this->context();
        $response = new Response('raw', 201);
        $stringable = new class implements Stringable {
            public function __toString(): string
            {
                return '<b>stringable</b>';
            }
        };

        self::assertSame($renderer, $factory->renderer());
        self::assertSame($response, $factory->from($response, $context));
        self::assertInstanceOf(PageResponse::class, $factory->from(new SamplePage(), $context));
        self::assertInstanceOf(PageResponse::class, $factory->from(new View('pages.view'), $context));
        self::assertInstanceOf(PageResponse::class, $factory->from(new Template('pages.template'), $context));
        self::assertSame('<b>html</b>', $factory->from('<b>html</b>', $context)->body());
        self::assertSame('<b>stringable</b>', $factory->from($stringable, $context)->body());
        self::assertSame(204, $factory->from(null, $context)->statusCode());
    }

    public function testItRejectsUnsupportedRouteResults(): void
    {
        $this->expectException(WebResponseException::class);

        (new PageResponseFactory())->from(new stdClass(), $this->context());
    }

    public function testItCreatesHtmlTextNoContentRedirectAndErrorResponses(): void
    {
        $factory = new PageResponseFactory();

        $html = $factory->html('<h1>Hello</h1>');
        $text = $factory->text('Hello', ResponseStatus::ACCEPTED, ['X-Test' => 'yes']);
        $noContent = $factory->noContent(['X-Empty' => 'yes']);
        $redirect = $factory->redirect('/next');
        $notFound = $factory->notFound('Missing.');
        $method = $factory->methodNotAllowed(['post', 'GET', 'GET']);
        $error = $factory->error('Broken.', 503);

        self::assertSame('text/html; charset=utf-8', $html->header('Content-Type'));
        self::assertSame((string) strlen('<h1>Hello</h1>'), $html->header('Content-Length'));
        self::assertSame(202, $text->statusCode());
        self::assertSame('text/plain; charset=utf-8', $text->header('Content-Type'));
        self::assertSame('yes', $text->header('X-Test'));
        self::assertSame(204, $noContent->statusCode());
        self::assertSame('yes', $noContent->header('X-Empty'));
        self::assertInstanceOf(RedirectResponse::class, $redirect);
        self::assertSame('/next', $redirect->header('Location'));
        self::assertSame(404, $notFound->statusCode());
        self::assertStringContainsString('Missing.', $notFound->body());
        self::assertSame(405, $method->statusCode());
        self::assertSame('GET, POST', $method->header('Allow'));
        self::assertSame(503, $error->statusCode());
        self::assertStringContainsString('Broken.', $error->body());
    }

    public function testItHonorsExistingHeadersAndHeaderBags(): void
    {
        $factory = new PageResponseFactory();
        $headers = new HeaderBag(['Content-Type' => 'text/custom']);

        $html = $factory->html('<p>Custom</p>', headers: ['content-type' => 'text/custom-array']);
        $text = $factory->text('Custom', headers: $headers);

        self::assertSame('text/custom-array', $html->header('content-type'));
        self::assertSame('text/custom', $text->header('Content-Type'));
    }

    public function testItCanSuppressResponseBodies(): void
    {
        $factory = new PageResponseFactory();
        $raw = new Response('raw body', 200);
        $html = $factory->html('<p>Hello</p>', includeBody: false);
        $fromRaw = $factory->from($raw, $this->context(), false);

        self::assertSame('', $html->body());
        self::assertSame((string) strlen('<p>Hello</p>'), $html->header('Content-Length'));
        self::assertSame('', $fromRaw->body());
        self::assertSame((string) strlen('raw body'), $fromRaw->header('Content-Length'));
    }

    public function testNoBodyStatusesSuppressBodies(): void
    {
        $factory = new PageResponseFactory();

        $response = $factory->html('<p>Ignored</p>', ResponseStatus::NO_CONTENT);

        self::assertSame(204, $response->statusCode());
        self::assertSame('', $response->body());
        self::assertSame('0', $response->header('Content-Length'));
    }

    private function context(): PageRenderContext
    {
        $request = new Request('GET', '/items/42');
        $route = Route::get('/items/{id}', static fn (): string => 'item', 'items.show');
        $match = new RouteMatch($route, ['id' => '42'], '/items/42', RouteMethod::GET, 'http');

        return new PageRenderContext($request, $match);
    }
}
