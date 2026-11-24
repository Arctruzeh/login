<?php

declare(strict_types=1);

/**
 * Open a MySQLi connection using strict exception reporting and utf8mb4 charset.
 *
 * @param array{host: string, port: int, database: string, username: string, password: string}|null $config
 */
function db_connect(?array $config = null): mysqli
{
    $cfg = $config ?? db_config_from_env();

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    $db = mysqli_connect(
        $cfg['host'],
        $cfg['username'],
        $cfg['password'],
        $cfg['database'],
        $cfg['port']
    );

    if (!$db instanceof mysqli) {
        throw new RuntimeException('Failed to connect to MySQL database.');
    }

    mysqli_set_charset($db, 'utf8mb4');

    return $db;
}

/**
 * @return array{id: int, username: string, password: string}|null
 */
function db_find_user_by_username(mysqli $db, string $username): ?array
{
    $stmt = mysqli_prepare($db, 'SELECT id, username, password FROM users WHERE username = ? LIMIT 1');
    if ($stmt === false) {
        throw new RuntimeException('Failed to prepare user lookup statement.');
    }

    try {
        mysqli_stmt_bind_param($stmt, 's', $username);
        mysqli_stmt_execute($stmt);

        $id = 0;
        $foundUsername = '';
        $passwordHash = '';
        mysqli_stmt_bind_result($stmt, $id, $foundUsername, $passwordHash);

        if (mysqli_stmt_fetch($stmt) === true) {
            return [
                'id' => (int) $id,
                'username' => (string) $foundUsername,
                'password' => (string) $passwordHash,
            ];
        }

        return null;
    } finally {
        mysqli_stmt_close($stmt);
    }
}

function db_create_user(mysqli $db, string $username, string $passwordHash): int
{
    $stmt = mysqli_prepare($db, 'INSERT INTO users (username, password) VALUES (?, ?)');
    if ($stmt === false) {
        throw new RuntimeException('Failed to prepare user insert statement.');
    }

    try {
        mysqli_stmt_bind_param($stmt, 'ss', $username, $passwordHash);
        mysqli_stmt_execute($stmt);
        return (int) mysqli_insert_id($db);
    } finally {
        mysqli_stmt_close($stmt);
    }
}

function db_update_user_password(mysqli $db, int $userId, string $passwordHash): bool
{
    $stmt = mysqli_prepare($db, 'UPDATE users SET password = ? WHERE id = ?');
    if ($stmt === false) {
        throw new RuntimeException('Failed to prepare password update statement.');
    }

    try {
        mysqli_stmt_bind_param($stmt, 'si', $passwordHash, $userId);
        mysqli_stmt_execute($stmt);
        return true;
    } finally {
        mysqli_stmt_close($stmt);
    }
}
