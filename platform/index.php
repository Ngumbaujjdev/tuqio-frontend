<?php
define('LARAVEL_START', microtime(true));

// ─── Detect Laravel root (production vs local) ────────────────────────────
$laravelRoot = file_exists('/home3/indepen3/tuqio.independentkenyawomenawards.com/vendor/autoload.php')
    ? '/home3/indepen3/tuqio.independentkenyawomenawards.com'
    : '/Applications/MAMP/htdocs/v1-events-backend';

// ─── Strip /platform prefix so Laravel sees clean routes ──────────────────
$uri  = $_SERVER['REQUEST_URI'] ?? '/';
$path = strtok($uri, '?');
if (str_starts_with($path, '/platform')) {
    $stripped = substr($path, strlen('/platform')) ?: '/';
    $query    = strstr($uri, '?') ?: '';
    $_SERVER['REQUEST_URI'] = $stripped . $query;
    $path = $stripped;
}

// ─── Stream storage files directly from Laravel storage ───────────────────
if (str_starts_with($path, '/storage/')) {
    $file = $laravelRoot . '/storage/app/public' . substr($path, strlen('/storage'));
    if (file_exists($file)) {
        $mime = mime_content_type($file) ?: 'application/octet-stream';
        header('Content-Type: ' . $mime);
        header('Content-Length: ' . filesize($file));
        header('Cache-Control: public, max-age=86400');
        readfile($file);
        exit;
    }
    http_response_code(404);
    exit;
}

// ─── Maintenance mode ─────────────────────────────────────────────────────
if (file_exists($m = $laravelRoot . '/storage/framework/maintenance.php')) {
    require $m;
}

// ─── Bootstrap Laravel ───────────────────────────────────────────────────
require $laravelRoot . '/vendor/autoload.php';
$app = require_once $laravelRoot . '/bootstrap/app.php';
$app->handleRequest(\Illuminate\Http\Request::capture());
