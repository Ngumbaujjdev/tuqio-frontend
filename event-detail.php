<?php
include 'config/config.php';
include 'libs/App.php';

$slug = trim($_GET['slug'] ?? '');
if (!$slug) { header('Location: ' . SITE_URL . '/events'); exit; }

$resp = tuqio_api('/api/public/events/' . urlencode($slug));
if (empty($resp['event'])) { header('Location: ' . SITE_URL . '/events'); exit; }

$event        = $resp['event'];
$scheduleDays = $resp['schedule_days'] ?? [];
$speakers     = $resp['speakers']      ?? [];
$sponsors     = $resp['sponsors']      ?? [];
$faqs         = $resp['faqs']          ?? [];
$ticketTypes  = $resp['ticket_types']  ?? [];

// Gallery (separate call)
$galleryResp = tuqio_api('/api/public/events/' . urlencode($slug) . '/gallery');
$gallery     = $galleryResp['photos'] ?? [];

// Nominees/categories
$nomResp    = tuqio_api('/api/public/events/' . urlencode($slug) . '/nominees');
$categories = $nomResp['categories'] ?? [];

// ── Dates & times ────────────────────────────────────────────────────────────
$now          = time();
$start        = $event['start_date'] ?? '';
$end          = $event['end_date']   ?? $start;
$dateStr      = $start ? date('d M Y', strtotime($start)) : 'TBD';
if ($end && $end !== $start) $dateStr .= ' – ' . date('d M Y', strtotime($end));

$banner    = !empty($event['banner_image'])    ? API_STORAGE . $event['banner_image']    : (SITE_URL . '/assets/slides/event.webp');
$thumbnail = !empty($event['thumbnail_image']) ? API_STORAGE . $event['thumbnail_image'] : $banner;

// ── Lifecycle flags ──────────────────────────────────────────────────────────
$phase           = $event['current_phase'] ?? '';
$isVotingOpen    = !empty($event['voting_is_open']);
$votingOpensTs   = !empty($event['voting_opens_at'])        ? strtotime($event['voting_opens_at'])        : 0;
$votingClosesTs  = !empty($event['voting_closes_at'])       ? strtotime($event['voting_closes_at'])       : 0;
$votingNotYet    = !empty($event['has_voting']) && $votingOpensTs  && $votingOpensTs  > $now;
$votingClosed    = !empty($event['has_voting']) && $votingClosesTs && $votingClosesTs < $now && !$isVotingOpen;

$hasRegistration = !empty($event['has_registration']);
$regOpensTs      = !empty($event['registration_opens_at'])  ? strtotime($event['registration_opens_at'])  : 0;
$regClosesTs     = !empty($event['registration_closes_at']) ? strtotime($event['registration_closes_at']) : 0;
$isRegOpen       = $hasRegistration && $regOpensTs && $regClosesTs && $now >= $regOpensTs && $now <= $regClosesTs;
$regNotYet       = $hasRegistration && $regOpensTs && $now < $regOpensTs;
$regClosed       = $hasRegistration && $regClosesTs && $now > $regClosesTs && !$isRegOpen;

$hasTicketing    = !empty($event['has_ticketing']);
$hasNominations  = !empty($event['has_nominations']);
$isVirtual       = !empty($event['is_virtual']);
$virtualUrl      = $event['virtual_url'] ?? '';
$eventFormat     = $event['event_format'] ?? 'in-person';
$maxAttendees    = $event['max_attendees'] ?? null;

// ── Sponsor tiers ────────────────────────────────────────────────────────────
$sponsorsByTier = [];
foreach ($sponsors as $s) { $sponsorsByTier[$s['tier'] ?? 'partner'][] = $s; }

// ── Initials colors palette ──────────────────────────────────────────────────
$initialsColors = ['#ed1c24', '#1e1548', '#2d1f6b', '#6c757d'];
$globalIdx = 0;

// ── Phase badge ──────────────────────────────────────────────────────────────
$phaseBadge = [
    'voting'     => ['Voting Open',       '#ed1c24'],
    'on_sale'    => ['Tickets On Sale',   '#10b981'],
    'nomination' => ['Nominations Open',  '#f59e0b'],
    'review'     => ['Under Review',      '#8b5cf6'],
    'event_day'  => ['Event Day!',        '#8b5cf6'],
    'results'    => ['Results Out',       '#f59e0b'],
    'ended'      => ['Ended',             '#6c757d'],
    'published'  => ['Coming Soon',       '#6366f1'],
][$phase] ?? ['Upcoming', '#6c757d'];

// ── Stepper: build steps based on event features ─────────────────────────────
// Phase order for "done" calculation
$phaseOrder = ['published'=>1,'nomination'=>2,'review'=>3,'on_sale'=>4,'voting'=>5,'event_day'=>6,'results'=>7,'ended'=>8];
$currentOrder = $phaseOrder[$phase] ?? 0;

function stepState(string $stepPhase, int $currentOrder, array $phaseOrder): string {
    $so = $phaseOrder[$stepPhase] ?? 99;
    if ($currentOrder > $so) return 'done';
    if ($currentOrder === $so) return 'active';
    return 'locked';
}

$steps = [['label'=>'Published','phase'=>'published','icon'=>'fa-check']];
if ($hasNominations) {
    $steps[] = ['label'=>'Nominations','phase'=>'nomination','icon'=>'fa-pen-nib'];
    $steps[] = ['label'=>'Finalists','phase'=>'review','icon'=>'fa-users'];
}
if ($hasTicketing) {
    $steps[] = ['label'=>'Tickets','phase'=>'on_sale','icon'=>'fa-ticket-alt'];
}
if (!empty($event['has_voting'])) {
    $steps[] = ['label'=>'Voting','phase'=>'voting','icon'=>'fa-vote-yea'];
}
$steps[] = ['label'=>'Event Day','phase'=>'event_day','icon'=>'fa-calendar-star'];
$steps[] = ['label'=>'Results','phase'=>'results','icon'=>'fa-trophy'];

