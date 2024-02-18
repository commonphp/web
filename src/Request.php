<?php

/** @noinspection PhpUnused */

declare(strict_types=1);

namespace CommonPHP\Web;

use CommonPHP\Web\Exceptions\UndefinedRequestMethodException;
use CommonPHP\Web\Exceptions\UndefinedRequestSchemeException;
use CommonPHP\Web\Support\RequestMethod;
use CommonPHP\Web\Support\RequestScheme;

if (!function_exists('getallheaders')) {
    /**
     * Retrieves all HTTP headers from the current request.
     *
     * This function iterates over the $_SERVER superglobal array and filters out the headers by checking if the key starts with 'HTTP_'. It then converts the header name to proper case
     * and replaces underscores with spaces. The resulting headers are added to an associative array with the corresponding values and returned.
     *
     * @return array<string, string> An associative array of HTTP headers, where the keys represent the header names and the values represent the header values.
     */
    function getallheaders() {
        $headers = [];
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
        return $headers;
    }
}

/**
 * Represents an HTTP request.
 *
 * This class encapsulates the information related to an HTTP request, such as the request method, scheme, host, port, path, URI, flags, parameters, values, cookies, and headers.
 *
 * @final
 */
final readonly class Request
{
    /**
     * The HTTP method used for the request.
     *
     * @var RequestMethod
     */
    public RequestMethod $method;

    /**
     * The scheme used in the URL.
     *
     * @var RequestScheme
     */
    public RequestScheme $scheme;

    /**
     * The hostname or IP address of the server.
     *
     * @var string
     */
    public string $host;

    /**
     * The port number used for the connection. Valid values are between 0 and 65535.
     *
     * @var int
     */
    public int $port;

    /**
     * This identifies the containing path of the URI
     *
     * @var string
     */
    public string $path;

    /**
     * This is the full URI of the request
     *
     * @var string
     */
    public string $uri;

    /**
     * These are GET parameters that did were not accompanied by an equals sign
     *
     * @var array<int<0, max>, int|string>
     */
    public array $flags;

    /**
     * These are GET parameters that were accompanied by an equals sign
     *
     * @var array<int|string, array|string>
     */
    public array $parameters;

    /**
     * These are any values found from in the non-GET body
     *
     * @var array<int|string, array|string>
     */
    public array $values;

    /**
     * The array that stores the cookie values.
     *
     * @var array<string, array|string>
     */
    public array $cookies;

    /**
     * Represents an associative array of HTTP headers.
     *
     * @var array<string, string>
     */
    public array $headers;

    /**
     * Constructs a new instance of the class.
     *
     * @param RequestMethod $method The request method.
     * @param RequestScheme $scheme The request scheme.
     * @param string $host The request host.
     * @param int $port The request port.
     * @param string $path The request path.
     * @param string $uri The request URI.
     * @param array<int<0, max>, int|string> $flags The request flags.
     * @param array<int|string, array|string> $parameters The request parameters.
     * @param array<string, array|string> $values The request values.
     * @param array<string> $cookies The request cookies.
     * @param array<string> $headers The request headers.
     */
    public function __construct(RequestMethod $method, RequestScheme $scheme, string $host, int $port, string $path, string $uri, array $flags, array $parameters, array $values, array $cookies, array $headers)
    {
        $this->method = $method;
        $this->scheme = $scheme;
        $this->host = $host;
        $this->port = $port;
        $this->path = $path;
        $this->uri = $uri;
        $this->flags = $flags;
        $this->parameters = $parameters;
        $this->values = $values;
        $this->cookies = $cookies;
        $this->headers = $headers;
    }

    /**
     * Creates a new Request object from the current HTTP request.
     *
     * @return self The newly created Request object.
     *
     * @throws UndefinedRequestMethodException If the request method is undefined.
     * @throws UndefinedRequestSchemeException If the request scheme is undefined.
     */
    public static function fromRequest(): self
    {
        $method =  RequestMethod::tryFrom(strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET'));
        if ($method === null)
        {
            throw new UndefinedRequestMethodException($_SERVER['REQUEST_METHOD']);
        }
        $scheme = RequestScheme::tryFrom(strtoupper($_SERVER['REQUEST_SCHEME'] ?? 'HTTP'));
        if ($scheme === null)
        {
            throw new UndefinedRequestSchemeException($_SERVER['REQUEST_SCHEME']);
        }
        $host = $_SERVER['SERVER_NAME'] ?? '127.0.0.1';
        $port = $_SERVER['SERVER_PORT'] ?? 0;
        $uri = ltrim($_SERVER['REQUEST_URI'] ?? '/', '/');
        $path = trim(dirname($_SERVER['SCRIPT_NAME'] ?? '/'), '/');
        $flags = [];
        $parameters = [];

        if (str_contains($uri, '?'))
        {
            $query = [];
            $queryPosition = strpos($uri, '?');
            if ($queryPosition === false) $queryPosition = 0;
            $queryString = substr($uri,  $queryPosition + 1);
            $uri = substr($uri, 0, $queryPosition);
            parse_str($queryString, $query);
            foreach ($query as $key => $value)
            {
                if ($value === '' && !str_contains($queryString, $key.'='))
                {
                    $flags[] = $key;
                }
                else
                {
                    $parameters[$key] = $value;
                }
            }
        }

        $values = $_POST;

        $uri = substr($uri, strlen($path));
        $uri = trim($uri, '/');

        return new Request($method, $scheme, $host, (int)$port, $path, $uri, $flags, $parameters, $values, $_COOKIE, getallheaders());
    }
}