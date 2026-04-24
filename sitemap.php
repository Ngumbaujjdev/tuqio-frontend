<?php
include 'config/config.php';
include 'libs/App.php';

header('Content-Type: application/xml; charset=UTF-8');

$base = 'https://tuqiohub.africa';
$today = date('Y-m-d');

$staticPages = [
    ['loc' => $base . '/',           'changefreq' => 'daily',   'priority' => '1.0', 'lastmod' => $today],
    ['loc' => $base . '/events',     'changefreq' => 'daily',   'priority' => '0.9', 'lastmod' => $today],
    ['loc' => $base . '/nominees',   'changefreq' => 'daily',   'priority' => '0.9', 'lastmod' => $today],
    ['loc' => $base . '/vote',       'changefreq' => 'daily',   'priority' => '0.8', 'lastmod' => $today],
    ['loc' => $base . '/nominate',   'changefreq' => 'weekly',  'priority' => '0.8', 'lastmod' => $today],
    ['loc' => $base . '/ticket',     'changefreq' => 'weekly',  'priority' => '0.8', 'lastmod' => $today],
    ['loc' => $base . '/pricing',    'changefreq' => 'monthly', 'priority' => '0.7', 'lastmod' => $today],
    ['loc' => $base . '/blog',       'changefreq' => 'weekly',  'priority' => '0.7', 'lastmod' => $today],
    ['loc' => $base . '/polls',      'changefreq' => 'weekly',  'priority' => '0.7', 'lastmod' => $today],
    ['loc' => $base . '/gallery',    'changefreq' => 'weekly',  'priority' => '0.6', 'lastmod' => $today],
    ['loc' => $base . '/about',      'changefreq' => 'monthly', 'priority' => '0.6', 'lastmod' => $today],
    ['loc' => $base . '/contact',    'changefreq' => 'monthly', 'priority' => '0.5', 'lastmod' => $today],
    ['loc' => $base . '/faq',        'changefreq' => 'monthly', 'priority' => '0.5', 'lastmod' => $today],
];

// Fetch dynamic events
$eventsResp = tuqio_api('/api/public/events');
$allEvents  = $eventsResp['data'] ?? [];

// Fetch dynamic blog posts
$blogResp = tuqio_api('/api/public/blog');
$allPosts = $blogResp['data'] ?? [];

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

// Static pages
foreach ($staticPages as $p) {
    echo "  <url>\n";
    echo "    <loc>" . htmlspecialchars($p['loc']) . "</loc>\n";
    echo "    <lastmod>" . $p['lastmod'] . "</lastmod>\n";
    echo "    <changefreq>" . $p['changefreq'] . "</changefreq>\n";
    echo "    <priority>" . $p['priority'] . "</priority>\n";
    echo "  </url>\n";
}

// Dynamic: event detail pages
foreach ($allEvents as $event) {
    $slug = $event['slug'] ?? '';
    if (!$slug) continue;
    $lastmod = !empty($event['updated_at']) ? date('Y-m-d', strtotime($event['updated_at'])) : $today;
    $url = $base . '/event-detail?slug=' . urlencode($slug);
    echo "  <url>\n";
    echo "    <loc>" . htmlspecialchars($url) . "</loc>\n";
    echo "    <lastmod>" . $lastmod . "</lastmod>\n";
    echo "    <changefreq>daily</changefreq>\n";
    echo "    <priority>0.9</priority>\n";
    echo "  </url>\n";
}

// Dynamic: blog post pages
foreach ($allPosts as $post) {
    $slug = $post['slug'] ?? '';
    if (!$slug) continue;
    $lastmod = !empty($post['published_at']) ? date('Y-m-d', strtotime($post['published_at']))
             : (!empty($post['created_at']) ? date('Y-m-d', strtotime($post['created_at'])) : $today);
    $url = $base . '/blog-single?slug=' . urlencode($slug);
    echo "  <url>\n";
    echo "    <loc>" . htmlspecialchars($url) . "</loc>\n";
    echo "    <lastmod>" . $lastmod . "</lastmod>\n";
    echo "    <changefreq>weekly</changefreq>\n";
    echo "    <priority>0.7</priority>\n";
    echo "  </url>\n";
}

echo '</urlset>';
