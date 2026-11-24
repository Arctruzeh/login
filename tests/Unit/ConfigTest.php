<?php

declare(strict_types=1);

namespace Tests\Unit;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class ConfigTest extends TestCase
{
    public function testParsesMysqlUrlWithExplicitPortAndEncodedPassword(): void
    {
        $cfg = db_config_from_env([
            'MYSQL_URL' => 'mysql://dbuser:p%40ss%2Fword@db.example.internal:3307/app_db',
        ]);

        self::assertSame('db.example.internal', $cfg['host']);
        self::assertSame(3307, $cfg['port']);
        self::assertSame('app_db', $cfg['database']);
        self::assertSame('dbuser', $cfg['username']);
        self::assertSame('p@ss/word', $cfg['password']);
    }

    public function testParsesMysqlUrlWithoutPortOrPassword(): void
    {
        $cfg = db_config_from_env([
            'MYSQL_URL' => 'mysql://admin@localhost/custom_db',
        ]);

        self::assertSame('localhost', $cfg['host']);
        self::assertSame(3306, $cfg['port']);
        self::assertSame('custom_db', $cfg['database']);
        self::assertSame('admin', $cfg['username']);
        self::assertSame('', $cfg['password']);
    }

    public function testFallsBackToDatabaseUrlWhenMysqlUrlIsAbsent(): void
    {
        $cfg = db_config_from_env([
            'DATABASE_URL' => 'mysql://user2:secret@railway.internal:3308/railway',
        ]);

        self::assertSame('railway.internal', $cfg['host']);
        self::assertSame(3308, $cfg['port']);
        self::assertSame('railway', $cfg['database']);
        self::assertSame('user2', $cfg['username']);
        self::assertSame('secret', $cfg['password']);
    }

    public function testUsesIndividualMysqlVariablesWhenNoUrlIsProvided(): void
    {
        $cfg = db_config_from_env([
            'MYSQLHOST' => '127.0.0.1',
            'MYSQLPORT' => '3309',
            'MYSQLDATABASE' => 'auth_db',
            'MYSQLUSER' => 'app',
            'MYSQLPASSWORD' => 'hunter2',
        ]);

        self::assertSame('127.0.0.1', $cfg['host']);
        self::assertSame(3309, $cfg['port']);
        self::assertSame('auth_db', $cfg['database']);
        self::assertSame('app', $cfg['username']);
        self::assertSame('hunter2', $cfg['password']);
    }

    public function testFallsBackToLocalDefaultsWhenEnvironmentIsEmpty(): void
    {
        $cfg = db_config_from_env([]);

        self::assertSame('localhost', $cfg['host']);
        self::assertSame(3306, $cfg['port']);
        self::assertSame('demo', $cfg['database']);
        self::assertSame('root', $cfg['username']);
        self::assertSame('', $cfg['password']);
    }

    public function testThrowsOnMalformedUrl(): void
    {
        $this->expectException(InvalidArgumentException::class);

        db_config_from_env([
            'MYSQL_URL' => 'not a valid url',
        ]);
    }
}
