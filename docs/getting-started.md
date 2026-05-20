# Getting Started

CommonPHP Web serves HTML pages through an HTTP surface. A `WebSurface` owns routes, resolves handlers into pages or responses, and normalizes the result into an HTTP response.

## Install

```bash
composer require comphp/web
```

In this monorepo, the package is available through the workspace path repository and the root Composer autoloader.

## Create A Surface

```php
<?php

declare(strict_types=1);

use CommonPHP\HTTP\Request;
use CommonPHP\Web\WebSurface;

$web = new WebSurface(pathPrefix: '/');

$web->get('/', static fn (): string => '<h1>Hello</h1>', 'home');
$web->get('/about', static fn (): string => '<h1>About</h1>', 'about');

$response = $web->handle(new Request('GET', '/'));
```

String route results are treated as HTML. `Response` objects pass through unchanged, `null` becomes `204 No Content`, and page or view objects are rendered.

## Add A Page Class

```php
use CommonPHP\UI\Contracts\TemplateInterface;
use CommonPHP\Web\Contracts\AbstractPage;
use CommonPHP\Web\PageRenderContext;

final class ProfilePage extends AbstractPage
{
    protected function template(PageRenderContext $context): TemplateInterface|string
    {
        return 'pages.profile';
    }

    protected function data(PageRenderContext $context): array
    {
        return ['id' => $context->routeParameter('id')];
    }
}

$web->get('/profiles/{id}', ProfilePage::class, 'profiles.show');
```

Page classes keep routing, rendering, headers, and status behavior easy to inspect and test.

## Mount In HTTP Runtime

`WebSurface` implements `CommonPHP\HTTP\Contracts\HttpSurfaceInterface`, so it can be registered with `CommonPHP\HTTP\HttpApplication` alongside other HTTP surfaces.

```php
use CommonPHP\HTTP\HttpApplication;

$app = (new HttpApplication())
    ->surface('web', $web, '/', priority: 0);
```
