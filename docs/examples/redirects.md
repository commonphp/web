# Redirects

```php
<?php

declare(strict_types=1);

use CommonPHP\Web\RedirectResponse;
use CommonPHP\Web\WebSurface;

$web = new WebSurface(pathPrefix: '/account');

$web->get('/login', LoginPage::class, 'login');

$web->post('/logout', static function (): RedirectResponse {
    return RedirectResponse::seeOther('/account/login');
}, 'logout');
```

Use `seeOther()` after form submissions when the browser should make a `GET` request to the target URL.
