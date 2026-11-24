<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

final class RouterTest extends TestCase
{
    public function testResolvesCleanPhpAndRootRoutes(): void
    {
        self::assertSame('/', resolve_request_route('/'));
        self::assertSame('/', resolve_request_route('/index'));
        self::assertSame('/', resolve_request_route('/index.php'));
        self::assertSame('/login', resolve_request_route('/login'));
        self::assertSame('/login', resolve_request_route('/login/'));
        self::assertSame('/login', resolve_request_route('/login.php'));
        self::assertSame('/register', resolve_request_route('/register?next=1'));
        self::assertSame('/change-password', resolve_request_route('/change-password'));
        self::assertSame('/logout', resolve_request_route('/logout'));
        self::assertSame('/does-not-exist', resolve_request_route('/does-not-exist'));
    }
}
