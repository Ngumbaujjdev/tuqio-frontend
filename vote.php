<?php
include 'config/config.php';
include 'libs/App.php';

// Fetch all events, filter to those with voting open
$eventsResp = tuqio_api('/api/public/events');
$allEvents  = $eventsResp['data'] ?? [];
$voteEvents = array_filter($allEvents, fn($e) => ($e['current_phase'] ?? '') === 'voting' || !empty($e['voting_is_open']));
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">

<!-- SEO -->
<title>Cast Your Vote | Tuqio Hub</title>
<meta name="description" content="Cast your vote for your favourite nominees on Tuqio Hub. Voting is open — support outstanding Kenyan talent and excellence.">
<meta name="keywords" content="vote Kenya awards, cast your vote, Kenya nominees voting, Tuqio Hub vote, online voting Kenya">
<meta name="author" content="Tuqio Hub">
<meta name="robots" content="index, follow">
<link rel="canonical" href="https://tuqio.independentkenyawomenawards.com/vote.php">

<!-- Schema.org microdata -->
<meta itemprop="name" content="Cast Your Vote | Tuqio Hub">
<meta itemprop="description" content="Cast your vote for your favourite nominees on Tuqio Hub.">
<meta itemprop="image" content="<?= OG_IMAGE ?>">

<!-- Open Graph -->
<meta property="og:title" content="Cast Your Vote | Tuqio Hub">
<meta property="og:type" content="website">
<meta property="og:image" content="<?= OG_IMAGE ?>">
<meta property="og:image:type" content="image/webp">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:url" content="https://tuqio.independentkenyawomenawards.com/vote.php">
<meta property="og:description" content="Cast your vote for your favourite nominees on Tuqio Hub. Voting is open now.">
<meta property="og:site_name" content="Tuqio Hub">

<!-- Twitter Card -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:site" content="@tuqiohub">
<meta name="twitter:title" content="Cast Your Vote | Tuqio Hub">
<meta name="twitter:description" content="Cast your vote for your favourite nominees on Tuqio Hub. Voting is open now.">
<meta name="twitter:image" content="<?= OG_IMAGE ?>">

<!-- Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-XXXXXXXXXX"></script>
<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','G-XXXXXXXXXX');</script>

<!-- JSON-LD: Organization -->
<script type="application/ld+json">
{"@context":"https://schema.org/","@type":"Organization","name":"Tuqio Hub","url":"https://tuqio.independentkenyawomenawards.com","description":"Kenya's premier event management and awards platform.","contactPoint":{"@type":"ContactPoint","telephone":"+254757140682","email":"tuqio@independentkenyawomenawards.com","contactType":"customer support"},"sameAs":["https://www.instagram.com/tuqiohub","https://www.facebook.com/tuqiohub","https://twitter.com/tuqiohub"]}
</script>

<!-- JSON-LD: BreadcrumbList -->
<script type="application/ld+json">
{"@context":"https://schema.org","@type":"BreadcrumbList","itemListElement":[{"@type":"ListItem","position":1,"name":"Home","item":"https://tuqio.independentkenyawomenawards.com/"},{"@type":"ListItem","position":2,"name":"Vote","item":"https://tuqio.independentkenyawomenawards.com/vote.php"}]}
</script>

<!-- JSON-LD: WebPage -->
<script type="application/ld+json">
{"@context":"https://schema.org","@type":"WebPage","name":"Cast Your Vote | Tuqio Hub","url":"https://tuqio.independentkenyawomenawards.com/vote.php","description":"Cast your vote for your favourite nominees on Tuqio Hub."}
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

<section class="page-title" style="background-image:url(<?= SITE_URL ?>/assets/slides/kenya-breadcrump.webp);">
    <div class="anim-icons full-width"><span class="icon icon-bull-eye"></span><span class="icon icon-dotted-circle"></span></div>
    <div class="auto-container">
        <div class="title-outer">
            <h1>Vote Now</h1>
            <ul class="page-breadcrumb">
                <li><a href="<?= SITE_URL ?>">Home</a></li>
                <li>Vote</li>
            </ul>
        </div>
    </div>
</section>

