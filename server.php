<?php

/**
 * Vercel serverless entrypoint for Laravel.
 *
 * Serves static files from the public/ directory (Vite build output, images,
 * etc.) directly, and forwards every other request to the Laravel front
 * controller. The assets are bundled into the function during the build,
 * which is why we resolve them from disk instead of relying on the platform.
 */

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? ''
);

$publicPath = __DIR__.'/public'.$uri;

if ($uri !== '/' && is_file($publicPath)) {
    $ext = strtolower(pathinfo($publicPath, PATHINFO_EXTENSION));

    $mimeTypes = [
        'js' => 'text/javascript; charset=UTF-8',
        'mjs' => 'text/javascript; charset=UTF-8',
        'css' => 'text/css; charset=UTF-8',
        'json' => 'application/json; charset=UTF-8',
        'map' => 'application/json; charset=UTF-8',
        'webmanifest' => 'application/manifest+json; charset=UTF-8',
        'svg' => 'image/svg+xml',
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif' => 'image/gif',
        'webp' => 'image/webp',
        'ico' => 'image/x-icon',
        'woff' => 'font/woff',
        'woff2' => 'font/woff2',
        'ttf' => 'font/ttf',
        'otf' => 'font/otf',
        'txt' => 'text/plain; charset=UTF-8',
        'xml' => 'application/xml; charset=UTF-8',
        'pdf' => 'application/pdf',
        'html' => 'text/html; charset=UTF-8',
    ];

    $type = $mimeTypes[$ext] ?? mime_content_type($publicPath) ?: 'application/octet-stream';

    header('Content-Type: '.$type);
    header('Content-Length: '.filesize($publicPath));

    readfile($publicPath);

    return;
}

require_once __DIR__.'/public/index.php';
