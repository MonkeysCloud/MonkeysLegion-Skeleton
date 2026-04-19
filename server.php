<?php
/**
 * PHP Built-in Server Router
 *
 * Usage: php -S localhost:8080 server.php
 *
 * This script tells PHP's built-in dev server to:
 *  1. Serve static files (CSS, JS, images) directly from public/
 *  2. Forward all other requests to public/index.php (the front controller)
 */

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/');

// If the request is for an actual static file in public/, serve it directly
$publicPath = __DIR__ . '/public' . $uri;
if ($uri !== '/' && is_file($publicPath)) {
    // Set correct MIME type for common file types
    $mimeTypes = [
        'css'  => 'text/css',
        'js'   => 'application/javascript',
        'json' => 'application/json',
        'svg'  => 'image/svg+xml',
        'png'  => 'image/png',
        'jpg'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif'  => 'image/gif',
        'webp' => 'image/webp',
        'ico'  => 'image/x-icon',
        'woff' => 'font/woff',
        'woff2'=> 'font/woff2',
        'ttf'  => 'font/ttf',
        'map'  => 'application/json',
    ];

    $ext = strtolower(pathinfo($publicPath, PATHINFO_EXTENSION));
    $mime = $mimeTypes[$ext] ?? mime_content_type($publicPath) ?: 'application/octet-stream';

    header('Content-Type: ' . $mime);
    header('Content-Length: ' . filesize($publicPath));
    readfile($publicPath);
    return;
}

// Otherwise, route through the framework's front controller
require __DIR__ . '/public/index.php';
