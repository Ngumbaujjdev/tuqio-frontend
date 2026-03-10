<?php
include 'config/config.php';
include 'libs/App.php';

$status      = $_GET['status']  ?? 'success';   // success | failed | error
$nomineeSlug = trim($_GET['nominee'] ?? '');
$eventSlug   = trim($_GET['event']   ?? '');
$votes       = (int) ($_GET['votes']  ?? 0);

$isSuccess = $status === 'success';

// Fetch event + nominee only for success state
$event   = null;
$nominee = null;

if ($eventSlug) {
    $resp  = tuqio_api('/api/public/events/' . urlencode($eventSlug));
    $event = $resp['event'] ?? null;
}

if ($isSuccess && $eventSlug && $nomineeSlug) {
    $nomResp = tuqio_api('/api/public/events/' . urlencode($eventSlug) . '/nominees');
    foreach ($nomResp['categories'] ?? [] as $cat) {
        foreach (($cat['candidates'] ?? $cat['nominees'] ?? []) as $n) {
            if (($n['slug'] ?? '') === $nomineeSlug) {
                $nominee = $n;
                $nominee['category_name'] = $cat['name'] ?? '';
                break 2;
            }
        }
    }
}

$nomineesUrl  = SITE_URL . '/nominees' . ($eventSlug ? '?event=' . urlencode($eventSlug) : '');
$voteAgainUrl = SITE_URL . '/vote-bundle' . ($eventSlug ? '?event=' . urlencode($eventSlug) : '');
if ($nominee && !empty($nominee['id'])) {
    $voteAgainUrl .= '&nominee=' . (int)$nominee['id'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Votes Cast! | Tuqio Hub</title>
<link href="<?= SITE_URL ?>/assets/css/bootstrap.min.css" rel="stylesheet">
<link href="<?= SITE_URL ?>/assets/css/style.css" rel="stylesheet">
<link href="<?= SITE_URL ?>/assets/css/responsive.css" rel="stylesheet">
<link href="<?= SITE_URL ?>/assets/css/custom.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="icon" type="image/png" href="<?= SITE_URL ?>/assets/images/favicon/favicon-96x96.png" sizes="96x96">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
<style>
.vote-hero {
    background:linear-gradient(135deg,#1e1548,#2d1f6b);
    padding:70px 0 60px;text-align:center;color:#fff;
}
.vote-icon {
    width:90px;height:90px;border-radius:50%;
    background:linear-gradient(135deg,#ed1c24,#c41820);
    display:flex;align-items:center;justify-content:center;
    font-size:2.5rem;color:#fff;margin:0 auto 22px;
    box-shadow:0 10px 35px rgba(237,28,36,.45);
    animation:popIn .5s cubic-bezier(.175,.885,.32,1.275) both;
}
@keyframes popIn { from{transform:scale(0);opacity:0} to{transform:scale(1);opacity:1} }
.vote-count-big {
    font-size:4rem;font-weight:900;line-height:1;
    background:linear-gradient(135deg,#fbbf24,#f59e0b);
    -webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;
    margin:16px 0 4px;
}
.vote-count-label { font-size:1rem;opacity:.8;margin-bottom:6px; }

.nominee-card {
    background:#fff;border-radius:16px;box-shadow:0 8px 32px rgba(0,0,0,.1);
    overflow:hidden;margin:0 auto;max-width:480px;
}
.nc-img { width:100%;height:220px;object-fit:cover; }
.nc-img-placeholder {
    width:100%;height:220px;background:linear-gradient(135deg,#1e1548,#2d1f6b);
    display:flex;align-items:center;justify-content:center;font-size:4rem;color:rgba(255,255,255,.3);
}
.nc-body { padding:24px 28px; }
.nc-category { font-size:.72rem;text-transform:uppercase;letter-spacing:1.5px;color:#ed1c24;font-weight:700;margin-bottom:8px; }
.nc-name { font-size:1.3rem;font-weight:900;color:#1e1548;margin-bottom:4px; }
.nc-subtitle { font-size:.85rem;color:#888;margin-bottom:16px; }
.nc-votes-row { display:flex;align-items:center;gap:12px;padding:14px;background:#f9f9fb;border-radius:10px;margin-top:14px; }
.nc-votes-num { font-size:1.6rem;font-weight:900;color:#ed1c24; }
.nc-votes-label { font-size:.78rem;color:#888;font-weight:600; }

.action-wrap { max-width:480px;margin:24px auto 0;display:flex;flex-direction:column;gap:12px; }
.ab { display:block;padding:14px;border-radius:10px;font-size:.92rem;font-weight:700;text-align:center;text-decoration:none;border:none;cursor:pointer;transition:opacity .2s; }
.ab:hover { opacity:.9; }
.ab-primary { background:linear-gradient(135deg,#ed1c24,#c41820);color:#fff; }
.ab-navy    { background:#1e1548;color:#fff; }
.ab-outline { background:#fff;color:#1e1548;border:2px solid #1e1548; }

.share-bar { display:flex;justify-content:center;gap:10px;margin-top:8px;flex-wrap:wrap; }
.share-btn {
    display:inline-flex;align-items:center;gap:7px;padding:9px 20px;border-radius:24px;
    font-size:.82rem;font-weight:700;text-decoration:none;transition:opacity .2s;
}
.share-btn:hover { opacity:.85; }
.share-fb { background:#1877f2;color:#fff; }
.share-tw { background:#000;color:#fff; }
.share-wa { background:#25d366;color:#fff; }
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

<!-- Vote hero -->
<div class="vote-hero" style="<?= !$isSuccess ? 'background:linear-gradient(135deg,#374151,#1f2937);' : '' ?>">
    <?php if ($isSuccess): ?>
    <div class="vote-icon"><i class="fas fa-check"></i></div>
    <h1 style="font-size:2rem;font-weight:900;margin-bottom:8px;">Votes Confirmed!</h1>
    <?php if ($votes > 0): ?>
    <div class="vote-count-big" id="voteCounter">0</div>
    <div class="vote-count-label">vote<?= $votes !== 1 ? 's' : '' ?> added to <?= $nominee ? htmlspecialchars($nominee['name']) : 'your nominee' ?></div>
    <?php else: ?>
    <p style="font-size:1rem;opacity:.8;margin-top:8px;">Your payment was received and votes have been allocated.</p>
    <?php endif; ?>
    <?php elseif ($status === 'failed'): ?>
    <div class="vote-icon" style="background:linear-gradient(135deg,#dc2626,#991b1b);"><i class="fas fa-times"></i></div>
    <h1 style="font-size:2rem;font-weight:900;margin-bottom:8px;">Payment Not Completed</h1>
    <p style="font-size:1rem;opacity:.8;margin-top:8px;">Your payment was cancelled or declined. You have not been charged.</p>
    <?php else: ?>
    <div class="vote-icon" style="background:linear-gradient(135deg,#d97706,#92400e);"><i class="fas fa-exclamation-triangle"></i></div>
    <h1 style="font-size:2rem;font-weight:900;margin-bottom:8px;">Something Went Wrong</h1>
    <p style="font-size:1rem;opacity:.8;margin-top:8px;">We could not verify your payment. If you were charged, please contact support.</p>
    <?php endif; ?>
</div>

<!-- Content -->
<section class="shop-section" style="padding:50px 0 70px;">
    <div class="auto-container">
        <div style="max-width:520px;margin:0 auto;padding:0 16px;">

            <?php if ($nominee): ?>
            <!-- Nominee card -->
            <div class="nominee-card">
                <?php
                $nomImg = !empty($nominee['image'])     ? API_STORAGE . $nominee['image']
                        : (!empty($nominee['thumbnail']) ? API_STORAGE . $nominee['thumbnail'] : '');
                ?>
                <?php if ($nomImg): ?>
                <img class="nc-img" src="<?= htmlspecialchars($nomImg) ?>" alt="<?= htmlspecialchars($nominee['name']) ?>"
                     onerror="this.onerror=null;this.style.display='none';this.nextElementSibling.style.display='flex';">
                <div class="nc-img-placeholder" style="display:none;"><i class="fas fa-user"></i></div>
                <?php else: ?>
                <div class="nc-img-placeholder"><i class="fas fa-user"></i></div>
                <?php endif; ?>
                <div class="nc-body">
                    <?php if (!empty($nominee['category_name'])): ?>
                    <div class="nc-category"><i class="fas fa-award" style="margin-right:5px;"></i><?= htmlspecialchars($nominee['category_name']) ?></div>
                    <?php endif; ?>
                    <div class="nc-name"><?= htmlspecialchars($nominee['name']) ?></div>
                    <?php if (!empty($nominee['subtitle'])): ?>
                    <div class="nc-subtitle"><?= htmlspecialchars($nominee['subtitle']) ?></div>
                    <?php endif; ?>

                    <?php if (!empty($nominee['votes_count'])): ?>
                    <div class="nc-votes-row">
                        <div>
                            <div class="nc-votes-num"><?= number_format($nominee['votes_count']) ?></div>
                            <div class="nc-votes-label">Total votes so far</div>
                        </div>
                        <div style="flex:1;">
                            <div style="background:#f0f0f5;border-radius:6px;height:8px;overflow:hidden;">
                                <div style="height:100%;background:linear-gradient(90deg,#ed1c24,#f59e0b);border-radius:6px;width:100%;"></div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Action buttons -->
            <div class="action-wrap">
                <?php if ($isSuccess): ?>
                    <a href="<?= htmlspecialchars($nomineesUrl) ?>" class="ab ab-primary">
                        <i class="fas fa-chart-bar" style="margin-right:8px;"></i>View Live Vote Counts
                    </a>
                    <a href="<?= htmlspecialchars($voteAgainUrl) ?>" class="ab ab-navy">
                        <i class="fas fa-vote-yea" style="margin-right:8px;"></i>Vote Again<?= $nominee ? ' for ' . htmlspecialchars($nominee['name']) : '' ?>
                    </a>
                <?php elseif ($status === 'failed'): ?>
                    <?php if ($eventSlug): ?>
                    <a href="<?= htmlspecialchars($voteAgainUrl) ?>" class="ab ab-primary">
                        <i class="fas fa-redo" style="margin-right:8px;"></i>Try Again
                    </a>
                    <?php endif; ?>
                    <a href="<?= htmlspecialchars($nomineesUrl) ?>" class="ab ab-outline">
                        <i class="fas fa-arrow-left" style="margin-right:8px;"></i>Back to Nominees
                    </a>
                <?php else: ?>
                    <a href="<?= htmlspecialchars($nomineesUrl) ?>" class="ab ab-primary">
                        <i class="fas fa-arrow-left" style="margin-right:8px;"></i>Back to Nominees
                    </a>
                    <a href="<?= SITE_URL ?>/events" class="ab ab-outline">
                        <i class="fas fa-calendar-alt" style="margin-right:8px;"></i>Browse Events
                    </a>
                <?php endif; ?>
            </div>

            <!-- Share -->
            <?php
            $shareText = $nominee
                ? 'I just voted for ' . $nominee['name'] . ($event ? ' at ' . $event['name'] : '') . '! Cast your votes on Tuqio Hub.'
                : 'I just voted on Tuqio Hub! Cast your votes too.';
            $shareUrl  = SITE_URL . ($eventSlug ? '/nominees?event=' . urlencode($eventSlug) : '/events');
            $shareEnc  = urlencode($shareText . ' ' . $shareUrl);
            ?>
            <div style="margin-top:30px;text-align:center;">
                <div style="font-size:.78rem;text-transform:uppercase;letter-spacing:1px;color:#aaa;font-weight:700;margin-bottom:14px;">Share the love</div>
                <div class="share-bar">
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($shareUrl) ?>"
                       target="_blank" rel="noopener" class="share-btn share-fb">
                        <i class="fab fa-facebook-f"></i> Share
                    </a>
                    <a href="https://twitter.com/intent/tweet?text=<?= $shareEnc ?>"
                       target="_blank" rel="noopener" class="share-btn share-tw">
                        <i class="fab fa-x-twitter"></i> Tweet
                    </a>
                    <a href="https://wa.me/?text=<?= $shareEnc ?>"
                       target="_blank" rel="noopener" class="share-btn share-wa">
                        <i class="fab fa-whatsapp"></i> WhatsApp
                    </a>
                </div>
            </div>

        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
<?php include 'includes/footer-links.php'; ?>

</div><!-- /page-wrapper -->

<script src="<?= SITE_URL ?>/assets/js/jquery.js"></script>
<script src="<?= SITE_URL ?>/assets/js/bootstrap.min.js"></script>
<script src="<?= SITE_URL ?>/assets/js/main.js"></script>

<?php if ($isSuccess && $votes > 0): ?>
<script>
// Animated vote count
(function() {
    var target = <?= (int)$votes ?>;
    var el     = document.getElementById('voteCounter');
    if (!el) return;
    var start = null;
    var dur   = 1200;
    function animate(ts) {
        if (!start) start = ts;
        var prog = Math.min((ts - start) / dur, 1);
        var ease = 1 - Math.pow(1 - prog, 3);
        el.textContent = Math.round(ease * target).toLocaleString();
        if (prog < 1) requestAnimationFrame(animate);
    }
    setTimeout(function() { requestAnimationFrame(animate); }, 300);
})();

// Confetti
(function() {
    var colors = ['#ed1c24','#1e1548','#f59e0b','#10b981','#3b82f6','#fff'];
    var style  = document.createElement('style');
    style.textContent = '.cf{position:fixed;top:-20px;animation:cfFall linear forwards;pointer-events:none;z-index:9999}' +
        '@keyframes cfFall{0%{transform:translateY(0) rotate(0deg);opacity:1}100%{transform:translateY(110vh) rotate(720deg);opacity:0}}';
    document.head.appendChild(style);
    for (var i = 0; i < 80; i++) {
        (function(i) {
            setTimeout(function() {
                var el   = document.createElement('div');
                el.className = 'cf';
                var sz   = 6 + Math.random() * 8;
                el.style.cssText = 'left:' + (Math.random() * 100) + 'vw;width:' + sz + 'px;height:' + sz + 'px;background:' +
                    colors[Math.floor(Math.random() * colors.length)] + ';border-radius:' + (Math.random() > .5 ? '50%' : '2px') +
                    ';animation-duration:' + (2 + Math.random() * 2) + 's';
                document.body.appendChild(el);
                setTimeout(function() { el.remove(); }, 5000);
            }, i * 25);
        })(i);
    }
})();
</script>
<?php endif; ?>
</body>
</html>
