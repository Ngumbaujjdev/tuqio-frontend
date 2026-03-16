<?php
include 'config/config.php';
include 'libs/App.php';

$resp  = tuqio_api('/api/public/polls');
$polls = $resp['data'] ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">

<!-- SEO -->
<title>Community Polls | Tuqio Hub</title>
<meta name="description" content="Participate in live community polls on Tuqio Hub. Share your opinion and see real-time results on topics that matter in Kenya.">
<meta name="keywords" content="live polls Kenya, community polls, Tuqio Hub polls, online voting polls, real-time results Kenya">
<meta name="author" content="Tuqio Hub">
<meta name="robots" content="index, follow">
<link rel="canonical" href="https://tuqiohub.africa/polls.php">

<!-- Schema.org microdata -->
<meta itemprop="name" content="Community Polls | Tuqio Hub">
<meta itemprop="description" content="Participate in live community polls on Tuqio Hub and see real-time results.">
<meta itemprop="image" content="<?= OG_IMAGE ?>">

<!-- Open Graph -->
<meta property="og:title" content="Community Polls | Tuqio Hub">
<meta property="og:type" content="website">
<meta property="og:image" content="<?= OG_IMAGE ?>">
<meta property="og:image:type" content="image/webp">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:url" content="https://tuqiohub.africa/polls.php">
<meta property="og:description" content="Participate in live community polls on Tuqio Hub and see real-time results.">
<meta property="og:site_name" content="Tuqio Hub">

<!-- Twitter Card -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:site" content="@tuqiohub">
<meta name="twitter:title" content="Community Polls | Tuqio Hub">
<meta name="twitter:description" content="Participate in live community polls on Tuqio Hub and see real-time results.">
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
{"@context":"https://schema.org","@type":"BreadcrumbList","itemListElement":[{"@type":"ListItem","position":1,"name":"Home","item":"https://tuqiohub.africa/"},{"@type":"ListItem","position":2,"name":"Polls","item":"https://tuqiohub.africa/polls.php"}]}
</script>

