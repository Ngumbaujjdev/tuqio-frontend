<?php
include 'config/config.php';
include 'libs/App.php';

// ── Resolve which event to show ───────────────────────────────────────────
$allEventsResp = tuqio_api('/api/public/events');
$eventsList    = $allEventsResp['data'] ?? [];
$requestedSlug = $_GET['event'] ?? '';
$activeSlug    = $requestedSlug;

// Auto-detect voting event if none specified
if (!$activeSlug) {
    foreach ($eventsList as $ev) {
        if (!empty($ev['has_voting']) && ($ev['current_phase'] ?? '') === 'voting') {
            $activeSlug = $ev['slug'];
            break;
        }
    }
    if (!$activeSlug) {
        foreach ($eventsList as $ev) {
            if (!empty($ev['has_voting']) && ($ev['status'] ?? '') === 'published') {
                $activeSlug = $ev['slug'];
                break;
            }
        }
    }
}

$response   = $activeSlug ? tuqio_api('/api/public/events/' . $activeSlug . '/nominees') : [];
$event      = $response['event'] ?? [];
$categories = $response['categories'] ?? [];

$now          = time();
$votingCloses = !empty($event['voting_closes_at']) ? strtotime($event['voting_closes_at']) : 0;
$isVotingOpen = !empty($event['voting_is_open']); // use backend-calculated flag
$voteUrl      = API_BASE . '/events/' . ($event['slug'] ?? $activeSlug);
$eventName    = $event['name'] ?? 'Tuqio Hub';

$voteBundleUrl = SITE_URL . '/vote-bundle.php?event=' . urlencode($activeSlug ?: '');

$initialsColors = ['#ed1c24', '#1e1548', '#2d1f6b', '#6c757d'];
$globalIdx = 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">

<!-- SEO -->
<title>Nominees &amp; Finalists | <?= htmlspecialchars($eventName) ?> | Tuqio Hub</title>
<meta name="description" content="Meet the nominees and finalists for <?= htmlspecialchars($eventName) ?>. Browse all categories and vote for your favourites on Tuqio Hub.">
<meta name="keywords" content="nominees Kenya, finalists awards Kenya, vote nominees, <?= htmlspecialchars($eventName) ?>, Tuqio Hub voting">
<meta name="author" content="Tuqio Hub">
<meta name="robots" content="index, follow">
<link rel="canonical" href="https://tuqiohub.africa/nominees.php">

<!-- Schema.org microdata -->
<meta itemprop="name" content="Nominees &amp; Finalists | <?= htmlspecialchars($eventName) ?>">
<meta itemprop="description" content="Meet the nominees and finalists for <?= htmlspecialchars($eventName) ?>. Vote for your favourites on Tuqio Hub.">
<meta itemprop="image" content="<?= OG_IMAGE ?>">

<!-- Open Graph -->
<meta property="og:title" content="Nominees &amp; Finalists | <?= htmlspecialchars($eventName) ?>">
<meta property="og:type" content="website">
<meta property="og:image" content="<?= OG_IMAGE ?>">
<meta property="og:image:type" content="image/webp">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:url" content="https://tuqiohub.africa/nominees.php">
<meta property="og:description" content="Meet the nominees and finalists for <?= htmlspecialchars($eventName) ?>. Vote on Tuqio Hub.">
<meta property="og:site_name" content="Tuqio Hub">

<!-- Twitter Card -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:site" content="@tuqiohub">
<meta name="twitter:title" content="Nominees &amp; Finalists | <?= htmlspecialchars($eventName) ?>">
<meta name="twitter:description" content="Meet the nominees and finalists for <?= htmlspecialchars($eventName) ?>. Vote on Tuqio Hub.">
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
{"@context":"https://schema.org","@type":"BreadcrumbList","itemListElement":[{"@type":"ListItem","position":1,"name":"Home","item":"https://tuqiohub.africa/"},{"@type":"ListItem","position":2,"name":"Nominees","item":"https://tuqiohub.africa/nominees.php"}]}
</script>

