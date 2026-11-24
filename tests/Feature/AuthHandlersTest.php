<?php

declare(strict_types=1);

namespace Tests\Feature;

use PHPUnit\Framework\TestCase;

final class AuthHandlersTest extends TestCase
{
    /** @var array<int, array{id: int, username: string, password: string}> */
    private array $users = [];
    private int $nextId = 1;

    protected function setUp(): void
    {
        $this->users = [];
        $this->nextId = 1;
    }

    public function testFullProceduralAuthWorkflowAndEscaping(): void
    {
        $session = [];
        $regenerated = 0;
        $destroyed = 0;

        $findUser = function (string $username): ?array {
            foreach ($this->users as $user) {
                if ($user['username'] === $username) {
                    return $user;
                }
            }
            return null;
        };

        $createUser = function (string $username, string $hash): int {
            $id = $this->nextId++;
            $this->users[$id] = [
                'id' => $id,
                'username' => $username,
                'password' => $hash,
            ];
            return $id;
        };

        $updatePassword = function (int $userId, string $hash): bool {
            if (isset($this->users[$userId])) {
                $this->users[$userId]['password'] = $hash;
            }
            return true;
        };

        $xssUsername = '<script>alert(1)</script>';
        $csrf = csrf_token($session);

        // 1. Registration
        $regResult = handle_register_submission([
            'csrf_token' => $csrf,
            'username' => '  ' . $xssUsername . '  ',
            'password' => 'InitialPass1!',
            'confirm_password' => 'InitialPass1!',
        ], $session, $findUser, $createUser);

        self::assertSame(302, $regResult['status']);
        self::assertSame('login', $regResult['redirect']);

        // 2. Duplicate username rejected
        $dupResult = handle_register_submission([
            'csrf_token' => $csrf,
            'username' => $xssUsername,
            'password' => 'InitialPass1!',
            'confirm_password' => 'InitialPass1!',
        ], $session, $findUser, $createUser);

        self::assertSame(422, $dupResult['status']);
        self::assertSame('This username is already taken.', $dupResult['errors']['username'] ?? null);

        // 3. Login page renders flash status
        $loginHtml = render_page('login', [
            'title' => 'Login',
            'csrfToken' => csrf_token($session),
            'username' => '',
            'errors' => [],
            'flash' => flash_consume($session, 'status'),
        ]);
        self::assertStringContainsString('Account created! Please log in.', $loginHtml);

        // 4. Login succeeds, regenerates session ID, and rotates CSRF token
        $loginResult = handle_login_submission(
            [
                'csrf_token' => $csrf,
                'username' => $xssUsername,
                'password' => 'InitialPass1!',
            ],
            $session,
            $findUser,
            static function () use (&$regenerated): void {
                $regenerated++;
            }
        );

        self::assertSame(302, $loginResult['status']);
        self::assertSame('./', $loginResult['redirect']);
        self::assertSame(1, $regenerated);
        self::assertNotSame($csrf, $session['csrf_token']);

        // 5. Dashboard renders escaped username and never raw script tags
        $dashboardHtml = render_page('dashboard', [
            'title' => 'Welcome',
            'username' => auth_username($session) ?? '',
            'csrfToken' => csrf_token($session),
        ]);
        self::assertStringContainsString('&lt;script&gt;alert(1)&lt;/script&gt;', $dashboardHtml);
        self::assertStringNotContainsString('<script>alert(1)</script>', $dashboardHtml);

        // 6. Change password updates hash, destroys session, and sets flash
        $changeResult = handle_change_password_submission(
            [
                'csrf_token' => csrf_token($session),
                'new_password' => 'UpdatedPass2!',
                'confirm_password' => 'UpdatedPass2!',
            ],
            $session,
            $updatePassword,
            static function () use (&$destroyed): void {
                $destroyed++;
            }
        );

        self::assertSame(302, $changeResult['status']);
        self::assertSame('login', $changeResult['redirect']);
        self::assertSame(1, $destroyed);
        self::assertFalse(auth_is_logged_in($session));
        self::assertSame('Password updated. Please log in with your new password.', flash_consume($session, 'status'));

        // 7. Old password fails; new password succeeds
        $freshCsrf = csrf_token($session);
        $oldAttempt = handle_login_submission([
            'csrf_token' => $freshCsrf,
            'username' => $xssUsername,
            'password' => 'InitialPass1!',
        ], $session, $findUser);
        self::assertSame(422, $oldAttempt['status']);

        $newAttempt = handle_login_submission([
            'csrf_token' => $freshCsrf,
            'username' => $xssUsername,
            'password' => 'UpdatedPass2!',
        ], $session, $findUser);
        self::assertSame(302, $newAttempt['status']);

        // 8. POST logout destroys session
        $logoutResult = handle_logout_submission(
            ['csrf_token' => csrf_token($session)],
            $session,
            static function () use (&$destroyed): void {
                $destroyed++;
            }
        );
        self::assertSame(302, $logoutResult['status']);
        self::assertSame('login', $logoutResult['redirect']);
        self::assertSame(2, $destroyed);
        self::assertFalse(auth_is_logged_in($session));
    }

    public function testInvalidOrMissingCsrfReturns403AcrossAllMutatingHandlers(): void
    {
        $session = [];
        csrf_token($session);

        $noopFind = static fn (string $u): ?array => null;
        $noopCreate = static fn (string $u, string $h): int => 1;
        $noopUpdate = static fn (int $id, string $h): bool => true;

        self::assertSame(403, handle_register_submission(['csrf_token' => 'bad'], $session, $noopFind, $noopCreate)['status']);
        self::assertSame(403, handle_login_submission(['csrf_token' => 'bad'], $session, $noopFind)['status']);
        self::assertSame(403, handle_logout_submission([], $session)['status']);

        auth_login_user($session, ['id' => 1, 'username' => 'alice']);
        self::assertSame(403, handle_change_password_submission(['csrf_token' => 'bad'], $session, $noopUpdate)['status']);
    }

    public function testFailedRegisterAndChangePasswordNeverEchoSubmittedPasswords(): void
    {
        $session = [];
        $csrf = csrf_token($session);

        $regResult = handle_register_submission([
            'csrf_token' => $csrf,
            'username' => 'alice',
            'password' => 'SecretPass1!',
            'confirm_password' => 'DifferentPass2!',
        ], $session, static fn (string $u): ?array => null, static fn (string $u, string $h): int => 1);

        self::assertSame(422, $regResult['status']);
        $regHtml = render_page('register', [
            'title' => 'Sign Up',
            'csrfToken' => $csrf,
            'username' => $regResult['username'],
            'errors' => $regResult['errors'],
        ]);
        self::assertStringNotContainsString('SecretPass1!', $regHtml);
        self::assertStringNotContainsString('DifferentPass2!', $regHtml);

        auth_login_user($session, ['id' => 1, 'username' => 'alice']);
        $changeResult = handle_change_password_submission([
            'csrf_token' => $csrf,
            'new_password' => 'SecretPass1!',
            'confirm_password' => 'DifferentPass2!',
        ], $session, static fn (int $id, string $h): bool => true);

        self::assertSame(422, $changeResult['status']);
        $changeHtml = render_page('change-password', [
            'title' => 'Change Password',
            'csrfToken' => $csrf,
            'errors' => $changeResult['errors'],
        ]);
        self::assertStringNotContainsString('SecretPass1!', $changeHtml);
        self::assertStringNotContainsString('DifferentPass2!', $changeHtml);
    }
}