<section style="padding:70px 0;background:#f9fafb;">
    <div class="auto-container">

        <?php if (empty($voteEvents)): ?>
        <!-- No active voting -->
        <div class="text-center" style="padding:80px 0;">
            <i class="fas fa-vote-yea" style="font-size:3.5rem;color:#1e1548;opacity:.15;"></i>
            <h3 style="margin-top:24px;color:#1e1548;font-weight:800;">No Active Voting Right Now</h3>
            <p class="text-muted" style="max-width:460px;margin:14px auto 28px;">Voting windows open and close with each event. Check back soon or browse all events to see what's coming up.</p>
            <div style="display:flex;justify-content:center;gap:14px;flex-wrap:wrap;">
                <a href="events" class="theme-btn btn-style-one">
                    <span class="btn-title"><i class="fas fa-calendar-alt me-2"></i>Browse Events</span>
                </a>
                <a href="nominees" class="theme-btn btn-style-two">
                    <span class="btn-title"><i class="fas fa-users me-2"></i>View Nominees</span>
                </a>
            </div>

            <!-- All events with past voting as reference -->
            <?php
            $pastVoted = array_filter($allEvents, fn($e) => ($e['current_phase'] ?? '') === 'ended' || ($e['current_phase'] ?? '') === 'closed');
            if (!empty($pastVoted)):
            ?>
            <div style="margin-top:60px;text-align:left;max-width:600px;margin-left:auto;margin-right:auto;">
                <h5 style="font-weight:700;color:#1e1548;margin-bottom:20px;">Past Voting Events</h5>
                <?php foreach ($pastVoted as $ev): ?>
                <div style="display:flex;align-items:center;gap:16px;background:#fff;border-radius:10px;padding:16px;margin-bottom:12px;box-shadow:0 2px 10px rgba(0,0,0,0.05);">
                    <div style="flex:1;">
                        <div style="font-weight:700;color:#1e1548;font-size:.95rem;"><?= htmlspecialchars($ev['name']) ?></div>
                        <?php if (!empty($ev['date_start'])): ?>
                        <div style="font-size:.78rem;color:#aaa;margin-top:3px;"><i class="fas fa-calendar me-1" style="color:#ed1c24;"></i><?= date('M Y', strtotime($ev['date_start'])) ?></div>
                        <?php endif; ?>
                    </div>
                    <a href="nominees?event=<?= urlencode($ev['slug']) ?>" class="theme-btn btn-style-two" style="font-size:.8rem;padding:8px 16px;">
                        <span class="btn-title">View Results</span>
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <?php else: ?>

        <div class="text-center" style="margin-bottom:50px;">
            <span style="font-size:.8rem;font-weight:700;letter-spacing:2px;color:#ed1c24;text-transform:uppercase;">Voting Now Open</span>
            <h2 style="font-weight:800;color:#1e1548;margin-top:8px;">Cast Your Vote</h2>
            <p style="color:#777;max-width:520px;margin:0 auto;">Select an event below and vote for your favourite nominees. Your vote counts!</p>
        </div>

        <div class="row gy-4 justify-content-center">
            <?php foreach ($voteEvents as $ev): ?>
            <?php
                $banner      = !empty($ev['banner']) ? $ev['banner'] : SITE_URL . '/assets/slides/event.webp';
                $closeDate   = $ev['voting_close'] ?? $ev['date_end'] ?? null;
                $categoryCount = $ev['categories_count'] ?? null;
                $nomineeCount  = $ev['nominees_count'] ?? null;
            ?>
            <div class="col-lg-5 col-md-6 wow fadeInUp">
                <div class="vote-event-card">
                    <div class="card-banner">
                        <img src="<?= htmlspecialchars($banner) ?>" alt="<?= htmlspecialchars($ev['name']) ?>" onerror="this.src='<?= SITE_URL ?>/assets/slides/event.webp'">
                        <span class="live-badge">Voting Live</span>
                    </div>
                    <div class="card-body-inner">
                        <?php if (!empty($ev['category'])): ?>
                        <span class="cat-pill"><?= htmlspecialchars($ev['category']) ?></span>
                        <?php endif; ?>
                        <h5><?= htmlspecialchars($ev['name']) ?></h5>
                        <div class="meta">
                            <?php if (!empty($ev['date_start'])): ?>
                            <span><i class="fas fa-calendar-alt"></i><?= date('d M Y', strtotime($ev['date_start'])) ?></span>
                            <?php endif; ?>
                            <?php if (!empty($ev['venue'])): ?>
                            <span><i class="fas fa-map-marker-alt"></i><?= htmlspecialchars($ev['venue']) ?></span>
                            <?php endif; ?>
                            <?php if ($nomineeCount): ?>
                            <span><i class="fas fa-users"></i><?= $nomineeCount ?> nominees</span>
                            <?php endif; ?>
                        </div>

                        <?php if ($closeDate): ?>
                        <div style="margin-bottom:14px;">
                            <div style="font-size:.75rem;color:#999;margin-bottom:8px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;">Voting closes in</div>
                            <div class="countdown-mini" data-deadline="<?= htmlspecialchars($closeDate) ?>">
                                <div class="cd-box"><span class="num cd-days">--</span><span class="lbl">Days</span></div>
                                <div class="cd-box"><span class="num cd-hrs">--</span><span class="lbl">Hrs</span></div>
                                <div class="cd-box"><span class="num cd-min">--</span><span class="lbl">Min</span></div>
                                <div class="cd-box"><span class="num cd-sec">--</span><span class="lbl">Sec</span></div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if (!empty($ev['short_description']) || !empty($ev['description'])): ?>
                        <p class="desc"><?= htmlspecialchars(mb_strimwidth(strip_tags($ev['short_description'] ?? $ev['description'] ?? ''), 0, 130, '…')) ?></p>
                        <?php endif; ?>

                        <a href="nominees?event=<?= urlencode($ev['slug']) ?>" class="theme-btn btn-style-one" style="text-align:center;display:block;">
                            <span class="btn-title"><i class="fas fa-vote-yea me-2"></i>Vote for Nominees</span>
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Info strip -->
        <div style="background:#fff;border-radius:14px;padding:30px 36px;margin-top:60px;box-shadow:0 4px 20px rgba(0,0,0,0.05);">
            <div class="row align-items-center gy-3">
                <div class="col-lg-7">
                    <h5 style="font-weight:800;color:#1e1548;margin-bottom:8px;"><i class="fas fa-info-circle me-2" style="color:#ed1c24;"></i>How Voting Works</h5>
                    <ul style="list-style:none;padding:0;margin:0;display:flex;flex-wrap:wrap;gap:14px 28px;">
                        <li style="font-size:.85rem;color:#555;"><i class="fas fa-check-circle me-2" style="color:#10b981;"></i>Select your event above</li>
                        <li style="font-size:.85rem;color:#555;"><i class="fas fa-check-circle me-2" style="color:#10b981;"></i>Browse nominees by category</li>
                        <li style="font-size:.85rem;color:#555;"><i class="fas fa-check-circle me-2" style="color:#10b981;"></i>Click "Vote" on your choice</li>
                        <li style="font-size:.85rem;color:#555;"><i class="fas fa-check-circle me-2" style="color:#10b981;"></i>Watch live vote counts update</li>
                    </ul>
                </div>
                <div class="col-lg-5 text-lg-end">
                    <a href="faq#voting" class="theme-btn btn-style-two" style="font-size:.88rem;">
                        <span class="btn-title"><i class="fas fa-question-circle me-2"></i>Voting FAQ</span>
                    </a>
                </div>
            </div>
        </div>

        <?php endif; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
