# Pages And Rendering

Pages describe how a route becomes a rendered HTML response.

## Page Interface

`PageInterface` requires three methods:

- `view(PageRenderContext $context): View`
- `status(PageRenderContext $context): ResponseStatus|int`
- `headers(PageRenderContext $context): array|HeaderBag`

Most pages should extend `AbstractPage` and implement only `template()`.

## Abstract Page

```php
use CommonPHP\Web\Contracts\AbstractPage;
use CommonPHP\Web\PageRenderContext;

final class AccountPage extends AbstractPage
{
    protected function template(PageRenderContext $context): string
    {
        return 'pages.account';
    }

    protected function data(PageRenderContext $context): array
    {
        return [
            'accountId' => $context->routeParameter('id'),
        ];
    }

    protected function layout(PageRenderContext $context): ?string
    {
        return 'layouts.main';
    }
}
```

By default, `AbstractPage` returns status `200 OK`, no custom headers, no layout, and empty data.

## Render Context

`PageRenderContext` carries the request, route match, route parameters, and render attributes.

```php
$context->request();
$context->routeMatch();
$context->route();
$context->routeName();
$context->routeLabel();
$context->routeParameters();
$context->routeParameter('id');
$context->requiredRouteParameter('id');
```

Context attributes are immutable-style helpers. Calling `withAttribute()` or `withAttributes()` returns a clone.

```php
$next = $context->withAttribute('section', 'admin');
```

## Renderer Bridge

`ViewPageRenderer` adapts page, view, and template objects to `CommonPHP\UI\ViewFactory`.

```php
use CommonPHP\UI\ViewFactory;
use CommonPHP\Web\ViewPageRenderer;

$renderer = new ViewPageRenderer(new ViewFactory(templatePaths: [
    __DIR__ . '/templates',
]));

$responses = new PageResponseFactory($renderer);
$web = new WebSurface(responses: $responses);
```

Swap `PageRendererInterface` in tests or integrations when you need different rendering behavior.
