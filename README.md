# CommonPHP Web Library

The CommonPHP Web Library provides a suite of components designed to streamline the development of web applications by offering a standardized, easy-to-use set of utilities for handling HTTP requests and responses, along with robust exception handling. Inspired by the flexibility of PHP and the need for more straightforward, decoupled components in modern web development, this library aims to offer PHP developers a reliable toolkit for enhancing their web projects.

## Features

- **HTTP Request Handling**: Simplifies the process of managing HTTP requests, including methods, schemes, headers, and cookies.
- **HTTP Response Management**: Offers a structured way to create and manage HTTP responses, including status codes, headers, and body content.
- **Exception Handling**: Provides specialized exceptions to handle common web development issues, improving the debuggability and reliability of your application.
- **Support Classes**: Includes enums for request methods, schemes, and response statuses, making your code more readable and maintainable.

## Installation

You can install the CommonPHP Web Library using Composer:

```bash
composer require comphp/web
```

Replace `your/package-name` with the actual package name of the CommonPHP Web Library on Packagist.

## Usage

### Handling Requests

To handle an HTTP request, you can easily instantiate a `Request` object:

```php
use CommonPHP\Web\Request;

$request = Request::fromRequest();
```

This will automatically populate the `Request` object with details from the current HTTP request, including method, scheme, headers, and any parameters.

### Creating Responses

To create an HTTP response, you can use the `Response` class:

```php
use CommonPHP\Web\Response;
use CommonPHP\Web\Support\ResponseStatus;

$response = new Response(
    body: 'Hello, world!',
    status: ResponseStatus::SUCCESS_OK,
    headers: ['Content-Type' => 'text/plain']
);

$response->send();
```

This will send a response with the specified body, status code, and headers.

### Exception Handling

The library includes several exceptions designed to handle common errors in web development:

- `UndefinedRequestMethodException`
- `UndefinedRequestSchemeException`
- `UndefinedResponseStatusCodeException`

These can be used to catch and respond to errors more effectively in your application.

## Contributing

We welcome contributions from the community, whether it's through submitting bug reports, proposing enhancements, or creating pull requests. Please refer to our CONTRIBUTING.md file for more details on how to contribute to the CommonPHP Web Library.

## License

This library is licensed under the [MIT License](LICENSE.md).