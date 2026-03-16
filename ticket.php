<?php
include 'config/config.php';
include 'libs/App.php';

$barcode = trim($_GET['code'] ?? '');
if (!$barcode) { header('Location: ' . SITE_URL . '/events'); exit; }

$resp   = tuqio_api('/api/public/ticket/' . urlencode($barcode));
$ticket = $resp['ticket'] ?? null;
$event  = $resp['event']  ?? null;

if (!$ticket) {
    $errorMsg = 'This ticket could not be found. It may be invalid or the code is incorrect.';
}

$isCheckedIn = !empty($ticket['is_checked_in']);
$status      = $ticket['status'] ?? 'active';
$orderNumber = $ticket['order_number'] ?? null;

$evtName  = $event['name']  ?? 'Event';
$evtDate  = !empty($event['start_date']) ? date('l, d F Y', strtotime($event['start_date'])) : '';
$evtTime  = !empty($event['start_time']) ? date('g:i A', strtotime($event['start_time'])) : '';
$evtVenue = implode(', ', array_filter([$event['venue_name'] ?? '', $event['venue_city'] ?? '']));

$evtThumb = !empty($event['thumbnail_image']) ? API_STORAGE . $event['thumbnail_image']
           : (!empty($event['banner_image'])  ? API_STORAGE . $event['banner_image'] : '');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title><?= $ticket ? htmlspecialchars($ticket['ticket_type_name'] . ' · ' . $evtName) : 'Ticket' ?> | Tuqio Hub</title>
<link href="<?= SITE_URL ?>/assets/css/bootstrap.min.css" rel="stylesheet">
<link href="<?= SITE_URL ?>/assets/css/style.css" rel="stylesheet">
<link href="<?= SITE_URL ?>/assets/css/responsive.css" rel="stylesheet">
<link href="<?= SITE_URL ?>/assets/css/custom.css?v=2" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="icon" type="image/png" href="<?= SITE_URL ?>/assets/images/favicon/favicon-96x96.png" sizes="96x96">
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

<div class="tkt-page-wrap">

<?php if ($ticket): ?>

<!-- Hero -->
<div class="tkt-hero">
    <div class="auto-container">
        <div class="tkt-hero-inner">
            <?php if ($evtThumb): ?>
            <img src="<?= htmlspecialchars($evtThumb) ?>" alt="<?= htmlspecialchars($evtName) ?>" class="tkt-hero-logo">
            <?php else: ?>
            <div class="tkt-hero-logo-placeholder"><i class="fas fa-calendar-star"></i></div>
            <?php endif; ?>

            <div class="tkt-event-name"><?= htmlspecialchars($evtName) ?></div>

            <?php if ($evtDate || $evtVenue): ?>
            <div class="tkt-event-meta">
                <?php if ($evtDate): ?>
                <span><i class="fas fa-calendar-alt"></i><?= $evtDate . ($evtTime ? ' · ' . $evtTime : '') ?></span>
                <?php endif; ?>
                <?php if ($evtVenue): ?>
                <span><i class="fas fa-map-marker-alt"></i><?= htmlspecialchars($evtVenue) ?></span>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Ticket card -->