<!-- JSON-LD: WebPage -->
<script type="application/ld+json">
{"@context":"https://schema.org","@type":"WebPage","name":"Community Polls | Tuqio Hub","url":"https://tuqiohub.africa/polls.php","description":"Participate in live community polls on Tuqio Hub."}
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
.poll-card { background:#fff;border-radius:12px;padding:28px;box-shadow:0 4px 20px rgba(0,0,0,0.06);height:100%; }
.poll-card h5 { font-weight:700;color:#1e1548;margin-bottom:8px; }
.poll-card .poll-desc { font-size:.85rem;color:#888;margin-bottom:20px; }
.poll-option-label {
    border:2px solid #eee;border-radius:8px;padding:12px 16px;margin-bottom:10px;cursor:pointer;
    transition:border-color .2s,background .2s;display:flex;align-items:center;gap:10px;
}
.poll-option-label:hover { border-color:#ed1c24;background:#fff5f5; }
.poll-option-label input { accent-color:#ed1c24;width:16px;height:16px;flex-shrink:0; }
.poll-bar { height:7px;background:#f0f0f0;border-radius:10px;margin-top:5px;overflow:hidden; }
.poll-bar-fill { height:100%;background:linear-gradient(90deg,#ed1c24,#1e1548);border-radius:10px;transition:width .8s; }
.poll-pct { font-size:.75rem;color:#ed1c24;font-weight:700;margin-left:auto; }
.voted-check { display:inline-block;width:20px;height:20px;background:#10b981;border-radius:50%;color:#fff;font-size:.7rem;line-height:20px;text-align:center;flex-shrink:0; }
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

<section class="page-title" style="background-image:url(<?= SITE_URL ?>/assets/slides/kenya-breadcrump.webp);">
    <div class="anim-icons full-width"><span class="icon icon-bull-eye"></span><span class="icon icon-dotted-circle"></span></div>
    <div class="auto-container">
        <div class="title-outer">
            <h1>Live Polls</h1>
            <ul class="page-breadcrumb">
                <li><a href="<?= SITE_URL ?>">Home</a></li>
                <li>Polls</li>
            </ul>
        </div>
    </div>
</section>

<section class="section-light">
    <div class="auto-container">

        <?php if (empty($polls)): ?>
        <div class="text-center empty-state">
            <i class="fas fa-poll empty-icon"></i>
            <h4>No Active Polls</h4>
            <p class="text-muted">Check back soon for new polls.</p>
        </div>
        <?php else: ?>
        <div class="row">
            <?php foreach ($polls as $poll): ?>
            <div class="col-md-6 mb-4 wow fadeInUp">
                <div class="poll-card" id="poll-<?= $poll['id'] ?>">
                    <h5><?= htmlspecialchars($poll['title']) ?></h5>
                    <?php if (!empty($poll['description'])): ?>
                    <p class="poll-desc"><?= htmlspecialchars($poll['description']) ?></p>
                    <?php endif; ?>

                    <!-- Voting form -->
                    <div class="poll-options-wrap" data-poll-id="<?= $poll['id'] ?>">
                        <form onsubmit="submitPoll(event, <?= $poll['id'] ?>)">
                            <?php foreach ($poll['options'] as $opt): ?>
                            <label class="poll-option-label" for="opt-<?= $poll['id'] ?>-<?= $opt['id'] ?>">
                                <input type="radio" name="poll-<?= $poll['id'] ?>"
                                       id="opt-<?= $poll['id'] ?>-<?= $opt['id'] ?>"
                                       value="<?= $opt['id'] ?>">
                                <?php if (!empty($opt['image'])): ?>
                                <img src="<?= htmlspecialchars($opt['image']) ?>" class="poll-option-img">
                                <?php endif; ?>
                                <span class="poll-option-span"><?= htmlspecialchars($opt['text']) ?></span>
                            </label>
                            <?php endforeach; ?>
                            <button type="submit" class="theme-btn btn-style-one">
                                <span class="btn-title"><i class="fas fa-check me-1"></i> Submit Vote</span>
                            </button>
                        </form>
                    </div>

                    <!-- Results -->
                    <div class="poll-results-wrap" id="results-<?= $poll['id'] ?>" style="display:none;">
                        <?php foreach ($poll['options'] as $opt): ?>
                        <div class="mb-3" id="result-row-<?= $poll['id'] ?>-<?= $opt['id'] ?>">
                            <div class="poll-result-header">
                                <span class="poll-result-text">
                                    <span class="voted-check" id="voted-<?= $poll['id'] ?>-<?= $opt['id'] ?>" style="display:none;">✓</span>
                                    <?= htmlspecialchars($opt['text']) ?>
                                </span>
                                <span class="poll-pct" id="pct-<?= $poll['id'] ?>-<?= $opt['id'] ?>"><?= $opt['percentage'] ?>%</span>
                            </div>
                            <div class="poll-bar">
                                <div class="poll-bar-fill" id="bar-<?= $poll['id'] ?>-<?= $opt['id'] ?>" style="width:<?= $opt['percentage'] ?>%;"></div>
                            </div>
                            <small class="poll-votes-count"><?= number_format($opt['vote_count']) ?> votes</small>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <p class="poll-footer-info">
                        <i class="fas fa-users me-1"></i>
                        <span id="total-<?= $poll['id'] ?>"><?= number_format($poll['total_votes']) ?></span> total votes
                        <?php if (!empty($poll['end_date'])): ?>
                        &nbsp;·&nbsp; Closes <?= date('d M Y', strtotime($poll['end_date'])) ?>
                        <?php endif; ?>
                    </p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

    </div>
</section>

<?php include 'includes/footer.php'; ?>
</div>
<div class="scroll-to-top scroll-to-target" data-target="html"><span class="fa fa-angle-up"></span></div>
<?php include 'includes/footer-links.php'; ?>
<script>
function submitPoll(e, pollId) {
    e.preventDefault();
    var selected = document.querySelector('input[name="poll-' + pollId + '"]:checked');
    if (!selected) { alert('Please select an option.'); return; }
    var optionId = parseInt(selected.value);
    var btn = e.target.querySelector('button[type=submit]');
    btn.disabled = true;
    btn.querySelector('.btn-title').textContent = 'Submitting…';

    fetch('<?= API_BASE ?>/api/public/polls/' + pollId + '/respond', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify({ option_id: optionId })
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.success && data.poll) {
            document.getElementById('poll-' + pollId).querySelector('.poll-options-wrap').style.display = 'none';
            var results = document.getElementById('results-' + pollId);
            results.style.display = 'block';
            data.poll.options.forEach(function(o) {
                var p = document.getElementById('pct-' + pollId + '-' + o.id);
                var b = document.getElementById('bar-' + pollId + '-' + o.id);
                var v = document.getElementById('voted-' + pollId + '-' + o.id);
                if (p) p.textContent = o.percentage + '%';
                if (b) b.style.width = o.percentage + '%';
                if (v && o.id === optionId) v.style.display = 'inline-block';
            });
            var t = document.getElementById('total-' + pollId);
            if (t) t.textContent = (data.poll.total_votes + 1).toLocaleString();
        }
    })
    .catch(function() { btn.disabled = false; btn.querySelector('.btn-title').textContent = 'Submit Vote'; });
}
</script>
</body>
</html>
