<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

final class ValidationTest extends TestCase
{
    public function testValidateLoginInput(): void
    {
        self::assertSame([], validate_login_input('  alice  ', 'secret123'));

        $errors = validate_login_input('   ', '');
        self::assertSame('Please enter username.', $errors['username'] ?? null);
        self::assertSame('Please enter your password.', $errors['password'] ?? null);
    }

    public function testValidateRegistrationInput(): void
    {
        self::assertSame([], validate_registration_input('  alice  ', 'secret123', 'secret123'));

        $blank = validate_registration_input('   ', '', '');
        self::assertSame('Please enter a username.', $blank['username'] ?? null);
        self::assertSame('Please enter a password.', $blank['password'] ?? null);
        self::assertSame('Please confirm password.', $blank['confirm_password'] ?? null);

        $tooLong = validate_registration_input(str_repeat('a', 51), '12345', '12345');
        self::assertSame('Username must be 50 characters or fewer.', $tooLong['username'] ?? null);
        self::assertSame('Password must have at least 6 characters.', $tooLong['password'] ?? null);

        // Strict comparison check against PHP's loose == numeric-string coercion
        $looseTrap = validate_registration_input('alice', '0e1234', '0e5678');
        self::assertSame('Passwords did not match.', $looseTrap['confirm_password'] ?? null);
    }

    public function testValidatePasswordChangeInput(): void
    {
        self::assertSame([], validate_password_change_input('newpass1', 'newpass1'));

        $empty = validate_password_change_input('', '');
        self::assertSame('Please enter the new password.', $empty['new_password'] ?? null);
        self::assertSame('Please confirm the password.', $empty['confirm_password'] ?? null);

        $short = validate_password_change_input('12345', '12345');
        self::assertSame('Password must have at least 6 characters.', $short['new_password'] ?? null);

        $looseTrap = validate_password_change_input('0e1234', '0e5678');
        self::assertSame('Passwords did not match.', $looseTrap['confirm_password'] ?? null);
    }
}
