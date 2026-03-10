<?php
include 'config/config.php';
include 'libs/App.php';

$slug      = trim($_GET['event']    ?? '');
$nomineeId = (int)($_GET['nominee'] ?? 0);

if (!$slug) { header('Location: ' . SITE_URL . '/events.php'); exit; }

// Fetch event (voting must be open)
$eventResp = tuqio_api('/api/public/events/' . urlencode($slug));
$event     = $eventResp['event'] ?? null;
if (!$event || empty($event['has_voting'])) {
    header('Location: ' . SITE_URL . '/events.php'); exit;
}
if (empty($event['voting_is_open'])) {
    header('Location: ' . SITE_URL . '/nominees.php?event=' . urlencode($slug)); exit;
}

// Fetch vote bundles
$bundlesResp = tuqio_api('/api/public/events/' . urlencode($slug) . '/vote-bundles');
$bundles     = $bundlesResp['bundles'] ?? [];

// Fetch nominees to find pre-selected one
$nomResp    = tuqio_api('/api/public/events/' . urlencode($slug) . '/nominees');
$categories = $nomResp['categories'] ?? [];

// Build flat nominee list
$allNominees = [];
foreach ($categories as $cat) {
    foreach (($cat['candidates'] ?? $cat['nominees'] ?? []) as $n) {
        $n['_category'] = $cat['name'];
        $allNominees[$n['id']] = $n;
    }
}

$preNominee = $nomineeId && isset($allNominees[$nomineeId]) ? $allNominees[$nomineeId] : null;

// Event display helpers
$dateStr = !empty($event['start_date']) ? date('d M Y', strtotime($event['start_date'])) : 'TBD';
if (!empty($event['end_date']) && $event['end_date'] !== $event['start_date']) {
    $dateStr .= ' – ' . date('d M Y', strtotime($event['end_date']));
}
$venue     = implode(', ', array_filter([$event['venue_name'] ?? '', $event['venue_city'] ?? '']));
$banner    = !empty($event['banner_image'])    ? API_STORAGE . $event['banner_image']    : (SITE_URL . '/assets/slides/event.webp');
$thumbnail = !empty($event['thumbnail_image']) ? API_STORAGE . $event['thumbnail_image'] : $banner;

$initialsColors = ['#ed1c24', '#1e1548', '#2d1f6b', '#6c757d'];
$nomIdx = 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Vote — <?= htmlspecialchars($event['name']) ?> | Tuqio Hub</title>
<link href="<?= SITE_URL ?>/assets/css/bootstrap.min.css" rel="stylesheet">
<link href="<?= SITE_URL ?>/assets/css/style.css" rel="stylesheet">
<link href="<?= SITE_URL ?>/assets/css/responsive.css" rel="stylesheet">
<link href="<?= SITE_URL ?>/assets/css/custom.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="icon" type="image/png" href="<?= SITE_URL ?>/assets/images/favicon/favicon-96x96.png" sizes="96x96">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
<style>
/* ── Page layout ───────────────────────────────────────── */
.vote-wrap { max-width:1080px; margin:0 auto; padding:0 16px 60px; }

