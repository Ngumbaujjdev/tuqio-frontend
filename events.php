<?php
include 'config/config.php';
include 'libs/App.php';

$resp      = tuqio_api('/api/public/events');
$allEvents = $resp['data'] ?? [];
$today     = date('Y-m-d');

$upcoming = [];
$past     = [];
foreach ($allEvents as $e) {
    $end = $e['end_date'] ?? $e['start_date'] ?? '';
    if ($end >= $today) { $upcoming[] = $e; }
    else                { $past[]     = $e; }
}
$imgFirst = fn($a, $b) =>
    (!empty($b['banner_image']) || !empty($b['thumbnail_image'])) <=> (!empty($a['banner_image']) || !empty($a['thumbnail_image']));
usort($upcoming, $imgFirst);
usort($past,     $imgFirst);

// Pass all data to JS
$jsData = json_encode([
    'upcoming' => array_values($upcoming),
    'past'     => array_values($past),
    'siteUrl'  => SITE_URL,
    'storage'  => API_STORAGE,
    'fallback' => SITE_URL . '/assets/slides/event.webp',
    'fallback2' => SITE_URL . '/assets/slides/event.webp',
], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT);

// For sidebar (PHP-rendered, no need to be dynamic)
$sidebarUpcoming = array_slice($upcoming, 0, 5);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">

<!-- SEO -->
<title>Upcoming Events | Tuqio Hub</title>
<meta name="description" content="Browse all upcoming and past events on Tuqio Hub — award ceremonies, conferences, and live experiences across Kenya.">
<meta name="keywords" content="Kenya events, upcoming events Nairobi, award ceremonies Kenya, conferences Kenya, Tuqio Hub events">
<meta name="author" content="Tuqio Hub">
<meta name="robots" content="index, follow">
<link rel="canonical" href="https://tuqiohub.africa/events.php">

<!-- Schema.org microdata -->
<meta itemprop="name" content="Upcoming Events | Tuqio Hub">
<meta itemprop="description" content="Browse all upcoming and past events on Tuqio Hub — award ceremonies, conferences, and live experiences across Kenya.">
<meta itemprop="image" content="<?= OG_IMAGE ?>">

<!-- Open Graph -->
<meta property="og:title" content="Upcoming Events | Tuqio Hub">
<meta property="og:type" content="website">
<meta property="og:image" content="<?= OG_IMAGE ?>">
<meta property="og:image:type" content="image/webp">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:url" content="https://tuqiohub.africa/events.php">
<meta property="og:description" content="Browse all upcoming and past events on Tuqio Hub — award ceremonies, conferences, and live experiences across Kenya.">
<meta property="og:site_name" content="Tuqio Hub">

<!-- Twitter Card -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:site" content="@tuqiohub">
<meta name="twitter:title" content="Upcoming Events | Tuqio Hub">
<meta name="twitter:description" content="Browse all upcoming and past events on Tuqio Hub — award ceremonies, conferences, and live experiences across Kenya.">
<meta name="twitter:image" content="<?= OG_IMAGE ?>">

<!-- Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-XXXXXXXXXX"></script>
<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','G-XXXXXXXXXX');</script>

<!-- JSON-LD: Organization -->
<script type="application/ld+json">
{"@context":"https://schema.org/","@type":"Organization","name":"Tuqio Hub","url":"https://tuqiohub.africa","description":"Kenya's premier event management and awards platform.","contactPoint":{"@type":"ContactPoint","telephone":"+254757140682","email":"info@tuqiohub.africa","contactType":"customer support"},"sameAs":["https://www.instagram.com/p/DV0RJ11ii-7/?igsh=MXNiemxwbXdzMzJ6aw==","https://www.facebook.com/share/p/1DJyLwtvqf/","https://twitter.com/tuqiohub","https://www.tiktok.com/@tuqiohubke"]}
</script>

<!-- JSON-LD: BreadcrumbList -->
<script type="application/ld+json">
{"@context":"https://schema.org","@type":"BreadcrumbList","itemListElement":[{"@type":"ListItem","position":1,"name":"Home","item":"https://tuqiohub.africa/"},{"@type":"ListItem","position":2,"name":"Events","item":"https://tuqiohub.africa/events.php"}]}
</script>

