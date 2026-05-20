# Responses

`PageResponseFactory` normalizes route results into HTTP responses.

## Page Responses

`PageResponse` extends `CommonPHP\HTTP\Response` and keeps references to the rendered page and context.

```php
$response = PageResponse::fromPage($page, $renderer, $context);

$response->page();
$response->context();
```

The response sets `Content-Type: text/html; charset=utf-8` and `Content-Length` unless the page supplies those headers.

## Factory Methods

```php
$responses = new PageResponseFactory();

$responses->html('<h1>Hello</h1>');
$responses->text('Hello');
$responses->redirect('/login');
$responses->noContent();
$responses->notFound('Page not found.');
$responses->methodNotAllowed(['GET', 'HEAD']);
$responses->error('Unable to serve this page.');
```

`from()` accepts a route result and chooses the correct response shape.

```php
$response = $responses->from($result, $context);
```

## Redirects

`RedirectResponse` validates the location and status code.

```php
use CommonPHP\Web\RedirectResponse;

return RedirectResponse::to('/login');
return RedirectResponse::permanent('/new-location');
return RedirectResponse::seeOther('/thanks');
```

Redirect statuses must be in the `3xx` range.

## HEAD And Bodyless Statuses

`WebSurface` omits response bodies for `HEAD` requests while preserving useful `Content-Length` values.

Responses with HTTP statuses that do not allow bodies, such as `204 No Content`, are emitted with an empty body.
