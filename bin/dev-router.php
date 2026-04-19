#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * PHP built-in server router – project override.
 *
 * Serves static files from public/ explicitly (with correct MIME types)
 * and routes all other requests to public/index.php.
 */

$projectRoot = dirname(__DIR__);
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';

// ── Dev Hot Reload Endpoint ──────────────────────────────────
if ($uri === '/_dev/reload.json') {
    $marker = $projectRoot . '/var/cache/dev-reload.json';

    header('Content-Type: application/json');
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');

    if (!is_file($marker)) {
        echo json_encode(['version' => 0, 'status' => 'no_marker']);
        return true;
    }

    $content = @file_get_contents($marker);
    echo ($content !== false) ? $content : json_encode(['version' => 0, 'status' => 'read_error']);
    return true;
}

// ── Static Files ─────────────────────────────────────────────
$file = $projectRoot . '/public' . $uri;

if ($uri !== '/' && is_file($file)) {
    $mimeTypes = [
        'css'   => 'text/css',
        'js'    => 'application/javascript',
        'json'  => 'application/json',
        'svg'   => 'image/svg+xml',
        'png'   => 'image/png',
        'jpg'   => 'image/jpeg',
        'jpeg'  => 'image/jpeg',
        'gif'   => 'image/gif',
        'webp'  => 'image/webp',
        'ico'   => 'image/x-icon',
        'woff'  => 'font/woff',
        'woff2' => 'font/woff2',
        'ttf'   => 'font/ttf',
        'map'   => 'application/json',
    ];

    $ext  = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    $mime = $mimeTypes[$ext] ?? mime_content_type($file) ?: 'application/octet-stream';

    header('Content-Type: ' . $mime);
    header('Content-Length: ' . filesize($file));
    readfile($file);
    return;
}

// ── Front Controller ─────────────────────────────────────────
require $projectRoot . '/public/index.php';
