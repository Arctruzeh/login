<?php

declare(strict_types=1);

if (PHP_SAPI !== 'cli' && realpath((string) ($_SERVER['SCRIPT_FILENAME'] ?? '')) === realpath(__FILE__)) {
    http_response_code(403);
    header('Content-Type: text/plain; charset=UTF-8');
    exit('403 Forbidden');
}

/**
 * @param array<string, mixed> $post
 */
function post_string(array $post, string $key): string
{
    if (!array_key_exists($key, $post) || !is_scalar($post[$key])) {
        return '';
    }

    return (string) $post[$key];
}

/**
 * @return array<string, string>
 */
function validate_login_input(string $rawUsername, string $password): array
{
    $errors = [];
    $username = trim($rawUsername);

    if ($username === '') {
        $errors['username'] = 'Please enter username.';
    }

    if ($password === '') {
        $errors['password'] = 'Please enter your password.';
    }

    return $errors;
}

/**
 * @return array<string, string>
 */
function validate_registration_input(string $rawUsername, string $password, string $confirmPassword): array
{
    $errors = [];
    $username = trim($rawUsername);

    if ($username === '') {
        $errors['username'] = 'Please enter a username.';
    } elseif (strlen($username) > 50) {
        $errors['username'] = 'Username must be 50 characters or fewer.';
    }

    if ($password === '') {
        $errors['password'] = 'Please enter a password.';
    } elseif (strlen($password) < 6) {
        $errors['password'] = 'Password must have at least 6 characters.';
    }

    if ($confirmPassword === '') {
        $errors['confirm_password'] = 'Please confirm password.';
    } elseif (!isset($errors['password']) && $password !== $confirmPassword) {
        $errors['confirm_password'] = 'Passwords did not match.';
    }

    return $errors;
}

/**
 * @return array<string, string>
 */
function validate_password_change_input(string $newPassword, string $confirmPassword): array
{
    $errors = [];

    if ($newPassword === '') {
        $errors['new_password'] = 'Please enter the new password.';
    } elseif (strlen($newPassword) < 6) {
        $errors['new_password'] = 'Password must have at least 6 characters.';
    }

    if ($confirmPassword === '') {
        $errors['confirm_password'] = 'Please confirm the password.';
    } elseif (!isset($errors['new_password']) && $newPassword !== $confirmPassword) {
        $errors['confirm_password'] = 'Passwords did not match.';
    }

    return $errors;
}
