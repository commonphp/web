<?php

declare(strict_types=1);

namespace CommonPHP\Web;

use CommonPHP\HTTP\Request;
use CommonPHP\Router\Route;
use CommonPHP\Router\RouteMatch;

class PageRenderContext
{
    /**
     * @param array<string, mixed> $attributes
     */
    public function __construct(
        private readonly Request $request,
        private readonly RouteMatch $routeMatch,
        private array $attributes = [],
    ) {
    }

    public function request(): Request
    {
        return $this->request;
    }

    public function routeMatch(): RouteMatch
    {
        return $this->routeMatch;
    }

    public function route(): Route
    {
        return $this->routeMatch->route();
    }

    public function routeName(): ?string
    {
        return $this->routeMatch->name();
    }

    public function routeLabel(): string
    {
        return $this->routeMatch->label();
    }

    /**
     * @return array<string, mixed>
     */
    public function routeParameters(): array
    {
        return $this->routeMatch->parameters()->all();
    }

    public function routeParameter(string $name, mixed $default = null): mixed
    {
        return $this->routeMatch->parameter($name, $default);
    }

    public function requiredRouteParameter(string $name): mixed
    {
        return $this->routeMatch->requiredParameter($name);
    }

    /**
     * @return array<string, mixed>
     */
    public function attributes(): array
    {
        return $this->attributes;
    }

    public function hasAttribute(string $name): bool
    {
        return array_key_exists($name, $this->attributes);
    }

    public function attribute(string $name, mixed $default = null): mixed
    {
        return $this->attributes[$name] ?? $default;
    }

    public function withAttribute(string $name, mixed $value): self
    {
        $clone = clone $this;
        $clone->attributes[$name] = $value;

        return $clone;
    }

    /**
     * @param array<string, mixed> $attributes
     */
    public function withAttributes(array $attributes): self
    {
        $clone = clone $this;
        $clone->attributes = array_replace($clone->attributes, $attributes);

        return $clone;
    }
}
