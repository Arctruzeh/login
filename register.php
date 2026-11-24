<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

session_init();

if (auth_is_logged_in($_SESSION)) {
    send_redirect('./');
}

$username = '';
$errors = [];

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $db = null;
    $getDb = static function () use (&$db): mysqli {
        if (!$db instanceof mysqli) {
            $db = db_connect();
        }
        return $db;
    };

    try {
        $result = handle_register_submission(
            $_POST,
            $_SESSION,
            static fn (string $user): ?array => db_find_user_by_username($getDb(), $user),
            static fn (string $user, string $hash): int => db_create_user($getDb(), $user, $hash),
        );
    } catch (Throwable $e) {
        error_log('Registration error: ' . $e->getMessage());
        http_response_code(500);
        echo render_error_page(500, 'An unexpected error occurred. Please try again later.');
        exit;
    } finally {
        if ($db instanceof mysqli) {
            mysqli_close($db);
        }
    }

    if ($result['status'] === 302 && $result['redirect'] !== null) {
        send_redirect($result['redirect']);
    }

    if ($result['status'] === 403) {
        http_response_code(403);
        echo render_error_page(403, $result['error_message'] ?? 'Forbidden');
        exit;
    }

    http_response_code($result['status']);
    $username = $result['username'];
    $errors = $result['errors'];
}

echo render_page('register', [
    'title' => 'Sign Up',
    'csrfToken' => csrf_token($_SESSION),
    'username' => $username,
    'errors' => $errors,
]);
