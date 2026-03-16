<?php
include 'config/config.php';
include 'libs/App.php';

$services = [
    'event-management' => [
        'icon'     => 'fa-calendar-alt',
        'title'    => 'Event Management',
        'tagline'  => 'End-to-end event operations from one dashboard',
        'desc'     => 'Tuqio Hub gives organizers a complete toolkit to plan, launch, and run any type of event — from intimate community gatherings to large national galas. Manage every detail from a single, intuitive dashboard.',
        'features' => [
            'Custom event landing pages with full branding control',
            'Multi-day programme and session scheduling',
            'Speaker profiles and session assignments',
            'Venue details and directions integration',
            'Live attendee dashboard with real-time registration data',
            'Post-event analytics and downloadable reports',
        ],
        'image' => 'event.webp',
        'color' => '#1e1548',
    ],
    'awards-nominations' => [
        'icon'     => 'fa-award',
        'title'    => 'Awards & Nominations',
        'tagline'  => 'Transparent awards from submission to winners night',
        'desc'     => 'Power the full awards lifecycle — from accepting public nominations and reviewing submissions to shortlisting finalists and running a live public vote. Every step is trackable and audit-ready.',
        'features' => [
            'Public nomination submission forms per award category',
            'Admin review and approval workflow',
            'Finalist shortlisting and public profile pages',
            'Live public voting with real-time counters',
            'Voting window countdown timers',
            'Winners announcement controls',
        ],
        'image' => 'about.webp',
        'color' => '#ed1c24',
    ],
    'ticketing' => [
        'icon'     => 'fa-ticket-alt',
        'title'    => 'Ticketing & Payments',
        'tagline'  => 'Sell tickets securely with instant delivery',
        'desc'     => 'Sell event tickets online with multiple ticket types and pricing tiers. Buyers receive instant confirmation and QR-code tickets. Funds are settled to your organization on an agreed schedule.',
        'features' => [
            'Multiple ticket types (VIP, General, Early Bird)',
            'Custom pricing and availability windows',
            'Secure payment gateway integration',
            'Instant QR-code ticket delivery via email',
            'Ticket scanning and gate management tools',
            'Revenue and sales reports for organizers',
        ],
        'image' => 'slide-1.webp',
        'color' => '#1e1548',
    ],
    'live-polls' => [
        'icon'     => 'fa-poll',
        'title'    => 'Live Polls',
        'tagline'  => 'Real-time audience engagement before, during & after',
        'desc'     => 'Keep your audience engaged with live polls that update in real-time. Use polls for pre-event surveys, on-stage audience interaction, or post-event feedback — results display live on screen.',
        'features' => [
            'Multiple choice and single-answer poll formats',
            'Live results displayed in real-time',
            'Schedulable polls (open/close at a specific time)',
            'Embed poll results on your event page',
            'Audience response analytics export',
            'Unlimited polls per event',
        ],
        'image' => 'slide-2.webp',
        'color' => '#ed1c24',
    ],
    'gallery' => [
        'icon'     => 'fa-images',
        'title'    => 'Event Gallery',
        'tagline'  => 'Showcase your event moments beautifully',
        'desc'     => 'Upload and organize photos from your event into a stunning public gallery. Categorize by day, session, or category. Galleries are linked to your event page and indexed for public browsing.',
        'features' => [
            'Unlimited photo uploads per event',
            'Category and album organization',
            'Lightbox viewer for full-screen browsing',
            'Public gallery linked to your event page',
            'Cover image and thumbnail controls',
            'Gallery export for post-event reports',
        ],
        'image' => 'slide-3.webp',
        'color' => '#1e1548',
    ],
    'analytics' => [
        'icon'     => 'fa-chart-bar',
        'title'    => 'Analytics & Reports',
        'tagline'  => 'Data-driven insights for every event',
        'desc'     => 'Get real-time visibility into every aspect of your event — ticket sales, nominee votes, poll responses, and page views — all from your organizer dashboard. Export everything to CSV for stakeholder reporting.',
        'features' => [
            'Real-time vote counts by nominee and category',
            'Ticket sales tracking and revenue summary',
            'Poll response rates and answer breakdowns',
            'Attendee registration data',
            'Page views and engagement metrics',
            'One-click CSV/Excel export for all reports',
        ],
        'image' => 'about.webp',
        'color' => '#ed1c24',
    ],
];

