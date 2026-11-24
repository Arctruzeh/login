<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    echo render_error_page(405, 'Method not allowed.');
    exit;
}

session_init();

$result = handle_logout_submission($_POST, $_SESSION, 'session_destroy_native');

if ($result['status'] === 403) {
    http_response_code(403);
    echo render_error_page(403, $result['error_message'] ?? 'Forbidden');
    exit;
}

send_redirect($result['redirect'] ?? 'login');
