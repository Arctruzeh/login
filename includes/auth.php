<?php

declare(strict_types=1);

/**
 * @param array<string, mixed> $post
 * @param array<string, mixed> $session
 * @param callable(string): (?array{id: int, username: string, password: string}) $findUser
 * @param callable(string, string): int $createUser
 * @return array{status: int, redirect: ?string, errors: array<string, string>, username: string, error_message: ?string}
 */
function handle_register_submission(
    array $post,
    array &$session,
    callable $findUser,
    callable $createUser,
): array {
    $rawUsername = post_string($post, 'username');
    $username = trim($rawUsername);

    if (!csrf_validate($session, post_string($post, 'csrf_token'))) {
        return [
            'status' => 403,
            'redirect' => null,
            'errors' => [],
            'username' => $username,
            'error_message' => 'Invalid or missing CSRF token.',
        ];
    }

    $password = post_string($post, 'password');
    $confirmPassword = post_string($post, 'confirm_password');
    $errors = validate_registration_input($rawUsername, $password, $confirmPassword);

    if ($errors !== []) {
        return [
            'status' => 422,
            'redirect' => null,
            'errors' => $errors,
            'username' => $username,
            'error_message' => null,
        ];
    }

    if ($findUser($username) !== null) {
        return [
            'status' => 422,
            'redirect' => null,
            'errors' => ['username' => 'This username is already taken.'],
            'username' => $username,
            'error_message' => null,
        ];
    }

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    try {
        $createUser($username, $passwordHash);
    } catch (mysqli_sql_exception $e) {
        if ((int) $e->getCode() === 1062) {
            return [
                'status' => 422,
                'redirect' => null,
                'errors' => ['username' => 'This username is already taken.'],
                'username' => $username,
                'error_message' => null,
            ];
        }
        throw $e;
    }

    flash_set($session, 'status', 'Account created! Please log in.');

    return [
        'status' => 302,
        'redirect' => 'login',
        'errors' => [],
        'username' => $username,
        'error_message' => null,
    ];
}

/**
 * @param array<string, mixed> $post
 * @param array<string, mixed> $session
 * @param callable(string): (?array{id: int, username: string, password: string}) $findUser
 * @return array{status: int, redirect: ?string, errors: array<string, string>, username: string, error_message: ?string}
 */
function handle_login_submission(
    array $post,
    array &$session,
    callable $findUser,
    ?callable $regenerateSession = null,
): array {
    $rawUsername = post_string($post, 'username');
    $username = trim($rawUsername);

    if (!csrf_validate($session, post_string($post, 'csrf_token'))) {
        return [
            'status' => 403,
            'redirect' => null,
            'errors' => [],
            'username' => $username,
            'error_message' => 'Invalid or missing CSRF token.',
        ];
    }

    $password = post_string($post, 'password');
    $errors = validate_login_input($rawUsername, $password);

    if ($errors !== []) {
        return [
            'status' => 422,
            'redirect' => null,
            'errors' => $errors,
            'username' => $username,
            'error_message' => null,
        ];
    }

    $user = $findUser($username);
    if ($user === null || !password_verify($password, $user['password'])) {
        return [
            'status' => 422,
            'redirect' => null,
            'errors' => ['form' => 'Invalid username or password.'],
            'username' => $username,
            'error_message' => null,
        ];
    }

    auth_login_user($session, $user, $regenerateSession);
    csrf_rotate($session);

    return [
        'status' => 302,
        'redirect' => './',
        'errors' => [],
        'username' => $user['username'],
        'error_message' => null,
    ];
}

/**
 * @param array<string, mixed> $post
 * @param array<string, mixed> $session
 * @param callable(int, string): bool $updatePassword
 * @return array{status: int, redirect: ?string, errors: array<string, string>, username: string, error_message: ?string}
 */
function handle_change_password_submission(
    array $post,
    array &$session,
    callable $updatePassword,
    ?callable $destroySession = null,
): array {
    $userId = auth_user_id($session);
    if (!auth_is_logged_in($session) || $userId === null) {
        return [
            'status' => 302,
            'redirect' => 'login',
            'errors' => [],
            'username' => '',
            'error_message' => null,
        ];
    }

    if (!csrf_validate($session, post_string($post, 'csrf_token'))) {
        return [
            'status' => 403,
            'redirect' => null,
            'errors' => [],
            'username' => '',
            'error_message' => 'Invalid or missing CSRF token.',
        ];
    }

    $newPassword = post_string($post, 'new_password');
    $confirmPassword = post_string($post, 'confirm_password');
    $errors = validate_password_change_input($newPassword, $confirmPassword);

    if ($errors !== []) {
        return [
            'status' => 422,
            'redirect' => null,
            'errors' => $errors,
            'username' => '',
            'error_message' => null,
        ];
    }

    $updatePassword($userId, password_hash($newPassword, PASSWORD_DEFAULT));
    auth_logout_user($session, $destroySession);
    flash_set($session, 'status', 'Password updated. Please log in with your new password.');

    return [
        'status' => 302,
        'redirect' => 'login',
        'errors' => [],
        'username' => '',
        'error_message' => null,
    ];
}

/**
 * @param array<string, mixed> $post
 * @param array<string, mixed> $session
 * @return array{status: int, redirect: ?string, errors: array<string, string>, username: string, error_message: ?string}
 */
function handle_logout_submission(
    array $post,
    array &$session,
    ?callable $destroySession = null,
): array {
    if (!csrf_validate($session, post_string($post, 'csrf_token'))) {
        return [
            'status' => 403,
            'redirect' => null,
            'errors' => [],
            'username' => '',
            'error_message' => 'Invalid or missing CSRF token.',
        ];
    }

    auth_logout_user($session, $destroySession);

    return [
        'status' => 302,
        'redirect' => 'login',
        'errors' => [],
        'username' => '',
        'error_message' => null,
    ];
}
