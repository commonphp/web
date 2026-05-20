<?php

declare(strict_types=1);

namespace CommonPHP\Web\Tests\Unit;

use CommonPHP\Web\Exceptions\PageNotFoundException;
use CommonPHP\Web\Exceptions\WebDispatchException;
use CommonPHP\Web\PageRegistry;
use CommonPHP\Web\Tests\Fixtures\NotAPage;
use CommonPHP\Web\Tests\Fixtures\SamplePage;
use CommonPHP\Web\Tests\Fixtures\StringPage;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class PageRegistryTest extends TestCase
{
    public function testItRegistersListsGetsIteratesAndRemovesPages(): void
    {
        $sample = new SamplePage();
        $registry = new PageRegistry(['sample' => $sample]);

        self::assertTrue($registry->has('sample'));
        self::assertSame($sample, $registry->get('sample'));
        self::assertSame(['sample'], $registry->names());
        self::assertSame(['sample' => $sample], $registry->all());
        self::assertCount(1, $registry);
        self::assertSame(['sample' => $sample], iterator_to_array($registry));

        self::assertSame($registry, $registry->register('string', StringPage::class));
        self::assertSame(StringPage::class, $registry->get('string'));
        self::assertSame(['sample', 'string'], $registry->names());

        self::assertSame($registry, $registry->remove('sample'));
        self::assertFalse($registry->has('sample'));
        self::assertSame($registry, $registry->clear());
        self::assertSame([], $registry->all());
    }

    public function testItResolvesPageObjectsAndPageClasses(): void
    {
        $sample = new SamplePage();
        $registry = new PageRegistry([
            'object' => $sample,
            'class' => StringPage::class,
        ]);

        self::assertSame($sample, $registry->resolve('object'));
        self::assertInstanceOf(StringPage::class, $registry->resolve('class'));
    }

    public function testItRejectsInvalidNamesAndClassNames(): void
    {
        $registry = new PageRegistry();

        $this->expectException(InvalidArgumentException::class);
        $registry->register('', SamplePage::class);
    }

    public function testItRejectsEmptyClassNames(): void
    {
        $registry = new PageRegistry();

        $this->expectException(InvalidArgumentException::class);
        $registry->register('empty', '');
    }

    public function testItThrowsWhenPagesAreMissingOrInvalid(): void
    {
        $registry = new PageRegistry([
            'not-page' => NotAPage::class,
        ]);

        $this->expectException(PageNotFoundException::class);
        $registry->get('missing');
    }

    public function testItThrowsWhenARegisteredClassIsNotAPage(): void
    {
        $registry = new PageRegistry([
            'not-page' => NotAPage::class,
        ]);

        $this->expectException(WebDispatchException::class);
        $registry->resolve('not-page');
    }
}
