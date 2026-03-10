<?php
include 'config/config.php';
include 'libs/App.php';

$eventsResp  = tuqio_api('/api/public/events');
$allEvents   = $eventsResp['data'] ?? [];
$upcoming    = array_values(array_filter($allEvents, fn($e) => ($e['status'] ?? '') !== 'past'));
usort($upcoming, fn($a, $b) =>
    (!empty($b['banner_image']) || !empty($b['thumbnail_image'])) <=> (!empty($a['banner_image']) || !empty($a['thumbnail_image'])));

$pollsResp  = tuqio_api('/api/public/polls');
$polls      = array_slice($pollsResp['data'] ?? [], 0, 2);

$blogResp   = tuqio_api('/api/public/blog');
$blogPosts  = array_slice($blogResp['data'] ?? [], 0, 3);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">

<!-- SEO -->
<title>Home | Tuqio Hub</title>
<meta name="description" content="Tuqio Hub — Kenya's premier event management platform powering nominations, voting, ticketing, and live events. Discover upcoming events and cast your vote.">
<meta name="keywords" content="Tuqio Hub, Kenya events, event management, nominations Kenya, voting platform, awards Kenya, live events Nairobi">
<meta name="author" content="Tuqio Hub">
<meta name="robots" content="index, follow">
<link rel="canonical" href="https://tuqio.independentkenyawomenawards.com/">

<!-- Schema.org microdata -->
<meta itemprop="name" content="Tuqio Hub — Kenya's Premier Event Hub">
<meta itemprop="description" content="Kenya's premier event management platform powering nominations, voting, ticketing, and live events.">
<meta itemprop="image" content="<?= OG_IMAGE ?>">

<!-- Open Graph -->
<meta property="og:title" content="Home | Tuqio Hub">
<meta property="og:type" content="website">
<meta property="og:image" content="<?= OG_IMAGE ?>">
<meta property="og:image:type" content="image/webp">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:url" content="https://tuqio.independentkenyawomenawards.com/">
<meta property="og:description" content="Kenya's premier event management platform powering nominations, voting, ticketing, and live events.">
<meta property="og:site_name" content="Tuqio Hub">

<!-- Twitter Card -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:site" content="@tuqiohub">
<meta name="twitter:title" content="Home | Tuqio Hub">
<meta name="twitter:description" content="Kenya's premier event management platform powering nominations, voting, ticketing, and live events.">
<meta name="twitter:image" content="<?= OG_IMAGE ?>">

<!-- Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-XXXXXXXXXX"></script>
<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','G-XXXXXXXXXX');</script>

<!-- JSON-LD: Organization -->
<script type="application/ld+json">
{"@context":"https://schema.org/","@type":"Organization","name":"Tuqio Hub","url":"https://tuqio.independentkenyawomenawards.com","description":"Kenya's premier event management and awards platform powering nominations, voting, ticketing, and live events.","contactPoint":{"@type":"ContactPoint","telephone":"+254757140682","email":"tuqio@independentkenyawomenawards.com","contactType":"customer support"},"sameAs":["https://www.instagram.com/tuqiohub","https://www.facebook.com/tuqiohub","https://twitter.com/tuqiohub"]}
</script>

