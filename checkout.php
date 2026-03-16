<?php
include 'config/config.php';
include 'libs/App.php';

$slug = trim($_GET['slug'] ?? '');
if (!$slug) { header('Location: ' . SITE_URL . '/events'); exit; }

$resp = tuqio_api('/api/public/events/' . urlencode($slug));
if (empty($resp['event'])) { header('Location: ' . SITE_URL . '/events'); exit; }

$event       = $resp['event'];
$ticketTypes = $resp['ticket_types'] ?? [];

// Only events with ticketing
if (empty($event['has_ticketing'])) {
    header('Location: ' . SITE_URL . '/event-detail?slug=' . urlencode($slug));
    exit;
}

$banner    = !empty($event['banner_image'])    ? API_STORAGE . $event['banner_image']    : (SITE_URL . '/assets/slides/event.webp');
$thumbnail = !empty($event['thumbnail_image']) ? API_STORAGE . $event['thumbnail_image'] : $banner;
$dateStr   = !empty($event['start_date']) ? date('d M Y', strtotime($event['start_date'])) : 'TBD';
if (!empty($event['end_date']) && $event['end_date'] !== $event['start_date']) {
    $dateStr .= ' – ' . date('d M Y', strtotime($event['end_date']));
}
$venue = implode(', ', array_filter([$event['venue_name'] ?? '', $event['venue_city'] ?? '']));
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Get Tickets — <?= htmlspecialchars($event['name']) ?> | Tuqio Hub</title>
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
/* ── Checkout layout ──────────────────────────────────────────── */
.checkout-wrap { max-width:1080px;margin:0 auto;padding:0 16px 60px; }

