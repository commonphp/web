<?php

declare(strict_types=1);

namespace CommonPHP\Web;

use CommonPHP\HTTP\Enums\ResponseStatus;
use CommonPHP\HTTP\HeaderBag;
use CommonPHP\HTTP\Response;
use CommonPHP\Web\Exceptions\WebResponseException;

class RedirectResponse extends Response
{
    private readonly string $location;

    /**
     * @param array<string, mixed>|HeaderBag $headers
     */
    public function __construct(
        string $location,
        ResponseStatus|int $status = ResponseStatus::FOUND,
        array|HeaderBag $headers = [],
    ) {
        $location = trim($location);

        if ($location === '') {
            throw WebResponseException::emptyRedirectLocation();
        }

        $statusCode = $this->statusCodeFrom($status);

        if ($statusCode < 300 || $statusCode >= 400) {
            throw WebResponseException::invalidRedirectStatus($statusCode);
        }

        $this->location = $location;

        parent::__construct('', $status, $this->withLocationHeader($headers, $location));
    }

    public static function to(string $location, ResponseStatus|int $status = ResponseStatus::FOUND): self
    {
        return new self($location, $status);
    }

    public static function permanent(string $location): self
    {
        return new self($location, ResponseStatus::MOVED_PERMANENTLY);
    }

    public static function seeOther(string $location): self
    {
        return new self($location, ResponseStatus::SEE_OTHER);
    }

    public function location(): string
    {
        return $this->location;
    }

    public function withLocation(string $location): static
    {
        return new static($location, $this->statusCode(), $this->headers());
    }

    /**
     * @param array<string, mixed>|HeaderBag $headers
     * @return array<string, mixed>|HeaderBag
     */
    private function withLocationHeader(array|HeaderBag $headers, string $location): array|HeaderBag
    {
        if ($headers instanceof HeaderBag) {
            return (clone $headers)->set('Location', $location);
        }

        $headers['Location'] = $location;

        return $headers;
    }

    private function statusCodeFrom(ResponseStatus|int $status): int
    {
        return $status instanceof ResponseStatus ? $status->value : $status;
    }
}
