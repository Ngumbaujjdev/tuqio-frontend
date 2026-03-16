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

// Derive standard per-vote rate from the FIRST available bundle (standard/base rate)
$pricePerVote = 10.0;
$minCustomAmt = 100;
if (!empty($bundles)) {
    foreach ($bundles as $b) {
        if ((int)($b['vote_count'] ?? 0) > 0 && (float)($b['price'] ?? 0) > 0) {
            $pricePerVote = (float)$b['price'] / (int)$b['vote_count'];
            break; // first bundle sets the standard rate
        }
    }
    $prices = array_filter(array_column($bundles, 'price'), fn($p) => (float)$p > 0);
    if ($prices) {
        $minCustomAmt = (int) ceil(min($prices));
    }
}

// Fetch nominees to find pre-selected one
$nomResp    = tuqio_api('/api/public/events/' . urlencode($slug) . '/nominees');
$categories = $nomResp['categories'] ?? [];

// Build flat nominee list
$allNominees = [];
foreach ($categories as $cat) {
    foreach (($cat['candidates'] ?? $cat['nominees'] ?? []) as $n) {
        $n['_category']     = $cat['name'];
        $n['category_slug'] = $cat['slug'];
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
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/css/intlTelInput.min.css">
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
/* intl-tel-input */
.voter-form .iti { width:100%; }
.voter-form .iti input { border-radius:0 8px 8px 0; padding-left:10px; }
.voter-form .iti--separate-dial-code .iti__selected-flag { background:#fafafa; border-right:1px solid #eee; border-radius:8px 0 0 8px; }
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

/* ── Step progress headers ────────────────────────────────── */
.step-hdr { display:flex; align-items:center; gap:12px; margin-bottom:16px; }
.step-num {
    width:34px; height:34px; border-radius:50%; flex-shrink:0;
    display:flex; align-items:center; justify-content:center;
    font-size:.88rem; font-weight:800; transition:background .25s,color .25s;
    background:#ed1c24; color:#fff;
}
.step-num.done     { background:#10b981; }
.step-num.upcoming { background:#e9ecef; color:#aaa; }
.step-hdr-title { font-size:.98rem; font-weight:800; color:#1e1548; line-height:1.2; }
.step-hdr-sub   { font-size:.74rem; color:#aaa; margin-top:2px; }

/* Voter form step hint */
.form-lock-hint {
    font-size:.82rem; color:#888; background:#f8f8fb;
    border-radius:8px; padding:10px 14px; margin-bottom:16px;
    border-left:3px solid #ddd;
}

/* ── Mobile sticky pay bar ────────────────────────────────── */
.mob-pay-bar {
    display:none; position:fixed; bottom:0; left:0; right:0; z-index:9990;
    background:#1e1548; padding:12px 16px 18px;
    box-shadow:0 -4px 24px rgba(0,0,0,.3);
}
@media (max-width:991px) { .mob-pay-bar.show { display:block; } }
body.mob-pay-on { padding-bottom:120px !important; }
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
                <div class="step-hdr" id="step1Hdr">
                    <div class="step-num" id="step1Num">1</div>
                    <div>
                        <div class="step-hdr-title">Select a Nominee</div>
                        <div class="step-hdr-sub" id="step1Sub">Choose who you're voting for</div>
                    </div>
                </div>

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
                <?php foreach ($categories as $cat):
                    $catNomIdx = 0;
                    foreach (($cat['candidates'] ?? $cat['nominees'] ?? []) as $n):
                    $catNomIdx++;
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
                     data-cat-idx="<?= $catNomIdx ?>"
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
                <div id="showMoreNominees" style="display:none;text-align:center;margin:12px 0 4px;">
                    <button type="button" onclick="VotePage.showAllNominees(this)"
                            style="background:none;border:1.5px solid #C23014;color:#C23014;border-radius:20px;padding:7px 22px;font-size:13px;cursor:pointer;font-weight:600;">
                        <i class="fas fa-chevron-down" style="margin-right:6px;"></i>Show <span id="showMoreCount"></span> more nominees
                    </button>
                </div>

                <!-- Step 2: Select bundle -->
                <div class="step-hdr" style="margin-top:36px;" id="step2Hdr">
                    <div class="step-num upcoming" id="step2Num">2</div>
                    <div>
                        <div class="step-hdr-title">Choose a Vote Package</div>
                        <div class="step-hdr-sub" id="step2Sub">Pick a bundle or enter your own amount below</div>
                    </div>
                </div>

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

                <?php if (!empty($bundles)): ?>
                <!-- Custom amount option -->
                <div style="text-align:center;margin:18px 0 10px;font-size:.75rem;color:#aaa;font-weight:700;text-transform:uppercase;letter-spacing:1.2px;">— or enter your own amount —</div>
                <div id="customAmountSection" style="background:#fff;border:2px solid #eee;border-radius:12px;padding:18px 20px;transition:border-color .2s,background .2s;">
                    <div style="font-size:.75rem;font-weight:800;color:#aaa;text-transform:uppercase;letter-spacing:.8px;margin-bottom:12px;">
                        <i class="fas fa-coins" style="color:#ed1c24;margin-right:5px;"></i>Enter any amount
                    </div>
                    <div style="display:flex;align-items:stretch;">
                        <span style="background:#f0f0f5;border:2px solid #eee;border-right:none;border-radius:8px 0 0 8px;padding:0 14px;display:flex;align-items:center;font-weight:700;color:#888;font-size:.9rem;white-space:nowrap;">KES</span>
                        <input type="number" id="customAmountInput" placeholder="e.g. 2500"
                               min="<?= $minCustomAmt ?>" step="100"
                               style="flex:1;border:2px solid #eee;border-left:none;border-radius:0 8px 8px 0;padding:10px 14px;font-size:.92rem;color:#333;background:#fafafa;outline:none;"
                               oninput="VotePage.calcCustomVotes(this.value)"
                               onfocus="this.style.borderColor='#ed1c24';this.previousElementSibling.style.borderColor='#ed1c24';"
                               onblur="this.style.borderColor='#eee';this.previousElementSibling.style.borderColor='#eee';">
                    </div>
                    <div id="customVotePreview" style="display:none;margin-top:10px;padding:10px 14px;background:#f0fdf4;border-radius:8px;font-size:.85rem;color:#1e1548;"></div>
                    <div style="margin-top:8px;font-size:.74rem;color:#aaa;">Rate: <?= number_format($pricePerVote, 2) ?> KES per vote (best available rate) &middot; Min <?= number_format($minCustomAmt, 0) ?> KES</div>
                </div>
                <?php endif; ?>

                <?php endif; // allNominees ?>

                <!-- Step 3: Voter Details -->
                <div class="voter-form" id="step3Section">
                    <div class="step-hdr" id="step3Hdr" style="margin-bottom:6px;">
                        <div class="step-num upcoming" id="step3Num">3</div>
                        <div>
                            <div class="step-hdr-title">Your Details</div>
                            <div class="step-hdr-sub">Enter your name, email and phone</div>
                        </div>
                    </div>
                    <div class="form-lock-hint" id="formLockHint" style="margin-top:10px;">
                        <i class="fas fa-arrow-up" style="margin-right:6px;color:#aaa;"></i>
                        Select a nominee and a vote package above to continue
                    </div>
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
                            <label for="voter_phone">Phone Number <span style="color:#ed1c24;">*</span></label>
                            <input type="tel" id="voter_phone" autocomplete="tel">
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

                    <!-- Kenya: M-Pesa + Card | International: Card only -->
                    <div id="payMethodWrap">
                        <button class="pay-btn" id="payBtnMpesa" onclick="VotePage.pay('mpesa')" disabled
                                style="display:none;background:linear-gradient(135deg,#4caf50 0%,#2e7d32 100%);margin-bottom:10px;">
                            <i class="fas fa-mobile-alt" style="margin-right:8px;"></i>
                            Pay with M-Pesa
                        </button>
                        <button class="pay-btn" id="payBtnCard" onclick="VotePage.pay('card')" disabled>
                            <i class="fas fa-credit-card" style="margin-right:8px;"></i>
                            Pay with Card
                        </button>
                    </div>

                    <!-- Confirmation modal (pure CSS/JS, no Bootstrap dependency) -->
                    <div id="payConfirmModal" onclick="if(event.target===this)VotePage.closeConfirm()" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;z-index:99999;background:rgba(0,0,0,.55);align-items:center;justify-content:center;padding:16px;">
                        <div style="background:#fff;border-radius:16px;max-width:400px;width:100%;box-shadow:0 20px 60px rgba(0,0,0,.25);overflow:hidden;">
                            <!-- Header -->
                            <div style="background:linear-gradient(135deg,#1e1548,#2d1f6b);padding:20px 24px;color:#fff;">
                                <div style="font-size:1rem;font-weight:800;margin-bottom:2px;">Confirm Your Vote</div>
                                <div style="font-size:.78rem;opacity:.75;">Please review before paying</div>
                            </div>
                            <!-- Body -->
                            <div style="padding:22px 24px;">
                                <div id="payConfirmBody"></div>
                                <div style="display:flex;gap:10px;margin-top:20px;">
                                    <button type="button" onclick="VotePage.closeConfirm()"
                                            style="flex:1;padding:12px;border-radius:9px;border:2px solid #eee;background:#fff;font-weight:700;font-size:.88rem;color:#555;cursor:pointer;">
                                        <i class="fas fa-times" style="margin-right:6px;"></i>Cancel
                                    </button>
                                    <button type="button" id="payConfirmBtn" onclick="VotePage.confirmPay()"
                                            style="flex:2;padding:12px;border-radius:9px;border:none;font-weight:800;font-size:.88rem;color:#fff;cursor:pointer;transition:opacity .2s;">
                                        <i class="fas fa-lock" style="margin-right:6px;"></i>Confirm &amp; Pay
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
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

<!-- ── Mobile sticky pay bar ───────────────────────────────────────────── -->
<div class="mob-pay-bar" id="mobPayBar">
    <div style="max-width:540px;margin:0 auto;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
            <div style="flex:1;min-width:0;margin-right:12px;">
                <div id="mobPayNominee" style="font-size:.74rem;color:rgba(255,255,255,.65);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"></div>
                <div id="mobPayVotes" style="font-size:.86rem;font-weight:700;color:#fff;"></div>
            </div>
            <div id="mobPayTotal" style="font-size:1.1rem;font-weight:900;color:#f59e0b;flex-shrink:0;"></div>
        </div>
        <div style="display:flex;gap:8px;">
            <button id="mobBtnMpesa" onclick="VotePage.pay('mpesa')"
                    style="flex:1;display:none;padding:12px 8px;border-radius:8px;border:none;background:#4caf50;color:#fff;font-weight:800;font-size:.88rem;cursor:pointer;">
                <i class="fas fa-mobile-alt" style="margin-right:5px;"></i>M-Pesa
            </button>
            <button id="mobBtnCard" onclick="VotePage.pay('card')"
                    style="flex:2;padding:12px 8px;border-radius:8px;border:none;background:linear-gradient(135deg,#ed1c24,#c41820);color:#fff;font-weight:800;font-size:.9rem;cursor:pointer;">
                <i class="fas fa-credit-card" style="margin-right:5px;"></i>Pay <span id="mobPayAmt"></span> KES
            </button>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
</div>

<div class="scroll-to-top scroll-to-target" data-target="html"><span class="fa fa-angle-up"></span></div>
<?php include 'includes/footer-links.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/js/intlTelInput.min.js"></script>

<script>
const API_BASE   = '<?= API_BASE ?>';
const SITE_URL_JS = '<?= SITE_URL ?>';
const EVENT_SLUG = <?= json_encode($slug) ?>;
const PRE_NOMINEE_ID  = <?= $preNominee ? (int)$preNominee['id'] : 'null' ?>;
const PRE_NOMINEE_CAT = <?= $preNominee ? json_encode($preNominee['category_slug'] ?? '') : 'null' ?>;
const PRICE_PER_VOTE  = <?= json_encode(round($pricePerVote, 4)) ?>;  // KES per vote — from first bundle
const MIN_CUSTOM_AMT  = <?= (int)$minCustomAmt ?>;
const MPESA_LIMIT     = 150000; // Paystack M-Pesa single-transaction cap (KES)

// ── Country detection ────────────────────────────────────────────────────────
(function detectVoterCountry() {
    var cached = localStorage.getItem('voter_country');
    if (cached) { window._voterCountry = cached; applyCountryUI(); return; }

    // Layer 1: timezone (instant)
    try {
        var tz = Intl.DateTimeFormat().resolvedOptions().timeZone;
        if (tz === 'Africa/Nairobi') { saveCountry('KE'); return; }
    } catch(e) {}

    // Layer 2: IP (async, non-blocking)
    fetch('https://ipapi.co/json/')
        .then(function(r){ return r.json(); })
        .then(function(d){ saveCountry((d.country_code || 'KE').toUpperCase()); })
        .catch(function(){ saveCountry('KE'); });

    function saveCountry(code) {
        localStorage.setItem('voter_country', code);
        window._voterCountry = code;
        applyCountryUI();
    }
})();

function applyCountryUI(countryCode) {
    var code = countryCode || window._voterCountry || 'KE';
    window._voterCountry = code;
    var isKenya = (code.toUpperCase() === 'KE');
    var mpesaBtn = document.getElementById('payBtnMpesa');
    if (mpesaBtn) mpesaBtn.style.display = isKenya ? '' : 'none';
    // Mobile bar M-Pesa button
    var mobMpesa = document.getElementById('mobBtnMpesa');
    if (mobMpesa) mobMpesa.style.display = isKenya ? '' : 'none';
    // Sync ITI flag if initialized
    if (window._voterITI) {
        try { window._voterITI.setCountry(code.toLowerCase()); } catch(e) {}
    }
}

window.VotePage = (function () {
    let selectedNomineeId   = PRE_NOMINEE_ID;
    let selectedNomineeName = <?= $preNominee ? json_encode($preNominee['name'] ?? '') : 'null' ?>;
    let selectedBundleId    = null;
    let selectedBundleVotes = 0;
    let selectedBundlePrice = 0;
    // Custom amount state
    let _useCustom    = false;
    let _customAmount = 0;
    let _customVotes  = 0;

    function renderSummary() {
        const $s = document.getElementById('osSummary');
        const ready = selectedNomineeId && (selectedBundleId || (_useCustom && _customVotes > 0));
        const fmt   = n => Number(n).toLocaleString('en-KE', {minimumFractionDigits:0, maximumFractionDigits:0});

        if (!ready) {
            $s.innerHTML = '<div class="os-empty">Select a nominee and vote package above</div>';
            document.getElementById('payBtnMpesa').disabled = true;
            document.getElementById('payBtnCard').disabled  = true;
            document.getElementById('payBtnCard').innerHTML = '<i class="fas fa-lock" style="margin-right:8px;"></i>Pay with Card';
            return;
        }

        const currency = 'KES';
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

        const label = `${fmt(selectedBundlePrice)} ${currency}`;
        document.getElementById('payBtnMpesa').disabled = false;
        document.getElementById('payBtnMpesa').innerHTML = `<i class="fas fa-mobile-alt" style="margin-right:8px;"></i>Pay ${label} via M-Pesa`;
        document.getElementById('payBtnCard').disabled  = false;
        document.getElementById('payBtnCard').innerHTML  = `<i class="fas fa-credit-card" style="margin-right:8px;"></i>Pay ${label} via Card`;
    }

    function selectNominee(id, name) {
        document.querySelectorAll('.nominee-pick-card').forEach(el => el.classList.remove('selected'));
        selectedNomineeId   = id;
        selectedNomineeName = name;
        const card = document.getElementById('npcard-' + id);
        if (card) card.classList.add('selected');
        renderSummary();
        updateStepUI();
        // Auto-scroll to bundle section if no package selected yet
        if (!selectedBundleId && !_useCustom) scrollNext('step2Hdr');
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
        // Clear custom amount state
        _useCustom = false;
        _customAmount = 0;
        _customVotes  = 0;
        const customSec = document.getElementById('customAmountSection');
        if (customSec) { customSec.style.borderColor = '#eee'; customSec.style.background = '#fff'; }
        const customInput = document.getElementById('customAmountInput');
        if (customInput) customInput.value = '';
        const customPreview = document.getElementById('customVotePreview');
        if (customPreview) customPreview.style.display = 'none';
        const applyBtn = document.getElementById('applyCustomBtn');
        if (applyBtn) { applyBtn.disabled = true; applyBtn.style.opacity = '.5'; }
        renderSummary();
        updateStepUI();
        // Auto-scroll to voter details
        scrollNext('step3Hdr');
    }

    function filterCat(catSlug, btn) {
        // Update active tab
        document.querySelectorAll('.cat-tab-btn').forEach(b => b.classList.remove('active'));
        if (btn) btn.classList.add('active');

        var hidden = 0;
        document.querySelectorAll('.nominee-pick-card').forEach(function(el) {
            var inCat = (catSlug === 'all' || el.dataset.cat === catSlug);
            if (!inCat) { el.style.display = 'none'; el.classList.remove('nom-overflow'); return; }
            var idx = parseInt(el.dataset.catIdx) || 0;
            var isPreSel = PRE_NOMINEE_ID && parseInt(el.dataset.nomineeId) === parseInt(PRE_NOMINEE_ID);
            if (idx <= 10 || isPreSel) {
                el.style.display = '';
                el.classList.remove('nom-overflow');
            } else {
                el.style.display = 'none';
                el.classList.add('nom-overflow');
                hidden++;
            }
        });

        var showMoreEl = document.getElementById('showMoreNominees');
        if (hidden > 0) {
            document.getElementById('showMoreCount').textContent = hidden;
            showMoreEl.style.display = '';
        } else {
            showMoreEl.style.display = 'none';
        }
    }

    function showAllNominees(btnEl) {
        document.querySelectorAll('.nominee-pick-card.nom-overflow').forEach(function(el) {
            el.style.display = '';
            el.classList.remove('nom-overflow');
        });
        document.getElementById('showMoreNominees').style.display = 'none';
    }

    function calcCustomVotes(val) {
        const fmt = n => Number(n).toLocaleString('en-KE', {minimumFractionDigits:0, maximumFractionDigits:0});
        const amt = parseFloat(val) || 0;
        const preview   = document.getElementById('customVotePreview');
        const applyBtn  = document.getElementById('applyCustomBtn');
        const customSec = document.getElementById('customAmountSection');

        if (amt < MIN_CUSTOM_AMT || amt <= 0) {
            if (preview)  preview.style.display = 'none';
            if (applyBtn) { applyBtn.disabled = true; applyBtn.style.opacity = '.5'; }
            // If custom was active, reset sidebar to empty
            if (_useCustom) {
                _useCustom = false; _customAmount = 0; _customVotes = 0;
                selectedBundleVotes = 0; selectedBundlePrice = 0;
                if (customSec) { customSec.style.borderColor = '#eee'; customSec.style.background = '#fff'; }
                renderSummary();
                updateStepUI();
            }
            return;
        }

        const votes = Math.floor(amt / PRICE_PER_VOTE);
        if (votes < 1) {
            if (preview)  preview.style.display = 'none';
            if (applyBtn) { applyBtn.disabled = true; applyBtn.style.opacity = '.5'; }
            return;
        }

        // Update inline preview
        if (preview) {
            preview.style.display = '';
            let html = `<i class="fas fa-check-circle" style="color:#10b981;margin-right:6px;"></i>`
                + `KES ${fmt(amt)} = <strong>${fmt(votes)} votes</strong> at ${PRICE_PER_VOTE} KES/vote`;
            if (amt > MPESA_LIMIT) {
                html += `<div style="margin-top:8px;padding:8px 10px;background:#fff3cd;border-radius:6px;color:#856404;font-size:.8rem;">`
                    + `<i class="fas fa-exclamation-triangle" style="margin-right:5px;"></i>`
                    + `<strong>M-Pesa limit:</strong> Amounts above KES ${MPESA_LIMIT.toLocaleString()} require Card payment.`
                    + `</div>`;
            }
            preview.innerHTML = html;
        }
        if (applyBtn) { applyBtn.disabled = false; applyBtn.style.opacity = '1'; }

        // ── Live-update sidebar, same as clicking a bundle card ──────────────
        document.querySelectorAll('.bundle-card').forEach(el => el.classList.remove('selected'));
        selectedBundleId    = null;
        const wasCustom     = _useCustom;
        _useCustom          = true;
        _customAmount       = amt;
        _customVotes        = votes;
        selectedBundleVotes = votes;
        selectedBundlePrice = amt;
        if (customSec) { customSec.style.borderColor = '#ed1c24'; customSec.style.background = '#fff8f8'; }
        renderSummary();
        updateStepUI();
        // First time entering custom → scroll to voter details
        if (!wasCustom) scrollNext('step3Hdr');
    }

    function applyCustomAmount() {
        const input = document.getElementById('customAmountInput');
        const amt   = parseFloat(input ? input.value : 0) || 0;
        if (amt < MIN_CUSTOM_AMT) return;
        const votes = Math.floor(amt / PRICE_PER_VOTE);
        if (votes < 1) return;

        // Deselect any bundle
        document.querySelectorAll('.bundle-card').forEach(el => el.classList.remove('selected'));
        selectedBundleId = null;

        // Set custom state
        _useCustom          = true;
        _customAmount       = amt;
        _customVotes        = votes;
        selectedBundleVotes = votes;
        selectedBundlePrice = amt;

        // Highlight custom section
        const customSec = document.getElementById('customAmountSection');
        if (customSec) { customSec.style.borderColor = '#ed1c24'; customSec.style.background = '#fff8f8'; }

        renderSummary();
    }

    // ── UX helpers ─────────────────────────────────────────────────────────
    function scrollNext(id) {
        setTimeout(function() {
            var el = document.getElementById(id);
            if (!el) return;
            var top = el.getBoundingClientRect().top + window.pageYOffset - 90;
            window.scrollTo({ top: Math.max(0, top), behavior: 'smooth' });
        }, 180);
    }

    function updateStepUI() {
        const fmt = n => Number(n).toLocaleString('en-KE', {minimumFractionDigits:0, maximumFractionDigits:0});
        const hasNom = !!selectedNomineeId;
        const hasPkg = !!(selectedBundleId || (_useCustom && _customVotes > 0));
        const ready  = hasNom && hasPkg;

        // Step circles
        const n1 = document.getElementById('step1Num');
        const n2 = document.getElementById('step2Num');
        const n3 = document.getElementById('step3Num');
        if (n1) { n1.className = 'step-num' + (hasNom ? ' done' : ''); n1.textContent = hasNom ? '✓' : '1'; }
        if (n2) { n2.className = 'step-num' + (hasPkg ? ' done' : (hasNom ? '' : ' upcoming')); n2.textContent = hasPkg ? '✓' : '2'; }
        if (n3) { n3.className = 'step-num' + (ready  ? '' : ' upcoming'); }

        // Step 1 sub-label
        var s1 = document.getElementById('step1Sub');
        if (s1) s1.textContent = hasNom ? 'Voting for ' + selectedNomineeName : 'Choose who you\'re voting for';

        // Form hint
        var hint = document.getElementById('formLockHint');
        if (hint) hint.style.display = ready ? 'none' : '';

        // Mobile sticky pay bar (only on screens < 992px)
        var bar = document.getElementById('mobPayBar');
        if (bar) {
            if (ready && window.innerWidth < 992) {
                bar.classList.add('show');
                document.body.classList.add('mob-pay-on');
                var el;
                el = document.getElementById('mobPayNominee'); if (el) el.textContent = selectedNomineeName;
                el = document.getElementById('mobPayVotes');   if (el) el.textContent = fmt(selectedBundleVotes) + ' votes';
                el = document.getElementById('mobPayTotal');   if (el) el.textContent = 'KES ' + fmt(selectedBundlePrice);
                el = document.getElementById('mobPayAmt');     if (el) el.textContent = fmt(selectedBundlePrice);
                var isKE = (window._voterCountry || 'KE').toUpperCase() === 'KE';
                el = document.getElementById('mobBtnMpesa'); if (el) el.style.display = isKE ? '' : 'none';
            } else {
                bar.classList.remove('show');
                document.body.classList.remove('mob-pay-on');
            }
        }
    }

    function showAlert(msg, type) {
        const el = document.getElementById('voteAlert');
        el.innerHTML = `<div class="vote-alert ${type}">${msg}</div>`;
        window.scrollTo({top: 0, behavior: 'smooth'});
    }

    function clearAlert() {
        document.getElementById('voteAlert').innerHTML = '';
    }

    // Holds pending payment details while confirm modal is open
    let _pendingPayload = null;
    let _pendingMethod  = null;

    function pay(method) {
        clearAlert();
        const name  = document.getElementById('voter_name').value.trim();
        const email = document.getElementById('voter_email').value.trim();
        const phone = window._voterITI ? window._voterITI.getNumber() : document.getElementById('voter_phone').value.trim();

        if (!selectedNomineeId) { showAlert('<i class="fas fa-exclamation-circle me-2"></i>Please select a nominee to vote for.', 'error'); return; }
        if (!selectedBundleId && !_useCustom) { showAlert('<i class="fas fa-exclamation-circle me-2"></i>Please choose a vote package or enter a custom amount.', 'error'); return; }
        if (_useCustom && _customAmount < MIN_CUSTOM_AMT) { showAlert('<i class="fas fa-exclamation-circle me-2"></i>Custom amount is below the minimum. Please enter at least KES ' + MIN_CUSTOM_AMT.toLocaleString() + '.', 'error'); return; }

        // M-Pesa single-transaction limit (KES 150,000 via Paystack)
        if (method === 'mpesa' && selectedBundlePrice > MPESA_LIMIT) {
            showAlert(
                '<i class="fas fa-exclamation-triangle me-2"></i>'
                + '<strong>Amount exceeds M-Pesa\'s single transaction limit of KES ' + MPESA_LIMIT.toLocaleString() + '.</strong>'
                + '<br>Please use <strong>Card payment</strong> for this amount.',
                'error'
            );
            return;
        }
        if (!name)  { showAlert('<i class="fas fa-exclamation-circle me-2"></i>Please enter your full name.', 'error'); return; }
        if (!email) { showAlert('<i class="fas fa-exclamation-circle me-2"></i>Please enter your email address.', 'error'); return; }
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) { showAlert('<i class="fas fa-exclamation-circle me-2"></i>Please enter a valid email address.', 'error'); return; }
        if (!phone || (window._voterITI && !window._voterITI.isValidNumber())) {
            showAlert('<i class="fas fa-exclamation-circle me-2"></i>Please enter a valid phone number.', 'error'); return;
        }

        if (_useCustom) {
            _pendingPayload = {
                custom_amount:  _customAmount,
                custom_votes:   _customVotes,
                nominee_id:     parseInt(selectedNomineeId),
                voter_name:     name,
                voter_email:    email,
                voter_phone:    phone,
                payment_method: method || 'card',
                voter_country:  window._voterCountry || localStorage.getItem('voter_country') || 'KE',
            };
        } else {
            _pendingPayload = {
                bundle_id:      selectedBundleId,
                nominee_id:     parseInt(selectedNomineeId),
                voter_name:     name,
                voter_email:    email,
                voter_phone:    phone,
                payment_method: method || 'card',
                voter_country:  window._voterCountry || localStorage.getItem('voter_country') || 'KE',
            };
        }
        _pendingMethod = method || 'card';

        // Build confirm modal body
        const fmt = n => Number(n).toLocaleString('en-KE', {minimumFractionDigits:0, maximumFractionDigits:0});
        const methodLabel = method === 'mpesa'
            ? '<span style="color:#2e7d32;font-weight:700;"><i class="fas fa-mobile-alt" style="margin-right:5px;"></i>M-Pesa</span>'
            : '<span style="color:#1e1548;font-weight:700;"><i class="fas fa-credit-card" style="margin-right:5px;"></i>Card</span>';

        document.getElementById('payConfirmBody').innerHTML = `
            <div style="display:flex;flex-direction:column;gap:10px;font-size:.88rem;">
                <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid #f0f0f5;">
                    <span style="color:#888;">Voting for</span>
                    <span style="font-weight:700;color:#1e1548;text-align:right;max-width:60%;">${escHtml(selectedNomineeName)}</span>
                </div>
                <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid #f0f0f5;">
                    <span style="color:#888;">Votes</span>
                    <span style="font-weight:700;color:#1e1548;">${fmt(selectedBundleVotes)} votes</span>
                </div>
                <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid #f0f0f5;">
                    <span style="color:#888;">Name</span>
                    <span style="font-weight:600;color:#1e1548;">${escHtml(name)}</span>
                </div>
                <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid #f0f0f5;">
                    <span style="color:#888;">Phone</span>
                    <span style="font-weight:600;color:#1e1548;">${escHtml(phone)}</span>
                </div>
                <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid #f0f0f5;">
                    <span style="color:#888;">Pay via</span>
                    <span>${methodLabel}</span>
                </div>
                <div style="display:flex;justify-content:space-between;padding:12px 0;font-size:1rem;">
                    <span style="font-weight:800;color:#1e1548;">Total</span>
                    <span style="font-weight:900;color:#ed1c24;font-size:1.1rem;">${fmt(selectedBundlePrice)} KES</span>
                </div>
            </div>`;

        // Style confirm button to match payment method
        const $confirmBtn = document.getElementById('payConfirmBtn');
        $confirmBtn.style.background = method === 'mpesa'
            ? 'linear-gradient(135deg,#4caf50,#2e7d32)'
            : 'linear-gradient(135deg,#ed1c24,#c41820)';

        // Show modal
        const modal = document.getElementById('payConfirmModal');
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closeConfirm() {
        document.getElementById('payConfirmModal').style.display = 'none';
        document.body.style.overflow = '';
        _pendingPayload = null;
        _pendingMethod  = null;
    }

    function confirmPay() {
        if (!_pendingPayload) return;

        // Close modal + lock buttons
        document.getElementById('payConfirmModal').style.display = 'none';
        document.body.style.overflow = '';

        const $mpesa = document.getElementById('payBtnMpesa');
        const $card  = document.getElementById('payBtnCard');
        $mpesa.disabled = true;
        $card.disabled  = true;
        const $btn = _pendingMethod === 'mpesa' ? $mpesa : $card;
        $btn.innerHTML = '<i class="fas fa-spinner fa-spin" style="margin-right:8px;"></i>Preparing payment…';

        fetch(API_BASE + '/api/public/events/' + encodeURIComponent(EVENT_SLUG) + '/vote-bundle/checkout', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify(_pendingPayload)
        })
        .then(r => r.json())
        .then(data => {
            if (data.payment_url) {
                window.location.href = data.payment_url;
            } else {
                const msg = data.error || data.message || (data.errors ? Object.values(data.errors).flat().join('<br>') : 'Checkout failed.');
                showAlert('<i class="fas fa-exclamation-circle me-2"></i>' + msg, 'error');
                renderSummary();
            }
        })
        .catch(() => {
            showAlert('<i class="fas fa-exclamation-circle me-2"></i>Network error. Please check your connection and try again.', 'error');
            renderSummary();
        });

        _pendingPayload = null;
        _pendingMethod  = null;
    }

    function escHtml(s) { const d = document.createElement('div'); d.textContent = s; return d.innerHTML; }

    // Init
    renderSummary();
    updateStepUI();

    // Auto-activate the nominee's category tab when arriving from "Vote Now"
    if (PRE_NOMINEE_CAT) {
        var catBtn = document.querySelector('.cat-tab-btn[data-cat="' + PRE_NOMINEE_CAT + '"]');
        filterCat(PRE_NOMINEE_CAT, catBtn);
    }

    // Scroll to pre-selected nominee
    if (PRE_NOMINEE_ID) {
        setTimeout(function() {
            var el = document.getElementById('npcard-' + PRE_NOMINEE_ID);
            if (el) el.scrollIntoView({behavior:'smooth', block:'nearest'});
        }, 400);
    }

    // If pre-selected nominee but no bundle yet, scroll to step 2 after a moment
    if (PRE_NOMINEE_ID && !selectedBundleId) {
        setTimeout(function() { scrollNext('step2Hdr'); }, 800);
    }

    return { selectNominee, selectBundle, filterCat, showAllNominees, pay, closeConfirm, confirmPay, calcCustomVotes, applyCustomAmount };
})();

// ── intl-tel-input init ───────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function() {
    var phoneEl = document.getElementById('voter_phone');
    if (phoneEl && window.intlTelInput) {
        window._voterITI = window.intlTelInput(phoneEl, {
            initialCountry:    (window._voterCountry || 'KE').toLowerCase(),
            preferredCountries: ['ke','ug','tz','rw','et','gh','ng','za','gb','us','ae'],
            separateDialCode:  true,
            utilsScript: 'https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/js/utils.js',
        });

        // When user manually changes the flag → update M-Pesa button visibility
        phoneEl.addEventListener('countrychange', function() {
            var data = window._voterITI.getSelectedCountryData();
            applyCountryUI((data.iso2 || 'ke').toUpperCase());
        });

        // Apply whatever country was already detected
        applyCountryUI(window._voterCountry || 'KE');
    }
});
</script>
</body>
</html>
