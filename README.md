# CommonPHP Web

CommonPHP Web provides the traditional web application integration layer for CommonPHP applications. It brings together runtime, HTTP, routing, actions, views, assets, and related packages into a cohesive structure for serving web pages.

The package is intended for page-oriented applications that need the common web stack wired together without forcing lower-level packages to lose their independence.

## Requirements

- PHP `^8.5`
- `comphp/runtime:^0.3`
- `comphp/http:^0.3`

## Installation

Once this package is available through your Composer repositories, install it with:

```bash
composer require comphp/web
```

## Usage

```php
<?php

// TODO: Write usage
```

## Package Notes

This package should combine the pieces needed for traditional web application pages while keeping lower-level concerns in their own packages. HTTP, routing, assets, UI, and actions should remain independently usable.

## Error Handling

Web dispatch, rendering, route, and response failures should throw package-specific exceptions or produce appropriate HTTP responses through the web surface.

## Documentation

- [Documentation index](docs/index.md)
- [Usage](docs/usage.md)
- [Testing](TESTING.md)
- [Contributing](CONTRIBUTING.md)
- [Security](SECURITY.md)

## License

MIT. See [LICENSE.md](LICENSE.md).
