<?php
if (PHP_SAPI !== 'cli' && realpath((string) ($_SERVER['SCRIPT_FILENAME'] ?? '')) === realpath(__FILE__)) {
    http_response_code(403);
    header('Content-Type: text/plain; charset=UTF-8');
    exit('403 Forbidden');
}
?><div class="wrapper text-center">
    <h2>Error <?= e($statusCode ?? 500) ?></h2>
    <p><?= e($message ?? 'Something went wrong.') ?></p>
    <p><a href="/" class="btn btn-primary">Back to Home</a></p>
</div>
