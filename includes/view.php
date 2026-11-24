<?php

declare(strict_types=1);

if (PHP_SAPI !== 'cli' && realpath((string) ($_SERVER['SCRIPT_FILENAME'] ?? '')) === realpath(__FILE__)) {
    http_response_code(403);
    header('Content-Type: text/plain; charset=UTF-8');
    exit('403 Forbidden');
}

function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * @param array<string, mixed> $data
 */
function render_page(string $template, array $data = []): string
{
    $viewsDir = __DIR__ . '/../views';
    $templatePath = $viewsDir . DIRECTORY_SEPARATOR . $template . '.php';
    $layoutPath = $viewsDir . DIRECTORY_SEPARATOR . 'layout.php';

    if (!is_file($templatePath)) {
        throw new RuntimeException("View template not found: {$template}");
    }

    $renderFile = static function (string $__path, array $__vars): string {
        extract($__vars, EXTR_SKIP);
        ob_start();
        try {
            require $__path;
            $output = ob_get_clean();
            return $output === false ? '' : $output;
        } catch (Throwable $e) {
            ob_end_clean();
            throw $e;
        }
    };

    $content = $renderFile($templatePath, $data);

    return $renderFile($layoutPath, array_merge($data, ['content' => $content]));
}

function render_error_page(int $statusCode, string $message): string
{
    return render_page('error', [
        'title' => 'Error ' . $statusCode,
        'statusCode' => $statusCode,
        'message' => $message,
    ]);
}

function send_redirect(string $location): never
{
    header('Location: ' . $location, true, 302);
    exit;
}
