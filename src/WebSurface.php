<?php

declare(strict_types=1);

namespace CommonPHP\Web;

use CommonPHP\HTTP\Contracts\HttpSurfaceInterface;
use CommonPHP\HTTP\Enums\RequestMethod;
use CommonPHP\HTTP\Enums\ResponseStatus;
use CommonPHP\HTTP\Request;
use CommonPHP\HTTP\Response;
use CommonPHP\Router\Enums\RouteMethod;
use CommonPHP\Router\Exceptions\MethodNotAllowedException;
use CommonPHP\Router\Exceptions\RouteNotFoundException;
use CommonPHP\Router\Exceptions\RouterException;
use CommonPHP\Router\Exceptions\SchemaNotAllowedException;
use CommonPHP\Router\Route;
use CommonPHP\Router\RouteGroup;
use CommonPHP\Router\Router;
use CommonPHP\Web\Contracts\PageInterface;
use CommonPHP\Web\Contracts\PageResolverInterface;
use CommonPHP\Web\Exceptions\PageNotFoundException;
use CommonPHP\Web\Exceptions\WebException;
use Throwable;

class WebSurface implements HttpSurfaceInterface
{
    private Router $router;

    private PageRegistry $pages;

    private PageResolverInterface $resolver;

    private PageResponseFactory $responses;

    private string $pathPrefix;

    public function __construct(
        ?Router $router = null,
        string $pathPrefix = '/',
        ?PageRegistry $pages = null,
        ?PageResolverInterface $resolver = null,
        ?PageResponseFactory $responses = null,
    ) {
        $this->router = $router ?? new Router();
        $this->pages = $pages ?? new PageRegistry();
        $this->resolver = $resolver ?? new PageResolver($this->pages);
        $this->responses = $responses ?? new PageResponseFactory();
        $this->pathPrefix = $this->normalizePathPrefix($pathPrefix);
    }

    public function supports(Request $request): bool
    {
        return $this->pathPrefix === '/'
            || $request->path() === $this->pathPrefix
            || str_starts_with($request->path(), $this->pathPrefix . '/');
    }

    public function handle(Request $request): Response
    {
        $includeBody = !$request->isMethod(RequestMethod::HEAD);

        try {
            if (!$this->supports($request)) {
                throw PageNotFoundException::forRequest($request);
            }

            $match = $this->router->match($request);
            $context = new PageRenderContext($request, $match);

            return $this->responses->from($this->resolver->resolve($match, $context), $context, $includeBody);
        } catch (RouteNotFoundException | PageNotFoundException) {
            return $this->responses->notFound(includeBody: $includeBody);
        } catch (MethodNotAllowedException $exception) {
            return $this->responses->methodNotAllowed($exception->allowedMethods(), includeBody: $includeBody);
        } catch (SchemaNotAllowedException) {
            return $this->responses->error('This page is not available over the requested scheme.', ResponseStatus::BAD_REQUEST, includeBody: $includeBody);
        } catch (RouterException | WebException) {
            return $this->responses->error(includeBody: $includeBody);
        } catch (Throwable) {
            return $this->responses->error(includeBody: $includeBody);
        }
    }

    public function router(): Router
    {
        return $this->router;
    }

    public function pages(): PageRegistry
    {
        return $this->pages;
    }

    public function resolver(): PageResolverInterface
    {
        return $this->resolver;
    }

    public function responses(): PageResponseFactory
    {
        return $this->responses;
    }

    public function pathPrefix(): string
    {
        return $this->pathPrefix;
    }

    /**
     * @param PageInterface|class-string<PageInterface> $page
     */
    public function registerPage(string $name, PageInterface|string $page): static
    {
        $this->pages->register($name, $page);

        return $this;
    }

    public function add(Route $route): static
    {
        $this->router->add($route);

        return $this;
    }

    /**
     * @param RouteMethod|RequestMethod|string|array<RouteMethod|RequestMethod|string> $methods
     */
    public function route(
        RouteMethod|RequestMethod|string|array $methods,
        string $path,
        mixed $handler,
        ?string $name = null,
    ): Route {
        return $this->router->route($methods, $this->routePath($path), $handler, $name);
    }

    public function page(string $path, mixed $page, ?string $name = null): Route
    {
        return $this->get($path, $page, $name);
    }

    public function any(string $path, mixed $handler, ?string $name = null): Route
    {
        return $this->route(RouteMethod::cases(), $path, $handler, $name);
    }

    public function get(string $path, mixed $handler, ?string $name = null): Route
    {
        return $this->route(RouteMethod::GET, $path, $handler, $name);
    }

    public function post(string $path, mixed $handler, ?string $name = null): Route
    {
        return $this->route(RouteMethod::POST, $path, $handler, $name);
    }

    public function put(string $path, mixed $handler, ?string $name = null): Route
    {
        return $this->route(RouteMethod::PUT, $path, $handler, $name);
    }

    public function patch(string $path, mixed $handler, ?string $name = null): Route
    {
        return $this->route(RouteMethod::PATCH, $path, $handler, $name);
    }

    public function delete(string $path, mixed $handler, ?string $name = null): Route
    {
        return $this->route(RouteMethod::DELETE, $path, $handler, $name);
    }

    public function options(string $path, mixed $handler, ?string $name = null): Route
    {
        return $this->route(RouteMethod::OPTIONS, $path, $handler, $name);
    }

    /**
     * @param callable(RouteGroup): void|null $routes
     * @param array<string, mixed> $constraints
     * @param array<string, mixed> $defaults
     * @param array<string, mixed> $metadata
     * @param list<string> $schemes
     * @param list<mixed> $middleware
     */
    public function group(
        string $prefix = '',
        ?callable $routes = null,
        ?string $namePrefix = null,
        array $constraints = [],
        array $defaults = [],
        array $metadata = [],
        array $schemes = [],
        array $middleware = [],
    ): RouteGroup {
        return $this->router->group(
            $this->routePath($prefix),
            $routes,
            $namePrefix,
            $constraints,
            $defaults,
            $metadata,
            $schemes,
            $middleware,
        );
    }

    public function named(string $name): Route
    {
        return $this->router->named($name);
    }

    private function routePath(string $path): string
    {
        $path = $this->normalizeRoutePath($path);

        if ($this->pathPrefix === '/') {
            return $path;
        }

        if ($path === $this->pathPrefix || str_starts_with($path, $this->pathPrefix . '/')) {
            return $path;
        }

        if ($path === '/') {
            return $this->pathPrefix;
        }

        return $this->pathPrefix . $path;
    }

    private function normalizePathPrefix(string $pathPrefix): string
    {
        $pathPrefix = $this->normalizeRoutePath($pathPrefix);

        return $pathPrefix === '/' ? '/' : rtrim($pathPrefix, '/');
    }

    private function normalizeRoutePath(string $path): string
    {
        $path = trim($path);

        if ($path === '' || $path === '/') {
            return '/';
        }

        return str_starts_with($path, '/') ? $path : '/' . $path;
    }
}