<!-- JSON-LD: WebPage -->
<script type="application/ld+json">
{"@context":"https://schema.org","@type":"WebPage","name":"Upcoming Events | Tuqio Hub","url":"https://tuqiohub.africa/events.php","description":"Browse all upcoming and past events on Tuqio Hub."}
</script>
<link href="<?= SITE_URL ?>/assets/css/bootstrap.min.css" rel="stylesheet">
<link href="<?= SITE_URL ?>/assets/css/style.css" rel="stylesheet">
<link href="<?= SITE_URL ?>/assets/css/responsive.css" rel="stylesheet">
<link href="<?= SITE_URL ?>/assets/css/custom.css" rel="stylesheet">
<link rel="icon" type="image/png" href="<?= SITE_URL ?>/assets/images/favicon/favicon-96x96.png" sizes="96x96">
<link rel="icon" type="image/svg+xml" href="<?= SITE_URL ?>/assets/images/favicon/favicon.svg">
<link rel="shortcut icon" href="<?= SITE_URL ?>/assets/images/favicon/favicon.ico">
<link rel="apple-touch-icon" sizes="180x180" href="<?= SITE_URL ?>/assets/images/favicon/apple-touch-icon.png">
<meta name="apple-mobile-web-app-title" content="Tuqio Hub">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
<style>
.event-block { margin-bottom: 40px; }
.event-block .inner-box {
    background: #fff; border-radius: 12px;
    box-shadow: 0 4px 25px rgba(0,0,0,0.07);
    overflow: hidden; display: flex; flex-direction: column;
    transition: box-shadow .3s;
}
.event-block:hover .inner-box { box-shadow: 0 10px 40px rgba(0,0,0,0.12); }
@media (min-width: 768px) {
    .event-block .inner-box { flex-direction: row; }
    .event-block .image-box { width: 300px; min-width: 300px; }
}
.event-block .image-box { position: relative; overflow: hidden; }
.event-block .image-box img {
    width: 100%; height: 100%; min-height: 210px;
    object-fit: cover; display: block; transition: transform .4s;
}
.event-block:hover .image-box img { transform: scale(1.04); }
.event-block .date-badge {
    position: absolute; top: 16px; left: 16px;
    background: #ed1c24; color: #fff; border-radius: 8px;
    padding: 8px 13px; text-align: center; line-height: 1.2;
    font-weight: 700; font-size: 1.1rem;
    box-shadow: 0 4px 12px rgba(237,28,36,0.4);
}
.event-block .date-badge span { display: block; font-size: .68rem; font-weight: 400; text-transform: uppercase; letter-spacing: 1px; }
.event-block .voting-badge {
    position: absolute; top: 16px; right: 16px;
    background: #1e1548; color: #fff; border-radius: 20px;
    padding: 4px 12px; font-size: .72rem; font-weight: 600;
    text-transform: uppercase; letter-spacing: 1px;
}
.event-block .format-badge {
    position: absolute; bottom: 14px; left: 14px;
    color: #fff; font-size: .68rem; font-weight: 700;
    letter-spacing: 1px; text-transform: uppercase;
    padding: 3px 10px; border-radius: 20px; z-index: 2;
}
.event-block .lower-content { padding: 24px 28px; display: flex; flex-direction: column; flex-grow: 1; }
.event-block .event-meta { display: flex; flex-wrap: wrap; gap: 14px; margin-bottom: 10px; }
.event-block .event-meta span { font-size: .82rem; color: #777; }
.event-block .event-meta span i { color: #ed1c24; margin-right: 4px; }
.event-block h3 { font-size: 1.3rem; color: #1e1548; margin-bottom: 8px; font-weight: 700; line-height: 1.3; }
.event-block h3 a { color: inherit; text-decoration: none; }
.event-block h3 a:hover { color: #ed1c24; }
.event-block .tagline { color: #555; font-size: .91rem; line-height: 1.65; margin-bottom: 18px; flex-grow: 1; }
.event-block .btn-row { display: flex; flex-wrap: wrap; gap: 10px; margin-top: auto; }

/* ── Filter + Search bar ── */
.events-controls { display: flex; flex-wrap: wrap; gap: 12px; align-items: center; margin-bottom: 28px; }
.filter-tabs { display: flex; gap: 8px; flex-shrink: 0; }
.filter-tab {
    padding: 9px 20px; border-radius: 6px; font-size: .87rem; font-weight: 600;
    border: 2px solid #eee; background: #fff; color: #555; cursor: pointer;
    transition: all .2s; white-space: nowrap;
}
.filter-tab.active, .filter-tab:hover { background: #ed1c24; border-color: #ed1c24; color: #fff; }
.events-search-wrap { flex-grow: 1; min-width: 180px; position: relative; }
.events-search-wrap input {
    width: 100%; padding: 10px 40px 10px 14px;
    border: 2px solid #eee; border-radius: 6px;
    font-size: .88rem; color: #333; outline: none;
    transition: border-color .2s;
}
.events-search-wrap input:focus { border-color: #ed1c24; }
.events-search-wrap .search-icon {
    position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
    color: #aaa; pointer-events: none;
}
.events-search-wrap .clear-search {
    position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
    color: #aaa; cursor: pointer; display: none; background: none; border: none; padding: 0;
    font-size: .95rem;
}

/* ── Pagination ── */
.events-pagination { display: flex; gap: 6px; align-items: center; justify-content: center; flex-wrap: wrap; margin-top: 10px; padding: 16px 0; }
.events-pagination button {
    min-width: 38px; height: 38px; padding: 0 10px;
    border: 2px solid #eee; border-radius: 6px;
    background: #fff; color: #555; font-size: .87rem; font-weight: 600;
    cursor: pointer; transition: all .2s;
}
.events-pagination button:hover { border-color: #ed1c24; color: #ed1c24; }
.events-pagination button.active { background: #ed1c24; border-color: #ed1c24; color: #fff; }
.events-pagination button:disabled { opacity: .38; cursor: default; pointer-events: none; }
.events-pagination .pg-dots { color: #aaa; padding: 0 4px; line-height: 38px; }

/* ── Empty state ── */
.events-empty { text-align: center; padding: 80px 0; }
.events-empty i { font-size: 3rem; color: #ed1c24; opacity: .35; }
.events-empty h4 { margin-top: 20px; color: #333; }
.events-empty p { color: #999; font-size: .9rem; }
</style>
</head>
<body>
<div class="page-wrapper">

<?php include 'includes/loader.php'; ?>
<header class="main-header header-style-two">
    <?php include 'includes/header-top.php'; ?>
    <?php include 'includes/nav.php'; ?>
    <?php include 'includes/sticky-header.php'; ?>
    <?php include 'includes/mobile-header.php'; ?>
    <?php include 'includes/search.php'; ?>
</header>
<div class="form-back-drop"></div>
<?php include 'includes/hidden-bar.php'; ?>

<!-- Page Title -->
<section class="page-title" style="background-image:url(<?= SITE_URL ?>/assets/slides/event.webp);">
    <div class="anim-icons full-width">
        <span class="icon icon-bull-eye"></span>
        <span class="icon icon-dotted-circle"></span>
    </div>
    <div class="auto-container">
        <div class="title-outer">
            <h1>Browse Events</h1>
            <ul class="page-breadcrumb">
                <li><a href="<?= SITE_URL ?>">Home</a></li>
                <li>Events</li>
            </ul>
        </div>
    </div>
</section>

<!-- Content -->
<div class="sidebar-page-container">
    <div class="auto-container">
        <div class="row clearfix">

            <!-- Content Side -->
            <div class="content-side col-lg-8 col-md-12 col-sm-12">

                <!-- Controls: filter tabs + search -->
                <div class="events-controls">
                    <div class="filter-tabs">
                        <button class="filter-tab active" data-filter="upcoming"
                                onclick="Events.setFilter('upcoming')">
                            Upcoming (<span id="count-upcoming"><?= count($upcoming) ?></span>)
                        </button>
                        <button class="filter-tab" data-filter="past"
                                onclick="Events.setFilter('past')">
                            Past (<span id="count-past"><?= count($past) ?></span>)
                        </button>
                    </div>
                    <div class="events-search-wrap">
                        <input type="text" id="events-search"
                               placeholder="Search events, venues…"
                               oninput="Events.onSearch(this.value)">
                        <i class="fas fa-search search-icon" id="search-icon"></i>
                        <button class="clear-search" id="clear-search" onclick="Events.clearSearch()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>

                <!-- Events list injected by JS -->
                <div id="events-list"></div>

                <!-- Pagination -->
                <div id="events-pagination" class="events-pagination"></div>

            </div>
            <!-- End Content Side -->

            <!-- Sidebar -->
            <div class="sidebar-side col-lg-4 col-md-12 col-sm-12">
                <aside class="sidebar padding-left">

                    <!-- Host CTA -->
                    <div class="sidebar-widget" style="margin-bottom:40px;">
                        <div style="background:linear-gradient(135deg,#1e1548,#ed1c24);border-radius:8px;padding:30px;text-align:center;color:#fff;">
                            <h5 style="margin-bottom:10px;">Host Your Event</h5>
                            <p style="font-size:.9rem;margin-bottom:20px;">Use Tuqio to manage nominations, voting, ticketing, and polls for your next event.</p>
                            <a href="<?= SITE_URL ?>/become-organizer" class="theme-btn btn-style-two"
                               style="background:#fff;color:#1e1548;border-color:#fff;">
                                <span class="btn-title">Get Started</span>
                            </a>
                        </div>
                    </div>

                    <!-- Upcoming Quick List (static from PHP) -->
                    <?php if (!empty($sidebarUpcoming)): ?>
                    <div class="sidebar-widget popular-posts">
                        <h5 class="sidebar-title">Upcoming Events</h5>
                        <div class="widget-content">
                            <?php foreach ($sidebarUpcoming as $e): ?>
                            <article class="post">
                                <div class="post-inner">
                                    <figure class="post-thumb" style="background:linear-gradient(135deg,#1e1548,#2d1f6b);height:110px;overflow:hidden;display:flex;align-items:center;justify-content:center;">
                                        <i class="fas fa-calendar-alt" style="font-size:1.8rem;color:rgba(255,255,255,0.22);flex-shrink:0;"></i>
                                        <?php
                                        $thumbSrc = !empty($e['banner_image']) ? API_STORAGE . $e['banner_image']
                                                  : (!empty($e['thumbnail_image']) ? API_STORAGE . $e['thumbnail_image'] : '');
                                        ?>
                                        <?php if ($thumbSrc): ?>
                                        <a href="<?= SITE_URL ?>/event-detail?slug=<?= urlencode($e['slug'] ?? '') ?>"
                                           style="position:absolute;inset:0;display:block;">
                                            <img src="<?= htmlspecialchars($thumbSrc) ?>"
                                                 alt="<?= htmlspecialchars($e['name'] ?? '') ?>"
                                                 style="width:100%;height:100%;object-fit:cover;display:block;"
                                                 onerror="this.onerror=null;this.parentNode.style.display='none';">
                                        </a>
                                        <?php else: ?>
                                        <a href="<?= SITE_URL ?>/event-detail?slug=<?= urlencode($e['slug'] ?? '') ?>"
                                           style="position:absolute;inset:0;display:block;"></a>
                                        <?php endif; ?>
                                    </figure>
                                    <div class="post-info"><i class="fa fa-calendar-alt"></i> <?= !empty($e['start_date']) ? date('d M Y', strtotime($e['start_date'])) : 'TBD' ?></div>
                                    <h6 class="text">
                                        <a href="<?= SITE_URL ?>/event-detail?slug=<?= urlencode($e['slug'] ?? '') ?>">
                                            <?= htmlspecialchars(mb_strimwidth($e['name'] ?? '', 0, 55, '…')) ?>
                                        </a>
                                    </h6>
                                </div>
                            </article>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Past Events -->
                    <?php if (!empty($past)): ?>
                    <div class="sidebar-widget categories">
                        <h5 class="sidebar-title">Past Events</h5>
                        <div class="widget-content">
                            <ul class="blog-categories">
                                <?php foreach ($past as $e): ?>
                                <li>
                                    <a href="<?= SITE_URL ?>/event-detail?slug=<?= urlencode($e['slug'] ?? '') ?>">
                                        <?= htmlspecialchars($e['name'] ?? '') ?>
                                        <span><?= !empty($e['start_date']) ? date('Y', strtotime($e['start_date'])) : '' ?></span>
                                    </a>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                    <?php endif; ?>

                </aside>
            </div>
            <!-- End Sidebar -->

        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
</div>

<div class="scroll-to-top scroll-to-target" data-target="html"><span class="fa fa-angle-up"></span></div>
<?php include 'includes/footer-links.php'; ?>

<script>
function tuqioImgErr(el) {
    el.onerror = null;
    el.parentElement.innerHTML = '<div style="height:100%;min-height:210px;background:linear-gradient(135deg,#1e1548,#2d1f6b);display:flex;align-items:center;justify-content:center;"><i class="fas fa-calendar-alt" style="font-size:3rem;color:rgba(255,255,255,0.2);"></i></div>';
}
(function () {
    var DATA   = <?= $jsData ?>;
    var PER_PAGE = 6;

    var state = {
        filter : 'upcoming',
        query  : '',
        page   : 1
    };

    // ── helpers ──────────────────────────────────────────────────────────────
    function esc(str) {
        return String(str || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }
    function fmtDate(d) {
        if (!d) return 'TBD';
        var parts = d.split('-');
        var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        return parts[2] + ' ' + months[parseInt(parts[1],10)-1] + ' ' + parts[0];
    }
    function fmtTime(t) {
        if (!t) return '';
        var parts = t.split(':');
        var h = parseInt(parts[0],10), m = parts[1], ampm = h >= 12 ? 'PM' : 'AM';
        h = h % 12 || 12;
        return h + ':' + m + ' ' + ampm;
    }
    function imgSrc(e) {
        if (e.banner_image)    return DATA.storage + e.banner_image;
        if (e.thumbnail_image) return DATA.storage + e.thumbnail_image;
        return null;
    }

    // ── card builder ─────────────────────────────────────────────────────────
    function buildCard(e) {
        var slug      = e.slug || '';
        var start     = e.start_date || '';
        var end       = e.end_date || start;
        var dayNum    = start ? start.split('-')[2] : '--';
        var month     = start ? ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'][parseInt(start.split('-')[1],10)-1] : '';
        var dateStr   = fmtDate(start);
        if (end && end !== start) dateStr += ' – ' + fmtDate(end);

        var isVoting  = !!e.voting_is_open;
        var isVirtual = !!e.is_virtual;
        var fmt       = e.event_format || 'in-person';
        var img       = imgSrc(e);
        var GRAD      = '<div style="height:100%;min-height:210px;background:linear-gradient(135deg,#1e1548,#2d1f6b);display:flex;align-items:center;justify-content:center;"><i class="fas fa-calendar-alt" style="font-size:3rem;color:rgba(255,255,255,0.2);"></i></div>';
        var link      = DATA.siteUrl + '/event-detail?slug=' + encodeURIComponent(slug);

        var fmtBadge = '';
        if (isVirtual) {
            fmtBadge = '<div class="format-badge" style="background:#1e1548;"><i class="fas fa-video" style="margin-right:4px;"></i>Virtual</div>';
        } else if (fmt === 'hybrid') {
            fmtBadge = '<div class="format-badge" style="background:#8b5cf6;"><i class="fas fa-layer-group" style="margin-right:4px;"></i>Hybrid</div>';
        }

        var votingBadge = isVoting
            ? '<div class="voting-badge"><i class="fas fa-vote-yea" style="margin-right:4px;"></i>Voting Open</div>'
            : '';

        var venueMeta = e.venue_city
            ? '<span><i class="fa fa-map-marker-alt"></i> ' + esc((e.venue_name ? e.venue_name + ', ' : '') + e.venue_city) + '</span>'
            : '';
        var timeMeta = e.start_time
            ? '<span><i class="fa fa-clock"></i> ' + fmtTime(e.start_time) + '</span>'
            : '';

        var ticketBtn = e.has_ticketing
            ? '<a href="' + DATA.siteUrl + '/checkout?slug=' + encodeURIComponent(slug) + '" class="theme-btn btn-style-two"><span class="btn-title"><i class="fas fa-ticket-alt" style="margin-right:4px;"></i>Get Tickets</span></a>'
            : '';
        var voteBtn = isVoting
            ? '<a href="' + DATA.siteUrl + '/nominees?event=' + encodeURIComponent(slug) + '" class="theme-btn btn-style-two" style="background:#ed1c24;border-color:#ed1c24;"><span class="btn-title"><i class="fas fa-vote-yea" style="margin-right:4px;"></i>Vote Now</span></a>'
            : '';

        return '<div class="event-block">' +
            '<div class="inner-box">' +
                '<div class="image-box">' +
                    '<div class="date-badge">' + esc(dayNum) + '<span>' + esc(month) + '</span></div>' +
                    votingBadge + fmtBadge +
                    (img
                        ? '<a href="' + link + '"><img src="' + esc(img) + '" alt="' + esc(e.name) + '" onerror="tuqioImgErr(this)"></a>'
                        : '<a href="' + link + '">' + GRAD + '</a>') +
                '</div>' +
                '<div class="lower-content">' +
                    '<div class="event-meta">' +
                        '<span><i class="fa fa-calendar-alt"></i> ' + esc(dateStr) + '</span>' +
                        venueMeta + timeMeta +
                    '</div>' +
                    '<h3><a href="' + link + '">' + esc(e.name || '') + '</a></h3>' +
                    '<p class="tagline">' + esc(e.tagline || e.short_description || '') + '</p>' +
                    '<div class="btn-row">' +
                        '<a href="' + link + '" class="theme-btn btn-style-one"><span class="btn-title">View Details</span></a>' +
                        ticketBtn + voteBtn +
                    '</div>' +
                '</div>' +
            '</div>' +
        '</div>';
    }

    // ── filter + search ───────────────────────────────────────────────────────
    function getFiltered() {
        var pool = DATA[state.filter] || [];
        if (!state.query) return pool;
        var q = state.query.toLowerCase();
        return pool.filter(function (e) {
            return (e.name           || '').toLowerCase().indexOf(q) > -1
                || (e.tagline        || '').toLowerCase().indexOf(q) > -1
                || (e.short_description || '').toLowerCase().indexOf(q) > -1
                || (e.venue_name     || '').toLowerCase().indexOf(q) > -1
                || (e.venue_city     || '').toLowerCase().indexOf(q) > -1;
        });
    }

    // ── pagination builder ───────────────────────────────────────────────────
    function buildPagination(total, current) {
        var pages = Math.ceil(total / PER_PAGE);
        if (pages <= 1) return '';
        var html = '';

        html += '<button onclick="Events.goPage(' + (current - 1) + ')" ' + (current <= 1 ? 'disabled' : '') + '><i class="fas fa-chevron-left"></i></button>';

        for (var i = 1; i <= pages; i++) {
            if (pages > 7 && i > 2 && i < pages - 1 && Math.abs(i - current) > 1) {
                if (i === 3 || i === pages - 2) html += '<span class="pg-dots">…</span>';
                continue;
            }
            html += '<button onclick="Events.goPage(' + i + ')" class="' + (i === current ? 'active' : '') + '">' + i + '</button>';
        }

        html += '<button onclick="Events.goPage(' + (current + 1) + ')" ' + (current >= pages ? 'disabled' : '') + '><i class="fas fa-chevron-right"></i></button>';
        return html;
    }

    // ── render ───────────────────────────────────────────────────────────────
    function render() {
        var filtered = getFiltered();
        var total    = filtered.length;
        var pages    = Math.ceil(total / PER_PAGE);
        if (state.page > pages) state.page = 1;

        var start    = (state.page - 1) * PER_PAGE;
        var slice    = filtered.slice(start, start + PER_PAGE);

        var listEl   = document.getElementById('events-list');
        var pgEl     = document.getElementById('events-pagination');

        if (slice.length === 0) {
            listEl.innerHTML = '<div class="events-empty">' +
                '<i class="fas fa-calendar-alt"></i>' +
                '<h4>' + (state.query ? 'No results for "' + esc(state.query) + '"' : 'No ' + (state.filter === 'past' ? 'Past' : 'Upcoming') + ' Events') + '</h4>' +
                '<p>' + (state.query ? 'Try a different search term.' : 'Check back soon for new events.') + '</p>' +
                '</div>';
            pgEl.innerHTML = '';
            return;
        }

        listEl.innerHTML = slice.map(buildCard).join('');
        pgEl.innerHTML   = buildPagination(total, state.page);
    }

    // ── public API ───────────────────────────────────────────────────────────
    var _searchTimer;

    window.Events = {
        setFilter: function (f) {
            state.filter = f;
            state.page   = 1;
            // Update tab buttons
            document.querySelectorAll('.filter-tab').forEach(function (btn) {
                btn.classList.toggle('active', btn.dataset.filter === f);
            });
            render();
        },
        onSearch: function (val) {
            clearTimeout(_searchTimer);
            _searchTimer = setTimeout(function () {
                state.query = val.trim();
                state.page  = 1;
                var clearBtn  = document.getElementById('clear-search');
                var searchIcon = document.getElementById('search-icon');
                if (val) {
                    clearBtn.style.display  = 'block';
                    searchIcon.style.display = 'none';
                } else {
                    clearBtn.style.display  = 'none';
                    searchIcon.style.display = 'block';
                }
                render();
            }, 280);
        },
        clearSearch: function () {
            var input = document.getElementById('events-search');
            input.value = '';
            Events.onSearch('');
            input.focus();
        },
        goPage: function (p) {
            state.page = p;
            render();
            // Scroll to top of list
            var el = document.getElementById('events-list');
            if (el) { el.scrollIntoView({ behavior: 'smooth', block: 'start' }); }
        }
    };

    // ── init ─────────────────────────────────────────────────────────────────
    // Respect ?filter=past from URL on initial load
    var urlParams = new URLSearchParams(window.location.search);
    var initFilter = urlParams.get('filter') === 'past' ? 'past' : 'upcoming';
    Events.setFilter(initFilter);

})();
</script>
</body>
</html>
