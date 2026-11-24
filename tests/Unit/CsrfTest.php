<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

final class CsrfTest extends TestCase
{
    public function testGeneratesAndReusesToken(): void
    {
        $session = [];

        $first = csrf_token($session);
        $second = csrf_token($session);

        self::assertSame(64, strlen($first));
        self::assertMatchesRegularExpression('/^[0-9a-f]{64}$/', $first);
        self::assertSame($first, $second);
    }

    public function testValidatesMatchingTokenAndRejectsInvalidTokens(): void
    {
        $session = [];
        $token = csrf_token($session);

        self::assertTrue(csrf_validate($session, $token));
        self::assertFalse(csrf_validate($session, null));
        self::assertFalse(csrf_validate($session, ''));
        self::assertFalse(csrf_validate($session, 'wrong-token'));
    }

    public function testRotateReplacesTokenAndInvalidatesPreviousToken(): void
    {
        $session = [];
        $original = csrf_token($session);

        $rotated = csrf_rotate($session);

        self::assertNotSame($original, $rotated);
        self::assertFalse(csrf_validate($session, $original));
        self::assertTrue(csrf_validate($session, $rotated));
    }
}