// ── Type icons for schedule ──────────────────────────────────────────────────
$typeIcons = [
    'keynote'     => 'fa-microphone',
    'panel'       => 'fa-users',
    'workshop'    => 'fa-tools',
    'ceremony'    => 'fa-award',
    'performance' => 'fa-music',
    'networking'  => 'fa-handshake',
    'break'       => 'fa-coffee',
    'session'     => 'fa-circle',
];
$ordinals = ['','1st','2nd','3rd','4th','5th','6th','7th','8th','9th','10th'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">

<?php
$seoTitle = htmlspecialchars($event['name'] ?? 'Event');
$seoDesc  = htmlspecialchars(strip_tags($event['tagline'] ?? $event['short_description'] ?? 'Discover event details, schedule, tickets, nominees and more on Tuqio Hub.'));
$seoDesc  = mb_strimwidth($seoDesc, 0, 160, '...');
$seoSlug  = urlencode($slug);
$seoImg   = !empty($event['banner_image']) ? API_STORAGE . $event['banner_image'] : OG_IMAGE;
$seoUrl   = 'https://tuqiohub.africa/event-detail.php?slug=' . $seoSlug;
?>

<!-- SEO -->
<title><?= $seoTitle ?> | Tuqio Hub</title>
<meta name="description" content="<?= $seoDesc ?>">
<meta name="keywords" content="<?= $seoTitle ?>, Kenya event, Tuqio Hub, event schedule, tickets Kenya, nominations">
<meta name="author" content="Tuqio Hub">
<meta name="robots" content="index, follow">
<link rel="canonical" href="<?= $seoUrl ?>">

<!-- Schema.org microdata -->
<meta itemprop="name" content="<?= $seoTitle ?> | Tuqio Hub">
<meta itemprop="description" content="<?= $seoDesc ?>">
<meta itemprop="image" content="<?= $seoImg ?>">

<!-- Open Graph -->
<meta property="og:title" content="<?= $seoTitle ?> | Tuqio Hub">
<meta property="og:type" content="website">
<meta property="og:image" content="<?= $seoImg ?>">
<meta property="og:image:type" content="image/jpeg">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:url" content="<?= $seoUrl ?>">
<meta property="og:description" content="<?= $seoDesc ?>">
<meta property="og:site_name" content="Tuqio Hub">

<!-- Twitter Card -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:site" content="@tuqiohub">
<meta name="twitter:title" content="<?= $seoTitle ?> | Tuqio Hub">
<meta name="twitter:description" content="<?= $seoDesc ?>">
<meta name="twitter:image" content="<?= $seoImg ?>">

<!-- Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-XXXXXXXXXX"></script>
<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','G-XXXXXXXXXX');</script>

<!-- JSON-LD: Organization -->
<script type="application/ld+json">
{"@context":"https://schema.org/","@type":"Organization","name":"Tuqio Hub","url":"https://tuqiohub.africa","contactPoint":{"@type":"ContactPoint","telephone":"+254757140682","email":"info@tuqiohub.africa","contactType":"customer support"},"sameAs":["https://www.facebook.com/share/p/1DJyLwtvqf/","https://www.instagram.com/p/DV0RJ11ii-7/?igsh=MXNiemxwbXdzMzJ6aw==","https://twitter.com/tuqiohub","https://www.tiktok.com/@tuqiohubke"]}
</script>

<!-- JSON-LD: BreadcrumbList -->
<script type="application/ld+json">
{"@context":"https://schema.org","@type":"BreadcrumbList","itemListElement":[{"@type":"ListItem","position":1,"name":"Home","item":"https://tuqiohub.africa/"},{"@type":"ListItem","position":2,"name":"Events","item":"https://tuqiohub.africa/events.php"},{"@type":"ListItem","position":3,"name":"<?= addslashes($event['name'] ?? '') ?>","item":"<?= $seoUrl ?>"}]}
</script>

<!-- JSON-LD: Event -->
<script type="application/ld+json">
{"@context":"https://schema.org","@type":"Event","name":"<?= addslashes($event['name'] ?? '') ?>","url":"<?= $seoUrl ?>","description":"<?= addslashes($seoDesc) ?>","image":"<?= $seoImg ?>","startDate":"<?= $event['start_date'] ?? '' ?>","endDate":"<?= $event['end_date'] ?? $event['start_date'] ?? '' ?>","eventStatus":"https://schema.org/EventScheduled","eventAttendanceMode":"https://schema.org/OfflineEventAttendanceMode","location":{"@type":"Place","name":"<?= addslashes($event['venue_name'] ?? 'Nairobi, Kenya') ?>","address":{"@type":"PostalAddress","addressLocality":"<?= addslashes($event['venue_city'] ?? 'Nairobi') ?>","addressCountry":"KE"}},"organizer":{"@type":"Organization","name":"Tuqio Hub","url":"https://tuqiohub.africa"}}
</script>
<link href="<?= SITE_URL ?>/assets/css/bootstrap.min.css" rel="stylesheet">
<link href="<?= SITE_URL ?>/assets/css/style.css" rel="stylesheet">
<link href="<?= SITE_URL ?>/assets/css/responsive.css" rel="stylesheet">
<link href="<?= SITE_URL ?>/assets/css/custom.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.css">
<link rel="icon" type="image/png" href="<?= SITE_URL ?>/assets/images/favicon/favicon-96x96.png" sizes="96x96">
<link rel="icon" type="image/svg+xml" href="<?= SITE_URL ?>/assets/images/favicon/favicon.svg">
<link rel="shortcut icon" href="<?= SITE_URL ?>/assets/images/favicon/favicon.ico">
<meta name="apple-mobile-web-app-title" content="Tuqio Hub">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<style>
/* ── Nominees tab controls ─────────────────────────────── */
.nom-controls {
    display: flex; flex-wrap: wrap; gap: 12px;
    align-items: center; margin-bottom: 28px;
    padding: 16px 18px; background: #f9fafb;
    border-radius: 10px; border: 1px solid #eee;
}
.nom-search-wrap { flex-grow: 1; min-width: 180px; position: relative; }
.nom-search-wrap input {
    width: 100%; padding: 10px 38px 10px 14px;
    border: 2px solid #e5e7eb; border-radius: 8px;
    font-size: .88rem; color: #333; outline: none;
    transition: border-color .2s;
}
.nom-search-wrap input:focus { border-color: #ed1c24; }
.nom-search-wrap .nom-search-icon {
    position: absolute; right: 12px; top: 50%;
    transform: translateY(-50%); color: #aaa; pointer-events: none;
}
.select2-container--default .select2-selection--single {
    height: 42px; border: 2px solid #e5e7eb;
    border-radius: 8px; display: flex; align-items: center;
}
.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 42px; color: #333; font-size: .88rem; padding-left: 12px;
}
.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 42px;
}
.select2-container--default.select2-container--focus .select2-selection--single {
    border-color: #ed1c24;
}
/* ── Nominee card ──────────────────────────────────────── */
.nom-card {
    background: #fff; border: 1px solid #f0f0f0;
    border-radius: 12px; padding: 18px 14px; text-align: center;
    transition: box-shadow .25s, transform .25s;
    height: 100%; display: flex; flex-direction: column;
    align-items: center; cursor: pointer;
}
.nom-card:hover { box-shadow: 0 8px 28px rgba(0,0,0,.1); transform: translateY(-2px); }
.nom-avatar {
    width: 60px; height: 60px; border-radius: 50%;
    object-fit: cover; display: block; margin: 0 auto 12px;
    border: 2px solid #f0f0f0;
}
.nom-initials {
    width: 60px; height: 60px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-weight: 800; font-size: 1rem; color: #fff;
    margin: 0 auto 12px; flex-shrink: 0;
}
.nom-name {
    font-size: .88rem; font-weight: 700; color: #1e1548;
    margin-bottom: 4px; line-height: 1.3;
}
.nom-subtitle { font-size: .76rem; color: #ed1c24; margin-bottom: 4px; line-height: 1.3; }
.nom-votes { font-size: .72rem; color: #aaa; }
.nom-winner-badge {
    display: inline-block; font-size: .65rem; background: #f59e0b;
    color: #fff; padding: 2px 8px; border-radius: 20px;
    font-weight: 700; margin-bottom: 6px;
}
/* ── Category section ─────────────────────────────────── */
.nom-cat-section { margin-bottom: 36px; }
.nom-cat-header {
    display: flex; align-items: baseline; gap: 10px;
    border-bottom: 2px solid #f0f0f0; padding-bottom: 10px;
    margin-bottom: 18px;
}
.nom-cat-title { font-size: 1.05rem; font-weight: 700; color: #1e1548; margin: 0; }
.nom-cat-count { font-size: .78rem; color: #aaa; font-weight: 400; }
/* ── No results state ─────────────────────────────────── */
.nom-no-results { text-align: center; padding: 40px 0; color: #aaa; display: none; }
.nom-no-results i { font-size: 2.5rem; opacity: .3; }

/* ── Select2 brand overrides ───────────────────────────── */
.select2-container--default .select2-results__option--highlighted {
    background-color: #1e1548 !important;
    color: #fff !important;
}
.select2-container--default .select2-search--dropdown .select2-search__field:focus {
    border-color: #ed1c24;
    outline: none;
}
.select2-dropdown {
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    box-shadow: 0 8px 24px rgba(0,0,0,.08);
}
.select2-container--default .select2-results__option--selected {
    background: rgba(30,21,72,.06);
    color: #1e1548;
    font-weight: 600;
}
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

<!-- ── Page Title / Breadcrumb ───────────────────────────────────────────── -->
<section class="page-title" style="background-color:#1e1548;">
    <div class="auto-container">
        <div class="title-outer">
            <h1><?= htmlspecialchars($event['name']) ?></h1>
            <ul class="page-breadcrumb">
                <li><a href="<?= SITE_URL ?>/">Home</a></li>
                <li><a href="<?= SITE_URL ?>/events">Events</a></li>
                <li><?= htmlspecialchars($event['name']) ?></li>
            </ul>
        </div>
    </div>
</section>

<!-- ── Voting Strip ─────────────────────────────────────────────────────── -->
<?php if ($isVotingOpen): ?>
<div class="voting-strip">
    <span style="font-size:.95rem;font-weight:600;"><i class="fas fa-vote-yea me-2"></i> Voting is open! Cast your vote now.</span>
    <a href="<?= SITE_URL ?>/nominees?event=<?= urlencode($slug) ?>"
       class="theme-btn btn-style-two"
       style="background:#fff;color:#ed1c24;border-color:#fff;font-size:.82rem;padding:8px 22px;">
        <span class="btn-title">Vote for Nominees</span>
    </a>
</div>
<?php endif; ?>

<!-- ── Event Lifecycle Stepper ──────────────────────────────────────────── -->
<div class="event-stepper">
    <div class="auto-container">
        <div class="stepper-inner">
            <?php foreach ($steps as $si => $step):
                $state = stepState($step['phase'], $currentOrder, $phaseOrder);
                $isLast = $si === count($steps) - 1;
            ?>
            <div class="stepper-step">
                <div class="stepper-item">
                    <div class="stepper-circle <?= $state ?>">
                        <?php if ($state === 'done'): ?>
                        <i class="fas fa-check" style="font-size:.75rem;"></i>
                        <?php elseif ($state === 'active'): ?>
                        <i class="fas <?= $step['icon'] ?>" style="font-size:.75rem;"></i>
                        <?php else: ?>
                        <i class="fas fa-lock" style="font-size:.7rem;"></i>
                        <?php endif; ?>
                    </div>
                    <div class="stepper-label <?= $state ?>"><?= $step['label'] ?></div>
                </div>
                <?php if (!$isLast): ?>
                <div class="stepper-line <?= $state === 'done' ? 'done' : '' ?>"></div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- ── Main Content ─────────────────────────────────────────────────────── -->
<div class="sidebar-page-container" style="padding-top:40px;">
    <div class="auto-container">
        <div class="row">

            <!-- Content -->
            <div class="content-side col-lg-8 col-md-12 col-sm-12">

                <!-- Event featured image -->
                <div style="margin-bottom:24px;border-radius:12px;overflow:hidden;position:relative;">
                    <img src="<?= htmlspecialchars($banner) ?>"
                         alt="<?= htmlspecialchars($event['name']) ?>"
                         style="width:100%;max-height:420px;object-fit:cover;display:block;"
                         onerror="this.outerHTML='<div style=\'width:100%;height:420px;border-radius:12px;background:linear-gradient(135deg,#1e1548 0%,#2d1f6b 60%,#ed1c24 100%);display:flex;flex-direction:column;align-items:center;justify-content:center;gap:12px;\'><i class=\'fas fa-ticket-alt\' style=\'font-size:3rem;color:rgba(255,255,255,.35);\'></i><span style=\'color:rgba(255,255,255,.5);font-size:.85rem;letter-spacing:1px;text-transform:uppercase;\'>Tuqio Hub</span></div>'">
                    <div style="position:absolute;top:16px;left:16px;">
                        <span style="background:<?= $phaseBadge[1] ?>;color:#fff;font-size:.72rem;font-weight:700;padding:5px 14px;border-radius:20px;text-transform:uppercase;letter-spacing:.8px;"><?= $phaseBadge[0] ?></span>
                    </div>
                </div>

                <!-- Event meta row -->
                <div style="display:flex;flex-wrap:wrap;gap:14px;margin-bottom:22px;align-items:center;">
                    <span style="font-size:.88rem;color:#555;"><i class="fa fa-calendar-alt" style="color:#ed1c24;margin-right:6px;"></i><?= $dateStr ?></span>
                    <?php if (!empty($event['start_time'])): ?>
                    <span style="font-size:.88rem;color:#555;"><i class="fa fa-clock" style="color:#ed1c24;margin-right:6px;"></i><?= date('h:i A', strtotime($event['start_time'])) ?><?= !empty($event['end_time']) ? ' – ' . date('h:i A', strtotime($event['end_time'])) : '' ?></span>
                    <?php endif; ?>
                    <?php if (!empty($event['venue_name'])): ?>
                    <span style="font-size:.88rem;color:#555;"><i class="fa fa-map-marker-alt" style="color:#ed1c24;margin-right:6px;"></i><?= htmlspecialchars($event['venue_name'] . ($event['venue_city'] ? ', ' . $event['venue_city'] : '')) ?></span>
                    <?php endif; ?>
                    <?php if ($isVirtual): ?>
                    <span style="font-size:.88rem;color:#555;"><i class="fa fa-video" style="color:#ed1c24;margin-right:6px;"></i>Virtual Event</span>
                    <?php elseif ($eventFormat === 'hybrid'): ?>
                    <span style="font-size:.88rem;color:#555;"><i class="fa fa-layer-group" style="color:#ed1c24;margin-right:6px;"></i>Hybrid Event</span>
                    <?php endif; ?>
                </div>

                <!-- Nomination / Voting action strip (shown above tabs when phase is active) -->
                <?php if ($hasNominations && !in_array($phase, ['review', 'results', 'ended'])): ?>
                <div style="display:flex;align-items:flex-start;gap:14px;background:linear-gradient(135deg,#fffbeb,#fef3c7);border:2px solid #f59e0b;border-radius:12px;padding:20px 24px;margin-bottom:22px;">
                    <i class="fas fa-pen-nib" style="font-size:1.6rem;color:#f59e0b;margin-top:2px;flex-shrink:0;"></i>
                    <div style="flex:1;">
                        <strong style="font-size:1rem;color:#92400e;display:block;margin-bottom:4px;">Nominations are open for this event!</strong>
                        <p style="font-size:.88rem;color:#78350f;margin:0 0 14px;">Know someone deserving of recognition? Submit a nomination now before the window closes.</p>
                        <a href="<?= SITE_URL ?>/nominate.php?event=<?= urlencode($slug) ?>" class="theme-btn btn-style-one" style="background:#f59e0b;border-color:#f59e0b;font-size:.85rem;padding:9px 22px;">
                            <span class="btn-title"><i class="fas fa-pen-nib me-1"></i> Nominate Someone</span>
                        </a>
                    </div>
                </div>
                <?php endif; ?>

                <?php if ($isVotingOpen): ?>
                <div style="display:flex;align-items:flex-start;gap:14px;background:linear-gradient(135deg,#fff1f2,#ffe4e6);border:2px solid #ed1c24;border-radius:12px;padding:20px 24px;margin-bottom:22px;">
                    <i class="fas fa-vote-yea" style="font-size:1.6rem;color:#ed1c24;margin-top:2px;flex-shrink:0;"></i>
                    <div style="flex:1;">
                        <strong style="font-size:1rem;color:#9f1239;display:block;margin-bottom:4px;">Voting is live — cast your vote now!</strong>
                        <p style="font-size:.88rem;color:#881337;margin:0 0 14px;">Voting closes <?= $votingClosesTs ? date('d M Y 	 h:i A', $votingClosesTs) : 'soon' ?>. Every vote counts.</p>
                        <a href="<?= SITE_URL ?>/nominees.php?event=<?= urlencode($slug) ?>" class="theme-btn btn-style-one" style="font-size:.85rem;padding:9px 22px;">
                            <span class="btn-title"><i class="fas fa-vote-yea me-1"></i> Go Vote Now</span>
                        </a>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Tabs nav -->
                <ul class="nav event-tabs mb-4" id="eventTabs" style="border-bottom:2px solid #f0f0f0;flex-wrap:wrap;gap:0;">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#tab-about">About</a>
                    </li>
                    <?php if (!empty($scheduleDays)): ?>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#tab-schedule">Schedule</a>
                    </li>
                    <?php endif; ?>
                    <?php if (!empty($speakers)): ?>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#tab-speakers">Speakers</a>
                    </li>
                    <?php endif; ?>
                    <?php if (!empty($gallery)): ?>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#tab-gallery">Gallery</a>
                    </li>
                    <?php endif; ?>
                    <?php if (!empty($event['has_voting']) || $hasNominations || !empty($categories)): ?>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#tab-nominees">
                            <?= $hasNominations && !$isVotingOpen ? 'Nominations' : 'Nominees & Voting' ?>
                        </a>
                    </li>
                    <?php endif; ?>
                    <?php if ($hasTicketing): ?>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#tab-tickets">Tickets</a>
                    </li>
                    <?php endif; ?>
                    <?php if (!empty($faqs)): ?>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#tab-faq">FAQ</a>
                    </li>
                    <?php endif; ?>
                </ul>

                <div class="tab-content">

                    <!-- ── About tab ───────────────────────────────────────── -->
                    <div class="tab-pane fade show active" id="tab-about">
                        <?php if (!empty($event['description'])): ?>
                        <div style="font-size:.95rem;line-height:1.85;color:#444;">
                            <?= nl2br(htmlspecialchars($event['description'])) ?>
                        </div>
                        <?php elseif (!empty($event['short_description'])): ?>
                        <p style="font-size:.95rem;line-height:1.85;color:#444;">
                            <?= htmlspecialchars($event['short_description']) ?>
                        </p>
                        <?php else: ?>
                        <p class="text-muted">Event details coming soon.</p>
                        <?php endif; ?>

                        <?php if (!empty($event['terms_conditions'])): ?>
                        <div style="margin-top:28px;background:#f9fafb;border-left:4px solid #e0e0e0;padding:16px 20px;border-radius:0 8px 8px 0;">
                            <h6 style="color:#1e1548;font-weight:700;margin-bottom:8px;"><i class="fas fa-file-alt me-2" style="color:#aaa;"></i>Terms & Conditions</h6>
                            <div style="font-size:.82rem;color:#666;line-height:1.7;"><?= nl2br(htmlspecialchars($event['terms_conditions'])) ?></div>
                        </div>
                        <?php endif; ?>

                        <?php if (!empty($sponsors)): ?>
                        <div style="margin-top:40px;">
                            <h5 style="font-weight:700;color:#1e1548;margin-bottom:20px;">Our Sponsors &amp; Partners</h5>
                            <?php foreach ($sponsorsByTier as $tier => $tierSponsors): ?>
                            <div class="tier-label"><?= htmlspecialchars(ucfirst($tier)) ?></div>
                            <div class="row mb-4">
                                <?php foreach ($tierSponsors as $sp): ?>
                                <div class="col-sm-4 col-6 mb-3">
                                    <?php if (!empty($sp['website'])): ?>
                                    <a href="<?= htmlspecialchars($sp['website']) ?>" target="_blank" class="sponsor-logo">
                                    <?php else: ?><div class="sponsor-logo"><?php endif; ?>
                                        <?php if (!empty($sp['logo'])): ?>
                                        <img src="<?= htmlspecialchars($sp['logo']) ?>" alt="<?= htmlspecialchars($sp['name']) ?>"
                                             onerror="this.style.display='none';this.nextElementSibling.style.display='block';">
                                        <span class="sp-name" style="display:none;"><?= htmlspecialchars($sp['name']) ?></span>
                                        <?php else: ?>
                                        <span class="sp-name"><?= htmlspecialchars($sp['name']) ?></span>
                                        <?php endif; ?>
                                    <?php if (!empty($sp['website'])): ?></a><?php else: ?></div><?php endif; ?>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- ── Schedule tab (Volia .schedule-block-two) ────────── -->
                    <?php if (!empty($scheduleDays)): ?>
                    <div class="tab-pane fade" id="tab-schedule">
                        <div class="schedule-tabs tabs-box">
                            <?php if (count($scheduleDays) > 1): ?>
                            <div class="btns-box" style="margin-bottom:28px;">
                                <ul class="tab-buttons clearfix">
                                    <?php foreach ($scheduleDays as $di => $day):
                                        $ord  = $ordinals[min($di+1, count($ordinals)-1)] ?? ($di+1).'th';
                                        $dNum = !empty($day['date']) ? date('j', strtotime($day['date'])) : ($di+1);
                                        $dMon = !empty($day['date']) ? date('M', strtotime($day['date'])) : '';
                                        $dYr  = !empty($day['date']) ? date('Y', strtotime($day['date'])) : '';
                                    ?>
                                    <li class="tab-btn <?= $di===0?'active-btn':'' ?>" data-tab="#sday-<?= $di ?>">
                                        <span class="day"><?= $ord ?> Day</span>
                                        <div class="date-box">
                                            <span class="date"><?= $dNum ?></span>
                                            <span class="month"><span class="colored"><?= $dMon ?></span> <?= $dYr ?></span>
                                        </div>
                                    </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            <?php endif; ?>

                            <div class="tabs-content">
                                <?php foreach ($scheduleDays as $di => $day): ?>
                                <div class="tab <?= $di===0?'active-tab':'' ?>" id="sday-<?= $di ?>">
                                    <?php if (count($scheduleDays) === 1 && !empty($day['date'])): ?>
                                    <div class="schedule-day-header-custom" style="margin-bottom:20px;">
                                        <h6><i class="fas fa-calendar-day me-2" style="color:#ed1c24;"></i><?= date('l, d F Y', strtotime($day['date'])) ?></h6>
                                    </div>
                                    <?php endif; ?>
                                    <div class="schedule-timeline">
                                        <?php foreach ($day['sessions'] as $si => $session):
                                            $icon = $typeIcons[$session['type'] ?? ''] ?? 'fa-microphone';
                                            $isEven = ($si % 2 === 1);
                                            $timeStr = '';
                                            if (!empty($session['start_time'])) {
                                                $timeStr = date('g:i A', strtotime($session['start_time']));
                                                if (!empty($session['end_time'])) $timeStr .= ' <br/>' . date('g:i A', strtotime($session['end_time']));
                                            }
                                        ?>
                                        <div class="schedule-block <?= $isEven ? 'even' : '' ?>">
                                            <div class="inner-box">
                                                <div class="inner">
                                                    <div class="date">
                                                        <span><?= $timeStr ?: 'TBD' ?></span>
                                                    </div>
                                                    <div class="speaker-info">
                                                        <?php if (!empty($session['speaker_photo'])): ?>
                                                        <figure class="thumb">
                                                            <img src="<?= htmlspecialchars($session['speaker_photo']) ?>" alt=""
                                                                 onerror="this.style.display='none';">
                                                        </figure>
                                                        <?php endif; ?>
                                                        <span class="icon fa <?= $icon ?>"></span>
                                                        <?php if (!empty($session['speaker'])): ?>
                                                        <h5 class="name"><?= htmlspecialchars($session['speaker']) ?></h5>
                                                        <span class="designation"><?= htmlspecialchars($session['speaker_title'] ?? '') ?></span>
                                                        <?php else: ?>
                                                        <h5 class="name"><?= htmlspecialchars(ucfirst($session['type'] ?? 'Session')) ?></h5>
                                                        <?php if (!empty($session['location'])): ?>
                                                        <span class="designation"><i class="fas fa-map-marker-alt" style="margin-right:4px;"></i><?= htmlspecialchars($session['location']) ?></span>
                                                        <?php endif; ?>
                                                        <?php endif; ?>
                                                    </div>
                                                    <h4><a href="#"><?= htmlspecialchars($session['title']) ?><?= !empty($session['is_highlighted']) ? ' <i class="fas fa-star" style="color:#f59e0b;font-size:.75rem;"></i>' : '' ?></a></h4>
                                                    <?php if (!empty($session['description'])): ?>
                                                    <p style="font-size:.83rem;color:#aaa;margin:6px 0 0;line-height:1.6;"><?= htmlspecialchars($session['description']) ?></p>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- ── Speakers tab ────────────────────────────────────── -->
                    <?php if (!empty($speakers)): ?>
                    <div class="tab-pane fade" id="tab-speakers">
                        <div class="row">
                            <?php foreach ($speakers as $sp):
                                $words = array_filter(explode(' ', trim($sp['name'] ?? '')));
                                $spInitials = implode('', array_map(fn($w) => strtoupper($w[0] ?? ''), array_slice($words, 0, 2)));
                            ?>
                            <div class="col-md-4 col-sm-6 mb-4">
                                <div class="speaker-card">
                                    <?php if (!empty($sp['photo'])): ?>
                                    <img src="<?= htmlspecialchars($sp['photo']) ?>" alt="<?= htmlspecialchars($sp['name']) ?>"
                                         onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                                    <div class="speaker-initials" style="display:none;"><?= $spInitials ?></div>
                                    <?php else: ?>
                                    <div class="speaker-initials"><?= $spInitials ?></div>
                                    <?php endif; ?>
                                    <h6><?= htmlspecialchars($sp['name']) ?></h6>
                                    <?php if (!empty($sp['title'])): ?>
                                    <div class="sp-title"><?= htmlspecialchars($sp['title']) ?></div>
                                    <?php endif; ?>
                                    <?php if (!empty($sp['company'])): ?>
                                    <div class="sp-company"><?= htmlspecialchars($sp['company']) ?></div>
                                    <?php endif; ?>
                                    <?php if (!empty($sp['is_featured'])): ?>
                                    <div style="margin-top:8px;">
                                        <span style="font-size:.7rem;background:rgba(237,28,36,0.1);color:#ed1c24;padding:2px 8px;border-radius:20px;font-weight:700;">Featured</span>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- ── Gallery tab ────────────────────────────────────── -->
                    <?php if (!empty($gallery)): ?>
                    <div class="tab-pane fade" id="tab-gallery">
                        <div class="gallery-grid">
                            <?php foreach ($gallery as $photo): ?>
                            <a href="<?= htmlspecialchars($photo['photo']) ?>"
                               data-fancybox="event-gallery"
                               data-caption="<?= htmlspecialchars($photo['title'] ?? '') ?>">
                                <img src="<?= htmlspecialchars($photo['photo']) ?>"
                                     alt="<?= htmlspecialchars($photo['alt'] ?? $photo['title'] ?? '') ?>"
                                     onerror="this.style.display='none'">
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- ── Nominees & Voting tab ───────────────────────────── -->
                    <?php if (!empty($event['has_voting']) || $hasNominations || !empty($categories)): ?>
                    <div class="tab-pane fade" id="tab-nominees">

                        <?php // ── Phase-aware banner ──────────────────────── ?>

                        <?php if ($phase === 'nomination' || ($hasNominations && empty($categories))): ?>
                        <div class="nom-phase-banner nominations">
                            <i class="fas fa-pen-nib" style="color:#f59e0b;font-size:1.4rem;margin-top:2px;flex-shrink:0;"></i>
                            <div>
                                <strong style="color:#b45309;display:block;margin-bottom:4px;">Nominations are open!</strong>
                                <span style="font-size:.88rem;color:#666;">Know someone who deserves recognition? Nominate them for this event.</span>
                                <div style="margin-top:12px;">
                                    <a href="<?= SITE_URL ?>/nominate.php?event=<?= urlencode($slug) ?>"
                                       class="theme-btn btn-style-one" style="font-size:.82rem;padding:8px 20px;background:#f59e0b;border-color:#f59e0b;">
                                        <span class="btn-title"><i class="fas fa-pen-nib me-1"></i> Nominate Someone</span>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <?php elseif ($phase === 'review'): ?>
                        <div class="nom-phase-banner review">
                            <i class="fas fa-search" style="color:#8b5cf6;font-size:1.4rem;margin-top:2px;flex-shrink:0;"></i>
                            <div>
                                <strong style="color:#6d28d9;display:block;margin-bottom:4px;">Finalists under review</strong>
                                <span style="font-size:.88rem;color:#666;">Nominations have closed. Our team is reviewing submissions — finalists will be announced shortly.</span>
                            </div>
                        </div>

                        <?php elseif ($isVotingOpen): ?>
                        <div class="nom-phase-banner voting-open">
                            <i class="fas fa-vote-yea" style="color:#ed1c24;font-size:1.4rem;margin-top:2px;flex-shrink:0;"></i>
                            <div>
                                <strong style="color:#ed1c24;display:block;margin-bottom:4px;">Voting is open!</strong>
                                <span style="font-size:.88rem;color:#666;">Cast your vote for your favourite nominees. Voting closes <?= $votingClosesTs ? date('d M Y \a\t h:i A', $votingClosesTs) : '' ?>.</span>
                                <div style="margin-top:12px;">
                                    <a href="<?= SITE_URL ?>/nominees?event=<?= urlencode($slug) ?>"
                                       class="theme-btn btn-style-one" style="font-size:.82rem;padding:8px 20px;">
                                        <span class="btn-title"><i class="fas fa-vote-yea me-1"></i> Go to Full Voting Page</span>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <?php elseif ($votingNotYet && !empty($event['has_voting'])): ?>
                        <div class="nom-phase-banner" style="background:rgba(99,102,241,.07);border:1px solid rgba(99,102,241,.25);">
                            <i class="fas fa-lock" style="color:#6366f1;font-size:1.4rem;margin-top:2px;flex-shrink:0;"></i>
                            <div>
                                <strong style="color:#4338ca;display:block;margin-bottom:4px;">Voting not yet open</strong>
                                <span style="font-size:.88rem;color:#666;">Voting opens <?= $votingOpensTs ? date('d M Y \a\t h:i A', $votingOpensTs) : 'soon' ?>. Check back then to cast your vote.</span>
                            </div>
                        </div>

                        <?php elseif ($votingClosed): ?>
                        <div class="nom-phase-banner voting-closed">
                            <i class="fas fa-flag-checkered" style="color:#6c757d;font-size:1.4rem;margin-top:2px;flex-shrink:0;"></i>
                            <div>
                                <strong style="color:#555;display:block;margin-bottom:4px;">Voting has closed</strong>
                                <span style="font-size:.88rem;color:#888;">Thank you to everyone who voted! Results will be announced at the event.</span>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php // ── Category list + search controls ──────────── ?>
                        <?php if (!empty($categories)): ?>

                        <?php // Build Select2 options ?>
                        <?php $catOptions = []; foreach ($categories as $cat) { $catOptions[] = ['id' => 'cat-sec-' . ($cat['id'] ?? ''), 'text' => $cat['name'] ?? '']; } ?>

                        <!-- Controls: jump + search -->
                        <div class="nom-controls">
                            <?php if (count($categories) > 1): ?>
                            <div style="min-width:200px;flex-shrink:0;">
                                <select id="nom-cat-jump" style="width:100%;">
                                    <option value="all">All Categories</option>
                                    <?php foreach ($categories as $cat):
                                        $cCount = count($cat['nominees'] ?? $cat['candidates'] ?? []);
                                    ?>
                                    <option value="cat-sec-<?= $cat['id'] ?? '' ?>">
                                        <?= htmlspecialchars($cat['name']) ?> (<?= $cCount ?>)
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <?php endif; ?>
                            <div class="nom-search-wrap">
                                <input type="text" id="nom-search" placeholder="Search nominees by name…" autocomplete="off">
                                <i class="fas fa-search nom-search-icon"></i>
                            </div>
                        </div>

                        <!-- No results state -->
                        <div class="nom-no-results" id="nom-no-results">
                            <i class="fas fa-users-slash"></i>
                            <h5 style="margin-top:14px;color:#999;">No nominees match your search</h5>
                            <p style="font-size:.88rem;">Try a different keyword.</p>
                        </div>

                        <!-- Category sections -->
                        <div id="nom-categories-wrap">
                        <?php foreach ($categories as $cat):
                            $cands    = $cat['nominees'] ?? $cat['candidates'] ?? [];
                            $catId    = (int)($cat['id'] ?? 0);
                            $catNomType   = $cat['nomination_type']   ?? '';
                            $catNomStatus = $cat['nomination_status'] ?? '';
                            $showNominateCta = $hasNominations
                                && $catNomStatus === 'collecting'
                                && $catNomType !== 'admin_only'
                                && !in_array($phase, ['review', 'results', 'ended']);
                        ?>
                        <div class="nom-cat-section" id="cat-sec-<?= $catId ?>">

                            <!-- Category header -->
                            <div class="nom-cat-header">
                                <h6 class="nom-cat-title"><?= htmlspecialchars($cat['name']) ?></h6>
                                <span class="nom-cat-count"><?= count($cands) ?> <?= count($cands) === 1 ? 'nominee' : 'nominees' ?></span>
                            </div>
                            <?php if (!empty($cat['description'])): ?>
                            <p style="font-size:.85rem;color:#666;margin-bottom:16px;"><?= htmlspecialchars($cat['description']) ?></p>
                            <?php endif; ?>

                            <?php if (!empty($cands)): ?>
                            <!-- Nominee grid — ALL nominees, no cap -->
                            <div class="row" id="cat-grid-<?= $catId ?>">
                                <?php foreach ($cands as $c):
                                    $cName = $c['name'] ?? '';
                                    $wds   = array_filter(explode(' ', trim($cName)));
                                    $ini   = implode('', array_map(fn($w) => strtoupper($w[0] ?? ''), array_slice($wds, 0, 2)));
                                    $col   = $initialsColors[$globalIdx % 4];
                                    $globalIdx++;
                                    $imgSrc = $c['image'] ?? $c['thumbnail'] ?? '';
                                ?>
                                <div class="col-lg-3 col-md-4 col-6 mb-3 nom-card-col"
                                     data-nom-id="<?= $c['id'] ?? '' ?>"
                                     data-nom-name="<?= htmlspecialchars($cName) ?>"
                                     data-nom-subtitle="<?= htmlspecialchars($c['subtitle'] ?? '') ?>"
                                     data-nom-description="<?= htmlspecialchars($c['description'] ?? '') ?>"
                                     data-nom-image="<?= htmlspecialchars($imgSrc) ?>"
                                     data-nom-initials="<?= htmlspecialchars($ini) ?>"
                                     data-nom-color="<?= htmlspecialchars($col) ?>"
                                     data-nom-votes="<?= $c['votes_count'] ?? 0 ?>"
                                     data-nom-winner="<?= !empty($c['is_winner']) ? '1' : '0' ?>"
                                     data-nom-winner-pos="<?= $c['winner_position'] ?? '' ?>"
                                     data-nom-cat="<?= htmlspecialchars($cat['name'] ?? '') ?>"
                                     data-nom-code="<?= htmlspecialchars($c['code'] ?? '') ?>"
                                     data-nom-email="<?= htmlspecialchars($c['contact_email'] ?? '') ?>"
                                     data-nom-phone="<?= htmlspecialchars($c['contact_phone'] ?? '') ?>"
                                     data-nom-video="<?= htmlspecialchars($c['video_url'] ?? '') ?>"
                                     data-nom-socials="<?= htmlspecialchars(json_encode($c['social_links'] ?? [])) ?>"
                                     data-name="<?= strtolower(htmlspecialchars($cName)) ?>"
                                     data-subtitle="<?= strtolower(htmlspecialchars($c['subtitle'] ?? '')) ?>">
                                    <div class="nom-card">
                                        <?php if (!empty($c['is_winner'])): ?>
                                        <div class="nom-winner-badge"><i class="fas fa-trophy me-1"></i> Winner</div>
                                        <?php endif; ?>

                                        <?php if ($imgSrc): ?>
                                        <img src="<?= htmlspecialchars($imgSrc) ?>" alt="<?= htmlspecialchars($cName) ?>"
                                             class="nom-avatar"
                                             onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                                        <div class="nom-initials" style="background:<?= $col ?>;display:none;"><?= $ini ?></div>
                                        <?php else: ?>
                                        <div class="nom-initials" style="background:<?= $col ?>;"><?= $ini ?></div>
                                        <?php endif; ?>

                                        <div class="nom-name"><?= htmlspecialchars($cName) ?></div>
                                        <?php if (!empty($c['subtitle'])): ?>
                                        <div class="nom-subtitle"><?= htmlspecialchars($c['subtitle']) ?></div>
                                        <?php endif; ?>
                                        <?php if (!empty($c['votes_count'])): ?>
                                        <div class="nom-votes"><i class="fas fa-vote-yea me-1"></i><?= number_format($c['votes_count']) ?> votes</div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>

                            <?php elseif ($phase === 'nomination' || ($hasNominations && $catNomStatus === 'collecting' && $catNomType !== 'admin_only')): ?>
                            <div style="font-size:.85rem;color:#aaa;padding:8px 0 14px;">No nominees yet — be the first to nominate!</div>
                            <?php endif; ?>

                            <!-- Nominate CTAs per category -->
                            <?php if ($showNominateCta): ?>
                            <div style="margin-top:14px;display:flex;gap:10px;flex-wrap:wrap;">
                                <a href="<?= SITE_URL ?>/nominate.php?event=<?= urlencode($slug) ?>&category=<?= $catId ?>"
                                   style="display:inline-flex;align-items:center;gap:7px;padding:9px 20px;border-radius:8px;background:#1e1548;color:#fff;font-size:.82rem;font-weight:700;text-decoration:none;" onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                                    <i class="fas fa-user-plus"></i> Nominate Someone
                                </a>
                                <a href="<?= SITE_URL ?>/nominate.php?event=<?= urlencode($slug) ?>&category=<?= $catId ?>&mode=self"
                                   style="display:inline-flex;align-items:center;gap:7px;padding:9px 20px;border-radius:8px;border:2px solid #1e1548;background:transparent;color:#1e1548;font-size:.82rem;font-weight:700;text-decoration:none;" onmouseover="this.style.opacity='.7'" onmouseout="this.style.opacity='1'">
                                    <i class="fas fa-user"></i> Nominate Myself
                                </a>
                            </div>
                            <?php endif; ?>

                        </div><!-- end nom-cat-section -->
                        <?php endforeach; ?>
                        </div><!-- end nom-categories-wrap -->

                        <?php elseif (!$hasNominations && !empty($event['has_voting']) && !$votingClosed && !$isVotingOpen): ?>
                        <div style="text-align:center;padding:60px 0;color:#aaa;">
                            <i class="fas fa-users" style="font-size:2.5rem;opacity:.3;"></i>
                            <h5 style="margin-top:16px;color:#999;">Nominees will be announced soon</h5>
                            <p style="font-size:.88rem;">Check back when voting opens.</p>
                        </div>
                        <?php endif; ?>

                    </div>
                    <?php endif; ?>

                    <!-- ── Tickets tab ────────────────────────────────────── -->

                    <?php if ($hasTicketing): ?>
                    <div class="tab-pane fade" id="tab-tickets">
                        <?php if ($phase !== 'on_sale' && empty($ticketTypes)): ?>
                        <div class="locked-cta" style="max-width:460px;margin:20px auto;">
                            <i class="fas fa-ticket-alt"></i>
                            <div class="locked-title">Tickets Not Yet On Sale</div>
                            <?php if ($regOpensTs || $votingOpensTs): ?>
                            <div class="locked-sub">Tickets will go on sale closer to the event date. Check back soon.</div>
                            <?php else: ?>
                            <div class="locked-sub">Ticket sales have not started. Stay tuned.</div>
                            <?php endif; ?>
                        </div>
                        <?php elseif (!empty($ticketTypes)): ?>
                        <p style="font-size:.88rem;color:#888;margin-bottom:20px;">Choose your ticket type for <?= htmlspecialchars($event['name']) ?>.</p>
                        <?php foreach ($ticketTypes as $tt):
                            $now         = time();
                            $saleStartTs = !empty($tt['sale_starts_at']) ? strtotime($tt['sale_starts_at']) : null;
                            $saleEndTs   = !empty($tt['sale_ends_at'])   ? strtotime($tt['sale_ends_at'])   : null;
                            $isSoldOut    = !empty($tt['is_sold_out']) || ($tt['remaining'] !== null && $tt['remaining'] <= 0);
                            $isComingSoon = !$isSoldOut && $saleStartTs && $saleStartTs > $now;
                            $isSaleEnded  = !$isSoldOut && $saleEndTs && $saleEndTs < $now;
                            $isAvailable  = !empty($tt['is_available']) && !$isSoldOut;
                            $remaining    = $tt['remaining'] ?? null;
                            $lowStock     = $remaining !== null && $remaining > 0 && $remaining <= 10;
                            $minOrder     = (int)($tt['min_per_order'] ?? 1);
                            $savingsPct   = (!empty($tt['original_price']) && $tt['original_price'] > $tt['price'])
                                            ? round((($tt['original_price'] - $tt['price']) / $tt['original_price']) * 100) : 0;
                            $unavailable  = $isSoldOut || $isComingSoon || $isSaleEnded;
                        ?>
                        <div class="ticket-type <?= $unavailable ? 'unavailable' : '' ?>">
                            <!-- Name + badges row -->
                            <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:8px;">
                                <div class="ticket-name"><?= htmlspecialchars($tt['name']) ?></div>
                                <div>
                                    <?php if ($isSoldOut): ?>
                                    <span class="tt-badge sold-out-badge"><i class="fas fa-times-circle"></i> Sold Out</span>
                                    <?php elseif ($isComingSoon): ?>
                                    <span class="tt-badge coming-soon-badge"><i class="fas fa-clock"></i> From <?= date('M j', $saleStartTs) ?></span>
                                    <?php elseif ($isSaleEnded): ?>
                                    <span class="tt-badge sale-ended-badge"><i class="fas fa-ban"></i> Sale ended</span>
                                    <?php elseif ($lowStock): ?>
                                    <span class="tt-badge low-stock-badge"><i class="fas fa-fire"></i> Only <?= $remaining ?> left</span>
                                    <?php endif; ?>
                                    <?php if ($minOrder > 1): ?>
                                    <span class="tt-badge group-badge"><i class="fas fa-users"></i> Min <?= $minOrder ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <!-- Price -->
                            <div style="margin-bottom:8px;">
                                <span class="ticket-price"><?= $tt['currency'] ?> <?= number_format($tt['price'], 0) ?></span>
                                <?php if ($savingsPct > 0): ?>
                                <span class="ticket-orig"><?= number_format($tt['original_price'], 0) ?></span>
                                <span class="ticket-save">Save <?= $savingsPct ?>%</span>
                                <?php endif; ?>
                            </div>
                            <?php if (!empty($tt['description'])): ?>
                            <div class="ticket-desc"><?= htmlspecialchars($tt['description']) ?></div>
                            <?php endif; ?>
                            <?php if (!empty($tt['benefits'])): ?>
                            <ul style="list-style:none;padding:0;margin:10px 0 0;font-size:.79rem;color:#555;">
                                <?php foreach ($tt['benefits'] as $b): ?>
                                <li style="margin-bottom:4px;"><i class="fas fa-check" style="color:#10b981;margin-right:6px;font-size:.68rem;"></i><?= htmlspecialchars($b) ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <?php endif; ?>
                            <?php if ($isAvailable): ?>
                            <a href="<?= SITE_URL ?>/checkout?slug=<?= urlencode($slug) ?>"
                               class="theme-btn btn-style-one" style="display:block;text-align:center;margin-top:14px;">
                                <span class="btn-title" style="color:#ffffff;"><i class="fas fa-ticket-alt me-1"></i> Get Tickets</span>
                            </a>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <p style="text-align:center;color:#aaa;padding:40px 0;">Ticket details coming soon.</p>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>

                    <!-- ── FAQ tab ────────────────────────────────────────── -->
                    <?php if (!empty($faqs)): ?>
                    <div class="tab-pane fade" id="tab-faq">
                        <div id="eventFaqAccordion">
                            <?php foreach ($faqs as $fi => $faq): ?>
                            <div class="card" style="border:1px solid #f0f0f0;border-radius:8px;margin-bottom:8px;">
                                <div class="card-header" style="background:#fafafa;border:none;padding:0;">
                                    <button class="btn btn-link w-100 text-left font-weight-bold"
                                            style="color:#1e1548;font-size:.93rem;text-decoration:none;padding:14px 20px;"
                                            data-toggle="collapse" data-target="#faq-c-<?= $fi ?>">
                                        <?= htmlspecialchars($faq['question']) ?>
                                    </button>
                                </div>
                                <div id="faq-c-<?= $fi ?>" class="collapse <?= $fi===0?'show':'' ?>">
                                    <div class="card-body" style="font-size:.9rem;color:#555;line-height:1.75;padding:14px 20px;">
                                        <?= nl2br(htmlspecialchars($faq['answer'])) ?>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                </div><!-- end tab-content -->
            </div><!-- end content-side -->

            <!-- ── Sidebar ───────────────────────────────────────────────── -->
            <div class="sidebar-side col-lg-4 col-md-12 col-sm-12">
                <aside class="sidebar padding-left">

                    <!-- Event Info Card -->
                    <div class="event-info-card">
                        <h5 style="font-weight:700;color:#1e1548;margin-bottom:20px;font-size:1rem;">Event Info</h5>

                        <div class="info-row">
                            <i class="fas fa-calendar-alt info-icon"></i>
                            <div>
                                <span class="info-label">Date</span>
                                <span class="info-value"><?= $dateStr ?></span>
                            </div>
                        </div>

                        <?php if (!empty($event['start_time'])): ?>
                        <div class="info-row">
                            <i class="fas fa-clock info-icon"></i>
                            <div>
                                <span class="info-label">Time</span>
                                <span class="info-value">
                                    <?= date('h:i A', strtotime($event['start_time'])) ?>
                                    <?= !empty($event['end_time']) ? ' – ' . date('h:i A', strtotime($event['end_time'])) : '' ?>
                                </span>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php
                        $fmtIcon  = $isVirtual ? 'fa-video' : ($eventFormat === 'hybrid' ? 'fa-layer-group' : 'fa-map-marker-alt');
                        $fmtLabel = $isVirtual ? 'Virtual' : ($eventFormat === 'hybrid' ? 'Hybrid' : 'In-Person');
                        ?>
                        <div class="info-row">
                            <i class="fas <?= $fmtIcon ?> info-icon"></i>
                            <div>
                                <span class="info-label">Format</span>
                                <span class="info-value"><?= $fmtLabel ?></span>
                            </div>
                        </div>

                        <?php if (!empty($event['venue_name'])): ?>
                        <div class="info-row">
                            <i class="fas fa-building info-icon"></i>
                            <div>
                                <span class="info-label">Venue</span>
                                <span class="info-value"><?= htmlspecialchars($event['venue_name']) ?><?= !empty($event['venue_city']) ? ', ' . htmlspecialchars($event['venue_city']) : '' ?></span>
                                <?php if (!empty($event['venue_address'])): ?>
                                <span style="display:block;font-size:.78rem;color:#aaa;margin-top:2px;"><?= htmlspecialchars($event['venue_address']) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if ($maxAttendees): ?>
                        <div class="info-row">
                            <i class="fas fa-users info-icon"></i>
                            <div>
                                <span class="info-label">Capacity</span>
                                <span class="info-value"><?= number_format($maxAttendees) ?> attendees</span>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if (!empty($event['has_voting'])): ?>
                        <div class="info-row">
                            <i class="fas fa-vote-yea info-icon"></i>
                            <div>
                                <span class="info-label">Voting</span>
                                <span class="info-value" style="color:<?= $isVotingOpen ? '#10b981' : ($votingClosed ? '#6c757d' : '#ed1c24') ?>;">
                                    <?= $isVotingOpen ? 'Currently Open' : ($votingClosed ? 'Closed' : ($votingNotYet ? 'Not Yet Open' : 'N/A')) ?>
                                </span>
                                <?php if ($votingOpensTs || $votingClosesTs): ?>
                                <span style="display:block;font-size:.75rem;color:#aaa;margin-top:2px;">
                                    <?= $votingOpensTs ? date('d M Y', $votingOpensTs) : '' ?>
                                    <?= ($votingOpensTs && $votingClosesTs) ? ' – ' : '' ?>
                                    <?= $votingClosesTs ? date('d M Y', $votingClosesTs) : '' ?>
                                </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if ($hasRegistration): ?>
                        <div class="info-row">
                            <i class="fas fa-user-plus info-icon"></i>
                            <div>
                                <span class="info-label">Registration</span>
                                <span class="info-value" style="color:<?= $isRegOpen ? '#10b981' : ($regClosed ? '#6c757d' : '#f59e0b') ?>;">
                                    <?= $isRegOpen ? 'Open' : ($regClosed ? 'Closed' : 'Not Yet Open') ?>
                                </span>
                                <?php if ($regClosesTs && $isRegOpen): ?>
                                <span style="display:block;font-size:.75rem;color:#aaa;margin-top:2px;">Closes <?= date('d M Y', $regClosesTs) ?></span>
                                <?php elseif ($regOpensTs && $regNotYet): ?>
                                <span style="display:block;font-size:.75rem;color:#aaa;margin-top:2px;">Opens <?= date('d M Y', $regOpensTs) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Virtual Join CTA -->
                    <?php if ($isVirtual && !empty($virtualUrl)): ?>
                    <div style="background:linear-gradient(135deg,#1e1548,#2d1f6b);border-radius:12px;padding:24px;text-align:center;color:#fff;margin-bottom:24px;">
                        <div style="font-size:.72rem;text-transform:uppercase;letter-spacing:2px;opacity:.7;margin-bottom:8px;">Virtual Event</div>
                        <h5 style="font-weight:800;margin-bottom:16px;"><?= htmlspecialchars($event['name']) ?></h5>
                        <a href="<?= htmlspecialchars($virtualUrl) ?>" target="_blank"
                           class="theme-btn btn-style-one" style="display:block;text-align:center;">
                            <span class="btn-title"><i class="fas fa-video me-2"></i> Join Online</span>
                        </a>
                    </div>
                    <?php endif; ?>

                    <!-- Ticket CTA (lifecycle-aware) -->
                    <?php if ($hasTicketing): ?>
                    <?php if ($phase === 'on_sale' || !empty($ticketTypes)): ?>
                    <div style="background:linear-gradient(135deg,#1e1548,#2d1f6b);border-radius:12px;padding:26px;text-align:center;color:#fff;margin-bottom:24px;" id="tickets">
                        <div style="font-size:.72rem;text-transform:uppercase;letter-spacing:2px;opacity:.7;margin-bottom:8px;">Get Your Ticket</div>
                        <h5 style="font-weight:800;margin-bottom:16px;"><?= htmlspecialchars($event['name']) ?></h5>
                        <a href="<?= SITE_URL ?>/checkout?slug=<?= urlencode($slug) ?>"
                           class="theme-btn btn-style-one" style="display:block;text-align:center;">
                            <span class="btn-title" style="color:#ffffff;"><i class="fas fa-ticket-alt me-2"></i> Buy Tickets</span>
                        </a>
                    </div>
                    <?php else: ?>
                    <div class="locked-cta" id="tickets">
                        <i class="fas fa-ticket-alt"></i>
                        <div class="locked-title">Tickets Coming Soon</div>
                        <div class="locked-sub">Ticket sales have not started yet. Check back soon.</div>
                    </div>
                    <?php endif; ?>
                    <?php endif; ?>

                    <!-- Vote CTA (lifecycle-aware) -->
                    <?php if (!empty($event['has_voting'])): ?>
                    <?php if ($isVotingOpen): ?>
                    <div style="background:linear-gradient(135deg,#ed1c24,#c41820);border-radius:12px;padding:26px;text-align:center;color:#fff;margin-bottom:24px;">
                        <div style="font-size:.72rem;text-transform:uppercase;letter-spacing:2px;color:rgba(255,255,255,.75);margin-bottom:8px;">Voting is Open</div>
                        <h5 style="font-weight:800;margin-bottom:8px;color:#fff;">Cast Your Vote</h5>
                        <p style="font-size:.85rem;color:rgba(255,255,255,.9);margin-bottom:16px;">Support your favourite nominees in <?= htmlspecialchars($event['name']) ?>.</p>
                        <a href="<?= SITE_URL ?>/nominees?event=<?= urlencode($slug) ?>"
                           class="theme-btn btn-style-two" style="display:block;text-align:center;background:#fff;color:#ed1c24;border-color:#fff;">
                            <span class="btn-title" style="color:#ed1c24;"><i class="fas fa-vote-yea me-2"></i> Vote Now</span>
                        </a>
                    </div>
                    <?php elseif ($votingNotYet): ?>
                    <div class="locked-cta">
                        <i class="fas fa-lock"></i>
                        <div class="locked-title">Voting Not Yet Open</div>
                        <div class="locked-sub">Voting opens <?= $votingOpensTs ? date('d M Y \a\t h:i A', $votingOpensTs) : 'soon' ?>.</div>
                    </div>
                    <?php elseif ($votingClosed): ?>
                    <div class="locked-cta">
                        <i class="fas fa-flag-checkered"></i>
                        <div class="locked-title">Voting Has Closed</div>
                        <div class="locked-sub">Thank you for voting! Results will be announced at the event.</div>
                    </div>
                    <?php endif; ?>
                    <?php endif; ?>

                    <!-- Registration CTA -->
                    <?php if ($hasRegistration): ?>
                    <?php if ($isRegOpen): ?>
                    <div style="background:linear-gradient(135deg,#059669,#10b981);border-radius:12px;padding:26px;text-align:center;color:#fff;margin-bottom:24px;">
                        <div style="font-size:.72rem;text-transform:uppercase;letter-spacing:2px;opacity:.7;margin-bottom:8px;">Registration Open</div>
                        <h5 style="font-weight:800;margin-bottom:8px;">Secure Your Spot</h5>
                        <?php if ($regClosesTs): ?>
                        <p style="font-size:.82rem;opacity:.88;margin-bottom:16px;">Registration closes <?= date('d M Y', $regClosesTs) ?>.</p>
                        <?php endif; ?>
                        <a href="<?= API_BASE ?>/events/<?= urlencode($slug) ?>/register" target="_blank"
                           class="theme-btn btn-style-two" style="display:block;text-align:center;background:#fff;color:#059669;border-color:#fff;">
                            <span class="btn-title"><i class="fas fa-user-plus me-2"></i> Register Now</span>
                        </a>
                    </div>
                    <?php elseif ($regNotYet): ?>
                    <div class="locked-cta">
                        <i class="fas fa-calendar-plus"></i>
                        <div class="locked-title">Registration Not Yet Open</div>
                        <div class="locked-sub">Opens <?= $regOpensTs ? date('d M Y', $regOpensTs) : 'soon' ?>.</div>
                    </div>
                    <?php elseif ($regClosed): ?>
                    <div class="locked-cta">
                        <i class="fas fa-door-closed"></i>
                        <div class="locked-title">Registration Closed</div>
                        <div class="locked-sub">Registration is no longer available for this event.</div>
                    </div>
                    <?php endif; ?>
                    <?php endif; ?>

                    <!-- More Events -->
                    <div class="sidebar-widget">
                        <h5 class="sidebar-title">Explore More</h5>
                        <div class="widget-content">
                            <ul style="list-style:none;padding:0;margin:0;">
                                <li style="border-bottom:1px solid #f0f0f0;">
                                    <a href="<?= SITE_URL ?>/events" style="display:flex;align-items:center;gap:10px;padding:10px 0;color:#333;text-decoration:none;font-size:.9rem;font-weight:500;" onmouseover="this.style.color='#ed1c24'" onmouseout="this.style.color='#333'">
                                        <i class="fa fa-calendar-alt" style="color:#ed1c24;width:18px;text-align:center;flex-shrink:0;"></i>
                                        All Events
                                    </a>
                                </li>
                                <li style="border-bottom:1px solid #f0f0f0;">
                                    <a href="<?= SITE_URL ?>/nominees" style="display:flex;align-items:center;gap:10px;padding:10px 0;color:#333;text-decoration:none;font-size:.9rem;font-weight:500;" onmouseover="this.style.color='#ed1c24'" onmouseout="this.style.color='#333'">
                                        <i class="fa fa-users" style="color:#ed1c24;width:18px;text-align:center;flex-shrink:0;"></i>
                                        All Nominees
                                    </a>
                                </li>
                                <li style="border-bottom:1px solid #f0f0f0;">
                                    <a href="<?= SITE_URL ?>/polls" style="display:flex;align-items:center;gap:10px;padding:10px 0;color:#333;text-decoration:none;font-size:.9rem;font-weight:500;" onmouseover="this.style.color='#ed1c24'" onmouseout="this.style.color='#333'">
                                        <i class="fa fa-poll" style="color:#ed1c24;width:18px;text-align:center;flex-shrink:0;"></i>
                                        Live Polls
                                    </a>
                                </li>
                                <?php if ($hasNominations): ?>
                                <li>
                                    <a href="<?= SITE_URL ?>/nominate.php?event=<?= urlencode($slug) ?>" style="display:flex;align-items:center;gap:10px;padding:10px 0;color:#333;text-decoration:none;font-size:.9rem;font-weight:500;" onmouseover="this.style.color='#ed1c24'" onmouseout="this.style.color='#333'">
                                        <i class="fa fa-pen-nib" style="color:#ed1c24;width:18px;text-align:center;flex-shrink:0;"></i>
                                        Nominate Someone
                                    </a>
                                </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>

                </aside>
            </div>
            <!-- end Sidebar -->

        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
</div>

<!-- ── Nominee Detail Modal ────────────────────────────── -->
<div class="modal fade" id="nomineeModal" tabindex="-1" role="dialog" aria-hidden="true" style="padding-right:0;">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width:560px;">
        <div class="modal-content" style="border-radius:14px;border:none;overflow:hidden;box-shadow:0 10px 40px rgba(0,0,0,.18);">

            <!-- Header -->
            <div class="modal-header" style="background:linear-gradient(135deg,#1e1548 0%,#2d1f6b 100%);padding:18px 22px;border:none;">
                <div>
                    <span id="modal-nom-cat" style="font-size:.7rem;color:rgba(255,255,255,.65);text-transform:uppercase;letter-spacing:.8px;font-weight:700;"></span>
                    <h5 class="modal-title" id="modal-nom-name" style="margin:3px 0 0;font-weight:800;color:#fff;line-height:1.2;font-size:1.15rem;"></h5>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="padding:18px 22px;margin:-18px -22px -18px auto;color:#fff;opacity:.8;text-shadow:none;">
                    <span aria-hidden="true" style="font-size:1.6rem;font-weight:300;">&times;</span>
                </button>
            </div>

            <!-- Body -->
            <div class="modal-body" style="padding:24px 22px;">

                <!-- Avatar + winner badge row -->
                <div style="display:flex;align-items:flex-start;gap:18px;margin-bottom:18px;">
                    <div style="flex-shrink:0;">
                        <img id="modal-nom-img" src="" alt="" style="width:88px;height:88px;border-radius:50%;object-fit:cover;border:3px solid #ede9f6;display:none;">
                        <div id="modal-nom-initials" style="width:88px;height:88px;border-radius:50%;color:#fff;font-size:1.7rem;font-weight:800;display:flex;align-items:center;justify-content:center;"></div>
                    </div>
                    <div style="flex:1;min-width:0;padding-top:4px;">
                        <!-- Winner badge -->
                        <div id="modal-nom-winner" style="display:none;font-size:.72rem;background:#f59e0b;color:#fff;padding:3px 10px;border-radius:20px;font-weight:700;margin-bottom:6px;display:inline-block;">
                            <i class="fas fa-trophy"></i> <span id="modal-nom-winner-label">Winner</span>
                        </div>
                        <!-- Subtitle / role -->
                        <div id="modal-nom-subtitle" style="font-size:.9rem;color:#ed1c24;font-weight:700;margin-bottom:6px;"></div>
                        <!-- Voting code -->
                        <div id="modal-nom-code-wrap" style="display:none;margin-bottom:6px;">
                            <span style="font-size:.7rem;color:#999;text-transform:uppercase;letter-spacing:.5px;">Code</span><br>
                            <span id="modal-nom-code" style="font-family:monospace;font-size:.9rem;font-weight:700;color:#1e1548;background:#f0eeff;padding:2px 8px;border-radius:5px;"></span>
                        </div>
                        <!-- Votes pill -->
                        <div id="modal-nom-votes-wrap" style="display:inline-flex;align-items:center;gap:6px;background:rgba(30,21,72,.06);padding:5px 12px;border-radius:20px;">
                            <i class="fas fa-poll" style="color:#1e1548;font-size:.8rem;"></i>
                            <span id="modal-nom-votes" style="font-size:.82rem;font-weight:700;color:#1e1548;"></span>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div id="modal-nom-desc-wrap" style="display:none;background:#f9fafb;border-radius:8px;padding:14px;margin-bottom:14px;">
                    <p style="font-size:.75rem;color:#aaa;text-transform:uppercase;letter-spacing:.5px;font-weight:700;margin:0 0 6px;">About</p>
                    <div id="modal-nom-desc" style="font-size:.88rem;color:#444;line-height:1.65;margin:0;"></div>
                </div>

                <!-- Contact info -->
                <div id="modal-nom-contact-wrap" style="display:none;margin-bottom:14px;">
                    <p style="font-size:.75rem;color:#aaa;text-transform:uppercase;letter-spacing:.5px;font-weight:700;margin:0 0 8px;">Contact</p>
                    <div style="display:flex;flex-direction:column;gap:6px;">
                        <div id="modal-nom-email-row" style="display:none;align-items:center;gap:8px;">
                            <i class="fas fa-envelope" style="color:#1e1548;font-size:.8rem;width:16px;text-align:center;"></i>
                            <a id="modal-nom-email" href="#" style="font-size:.85rem;color:#1e1548;text-decoration:none;"></a>
                        </div>
                        <div id="modal-nom-phone-row" style="display:none;align-items:center;gap:8px;">
                            <i class="fas fa-phone" style="color:#1e1548;font-size:.8rem;width:16px;text-align:center;"></i>
                            <span id="modal-nom-phone" style="font-size:.85rem;color:#444;"></span>
                        </div>
                    </div>
                </div>

                <!-- Video link -->
                <div id="modal-nom-video-wrap" style="display:none;margin-bottom:14px;">
                    <a id="modal-nom-video" href="#" target="_blank" rel="noopener"
                       style="display:inline-flex;align-items:center;gap:8px;font-size:.85rem;color:#ed1c24;font-weight:600;text-decoration:none;">
                        <i class="fas fa-play-circle" style="font-size:1rem;"></i> Watch Video
                    </a>
                </div>

                <!-- Social links -->
                <div id="modal-nom-socials-wrap" style="display:none;">
                    <p style="font-size:.75rem;color:#aaa;text-transform:uppercase;letter-spacing:.5px;font-weight:700;margin:0 0 8px;">Social</p>
                    <div id="modal-nom-socials" style="display:flex;gap:10px;flex-wrap:wrap;"></div>
                </div>

            </div><!-- /modal-body -->

            <?php if ($isVotingOpen): ?>
            <div class="modal-footer" style="padding:14px 22px;background:#f9fafb;border-top:1px solid #f0f0f0;justify-content:center;">
                <a href="#" id="modal-vote-btn" class="theme-btn btn-style-one" style="font-size:.85rem;padding:10px 24px;width:100%;text-align:center;">
                    <span class="btn-title"><i class="fas fa-vote-yea me-1"></i> Vote for this Nominee</span>
                </a>
            </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<div class="scroll-to-top scroll-to-target" data-target="html"><span class="fa fa-angle-up"></span></div>
<?php include 'includes/footer-links.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    // FancyBox gallery
    $('[data-fancybox="event-gallery"]').fancybox({ buttons: ['slideShow','fullScreen','thumbs','close'] });
    // Bootstrap 4 tabs
    $('#eventTabs a[data-toggle="tab"]').on('click', function(e) {
        e.preventDefault();
        $(this).tab('show');
    });

    // ── Nominee Modal ───────────────────────────────────────
    $(document).on('click', '.nom-card-col', function() {
        var $this     = $(this);
        var nomId     = $this.data('nom-id');
        var name      = $this.data('nom-name');
        var subtitle  = $this.data('nom-subtitle');
        var desc      = $this.data('nom-description');
        var img       = $this.data('nom-image');
        var ini       = $this.data('nom-initials');
        var color     = $this.data('nom-color');
        var votes     = parseInt($this.data('nom-votes'), 10) || 0;
        var isWinner  = parseInt($this.data('nom-winner'), 10);
        var winnerPos = $this.data('nom-winner-pos');
        var cat       = $this.data('nom-cat');
        var code      = $this.data('nom-code');
        var email     = $this.data('nom-email');
        var phone     = $this.data('nom-phone');
        var video     = $this.data('nom-video');
        var socialsRaw = $this.data('nom-socials');
        var socials   = {};
        try { socials = typeof socialsRaw === 'object' ? socialsRaw : JSON.parse(socialsRaw || '{}'); } catch(e) {}

        // Header
        $('#modal-nom-cat').text(cat);
        $('#modal-nom-name').text(name);

        // Avatar
        if (img) {
            $('#modal-nom-img').attr('src', img).attr('alt', name).show();
            $('#modal-nom-initials').hide();
        } else {
            $('#modal-nom-img').hide();
            $('#modal-nom-initials').text(ini).css('background-color', color).show();
        }

        // Winner badge
        if (isWinner) {
            var wLabel = winnerPos ? 'Winner — #' + winnerPos : 'Winner';
            $('#modal-nom-winner-label').text(wLabel);
            $('#modal-nom-winner').css('display', 'inline-block');
        } else {
            $('#modal-nom-winner').hide();
        }

        // Subtitle
        $('#modal-nom-subtitle').text(subtitle || '').toggle(!!subtitle);

        // Code
        if (code) {
            $('#modal-nom-code').text(code);
            $('#modal-nom-code-wrap').show();
        } else {
            $('#modal-nom-code-wrap').hide();
        }

        // Votes
        $('#modal-nom-votes').text(votes.toLocaleString() + ' votes');

        // Description
        if (desc) {
            $('#modal-nom-desc').text(desc);
            $('#modal-nom-desc-wrap').show();
        } else {
            $('#modal-nom-desc-wrap').hide();
        }

        // Contact
        var hasContact = false;
        if (email) {
            $('#modal-nom-email').text(email).attr('href', 'mailto:' + email);
            $('#modal-nom-email-row').css('display', 'flex');
            hasContact = true;
        } else {
            $('#modal-nom-email-row').hide();
        }
        if (phone) {
            $('#modal-nom-phone').text(phone);
            $('#modal-nom-phone-row').css('display', 'flex');
            hasContact = true;
        } else {
            $('#modal-nom-phone-row').hide();
        }
        $('#modal-nom-contact-wrap').toggle(hasContact);

        // Video
        if (video) {
            $('#modal-nom-video').attr('href', video);
            $('#modal-nom-video-wrap').show();
        } else {
            $('#modal-nom-video-wrap').hide();
        }

        // Social links
        var socialIcons = {
            facebook:  { icon:'fab fa-facebook-f',   color:'#1877f2', label:'Facebook' },
            twitter:   { icon:'fab fa-twitter',       color:'#1da1f2', label:'Twitter'  },
            instagram: { icon:'fab fa-instagram',     color:'#e1306c', label:'Instagram'},
            linkedin:  { icon:'fab fa-linkedin-in',   color:'#0a66c2', label:'LinkedIn' },
            youtube:   { icon:'fab fa-youtube',       color:'#ff0000', label:'YouTube'  },
            tiktok:    { icon:'fab fa-tiktok',        color:'#010101', label:'TikTok'   },
            website:   { icon:'fas fa-globe',         color:'#1e1548', label:'Website'  }
        };
        var $socialContainer = $('#modal-nom-socials').empty();
        var hasSocials = false;
        $.each(socials, function(platform, url) {
            if (!url) return;
            var cfg = socialIcons[platform.toLowerCase()] || { icon:'fas fa-link', color:'#888', label: platform };
            hasSocials = true;
            $socialContainer.append(
                '<a href="' + url + '" target="_blank" rel="noopener" title="' + cfg.label + '" ' +
                'style="display:inline-flex;align-items:center;justify-content:center;width:34px;height:34px;border-radius:50%;background:' + cfg.color + ';color:#fff;font-size:.82rem;text-decoration:none;">' +
                '<i class="' + cfg.icon + '"></i></a>'
            );
        });
        $('#modal-nom-socials-wrap').toggle(hasSocials);

        // Vote button
        if ($('#modal-vote-btn').length) {
            $('#modal-vote-btn').attr('href', '<?= SITE_URL ?>/vote-bundle?event=<?= urlencode($slug) ?>&nominee=' + nomId);
        }

        $('#nomineeModal').modal('show');
    });

    // ── Live search & Filter nominees ────────────────────────
    function filterNominees() {
        var q = $('#nom-search').val() ? $('#nom-search').val().trim().toLowerCase() : '';
        var selectedCatId = $('#nom-cat-jump').length ? $('#nom-cat-jump').val() : 'all';
        var totalVisible = 0;

        $('.nom-cat-section').each(function() {
            var $section  = $(this);
            var sectionId = $section.attr('id');
            
            // Cat match
            var catMatches = (selectedCatId === 'all' || !selectedCatId || sectionId === selectedCatId);
            
            if (!catMatches) {
                $section.addClass('d-none');
                return; // skip inner loops
            }

            var secVisible = 0;
            $section.find('.nom-card-col').each(function() {
                var name     = $(this).data('name')     || '';
                var subtitle = $(this).data('subtitle') || '';
                var matches  = !q || name.indexOf(q) > -1 || subtitle.indexOf(q) > -1;
                $(this).toggleClass('d-none', !matches);
                if (matches) secVisible++;
            });

            // Section is visible if catMatches AND text search has results (or no text search)
            var searchMatches = (q.length === 0) || (secVisible > 0);
            $section.toggleClass('d-none', !searchMatches);
            
            totalVisible += secVisible;
        });

        // Show/hide "no results" message
        if (q.length > 0 && totalVisible === 0) {
            $('#nom-no-results').show();
        } else {
            $('#nom-no-results').hide();
        }
    }

    $('#nom-search').on('input', filterNominees);

    // ── Select2: Filter by category ───────────────────────────
    if ($('#nom-cat-jump').length) {
        $('#nom-cat-jump').select2({
            minimumResultsForSearch: 10,
            width: '100%'
        }).on('change', filterNominees);
    }
});
</script>

</body>
</html>
