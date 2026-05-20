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
use CommonPHP\Web\Exceptions\PageRenderException;
use Throwable;

class PageResponse extends Response
{
    /**
     * @param array<string, mixed>|HeaderBag $headers
     */
    public function __construct(
        private readonly ?PageInterface $page = null,
        private readonly ?PageRenderContext $context = null,
        string $body = '',
        ResponseStatus|int $status = ResponseStatus::OK,
        array|HeaderBag $headers = [],
    ) {
        parent::__construct($body, $status, $headers);
    }

    public static function fromPage(
        PageInterface|View|TemplateInterface $page,
        PageRendererInterface $renderer,
        PageRenderContext $context,
        bool $includeBody = true,
    ): self {
        try {
            $body = $renderer->render($page, $context);
        } catch (Throwable $exception) {
            throw PageRenderException::forTarget(self::targetLabel($page), $exception);
        }

        $status = $page instanceof PageInterface ? $page->status($context) : ResponseStatus::OK;
        $body = ResponseStatus::codeAllowsBody(self::statusCodeFrom($status)) ? $body : '';
        $headers = self::headersForBody(
            $page instanceof PageInterface ? $page->headers($context) : [],
            $body,
        );

        return new self(
            $page instanceof PageInterface ? $page : null,
            $context,
            $includeBody ? $body : '',
            $status,
            $headers,
        );
    }

    public function page(): ?PageInterface
    {
        return $this->page;
    }

    public function context(): ?PageRenderContext
    {
        return $this->context;
    }

    /**
     * @param array<string, mixed>|HeaderBag $headers
     * @return array<string, mixed>|HeaderBag
     */
    private static function headersForBody(array|HeaderBag $headers, string $body): array|HeaderBag
    {
        if ($headers instanceof HeaderBag) {
            $headers = clone $headers;

            if (!$headers->has('Content-Type')) {
                $headers->set('Content-Type', 'text/html; charset=utf-8');
            }

            if (!$headers->has('Content-Length')) {
                $headers->set('Content-Length', (string) strlen($body));
            }

            return $headers;
        }

        if (!self::arrayHasHeader($headers, 'Content-Type')) {
            $headers['Content-Type'] = 'text/html; charset=utf-8';
        }

        if (!self::arrayHasHeader($headers, 'Content-Length')) {
            $headers['Content-Length'] = (string) strlen($body);
        }

        return $headers;
    }

    /**
     * @param array<string, mixed> $headers
     */
    private static function arrayHasHeader(array $headers, string $name): bool
    {
        foreach ($headers as $header => $_) {
            if (strcasecmp((string) $header, $name) === 0) {
                return true;
            }
        }

        return false;
    }

    private static function statusCodeFrom(ResponseStatus|int $status): int
    {
        return $status instanceof ResponseStatus ? $status->value : $status;
    }

    private static function targetLabel(PageInterface|View|TemplateInterface $page): string
    {
        if ($page instanceof PageInterface) {
            return $page::class;
        }

        if ($page instanceof View) {
            return $page->template()->name();
        }

        return $page->name();
    }
}
