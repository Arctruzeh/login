<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

final class SessionTest extends TestCase
{
    public function testLoginAndLogoutManageSessionStateAndCallbacks(): void
    {
        $session = [];
        $regenerated = 0;
        $destroyed = 0;

        self::assertFalse(auth_is_logged_in($session));
        self::assertNull(auth_user_id($session));
        self::assertNull(auth_username($session));

        auth_login_user(
            $session,
            ['id' => 42, 'username' => 'alice'],
            static function () use (&$regenerated): void {
                $regenerated++;
            }
        );

        self::assertSame(1, $regenerated);
        self::assertTrue(auth_is_logged_in($session));
        self::assertSame(42, auth_user_id($session));
        self::assertSame('alice', auth_username($session));

        auth_logout_user(
            $session,
            static function () use (&$destroyed): void {
                $destroyed++;
            }
        );

        self::assertSame(1, $destroyed);
        self::assertFalse(auth_is_logged_in($session));
        self::assertSame([], $session);
    }

    public function testFlashSetAndConsume(): void
    {
        $session = [];

        self::assertNull(flash_consume($session, 'status'));

        flash_set($session, 'status', 'Saved!');
        self::assertSame('Saved!', flash_consume($session, 'status'));
        self::assertNull(flash_consume($session, 'status'));
    }
}
