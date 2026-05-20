<?php

declare(strict_types=1);

namespace CommonPHP\Web\Tests\Unit;

use CommonPHP\HTTP\Enums\ResponseStatus;
use CommonPHP\HTTP\HeaderBag;
use CommonPHP\Web\Exceptions\WebResponseException;
use CommonPHP\Web\RedirectResponse;
use PHPUnit\Framework\TestCase;

final class RedirectResponseTest extends TestCase
{
    public function testItCreatesRedirectResponses(): void
    {
        $temporary = RedirectResponse::to('/temporary');
        $permanent = RedirectResponse::permanent('/permanent');
        $seeOther = RedirectResponse::seeOther('/thanks');

        self::assertSame(302, $temporary->statusCode());
        self::assertSame('/temporary', $temporary->location());
        self::assertSame('/temporary', $temporary->header('Location'));
        self::assertSame(301, $permanent->statusCode());
        self::assertSame(303, $seeOther->statusCode());
    }

    public function testItAcceptsArrayAndHeaderBagHeaders(): void
    {
        $array = new RedirectResponse('/array', ResponseStatus::TEMPORARY_REDIRECT, ['X-Test' => 'array']);
        $bag = new RedirectResponse('/bag', ResponseStatus::PERMANENT_REDIRECT, new HeaderBag(['X-Test' => 'bag']));

        self::assertSame('array', $array->header('X-Test'));
        self::assertSame('/array', $array->header('Location'));
        self::assertSame('bag', $bag->header('X-Test'));
        self::assertSame('/bag', $bag->header('Location'));
    }

    public function testWithLocationReturnsANewResponse(): void
    {
        $response = RedirectResponse::to('/old');
        $changed = $response->withLocation('/new');

        self::assertNotSame($response, $changed);
        self::assertSame('/old', $response->location());
        self::assertSame('/new', $changed->location());
        self::assertSame('/new', $changed->header('Location'));
    }

    public function testItRejectsEmptyLocations(): void
    {
        $this->expectException(WebResponseException::class);
        new RedirectResponse(' ');
    }

    public function testItRejectsNonRedirectStatuses(): void
    {
        $this->expectException(WebResponseException::class);
        new RedirectResponse('/bad', ResponseStatus::OK);
    }
}
