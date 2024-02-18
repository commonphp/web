<?php

/** @noinspection PhpUnused */

declare(strict_types=1);

namespace CommonPHP\Web\Support;

/**
 * Class RequestScheme
 *
 * Represents the request scheme for HTTP communications.
 */
enum RequestScheme : string
{
    case HTTP = 'HTTP';
    case HTTPS = 'HTTPS';
}