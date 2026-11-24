<?php

declare(strict_types=1);

if (PHP_SAPI !== 'cli' && realpath((string) ($_SERVER['SCRIPT_FILENAME'] ?? '')) === realpath(__FILE__)) {
    http_response_code(403);
    header('Content-Type: text/plain; charset=UTF-8');
    exit('403 Forbidden');
}

function resolve_request_route(string $requestUri): string
{
    $parsed = parse_url($requestUri, PHP_URL_PATH);
    $path = is_string($parsed) ? $parsed : '/';
    $normalized = '/' . trim($path, '/');

    if (str_ends_with($normalized, '.php')) {
        $normalized = substr($normalized, 0, -4);
        if ($normalized === '') {
            $normalized = '/';
        }
    }

    if ($normalized === '/index') {
        return '/';
    }

    return $normalized;
}
