<?php
include 'config/config.php';
include 'libs/App.php';

$orderNumber = trim($_GET['ref'] ?? '');
if (!$orderNumber) { header('Location: ' . SITE_URL . '/events'); exit; }

// Fetch purchase details from API
$resp = tuqio_api('/api/public/tickets/' . urlencode($orderNumber));

// If API returns error, show generic success screen (payment processed)
$purchase = $resp['purchase'] ?? null;
$tickets  = $resp['tickets']  ?? [];
$event    = $resp['event']    ?? null;

// Graceful fallback if backend hasn't returned details yet
$hasFull  = !empty($purchase) && !empty($event);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Booking Confirmed | Tuqio Hub</title>
<link href="<?= SITE_URL ?>/assets/css/bootstrap.min.css" rel="stylesheet">
<link href="<?= SITE_URL ?>/assets/css/style.css" rel="stylesheet">
<link href="<?= SITE_URL ?>/assets/css/responsive.css" rel="stylesheet">
<link href="<?= SITE_URL ?>/assets/css/custom.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="icon" type="image/png" href="<?= SITE_URL ?>/assets/images/favicon/favicon-96x96.png" sizes="96x96">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
<style>
/* ── Confirmation styles ─────────────────────────────── */
.confirm-hero {
    background:linear-gradient(135deg,#1e1548,#2d1f6b);
    padding:60px 0 50px;
    text-align:center;
    color:#fff;
}
.confirm-icon {
    width:80px;height:80px;border-radius:50%;
    background:linear-gradient(135deg,#10b981,#059669);
    display:flex;align-items:center;justify-content:center;
    font-size:2.2rem;color:#fff;margin:0 auto 20px;
    box-shadow:0 8px 30px rgba(16,185,129,.4);
}
.confirm-title { font-size:1.8rem;font-weight:900;margin-bottom:8px; }
.confirm-sub   { font-size:.95rem;opacity:.8; }
.order-ref {
    display:inline-block;margin-top:14px;
    background:rgba(255,255,255,.12);border-radius:8px;
    padding:8px 20px;font-size:.9rem;font-weight:700;letter-spacing:1px;
}

.ticket-card {
    background:#fff;border-radius:14px;box-shadow:0 4px 24px rgba(0,0,0,0.08);
    margin-bottom:20px;overflow:hidden;
}
.ticket-card-header {
    background:linear-gradient(135deg,#1e1548,#2d1f6b);
    padding:16px 24px;display:flex;align-items:center;gap:14px;
}
.ticket-number-badge {
    background:rgba(237,28,36,.9);border-radius:8px;
    padding:4px 12px;font-size:.75rem;font-weight:800;letter-spacing:.5px;color:#fff;
}
.ticket-card-body { padding:20px 24px; }
.tc-row { display:flex;gap:10px;padding:8px 0;border-bottom:1px solid #f5f5f5;font-size:.87rem; }
.tc-row:last-child { border-bottom:none; }
.tc-label { color:#aaa;font-weight:600;min-width:90px;font-size:.78rem;text-transform:uppercase;letter-spacing:.5px;padding-top:1px; }
.tc-val   { color:#1e1548;font-weight:700;flex:1; }

.action-card {
    background:#fff;border-radius:14px;box-shadow:0 4px 24px rgba(0,0,0,0.07);
    padding:28px 30px;margin-bottom:20px;
}
.action-btn {
    display:inline-flex;align-items:center;gap:10px;padding:12px 24px;border-radius:10px;
    font-size:.9rem;font-weight:700;text-decoration:none;border:none;cursor:pointer;
    transition:opacity .2s,transform .1s;
}
.action-btn:hover { opacity:.9;transform:translateY(-1px); }
.btn-download { background:linear-gradient(135deg,#ed1c24,#c41820);color:#fff; }
.btn-outline   { background:#fff;color:#1e1548;border:2px solid #1e1548; }
.btn-share     { background:#1e1548;color:#fff; }

.email-note {
    background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;
    padding:14px 18px;font-size:.84rem;color:#065f46;margin-bottom:20px;
    display:flex;align-items:center;gap:10px;
}

/* Summary sidebar */
.summary-sidebar { background:linear-gradient(160deg,#1e1548,#2d1f6b);border-radius:14px;padding:28px;color:#fff;position:sticky;top:100px; }
.ss-line { display:flex;justify-content:space-between;font-size:.85rem;padding:7px 0;border-bottom:1px solid rgba(255,255,255,.1); }
.ss-line.total { font-weight:800;font-size:1.05rem;border-top:2px solid rgba(255,255,255,.2);padding-top:12px;margin-top:4px;border-bottom:none; }
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

<!-- Confirmation hero -->
<div class="confirm-hero">
    <div class="confirm-icon"><i class="fas fa-check"></i></div>
    <div class="confirm-title">Booking Confirmed!</div>
    <?php if ($hasFull): ?>
    <div class="confirm-sub">You're all set for <strong><?= htmlspecialchars($event['name']) ?></strong></div>
    <?php else: ?>
    <div class="confirm-sub">Your payment was received. Tickets have been sent to your email.</div>
    <?php endif; ?>
    <div class="order-ref">
        <i class="fas fa-hashtag" style="opacity:.7;margin-right:4px;"></i>
        Order: <?= htmlspecialchars(strtoupper($orderNumber)) ?>
    </div>
</div>

<!-- Content -->
<section class="shop-section" style="padding:50px 0 60px;">
    <div class="auto-container">
        <div style="max-width:1040px;margin:0 auto;padding:0 16px;">

            <!-- Email notice -->
            <div class="email-note">
                <i class="fas fa-envelope-open-text" style="font-size:1.2rem;flex-shrink:0;"></i>
                <div>
                    <?php if ($hasFull && !empty($purchase['buyer_email'])): ?>
                    Your PDF ticket<?= count($tickets) > 1 ? 's have' : ' has' ?> been sent to <strong><?= htmlspecialchars($purchase['buyer_email']) ?></strong>. Check your inbox (and spam folder).
                    <?php else: ?>
                    Your PDF ticket(s) have been sent to your email address. Check your inbox (and spam folder).
                    <?php endif; ?>
                </div>
            </div>

            <div class="row">
                <!-- LEFT: ticket list -->
                <div class="col-lg-7 col-md-12">
                    <h4 style="font-size:1.1rem;font-weight:800;color:#1e1548;margin-bottom:20px;">
                        <i class="fas fa-ticket-alt" style="color:#ed1c24;margin-right:8px;"></i>
                        Your Ticket<?= count($tickets) !== 1 ? 's' : '' ?>
                        <?php if ($hasFull): ?>
                        (<?= count($tickets) ?>)
                        <?php endif; ?>
                    </h4>

                    <?php if (!empty($tickets)): ?>
                    <?php foreach ($tickets as $i => $tk): ?>
                    <div class="ticket-card">
                        <div class="ticket-card-header">
                            <div style="flex:1;">
                                <div style="font-size:.95rem;font-weight:800;color:#fff;margin-bottom:4px;">
                                    <?= htmlspecialchars($tk['ticket_type_name'] ?? $tk['type'] ?? 'Ticket') ?>
                                </div>
                                <?php if (!empty($event['name'])): ?>
                                <div style="font-size:.78rem;opacity:.7;color:#fff;"><?= htmlspecialchars($event['name']) ?></div>
                                <?php endif; ?>
                            </div>
                            <span class="ticket-number-badge">#<?= htmlspecialchars($tk['ticket_number']) ?></span>
                        </div>
                        <div class="ticket-card-body">
                            <?php if (!empty($tk['holder_name'])): ?>
                            <div class="tc-row">
                                <span class="tc-label">Holder</span>
                                <span class="tc-val"><?= htmlspecialchars($tk['holder_name']) ?></span>
                            </div>
                            <?php endif; ?>
                            <?php if (!empty($tk['seat_number'])): ?>
                            <div class="tc-row">
                                <span class="tc-label">Seat</span>
                                <span class="tc-val"><?= htmlspecialchars($tk['seat_number']) ?></span>
                            </div>
                            <?php endif; ?>
                            <?php if (!empty($tk['table_number'])): ?>
                            <div class="tc-row">
                                <span class="tc-label">Table</span>
                                <span class="tc-val"><?= htmlspecialchars($tk['table_number']) ?></span>
                            </div>
                            <?php endif; ?>
                            <?php if (!empty($tk['section'])): ?>
                            <div class="tc-row">
                                <span class="tc-label">Zone</span>
                                <span class="tc-val"><?= htmlspecialchars($tk['section']) ?></span>
                            </div>
                            <?php endif; ?>
                            <?php if (!empty($tk['barcode'])): ?>
                            <div class="tc-row">
                                <span class="tc-label">Barcode</span>
                                <span class="tc-val" style="font-family:monospace;font-size:.8rem;"><?= htmlspecialchars($tk['barcode']) ?></span>
                            </div>
                            <?php endif; ?>
                            <div style="margin-top:12px;">
                                <span style="display:inline-flex;align-items:center;gap:6px;background:#f0fdf4;color:#059669;border-radius:20px;padding:4px 14px;font-size:.75rem;font-weight:700;">
                                    <i class="fas fa-circle" style="font-size:.45rem;"></i> Valid
                                </span>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>

                    <?php else: ?>
                    <!-- Fallback when API doesn't return ticket details -->
                    <div class="ticket-card">
                        <div class="ticket-card-header">
                            <div>
                                <div style="font-size:.95rem;font-weight:800;color:#fff;margin-bottom:4px;">Your Ticket(s)</div>
                                <div style="font-size:.78rem;opacity:.7;color:#fff;">PDF tickets sent to your email</div>
                            </div>
                        </div>
                        <div class="ticket-card-body" style="text-align:center;padding:30px;">
                            <i class="fas fa-envelope-open-text" style="font-size:2.5rem;color:#ed1c24;margin-bottom:14px;display:block;"></i>
                            <p style="font-size:.9rem;color:#555;margin-bottom:0;">
                                Your tickets are on their way to your inbox.<br>
                                Order ref: <strong><?= htmlspecialchars(strtoupper($orderNumber)) ?></strong>
                            </p>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Actions -->
                    <div class="action-card">
                        <h5 style="font-size:.9rem;font-weight:800;color:#1e1548;margin-bottom:16px;text-transform:uppercase;letter-spacing:.5px;">
                            <i class="fas fa-bolt" style="color:#ed1c24;margin-right:6px;"></i>Actions
                        </h5>
                        <div style="display:flex;flex-wrap:wrap;gap:10px;">
                            <a href="<?= API_BASE ?>/api/public/tickets/<?= urlencode($orderNumber) ?>/download"
                               class="action-btn btn-download" target="_blank">
                                <i class="fas fa-file-pdf"></i> Download PDF Tickets
                            </a>
                            <?php if ($hasFull): ?>
                            <a href="<?= SITE_URL ?>/event-detail?slug=<?= urlencode($event['slug'] ?? '') ?>"
                               class="action-btn btn-outline">
                                <i class="fas fa-calendar-alt"></i> View Event
                            </a>
                            <?php endif; ?>
                        </div>

                        <!-- Add to Calendar -->
                        <?php if ($hasFull && !empty($event['start_date'])): ?>
                        <div style="margin-top:20px;padding-top:16px;border-top:1px solid #f5f5f5;">
                            <div style="font-size:.8rem;font-weight:700;color:#888;margin-bottom:10px;text-transform:uppercase;letter-spacing:.5px;">Add to Calendar</div>
                            <div style="display:flex;flex-wrap:wrap;gap:8px;">
                                <?php
                                $calDate    = date('Ymd', strtotime($event['start_date']));
                                $calTime    = !empty($event['start_time']) ? str_replace(':', '', substr($event['start_time'], 0, 5)) : '090000';
                                $calEnd     = !empty($event['end_date'])  ? date('Ymd', strtotime($event['end_date'])) : $calDate;
                                $calEndTime = !empty($event['end_time'])  ? str_replace(':', '', substr($event['end_time'], 0, 5)) : '180000';
                                $calTitle   = urlencode($event['name']);
                                $calLoc     = urlencode(implode(', ', array_filter([$event['venue_name'] ?? '', $event['venue_city'] ?? ''])));
                                $gcalStart  = $calDate . 'T' . $calTime . '00';
                                $gcalEnd    = $calEnd  . 'T' . $calEndTime . '00';
                                $gcalUrl    = "https://calendar.google.com/calendar/render?action=TEMPLATE&text={$calTitle}&dates={$gcalStart}/{$gcalEnd}&location={$calLoc}";
                                ?>
                                <a href="<?= $gcalUrl ?>" target="_blank" rel="noopener"
                                   style="font-size:.78rem;padding:6px 14px;border-radius:6px;background:#4285f4;color:#fff;text-decoration:none;font-weight:600;">
                                    <i class="fab fa-google" style="margin-right:4px;"></i>Google Calendar
                                </a>
                                <button onclick="Confirm.downloadIcs()" style="font-size:.78rem;padding:6px 14px;border-radius:6px;background:#1e1548;color:#fff;border:none;cursor:pointer;font-weight:600;">
                                    <i class="fas fa-calendar-plus" style="margin-right:4px;"></i>Download .ics
                                </button>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>

                </div><!-- /col-left -->

                <!-- RIGHT: summary sidebar -->
                <div class="col-lg-5 col-md-12 mt-4 mt-lg-0">
                    <?php if ($hasFull): ?>
                    <div class="summary-sidebar">
                        <h5 style="font-weight:800;font-size:.95rem;text-transform:uppercase;letter-spacing:.5px;margin-bottom:18px;">
                            <i class="fas fa-receipt" style="margin-right:8px;opacity:.8;"></i>Booking Summary
                        </h5>
                        <div style="font-size:.95rem;font-weight:700;margin-bottom:6px;"><?= htmlspecialchars($event['name']) ?></div>
                        <?php
                        $eventDateStr = !empty($event['start_date']) ? date('d M Y', strtotime($event['start_date'])) : '';
                        $eventTime    = !empty($event['start_time']) ? date('g:i A', strtotime($event['start_time'])) : '';
                        $eventVenue   = implode(', ', array_filter([$event['venue_name'] ?? '', $event['venue_city'] ?? '']));
                        ?>
                        <?php if ($eventDateStr): ?>
                        <div style="font-size:.78rem;opacity:.7;margin-bottom:4px;"><i class="fas fa-calendar-alt" style="margin-right:5px;"></i><?= $eventDateStr . ($eventTime ? ' · ' . $eventTime : '') ?></div>
                        <?php endif; ?>
                        <?php if ($eventVenue): ?>
                        <div style="font-size:.78rem;opacity:.7;margin-bottom:18px;"><i class="fas fa-map-marker-alt" style="margin-right:5px;"></i><?= htmlspecialchars($eventVenue) ?></div>
                        <?php endif; ?>

                        <?php if (!empty($purchase['buyer_name'])): ?>
                        <div class="ss-line"><span>Buyer</span><span><?= htmlspecialchars($purchase['buyer_name']) ?></span></div>
                        <?php endif; ?>
                        <div class="ss-line"><span>Order #</span><span style="font-family:monospace;"><?= htmlspecialchars(strtoupper($purchase['order_number'] ?? $orderNumber)) ?></span></div>
                        <div class="ss-line"><span>Tickets</span><span><?= count($tickets) ?></span></div>
                        <?php if (!empty($purchase['total_amount'])): ?>
                        <div class="ss-line total">
                            <span>Total Paid</span>
                            <span><?= number_format($purchase['total_amount'], 0) ?> <?= htmlspecialchars($purchase['currency'] ?? 'KES') ?></span>
                        </div>
                        <?php endif; ?>

                        <!-- Payment status badge -->
                        <div style="margin-top:16px;text-align:center;">
                            <span style="display:inline-flex;align-items:center;gap:6px;background:rgba(16,185,129,.2);color:#6ee7b7;border-radius:20px;padding:5px 16px;font-size:.78rem;font-weight:700;">
                                <i class="fas fa-check-circle"></i> Payment Complete
                            </span>
                        </div>
                    </div>

                    <!-- Event image -->
                    <?php
                    $evtThumb = !empty($event['thumbnail_image']) ? API_STORAGE . $event['thumbnail_image']
                               : (!empty($event['banner_image'])  ? API_STORAGE . $event['banner_image'] : '');
                    ?>
                    <?php if ($evtThumb): ?>
                    <div style="margin-top:20px;border-radius:12px;overflow:hidden;box-shadow:0 4px 18px rgba(0,0,0,0.1);">
                        <img src="<?= htmlspecialchars($evtThumb) ?>" alt="<?= htmlspecialchars($event['name']) ?>"
                             style="width:100%;height:180px;object-fit:cover;">
                    </div>
                    <?php endif; ?>

                    <?php else: ?>
                    <!-- Fallback summary when API doesn't return purchase details -->
                    <div class="summary-sidebar">
                        <h5 style="font-weight:800;font-size:.95rem;text-transform:uppercase;letter-spacing:.5px;margin-bottom:18px;">
                            <i class="fas fa-receipt" style="margin-right:8px;opacity:.8;"></i>Booking Summary
                        </h5>
                        <div class="ss-line"><span>Order Ref</span><span style="font-family:monospace;"><?= htmlspecialchars(strtoupper($orderNumber)) ?></span></div>
                        <div style="margin-top:16px;text-align:center;">
                            <span style="display:inline-flex;align-items:center;gap:6px;background:rgba(16,185,129,.2);color:#6ee7b7;border-radius:20px;padding:5px 16px;font-size:.78rem;font-weight:700;">
                                <i class="fas fa-check-circle"></i> Payment Complete
                            </span>
                        </div>
                        <p style="font-size:.82rem;opacity:.7;margin-top:20px;text-align:center;">Your tickets will be delivered to your email shortly.</p>
                    </div>
                    <?php endif; ?>

                    <!-- What to expect -->
                    <div style="background:#fff;border-radius:12px;padding:22px;box-shadow:0 2px 12px rgba(0,0,0,0.06);margin-top:20px;">
                        <div style="font-size:.85rem;font-weight:800;color:#1e1548;margin-bottom:14px;text-transform:uppercase;letter-spacing:.5px;">
                            <i class="fas fa-info-circle" style="color:#ed1c24;margin-right:6px;"></i>What's Next
                        </div>
                        <div style="font-size:.8rem;color:#555;line-height:1.7;">
                            <div style="margin-bottom:8px;"><i class="fas fa-check" style="color:#10b981;margin-right:8px;"></i>Check your email for your PDF ticket(s)</div>
                            <div style="margin-bottom:8px;"><i class="fas fa-check" style="color:#10b981;margin-right:8px;"></i>Save or print the QR code on each ticket</div>
                            <div style="margin-bottom:8px;"><i class="fas fa-check" style="color:#10b981;margin-right:8px;"></i>Present the QR code at the venue entrance</div>
                            <div><i class="fas fa-check" style="color:#10b981;margin-right:8px;"></i>Arrive early — doors open 30 min before start time</div>
                        </div>
                    </div>

                </div><!-- /col-right -->
            </div><!-- /row -->

        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
<?php include 'includes/footer-links.php'; ?>

</div><!-- /page-wrapper -->

<script src="<?= SITE_URL ?>/assets/js/jquery.js"></script>
<script src="<?= SITE_URL ?>/assets/js/bootstrap.min.js"></script>
<script src="<?= SITE_URL ?>/assets/js/main.js"></script>

<?php if ($hasFull && !empty($event['start_date'])): ?>
<script>
window.Confirm = {
    downloadIcs: function() {
        const eventName  = <?= json_encode($event['name']) ?>;
        const startDate  = <?= json_encode($event['start_date'] ?? '') ?>;
        const endDate    = <?= json_encode($event['end_date'] ?? $event['start_date'] ?? '') ?>;
        const startTime  = <?= json_encode($event['start_time'] ?? '09:00:00') ?>;
        const endTime    = <?= json_encode($event['end_time'] ?? '18:00:00') ?>;
        const venue      = <?= json_encode(implode(', ', array_filter([$event['venue_name'] ?? '', $event['venue_address'] ?? '', $event['venue_city'] ?? '']))) ?>;
        const orderNum   = <?= json_encode($orderNumber) ?>;

        function icsDate(d, t) {
            const dt = new Date(d + 'T' + t);
            return dt.toISOString().replace(/[-:]/g, '').replace(/\.\d{3}/, '');
        }

        const dtStart  = icsDate(startDate, startTime);
        const dtEnd    = icsDate(endDate, endTime);
        const uid      = 'tuqio-' + orderNum + '@tuqiohub.com';

        const ics = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//Tuqio Hub//EN',
            'CALSCALE:GREGORIAN',
            'METHOD:PUBLISH',
            'BEGIN:VEVENT',
            'UID:' + uid,
            'DTSTAMP:' + new Date().toISOString().replace(/[-:]/g, '').replace(/\.\d{3}/, ''),
            'DTSTART:' + dtStart,
            'DTEND:' + dtEnd,
            'SUMMARY:' + eventName,
            'LOCATION:' + venue,
            'DESCRIPTION:Ticket booking #' + orderNum + '. Powered by Tuqio Hub.',
            'STATUS:CONFIRMED',
            'END:VEVENT',
            'END:VCALENDAR'
        ].join('\r\n');

        const blob = new Blob([ics], { type: 'text/calendar;charset=utf-8' });
        const url  = URL.createObjectURL(blob);
        const a    = document.createElement('a');
        a.href     = url;
        a.download = eventName.replace(/[^a-z0-9]/gi, '_').toLowerCase() + '.ics';
        a.click();
        URL.revokeObjectURL(url);
    }
};
</script>
<?php else: ?>
<script>window.Confirm = { downloadIcs: function(){} };</script>
<?php endif; ?>
</body>
</html>
