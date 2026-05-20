# Error Handling

The web package separates package exceptions from user-facing surface responses.

## Surface Responses

`WebSurface::handle()` catches routing, dispatch, rendering, and response normalization failures and returns HTML error responses.

Common mappings:

- unmatched route or unsupported prefix: `404 Not Found`;
- unsupported method: `405 Method Not Allowed` with `Allow`;
- unsupported scheme: `400 Bad Request`;
- dispatch, renderer, or unsupported result failures: `500 Internal Server Error`.

## Exceptions

Package-specific exceptions are still available when working with lower-level classes directly.

- `PageNotFoundException` for missing registered pages.
- `PageRenderException` for renderer failures.
- `WebDispatchException` for invalid handlers or handler failures.
- `WebResponseException` for unsupported route results or invalid redirects.
- `WebRouteException` for route-related wrapping in integrations.

## Debugging

Use direct collaborators in tests when you need exception-level visibility.

```php
$resolver = new PageResolver($pages);
$result = $resolver->resolve($match, $context);
```

Use `WebSurface` in integration tests when you want the final HTTP response behavior.
