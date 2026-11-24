<?php

declare(strict_types=1);

if (PHP_SAPI !== 'cli' && realpath((string) ($_SERVER['SCRIPT_FILENAME'] ?? '')) === realpath(__FILE__)) {
    http_response_code(403);
    header('Content-Type: text/plain; charset=UTF-8');
    exit('403 Forbidden');
}

/**
 * Resolve MySQL connection settings from an environment array or process env.
 *
 * @param array<string, mixed>|null $env Optional map for deterministic testing.
 * @return array{host: string, port: int, database: string, username: string, password: string}
 */
function db_config_from_env(?array $env = null): array
{
    $read = static function (string $key) use ($env): ?string {
        if ($env !== null) {
            if (!array_key_exists($key, $env) || $env[$key] === null || $env[$key] === '') {
                return null;
            }
            return (string) $env[$key];
        }

        $value = getenv($key);
        if ($value === false || $value === '') {
            return null;
        }
        return (string) $value;
    };

    $url = $read('MYSQL_URL') ?? $read('DATABASE_URL');
    if ($url !== null) {
        $parts = parse_url($url);
        if ($parts === false || !isset($parts['host'])) {
            throw new InvalidArgumentException('Invalid database connection URL.');
        }

        $database = isset($parts['path']) ? ltrim($parts['path'], '/') : '';

        return [
            'host' => $parts['host'],
            'port' => isset($parts['port']) ? (int) $parts['port'] : 3306,
            'database' => $database !== '' ? $database : 'demo',
            'username' => isset($parts['user']) ? rawurldecode($parts['user']) : 'root',
            'password' => isset($parts['pass']) ? rawurldecode($parts['pass']) : '',
        ];
    }

    $portRaw = $read('MYSQLPORT');
    $port = ($portRaw !== null && is_numeric($portRaw)) ? (int) $portRaw : 3306;

    return [
        'host' => $read('MYSQLHOST') ?? 'localhost',
        'port' => $port,
        'database' => $read('MYSQLDATABASE') ?? 'demo',
        'username' => $read('MYSQLUSER') ?? 'root',
        'password' => $read('MYSQLPASSWORD') ?? '',
    ];
}