/* Nominee picker */
.nominee-pick-card {
    background:#fff; border:2px solid #eee; border-radius:12px;
    padding:14px 16px; cursor:pointer; transition:all .2s;
    display:flex; align-items:center; gap:14px; margin-bottom:8px;
}
.nominee-pick-card:hover,.nominee-pick-card.selected { border-color:#ed1c24; background:#fff8f8; }
.nom-thumb {
    width:50px; height:50px; border-radius:50%; object-fit:cover;
    flex-shrink:0; background:#eee;
}
.nom-initials-sm {
    width:50px; height:50px; border-radius:50%; flex-shrink:0;
    display:flex; align-items:center; justify-content:center;
    font-size:1.1rem; font-weight:800; color:#fff;
}
.nom-info { flex:1; min-width:0; }
.nom-info .nom-name { font-weight:700; color:#1e1548; font-size:.92rem; line-height:1.3; }
.nom-info .nom-cat  { font-size:.75rem; color:#888; }
.nom-info .nom-votes { font-size:.75rem; color:#ed1c24; font-weight:600; }
.nom-pick-check {
    width:22px; height:22px; border-radius:50%; border:2px solid #eee;
    display:flex; align-items:center; justify-content:center;
    flex-shrink:0; transition:all .2s; font-size:.7rem; color:#fff;
}
.nominee-pick-card.selected .nom-pick-check { background:#ed1c24; border-color:#ed1c24; }

/* Bundle cards */
.bundle-card {
    background:#fff; border:2px solid #eee; border-radius:12px;
    padding:18px 20px; cursor:pointer; transition:all .2s;
    position:relative; margin-bottom:10px;
    display:flex; align-items:center; justify-content:space-between; gap:12px;
}
.bundle-card:hover,.bundle-card.selected { border-color:#ed1c24; box-shadow:0 4px 18px rgba(237,28,36,.1); background:#fff8f8; }
.bundle-card .bc-badge {
    position:absolute; top:-10px; right:14px;
    background:#f59e0b; color:#fff; font-size:.64rem; font-weight:800;
    padding:2px 10px; border-radius:20px; text-transform:uppercase; letter-spacing:.5px;
}
.bundle-card .bc-left .bc-name  { font-weight:700; color:#1e1548; font-size:.95rem; margin-bottom:2px; }
.bundle-card .bc-left .bc-votes { font-size:.8rem; color:#888; }
.bundle-card .bc-right { text-align:right; flex-shrink:0; }
.bundle-card .bc-price { font-size:1.15rem; font-weight:800; color:#ed1c24; }
.bundle-card .bc-orig  { font-size:.75rem; color:#aaa; text-decoration:line-through; }
.bundle-card .bc-save  { font-size:.72rem; color:#10b981; font-weight:600; margin-left:6px; }
.bundle-pick-check {
    width:22px; height:22px; border-radius:50%; border:2px solid #eee;
    display:flex; align-items:center; justify-content:center;
    flex-shrink:0; transition:all .2s; font-size:.75rem; color:#fff;
    margin-left:12px;
}
.bundle-card.selected .bundle-pick-check { background:#ed1c24; border-color:#ed1c24; }

/* Voter form */
.voter-form { background:#fff; border-radius:14px; padding:28px 32px; box-shadow:0 4px 24px rgba(0,0,0,.07); margin-top:28px; }
.voter-form label { font-weight:600; font-size:.88rem; color:#1e1548; margin-bottom:6px; display:block; }
.voter-form input {
    width:100%; border:2px solid #eee; border-radius:8px; padding:10px 14px;
    font-size:.9rem; color:#333; background:#fafafa; transition:border-color .2s;
}
.voter-form input:focus { border-color:#ed1c24; outline:none; background:#fff; box-shadow:0 0 0 3px rgba(237,28,36,.08); }

/* Summary sidebar */
.order-summary { background:linear-gradient(160deg,#1e1548,#2d1f6b); border-radius:14px; padding:28px; color:#fff; position:sticky; top:100px; }
.order-summary h5 { font-weight:800; font-size:1rem; letter-spacing:.5px; margin-bottom:18px; text-transform:uppercase; }
.os-event-name { font-size:.95rem; font-weight:700; margin-bottom:4px; }
.os-meta { font-size:.78rem; opacity:.7; margin-bottom:20px; }
.os-line { display:flex; justify-content:space-between; font-size:.85rem; padding:7px 0; border-bottom:1px solid rgba(255,255,255,.1); gap:8px; }
.os-line:last-child { border-bottom:none; }
.os-line.total { font-weight:800; font-size:1.05rem; border-top:2px solid rgba(255,255,255,.2); padding-top:12px; margin-top:4px; border-bottom:none; }
.os-label { opacity:.75; }
.os-empty { font-size:.82rem; opacity:.6; text-align:center; padding:20px 0; }
.pay-btn {
    display:block; width:100%; margin-top:20px; padding:14px; border-radius:10px;
    background:linear-gradient(135deg,#ed1c24,#c41820); border:none;
    color:#fff; font-size:1rem; font-weight:800; cursor:pointer; text-align:center;
    transition:opacity .2s,transform .1s;
}
.pay-btn:hover:not(:disabled) { opacity:.9; transform:translateY(-1px); }
.pay-btn:disabled { opacity:.5; cursor:default; }
.secure-note { font-size:.73rem; opacity:.6; text-align:center; margin-top:10px; }

/* Section header */
.step-label {
    font-size:.75rem; font-weight:800; text-transform:uppercase; letter-spacing:1px;
    color:#888; margin-bottom:12px;
}
.step-label i { color:#ed1c24; margin-right:5px; }

/* Alert */
.vote-alert { border-radius:10px; padding:13px 16px; margin-bottom:18px; font-size:.87rem; }
.vote-alert.error { background:#fff0f0; color:#c41820; border:1px solid #fca5a5; }
.vote-alert.info  { background:#eff6ff; color:#1e40af; border:1px solid #93c5fd; }

/* Category tabs */
.cat-tab-btns { display:flex; flex-wrap:wrap; gap:8px; margin-bottom:18px; }
.cat-tab-btn  {
    padding:6px 14px; border-radius:20px; border:2px solid #eee;
    font-size:.8rem; font-weight:700; cursor:pointer; background:#fafafa; color:#1e1548;
    transition:all .2s;
}
.cat-tab-btn.active { background:#1e1548; border-color:#1e1548; color:#fff; }
.cat-tab-btn:hover:not(.active) { border-color:#ed1c24; color:#ed1c24; }
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

<!-- Breadcrumb -->
<section class="page-title" style="background-image:url(<?= SITE_URL ?>/assets/slides/kenya-breadcrump.webp);">
    <div class="anim-icons full-width"><span class="icon icon-bull-eye"></span><span class="icon icon-dotted-circle"></span></div>
    <div class="auto-container">
        <div class="title-outer">
            <h1>Cast Your Votes</h1>
            <ul class="page-breadcrumb">
                <li><a href="<?= SITE_URL ?>">Home</a></li>
                <li><a href="<?= SITE_URL ?>/nominees.php?event=<?= urlencode($slug) ?>"><?= htmlspecialchars($event['name']) ?></a></li>
                <li>Vote</li>
            </ul>
        </div>
    </div>
</section>

<!-- Main -->
<section class="shop-section" style="padding-top:50px;">
    <div class="auto-container vote-wrap">
        <div id="voteAlert"></div>

        <div class="row">
            <!-- LEFT -->
            <div class="col-lg-7 col-md-12">

                <!-- Step 1: Select nominee -->
                <h3 style="font-size:1.3rem;font-weight:800;color:#1e1548;margin-bottom:6px;">
                    <i class="fas fa-vote-yea" style="color:#ed1c24;margin-right:8px;"></i>
                    <?= empty($preNominee) ? 'Vote Now' : 'Voting for' ?>
                </h3>
                <p style="font-size:.85rem;color:#888;margin-bottom:26px;">
                    <?= htmlspecialchars($event['name']) ?> &mdash; <?= htmlspecialchars($dateStr) ?>
                    <?php if ($venue): ?>&middot; <?= htmlspecialchars($venue) ?><?php endif; ?>
                </p>

                <?php if (empty($allNominees)): ?>
                <div class="vote-alert info"><i class="fas fa-info-circle me-2"></i>No nominees available yet for this event.</div>
                <?php else: ?>

                <!-- Nominee picker -->
                <div class="step-label"><i class="fas fa-user"></i>Step 1 — Select a Nominee</div>

                <?php if (count($categories) > 1): ?>
                <!-- Category filter -->
                <div class="cat-tab-btns" id="catTabBtns">
                    <button class="cat-tab-btn active" data-cat="all" onclick="VotePage.filterCat('all', this)">All</button>
                    <?php foreach ($categories as $cat): ?>
                    <?php if (!empty($cat['candidates'] ?? $cat['nominees'] ?? [])): ?>
                    <button class="cat-tab-btn" data-cat="<?= htmlspecialchars($cat['slug']) ?>"
                            onclick="VotePage.filterCat(<?= htmlspecialchars(json_encode($cat['slug']), ENT_QUOTES) ?>, this)">
                        <?= htmlspecialchars($cat['name']) ?>
                    </button>
                    <?php endif; ?>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <div id="nomineeList">
                <?php foreach ($categories as $cat): foreach (($cat['candidates'] ?? $cat['nominees'] ?? []) as $n):
                    $nName    = $n['name'] ?? '';
                    $words    = array_filter(explode(' ', trim($nName)));
                    $initials = implode('', array_map(fn($w) => strtoupper($w[0] ?? ''), array_slice($words, 0, 2)));
                    $color    = $initialsColors[$nomIdx % 4];
                    $nomIdx++;
                    $nImage   = !empty($n['image']) ? API_STORAGE . $n['image'] : '';
                    $isPreSel = $preNominee && (int)$n['id'] === $nomineeId;
                ?>
                <div class="nominee-pick-card <?= $isPreSel ? 'selected' : '' ?>"
                     id="npcard-<?= (int)$n['id'] ?>"
                     data-nominee-id="<?= (int)$n['id'] ?>"
                     data-nominee-name="<?= htmlspecialchars($nName) ?>"
                     data-cat="<?= htmlspecialchars($cat['slug']) ?>"
                     onclick="VotePage.selectNominee(<?= (int)$n['id'] ?>, <?= htmlspecialchars(json_encode($nName), ENT_QUOTES) ?>)">
                    <?php if ($nImage): ?>
                    <img src="<?= htmlspecialchars($nImage) ?>" alt="<?= htmlspecialchars($nName) ?>"
                         class="nom-thumb"
                         onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                    <div class="nom-initials-sm" style="display:none;background:<?= $color ?>;"><?= $initials ?></div>
                    <?php else: ?>
                    <div class="nom-initials-sm" style="background:<?= $color ?>;"><?= $initials ?></div>
                    <?php endif; ?>
                    <div class="nom-info">
                        <div class="nom-name"><?= htmlspecialchars($nName) ?></div>
                        <?php if (!empty($n['subtitle'])): ?>
                        <div class="nom-cat"><?= htmlspecialchars($n['subtitle']) ?></div>
                        <?php else: ?>
                        <div class="nom-cat"><?= htmlspecialchars($cat['name']) ?></div>
                        <?php endif; ?>
                        <div class="nom-votes"><i class="fas fa-poll-h" style="margin-right:3px;"></i><?= number_format((int)($n['votes_count'] ?? 0)) ?> votes</div>
                    </div>
                    <div class="nom-pick-check" id="npcheck-<?= (int)$n['id'] ?>">
                        <i class="fas fa-check"></i>
                    </div>
                </div>
                <?php endforeach; endforeach; ?>
                </div>

                <!-- Step 2: Select bundle -->
                <div class="step-label" style="margin-top:32px;"><i class="fas fa-cubes"></i>Step 2 — Choose a Vote Package</div>

                <?php if (empty($bundles)): ?>
                <div class="vote-alert info"><i class="fas fa-info-circle me-2"></i>No vote packages are available for this event right now.</div>
                <?php else: ?>
                <?php foreach ($bundles as $b): ?>
                <div class="bundle-card"
                     id="bcard-<?= (int)$b['id'] ?>"
                     data-bundle-id="<?= (int)$b['id'] ?>"
                     data-vote-count="<?= (int)$b['vote_count'] ?>"
                     data-price="<?= (float)$b['price'] ?>"
                     onclick="VotePage.selectBundle(<?= (int)$b['id'] ?>)">
                    <?php if (!empty($b['is_featured'])): ?>
                    <div class="bc-badge"><i class="fas fa-star" style="margin-right:3px;"></i>Popular</div>
                    <?php endif; ?>
                    <div class="bc-left">
                        <div class="bc-name"><?= htmlspecialchars($b['name']) ?></div>
                        <div class="bc-votes"><?= number_format((int)$b['vote_count']) ?> votes</div>
                    </div>
                    <div style="display:flex;align-items:center;">
                        <div class="bc-right">
                            <span class="bc-price"><?= number_format($b['price'], 0) ?> <?= htmlspecialchars($b['currency'] ?? 'KES') ?></span>
                            <?php if (!empty($b['savings']) && $b['savings'] > 0): ?>
                            <span class="bc-save">Save <?= number_format($b['savings'], 0) ?></span>
                            <?php endif; ?>
                            <?php if (!empty($b['original_price']) && $b['original_price'] > $b['price']): ?>
                            <div class="bc-orig"><?= number_format($b['original_price'], 0) ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="bundle-pick-check" id="bcheck-<?= (int)$b['id'] ?>">
                            <i class="fas fa-check"></i>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>

                <?php endif; // allNominees ?>

                <!-- Step 3: Voter Details -->
                <div class="voter-form">
                    <div class="step-label" style="margin-bottom:18px;"><i class="fas fa-user-circle"></i>Step 3 — Your Details</div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="voter_name">Full Name <span style="color:#ed1c24;">*</span></label>
                            <input type="text" id="voter_name" placeholder="e.g. Jane Wanjiku" autocomplete="name">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="voter_email">Email Address <span style="color:#ed1c24;">*</span></label>
                            <input type="email" id="voter_email" placeholder="your@email.com" autocomplete="email">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label for="voter_phone">Phone <span style="font-size:.78rem;color:#aaa;font-weight:400;">(optional)</span></label>
                            <input type="tel" id="voter_phone" placeholder="+254 700 000 000" autocomplete="tel">
                        </div>
                    </div>
                </div>

            </div><!-- /col-left -->

            <!-- RIGHT: summary sidebar -->
            <div class="col-lg-5 col-md-12 mt-4 mt-lg-0">
                <div class="order-summary">
                    <h5><i class="fas fa-receipt" style="margin-right:8px;opacity:.8;"></i>Your Vote Summary</h5>
                    <div class="os-event-name"><?= htmlspecialchars($event['name']) ?></div>
                    <div class="os-meta"><?= htmlspecialchars($dateStr) ?><?php if ($venue): ?><br><?= htmlspecialchars($venue) ?><?php endif; ?></div>

                    <div id="osSummary">
                        <div class="os-empty">Select a nominee and vote package above</div>
                    </div>

                    <button class="pay-btn" id="payBtn" onclick="VotePage.pay()" disabled>
                        <i class="fas fa-lock" style="margin-right:8px;"></i>
                        Pay with Paystack
                    </button>
                    <div class="secure-note">
                        <i class="fas fa-shield-alt" style="margin-right:4px;"></i>
                        Secured by Paystack &middot; 256-bit SSL
                    </div>
                </div>

                <!-- Trust badges -->
                <div style="margin-top:20px;background:#fff;border-radius:12px;padding:20px;box-shadow:0 2px 12px rgba(0,0,0,.06);">
                    <div style="display:flex;gap:12px;align-items:flex-start;margin-bottom:14px;">
                        <i class="fas fa-bolt" style="color:#ed1c24;font-size:1.1rem;margin-top:2px;flex-shrink:0;"></i>
                        <div>
                            <div style="font-size:.83rem;font-weight:700;color:#1e1548;">Votes Applied Instantly</div>
                            <div style="font-size:.75rem;color:#888;">Your votes are credited immediately after payment confirmation</div>
                        </div>
                    </div>
                    <div style="display:flex;gap:12px;align-items:flex-start;margin-bottom:14px;">
                        <i class="fas fa-chart-bar" style="color:#ed1c24;font-size:1.1rem;margin-top:2px;flex-shrink:0;"></i>
                        <div>
                            <div style="font-size:.83rem;font-weight:700;color:#1e1548;">Live Vote Counts</div>
                            <div style="font-size:.75rem;color:#888;">Watch the leaderboard update in real time on the nominees page</div>
                        </div>
                    </div>
                    <div style="display:flex;gap:12px;align-items:flex-start;">
                        <i class="fas fa-redo" style="color:#ed1c24;font-size:1.1rem;margin-top:2px;flex-shrink:0;"></i>
                        <div>
                            <div style="font-size:.83rem;font-weight:700;color:#1e1548;">Vote Multiple Times</div>
                            <div style="font-size:.75rem;color:#888;">Buy additional vote packages to keep your favourite in the lead</div>
                        </div>
                    </div>
                </div>

                <!-- Event thumbnail -->
                <?php if (!empty($event['thumbnail_image']) || !empty($event['banner_image'])): ?>
                <div style="margin-top:20px;border-radius:12px;overflow:hidden;">
                    <img src="<?= htmlspecialchars($thumbnail) ?>" alt="<?= htmlspecialchars($event['name']) ?>"
                         style="width:100%;height:160px;object-fit:cover;">
                </div>
                <?php endif; ?>
            </div><!-- /col-right -->

        </div><!-- /row -->
    </div>
</section>

<?php include 'includes/footer.php'; ?>
</div>

<div class="scroll-to-top scroll-to-target" data-target="html"><span class="fa fa-angle-up"></span></div>
<?php include 'includes/footer-links.php'; ?>

<script>
const API_BASE   = '<?= API_BASE ?>';
const SITE_URL_JS = '<?= SITE_URL ?>';
const EVENT_SLUG = <?= json_encode($slug) ?>;
const PRE_NOMINEE_ID = <?= $preNominee ? (int)$preNominee['id'] : 'null' ?>;

window.VotePage = (function () {
    let selectedNomineeId   = PRE_NOMINEE_ID;
    let selectedNomineeName = <?= $preNominee ? json_encode($preNominee['name'] ?? '') : 'null' ?>;
    let selectedBundleId    = null;
    let selectedBundleVotes = 0;
    let selectedBundlePrice = 0;

    function renderSummary() {
        const $s   = document.getElementById('osSummary');
        const $btn = document.getElementById('payBtn');

        if (!selectedNomineeId || !selectedBundleId) {
            $s.innerHTML = '<div class="os-empty">Select a nominee and vote package above</div>';
            $btn.disabled = true;
            $btn.innerHTML = '<i class="fas fa-lock" style="margin-right:8px;"></i>Pay with Paystack';
            return;
        }

        const currency = 'KES';
        const fmt = n => Number(n).toLocaleString('en-KE', {minimumFractionDigits:0, maximumFractionDigits:0});
        $s.innerHTML = `
            <div class="os-line">
                <span class="os-label">Voting for</span>
                <span style="font-weight:700;text-align:right;max-width:55%;">${escHtml(selectedNomineeName)}</span>
            </div>
            <div class="os-line">
                <span class="os-label">Votes</span>
                <span style="font-weight:700;">${fmt(selectedBundleVotes)} votes</span>
            </div>
            <div class="os-line total">
                <span>Total</span>
                <span>${fmt(selectedBundlePrice)} ${currency}</span>
            </div>`;

        $btn.disabled = false;
        $btn.innerHTML = `<i class="fas fa-lock" style="margin-right:8px;"></i>Pay ${fmt(selectedBundlePrice)} ${currency}`;
    }

    function selectNominee(id, name) {
        // Deselect previous
        document.querySelectorAll('.nominee-pick-card').forEach(el => el.classList.remove('selected'));
        selectedNomineeId   = id;
        selectedNomineeName = name;
        const card = document.getElementById('npcard-' + id);
        if (card) card.classList.add('selected');
        renderSummary();
    }

    function selectBundle(id) {
        document.querySelectorAll('.bundle-card').forEach(el => el.classList.remove('selected'));
        const card = document.getElementById('bcard-' + id);
        if (card) {
            card.classList.add('selected');
            selectedBundleId    = id;
            selectedBundleVotes = parseInt(card.dataset.voteCount);
            selectedBundlePrice = parseFloat(card.dataset.price);
        }
        renderSummary();
    }

    function filterCat(catSlug, btn) {
        // Update active tab
        document.querySelectorAll('.cat-tab-btn').forEach(b => b.classList.remove('active'));
        if (btn) btn.classList.add('active');
        // Show/hide nominee cards
        document.querySelectorAll('.nominee-pick-card').forEach(function(el) {
            el.style.display = (catSlug === 'all' || el.dataset.cat === catSlug) ? '' : 'none';
        });
    }

    function showAlert(msg, type) {
        const el = document.getElementById('voteAlert');
        el.innerHTML = `<div class="vote-alert ${type}">${msg}</div>`;
        window.scrollTo({top: 0, behavior: 'smooth'});
    }

    function clearAlert() {
        document.getElementById('voteAlert').innerHTML = '';
    }

    function pay() {
        clearAlert();
        const name  = document.getElementById('voter_name').value.trim();
        const email = document.getElementById('voter_email').value.trim();
        const phone = document.getElementById('voter_phone').value.trim();

        if (!selectedNomineeId) { showAlert('<i class="fas fa-exclamation-circle me-2"></i>Please select a nominee to vote for.', 'error'); return; }
        if (!selectedBundleId)  { showAlert('<i class="fas fa-exclamation-circle me-2"></i>Please choose a vote package.', 'error'); return; }
        if (!name)  { showAlert('<i class="fas fa-exclamation-circle me-2"></i>Please enter your full name.', 'error'); return; }
        if (!email) { showAlert('<i class="fas fa-exclamation-circle me-2"></i>Please enter your email address.', 'error'); return; }
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) { showAlert('<i class="fas fa-exclamation-circle me-2"></i>Please enter a valid email address.', 'error'); return; }

        const payload = {
            bundle_id:   selectedBundleId,
            nominee_id:  parseInt(selectedNomineeId),
            voter_name:  name,
            voter_email: email,
        };
        if (phone) payload.voter_phone = phone;

        const $btn = document.getElementById('payBtn');
        $btn.disabled = true;
        $btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Preparing payment…';

        fetch(API_BASE + '/api/public/events/' + encodeURIComponent(EVENT_SLUG) + '/vote-bundle/checkout', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify(payload)
        })
        .then(r => r.json())
        .then(data => {
            if (data.payment_url) {
                window.location.href = data.payment_url;
            } else {
                const msg = data.error || data.message || (data.errors ? Object.values(data.errors).flat().join('<br>') : 'Checkout failed.');
                showAlert('<i class="fas fa-exclamation-circle me-2"></i>' + msg, 'error');
                $btn.disabled = false;
                $btn.innerHTML = '<i class="fas fa-lock" style="margin-right:8px;"></i>Try Again';
                renderSummary();
            }
        })
        .catch(() => {
            showAlert('<i class="fas fa-exclamation-circle me-2"></i>Network error. Please check your connection and try again.', 'error');
            $btn.disabled = false;
            $btn.innerHTML = '<i class="fas fa-lock" style="margin-right:8px;"></i>Try Again';
            renderSummary();
        });
    }

    function escHtml(s) { const d = document.createElement('div'); d.textContent = s; return d.innerHTML; }

    // Init: render summary if nominee pre-selected
    renderSummary();

    // Scroll to pre-selected nominee
    if (PRE_NOMINEE_ID) {
        setTimeout(function() {
            var el = document.getElementById('npcard-' + PRE_NOMINEE_ID);
            if (el) el.scrollIntoView({behavior:'smooth', block:'nearest'});
        }, 400);
    }

    return { selectNominee, selectBundle, filterCat, pay };
})();
</script>
</body>
</html>