/* Ticket type card */
.tt-card {
    background:#fff;border:1.5px solid #e8e8f0;border-radius:10px;
    margin-bottom:10px;transition:border-color .2s,box-shadow .2s;
    display:flex;align-items:stretch;
}
.tt-card:not(.sold-out):not(.coming-soon):not(.sale-ended):hover {
    border-color:#1e1548;box-shadow:0 2px 12px rgba(30,21,72,0.07);
}
.tt-card.selected { border-color:#ed1c24;box-shadow:0 2px 12px rgba(237,28,36,0.1); }
.tt-card.sold-out,.tt-card.coming-soon,.tt-card.sale-ended { opacity:.5; }
/* Card body */
.tt-body { flex:1;padding:16px 18px;min-width:0; }
.tt-name { font-size:.97rem;font-weight:800;color:#1e1548;margin-bottom:3px; }
.tt-desc { font-size:.8rem;color:#888;margin-bottom:7px;line-height:1.4; }
/* Badges row */
.tt-badges { display:flex;flex-wrap:wrap;gap:5px;margin-bottom:7px; }
.tt-badge { display:inline-flex;align-items:center;gap:3px;font-size:.67rem;font-weight:700;
    padding:2px 8px;border-radius:4px; }
.tt-badge.sold-out-badge  { background:#fee2e2;color:#c41820; }
.tt-badge.low-stock-badge { background:#fef3c7;color:#92400e; }
.tt-badge.coming-soon-badge { background:#ede9fe;color:#4f46e5; }
.tt-badge.sale-ended-badge  { background:#f3f4f6;color:#6b7280; }
.tt-badge.group-badge { background:#ecfdf5;color:#065f46; }
/* Benefits */
.tt-benefits { display:flex;flex-wrap:wrap;gap:4px;margin-bottom:6px; }
.tt-benefit-chip { font-size:.7rem;color:#555;background:#f5f5f5;padding:2px 8px;border-radius:4px; }
/* Order limits note */
.tt-order-limits { font-size:.72rem;color:#aaa;margin-top:4px; }
/* Right price+qty block */
.tt-right { display:flex;flex-direction:column;align-items:flex-end;
    justify-content:space-between;padding:16px 18px;flex-shrink:0;min-width:110px;
    border-left:1px solid #f0f0f0; }
.tt-price-block { text-align:right; }
.tt-price { font-size:1.1rem;font-weight:800;color:#ed1c24;display:block; }
.tt-orig  { font-size:.75rem;color:#bbb;text-decoration:line-through;display:block;margin-top:2px; }
.tt-save  { font-size:.67rem;font-weight:700;color:#059669;display:inline-block;margin-top:3px; }
/* Qty stepper */
.tt-qty { display:flex;align-items:center;gap:7px;margin-top:10px; }
.qty-btn {
    width:28px;height:28px;border-radius:5px;border:1.5px solid #ddd;background:#fff;
    font-size:1rem;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;
    color:#333;transition:all .15s;line-height:1;padding:0;
}
.qty-btn:hover:not(:disabled) { border-color:#ed1c24;color:#ed1c24; }
.qty-btn:disabled { opacity:.3;cursor:default; }
.qty-val { font-size:.97rem;font-weight:800;color:#1e1548;min-width:20px;text-align:center; }
.qty-sold-out,.qty-coming-soon,.qty-sale-ended {
    font-size:.7rem;font-weight:600;color:#aaa;text-align:center;line-height:1.4;
}

/* Buyer form */
.buyer-form { background:#fff;border-radius:14px;padding:32px;box-shadow:0 4px 24px rgba(0,0,0,0.07);margin-top:28px; }
.buyer-form label { font-weight:600;font-size:.88rem;color:#1e1548;margin-bottom:6px;display:block; }
.buyer-form input,.buyer-form select {
    width:100%;border:2px solid #eee;border-radius:8px;padding:10px 14px;font-size:.9rem;color:#333;
    transition:border-color .2s;background:#fafafa;
}
.buyer-form input:focus,.buyer-form select:focus {
    border-color:#ed1c24;outline:none;background:#fff;box-shadow:0 0 0 3px rgba(237,28,36,0.08);
}
.promo-row { display:flex;gap:8px; }
.promo-row input { flex:1; }
.promo-btn {
    padding:10px 20px;border-radius:8px;border:2px solid #1e1548;background:#1e1548;
    color:#fff;font-size:.85rem;font-weight:700;cursor:pointer;white-space:nowrap;
}
.promo-btn:hover { background:#2d1f6b; }

/* Order summary sidebar */
.order-summary { background:linear-gradient(160deg,#1e1548,#2d1f6b);border-radius:14px;padding:28px;color:#fff;position:sticky;top:100px; }
.order-summary h5 { font-weight:800;font-size:1rem;letter-spacing:.5px;margin-bottom:18px;text-transform:uppercase; }
.os-event-name { font-size:.95rem;font-weight:700;margin-bottom:4px; }
.os-meta { font-size:.78rem;opacity:.7;margin-bottom:20px; }
.os-line { display:flex;justify-content:space-between;font-size:.85rem;padding:7px 0;border-bottom:1px solid rgba(255,255,255,.1); }
.os-line:last-child { border-bottom:none; }
.os-line.total { font-weight:800;font-size:1.05rem;border-top:2px solid rgba(255,255,255,.2);padding-top:12px;margin-top:4px;border-bottom:none; }
.os-empty { font-size:.82rem;opacity:.6;text-align:center;padding:20px 0; }
.pay-btn {
    display:block;width:100%;margin-top:20px;padding:14px;border-radius:10px;
    background:linear-gradient(135deg,#ed1c24,#c41820);border:none;
    color:#fff;font-size:1rem;font-weight:800;cursor:pointer;text-align:center;
    transition:opacity .2s,transform .1s;
}
.pay-btn:hover:not(:disabled) { opacity:.9;transform:translateY(-1px); }
.pay-btn:disabled { opacity:.5;cursor:default; }
.secure-note { font-size:.73rem;opacity:.6;text-align:center;margin-top:10px; }

/* Error/info alerts */
.checkout-alert { border-radius:10px;padding:14px 18px;margin-bottom:20px;font-size:.87rem; }
.checkout-alert.error { background:#fff0f0;color:#c41820;border:1px solid #fca5a5; }
.checkout-alert.info  { background:#eff6ff;color:#1e40af;border:1px solid #93c5fd; }

/* Promo applied badge */
.promo-applied { display:inline-flex;align-items:center;gap:6px;background:rgba(16,185,129,.15);
    color:#059669;border-radius:20px;padding:4px 12px;font-size:.78rem;font-weight:700;margin-top:8px; }

/* intl-tel-input */
.buyer-form .iti { width:100%; }
.buyer-form .iti input { border-radius:0 8px 8px 0;padding-left:10px; }
.buyer-form .iti--separate-dial-code .iti__selected-flag { background:#fafafa;border-right:1px solid #eee;border-radius:8px 0 0 8px; }
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
            <h1>Get Tickets</h1>
            <ul class="page-breadcrumb">
                <li><a href="<?= SITE_URL ?>">Home</a></li>
                <li><a href="<?= SITE_URL ?>/events">Events</a></li>
                <li><a href="<?= SITE_URL ?>/event-detail?slug=<?= urlencode($slug) ?>"><?= htmlspecialchars($event['name']) ?></a></li>
                <li>Checkout</li>
            </ul>
        </div>
    </div>
</section>

<!-- Main checkout section -->
<section class="shop-section" style="padding-top:50px;">
    <div class="auto-container checkout-wrap">
        <div id="checkoutAlert"></div>

        <div class="row">
            <!-- LEFT: ticket selector + buyer form -->
            <div class="col-lg-7 col-md-12">

                <h3 style="font-size:1.3rem;font-weight:800;color:#1e1548;margin-bottom:6px;">
                    <i class="fas fa-ticket-alt" style="color:#ed1c24;margin-right:8px;"></i>
                    Select Your Tickets
                </h3>
                <p style="font-size:.85rem;color:#888;margin-bottom:22px;">
                    <?= htmlspecialchars($event['name']) ?> &mdash; <?= htmlspecialchars($dateStr) ?>
                    <?php if ($venue): ?> &middot; <?= htmlspecialchars($venue) ?><?php endif; ?>
                </p>

                <?php if (empty($ticketTypes)): ?>
                <div class="checkout-alert info">
                    <i class="fas fa-info-circle me-2"></i> No ticket types available for this event yet.
                </div>
                <?php else: ?>
                <?php foreach ($ticketTypes as $tt): ?>
                <?php
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
                    $maxOrder     = isset($tt['max_per_order']) && $tt['max_per_order'] ? (int)$tt['max_per_order'] : null;
                    $savingsPct   = (!empty($tt['original_price']) && $tt['original_price'] > $tt['price'])
                                    ? round((($tt['original_price'] - $tt['price']) / $tt['original_price']) * 100) : 0;
                    $cardClass    = $isSoldOut ? 'sold-out' : ($isComingSoon ? 'coming-soon' : ($isSaleEnded ? 'sale-ended' : ''));
                ?>
                <div class="tt-card <?= $cardClass ?>"
                     id="ttcard-<?= $tt['id'] ?>"
                     data-id="<?= $tt['id'] ?>"
                     data-name="<?= htmlspecialchars($tt['name']) ?>"
                     data-price="<?= $tt['price'] ?>"
                     data-currency="<?= htmlspecialchars($tt['currency'] ?? 'KES') ?>"
                     data-available="<?= $isAvailable ? '1' : '0' ?>">
                    <div class="tt-body">
                        <div class="tt-name"><?= htmlspecialchars($tt['name']) ?></div>
                        <?php if (!empty($tt['description'])): ?>
                        <div class="tt-desc"><?= htmlspecialchars($tt['description']) ?></div>
                        <?php endif; ?>
                        <div class="tt-badges">
                            <?php if ($isSoldOut): ?>
                            <span class="tt-badge sold-out-badge"><i class="fas fa-times-circle"></i> Sold Out</span>
                            <?php elseif ($isComingSoon): ?>
                            <span class="tt-badge coming-soon-badge"><i class="fas fa-clock"></i> Sale starts <?= date('M j', $saleStartTs) ?></span>
                            <?php elseif ($isSaleEnded): ?>
                            <span class="tt-badge sale-ended-badge"><i class="fas fa-ban"></i> Sale ended</span>
                            <?php elseif ($lowStock): ?>
                            <span class="tt-badge low-stock-badge"><i class="fas fa-fire"></i> Only <?= $remaining ?> left</span>
                            <?php endif; ?>
                            <?php if ($minOrder > 1): ?>
                            <span class="tt-badge group-badge"><i class="fas fa-users"></i> Min <?= $minOrder ?> tickets</span>
                            <?php endif; ?>
                        </div>
                        <?php if (!empty($tt['benefits']) && is_array($tt['benefits'])): ?>
                        <div class="tt-benefits">
                            <?php foreach (array_slice($tt['benefits'], 0, 4) as $b): ?>
                            <span class="tt-benefit-chip">
                                <i class="fas fa-check" style="color:#10b981;margin-right:3px;font-size:.65rem;"></i><?= htmlspecialchars($b) ?>
                            </span>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                        <?php if ($maxOrder || $minOrder > 1): ?>
                        <div class="tt-order-limits">
                            <?php if ($minOrder > 1): ?><span>Min <?= $minOrder ?></span><?php endif; ?>
                            <?php if ($maxOrder): ?>
                            <span <?= $minOrder > 1 ? 'style="margin-left:8px;"' : '' ?>>Max <?= $maxOrder ?> per order</span>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="tt-right">
                        <div class="tt-price-block">
                            <span class="tt-price"><?= number_format($tt['price'], 0) ?> <?= htmlspecialchars($tt['currency'] ?? 'KES') ?></span>
                            <?php if ($savingsPct > 0): ?>
                            <span class="tt-orig"><?= number_format($tt['original_price'], 0) ?></span>
                            <span class="tt-save">Save <?= $savingsPct ?>%</span>
                            <?php endif; ?>
                        </div>
                        <div class="tt-qty">
                            <?php if ($isSoldOut): ?>
                            <span class="qty-sold-out">Sold Out</span>
                            <?php elseif ($isComingSoon): ?>
                            <span class="qty-coming-soon">From<br><?= date('M j', $saleStartTs) ?></span>
                            <?php elseif ($isSaleEnded): ?>
                            <span class="qty-sale-ended">Sale<br>Ended</span>
                            <?php else: ?>
                            <button class="qty-btn" onclick="Checkout.decQty(<?= $tt['id'] ?>)" id="btn-dec-<?= $tt['id'] ?>" disabled>−</button>
                            <span class="qty-val" id="qty-<?= $tt['id'] ?>">0</span>
                            <button class="qty-btn" onclick="Checkout.incQty(<?= $tt['id'] ?>)" id="btn-inc-<?= $tt['id'] ?>">+</button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>

                <!-- Per-Attendee Details (rendered by JS when a group ticket is selected) -->
                <div id="attendee-forms-wrap"></div>

                <!-- Buyer Details Form -->
                <div class="buyer-form">
                    <h4 style="font-size:1rem;font-weight:800;color:#1e1548;margin-bottom:20px;">
                        <i class="fas fa-user-circle" style="color:#ed1c24;margin-right:8px;"></i>Your Details
                    </h4>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="buyer_name">Full Name <span style="color:#ed1c24">*</span></label>
                            <input type="text" id="buyer_name" placeholder="e.g. Joshua Ngumbau" autocomplete="name">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="buyer_email">Email Address <span style="color:#ed1c24">*</span></label>
                            <input type="email" id="buyer_email" placeholder="your@email.com" autocomplete="email">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="buyer_phone">Phone Number</label>
                            <input type="tel" id="buyer_phone" placeholder="+254 700 000 000" autocomplete="tel">
                        </div>
                    </div>

                    <!-- Promo code -->
                    <div class="mb-3">
                        <label for="promo_code">Promo Code <span style="font-size:.78rem;color:#aaa;font-weight:400;">(optional)</span></label>
                        <div class="promo-row">
                            <input type="text" id="promo_code" placeholder="Enter promo code" style="text-transform:uppercase;" oninput="this.value=this.value.toUpperCase()">
                            <button class="promo-btn" onclick="Checkout.applyPromo()">Apply</button>
                        </div>
                        <div id="promoStatus"></div>
                    </div>
                </div>

            </div><!-- /col-left -->

            <!-- RIGHT: order summary -->
            <div class="col-lg-5 col-md-12 mt-4 mt-lg-0">
                <div class="order-summary">
                    <h5><i class="fas fa-receipt" style="margin-right:8px;opacity:.8;"></i>Order Summary</h5>
                    <div class="os-event-name"><?= htmlspecialchars($event['name']) ?></div>
                    <div class="os-meta">
                        <?= htmlspecialchars($dateStr) ?>
                        <?php if ($venue): ?><br><?= htmlspecialchars($venue) ?><?php endif; ?>
                    </div>

                    <div id="os-items">
                        <div class="os-empty">Select tickets above to continue</div>
                    </div>

                    <div id="payMethodWrap">
                        <button class="pay-btn" id="payBtnMpesa" onclick="Checkout.pay('mpesa')" disabled
                                style="display:none;background:linear-gradient(135deg,#4caf50 0%,#2e7d32 100%);margin-bottom:10px;">
                            <i class="fas fa-mobile-alt" style="margin-right:8px;"></i>Pay with M-Pesa
                        </button>
                        <button class="pay-btn" id="payBtnCard" onclick="Checkout.pay('card')" disabled>
                            <i class="fas fa-credit-card" style="margin-right:8px;"></i>Pay with Card
                        </button>
                    </div>

                    <!-- Confirmation modal -->
                    <div id="payConfirmModal" onclick="if(event.target===this)Checkout.closeConfirm()"
                         style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;z-index:99999;background:rgba(0,0,0,.55);align-items:center;justify-content:center;padding:16px;">
                        <div style="background:#fff;border-radius:16px;max-width:420px;width:100%;box-shadow:0 20px 60px rgba(0,0,0,.25);overflow:hidden;">
                            <div style="background:linear-gradient(135deg,#1e1548,#2d1f6b);padding:20px 24px;color:#fff;">
                                <div style="font-size:1rem;font-weight:800;margin-bottom:2px;">Confirm Your Order</div>
                                <div style="font-size:.78rem;opacity:.75;">Please review before paying</div>
                            </div>
                            <div style="padding:22px 24px;">
                                <div id="payConfirmBody"></div>
                                <div style="display:flex;gap:10px;margin-top:20px;">
                                    <button type="button" onclick="Checkout.closeConfirm()"
                                            style="flex:1;padding:12px;border-radius:9px;border:2px solid #eee;background:#fff;font-weight:700;font-size:.88rem;color:#555;cursor:pointer;">
                                        <i class="fas fa-times" style="margin-right:6px;"></i>Cancel
                                    </button>
                                    <button type="button" id="payConfirmBtn" onclick="Checkout.confirmPay()"
                                            style="flex:2;padding:12px;border-radius:9px;border:none;font-weight:800;font-size:.88rem;color:#fff;cursor:pointer;">
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

                <!-- Event thumbnail -->
                <?php if (!empty($event['thumbnail_image']) || !empty($event['banner_image'])): ?>
                <div style="margin-top:20px;border-radius:12px;overflow:hidden;">
                    <img src="<?= htmlspecialchars($thumbnail) ?>" alt="<?= htmlspecialchars($event['name']) ?>"
                         style="width:100%;height:180px;object-fit:cover;">
                </div>
                <?php endif; ?>

                <!-- Trust badges -->
                <div style="margin-top:20px;background:#fff;border-radius:12px;padding:20px;box-shadow:0 2px 12px rgba(0,0,0,0.06);">
                    <div style="display:flex;gap:12px;align-items:flex-start;margin-bottom:14px;">
                        <i class="fas fa-ticket-alt" style="color:#ed1c24;font-size:1.1rem;margin-top:2px;flex-shrink:0;"></i>
                        <div>
                            <div style="font-size:.83rem;font-weight:700;color:#1e1548;">Instant Ticket Delivery</div>
                            <div style="font-size:.75rem;color:#888;">PDF tickets sent to your email immediately after payment</div>
                        </div>
                    </div>
                    <div style="display:flex;gap:12px;align-items:flex-start;margin-bottom:14px;">
                        <i class="fas fa-qrcode" style="color:#ed1c24;font-size:1.1rem;margin-top:2px;flex-shrink:0;"></i>
                        <div>
                            <div style="font-size:.83rem;font-weight:700;color:#1e1548;">QR Code Entry</div>
                            <div style="font-size:.75rem;color:#888;">Unique QR code per ticket for fast check-in at the venue</div>
                        </div>
                    </div>
                    <div style="display:flex;gap:12px;align-items:flex-start;">
                        <i class="fas fa-headset" style="color:#ed1c24;font-size:1.1rem;margin-top:2px;flex-shrink:0;"></i>
                        <div>
                            <div style="font-size:.83rem;font-weight:700;color:#1e1548;">24/7 Support</div>
                            <div style="font-size:.75rem;color:#888;">Questions? We're here to help via email or chat</div>
                        </div>
                    </div>
                </div>
            </div><!-- /col-right -->
        </div><!-- /row -->
    </div>
</section>

<?php include 'includes/footer.php'; ?>
<?php include 'includes/footer-links.php'; ?>

</div><!-- /page-wrapper -->

<script src="<?= SITE_URL ?>/assets/js/jquery.js"></script>
<script src="<?= SITE_URL ?>/assets/js/bootstrap.min.js"></script>
<script src="<?= SITE_URL ?>/assets/js/main.js"></script>

<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/js/intlTelInput.min.js"></script>
<script>
const API_BASE  = '<?= API_BASE ?>';
const MPESA_LIMIT = 150000; // Paystack M-Pesa single-transaction cap (KES)
const SITE_URL  = '<?= SITE_URL ?>';
const EVENT_SLUG = <?= json_encode($slug) ?>;
const TICKET_TYPES = <?= json_encode($ticketTypes) ?>;

// ── Country detection (3-layer) ───────────────────────────────────────────────
(function detectVoterCountry() {
    var cached = localStorage.getItem('voter_country');
    if (cached) { window._voterCountry = cached; applyCountryUI(); return; }
    try {
        var tz = Intl.DateTimeFormat().resolvedOptions().timeZone;
        if (tz === 'Africa/Nairobi') { saveCountry('KE'); return; }
    } catch(e) {}
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
    if (window._checkoutITI) {
        try { window._checkoutITI.setCountry(code.toLowerCase()); } catch(e) {}
    }
}

window.Checkout = (function () {
    let quantities = {};   // {ticket_type_id: qty}
    let promoCode  = '';
    let promoDiscount = 0; // absolute KES amount
    let promoPercent  = 0;
    let promoApplied  = false;

    function getType(id) {
        return TICKET_TYPES.find(t => t.id == id);
    }

    function subtotal() {
        let s = 0;
        for (const [id, qty] of Object.entries(quantities)) {
            if (qty > 0) {
                const t = getType(id);
                if (t) s += t.price * qty;
            }
        }
        return s;
    }

    function totalQty() {
        return Object.values(quantities).reduce((a, b) => a + b, 0);
    }

    function renderSummary() {
        const $items = document.getElementById('os-items');
        const $mpesa = document.getElementById('payBtnMpesa');
        const $card  = document.getElementById('payBtnCard');
        const lines  = [];
        let sub = 0;

        for (const [id, qty] of Object.entries(quantities)) {
            if (qty > 0) {
                const t = getType(id);
                if (t) {
                    const lineTotal = t.price * qty;
                    sub += lineTotal;
                    lines.push(`<div class="os-line">
                        <span>${escHtml(t.name)} × ${qty}</span>
                        <span>${fmt(lineTotal)} ${escHtml(t.currency || 'KES')}</span>
                    </div>`);
                }
            }
        }

        if (lines.length === 0) {
            $items.innerHTML = '<div class="os-empty">Select tickets above to continue</div>';
            if ($mpesa) { $mpesa.disabled = true; $mpesa.innerHTML = '<i class="fas fa-mobile-alt" style="margin-right:8px;"></i>Pay with M-Pesa'; }
            if ($card)  { $card.disabled  = true; $card.innerHTML  = '<i class="fas fa-credit-card" style="margin-right:8px;"></i>Pay with Card'; }
            return;
        }

        // Recalc promo discount
        if (promoApplied && promoPercent > 0) {
            promoDiscount = Math.round(sub * promoPercent / 100);
        }

        const total = Math.max(0, sub - promoDiscount);
        const currency = TICKET_TYPES[0]?.currency || 'KES';
        const label = `${fmt(total)} ${currency}`;

        let html = lines.join('');
        if (promoDiscount > 0) {
            html += `<div class="os-line" style="color:#10b981;">
                <span><i class="fas fa-tag" style="margin-right:5px;"></i>Promo discount</span>
                <span>− ${fmt(promoDiscount)} ${currency}</span>
            </div>`;
        }
        html += `<div class="os-line total">
            <span>Total</span>
            <span>${fmt(total)} ${currency}</span>
        </div>`;

        $items.innerHTML = html;
        if ($mpesa) { $mpesa.disabled = false; $mpesa.innerHTML = `<i class="fas fa-mobile-alt" style="margin-right:8px;"></i>Pay ${label} via M-Pesa`; }
        if ($card)  { $card.disabled  = false; $card.innerHTML  = `<i class="fas fa-credit-card" style="margin-right:8px;"></i>Pay ${label} via Card`; }
    }

    function updateCard(id) {
        const t   = getType(id);
        const qty = quantities[id] || 0;
        const card = document.getElementById('ttcard-' + id);
        if (card) card.classList.toggle('selected', qty > 0);
        const decBtn = document.getElementById('btn-dec-' + id);
        const incBtn = document.getElementById('btn-inc-' + id);
        const qtyEl  = document.getElementById('qty-' + id);
        if (qtyEl)  qtyEl.textContent = qty;
        if (decBtn) decBtn.disabled = (qty <= 0);
        if (incBtn && t) {
            const max = t.remaining !== null
                ? Math.min(t.remaining, t.max_per_order || 20)
                : (t.max_per_order || 20);
            incBtn.disabled = (qty >= max);
        }
    }

    function incQty(id) {
        const t = getType(id);
        if (!t || !t.is_available) return;
        const min = t.min_per_order || 1;
        const max = t.remaining !== null
            ? Math.min(t.remaining, t.max_per_order || 20)
            : (t.max_per_order || 20);
        const cur = quantities[id] || 0;
        if (cur >= max) return;
        // First click jumps to min_per_order (e.g. group ticket min=2 adds 2 at once)
        quantities[id] = cur === 0 ? Math.min(min, max) : cur + 1;
        updateCard(id);
        renderSummary();
        renderAttendees();
    }

    function decQty(id) {
        const t   = getType(id);
        const min = t?.min_per_order || 1;
        const cur = quantities[id] || 0;
        if (cur <= 0) return;
        const next = cur - 1;
        // If next lands between 1 and min-1, skip to 0 (can't hold partial group)
        quantities[id] = (next > 0 && next < min) ? 0 : next;
        updateCard(id);
        renderSummary();
        renderAttendees();
    }

    // ── Per-attendee forms ────────────────────────────────────────────────────

    const FIELD_LABELS = { name:'Full Name', email:'Email Address', phone:'Phone Number', title:'Job Title', company:'Company / Organisation', dietary:'Dietary Requirements' };

    function renderAttendees() {
        const wrap = document.getElementById('attendee-forms-wrap');
        if (!wrap) return;

        let html = '';
        for (const t of TICKET_TYPES) {
            const af = t.attendee_fields;
            if (!af || !af.collect) continue;
            const qty = quantities[t.id] || 0;
            if (qty === 0) continue;

            const fields   = af.fields   || ['name'];
            const required = af.required || ['name'];

            html += `<div class="attendee-section" style="margin-bottom:20px;background:#f9f9fc;border-radius:12px;padding:18px 20px;">`;
            html += `<h5 style="font-size:.9rem;font-weight:800;color:#1e1548;margin-bottom:14px;">
                <i class="fas fa-users" style="color:#ed1c24;margin-right:8px;"></i>
                Attendee Details — ${escHtml(t.name)}
            </h5>`;

            for (let i = 0; i < qty; i++) {
                const prefix = `attendee_${t.id}_${i}`;
                html += `<div style="background:#fff;border-radius:8px;padding:14px 16px;margin-bottom:10px;border:1px solid #eee;">`;
                html += `<div style="font-size:.78rem;font-weight:700;color:#aaa;text-transform:uppercase;letter-spacing:.5px;margin-bottom:10px;">Attendee ${i + 1}</div>`;
                html += `<div class="row g-2">`;
                for (const fk of fields) {
                    const isReq = required.includes(fk);
                    const label = FIELD_LABELS[fk] || fk;
                    const inputType = fk === 'email' ? 'email' : (fk === 'phone' ? 'tel' : 'text');
                    html += `<div class="col-md-6">
                        <label style="font-size:.78rem;font-weight:700;color:#888;margin-bottom:3px;display:block;">${escHtml(label)}${isReq ? ' <span style="color:#ed1c24">*</span>' : ''}</label>
                        <input type="${inputType}" id="${prefix}_${fk}" placeholder="${escHtml(label)}"
                               style="width:100%;padding:7px 10px;border:1px solid #ddd;border-radius:6px;font-size:.85rem;"
                               ${isReq ? 'required' : ''}>
                    </div>`;
                }
                html += `</div></div>`;
            }
            html += `</div>`;
        }

        wrap.innerHTML = html;

        // Pre-fill first attendee with buyer details
        setTimeout(function() {
            const nameEl  = document.getElementById('buyer_name');
            const emailEl = document.getElementById('buyer_email');
            for (const t of TICKET_TYPES) {
                const af = t.attendee_fields;
                if (!af || !af.collect) continue;
                if ((quantities[t.id] || 0) === 0) continue;

                const p = `attendee_${t.id}_0`;
                const nEl = document.getElementById(p + '_name');
                const eEl = document.getElementById(p + '_email');
                if (nEl && !nEl.value && nameEl)  nEl.value = nameEl.value;
                if (eEl && !eEl.value && emailEl) eEl.value = emailEl.value;
            }
        }, 0);
    }

    function collectAttendees() {
        const result = {};
        for (const t of TICKET_TYPES) {
            const af = t.attendee_fields;
            if (!af || !af.collect) continue;
            const qty = quantities[t.id] || 0;
            if (qty === 0) continue;

            const fields = af.fields || ['name'];
            result[t.id] = [];
            for (let i = 0; i < qty; i++) {
                const prefix = `attendee_${t.id}_${i}`;
                const row = {};
                for (const fk of fields) {
                    const el = document.getElementById(`${prefix}_${fk}`);
                    if (el) row[fk] = el.value.trim();
                }
                result[t.id].push(row);
            }
        }
        return result;
    }

    function validateAttendees() {
        for (const t of TICKET_TYPES) {
            const af = t.attendee_fields;
            if (!af || !af.collect) continue;
            const qty = quantities[t.id] || 0;
            if (qty === 0) continue;

            const required = af.required || ['name'];
            for (let i = 0; i < qty; i++) {
                const prefix = `attendee_${t.id}_${i}`;
                for (const fk of required) {
                    const el = document.getElementById(`${prefix}_${fk}`);
                    if (el && !el.value.trim()) {
                        el.style.borderColor = '#ed1c24';
                        el.focus();
                        return `Please fill in the ${FIELD_LABELS[fk] || fk} for Attendee ${i + 1} (${t.name}).`;
                    }
                    if (el) el.style.borderColor = '#ddd';
                }
            }
        }
        return null;
    }

    function applyPromo() {
        const code = document.getElementById('promo_code').value.trim().toUpperCase();
        const $status = document.getElementById('promoStatus');
        if (!code) { $status.innerHTML = ''; return; }
        $status.innerHTML = '<span style="font-size:.78rem;color:#888;"><i class="fas fa-spinner fa-spin me-1"></i>Checking…</span>';

        // We'll validate server-side at checkout; for now just store it
        promoCode = code;
        promoApplied = true;
        $status.innerHTML = `<div class="promo-applied"><i class="fas fa-tag"></i> Code "<strong>${escHtml(code)}</strong>" will be applied at checkout</div>`;
        renderSummary();
    }

    // Pending payment state (while confirm modal is open)
    let _pendingPayload = null;
    let _pendingMethod  = null;

    function showAlert(msg, type) {
        const el = document.getElementById('checkoutAlert');
        el.innerHTML = `<div class="checkout-alert ${type}">${msg}</div>`;
        window.scrollTo({top: 0, behavior: 'smooth'});
    }

    function pay(method) {
        const name  = document.getElementById('buyer_name').value.trim();
        const email = document.getElementById('buyer_email').value.trim();
        const phone = window._checkoutITI
            ? window._checkoutITI.getNumber()
            : document.getElementById('buyer_phone').value.trim();

        if (!name)  { showAlert('<i class="fas fa-exclamation-circle me-2"></i>Please enter your full name.', 'error'); return; }
        if (!email) { showAlert('<i class="fas fa-exclamation-circle me-2"></i>Please enter your email address.', 'error'); return; }
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            showAlert('<i class="fas fa-exclamation-circle me-2"></i>Please enter a valid email address.', 'error'); return;
        }
        if (totalQty() === 0) {
            showAlert('<i class="fas fa-exclamation-circle me-2"></i>Please select at least one ticket.', 'error'); return;
        }

        const attendeeError = validateAttendees();
        if (attendeeError) {
            showAlert('<i class="fas fa-exclamation-circle me-2"></i>' + escHtml(attendeeError), 'error');
            document.getElementById('attendee-forms-wrap')?.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return;
        }

        const currentTotal = Math.max(0, subtotal() - promoDiscount);
        const currency = TICKET_TYPES[0]?.currency || 'KES';

        // M-Pesa single-transaction limit
        if (method === 'mpesa' && currentTotal > MPESA_LIMIT) {
            showAlert(
                '<i class="fas fa-exclamation-triangle me-2"></i>'
                + `<strong>Amount exceeds M-Pesa's single transaction limit of KES ${MPESA_LIMIT.toLocaleString()}.</strong>`
                + '<br>Please use <strong>Card payment</strong> for this amount.',
                'error'
            );
            return;
        }

        const items = Object.entries(quantities)
            .filter(([, q]) => q > 0)
            .map(([id, quantity]) => ({ ticket_type_id: parseInt(id), quantity }));

        const attendees = collectAttendees();
        _pendingPayload = { items, buyer_name: name, buyer_email: email, payment_method: method,
            buyer_country: window._voterCountry || localStorage.getItem('voter_country') || 'KE' };
        if (phone) _pendingPayload.buyer_phone = phone;
        if (promoCode) _pendingPayload.promo_code = promoCode;
        if (Object.keys(attendees).length > 0) _pendingPayload.attendees = attendees;
        _pendingMethod = method;

        // Build modal body
        const methodLabel = method === 'mpesa'
            ? '<span style="color:#2e7d32;font-weight:700;"><i class="fas fa-mobile-alt" style="margin-right:5px;"></i>M-Pesa</span>'
            : '<span style="color:#1e1548;font-weight:700;"><i class="fas fa-credit-card" style="margin-right:5px;"></i>Card</span>';

        let linesHtml = '';
        for (const [id, qty] of Object.entries(quantities)) {
            if (qty > 0) {
                const t = getType(id);
                if (t) {
                    linesHtml += `<div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #f0f0f5;">
                        <span style="color:#888;">${escHtml(t.name)} × ${qty}</span>
                        <span style="font-weight:600;color:#1e1548;">${fmt(t.price * qty)} ${escHtml(t.currency || 'KES')}</span>
                    </div>`;
                }
            }
        }
        let promoHtml = '';
        if (promoDiscount > 0) {
            promoHtml = `<div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #f0f0f5;">
                <span style="color:#10b981;"><i class="fas fa-tag" style="margin-right:4px;"></i>Promo</span>
                <span style="font-weight:600;color:#10b981;">− ${fmt(promoDiscount)} ${currency}</span>
            </div>`;
        }

        document.getElementById('payConfirmBody').innerHTML = `
            <div style="font-size:.88rem;">
                ${linesHtml}${promoHtml}
                <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #f0f0f5;">
                    <span style="color:#888;">Name</span>
                    <span style="font-weight:600;color:#1e1548;">${escHtml(name)}</span>
                </div>
                ${phone ? `<div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #f0f0f5;">
                    <span style="color:#888;">Phone</span>
                    <span style="font-weight:600;color:#1e1548;">${escHtml(phone)}</span>
                </div>` : ''}
                <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #f0f0f5;">
                    <span style="color:#888;">Pay via</span>
                    <span>${methodLabel}</span>
                </div>
                <div style="display:flex;justify-content:space-between;padding:12px 0;font-size:1rem;">
                    <span style="font-weight:800;color:#1e1548;">Total</span>
                    <span style="font-weight:900;color:#ed1c24;font-size:1.1rem;">${fmt(currentTotal)} ${currency}</span>
                </div>
            </div>`;

        const $confirmBtn = document.getElementById('payConfirmBtn');
        if ($confirmBtn) $confirmBtn.style.background = method === 'mpesa'
            ? 'linear-gradient(135deg,#4caf50,#2e7d32)'
            : 'linear-gradient(135deg,#ed1c24,#c41820)';

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

        document.getElementById('payConfirmModal').style.display = 'none';
        document.body.style.overflow = '';

        const $mpesa = document.getElementById('payBtnMpesa');
        const $card  = document.getElementById('payBtnCard');
        if ($mpesa) $mpesa.disabled = true;
        if ($card)  $card.disabled  = true;
        const $btn = _pendingMethod === 'mpesa' ? $mpesa : $card;
        if ($btn) $btn.innerHTML = '<i class="fas fa-spinner fa-spin" style="margin-right:8px;"></i>Preparing payment…';

        const payload = _pendingPayload;
        _pendingPayload = null;
        _pendingMethod  = null;

        fetch(API_BASE + '/api/public/events/' + encodeURIComponent(EVENT_SLUG) + '/checkout', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify(payload)
        })
        .then(r => r.json())
        .then(data => {
            if (data.payment_url) {
                window.location.href = data.payment_url;
            } else {
                const msg = data.message || (data.errors ? Object.values(data.errors).flat().join('<br>') : 'Checkout failed. Please try again.');
                showAlert('<i class="fas fa-exclamation-circle me-2"></i>' + msg, 'error');
                renderSummary();
            }
        })
        .catch(() => {
            showAlert('<i class="fas fa-exclamation-circle me-2"></i>Network error. Please check your connection and try again.', 'error');
            renderSummary();
        });
    }

    function fmt(n) { return Number(n).toLocaleString('en-KE', {minimumFractionDigits:0, maximumFractionDigits:0}); }
    function escHtml(s) { const d = document.createElement('div'); d.textContent = s; return d.innerHTML; }

    return { incQty, decQty, applyPromo, pay, closeConfirm, confirmPay };
})();

// ── intl-tel-input on phone field ─────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function() {
    var phoneEl = document.getElementById('buyer_phone');
    if (phoneEl && window.intlTelInput) {
        window._checkoutITI = window.intlTelInput(phoneEl, {
            initialCountry:     (window._voterCountry || 'KE').toLowerCase(),
            preferredCountries: ['ke','ug','tz','rw','gh','ng','za','gb','us','ae'],
            separateDialCode:   true,
            utilsScript: 'https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/js/utils.js',
        });
        phoneEl.addEventListener('countrychange', function() {
            var data = window._checkoutITI.getSelectedCountryData();
            applyCountryUI((data.iso2 || 'ke').toUpperCase());
        });
        applyCountryUI(window._voterCountry || 'KE');
    }
});
</script>
</body>
</html>
