<?php

/** @noinspection PhpUnused */

declare(strict_types=1);

namespace CommonPHP\Web\Support;

/**
 * The RequestMethod class represents the HTTP request methods.
 */
enum RequestMethod : string
{
    case GET = 'GET';
    case HEAD = 'HEAD';
    case POST = 'POST';
    case PUT = 'PUT';
    case DELETE = 'DELETE';
    case CONNECT = 'CONNECT';
    case OPTIONS = 'OPTIONS';
    case TRACE = 'TRACE';
    case PATCH = 'PATCH';
}