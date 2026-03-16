<?php
include 'config/config.php';
include 'libs/App.php';
$stats = tuqio_api('/api/public/stats');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">

<!-- SEO -->
<title>About Tuqio Hub | Kenya's Premier Event Platform</title>
<meta name="description" content="Learn about Tuqio Hub — Kenya's premier event management platform powering awards, conferences, nominations, voting, ticketing, and live polls.">
<meta name="keywords" content="about Tuqio Hub, Kenya event platform, awards management Kenya, event technology Nairobi, voting system Kenya">
<meta name="author" content="Tuqio Hub">
<meta name="robots" content="index, follow">
<link rel="canonical" href="https://tuqiohub.africa/about.php">

<!-- Schema.org microdata -->
<meta itemprop="name" content="About Tuqio Hub">
<meta itemprop="description" content="Kenya's premier event management platform powering awards, conferences, nominations, voting, ticketing, and live polls.">
<meta itemprop="image" content="<?= OG_IMAGE ?>">

<!-- Open Graph -->
<meta property="og:title" content="About Tuqio Hub | Kenya's Premier Event Platform">
<meta property="og:type" content="website">
<meta property="og:image" content="<?= OG_IMAGE ?>">
<meta property="og:image:type" content="image/webp">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:url" content="https://tuqiohub.africa/about.php">
<meta property="og:description" content="Kenya's premier event management platform powering awards, conferences, nominations, voting, ticketing, and live polls.">
<meta property="og:site_name" content="Tuqio Hub">

<!-- Twitter Card -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:site" content="@tuqiohub">
<meta name="twitter:title" content="About Tuqio Hub | Kenya's Premier Event Platform">
<meta name="twitter:description" content="Kenya's premier event management platform powering awards, conferences, nominations, voting, ticketing, and live polls.">
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
{"@context":"https://schema.org","@type":"BreadcrumbList","itemListElement":[{"@type":"ListItem","position":1,"name":"Home","item":"https://tuqiohub.africa/"},{"@type":"ListItem","position":2,"name":"About","item":"https://tuqiohub.africa/about.php"}]}
</script>

