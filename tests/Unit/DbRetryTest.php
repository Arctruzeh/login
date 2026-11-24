<?php

declare(strict_types=1);

namespace Tests\Unit;

use mysqli_sql_exception;
use PHPUnit\Framework\TestCase;

final class DbRetryTest extends TestCase
{
    public function testRetriesTransientConnectionErrorUntilSuccess(): void
    {
        $attempts = 0;
        $sleeps = 0;

        $result = db_connect_with_retry(
            static function () use (&$attempts): string {
                $attempts++;
                if ($attempts < 3) {
                    throw new mysqli_sql_exception('Connection refused', 2002);
                }
                return 'connected';
            },
            maxAttempts: 5,
            delayUs: 1000,
            sleep: static function (int $us) use (&$sleeps): void {
                $sleeps++;
            }
        );

        self::assertSame('connected', $result);
        self::assertSame(3, $attempts);
        self::assertSame(2, $sleeps);
    }

    public function testDoesNotRetryNonTransientAuthError(): void
    {
        $attempts = 0;
        $sleeps = 0;

        try {
            db_connect_with_retry(
                static function () use (&$attempts): never {
                    $attempts++;
                    throw new mysqli_sql_exception('Access denied', 1045);
                },
                maxAttempts: 5,
                delayUs: 1000,
                sleep: static function () use (&$sleeps): void {
                    $sleeps++;
                }
            );
            self::fail('Expected mysqli_sql_exception to be thrown.');
        } catch (mysqli_sql_exception $e) {
            self::assertSame(1045, (int) $e->getCode());
            self::assertSame(1, $attempts);
            self::assertSame(0, $sleeps);
        }
    }

    public function testRethrowsTransientErrorAfterMaxAttempts(): void
    {
        $attempts = 0;
        $sleeps = 0;

        try {
            db_connect_with_retry(
                static function () use (&$attempts): never {
                    $attempts++;
                    throw new mysqli_sql_exception('Connection refused', 2002);
                },
                maxAttempts: 4,
                delayUs: 500,
                sleep: static function () use (&$sleeps): void {
                    $sleeps++;
                }
            );
            self::fail('Expected mysqli_sql_exception after max attempts.');
        } catch (mysqli_sql_exception $e) {
            self::assertSame(2002, (int) $e->getCode());
            self::assertSame(4, $attempts);
            self::assertSame(3, $sleeps);
        }
    }
}
