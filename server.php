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
    header('Content-Type: '.mime_content_type($publicPath));
    header('Content-Length: '.filesize($publicPath));

    readfile($publicPath);

    return;
}

require_once __DIR__.'/public/index.php';