<!-- JSON-LD: WebSite -->
<script type="application/ld+json">
{"@context":"https://schema.org","@type":"WebSite","name":"Tuqio Hub","url":"https://tuqio.independentkenyawomenawards.com","description":"Kenya's premier event management platform powering nominations, voting, ticketing, and live events.","potentialAction":{"@type":"SearchAction","target":"https://tuqio.independentkenyawomenawards.com/events.php?q={search_term_string}","query-input":"required name=search_term_string"}}
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
<link href="<?= SITE_URL ?>/assets/css/color-switcher-design.css" rel="stylesheet">
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


    <!-- Banner Section -->

    <section class="banner-section">

        <div class="banner-carousel owl-carousel owl-theme">

            <!-- Slide 1: Discover Events -->

            <div class="slide-item slide-bg-1">

                <div class="auto-container">

                    <div class="content-box">

                        <span class="title animate-1">Kenya's Premier Event Hub</span>

                        <h2 class="animate-2">Discover, Attend &amp; Experience <br>Kenya's Best Events</h2>

                        <div class="text animate-3">From award galas to conferences — find, vote and engage with events that matter to you</div>

                        <div class="btn-box animate-5">
                            <a href="<?= SITE_URL ?>/events" class="theme-btn btn-style-two"><span class="btn-title">Browse Events</span></a>
                            <a href="<?= SITE_URL ?>/become-organizer" class="theme-btn btn-style-one"><span class="btn-title">Host an Event</span></a>
                        </div>

                    </div>

                </div>

            </div>

            <!-- Slide 2: Nominations & Voting -->

            <div class="slide-item slide-bg-2">

                <div class="auto-container">

                    <div class="content-box">

                        <span class="title animate-1">Nominations &amp; Live Voting</span>

                        <h2 class="animate-2">Vote for Your Favourites. <br>Shape the Outcome.</h2>

                        <div class="text animate-3">Nominate outstanding individuals and cast your vote — your voice decides the winners</div>

                        <div class="btn-box animate-5">
                            <a href="<?= SITE_URL ?>/nominees" class="theme-btn btn-style-two"><span class="btn-title">View Nominees</span></a>
                            <a href="<?= SITE_URL ?>/nominate" class="theme-btn btn-style-one"><span class="btn-title">Nominate Now</span></a>
                        </div>

                    </div>

                </div>

            </div>

            <!-- Slide 3: Host an Event -->

            <div class="slide-item slide-bg-3">

                <div class="auto-container">

                    <div class="content-box">

                        <span class="title animate-1">Built for Event Organizers</span>

                        <h2 class="animate-2">Everything You Need to Run <br>a Successful Event</h2>

                        <div class="text animate-3">Ticketing, voting, nominations, polls &amp; galleries — all from one powerful platform</div>

                        <div class="btn-box animate-5">
                            <a href="<?= SITE_URL ?>/about" class="theme-btn btn-style-two"><span class="btn-title">Learn More</span></a>
                            <a href="<?= SITE_URL ?>/become-organizer" class="theme-btn btn-style-one"><span class="btn-title">Get Started</span></a>
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>

    <!--End Banner Section -->


<!-- ══ 2. UPCOMING EVENTS ═════════════════════════════ -->
<?php if (!empty($upcoming)): ?>
<section class="news-section">
    <div class="anim-icons full-width">
        <span class="icon icon-circle-1 wow zoomIn"></span>
        <span class="icon icon-dotted-circle wow zoomIn" data-wow-delay="400ms"></span>
    </div>
    <div class="auto-container">
        <div class="sec-title text-center">
            <span class="sub-title">What's On</span>
            <h2>Upcoming Events</h2>
            <span class="divider"></span>
        </div>
        <div class="row">
            <?php foreach (array_slice($upcoming, 0, 3) as $i => $ev):
                $hasImage = !empty($ev['banner_image']) || !empty($ev['thumbnail_image']);
                $banner   = !empty($ev['banner_image']) ? API_STORAGE . $ev['banner_image']
                          : (!empty($ev['thumbnail_image']) ? API_STORAGE . $ev['thumbnail_image'] : '');
                $phase  = $ev['current_phase'] ?? '';
                $phaseLabels = ['voting' => 'Voting Open', 'on_sale' => 'Tickets On Sale', 'nomination' => 'Nominations Open', 'upcoming' => 'Upcoming'];
                $phaseLabel  = $phaseLabels[$phase] ?? '';
            ?>
            <div class="news-block style-four col-lg-4 col-md-6 col-sm-12 wow fadeInUp" data-wow-delay="<?= ($i % 3) * 150 ?>ms">
                <div class="inner-box">
                    <div class="image-box">
                        <?php if ($phaseLabel): ?>
                        <span class="tag event-phase-tag event-phase-<?= htmlspecialchars($phase) ?>"><?= $phaseLabel ?></span>
                        <?php endif; ?>
                        <?php if ($hasImage): ?>
                        <figure class="image">
                            <a href="<?= SITE_URL ?>/event-detail?slug=<?= urlencode($ev['slug']) ?>">
                                <img src="<?= htmlspecialchars($banner) ?>"
                                     alt="<?= htmlspecialchars($ev['name']) ?>"
                                     class="event-card-img"
                                     onerror="tuqioImgErr(this)">
                            </a>
                        </figure>
                        <?php else: ?>
                        <a href="<?= SITE_URL ?>/event-detail?slug=<?= urlencode($ev['slug']) ?>" style="display:block;">
                            <div style="height:220px;background:linear-gradient(135deg,#1e1548,#2d1f6b);display:flex;align-items:center;justify-content:center;">
                                <i class="fas fa-calendar-alt" style="font-size:3rem;color:rgba(255,255,255,0.2);"></i>
                            </div>
                        </a>
                        <?php endif; ?>
                    </div>
                    <div class="lower-content">
                        <ul class="post-info">
                            <?php if (!empty($ev['start_date'])): ?>
                            <li><span class="far fa-calendar-alt"></span> <?= date('d M Y', strtotime($ev['start_date'])) ?></li>
                            <?php endif; ?>
                            <?php if (!empty($ev['venue_city'])): ?>
                            <li><span class="fas fa-map-marker-alt"></span> <?= htmlspecialchars($ev['venue_city']) ?></li>
                            <?php endif; ?>
                        </ul>
                        <h4><a href="<?= SITE_URL ?>/event-detail?slug=<?= urlencode($ev['slug']) ?>"><?= htmlspecialchars($ev['name']) ?></a></h4>
                        <?php if (!empty($ev['tagline'])): ?>
                        <div class="text"><?= htmlspecialchars($ev['tagline']) ?></div>
                        <?php elseif (!empty($ev['short_description'])): ?>
                        <div class="text"><?= htmlspecialchars(mb_substr($ev['short_description'], 0, 110)) ?>...</div>
                        <?php endif; ?>
                        <div class="btn-box event-card-btn">
                            <a href="<?= SITE_URL ?>/event-detail?slug=<?= urlencode($ev['slug']) ?>" class="theme-btn btn-style-one"><span class="btn-title">View Details</span></a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="sec-bottom-text">
            <div class="text">More events on the platform. <a href="<?= SITE_URL ?>/events">Browse all events →</a></div>
        </div>
    </div>
