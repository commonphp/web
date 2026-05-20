# Basic Web Surface

```php
<?php

declare(strict_types=1);

use CommonPHP\HTTP\HttpApplication;
use CommonPHP\Web\WebSurface;

$web = new WebSurface(pathPrefix: '/');

$web->get('/', static fn (): string => '<h1>Home</h1>', 'home');
$web->get('/about', static fn (): string => '<h1>About</h1>', 'about');

$app = (new HttpApplication())
    ->surface('web', $web, '/');
```

The route callbacks return HTML strings, which `PageResponseFactory` turns into `text/html` responses.
