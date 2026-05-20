# Page Class

```php
<?php

declare(strict_types=1);

use CommonPHP\Web\Contracts\AbstractPage;
use CommonPHP\Web\PageRenderContext;
use CommonPHP\Web\WebSurface;

final class PostPage extends AbstractPage
{
    protected function template(PageRenderContext $context): string
    {
        return 'pages.post';
    }

    protected function data(PageRenderContext $context): array
    {
        return [
            'slug' => $context->routeParameter('slug'),
        ];
    }

    protected function layout(PageRenderContext $context): string
    {
        return 'layouts.main';
    }
}

$web = new WebSurface(pathPrefix: '/blog');
$web->get('/posts/{slug}', PostPage::class, 'posts.show');
```

The final route path is `/blog/posts/{slug}`. The page receives the route parameter through `PageRenderContext`.
