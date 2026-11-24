<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

$route = resolve_request_route((string) ($_SERVER['REQUEST_URI'] ?? '/'));

switch ($route) {
    case '/login':
        require __DIR__ . '/login.php';
        exit;
    case '/register':
        require __DIR__ . '/register.php';
        exit;
    case '/change-password':
        require __DIR__ . '/change-password.php';
        exit;
    case '/logout':
        require __DIR__ . '/logout.php';
        exit;
    case '/':
        break;
    default:
        http_response_code(404);
        echo render_error_page(404, 'The requested page could not be found.');
        exit;
}

session_init();

if (!auth_is_logged_in($_SESSION)) {
    send_redirect('login');
}

echo render_page('dashboard', [
    'title' => 'Welcome',
    'username' => auth_username($_SESSION) ?? '',
    'csrfToken' => csrf_token($_SESSION),
]);
