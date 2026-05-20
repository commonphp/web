# Routing And Surfaces

`WebSurface` is an HTTP surface backed by `CommonPHP\Router\Router`.

## Path Prefix

The surface only supports requests under its configured prefix.

```php
$web = new WebSurface(pathPrefix: '/app');

$web->supports(new Request('GET', '/app'));       // true
$web->supports(new Request('GET', '/app/users')); // true
$web->supports(new Request('GET', '/api/users')); // false
```

Use `/` for a root-mounted web surface.

## Route Helpers

Route helpers mirror the router package and automatically apply the surface prefix.

```php
$web->get('/posts', PostsPage::class, 'posts.index');
$web->post('/posts', CreatePostController::class . '@store', 'posts.store');
$web->put('/posts/{id}', UpdatePostController::class . '@update', 'posts.update');
$web->delete('/posts/{id}', DeletePostController::class . '@delete', 'posts.delete');
$web->any('/health', static fn () => 'ok', 'health');
```

If a route is already prefixed, it is not prefixed again.

```php
$web = new WebSurface(pathPrefix: '/app');
$route = $web->get('/app/status', static fn () => 'ok');

$route->path(); // /app/status
```

## Route Groups

Groups are delegated to the router after prefix normalization.

```php
$web->group('/admin', static function ($group): void {
    $group->get('/dashboard', DashboardPage::class, 'dashboard');
}, 'admin.');
```

With a `/web` surface prefix, the dashboard route path is `/web/admin/dashboard` and its name is `admin.dashboard`.

## Manual Routes

Use `add()` for routes built elsewhere.

```php
use CommonPHP\Router\Route;

$web->add(Route::get('/web/manual', ManualPage::class, 'manual'));
```

Manual routes are not re-prefixed. Build them with the final route path.

## Named Routes

`named()` delegates to the underlying router collection.

```php
$route = $web->named('posts.index');
```