</section>
<?php endif; ?>


<!-- ══ 4. POLLS SECTION ══════════════════════════════ -->
<?php if (!empty($polls)): ?>
<section class="about-section-two">
    <div class="auto-container">
        <div class="sec-title text-center">
            <span class="sub-title">Have Your Say</span>
            <h2>Live Polls</h2>
            <span class="divider"></span>
            <p>Cast your vote on the hottest topics. Results update in real time.</p>
        </div>
        <div class="row justify-content-center">
            <?php foreach ($polls as $poll):
                $totalVotes = (int)($poll['total_votes'] ?? array_sum(array_column($poll['options'] ?? [], 'vote_count')));
            ?>
            <div class="col-lg-5 col-md-6 col-sm-12 wow fadeInUp poll-col">
                <div class="inner-column">
                    <h4><?= htmlspecialchars($poll['title'] ?? $poll['question'] ?? '') ?></h4>
                    <?php if (!empty($poll['description'])): ?>
                    <p class="poll-desc"><?= htmlspecialchars($poll['description']) ?></p>
                    <?php endif; ?>
                    <form class="poll-form" data-poll-id="<?= (int)$poll['id'] ?>">
                        <?php foreach ($poll['options'] as $opt):
                            $pct = $totalVotes > 0 ? round(($opt['vote_count'] / $totalVotes) * 100) : ($opt['percentage'] ?? 0);
                        ?>
                        <div class="schedule-block-two">
                            <div class="inner-box">
                                <label class="poll-option-label">
                                    <input type="radio" name="option_<?= $poll['id'] ?>" value="<?= (int)$opt['id'] ?>">
                                    <span class="poll-option-text"><?= htmlspecialchars($opt['text'] ?? $opt['option_text'] ?? '') ?></span>
                                    <span class="poll-option-pct"><?= $pct ?>%</span>
                                </label>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <div class="btn-box poll-btn-box">
                            <button type="submit" class="theme-btn btn-style-one"><span class="btn-title">Submit Vote</span></button>
                        </div>
                        <div class="poll-feedback"></div>
                    </form>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="sec-bottom-text">
            <div class="text"><a href="<?= SITE_URL ?>/polls">View all polls →</a></div>
        </div>
    </div>
</section>
<?php endif; ?>


