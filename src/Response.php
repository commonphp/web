<?php

/** @noinspection PhpUnused */

declare(strict_types=1);

/**
 * Class Response
 *
 * Represents an HTTP response with its associated status code, message,
 * body, and headers.
 *
 * @package CommonPHP\Web
 */

namespace CommonPHP\Web;

use CommonPHP\Web\Exceptions\UndefinedResponseStatusCodeException;
use CommonPHP\Web\Support\ResponseStatus;

/**
 * Class Response
 *
 * The Response class represents an HTTP response.
 */
final readonly class Response
{
    /**
     * The $body variable is used to store the content of the message body.
     * It can be used to store the text, HTML, or any other format of the body.
     * Please note that this is just a placeholder variable and should be replaced
     * with the actual implementation as required.
     *
     * @var string
     */
    public string $body;

    /**
     * The status code of the response.
     *
     * @var int
     */
    public int $statusCode;

    /**
     * A message indicating the status of a certain process or action.
     *
     * @var string
     */
    public string $statusMessage;

    /**
     * The $headers variable is an associative array that stores the HTTP headers of a response.
     *
     * Each key-value pair in the $headers array represents a single header where the key is the header name and the value is the header value.
     *
     * Example usage:
     * $headers = [
     *     'Content-Type' => 'application/json',
     *     'Cache-Control' => 'no-cache',
     *     'X-Request-ID' => '12345'
     * ];
     *
     * @var array<string>
     */
    public array $headers;

    /**
     * __construct
     *
     * Initializes a new instance of the class.
     *
     * @param string $body The response body. Default: ''
     * @param ResponseStatus|int $status The response status code or an instance of ResponseStatus.
     *                                   Default: 202
     * @param array<string> $headers An array of response headers. Default: []
     * @throws UndefinedResponseStatusCodeException If the provided status code is not valid.
     */
    public function __construct(string $body = '', ResponseStatus|int $status = 202, array $headers = [])
    {
        $status = $this->getStatusCode($status);
        $this->body = $body;
        $this->statusCode = $status->value;
        $this->statusMessage = $status->getMessage();
        $this->headers = $headers;
    }

    /**
     * Sends the HTTP response to the client.
     *
     * This method sets the HTTP response code, sets the response headers,
     * flushes the output buffer, and terminates the script execution
     * with the response body. The method does not return the response body,
     * but instead terminates the script immediately.
     *
     * @return never
     */
    public function send(): never
    {
        http_response_code($this->statusCode);
        foreach ($this->headers as $key => $value)
        {
            header($key.': '.$value);
        }
        while (ob_get_level() > 0) ob_end_flush();
        die($this->body);
    }

    /**
     * Returns the HTTP response status code.
     *
     * This method accepts a value representing the response status code,
     * it can be either an integer or a ResponseStatus enum. If an integer is provided,
     * it will try to convert it into a ResponseStatus enum. If the conversion fails,
     * it throws an UndefinedResponseStatusCodeException.
     *
     * @param ResponseStatus|int $status The response status code or enum.
     * @return ResponseStatus The response status code.
     * @throws UndefinedResponseStatusCodeException When the provided status code is invalid.
     */
    private function getStatusCode(ResponseStatus|int $status): ResponseStatus
    {
        if (is_int($status))
        {
            $realStatus = ResponseStatus::tryFrom($status);
            if ($realStatus === null)
            {
                throw new UndefinedResponseStatusCodeException($status);
            }
            $status = $realStatus;
        }
        return $status;
    }
}