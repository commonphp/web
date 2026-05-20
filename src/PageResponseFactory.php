<?php

declare(strict_types=1);

namespace CommonPHP\Web;

use CommonPHP\HTTP\Enums\ResponseStatus;
use CommonPHP\HTTP\HeaderBag;
use CommonPHP\HTTP\Response;
use CommonPHP\UI\Contracts\TemplateInterface;
use CommonPHP\UI\View;
use CommonPHP\Web\Contracts\PageInterface;
use CommonPHP\Web\Contracts\PageRendererInterface;
use CommonPHP\Web\Exceptions\WebResponseException;
use Stringable;

class PageResponseFactory
{
    private PageRendererInterface $renderer;

    public function __construct(?PageRendererInterface $renderer = null)
    {
        $this->renderer = $renderer ?? new ViewPageRenderer();
    }

    public function renderer(): PageRendererInterface
    {
        return $this->renderer;
    }

    public function from(mixed $result, PageRenderContext $context, bool $includeBody = true): Response
    {
        if ($result instanceof Response) {
            return $this->withBodyPolicy($result, $includeBody);
        }

        if ($result instanceof PageInterface || $result instanceof View || $result instanceof TemplateInterface) {
            return $this->page($result, $context, $includeBody);
        }

        if (is_string($result) || $result instanceof Stringable) {
            return $this->html((string) $result, includeBody: $includeBody);
        }

        if ($result === null) {
            return $this->noContent();
        }

        throw WebResponseException::invalidResult(get_debug_type($result));
    }

    public function page(
        PageInterface|View|TemplateInterface $page,
        PageRenderContext $context,
        bool $includeBody = true,
    ): PageResponse {
        return PageResponse::fromPage($page, $this->renderer, $context, $includeBody);
    }

    /**
     * @param array<string, mixed>|HeaderBag $headers
     */
    public function html(
        string $body,
        ResponseStatus|int $status = ResponseStatus::OK,
        array|HeaderBag $headers = [],
        bool $includeBody = true,
    ): Response {
        $body = ResponseStatus::codeAllowsBody($this->statusCodeFrom($status)) ? $body : '';

        return $this->withBodyPolicy(new Response(
            $body,
            $status,
            $this->headersForBody($headers, $body, 'text/html; charset=utf-8'),
        ), $includeBody);
    }

    /**
     * @param array<string, mixed>|HeaderBag $headers
     */
    public function text(
        string $body,
        ResponseStatus|int $status = ResponseStatus::OK,
        array|HeaderBag $headers = [],
        bool $includeBody = true,
    ): Response {
        $body = ResponseStatus::codeAllowsBody($this->statusCodeFrom($status)) ? $body : '';

        return $this->withBodyPolicy(new Response(
            $body,
            $status,
            $this->headersForBody($headers, $body, 'text/plain; charset=utf-8'),
        ), $includeBody);
    }

    /**
     * @param array<string, mixed>|HeaderBag $headers
     */
    public function redirect(
        string $location,
        ResponseStatus|int $status = ResponseStatus::FOUND,
        array|HeaderBag $headers = [],
    ): RedirectResponse {
        return new RedirectResponse($location, $status, $headers);
    }

    /**
     * @param array<string, mixed>|HeaderBag $headers
     */
    public function noContent(array|HeaderBag $headers = []): Response
    {
        return new Response('', ResponseStatus::NO_CONTENT, $headers);
    }

    /**
     * @param array<string, mixed>|HeaderBag $headers
     */
    public function notFound(string $message = 'Page not found.', array|HeaderBag $headers = [], bool $includeBody = true): Response
    {
        return $this->html($this->simpleHtml('Not Found', $message), ResponseStatus::NOT_FOUND, $headers, $includeBody);
    }

    /**
     * @param list<string> $allowedMethods
     * @param array<string, mixed>|HeaderBag $headers
     */
    public function methodNotAllowed(array $allowedMethods = [], array|HeaderBag $headers = [], bool $includeBody = true): Response
    {
        $allowedMethods = array_values(array_unique(array_map('strtoupper', $allowedMethods)));
        sort($allowedMethods);

        if ($allowedMethods !== []) {
            $headers = $this->withHeader($headers, 'Allow', implode(', ', $allowedMethods));
        }

        return $this->html(
            $this->simpleHtml('Method Not Allowed', 'The request method is not allowed for this page.'),
            ResponseStatus::METHOD_NOT_ALLOWED,
            $headers,
            $includeBody,
        );
    }

    /**
     * @param array<string, mixed>|HeaderBag $headers
     */
    public function error(
        string $message = 'Unable to serve this page.',
        ResponseStatus|int $status = ResponseStatus::INTERNAL_SERVER_ERROR,
        array|HeaderBag $headers = [],
        bool $includeBody = true,
    ): Response {
        return $this->html($this->simpleHtml('Server Error', $message), $status, $headers, $includeBody);
    }

    private function withBodyPolicy(Response $response, bool $includeBody): Response
    {
        if ($includeBody) {
            return $response;
        }

        if ($response->allowsBody() && !$response->hasHeader('Content-Length')) {
            $response = $response->withHeader('Content-Length', (string) strlen($response->body()));
        }

        return $response->withBody('');
    }

    /**
     * @param array<string, mixed>|HeaderBag $headers
     * @return array<string, mixed>|HeaderBag
     */
    private function headersForBody(array|HeaderBag $headers, string $body, string $contentType): array|HeaderBag
    {
        $headers = $this->withHeaderIfMissing($headers, 'Content-Type', $contentType);

        return $this->withHeaderIfMissing($headers, 'Content-Length', (string) strlen($body));
    }

    /**
     * @param array<string, mixed>|HeaderBag $headers
     * @return array<string, mixed>|HeaderBag
     */
    private function withHeaderIfMissing(array|HeaderBag $headers, string $name, string $value): array|HeaderBag
    {
        if ($headers instanceof HeaderBag) {
            $headers = clone $headers;

            if (!$headers->has($name)) {
                $headers->set($name, $value);
            }

            return $headers;
        }

        foreach ($headers as $header => $_) {
            if (strcasecmp((string) $header, $name) === 0) {
                return $headers;
            }
        }

        $headers[$name] = $value;

        return $headers;
    }

    /**
     * @param array<string, mixed>|HeaderBag $headers
     * @return array<string, mixed>|HeaderBag
     */
    private function withHeader(array|HeaderBag $headers, string $name, string $value): array|HeaderBag
    {
        if ($headers instanceof HeaderBag) {
            return (clone $headers)->set($name, $value);
        }

        $headers[$name] = $value;

        return $headers;
    }

    private function statusCodeFrom(ResponseStatus|int $status): int
    {
        return $status instanceof ResponseStatus ? $status->value : $status;
    }

    private function simpleHtml(string $title, string $message): string
    {
        return '<!doctype html><html lang="en"><head><meta charset="utf-8"><title>'
            . $this->escape($title)
            . '</title></head><body><h1>'
            . $this->escape($title)
            . '</h1><p>'
            . $this->escape($message)
            . '</p></body></html>';
    }

    private function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