<!-- ══ 5. HOW IT WORKS ═══════════════════════════════ -->
<section class="feature-section">
    <div class="anim-icons full-width">
        <span class="icon icon-circle-1 wow zoomIn"></span>
        <span class="icon icon-dotted-circle wow zoomIn" data-wow-delay="400ms"></span>
    </div>
    <div class="auto-container">
        <div class="sec-title text-center">
            <span class="sub-title">Simple Steps</span>
            <h2>How Tuqio Works</h2>
            <span class="divider"></span>
        </div>
        <div class="row">
            <div class="feature-block col-lg-4 col-md-6 col-sm-12 wow fadeInUp">
                <div class="inner-box">
                    <div class="icon-box">
                        <div class="icon"><span class="flaticon-calendar-1"></span></div>
                    </div>
                    <h4>1. Browse Events</h4>
                    <p>Explore upcoming awards galas, conferences, concerts and summits happening across Kenya. Find what interests you.</p>
                    <a href="<?= SITE_URL ?>/events" class="read-more">Browse Events <span class="fa fa-arrow-right"></span></a>
                </div>
            </div>
            <div class="feature-block col-lg-4 col-md-6 col-sm-12 wow fadeInUp" data-wow-delay="300ms">
                <div class="inner-box">
                    <div class="icon-box">
                        <div class="icon"><span class="flaticon-ticket"></span></div>
                    </div>
                    <h4>2. Get Tickets &amp; Vote</h4>
                    <p>Buy your event tickets online and vote for your favourite nominees. Your engagement shapes the outcome of the event.</p>
                    <a href="<?= SITE_URL ?>/events" class="read-more">Get Tickets <span class="fa fa-arrow-right"></span></a>
                </div>
            </div>
            <div class="feature-block col-lg-4 col-md-6 col-sm-12 wow fadeInUp" data-wow-delay="600ms">
                <div class="inner-box">
                    <div class="icon-box">
                        <div class="icon"><span class="flaticon-trophy-1"></span></div>
                    </div>
                    <h4>3. Attend &amp; Celebrate</h4>
                    <p>Show up on the day, watch the awards unfold live, and be part of Kenya's most exciting event experiences.</p>
                    <a href="<?= SITE_URL ?>/about" class="read-more">Learn More <span class="fa fa-arrow-right"></span></a>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- ══ 5b. OUR SERVICES ══════════════════════════════ -->
<section class="services-section">
    <div class="auto-container">
        <div class="sec-title text-center">
            <span class="sub-title">What We Offer</span>
            <h2>Tuqio Services</h2>
            <span class="divider"></span>
            <p>Everything you need to plan, run and grow your event — on one platform.</p>
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

            <div class="col-lg-4 col-md-6 col-sm-12 wow fadeInUp" data-wow-delay="0ms">
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


<!-- ══ 6. RECENT BLOG POSTS ══════════════════════════ -->
<?php if (!empty($blogPosts)): ?>
<section class="news-section">
    <div class="auto-container">
        <div class="sec-title">
            <span class="sub-title">News &amp; Insights</span>
            <h2>Latest from Tuqio</h2>
            <span class="divider"></span>
        </div>
        <div class="row">
            <?php $delays = ['', '400ms', '800ms']; ?>
            <?php foreach ($blogPosts as $i => $post): ?>
            <div class="news-block col-lg-4 col-md-6 col-sm-12 wow fadeInRight"
                 <?= !empty($delays[$i]) ? 'data-wow-delay="'.$delays[$i].'"' : '' ?>>
                <div class="inner-box">
                    <div class="image-box">
                        <?php if (!empty($post['category']['name'])): ?>
                        <span class="tag"><?= htmlspecialchars($post['category']['name']) ?></span>
                        <?php endif; ?>
                        <figure class="image">
                            <a href="<?= SITE_URL ?>/blog-single?slug=<?= urlencode($post['slug']) ?>">
                                <img src="<?= htmlspecialchars($post['featured_image'] ?? SITE_URL.'/assets/slides/event.webp') ?>"
                                     alt="<?= htmlspecialchars($post['title']) ?>"
                                     onerror="this.src='<?= SITE_URL ?>/assets/slides/event.webp'"
                                     class="news-thumb-img">
                            </a>
                        </figure>
                    </div>
                    <div class="lower-content">
                        <div class="author">
                            <figure class="thumb">
                                <img src="<?= SITE_URL ?>/assets/images/logo/tuqio-logo.png" alt="Tuqio">
                            </figure>
                            <h5 class="name"><?= htmlspecialchars($post['author']['name'] ?? 'Tuqio Hub') ?></h5>
                        </div>
                        <h4><a href="<?= SITE_URL ?>/blog-single?slug=<?= urlencode($post['slug']) ?>"><?= htmlspecialchars(mb_strimwidth($post['title'], 0, 65, '…')) ?></a></h4>
                        <?php if (!empty($post['excerpt'])): ?>
                        <div class="text"><?= htmlspecialchars(mb_strimwidth($post['excerpt'], 0, 100, '…')) ?></div>
                        <?php endif; ?>
                        <ul class="post-info">
                            <?php if (!empty($post['published_at'])): ?>
                            <li><span class="far fa-calendar"></span> <?= date('d M Y', strtotime($post['published_at'])) ?></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="sec-bottom-text">
            <div class="text">Want to read more? <a href="<?= SITE_URL ?>/blog">View all articles →</a></div>
        </div>
    </div>
</section>
<?php endif; ?>


