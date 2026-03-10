<?php
ob_start();

$isLocal = str_contains($_SERVER['HTTP_HOST'] ?? '', 'localhost');

// ─── Site ──────────────────────────────────────────────────────────────────
define("SITE_URL",    $isLocal
    ? "http://localhost/tuqio-frontend"
    : "https://tuqio.independentkenyawomenawards.com");
define("SITE_NAME",   "Tuqio Hub");
define("ADMIN_EMAIL", "tuqio@independentkenyawomenawards.com");
define("SITE_PHONE",  "+254757140682");

// ─── Social ────────────────────────────────────────────────────────────────
define("SOCIAL_INSTAGRAM", "https://www.instagram.com/tuqiohub");
define("SOCIAL_FACEBOOK",  "https://www.facebook.com/tuqiohub");
define("SOCIAL_TWITTER",   "https://twitter.com/tuqiohub");
define("SOCIAL_LINKEDIN",  "https://www.linkedin.com/company/tuqiohub");

// ─── OG image ──────────────────────────────────────────────────────────────
define("OG_IMAGE", "https://tuqio.independentkenyawomenawards.com/assets/images/og/tuqio-og.webp");

// ─── v1-backend API ────────────────────────────────────────────────────────
define("API_BASE",    $isLocal
    ? "http://localhost:8000"
    : "https://tuqio.independentkenyawomenawards.com");
define("API_STORAGE", $isLocal
    ? "http://localhost:8000/storage/"
    : "https://tuqio.independentkenyawomenawards.com/storage/");

// ─── Admin (organizer platform) ────────────────────────────────────────────
define("ADMIN_URL",   $isLocal
    ? "http://localhost:8000/login"
    : "https://tuqio.independentkenyawomenawards.com/platform/login");

// ─── Tuqio branding ────────────────────────────────────────────────────────
define("TUQIO_NAVY",  "#1e1548");
define("TUQIO_RED",   "#ed1c24");

// ─── API helper (GET) ──────────────────────────────────────────────────────
function tuqio_api(string $path): array {
    $url = API_BASE . $path;
    $ch  = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 6,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTPHEADER     => ['Accept: application/json'],
    ]);
    $body = curl_exec($ch);
    curl_close($ch);
    return json_decode($body ?: '[]', true) ?? [];
}

// ─── API helper (POST) ─────────────────────────────────────────────────────
function tuqio_api_post(string $path, array $data): array {
    $ch = curl_init(API_BASE . $path);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 8,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => json_encode($data),
        CURLOPT_HTTPHEADER     => ['Content-Type: application/json', 'Accept: application/json'],
    ]);
    $body   = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    $result = json_decode($body ?: '{}', true) ?? [];
    $result['_http_status'] = $status;
    return $result;
}
