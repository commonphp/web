<?php

declare(strict_types=1);

namespace CommonPHP\Web;

use Closure;
use CommonPHP\HTTP\Request;
use CommonPHP\Router\Contracts\RouteHandlerInterface;
use CommonPHP\Router\RouteMatch;
use CommonPHP\Web\Contracts\PageInterface;
use CommonPHP\Web\Contracts\PageResolverInterface;
use CommonPHP\Web\Exceptions\WebDispatchException;
use ReflectionFunction;
use ReflectionMethod;
use Throwable;

class PageResolver implements PageResolverInterface
{
    private PageRegistry $pages;

    public function __construct(?PageRegistry $pages = null)
    {
        $this->pages = $pages ?? new PageRegistry();
    }

    public function pages(): PageRegistry
    {
        return $this->pages;
    }

    public function resolve(RouteMatch $match, PageRenderContext $context): mixed
    {
        $handler = $this->resolveHandler($match->handler());

        if ($handler instanceof PageInterface) {
            return $handler;
        }

        try {
            if ($handler instanceof RouteHandlerInterface) {
                return $handler->handle($context->request(), $match);
            }

            if (is_callable($handler)) {
                return $this->callHandler($handler, $context->request(), $match, $context);
            }
        } catch (WebDispatchException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            throw WebDispatchException::failed($match, $exception);
        }

        throw WebDispatchException::invalidHandler($match, get_debug_type($handler));
    }

    private function resolveHandler(mixed $handler): mixed
    {
        if ($handler instanceof PageInterface || $handler instanceof RouteHandlerInterface || is_callable($handler)) {
            return $handler;
        }

        if (is_array($handler) && count($handler) === 2) {
            [$target, $method] = array_values($handler);

            if (is_string($target) && is_string($method)) {
                return [$this->resolveClass($target), $method];
            }

            return $handler;
        }

        if (!is_string($handler)) {
            return $handler;
        }

        if ($this->pages->has($handler)) {
            return $this->pages->resolve($handler);
        }

        if (str_contains($handler, '@')) {
            [$class, $method] = explode('@', $handler, 2);

            return [$this->resolveClass($class), $method];
        }

        if (str_contains($handler, '::')) {
            [$class, $method] = explode('::', $handler, 2);

            return [$this->resolveClass($class), $method];
        }

        if (class_exists($handler)) {
            return $this->resolveClass($handler);
        }

        return $handler;
    }

    private function resolveClass(string $class): object
    {
        if (!class_exists($class)) {
            throw new WebDispatchException('Web page handler class "' . $class . '" was not found.');
        }

        return new $class();
    }

    private function callHandler(callable $handler, Request $request, RouteMatch $match, PageRenderContext $context): mixed
    {
        return $this->acceptsContext($handler)
            ? $handler($request, $match, $context)
            : $handler($request, $match);
    }

    private function acceptsContext(callable $handler): bool
    {
        try {
            $reflection = is_array($handler)
                ? new ReflectionMethod($handler[0], $handler[1])
                : new ReflectionFunction(Closure::fromCallable($handler));
        } catch (Throwable) {
            return false;
        }

        return $reflection->isVariadic() || $reflection->getNumberOfParameters() >= 3;
    }
}