<!-- JSON-LD: AboutPage -->
<script type="application/ld+json">
{"@context":"https://schema.org","@type":"AboutPage","name":"About Tuqio Hub","url":"https://tuqiohub.africa/about.php","description":"Kenya's premier event management platform powering awards, conferences, nominations, voting, ticketing, and live polls."}
</script>
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
/* ── Section spacing ── */
.about-section   { padding: 80px 0; background: #fff; }
.about-section-alt { padding: 80px 0; background: #f9fafb; }
.about-cta       { padding: 0 0 80px; background: #f9fafb; }

/* ── Pre-tag label ── */
.pre-tag {
    display: inline-block;
    background: rgba(237,28,36,0.1);
    color: #ed1c24;
    font-size: .72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 2px;
    padding: 4px 14px;
    border-radius: 20px;
    margin-bottom: 14px;
}

/* ── Mission split ── */
.about-split { display: flex; min-height: 520px; }
.about-split .split-img {
    flex: 0 0 50%; width: 50%;
    background-size: cover;
    background-position: center;
}
.about-split .split-content {
    flex: 0 0 50%; width: 50%;
    padding: 80px 60px;
    background: #fff;
    display: flex; align-items: center;
}
@media (max-width: 991px) {
    .about-split { flex-direction: column; }
    .about-split .split-img  { width: 100%; min-height: 320px; }
    .about-split .split-content { width: 100%; padding: 50px 28px; }
}
.split-label {
    font-size: .72rem; text-transform: uppercase;
    letter-spacing: 2px; color: #ed1c24;
    font-weight: 700; margin-bottom: 16px;
}
.split-heading {
    font-size: 2rem; font-weight: 900;
    color: #1e1548; line-height: 1.25; margin-bottom: 20px;
}
.split-text { font-size: .95rem; color: #666; line-height: 1.85; margin-bottom: 14px; }

/* ── Stats ── */
.about-counter {
    padding: 90px 0;
    background-size: cover;
    background-position: center;
    position: relative;
}
.about-counter::before {
    content: '';
    position: absolute; inset: 0;
    background: rgba(21,16,46,0.78);
}
.about-counter .auto-container { position: relative; z-index: 1; }
.counter-col { text-align: center; padding: 20px 10px; }
.counter-col .count-text {
    display: block;
    font-size: 3.2rem; font-weight: 900;
    color: #fff; line-height: 1;
    margin-bottom: 8px;
}
.counter-col .counter-title {
    font-size: .78rem; text-transform: uppercase;
    letter-spacing: 2px; color: rgba(255,255,255,.6);
    display: block;
}
.counter-col .counter-icon {
    font-size: 2rem; color: #ed1c24;
    display: block; margin-bottom: 14px;
}
.counter-divider {
    width: 1px; background: rgba(255,255,255,.15);
    align-self: stretch; margin: 0 auto;
}

/* ── Section headings ── */
.section-heading { font-size: 2rem; font-weight: 800; color: #1e1548; margin-bottom: 10px; }
.section-sub     { font-size: .95rem; color: #777; max-width: 520px; margin: 0 auto; }


/* ── How It Works ── */
.step-wrap { text-align: center; padding: 10px; }
.step-num {
    width: 64px; height: 64px; border-radius: 50%;
    background: #ed1c24; color: #fff;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.4rem; font-weight: 900;
    margin: 0 auto 20px;
}
.step-wrap h5 { font-weight: 700; color: #1e1548; margin-bottom: 8px; }
.step-wrap p  { font-size: .88rem; color: #777; line-height: 1.7; }
.step-connector {
    display: flex; align-items: center; justify-content: center;
    padding-top: 20px; color: #ddd; font-size: 1.4rem;
}

/* ── Values ── */
.value-item { display: flex; gap: 16px; margin-bottom: 28px; align-items: flex-start; }
.value-icon {
    width: 46px; height: 46px; border-radius: 10px;
    background: rgba(237,28,36,0.1);
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0; font-size: 1.1rem; color: #ed1c24;
}
.value-item h6 { font-weight: 700; color: #1e1548; margin-bottom: 4px; }
.value-item p  { font-size: .85rem; color: #777; margin: 0; line-height: 1.6; }

/* ── CTA box ── */
.cta-box {
    background: linear-gradient(135deg, #ed1c24, #c41820);
    border-radius: 16px; padding: 48px 36px;
    color: #fff; text-align: center;
}
.cta-box h3 { font-weight: 900; margin-bottom: 14px; }
.cta-box p  { opacity: .88; font-size: .95rem; margin-bottom: 24px; line-height: 1.7; }
.cta-box .theme-btn { background: #fff; color: #ed1c24; border-color: #fff; display: block; text-align: center; }
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

<section class="page-title" style="background-image:url(<?= SITE_URL ?>/assets/slides/about.webp);">
    <div class="anim-icons full-width"><span class="icon icon-bull-eye"></span><span class="icon icon-dotted-circle"></span></div>
    <div class="auto-container">
        <div class="title-outer">
            <h1>About Tuqio Hub</h1>
            <ul class="page-breadcrumb">
                <li><a href="<?= SITE_URL ?>">Home</a></li>
                <li>About</li>
            </ul>
        </div>
    </div>
</section>

<!-- ── Mission ─────────────────────────────────────────────── -->
<div class="about-split wow fadeIn">
    <div class="split-img" style="background-image:url(<?= SITE_URL ?>/assets/slides/about.webp);"></div>
    <div class="split-content">
        <div>
            <div class="split-label">Who We Are</div>
            <h2 class="split-heading">Powering Kenya's Most Impactful Events</h2>
            <p class="split-text">Tuqio Hub is Kenya's premier event management and engagement platform — built to power award ceremonies, conferences, nominations, voting, ticketing, and community polls, all from one place.</p>
            <p class="split-text">From small community awards to large national conferences, we provide the infrastructure event organizers need to run professional, engaging, and transparent events.</p>
            <a href="<?= ADMIN_URL ?>" target="_blank" rel="noopener" class="theme-btn btn-style-one" style="margin-top:8px;">
                <span class="btn-title">Host an Event</span>
            </a>
        </div>
    </div>
</div>

<!-- ── Stats ────────────────────────────────────────────────── -->
<section class="about-counter" style="background-image:url(<?= SITE_URL ?>/assets/slides/kenya-breadcrump.webp);">
    <div class="auto-container">
        <div class="row">
            <?php
            $statsArr = [
                [(int)($stats['events'] ?? 0),          'Events Hosted',   'fa-calendar-check'],
                [(int)($stats['upcoming_events'] ?? 0), 'Upcoming Events', 'fa-clock'],
                [(int)($stats['nominees'] ?? 0),        'Total Nominees',  'fa-users'],
                [(int)($stats['total_votes'] ?? 0),     'Votes Cast',      'fa-vote-yea'],
            ];
            foreach ($statsArr as $idx => [$n,$l,$i]): ?>
            <div class="col-md-3 col-sm-6 counter-col wow zoomIn" data-wow-delay="<?= $idx * 150 ?>ms">
                <i class="fas <?= $i ?> counter-icon"></i>
                <span class="count-text" data-speed="3000" data-stop="<?= $n ?>"><?= $n ?></span>
                <span class="counter-title"><?= $l ?></span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ── Services ─────────────────────────────────────────────── -->
<section class="services-section">
    <div class="auto-container">
        <div class="sec-title text-center">
            <span class="sub-title">What We Offer</span>
            <h2>Everything Your Event Needs</h2>
            <span class="divider"></span>
            <p>Six powerful tools, one seamless platform — built for Kenyan event organizers.</p>
        </div>
        <div class="row">

            <div class="col-lg-4 col-md-6 col-sm-12 wow fadeInUp">
                <div class="service-card svc-bg-1">
                    <div class="service-content">
                        <div class="service-icon"><span class="flaticon-calendar-1"></span></div>
                        <h4>Event Management</h4>
                        <p>Ticketing, phases and registrations on one powerful platform</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 col-sm-12 wow fadeInUp" data-wow-delay="150ms">
                <div class="service-card svc-bg-2">
                    <div class="service-content">
                        <div class="service-icon"><span class="flaticon-trophy-1"></span></div>
                        <h4>Nominations &amp; Voting</h4>
                        <p>Collect nominees and run live public voting for your awards</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 col-sm-12 wow fadeInUp" data-wow-delay="300ms">
                <div class="service-card svc-bg-3">
                    <div class="service-content">
                        <div class="service-icon"><span class="flaticon-paint"></span></div>
                        <h4>Event Graphics &amp; Branding</h4>
                        <p>Branded microsites, banners and marketing design for your event</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 col-sm-12 wow fadeInUp">
                <div class="service-card svc-bg-4">
                    <div class="service-content">
                        <div class="service-icon"><span class="flaticon-location"></span></div>
                        <h4>Event Sourcing</h4>
                        <p>Venues, speakers and sponsors — we help you find the right fit</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 col-sm-12 wow fadeInUp" data-wow-delay="150ms">
                <div class="service-card svc-bg-5">
                    <div class="service-content">
                        <div class="service-icon"><span class="flaticon-thumbs-up"></span></div>
                        <h4>Live Audience Polls</h4>
                        <p>Real-time polls to engage and get feedback from your audience</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 col-sm-12 wow fadeInUp" data-wow-delay="300ms">
                <div class="service-card svc-bg-6">
                    <div class="service-content">
                        <div class="service-icon"><span class="flaticon-camera"></span></div>
                        <h4>Photography &amp; Gallery</h4>
                        <p>Professional event coverage and digital gallery management</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- ── How It Works ─────────────────────────────────────────── -->
<section class="about-section">
    <div class="auto-container">
        <div class="text-center mb-5">
            <span class="pre-tag">Getting Started</span>
            <h2 class="section-heading">How It Works</h2>
            <p class="section-sub">Three simple steps to launch your event on Tuqio Hub.</p>
        </div>
        <div class="row align-items-start">
            <div class="col-md-4 wow fadeInUp">
                <div class="step-wrap">
                    <div class="step-num">1</div>
                    <h5>Register &amp; Apply</h5>
                    <p>Create your organizer account and submit your event details. Our team reviews applications within 2 business days.</p>
                </div>
            </div>
            <div class="col-md-4 wow fadeInUp" data-wow-delay="0.15s">
                <div class="step-wrap">
                    <div class="step-num">2</div>
                    <h5>Onboarding &amp; Setup</h5>
                    <p>We'll walk you through configuring your event page, nominees, ticket types, polls, and gallery — everything ready before launch day.</p>
                </div>
            </div>
            <div class="col-md-4 wow fadeInUp" data-wow-delay="0.3s">
                <div class="step-wrap">
                    <div class="step-num">3</div>
                    <h5>Go Live &amp; Grow</h5>
                    <p>Your event goes live to the public. Monitor voting, ticket sales, and engagement in real-time from your organizer dashboard.</p>
                </div>
            </div>
        </div>
        <div class="text-center mt-5">
            <a href="https://tuqiohub.africa/register" target="_blank" rel="noopener" class="theme-btn btn-style-one">
                <span class="btn-title"><i class="fas fa-rocket me-2"></i>Get Started Today</span>
            </a>
        </div>
    </div>
</section>

<!-- ── Clients ───────────────────────────────────────────────── -->
<section class="clients-section-two">
    <div class="auto-container">
        <div class="text-center mb-4">
            <span class="pre-tag">Trusted By</span>
            <h2 class="section-heading">Our Partners &amp; Clients</h2>
        </div>
        <div class="sponsors-outer">
            <ul class="clients-carousel owl-carousel owl-theme default-nav">
                <li class="slide-item"><a href="#"><img src="<?= SITE_URL ?>/assets/images/clients/ikwa-logo-bright.jpeg" alt="IKWA Awards" style="height:80px;width:160px;object-fit:contain;"></a></li>
                <li class="slide-item"><a href="#"><img src="<?= SITE_URL ?>/assets/images/clients/acco.jpg" alt="ACCO" style="height:80px;width:160px;object-fit:contain;"></a></li>
                <li class="slide-item"><a href="#"><img src="<?= SITE_URL ?>/assets/images/clients/change-africa-foundation.jpeg" alt="Change Africa Foundation" style="height:80px;width:160px;object-fit:contain;"></a></li>
                <li class="slide-item"><a href="#"><img src="<?= SITE_URL ?>/assets/images/clients/delight-college.jpeg" alt="Delight College" style="height:80px;width:160px;object-fit:contain;"></a></li>
                <li class="slide-item"><a href="#"><img src="<?= SITE_URL ?>/assets/images/clients/elimisha-network.jpeg" alt="Elimisha Network" style="height:80px;width:160px;object-fit:contain;"></a></li>
                <li class="slide-item"><a href="#"><img src="<?= SITE_URL ?>/assets/images/clients/tuqio-hub.png" alt="Tuqio Hub" style="height:80px;width:160px;object-fit:contain;"></a></li>
            </ul>
        </div>
    </div>
</section>

<!-- ── Values + CTA ─────────────────────────────────────────── -->
<section class="about-section-alt">
    <div class="auto-container">
        <div class="row align-items-center">

            <div class="col-lg-6 mb-5 mb-lg-0 wow fadeInLeft">
                <span class="pre-tag">Our Values</span>
                <h2 class="section-heading mb-4">Built on Transparency &amp; Excellence</h2>
                <?php
                $values = [
                    ['fa-shield-alt', 'Transparency', 'Every vote, nomination, and ticket is tracked and verifiable. We believe in open, honest event management.'],
                    ['fa-bolt',       'Speed',         'Real-time data, live vote counts, and instant notifications keep organizers and participants always informed.'],
                    ['fa-lock',       'Security',      'Secure payments, protected voter data, and HTTPS-encrypted communication across the entire platform.'],
                    ['fa-heart',      'Community',     'We build for the communities we serve — Kenyan organizers, participants, and audience members.'],
                ];
                foreach ($values as [$icon,$title,$desc]): ?>
                <div class="value-item">
                    <div class="value-icon"><i class="fas <?= $icon ?>"></i></div>
                    <div>
                        <h6><?= $title ?></h6>
                        <p><?= $desc ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="col-lg-6 wow fadeInRight">
                <div class="cta-box">
                    <i class="fas fa-rocket mb-4" style="font-size:3rem;opacity:.8;display:block;"></i>
                    <h3>Ready to Host Your Event?</h3>
                    <p>Join the growing list of Kenyan event organizers who trust Tuqio Hub to power their events.</p>
                    <a href="https://tuqiohub.africa/register" target="_blank" rel="noopener" class="theme-btn btn-style-two cta-box-btn">
                        <span class="btn-title">Create Your Account</span>
                    </a>
                </div>
            </div>

        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
</div>
<div class="scroll-to-top scroll-to-target" data-target="html"><span class="fa fa-angle-up"></span></div>
<?php include 'includes/footer-links.php'; ?>
</body>
</html>
