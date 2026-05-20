# Usage

The package has three common usage styles: route callbacks, page classes, and registered named pages.

## Route Callbacks

Callbacks receive the HTTP request and route match. If the callable declares a third parameter, it receives `PageRenderContext`.

```php
use CommonPHP\HTTP\Request;
use CommonPHP\Router\RouteMatch;
use CommonPHP\Web\PageRenderContext;
use CommonPHP\Web\WebSurface;

$web = new WebSurface(pathPrefix: '/web');

$web->get('/posts/{slug}', static function (
    Request $request,
    RouteMatch $match,
    PageRenderContext $context,
): string {
    return '<h1>' . htmlspecialchars((string) $context->routeParameter('slug')) . '</h1>';
}, 'posts.show');
```

## Page Classes

Use page classes when the page has template data, custom status, or custom headers.

```php
use CommonPHP\HTTP\Enums\ResponseStatus;
use CommonPHP\Web\Contracts\AbstractPage;
use CommonPHP\Web\PageRenderContext;

final class CreatedPage extends AbstractPage
{
    protected function template(PageRenderContext $context): string
    {
        return 'pages.created';
    }

    public function status(PageRenderContext $context): ResponseStatus|int
    {
        return ResponseStatus::CREATED;
    }

    public function headers(PageRenderContext $context): array
    {
        return ['X-Page' => 'created'];
    }
}

$web->get('/created', CreatedPage::class, 'created');
```

## Registered Named Pages

`PageRegistry` lets routes reference simple names instead of classes.

```php
use CommonPHP\Web\PageRegistry;
use CommonPHP\Web\WebSurface;

$pages = new PageRegistry([
    'home' => HomePage::class,
]);

$web = new WebSurface(pathPrefix: '/', pages: $pages);
$web->get('/', 'home', 'home');
```

You can also add pages after construction:

```php
$web->registerPage('dashboard', new DashboardPage());
$web->get('/dashboard', 'dashboard', 'dashboard');
```

## Return Values

Route handlers may return:

- `CommonPHP\HTTP\Response`, passed through;
- `CommonPHP\Web\Contracts\PageInterface`, rendered as a page;
- `CommonPHP\UI\View`, rendered as a view;
- `CommonPHP\UI\Contracts\TemplateInterface`, rendered as a view using that template;
- `string` or `Stringable`, returned as HTML;
- `null`, returned as `204 No Content`.

Unsupported results are converted to an internal web error response by `WebSurface`.
