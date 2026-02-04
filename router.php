<?php
// router.php
if (php_sapi_name() == 'cli-server') {
    $url  = parse_url($_SERVER['REQUEST_URI']);
    $file = __DIR__ . $url['path'];

    // If the file exists physically, serve it.
    if (is_file($file)) {
        return false;
    }

    // Attempt to verify if the request matches a .php file
    $path = $url['path'];
    // Remove leading slash if necessary for some logic, but usually file_exists needs full path or relative.
    // Let's try appending .php
    $phpFile = __DIR__ . $path . '.php';
    
    if (is_file($phpFile)) {
        require $phpFile;
        return true; // We handled the request
    }
}
// Otherwise, let the server handle it (404)
return false;
?>
