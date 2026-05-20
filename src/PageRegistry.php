<?php

declare(strict_types=1);

namespace CommonPHP\Web;

use CommonPHP\Web\Contracts\PageInterface;
use CommonPHP\Web\Exceptions\PageNotFoundException;
use CommonPHP\Web\Exceptions\WebDispatchException;
use Countable;
use InvalidArgumentException;
use IteratorAggregate;
use Traversable;

/**
 * @implements IteratorAggregate<string, PageInterface|class-string<PageInterface>>
 */
class PageRegistry implements Countable, IteratorAggregate
{
    /**
     * @var array<string, PageInterface|class-string<PageInterface>>
     */
    private array $pages = [];

    /**
     * @param iterable<string, PageInterface|class-string<PageInterface>> $pages
     */
    public function __construct(iterable $pages = [])
    {
        foreach ($pages as $name => $page) {
            $this->register((string) $name, $page);
        }
    }

    /**
     * @param PageInterface|class-string<PageInterface> $page
     */
    public function register(string $name, PageInterface|string $page): static
    {
        $name = $this->normalizeName($name);

        if (is_string($page) && trim($page) === '') {
            throw new InvalidArgumentException('Page class names cannot be empty.');
        }

        $this->pages[$name] = $page;

        return $this;
    }

    public function has(string $name): bool
    {
        return isset($this->pages[$name]);
    }

    /**
     * @return PageInterface|class-string<PageInterface>
     */
    public function get(string $name): PageInterface|string
    {
        return $this->pages[$name] ?? throw PageNotFoundException::forName($name);
    }

    public function resolve(string $name): PageInterface
    {
        $page = $this->get($name);

        if ($page instanceof PageInterface) {
            return $page;
        }

        if (!class_exists($page)) {
            throw PageNotFoundException::forName($name);
        }

        $instance = new $page();

        if (!$instance instanceof PageInterface) {
            throw WebDispatchException::invalidPage($name, get_debug_type($instance));
        }

        return $instance;
    }

    public function remove(string $name): static
    {
        unset($this->pages[$name]);

        return $this;
    }

    public function clear(): static
    {
        $this->pages = [];

        return $this;
    }

    /**
     * @return array<string, PageInterface|class-string<PageInterface>>
     */
    public function all(): array
    {
        return $this->pages;
    }

    /**
     * @return list<string>
     */
    public function names(): array
    {
        return array_keys($this->pages);
    }

    public function count(): int
    {
        return count($this->pages);
    }

    public function getIterator(): Traversable
    {
        yield from $this->pages;
    }

    private function normalizeName(string $name): string
    {
        $name = trim($name);

        if ($name === '') {
            throw new InvalidArgumentException('Page names cannot be empty.');
        }

        return $name;
    }
}
