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
<link rel="icon" type="image/png" href="<?= SITE_URL ?>/assets/images/favicon/favicon-96x96.png" sizes="96x96">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
<style>
/* ── Checkout layout ──────────────────────────────────────────── */
.checkout-wrap { max-width:1080px;margin:0 auto;padding:0 16px 60px; }

/* Ticket type card */
.tt-card {
    background:#fff;border:2px solid #eee;border-radius:12px;
    padding:20px 24px;margin-bottom:14px;transition:border-color .2s,box-shadow .2s;
    display:flex;align-items:center;gap:16px;flex-wrap:wrap;
}
.tt-card.selected { border-color:#ed1c24;box-shadow:0 4px 18px rgba(237,28,36,0.1); }
.tt-card.sold-out { opacity:.55; }
.tt-info { flex:1;min-width:0; }
.tt-name { font-size:1rem;font-weight:700;color:#1e1548;margin-bottom:2px; }
.tt-desc { font-size:.8rem;color:#888;margin-bottom:4px; }
.tt-price { font-size:1.15rem;font-weight:800;color:#ed1c24; }
.tt-orig  { font-size:.8rem;color:#aaa;text-decoration:line-through;margin-left:8px; }
.tt-remaining { font-size:.73rem;color:#f59e0b;font-weight:600;margin-top:4px; }
.tt-qty { display:flex;align-items:center;gap:10px;flex-shrink:0; }
.qty-btn {
    width:32px;height:32px;border-radius:8px;border:2px solid #eee;background:#f9f9fb;
    font-size:1.1rem;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;
    color:#1e1548;transition:all .15s;line-height:1;
}
.qty-btn:hover:not(:disabled) { border-color:#ed1c24;color:#ed1c24;background:#fff; }
.qty-btn:disabled { opacity:.4;cursor:default; }
.qty-val { font-size:1rem;font-weight:700;color:#1e1548;min-width:24px;text-align:center; }
.qty-sold-out { font-size:.8rem;font-weight:700;color:#aaa;padding:4px 12px;background:#f5f5f5;border-radius:20px; }

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
                    $isSoldOut   = !empty($tt['is_sold_out']) || ($tt['remaining'] !== null && $tt['remaining'] <= 0);
                    $isAvailable = !empty($tt['is_available']) && !$isSoldOut;
                    $remaining   = $tt['remaining'] ?? null;
                    $lowStock    = $remaining !== null && $remaining > 0 && $remaining <= 10;
                ?>
                <div class="tt-card <?= $isSoldOut ? 'sold-out' : '' ?>"
                     id="ttcard-<?= $tt['id'] ?>"
                     data-id="<?= $tt['id'] ?>"
                     data-name="<?= htmlspecialchars($tt['name']) ?>"
                     data-price="<?= $tt['price'] ?>"
                     data-currency="<?= htmlspecialchars($tt['currency'] ?? 'KES') ?>"
                     data-available="<?= $isAvailable ? '1' : '0' ?>">
                    <div class="tt-info">
                        <div class="tt-name"><?= htmlspecialchars($tt['name']) ?></div>
                        <?php if (!empty($tt['description'])): ?>
                        <div class="tt-desc"><?= htmlspecialchars($tt['description']) ?></div>
                        <?php endif; ?>
                        <?php if (!empty($tt['benefits']) && is_array($tt['benefits'])): ?>
                        <div style="margin:5px 0;">
                        <?php foreach (array_slice($tt['benefits'], 0, 3) as $b): ?>
                            <span style="font-size:.72rem;color:#1e1548;margin-right:10px;">
                                <i class="fas fa-check" style="color:#10b981;margin-right:3px;"></i><?= htmlspecialchars($b) ?>
                            </span>
                        <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                        <div>
                            <span class="tt-price"><?= number_format($tt['price'], 0) ?> <?= htmlspecialchars($tt['currency'] ?? 'KES') ?></span>
                            <?php if (!empty($tt['original_price']) && $tt['original_price'] > $tt['price']): ?>
                            <span class="tt-orig"><?= number_format($tt['original_price'], 0) ?></span>
                            <?php endif; ?>
                        </div>
                        <?php if ($isSoldOut): ?>
                        <div class="tt-remaining">Sold out</div>
                        <?php elseif ($lowStock): ?>
                        <div class="tt-remaining"><i class="fas fa-fire" style="color:#f59e0b;"></i> Only <?= $remaining ?> left!</div>
                        <?php endif; ?>
                    </div>
                    <div class="tt-qty">
                        <?php if ($isSoldOut): ?>
                        <span class="qty-sold-out">Sold Out</span>
                        <?php else: ?>
                        <button class="qty-btn" onclick="Checkout.decQty(<?= $tt['id'] ?>)" id="btn-dec-<?= $tt['id'] ?>" disabled>−</button>
                        <span class="qty-val" id="qty-<?= $tt['id'] ?>">0</span>
                        <button class="qty-btn" onclick="Checkout.incQty(<?= $tt['id'] ?>)" id="btn-inc-<?= $tt['id'] ?>">+</button>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>

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

                    <button class="pay-btn" id="payBtn" onclick="Checkout.pay()" disabled>
                        <i class="fas fa-lock" style="margin-right:8px;"></i>
                        Pay with Paystack
                    </button>
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

<script>
const API_BASE  = '<?= API_BASE ?>';
const SITE_URL  = '<?= SITE_URL ?>';
const EVENT_SLUG = <?= json_encode($slug) ?>;
const TICKET_TYPES = <?= json_encode($ticketTypes) ?>;

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
        const $btn   = document.getElementById('payBtn');
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
            $btn.disabled = true;
            return;
        }

        // Recalc promo discount
        if (promoApplied && promoPercent > 0) {
            promoDiscount = Math.round(sub * promoPercent / 100);
        }

        const total = Math.max(0, sub - promoDiscount);
        const currency = TICKET_TYPES[0]?.currency || 'KES';

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
        $btn.disabled = false;
        $btn.innerHTML = `<i class="fas fa-lock" style="margin-right:8px;"></i>Pay ${fmt(total)} ${currency}`;
    }

    function updateCard(id) {
        const qty = quantities[id] || 0;
        const card = document.getElementById('ttcard-' + id);
        if (card) {
            card.classList.toggle('selected', qty > 0);
        }
        const decBtn = document.getElementById('btn-dec-' + id);
        const incBtn = document.getElementById('btn-inc-' + id);
        const qtyEl  = document.getElementById('qty-' + id);
        if (qtyEl)  qtyEl.textContent = qty;
        if (decBtn) decBtn.disabled = (qty <= 0);
    }

    function incQty(id) {
        const t = getType(id);
        if (!t || !t.is_available) return;
        const max = t.remaining !== null ? Math.min(t.remaining, 10) : 10;
        const cur = quantities[id] || 0;
        if (cur >= max) return;
        quantities[id] = cur + 1;
        updateCard(id);
        renderSummary();
    }

    function decQty(id) {
        const cur = quantities[id] || 0;
        if (cur <= 0) return;
        quantities[id] = cur - 1;
        updateCard(id);
        renderSummary();
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

    function showAlert(msg, type) {
        const el = document.getElementById('checkoutAlert');
        el.innerHTML = `<div class="checkout-alert ${type}">${msg}</div>`;
        window.scrollTo({top: 0, behavior: 'smooth'});
    }

    function pay() {
        const name  = document.getElementById('buyer_name').value.trim();
        const email = document.getElementById('buyer_email').value.trim();
        const phone = document.getElementById('buyer_phone').value.trim();

        if (!name)  { showAlert('<i class="fas fa-exclamation-circle me-2"></i>Please enter your full name.', 'error'); return; }
        if (!email) { showAlert('<i class="fas fa-exclamation-circle me-2"></i>Please enter your email address.', 'error'); return; }
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            showAlert('<i class="fas fa-exclamation-circle me-2"></i>Please enter a valid email address.', 'error');
            return;
        }
        if (totalQty() === 0) {
            showAlert('<i class="fas fa-exclamation-circle me-2"></i>Please select at least one ticket.', 'error');
            return;
        }

        const items = Object.entries(quantities)
            .filter(([, q]) => q > 0)
            .map(([id, quantity]) => ({ ticket_type_id: parseInt(id), quantity }));

        const payload = { items, buyer_name: name, buyer_email: email };
        if (phone) payload.buyer_phone = phone;
        if (promoCode) payload.promo_code = promoCode;

        const $btn = document.getElementById('payBtn');
        $btn.disabled = true;
        $btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Preparing payment…';

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
                $btn.disabled = false;
                $btn.innerHTML = '<i class="fas fa-lock" style="margin-right:8px;"></i>Pay with Paystack';
                renderSummary();
            }
        })
        .catch(() => {
            showAlert('<i class="fas fa-exclamation-circle me-2"></i>Network error. Please check your connection and try again.', 'error');
            $btn.disabled = false;
            $btn.innerHTML = '<i class="fas fa-lock" style="margin-right:8px;"></i>Pay with Paystack';
            renderSummary();
        });
    }

    function fmt(n) { return Number(n).toLocaleString('en-KE', {minimumFractionDigits:0, maximumFractionDigits:0}); }
    function escHtml(s) { const d = document.createElement('div'); d.textContent = s; return d.innerHTML; }

    return { incQty, decQty, applyPromo, pay };
})();
</script>
</body>
</html>
