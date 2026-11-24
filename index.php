<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

session_init();

if (!auth_is_logged_in($_SESSION)) {
    send_redirect('login');
}

echo render_page('dashboard', [
    'title' => 'Welcome',
    'username' => auth_username($_SESSION) ?? '',
    'csrfToken' => csrf_token($_SESSION),
]);