<!-- JSON-LD: WebPage -->
<script type="application/ld+json">
{"@context":"https://schema.org","@type":"WebPage","name":"Nominees & Finalists | Tuqio Hub","url":"https://tuqiohub.africa/nominees.php","description":"Meet the nominees and finalists. Vote for your favourites on Tuqio Hub."}
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
/* Category nav */
.cat-nav .nav-link {
    color: #1e1548; padding: 10px 16px; font-size: .88rem; font-weight: 600;
    border-radius: 6px; margin-bottom: 4px; border: 1px solid #eee;
    background: #fafafa; transition: all .25s; display: flex; align-items: center; gap: 8px;
}
.cat-nav .nav-link.active { background: #ed1c24; border-color: #ed1c24; color: #fff; }
.cat-nav .nav-link:hover:not(.active) { background: #fff0f0; border-color: #ed1c24; color: #ed1c24; }
.cat-nav .nav-link .count-badge {
    margin-left: auto; background: rgba(237,28,36,0.12); color: #ed1c24;
    border-radius: 20px; font-size: .7rem; font-weight: 700; padding: 2px 8px;
}
.cat-nav .nav-link.active .count-badge { background: rgba(255,255,255,0.25); color: #fff; }
/* Nominee cards */
.nominee-card {
    background: #fff; border-radius: 12px; padding: 28px 20px 20px;
    text-align: center; box-shadow: 0 4px 20px rgba(0,0,0,0.06);
    transition: transform .3s, box-shadow .3s; height: 100%;
    display: flex; flex-direction: column; cursor: pointer;
}
.nominee-card:hover { transform: translateY(-6px); box-shadow: 0 12px 30px rgba(0,0,0,0.1); }
.nominee-photo {
    width: 110px; height: 110px; border-radius: 50%; object-fit: cover;
    margin: 0 auto 18px; border: 4px solid #f9fafc;
    box-shadow: 0 2px 12px rgba(0,0,0,0.1); display: block;
}
.nominee-initials {
    width: 110px; height: 110px; border-radius: 50%; display: flex;
    align-items: center; justify-content: center; margin: 0 auto 18px;
    font-size: 1.9rem; font-weight: 800; color: #fff; letter-spacing: 1px;
}
.nominee-name { font-size: 1.05rem; color: #1e1548; margin-bottom: 4px; font-weight: 700; line-height: 1.3; }
.nominee-title { font-size: .82rem; color: #ed1c24; font-weight: 600; margin-bottom: 4px; }
.nominee-location { font-size: .8rem; color: #999; margin-bottom: 16px; }
.nominee-location i { color: #ed1c24; margin-right: 3px; }
.vote-bar-container { background: #f0f0f0; border-radius: 10px; height: 7px; width: 100%; margin-bottom: 6px; overflow: hidden; }
.vote-bar-fill { background: linear-gradient(90deg, #ed1c24, #1e1548); height: 100%; border-radius: 10px; transition: width 1s ease-in-out; }
.vote-stats { display: flex; justify-content: space-between; font-size: .78rem; font-weight: 600; color: #888; margin-bottom: 16px; }
.vote-stats .vote-count { color: #ed1c24; }
.nominee-card-footer { margin-top: auto; }
/* Countdown */
.countdown-widget { background: linear-gradient(135deg, #15102e, #ed1c24); border-radius: 10px; padding: 24px; color: #fff; text-align: center; margin-bottom: 30px; }
.countdown-widget h6 { font-size: .88rem; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; opacity: .85; margin-bottom: 12px; }
.countdown-boxes { display: flex; gap: 8px; justify-content: center; }
.countdown-box { background: rgba(255,255,255,0.18); border-radius: 8px; padding: 10px 14px; min-width: 52px; }
.countdown-box .num { font-size: 1.6rem; font-weight: 800; display: block; line-height: 1; }
.countdown-box .lbl { font-size: .62rem; text-transform: uppercase; letter-spacing: 1px; opacity: .7; }
.countdown-closed { font-size: .92rem; opacity: .88; }
/* Modal */
#nomineeModal .modal-header-media { position: relative; height: 220px; overflow: hidden; background: #1e1548; }
#nomineeModal .modal-header-media img { width: 100%; height: 100%; object-fit: cover; display: block; }
#nomineeModal .modal-header-initials {
    position: absolute; inset: 0; display: none;
    align-items: center; justify-content: center;
    font-size: 4.5rem; font-weight: 800; color: #fff; letter-spacing: 2px;
}
#nomineeModal .modal-header-close {
    position: absolute; top: 12px; right: 14px;
    background: rgba(0,0,0,0.35); border: none; border-radius: 50%;
    width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;
    color: #fff; cursor: pointer; font-size: 1.1rem;
}
/* Event selector */
.event-selector { margin-bottom: 28px; }
.event-selector label { font-weight: 600; color: #1e1548; margin-bottom: 8px; display: block; }
.event-selector select { border: 2px solid #eee; border-radius: 8px; padding: 9px 14px; font-size: .9rem; width: 100%; max-width: 420px; color: #333; }
.event-selector select:focus { border-color: #ed1c24; outline: none; box-shadow: 0 0 0 3px rgba(237,28,36,0.12); }

/* Animated vote count */
@keyframes voteFlash { 0%{background:rgba(237,28,36,.15)} 100%{background:transparent} }
.vote-updated { animation: voteFlash .8s ease-out; border-radius: 4px; }

/* Velocity badge */
.velocity-badge {
    display: inline-flex; align-items: center; gap: 4px; font-size: .68rem; font-weight: 700;
    background: linear-gradient(135deg, #fbbf24, #f59e0b); color: #fff;
    padding: 2px 8px; border-radius: 20px; margin-left: 6px;
}

/* Winner badge */
.winner-badge {
    position: absolute; top: -10px; left: 50%; transform: translateX(-50%);
    background: linear-gradient(135deg, #f59e0b, #fbbf24); color: #fff;
    font-size: .65rem; font-weight: 800; padding: 3px 12px; border-radius: 20px;
    text-transform: uppercase; letter-spacing: .5px; white-space: nowrap;
    box-shadow: 0 2px 8px rgba(245,158,11,.4);
}
.nominee-card { position: relative; }
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
<section class="page-title" style="background-image:url(<?= SITE_URL ?>/assets/slides/kenya-breadcrump.webp);">
    <div class="anim-icons full-width">
        <span class="icon icon-bull-eye"></span>
        <span class="icon icon-dotted-circle"></span>
    </div>
    <div class="auto-container">
        <div class="title-outer">
            <h1>Nominees &amp; Finalists</h1>
            <ul class="page-breadcrumb">
                <li><a href="<?= SITE_URL ?>">Home</a></li>
                <li>Nominees</li>
            </ul>
        </div>
    </div>
</section>

<!-- Content -->
<div class="sidebar-page-container">
    <div class="auto-container">
        <div class="row clearfix">

            <!-- Content Side -->
            <div class="content-side col-lg-9 col-md-12 col-sm-12">

                <!-- Event selector -->
                <?php
                $votingEvents = array_filter($eventsList, fn($e) => !empty($e['has_voting']));
                if (count($votingEvents) > 1): ?>
                <div class="event-selector">
                    <label>Select Event:</label>
                    <select onchange="location.href='nominees?event='+this.value">
                        <?php foreach ($votingEvents as $ev): ?>
                        <option value="<?= htmlspecialchars($ev['slug']) ?>"
                            <?= ($ev['slug'] === $activeSlug) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($ev['name']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>

                <?php if (empty($categories)): ?>
                <div class="text-center" style="padding:80px 0;">
                    <i class="fas fa-users" style="font-size:3rem;color:#ed1c24;opacity:.3;"></i>
                    <h4 style="margin-top:20px;">Nominees Announced Soon</h4>
                    <p class="text-muted">Finalists for <?= htmlspecialchars($eventName) ?> have not been published yet. Check back soon.</p>
                </div>
                <?php else: ?>
                <div class="tab-content" id="nomineeTabContent">
                    <?php foreach ($categories as $catIndex => $category):
                        $candidates = $category['candidates'] ?? $category['nominees'] ?? [];
                        $maxVotes   = max(1, array_reduce($candidates, fn($c,$n) => max($c, $n['votes_count'] ?? $n['total_votes'] ?? 0), 0));
                    ?>
                    <div class="tab-pane fade <?= $catIndex === 0 ? 'show active' : '' ?>"
                         id="cat-pane-<?= htmlspecialchars($category['slug']) ?>"
                         role="tabpanel">

                        <div style="margin-bottom:28px;">
                            <h3 style="color:#1e1548;font-weight:700;font-size:1.4rem;margin-bottom:6px;">
                                <?= htmlspecialchars($category['name']) ?>
                            </h3>
                            <?php if (!empty($category['description'])): ?>
                            <p style="color:#777;font-size:.9rem;margin:0;"><?= htmlspecialchars($category['description']) ?></p>
                            <?php endif; ?>
                        </div>

                        <?php if (empty($candidates)): ?>
                        <div class="text-center" style="padding:50px 0;color:#aaa;">
                            <i class="fas fa-hourglass-half" style="font-size:2rem;margin-bottom:10px;"></i>
                            <p>Nominees for this category will be announced soon.</p>
                        </div>
                        <?php else: ?>
                        <div class="row">
                            <?php foreach ($candidates as $candidate):
                                $votes   = (int)($candidate['votes_count'] ?? $candidate['total_votes'] ?? 0);
                                $pct     = min(100, round($votes / $maxVotes * 100));
                                $cImage  = $candidate['image'] ?? '';
                                $cName   = $candidate['name'] ?? '';
                                $words   = array_filter(explode(' ', trim($cName)));
                                $initials= implode('', array_map(fn($w) => strtoupper($w[0] ?? ''), array_slice($words, 0, 2)));
                                $color   = $initialsColors[$globalIdx % 4];
                                $globalIdx++;
                            ?>
                            <div class="col-md-4 col-sm-6 col-12" style="margin-bottom:30px;">
                                <div class="nominee-card"
                                     data-bs-toggle="modal" data-bs-target="#nomineeModal"
                                     data-name="<?= htmlspecialchars($cName) ?>"
                                     data-title="<?= htmlspecialchars($candidate['title'] ?? $candidate['subtitle'] ?? '') ?>"
                                     data-desc="<?= htmlspecialchars($candidate['description'] ?? '') ?>"
                                     data-image="<?= htmlspecialchars($cImage ? API_STORAGE . $cImage : '') ?>"
                                     data-initials="<?= htmlspecialchars($initials) ?>"
                                     data-color="<?= $color ?>"
                                     data-votes="<?= $votes ?>"
                                     data-pct="<?= $pct ?>"
                                     data-vote-url="<?= htmlspecialchars($voteUrl) ?>"
                                     data-candidate-id="<?= (int)($candidate['id'] ?? 0) ?>"
                                     data-slug="<?= htmlspecialchars($candidate['slug'] ?? '') ?>"
                                     data-category="<?= htmlspecialchars($category['name']) ?>"
                                     data-is-winner="<?= !empty($candidate['is_winner']) ? '1' : '0' ?>">

                                    <?php if (!empty($candidate['is_winner'])): ?>
                                    <div class="winner-badge"><i class="fas fa-crown" style="margin-right:4px;"></i>Winner</div>
                                    <?php endif; ?>
                                    <?php
                                    $cImageFull = $cImage ? API_STORAGE . $cImage : '';
                                    ?>
                                    <?php if ($cImageFull): ?>
                                    <img src="<?= htmlspecialchars($cImageFull) ?>"
                                         alt="<?= htmlspecialchars($cName) ?>"
                                         class="nominee-photo"
                                         onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                                    <div class="nominee-initials" style="display:none;background:<?= $color ?>;"><?= $initials ?></div>
                                    <?php else: ?>
                                    <div class="nominee-initials" style="background:<?= $color ?>;"><?= $initials ?></div>
                                    <?php endif; ?>

                                    <h4 class="nominee-name"><?= htmlspecialchars($cName) ?></h4>
                                    <?php if (!empty($candidate['subtitle'])): ?>
                                    <div class="nominee-title"><?= htmlspecialchars($candidate['subtitle']) ?></div>
                                    <?php endif; ?>
                                    <?php if (!empty($candidate['description'])): ?>
                                    <div class="nominee-location">
                                        <i class="fa fa-info-circle"></i>
                                        <?= htmlspecialchars(mb_strimwidth($candidate['description'], 0, 60, '…')) ?>
                                    </div>
                                    <?php endif; ?>

                                    <div class="vote-bar-container">
                                        <div class="vote-bar-fill" style="width:<?= $pct ?>%;"
                                             data-candidate-id="<?= (int)($candidate['id'] ?? 0) ?>-bar"></div>
                                    </div>
                                    <div class="vote-stats">
                                        <span>Votes</span>
                                        <span class="vote-count" data-candidate-id="<?= (int)($candidate['id'] ?? 0) ?>">
                                            <?= number_format($votes) ?>
                                        </span>
                                    </div>

                                    <div class="nominee-card-footer">
                                        <?php if ($isVotingOpen): ?>
                                        <a class="theme-btn btn-style-one"
                                           style="font-size:.8rem;padding:8px 20px;display:inline-block;"
                                           href="<?= htmlspecialchars($voteBundleUrl . '&nominee=' . (int)($candidate['id'] ?? 0)) ?>"
                                           onclick="event.stopPropagation();">
                                            <span class="btn-title"><i class="fas fa-vote-yea me-1"></i> Vote Now</span>
                                        </a>
                                        <?php else: ?>
                                        <span style="font-size:.8rem;color:#aaa;font-style:italic;">Voting not open</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

            </div>
            <!-- End Content Side -->

            <!-- Sidebar -->
            <div class="sidebar-side col-lg-3 col-md-12 col-sm-12">
                <aside class="sidebar padding-left">

                    <!-- Category filter -->
                    <?php if (!empty($categories)): ?>
                    <div class="sidebar-widget" style="margin-bottom:30px;">
                        <h5 class="sidebar-title">Categories</h5>
                        <div class="widget-content">
                            <nav>
                                <div class="nav flex-column cat-nav" id="nomineeTabs" role="tablist">
                                    <?php foreach ($categories as $ci => $cat):
                                        $cCount = count($cat['candidates'] ?? $cat['nominees'] ?? []);
                                    ?>
                                    <button class="nav-link <?= $ci === 0 ? 'active' : '' ?>"
                                            id="cat-tab-<?= htmlspecialchars($cat['slug']) ?>"
                                            data-bs-toggle="pill"
                                            data-bs-target="#cat-pane-<?= htmlspecialchars($cat['slug']) ?>"
                                            type="button" role="tab">
                                        <?= htmlspecialchars($cat['name']) ?>
                                        <span class="count-badge"><?= $cCount ?></span>
                                    </button>
                                    <?php endforeach; ?>
                                </div>
                            </nav>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Countdown -->
                    <?php if (!empty($event['has_voting'])): ?>
                    <div class="countdown-widget">
                        <?php if ($isVotingOpen && $votingCloses): ?>
                        <h6><i class="fas fa-vote-yea me-1"></i> Voting Closes In</h6>
                        <div class="countdown-boxes" id="countdown-boxes">
                            <div class="countdown-box"><span class="num" id="cd-days">--</span><span class="lbl">Days</span></div>
                            <div class="countdown-box"><span class="num" id="cd-hours">--</span><span class="lbl">Hrs</span></div>
                            <div class="countdown-box"><span class="num" id="cd-mins">--</span><span class="lbl">Min</span></div>
                            <div class="countdown-box"><span class="num" id="cd-secs">--</span><span class="lbl">Sec</span></div>
                        </div>
                        <div style="margin-top:16px;">
                            <a href="<?= htmlspecialchars($voteBundleUrl) ?>"
                               class="theme-btn btn-style-two"
                               style="background:#fff;color:#ed1c24;border-color:#fff;font-size:.82rem;width:100%;text-align:center;display:block;border:2px solid #fff;">
                                <span class="btn-title"><i class="fas fa-vote-yea me-1"></i> Cast Your Votes</span>
                            </a>
                        </div>
                        <?php elseif ($votingCloses && $now > $votingCloses): ?>
                        <h6>Voting Status</h6>
                        <p class="countdown-closed"><i class="fas fa-check-circle me-1"></i> Voting has closed. Results will be announced at the event.</p>
                        <?php else: ?>
                        <h6>Public Voting</h6>
                        <p class="countdown-closed">Voting will be announced soon. Stay tuned!</p>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>

                    <!-- Explore links -->
                    <div class="sidebar-widget">
                        <h5 class="sidebar-title">Explore</h5>
                        <div class="widget-content">
                            <ul class="blog-categories">
                                <li><a href="<?= SITE_URL ?>/events"><i class="fa fa-calendar-alt me-2" style="color:#ed1c24;"></i> All Events</a></li>
                                <li><a href="<?= SITE_URL ?>/polls"><i class="fa fa-poll me-2" style="color:#ed1c24;"></i> Live Polls</a></li>
                                <li><a href="<?= SITE_URL ?>/gallery"><i class="fa fa-images me-2" style="color:#ed1c24;"></i> Gallery</a></li>
                                <li><a href="<?= SITE_URL ?>/blog"><i class="fa fa-newspaper me-2" style="color:#ed1c24;"></i> Blog</a></li>
                            </ul>
                        </div>
                    </div>

                </aside>
            </div>
            <!-- End Sidebar -->

        </div>
    </div>
</div>

<!-- Nominee Modal -->
<div class="modal fade" id="nomineeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:480px;">
        <div class="modal-content" style="border:none;border-radius:14px;overflow:hidden;">
            <div class="modal-header-media">
                <img id="modal-photo" src="" alt="" style="width:100%;height:100%;object-fit:cover;display:block;">
                <div class="modal-header-initials" id="modal-initials"></div>
                <button class="modal-header-close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fa fa-times"></i>
                </button>
            </div>
            <div class="modal-body" style="padding:28px;">
                <h4 id="modal-name" style="color:#1e1548;font-weight:800;margin-bottom:4px;"></h4>
                <div id="modal-title" style="color:#ed1c24;font-weight:600;font-size:.9rem;margin-bottom:4px;"></div>
                <div id="modal-desc" style="color:#888;font-size:.85rem;margin-bottom:18px;">
                    <span id="modal-desc-text"></span>
                </div>
                <div style="margin-bottom:18px;">
                    <div class="vote-bar-container"><div class="vote-bar-fill" id="modal-bar" style="width:0%;"></div></div>
                    <div class="vote-stats">
                        <span>Total Votes</span>
                        <span class="vote-count" id="modal-vote-count">0</span>
                    </div>
                </div>
                <?php if ($isVotingOpen): ?>
                <a id="modal-vote-btn"
                   class="theme-btn btn-style-one"
                   style="display:block;width:100%;text-align:center;"
                   href="#">
                    <span class="btn-title"><i class="fas fa-vote-yea me-1"></i> Vote Now</span>
                </a>
                <?php else: ?>
                <div style="text-align:center;color:#aaa;font-style:italic;font-size:.88rem;">Voting is not currently open.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
</div>

<div class="scroll-to-top scroll-to-target" data-target="html"><span class="fa fa-angle-up"></span></div>
<?php include 'includes/footer-links.php'; ?>

<script>
(function () {
    'use strict';

    var API_BASE   = '<?= API_BASE ?>';
    var EVENT_SLUG = <?= json_encode($activeSlug ?: '') ?>;
    var IS_VOTING  = <?= $isVotingOpen ? 'true' : 'false' ?>;

    // ── Nominee modal ─────────────────────────────────────────────────────────
    var currentModalData = {};
    var nomineeModal = document.getElementById('nomineeModal');
    if (nomineeModal) {
        nomineeModal.addEventListener('show.bs.modal', function (e) {
            var card = e.relatedTarget;
            if (!card) return;
            currentModalData = {
                id:       card.dataset.candidateId,
                name:     card.dataset.name || '',
                slug:     card.dataset.slug || '',
                category: card.dataset.category || '',
            };
            var voteBtn = document.getElementById('modal-vote-btn');
            if (voteBtn) {
                voteBtn.href = 'vote-bundle.php?event=' + encodeURIComponent(EVENT_SLUG) + '&nominee=' + encodeURIComponent(card.dataset.candidateId || '');
            }
            document.getElementById('modal-name').textContent      = currentModalData.name;
            document.getElementById('modal-title').textContent     = card.dataset.title || '';
            document.getElementById('modal-desc-text').textContent = card.dataset.desc || '';
            document.getElementById('modal-vote-count').textContent= parseInt(card.dataset.votes || '0').toLocaleString();
            document.getElementById('modal-bar').style.width       = (card.dataset.pct || '0') + '%';
            var photoEl    = document.getElementById('modal-photo');
            var initialsEl = document.getElementById('modal-initials');
            initialsEl.textContent       = card.dataset.initials || '';
            initialsEl.style.background  = card.dataset.color || '#ed1c24';
            if (card.dataset.image) {
                photoEl.src = card.dataset.image;
                photoEl.style.display    = 'block';
                initialsEl.style.display = 'none';
                photoEl.onerror = function () {
                    this.style.display       = 'none';
                    initialsEl.style.display = 'flex';
                    this.onerror = null;
                };
            } else {
                photoEl.style.display    = 'none';
                initialsEl.style.display = 'flex';
            }
        });
    }

    // ── Countdown ─────────────────────────────────────────────────────────────
    var votingClosesAt = <?= $votingCloses ?: 'null' ?>;
    function updateCountdown() {
        if (!votingClosesAt) return;
        var remaining = votingClosesAt - Math.floor(Date.now() / 1000);
        if (remaining <= 0) {
            var boxes = document.getElementById('countdown-boxes');
            if (boxes) boxes.innerHTML = '<span style="font-size:.9rem;opacity:.85;">Voting has closed.</span>';
            return;
        }
        var d = Math.floor(remaining / 86400);
        var h = Math.floor((remaining % 86400) / 3600);
        var m = Math.floor((remaining % 3600) / 60);
        var s = remaining % 60;
        var el;
        if ((el = document.getElementById('cd-days')))  el.textContent = String(d).padStart(2,'0');
        if ((el = document.getElementById('cd-hours'))) el.textContent = String(h).padStart(2,'0');
        if ((el = document.getElementById('cd-mins')))  el.textContent = String(m).padStart(2,'0');
        if ((el = document.getElementById('cd-secs')))  el.textContent = String(s).padStart(2,'0');
    }
    if (votingClosesAt) { updateCountdown(); setInterval(updateCountdown, 1000); }

    // ── Animated number counter helper ────────────────────────────────────────
    function animateCount(el, fromVal, toVal, duration) {
        if (fromVal === toVal) return;
        var start = null;
        el.classList.add('vote-updated');
        setTimeout(function(){ el.classList.remove('vote-updated'); }, 1000);
        function step(ts) {
            if (!start) start = ts;
            var prog = Math.min((ts - start) / duration, 1);
            var ease = 1 - Math.pow(1 - prog, 3);
            el.textContent = Math.round(fromVal + (toVal - fromVal) * ease).toLocaleString();
            if (prog < 1) requestAnimationFrame(step);
        }
        requestAnimationFrame(step);
    }

    // ── Vote velocity tracking ────────────────────────────────────────────────
    var prevVotes = {};  // {candidate_id: {count, timestamp}}
    function getVelocity(id, newCount) {
        var now = Date.now() / 1000;
        var prev = prevVotes[id];
        if (!prev) { prevVotes[id] = { count: newCount, timestamp: now }; return 0; }
        var elapsed = now - prev.timestamp;
        if (elapsed < 5) return 0;  // too soon
        var perMin = Math.round((newCount - prev.count) / elapsed * 60);
        prevVotes[id] = { count: newCount, timestamp: now };
        return Math.max(0, perMin);
    }

    // ── Live vote count polling ───────────────────────────────────────────────
    var lastCounts = {};
    function refreshVoteCounts() {
        if (!EVENT_SLUG) return;
        fetch(API_BASE + '/api/public/events/' + encodeURIComponent(EVENT_SLUG) + '/vote-counts')
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (!data.categories) return;
                data.categories.forEach(function(cat) {
                    var maxV = 1;
                    cat.nominees.forEach(function(c) { if (c.votes_count > maxV) maxV = c.votes_count; });
                    cat.nominees.forEach(function(c) {
                        var pct     = Math.min(100, Math.round(c.votes_count / maxV * 100));
                        var prevVal = lastCounts[c.id] || 0;
                        lastCounts[c.id] = c.votes_count;

                        // Animate count
                        var cEl = document.querySelector('.vote-count[data-candidate-id="' + c.id + '"]');
                        if (cEl) {
                            if (c.votes_count !== prevVal) {
                                animateCount(cEl, prevVal, c.votes_count, 800);
                            } else {
                                cEl.textContent = c.votes_count.toLocaleString();
                            }
                        }

                        // Animate bar
                        var bEl = document.querySelector('.vote-bar-fill[data-candidate-id="' + c.id + '-bar"]');
                        if (bEl) bEl.style.width = pct + '%';

                        // Velocity badge
                        var vel = getVelocity(c.id, c.votes_count);
                        var vbEl = document.getElementById('vel-' + c.id);
                        if (vel > 0) {
                            if (!vbEl) {
                                var statsRow = document.querySelector('.vote-stats .vote-count[data-candidate-id="' + c.id + '"]');
                                if (statsRow) {
                                    vbEl = document.createElement('span');
                                    vbEl.id = 'vel-' + c.id;
                                    vbEl.className = 'velocity-badge';
                                    statsRow.parentNode.appendChild(vbEl);
                                }
                            }
                            if (vbEl) vbEl.innerHTML = '🔥 +' + vel + '/min';
                        }

                        // Update card data-votes for modal
                        var card = document.querySelector('.nominee-card[data-candidate-id="' + c.id + '"]');
                        if (card) {
                            card.dataset.votes = c.votes_count;
                            card.dataset.pct   = pct;
                        }
                    });
                });
            })
            .catch(function() {});
    }
    setInterval(refreshVoteCounts, 8000);

}());
</script>
</body>
</html>