<div class="tkt-wrap">
    <div class="tkt-card">

        <!-- Header: ticket type -->
        <div class="tkt-card-top">
            <div>
                <div class="tkt-type-label">Ticket Type</div>
                <div class="tkt-type-name"><?= htmlspecialchars($ticket['ticket_type_name'] ?? 'General Admission') ?></div>
            </div>
            <?php if ($isCheckedIn): ?>
            <span class="tkt-status-badge tkt-status-used" style="margin-top:2px;"><i class="fas fa-check-circle"></i> Checked In</span>
            <?php elseif ($status === 'cancelled'): ?>
            <span class="tkt-status-badge tkt-status-cancelled" style="margin-top:2px;"><i class="fas fa-times-circle"></i> Cancelled</span>
            <?php else: ?>
            <span class="tkt-status-badge tkt-status-active" style="margin-top:2px;"><i class="fas fa-circle" style="font-size:.4rem;"></i> Valid</span>
            <?php endif; ?>
        </div>

        <!-- QR code -->
        <div class="tkt-qr-wrap">
            <div class="tkt-qr-box" id="qr-code"></div>
            <div class="tkt-qr-caption">Scan to Enter</div>
        </div>

        <!-- Perforation divider -->
        <div class="tkt-perf">
            <div class="tkt-perf-notch"></div>
            <div class="tkt-perf-line"></div>
            <div class="tkt-perf-notch"></div>
        </div>

        <!-- Details -->
        <div class="tkt-details">
            <?php if (!empty($ticket['holder_name'])): ?>
            <div class="tkt-detail-row">
                <span class="tkt-detail-label">Holder</span>
                <span class="tkt-detail-val"><?= htmlspecialchars($ticket['holder_name']) ?></span>
            </div>
            <?php endif; ?>
            <div class="tkt-detail-row">
                <span class="tkt-detail-label">Ticket #</span>
                <span class="tkt-detail-val mono"><?= htmlspecialchars($ticket['ticket_number']) ?></span>
            </div>
            <?php if (!empty($ticket['seat_number'])): ?>
            <div class="tkt-detail-row">
                <span class="tkt-detail-label">Seat</span>
                <span class="tkt-detail-val"><?= htmlspecialchars($ticket['seat_number']) ?></span>
            </div>
            <?php endif; ?>
            <?php if (!empty($ticket['table_number'])): ?>
            <div class="tkt-detail-row">
                <span class="tkt-detail-label">Table</span>
                <span class="tkt-detail-val"><?= htmlspecialchars($ticket['table_number']) ?></span>
            </div>
            <?php endif; ?>
            <?php if (!empty($ticket['section'])): ?>
            <div class="tkt-detail-row">
                <span class="tkt-detail-label">Zone</span>
                <span class="tkt-detail-val"><?= htmlspecialchars($ticket['section']) ?></span>
            </div>
            <?php endif; ?>
            <?php if (!empty($ticket['valid_from'])): ?>
            <div class="tkt-detail-row">
                <span class="tkt-detail-label">Valid</span>
                <span class="tkt-detail-val"><?= date('d M Y', strtotime($ticket['valid_from'])) ?></span>
            </div>
            <?php endif; ?>
            <?php if (!empty($orderNumber)): ?>
            <div class="tkt-detail-row">
                <span class="tkt-detail-label">Order</span>
                <span class="tkt-detail-val mono"><?= htmlspecialchars($orderNumber) ?></span>
            </div>
            <?php endif; ?>
        </div>

        <!-- Footer: download button -->
        <?php if ($orderNumber && !empty($ticket['ticket_number'])): ?>
        <div class="tkt-footer">
            <span style="font-size:.78rem;color:#aaa;"><i class="fas fa-shield-alt" style="color:#10b981;margin-right:4px;"></i>Secured by Tuqio</span>
            <a href="<?= API_BASE ?>/api/public/tickets/<?= urlencode($orderNumber) ?>/download/<?= urlencode($ticket['ticket_number']) ?>"
               class="tkt-dl-btn" target="_blank">
                <i class="fas fa-file-pdf"></i> Download PDF
            </a>
        </div>
        <?php endif; ?>

    </div><!-- /tkt-card -->

    <!-- Back link -->
    <?php if ($event && !empty($event['slug'])): ?>
    <a href="<?= SITE_URL ?>/event-detail?slug=<?= urlencode($event['slug']) ?>" class="tkt-back-link">
        <i class="fas fa-arrow-left"></i> Back to event
    </a>
    <?php endif; ?>
</div><!-- /tkt-wrap -->

<?php else: ?>

<!-- Error state -->
<div class="tkt-hero" style="padding:48px 0 80px;">
    <div class="auto-container">
        <div class="tkt-hero-inner">
            <div class="tkt-event-name" style="font-size:1.4rem;">Ticket Not Found</div>
        </div>
    </div>
</div>
<div class="tkt-wrap">
    <div class="tkt-error-card">
        <i class="fas fa-ticket-alt err-icon"></i>
        <h5>Invalid Ticket Code</h5>
        <p><?= htmlspecialchars($errorMsg ?? 'Ticket not found.') ?></p>
        <a href="<?= SITE_URL ?>/events" class="tkt-error-btn">
            <i class="fas fa-calendar-alt"></i> Browse Events
        </a>
    </div>
</div>

<?php endif; ?>

</div><!-- /tkt-page-wrap -->

<?php include 'includes/footer.php'; ?>
<?php include 'includes/footer-links.php'; ?>

</div><!-- /page-wrapper -->

<script src="<?= SITE_URL ?>/assets/js/jquery.js"></script>
<script src="<?= SITE_URL ?>/assets/js/bootstrap.min.js"></script>
<script src="<?= SITE_URL ?>/assets/js/main.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

<?php if ($ticket && !empty($ticket['barcode'])): ?>
<script>
(function() {
    new QRCode(document.getElementById('qr-code'), {
        text:         <?= json_encode($ticket['barcode']) ?>,
        width:        186,
        height:       186,
        colorDark:    '#000000',
        colorLight:   '#ffffff',
        correctLevel: QRCode.CorrectLevel.H,
    });
})();
</script>
<?php endif; ?>

</body>
</html>
