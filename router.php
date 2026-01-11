<?php
// Simple router for PHP built-in server
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$file = __DIR__ . $path;

// Serve static files directly
if (is_file($file)) {
    return false;
}

// Handle PHP files
if (preg_match('/\.php$/', $path)) {
    $phpFile = __DIR__ . $path;
    if (file_exists($phpFile)) {
        require $phpFile;
        exit;
    }
}

// Try .php extension
$phpFile = __DIR__ . $path . '.php';
if (file_exists($phpFile)) {
    require $phpFile;
    exit;
}

// Default to index.php
if (file_exists(__DIR__ . '/index.php')) {
    require __DIR__ . '/index.php';
    exit;
}

http_response_code(404);
echo '404 Not Found';

