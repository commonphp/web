# Runtime Integration

CommonPHP Web does not define its own application kernel. It plugs into the HTTP package through `HttpSurfaceInterface`.

## Register With HttpApplication

```php
use CommonPHP\HTTP\HttpApplication;
use CommonPHP\Web\WebSurface;

$web = new WebSurface(pathPrefix: '/');
$web->get('/', HomePage::class, 'home');

$app = (new HttpApplication())
    ->surface('web', $web, '/', priority: 0);
```

The HTTP application handles request creation, middleware, surface resolution, response emission, and runtime execution.

## Multiple Surfaces

Register the web surface alongside API, docs, or asset surfaces. Use prefixes and priorities to make resolution predictable.

```php
$app
    ->surface('api', $apiSurface, '/api', priority: 20)
    ->surface('docs', $docsSurface, '/docs', priority: 10)
    ->surface('web', $webSurface, '/', priority: 0);
```

More specific prefixes and higher priorities should be used for specialized surfaces.

## Middleware

HTTP middleware belongs to `comphp/http`. Add middleware to the `HttpApplication`, not to individual web pages.

```php
$app->middleware($securityHeaders);
```