<!-- ══ 7. CALL TO ACTION — BECOME AN ORGANIZER ═══════ -->
<section class="call-to-action-two">
    <div class="auto-container">
        <div class="row">
            <div class="content-column col-lg-9 col-md-12 col-sm-12">
                <div class="content-box">
                    <span class="sub-title">For Organizers</span>
                    <h2>Ready to host your event on Tuqio?</h2>
                    <div class="text">Join Kenya's leading event platform. Get ticketing, voting, nominations, polls and a branded microsite — all in one place.</div>
                    <div class="btn-box">
                        <a href="<?= ADMIN_URL ?>" target="_blank" class="theme-btn btn-style-one"><span class="btn-title">Start Hosting on Tuqio →</span></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- Partners -->
<section class="clients-section-two">
    <div class="auto-container">
        <div class="sponsors-outer">
            <ul class="clients-carousel owl-carousel owl-theme default-nav">
                <li class="slide-item"><a href="#"><img src="<?= SITE_URL ?>/assets/images/clients/ikwa-logo-bright.jpeg" alt="IKWA Awards" style="height:80px; width:160px; object-fit:contain;"></a></li>
                <li class="slide-item"><a href="#"><img src="<?= SITE_URL ?>/assets/images/clients/acco.jpg" alt="ACCO" style="height:80px; width:160px; object-fit:contain;"></a></li>
                <li class="slide-item"><a href="#"><img src="<?= SITE_URL ?>/assets/images/clients/change-africa-foundation.jpeg" alt="Change Africa Foundation" style="height:80px; width:160px; object-fit:contain;"></a></li>
                <li class="slide-item"><a href="#"><img src="<?= SITE_URL ?>/assets/images/clients/delight-college.jpeg" alt="Delight College" style="height:80px; width:160px; object-fit:contain;"></a></li>
                <li class="slide-item"><a href="#"><img src="<?= SITE_URL ?>/assets/images/clients/elimisha-network.jpeg" alt="Elimisha Network" style="height:80px; width:160px; object-fit:contain;"></a></li>
                <li class="slide-item"><a href="#"><img src="<?= SITE_URL ?>/assets/images/clients/tuqio-hub.png" alt="Tuqio Hub" style="height:80px; width:160px; object-fit:contain;"></a></li>
                <li class="slide-item"><a href="#"><img src="<?= SITE_URL ?>/assets/images/clients/ikwa-logo-bright.jpeg" alt="IKWA Awards" style="height:80px; width:160px; object-fit:contain;"></a></li>
                <li class="slide-item"><a href="#"><img src="<?= SITE_URL ?>/assets/images/clients/acco.jpg" alt="ACCO" style="height:80px; width:160px; object-fit:contain;"></a></li>
                <li class="slide-item"><a href="#"><img src="<?= SITE_URL ?>/assets/images/clients/change-africa-foundation.jpeg" alt="Change Africa Foundation" style="height:80px; width:160px; object-fit:contain;"></a></li>
                <li class="slide-item"><a href="#"><img src="<?= SITE_URL ?>/assets/images/clients/delight-college.jpeg" alt="Delight College" style="height:80px; width:160px; object-fit:contain;"></a></li>
            </ul>
        </div>
    </div>
</section>


<?php include 'includes/footer.php'; ?>
</div>

<div class="scroll-to-top scroll-to-target" data-target="html"><span class="fa fa-angle-up"></span></div>
<?php include 'includes/footer-links.php'; ?>

<script>
function tuqioImgErr(el) {
    el.onerror = null;
    el.parentElement.innerHTML = '<div style="height:100%;min-height:220px;background:linear-gradient(135deg,#1e1548,#2d1f6b);display:flex;align-items:center;justify-content:center;"><i class="fas fa-calendar-alt" style="font-size:3rem;color:rgba(255,255,255,0.2);"></i></div>';
}
document.querySelectorAll('.poll-form').forEach(function(form) {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        var pollId   = this.dataset.pollId;
        var selected = this.querySelector('input[type=radio]:checked');
        var feedback = this.querySelector('.poll-feedback');
        if (!selected) { feedback.style.display='block'; feedback.textContent='Please select an option.'; return; }
        fetch('<?= API_BASE ?>/api/public/polls/' + pollId + '/respond', {
            method: 'POST',
            headers: {'Content-Type':'application/json','Accept':'application/json'},
            body: JSON.stringify({option_id: parseInt(selected.value)})
        })
        .then(function(r){ return r.json(); })
        .then(function(res){ feedback.style.display='block'; feedback.textContent=res.message||'Vote recorded!'; })
        .catch(function(){ feedback.style.display='block'; feedback.textContent='Could not submit vote.'; });
    });
});
</script>
</body>
</html>
