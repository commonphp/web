# CommonPHP Web Documentation

CommonPHP Web is the page-oriented HTTP surface for CommonPHP applications. It combines `comphp/http`, `comphp/router`, and `comphp/ui` into a small layer for serving traditional HTML pages.

The package owns web pages, page routing, page rendering, redirects, and web-facing response normalization. Lower-level HTTP, routing, and UI packages remain independently usable.

## Start Here

- [Getting started](getting-started.md)
- [Usage](usage.md)
- [Package boundaries](package-boundaries.md)

## Web Concepts

- [Routing and surfaces](routing-and-surfaces.md)
- [Pages and rendering](pages-and-rendering.md)
- [Responses](responses.md)
- [Runtime integration](runtime-integration.md)
- [Error handling](error-handling.md)

## Examples

- [Examples index](examples/index.md)
- [Basic web surface](examples/basic-surface.md)
- [Page class](examples/page-class.md)
- [Redirects](examples/redirects.md)

## Development

- [Testing and QA](testing.md)

## Public API Map

Entry points:

- `CommonPHP\Web\WebSurface`
- `CommonPHP\Web\PageRegistry`
- `CommonPHP\Web\PageResolver`
- `CommonPHP\Web\PageResponseFactory`

Page rendering:

- `CommonPHP\Web\PageRenderContext`
- `CommonPHP\Web\PageResponse`
- `CommonPHP\Web\ViewPageRenderer`
- `CommonPHP\Web\RedirectResponse`

Contracts:

- `CommonPHP\Web\Contracts\PageInterface`
- `CommonPHP\Web\Contracts\AbstractPage`
- `CommonPHP\Web\Contracts\PageResolverInterface`
- `CommonPHP\Web\Contracts\PageRendererInterface`

Exceptions:

- `CommonPHP\Web\Exceptions\WebException`
- `CommonPHP\Web\Exceptions\WebDispatchException`
- `CommonPHP\Web\Exceptions\WebRouteException`
- `CommonPHP\Web\Exceptions\WebResponseException`
- `CommonPHP\Web\Exceptions\PageRenderException`
- `CommonPHP\Web\Exceptions\PageNotFoundException`
