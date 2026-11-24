<?php

declare(strict_types=1);

/**
 * @param array<string, mixed> $session
 */
function csrf_token(array &$session): string
{
    $existing = $session['csrf_token'] ?? null;
    if (is_string($existing) && $existing !== '') {
        return $existing;
    }

    return csrf_rotate($session);
}

/**
 * @param array<string, mixed> $session
 */
function csrf_rotate(array &$session): string
{
    $token = bin2hex(random_bytes(32));
    $session['csrf_token'] = $token;
    return $token;
}

/**
 * @param array<string, mixed> $session
 */
function csrf_validate(array $session, ?string $submitted): bool
{
    if (!is_string($submitted) || $submitted === '') {
        return false;
    }

    $stored = $session['csrf_token'] ?? null;
    if (!is_string($stored) || $stored === '') {
        return false;
    }

    return hash_equals($stored, $submitted);
}