</div>
<div class="scroll-to-top scroll-to-target" data-target="html"><span class="fa fa-angle-up"></span></div>
<?php include 'includes/footer-links.php'; ?>
<script>
// Countdown timers for each voting event card
document.querySelectorAll('.countdown-mini[data-deadline]').forEach(function(wrap) {
    var deadline = new Date(wrap.dataset.deadline).getTime();
    function tick() {
        var now  = Date.now();
        var diff = deadline - now;
        if (diff <= 0) {
            wrap.innerHTML = '<span style="font-size:.85rem;color:#ed1c24;font-weight:600;">Voting closed</span>';
            return;
        }
        var d = Math.floor(diff / 86400000);
        var h = Math.floor((diff % 86400000) / 3600000);
        var m = Math.floor((diff % 3600000)  / 60000);
        var s = Math.floor((diff % 60000)    / 1000);
        wrap.querySelector('.cd-days').textContent = String(d).padStart(2,'0');
        wrap.querySelector('.cd-hrs').textContent  = String(h).padStart(2,'0');
        wrap.querySelector('.cd-min').textContent  = String(m).padStart(2,'0');
        wrap.querySelector('.cd-sec').textContent  = String(s).padStart(2,'0');
    }
    tick();
    setInterval(tick, 1000);
});
</script>
</body>
</html>
