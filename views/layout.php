<?php
if (PHP_SAPI !== 'cli' && realpath((string) ($_SERVER['SCRIPT_FILENAME'] ?? '')) === realpath(__FILE__)) {
    http_response_code(403);
    header('Content-Type: text/plain; charset=UTF-8');
    exit('403 Forbidden');
}
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Login') ?></title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style>
        body {
            font: 14px sans-serif;
            background-color: #f8f9fa;
        }
        .wrapper {
            width: 360px;
            max-width: 100%;
            margin: 40px auto;
            padding: 24px;
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
        }
        .dashboard {
            width: 680px;
            max-width: 95%;
            margin: 40px auto;
            padding: 24px;
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            text-align: center;
        }
        .back-to-portfolio {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #222;
            color: #fff;
            padding: 10px 15px;
            border-radius: 5px;
            text-decoration: none;
            font-family: system-ui, sans-serif;
            font-size: 14px;
            z-index: 10000;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            transition: transform 0.2s;
        }
        .back-to-portfolio:hover,
        .back-to-portfolio:focus {
            color: #fff;
            text-decoration: none;
            transform: scale(1.05);
        }
    </style>
</head>
<body>
    <?= $content ?>
    <a href="https://ajlato.com/projects/login" class="back-to-portfolio">&larr; Back to Portfolio</a>
</body>
</html>