$id = trim($_GET['id'] ?? '');
if (!$id || !isset($services[$id])) {
    header('Location: ' . SITE_URL . '/about');
    exit;
}

$service = $services[$id];

// Build other services list (excluding current)
$otherServices = array_filter($services, fn($k) => $k !== $id, ARRAY_FILTER_USE_KEY);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title><?= htmlspecialchars($service['title']) ?> | Tuqio Hub</title>
<meta name="description" content="<?= htmlspecialchars($service['tagline']) ?>">
<link href="<?= SITE_URL ?>/assets/css/bootstrap.min.css" rel="stylesheet">
<link href="<?= SITE_URL ?>/assets/css/style.css" rel="stylesheet">
<link href="<?= SITE_URL ?>/assets/css/responsive.css" rel="stylesheet">
<link href="<?= SITE_URL ?>/assets/css/custom.css" rel="stylesheet">
<link rel="icon" type="image/png" href="<?= SITE_URL ?>/assets/images/favicon/favicon-96x96.png" sizes="96x96">
<link rel="icon" type="image/svg+xml" href="<?= SITE_URL ?>/assets/images/favicon/favicon.svg">
<link rel="shortcut icon" href="<?= SITE_URL ?>/assets/images/favicon/favicon.ico">
<meta name="apple-mobile-web-app-title" content="Tuqio Hub">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
<style>
.pre-tag {
    display: inline-block;
    background: rgba(237,28,36,0.1); color: #ed1c24;
    font-size: .72rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: 2px;
    padding: 4px 14px; border-radius: 20px; margin-bottom: 14px;
}
.service-hero {
    padding: 80px 0; background: #fff;
}
.service-icon-lg {
    width: 90px; height: 90px; border-radius: 20px;
    background: rgba(237,28,36,0.1);
    display: flex; align-items: center; justify-content: center;
    font-size: 2.2rem; color: #ed1c24; margin-bottom: 24px;
}
.service-img {
    width: 100%; border-radius: 14px;
    object-fit: cover; max-height: 380px;
}
.service-features { padding: 70px 0; background: #f9fafb; }
.feature-check {
    display: flex; gap: 14px; align-items: flex-start;
    margin-bottom: 18px;
}
.feature-check .check-icon {
    width: 32px; height: 32px; border-radius: 50%;
    background: rgba(237,28,36,0.1);
    display: flex; align-items: center; justify-content: center;
    color: #ed1c24; font-size: .85rem; flex-shrink: 0; margin-top: 2px;
}
.feature-check p { font-size: .92rem; color: #444; line-height: 1.6; margin: 0; }
.other-services { padding: 70px 0; background: #fff; }
.service-card {
    background: #f9fafb; border-radius: 12px;
    padding: 24px 20px; text-align: center;
    text-decoration: none; display: block; color: inherit;
    transition: transform .25s, box-shadow .25s;
}
.service-card:hover { transform: translateY(-4px); box-shadow: 0 10px 28px rgba(0,0,0,0.09); color: inherit; }
.service-card .sc-icon {
    width: 56px; height: 56px; border-radius: 50%;
    background: rgba(237,28,36,0.1);
    display: flex; align-items: center; justify-content: center;
    font-size: 1.3rem; color: #ed1c24; margin: 0 auto 14px;
}
.service-card h6 { font-weight: 700; color: #1e1548; margin: 0; font-size: .9rem; }
.service-cta { padding: 70px 0; background: linear-gradient(135deg, #15102e, #1e1548); }
.section-heading { font-size: 2rem; font-weight: 800; color: #1e1548; }
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

<section class="page-title" style="background-image:url(<?= SITE_URL ?>/assets/slides/<?= htmlspecialchars($service['image']) ?>);">
    <div class="anim-icons full-width"><span class="icon icon-bull-eye"></span><span class="icon icon-dotted-circle"></span></div>
    <div class="auto-container">
        <div class="title-outer">
            <h1><?= htmlspecialchars($service['title']) ?></h1>
            <ul class="page-breadcrumb">
                <li><a href="<?= SITE_URL ?>">Home</a></li>
                <li><a href="<?= SITE_URL ?>/about">About</a></li>
                <li><?= htmlspecialchars($service['title']) ?></li>
            </ul>
        </div>
    </div>
</section>

<!-- ── Service Overview ── -->
<section class="service-hero">
    <div class="auto-container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-5 mb-lg-0 wow fadeInLeft">
                <div class="service-icon-lg"><i class="fas <?= $service['icon'] ?>"></i></div>
                <span class="pre-tag">Our Services</span>
                <h2 class="section-heading mb-3"><?= htmlspecialchars($service['title']) ?></h2>
                <p class="mb-2" style="color:#777;font-size:.88rem;text-transform:uppercase;letter-spacing:1px;"><?= htmlspecialchars($service['tagline']) ?></p>
                <p class="mb-4" style="color:#555;font-size:.96rem;line-height:1.8;"><?= htmlspecialchars($service['desc']) ?></p>
                <a href="https://tuqiohub.africa/register" target="_blank" rel="noopener" class="theme-btn btn-style-one me-3">
                    <span class="btn-title">Get Started</span>
                </a>
                <a href="<?= SITE_URL ?>/contact" class="theme-btn btn-style-two">
                    <span class="btn-title">Talk to Us</span>
                </a>
            </div>
            <div class="col-lg-6 wow fadeInRight">
                <img src="<?= SITE_URL ?>/assets/slides/<?= htmlspecialchars($service['image']) ?>"
                     alt="<?= htmlspecialchars($service['title']) ?>"
                     class="service-img">
            </div>
        </div>
    </div>
</section>

<!-- ── Features ── -->
<section class="service-features">
    <div class="auto-container">
        <div class="row">
            <div class="col-lg-5 mb-5 mb-lg-0">
                <span class="pre-tag">What's Included</span>
                <h2 class="section-heading mb-2">Key Features</h2>
                <p style="color:#777;font-size:.9rem;line-height:1.7;">Everything you need is built in — no third-party tools or technical expertise required.</p>
            </div>
            <div class="col-lg-7">
                <?php foreach ($service['features'] as $feat): ?>
                <div class="feature-check">
                    <div class="check-icon"><i class="fas fa-check"></i></div>
                    <p><?= htmlspecialchars($feat) ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<!-- ── Other Services ── -->
<section class="other-services">
    <div class="auto-container">
        <div class="text-center mb-5">
            <span class="pre-tag">Explore More</span>
            <h2 class="section-heading">Other Services</h2>
        </div>
        <div class="row justify-content-center">
            <?php foreach ($otherServices as $sid => $svc): ?>
            <div class="col-lg-2 col-md-4 col-sm-6 mb-4 wow fadeInUp">
                <a href="<?= SITE_URL ?>/service-detail?id=<?= $sid ?>" class="service-card">
                    <div class="sc-icon"><i class="fas <?= $svc['icon'] ?>"></i></div>
                    <h6><?= $svc['title'] ?></h6>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ── CTA ── -->
<section class="service-cta">
    <div class="auto-container text-center">
        <h2 style="color:#fff;font-weight:900;margin-bottom:14px;">Ready to use <?= htmlspecialchars($service['title']) ?>?</h2>
        <p style="color:rgba(255,255,255,.7);font-size:.95rem;margin-bottom:30px;max-width:480px;margin-left:auto;margin-right:auto;">Create your organizer account and get your event live in days, not weeks.</p>
        <a href="https://tuqiohub.africa/register" target="_blank" rel="noopener" class="theme-btn btn-style-one me-3">
            <span class="btn-title">Create Account</span>
        </a>
        <a href="<?= SITE_URL ?>/contact" class="theme-btn btn-style-two">
            <span class="btn-title">Contact Us</span>
        </a>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
</div>
<div class="scroll-to-top scroll-to-target" data-target="html"><span class="fa fa-angle-up"></span></div>
<?php include 'includes/footer-links.php'; ?>
</body>
</html>
