<?php

declare(strict_types=1);

if (PHP_SAPI === 'cli-server') {
    $uriPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    $path = is_string($uriPath) ? $uriPath : '/';

    $blockedPrefixes = ['/includes', '/views', '/tests', '/vendor'];
    $blockedExact = ['/composer.json', '/composer.lock', '/phpunit.xml', '/database.sql', '/README.md'];

    foreach ($blockedPrefixes as $prefix) {
        if ($path === $prefix || str_starts_with($path, $prefix . '/')) {
            http_response_code(403);
            require_once __DIR__ . '/vendor/autoload.php';
            echo render_error_page(403, 'Forbidden');
            return true;
        }
    }

    if (in_array($path, $blockedExact, true)) {
        http_response_code(403);
        require_once __DIR__ . '/vendor/autoload.php';
        echo render_error_page(403, 'Forbidden');
        return true;
    }

    $normalized = trim($path, '/');
    if (str_ends_with($normalized, '.php')) {
        $normalized = substr($normalized, 0, -4);
    }
    if ($normalized === '') {
        $normalized = 'index';
    }

    $allowedEntrypoints = ['index', 'login', 'register', 'change-password', 'logout'];
    if (in_array($normalized, $allowedEntrypoints, true)) {
        $target = __DIR__ . '/' . $normalized . '.php';
        if (is_file($target)) {
            require $target;
            return true;
        }
    }

    http_response_code(404);
    require_once __DIR__ . '/vendor/autoload.php';
    echo render_error_page(404, 'The requested page could not be found.');
    return true;
}

return false;
