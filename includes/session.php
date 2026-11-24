<?php

declare(strict_types=1);

if (PHP_SAPI !== 'cli' && realpath((string) ($_SERVER['SCRIPT_FILENAME'] ?? '')) === realpath(__FILE__)) {
    http_response_code(403);
    header('Content-Type: text/plain; charset=UTF-8');
    exit('403 Forbidden');
}

function session_init(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');

        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'domain' => '',
            'secure' => $isHttps,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);

        session_start();
    }
}

function session_destroy_native(): void
{
    session_init();
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        $sessionName = session_name();
        if ($sessionName !== false) {
            setcookie(
                $sessionName,
                '',
                [
                    'expires' => time() - 42000,
                    'path' => $params['path'],
                    'domain' => $params['domain'],
                    'secure' => $params['secure'],
                    'httponly' => $params['httponly'],
                    'samesite' => $params['samesite'] ?: 'Lax',
                ]
            );
        }
    }

    session_destroy();
}

function session_reset_native(): void
{
    session_init();
    $_SESSION = [];
    session_regenerate_id(true);
}

/**
 * @param array<string, mixed> $session
 */
function auth_is_logged_in(array $session): bool
{
    return ($session['loggedin'] ?? false) === true && auth_user_id($session) !== null;
}

/**
 * @param array<string, mixed> $session
 */
function auth_user_id(array $session): ?int
{
    $id = $session['id'] ?? null;
    return is_int($id) && $id > 0 ? $id : null;
}

/**
 * @param array<string, mixed> $session
 */
function auth_username(array $session): ?string
{
    $username = $session['username'] ?? null;
    return is_string($username) && $username !== '' ? $username : null;
}

/**
 * @param array<string, mixed> $session
 * @param array{id: int, username: string} $user
 */
function auth_login_user(array &$session, array $user, ?callable $regenerate = null): void
{
    if ($regenerate !== null) {
        $regenerate();
    }

    $session['loggedin'] = true;
    $session['id'] = (int) $user['id'];
    $session['username'] = (string) $user['username'];
}

/**
 * @param array<string, mixed> $session
 */
function auth_logout_user(array &$session, ?callable $destroy = null): void
{
    $session = [];

    if ($destroy !== null) {
        $destroy();
    }
}

/**
 * @param array<string, mixed> $session
 */
function flash_set(array &$session, string $key, string $message): void
{
    if (!isset($session['_flash']) || !is_array($session['_flash'])) {
        $session['_flash'] = [];
    }
    $session['_flash'][$key] = $message;
}

/**
 * @param array<string, mixed> $session
 */
function flash_consume(array &$session, string $key): ?string
{
    if (!isset($session['_flash'][$key])) {
        return null;
    }

    $message = (string) $session['_flash'][$key];
    unset($session['_flash'][$key]);
    return $message;
}
