# Testing And QA

CommonPHP Web includes a package-local PHPUnit configuration and unit tests.

## Install Dependencies

From the package directory:

```bash
composer install
```

From the monorepo, the root `vendor` directory can also satisfy the test suite because `tests/bootstrap.php` checks both package and workspace autoloaders.

## Run PHPUnit

From the monorepo root:

```bash
vendor/bin/phpunit -c package/web/phpunit.xml.dist
```

On Windows:

```bat
vendor\bin\phpunit.bat -c package\web\phpunit.xml.dist
```

## Current Test Coverage

The unit suite covers:

- `AbstractPage` defaults and template/data/layout hooks;
- `PageRegistry` registration, lookup, iteration, removal, clearing, class resolution, and invalid entries;
- `PageRenderContext` request, route, parameter, label, and immutable attribute helpers;
- `PageResolver` registered pages, page classes, callables, route handlers, controller strings, controller arrays, static handlers, invalid handlers, missing classes, and wrapped failures;
- `PageResponse` rendering pages, views, templates, headers, content length, omitted bodies, bodyless statuses, and renderer failures;
- `PageResponseFactory` route-result normalization, HTML/text responses, redirects, `204`, `404`, `405`, error responses, header preservation, and body suppression;
- `RedirectResponse` redirect constructors, header handling, location updates, and validation failures;
- `ViewPageRenderer` integration with `ViewFactory`;
- `WebSurface` dependency accessors, prefix support, root mounts, route helpers, groups, manual routes, registered pages, handler result normalization, controller handlers, routing failures, dispatch failures, scheme failures, and `HEAD` requests;
- package exception factory methods.

## Manual Review Areas

Manual review should still cover application-specific templates, middleware order in `HttpApplication`, and production renderer driver configuration.
