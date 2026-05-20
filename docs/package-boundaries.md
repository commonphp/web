# Package Boundaries

CommonPHP Web owns page-oriented HTTP integration. It should stay small, readable, and easy to debug.

## Belongs Here

- HTTP surfaces for traditional HTML pages.
- Route helpers that delegate to `comphp/router`.
- Page objects and page registries.
- Rendering pages, views, and templates through `comphp/ui`.
- Web response normalization, redirects, and HTML error responses.
- Web-specific exceptions.

## Does Not Belong Here

- Low-level request and response primitives. Those belong in `comphp/http`.
- Route matching internals, route constraints, and route collections. Those belong in `comphp/router`.
- Template engines, component systems, and renderer drivers. Those belong in `comphp/ui` or UI drivers.
- JSON API envelopes or problem details. Those belong in `comphp/api`.
- Authentication, authorization, CSRF, sessions, database access, caching, logging, or filesystem storage.
- Runtime kernels, modules, container factories, or lifecycle events.

## Integration Shape

Application code should compose Web with the lower-level packages:

- register page routes on `WebSurface`;
- use page classes for renderable pages;
- use `ViewFactory` and renderer drivers through `ViewPageRenderer`;
- let `HttpApplication` own request creation, middleware, surface resolution, and response emission.
