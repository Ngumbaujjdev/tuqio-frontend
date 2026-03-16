<?php
include 'config/config.php';
include 'libs/App.php';

// Fetch events with nominations open
$eventsResp = tuqio_api('/api/public/events');
$allEvents  = $eventsResp['data'] ?? [];
$nomEvents  = array_filter($allEvents, fn($e) => !empty($e['has_nominations']) || ($e['current_phase'] ?? '') === 'nomination');

$selectedSlug  = $_GET['event'] ?? (count($nomEvents) === 1 ? (array_values($nomEvents)[0]['slug'] ?? '') : '');
$selectedCatId = (int)($_GET['category'] ?? 0);
$selectedCat   = null;
$selectedEvent = null;
foreach ($nomEvents as $ev) {
    if ($ev['slug'] === $selectedSlug) { $selectedEvent = $ev; break; }
}

// Fetch ONLY public-nomination categories for selected event
$categories = [];
if ($selectedSlug) {
    $nomResp    = tuqio_api('/api/public/events/' . urlencode($selectedSlug) . '/nominees?for_nomination=1');
    $categories = $nomResp['categories'] ?? [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">

<!-- SEO -->
<title>Nominate | Tuqio Hub</title>
<meta name="description" content="Submit a nomination on Tuqio Hub. Recognise outstanding Kenyans in leadership, business, community, and more. Nominations are open now.">
<meta name="keywords" content="nominate Kenya, submit nomination awards, Tuqio Hub nominations, Kenya awards nomination, recognise outstanding Kenyans">
<meta name="author" content="Tuqio Hub">
<meta name="robots" content="index, follow">
<link rel="canonical" href="https://tuqiohub.africa/nominate.php">

<!-- Schema.org microdata -->
<meta itemprop="name" content="Nominate | Tuqio Hub">
<meta itemprop="description" content="Submit a nomination on Tuqio Hub. Recognise outstanding Kenyans.">
<meta itemprop="image" content="<?= OG_IMAGE ?>">

<!-- Open Graph -->
<meta property="og:title" content="Nominate | Tuqio Hub">
<meta property="og:type" content="website">
<meta property="og:image" content="<?= OG_IMAGE ?>">
<meta property="og:image:type" content="image/webp">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:url" content="https://tuqiohub.africa/nominate.php">
<meta property="og:description" content="Submit a nomination on Tuqio Hub. Recognise outstanding Kenyans in leadership, business, and community.">
<meta property="og:site_name" content="Tuqio Hub">

<!-- Twitter Card -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:site" content="@tuqiohub">
<meta name="twitter:title" content="Nominate | Tuqio Hub">
<meta name="twitter:description" content="Submit a nomination on Tuqio Hub. Recognise outstanding Kenyans in leadership, business, and community.">
<meta name="twitter:image" content="<?= OG_IMAGE ?>">

<!-- Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-XXXXXXXXXX"></script>
<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','G-XXXXXXXXXX');</script>

<!-- JSON-LD: Organization -->
<script type="application/ld+json">
{"@context":"https://schema.org/","@type":"Organization","name":"Tuqio Hub","url":"https://tuqiohub.africa","contactPoint":{"@type":"ContactPoint","telephone":"+254757140682","email":"info@tuqiohub.africa","contactType":"customer support"},"sameAs":["https://www.facebook.com/share/p/1DJyLwtvqf/","https://www.instagram.com/p/DV0RJ11ii-7/?igsh=MXNiemxwbXdzMzJ6aw==","https://twitter.com/tuqiohub","https://www.tiktok.com/@tuqiohubke"]}
</script>

<!-- JSON-LD: BreadcrumbList -->
<script type="application/ld+json">
{"@context":"https://schema.org","@type":"BreadcrumbList","itemListElement":[{"@type":"ListItem","position":1,"name":"Home","item":"https://tuqiohub.africa/"},{"@type":"ListItem","position":2,"name":"Nominate","item":"https://tuqiohub.africa/nominate.php"}]}
</script>

<!-- JSON-LD: WebPage -->
<script type="application/ld+json">
{"@context":"https://schema.org","@type":"WebPage","name":"Nominate | Tuqio Hub","url":"https://tuqiohub.africa/nominate.php","description":"Submit a nomination on Tuqio Hub. Recognise outstanding Kenyans."}
</script>
<link href="<?= SITE_URL ?>/assets/css/bootstrap.min.css" rel="stylesheet">
<link href="<?= SITE_URL ?>/assets/css/style.css" rel="stylesheet">
<link href="<?= SITE_URL ?>/assets/css/responsive.css" rel="stylesheet">
<link href="<?= SITE_URL ?>/assets/css/custom.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/css/intlTelInput.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<link rel="icon" type="image/png" href="<?= SITE_URL ?>/assets/images/favicon/favicon-96x96.png" sizes="96x96">
<link rel="icon" type="image/svg+xml" href="<?= SITE_URL ?>/assets/images/favicon/favicon.svg">
<link rel="shortcut icon" href="<?= SITE_URL ?>/assets/images/favicon/favicon.ico">
<meta name="apple-mobile-web-app-title" content="Tuqio Hub">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
<style>
.nom-form-wrap { background:#fff;border-radius:14px;padding:40px;box-shadow:0 4px 24px rgba(0,0,0,0.07); }
.nom-form-wrap label { font-weight:600;font-size:.88rem;color:#1e1548;margin-bottom:6px;display:block; }
.nom-form-wrap input,.nom-form-wrap textarea,.nom-form-wrap select {
    width:100%;border:2px solid #eee;border-radius:8px;padding:11px 14px;
    font-size:.9rem;color:#333;transition:border-color .2s;background:#fafafa;
}
.nom-form-wrap input:focus,.nom-form-wrap textarea:focus,.nom-form-wrap select:focus {
    border-color:#ed1c24;outline:none;background:#fff;box-shadow:0 0 0 3px rgba(237,28,36,0.1);
}
.section-divider { border-top:2px solid #f0f0f0;margin:28px 0;padding-top:24px; }
.section-divider h6 { font-weight:700;color:#1e1548;margin-bottom:20px;font-size:.95rem;text-transform:uppercase;letter-spacing:1px; }
.app-question-wrap { margin-bottom:16px; }
.cat-card:hover { border-color:#1e1548 !important; background:#f7f5ff !important; }
/* intl-tel-input */
.nom-form-wrap .iti { width:100%; }
.nom-form-wrap .iti input { border-radius:0 8px 8px 0; }
.nom-form-wrap .iti--separate-dial-code .iti__selected-flag { background:#fafafa; border-right:1px solid #eee; border-radius:8px 0 0 8px; }
/* Select2 country picker */
.nom-form-wrap .select2-container { width:100% !important; }
.nom-form-wrap .select2-container--default .select2-selection--single { height:44px;border:2px solid #eee;border-radius:8px;background:#fafafa; }
.nom-form-wrap .select2-container--default .select2-selection--single .select2-selection__rendered { line-height:44px;padding-left:14px;font-size:.9rem;color:#333; }
.nom-form-wrap .select2-container--default .select2-selection--single .select2-selection__placeholder { color:#aaa; }
.nom-form-wrap .select2-container--default .select2-selection--single .select2-selection__arrow { height:42px;right:10px; }
.nom-form-wrap .select2-container--default.select2-container--focus .select2-selection--single,
.nom-form-wrap .select2-container--default.select2-container--open .select2-selection--single { border-color:#ed1c24;box-shadow:0 0 0 3px rgba(237,28,36,0.1);background:#fff; }
.select2-dropdown { border:2px solid #ed1c24;border-radius:8px;font-size:.9rem;z-index:9999; }
.select2-container--default .select2-results__option--highlighted[aria-selected] { background:#1e1548; }
.select2-search--dropdown .select2-search__field { border:1px solid #eee;border-radius:6px;padding:6px 10px;font-size:.88rem; }
.select2-container--default .select2-selection--single .select2-selection__clear { margin-right:24px;color:#aaa;font-size:1.1rem; }
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
            <h1>Submit a Nomination</h1>
            <ul class="page-breadcrumb">
                <li><a href="<?= SITE_URL ?>">Home</a></li>
                <li>Nominate</li>
            </ul>
        </div>
    </div>
</section>

<section style="padding:70px 0;background:#f9fafb;">
    <div class="auto-container">
        <div class="row justify-content-center">
            <div class="col-lg-8 wow fadeInUp">

                <?php if (empty($nomEvents)): ?>
                <div class="text-center" style="padding:80px 0;">
                    <i class="fas fa-hourglass-half" style="font-size:3rem;color:#ed1c24;opacity:.3;"></i>
                    <h4 style="margin-top:20px;color:#1e1548;">No Nominations Currently Open</h4>
                    <p class="text-muted">Check back soon when nominations open for upcoming events.</p>
                    <a href="<?= SITE_URL ?>/events.php" class="theme-btn btn-style-one" style="margin-top:16px;">
                        <span class="btn-title">Browse Events</span>
                    </a>
                </div>
                <?php else: ?>

                <div class="nom-form-wrap">
                    <h3 id="form-headline" style="font-weight:800;color:#1e1548;margin-bottom:6px;">Submit a Nomination</h3>
                    <p id="form-subheadline" style="color:#888;font-size:.9rem;margin-bottom:20px;">Are you nominating yourself or putting someone else forward?</p>

                    <!-- Self / Other toggle -->
                    <div style="display:flex;gap:10px;margin-bottom:28px;">
                        <button type="button" id="btn-self" onclick="NomForm.setMode('self')"
                            style="flex:1;padding:13px;border-radius:10px;border:2px solid #eee;background:#fff;font-weight:700;font-size:.9rem;color:#888;cursor:pointer;transition:all .2s;">
                            <i class="fas fa-user me-2"></i>Nominate Myself
                        </button>
                        <button type="button" id="btn-other" onclick="NomForm.setMode('other')"
                            style="flex:1;padding:13px;border-radius:10px;border:2px solid #eee;background:#fff;font-weight:700;font-size:.9rem;color:#888;cursor:pointer;transition:all .2s;">
                            <i class="fas fa-user-plus me-2"></i>Nominate Someone Else
                        </button>
                    </div>

                    <form method="post" action="" id="nom-form" enctype="multipart/form-data">
                        <!-- Event: badge when pre-selected, dropdown when not -->
                        <?php if ($selectedSlug && $selectedEvent): ?>
                        <div style="display:flex;align-items:center;gap:12px;background:#f0eeff;border-radius:10px;padding:12px 16px;margin-bottom:20px;">
                            <div style="width:36px;height:36px;background:#1e1548;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <i class="fas fa-calendar-alt" style="color:#fff;font-size:.85rem;"></i>
                            </div>
                            <div style="flex:1;min-width:0;">
                                <p style="margin:0;font-size:.68rem;color:#888;text-transform:uppercase;letter-spacing:.5px;font-weight:700;">Submitting for</p>
                                <p style="margin:0;font-weight:800;color:#1e1548;font-size:.95rem;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?= htmlspecialchars($selectedEvent['name']) ?></p>
                            </div>
                            <?php if (count($nomEvents) > 1): ?>
                            <a href="nominate.php" style="font-size:.78rem;color:#ed1c24;font-weight:700;white-space:nowrap;flex-shrink:0;text-decoration:none;">
                                <i class="fas fa-exchange-alt me-1"></i>Change
                            </a>
                            <?php endif; ?>
                        </div>
                        <input type="hidden" name="event_slug" id="event_slug_select" value="<?= htmlspecialchars($selectedSlug) ?>" data-event-name="<?= htmlspecialchars($selectedEvent['name']) ?>">
                        <?php else: ?>
                        <div class="mb-3">
                            <label>Event <span style="color:#ed1c24;">*</span></label>
                            <select name="event_slug" id="event_slug_select" required onchange="this.form.submit()">
                                <option value="">— Select Event —</option>
                                <?php foreach ($nomEvents as $ev): ?>
                                <option value="<?= htmlspecialchars($ev['slug']) ?>"
                                        <?= ($ev['slug'] === $selectedSlug) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($ev['name']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php endif; ?>

                        <?php if ($selectedSlug && empty($categories)): ?>
                        <!-- Event selected but no open nomination categories -->
                        <div style="background:rgba(237,180,0,0.1);border:1px solid rgba(237,180,0,0.3);border-radius:8px;padding:18px 20px;margin:20px 0;color:#7a5c00;">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>No open nomination categories</strong> for this event right now.
                            All categories are either admin-curated or the nomination window has closed.
                        </div>
                        <input type="hidden" name="event_slug" value="<?= htmlspecialchars($selectedSlug) ?>">

                        <?php elseif (!empty($categories)): ?>

                        <!-- Category select (Select2) -->
                        <div class="mb-4">
                            <label>Award Category <span style="color:#ed1c24;">*</span></label>
                            <select name="category_id" id="category_select">
                                <option value="">— Select a category —</option>
                                <?php foreach ($categories as $cat):
                                    $cId   = (int)$cat['id'];
                                    $cType = $cat['nomination_type'] ?? '';
                                    $typeLabel = $cType === 'application_form' ? 'Full Application' : 'Nomination';
                                ?>
                                <option value="<?= $cId ?>"
                                        data-type="<?= htmlspecialchars($cType) ?>"
                                        data-questions="<?= htmlspecialchars(json_encode($cat['nomination_questions'] ?? [])) ?>"
                                        <?= ($selectedCatId === $cId) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['name']) ?> — <?= $typeLabel ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <p id="cat-required-msg" style="display:none;font-size:.78rem;color:#ed1c24;margin-top:6px;"><i class="fas fa-exclamation-triangle me-1"></i>Please select a category.</p>
                        </div>

                        <!-- Application form questions (rendered dynamically by JS) -->
                        <div id="app-questions-container">
                        <?php if ($selectedCat && ($selectedCat['nomination_type'] ?? '') === 'application_form' && !empty($selectedCat['nomination_questions'])): ?>
                            <?php foreach ($selectedCat['nomination_questions'] as $qIdx => $q): ?>
                            <div class="app-question-wrap">
                                <label>
                                    <?= htmlspecialchars($q['label']) ?>
                                    <?php if (!empty($q['required'])): ?><span style="color:#ed1c24;">*</span><?php endif; ?>
                                </label>
                                <?php
                                $fname = 'app_q' . $qIdx;
                                $fval  = htmlspecialchars($_POST[$fname] ?? '');
                                if ($q['type'] === 'textarea'): ?>
                                <textarea name="<?= $fname ?>" rows="4" <?= !empty($q['required']) ? 'required' : '' ?>><?= $fval ?></textarea>
                                <?php elseif ($q['type'] === 'select' && !empty($q['options'])): ?>
                                <select name="<?= $fname ?>" <?= !empty($q['required']) ? 'required' : '' ?>>
                                    <option value="">— Select —</option>
                                    <?php foreach ($q['options'] as $opt): ?>
                                    <option value="<?= htmlspecialchars($opt) ?>" <?= ($fval === htmlspecialchars($opt)) ? 'selected' : '' ?>><?= htmlspecialchars($opt) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <?php else: ?>
                                <input type="<?= $q['type'] === 'number' ? 'number' : 'text' ?>"
                                       name="<?= $fname ?>"
                                       value="<?= $fval ?>"
                                       <?= !empty($q['required']) ? 'required' : '' ?>>
                                <?php endif; ?>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        </div>

                        <!-- ── Nominee section ── -->
                        <div class="section-divider" id="nominee-section-header">
                            <h6 id="nominee-section-title"><i class="fas fa-user me-2" style="color:#ed1c24;"></i>About the Nominee</h6>
                        </div>
                        <div class="mb-3">
                            <label id="nominee-name-label">Nominee's Full Name <span style="color:#ed1c24;">*</span></label>
                            <input type="text" name="nominee_name" id="nominee_name" value="<?= htmlspecialchars($_POST['nominee_name'] ?? '') ?>" placeholder="Full name of person you're nominating" required>
                        </div>

                        <!-- Country -->
                        <div class="mb-3" id="nominee-country-wrap">
                            <label id="nominee-country-label">Nominee's Country <span style="color:#aaa;font-weight:400;font-size:.82rem;">(optional)</span></label>
                            <select name="nominee_country" id="nominee_country">
                                <option value="">🌍  Select Country</option>
                                <optgroup label="── East Africa ──">
                                    <option value="KE">🇰🇪  Kenya</option>
                                    <option value="UG">🇺🇬  Uganda</option>
                                    <option value="TZ">🇹🇿  Tanzania</option>
                                    <option value="RW">🇷🇼  Rwanda</option>
                                    <option value="BI">🇧🇮  Burundi</option>
                                    <option value="SS">🇸🇸  South Sudan</option>
                                    <option value="ET">🇪🇹  Ethiopia</option>
                                    <option value="SO">🇸🇴  Somalia</option>
                                    <option value="DJ">🇩🇯  Djibouti</option>
                                    <option value="ER">🇪🇷  Eritrea</option>
                                </optgroup>
                                <optgroup label="── Rest of Africa ──">
                                    <option value="NG">🇳🇬  Nigeria</option>
                                    <option value="GH">🇬🇭  Ghana</option>
                                    <option value="ZA">🇿🇦  South Africa</option>
                                    <option value="EG">🇪🇬  Egypt</option>
                                    <option value="MA">🇲🇦  Morocco</option>
                                    <option value="SN">🇸🇳  Senegal</option>
                                    <option value="CM">🇨🇲  Cameroon</option>
                                    <option value="CI">🇨🇮  Côte d'Ivoire</option>
                                    <option value="CD">🇨🇩  DR Congo</option>
                                    <option value="ZM">🇿🇲  Zambia</option>
                                    <option value="ZW">🇿🇼  Zimbabwe</option>
                                    <option value="MZ">🇲🇿  Mozambique</option>
                                    <option value="AO">🇦🇴  Angola</option>
                                    <option value="MG">🇲🇬  Madagascar</option>
                                    <option value="MU">🇲🇺  Mauritius</option>
                                    <option value="TN">🇹🇳  Tunisia</option>
                                    <option value="LY">🇱🇾  Libya</option>
                                    <option value="SD">🇸🇩  Sudan</option>
                                    <option value="DZ">🇩🇿  Algeria</option>
                                    <option value="BW">🇧🇼  Botswana</option>
                                    <option value="NA">🇳🇦  Namibia</option>
                                    <option value="SZ">🇸🇿  Eswatini</option>
                                    <option value="LS">🇱🇸  Lesotho</option>
                                    <option value="MW">🇲🇼  Malawi</option>
                                </optgroup>
                                <optgroup label="── Europe ──">
                                    <option value="GB">🇬🇧  United Kingdom</option>
                                    <option value="DE">🇩🇪  Germany</option>
                                    <option value="FR">🇫🇷  France</option>
                                    <option value="NL">🇳🇱  Netherlands</option>
                                    <option value="SE">🇸🇪  Sweden</option>
                                    <option value="NO">🇳🇴  Norway</option>
                                    <option value="DK">🇩🇰  Denmark</option>
                                    <option value="IT">🇮🇹  Italy</option>
                                    <option value="ES">🇪🇸  Spain</option>
                                    <option value="PT">🇵🇹  Portugal</option>
                                    <option value="BE">🇧🇪  Belgium</option>
                                    <option value="CH">🇨🇭  Switzerland</option>
                                    <option value="AT">🇦🇹  Austria</option>
                                    <option value="FI">🇫🇮  Finland</option>
                                    <option value="IE">🇮🇪  Ireland</option>
                                    <option value="PL">🇵🇱  Poland</option>
                                </optgroup>
                                <optgroup label="── Americas ──">
                                    <option value="US">🇺🇸  United States</option>
                                    <option value="CA">🇨🇦  Canada</option>
                                    <option value="BR">🇧🇷  Brazil</option>
                                    <option value="MX">🇲🇽  Mexico</option>
                                    <option value="AR">🇦🇷  Argentina</option>
                                    <option value="CO">🇨🇴  Colombia</option>
                                    <option value="JM">🇯🇲  Jamaica</option>
                                    <option value="TT">🇹🇹  Trinidad &amp; Tobago</option>
                                </optgroup>
                                <optgroup label="── Asia &amp; Middle East ──">
                                    <option value="IN">🇮🇳  India</option>
                                    <option value="CN">🇨🇳  China</option>
                                    <option value="JP">🇯🇵  Japan</option>
                                    <option value="AE">🇦🇪  UAE</option>
                                    <option value="SA">🇸🇦  Saudi Arabia</option>
                                    <option value="QA">🇶🇦  Qatar</option>
                                    <option value="SG">🇸🇬  Singapore</option>
                                    <option value="MY">🇲🇾  Malaysia</option>
                                    <option value="PK">🇵🇰  Pakistan</option>
                                    <option value="BD">🇧🇩  Bangladesh</option>
                                    <option value="PH">🇵🇭  Philippines</option>
                                    <option value="IL">🇮🇱  Israel</option>
                                    <option value="TR">🇹🇷  Turkey</option>
                                </optgroup>
                                <optgroup label="── Oceania ──">
                                    <option value="AU">🇦🇺  Australia</option>
                                    <option value="NZ">🇳🇿  New Zealand</option>
                                </optgroup>
                            </select>
                        </div>

                        <!-- Photo dropzone -->
                        <div class="mb-4" id="nominee-photo-wrap">
                            <label id="nominee-photo-label" style="margin-bottom:8px;">Nominee's Photo <span style="color:#aaa;font-weight:400;font-size:.82rem;">(optional · JPG / PNG / WebP · max 2 MB)</span></label>
                            <input type="file" name="nominee_photo" id="nominee_photo" accept="image/jpeg,image/png,image/webp" style="display:none;">
                            <div id="photo-dropzone"
                                 onclick="document.getElementById('nominee_photo').click()"
                                 style="border:2px dashed #ddd;border-radius:12px;padding:28px 20px;text-align:center;cursor:pointer;background:#fafafa;transition:border-color .2s,background .2s;position:relative;">
                                <!-- Empty state -->
                                <div id="photo-empty-state">
                                    <div style="width:60px;height:60px;border-radius:50%;background:#f0eeff;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;">
                                        <i class="fas fa-camera" style="font-size:1.4rem;color:#1e1548;opacity:.6;"></i>
                                    </div>
                                    <p style="margin:0 0 4px;font-weight:700;font-size:.9rem;color:#333;">Click to upload or drag &amp; drop</p>
                                    <p style="margin:0;font-size:.8rem;color:#aaa;">JPG, PNG or WebP</p>
                                </div>
                                <!-- Preview state (hidden until file chosen) -->
                                <div id="photo-preview-state" style="display:none;align-items:center;gap:16px;justify-content:center;">
                                    <img id="photo-preview-img" src="" alt="Preview"
                                         style="width:72px;height:72px;border-radius:50%;object-fit:cover;border:3px solid #ede9f6;flex-shrink:0;">
                                    <div style="text-align:left;">
                                        <p id="photo-preview-name" style="margin:0 0 2px;font-weight:700;font-size:.9rem;color:#1e1548;word-break:break-all;"></p>
                                        <p id="photo-preview-size" style="margin:0 0 8px;font-size:.78rem;color:#aaa;"></p>
                                        <button type="button" id="photo-remove-btn"
                                                style="font-size:.78rem;color:#ed1c24;background:none;border:1px solid #fca5a5;border-radius:6px;padding:3px 10px;cursor:pointer;">
                                            <i class="fas fa-times me-1"></i>Remove
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <p id="photo-size-warning" style="display:none;font-size:.78rem;color:#ed1c24;margin-top:6px;">
                                <i class="fas fa-exclamation-triangle me-1"></i>File exceeds 2 MB — please choose a smaller image.
                            </p>
                        </div>

                        <div class="mb-4" id="nominee-desc-wrap">
                            <label id="nominee-desc-label">Why do they deserve this award?</label>
                            <textarea name="nominee_desc" rows="4" id="nominee_desc" placeholder="Tell us why this person deserves to win…"><?= htmlspecialchars($_POST['nominee_desc'] ?? '') ?></textarea>
                        </div>

                        <!-- ── Nominator / Your details section ── -->
                        <div class="section-divider" id="nominator-section-header">
                            <h6 id="nominator-section-title"><i class="fas fa-user-edit me-2" style="color:#ed1c24;"></i>Your Details (Nominator)</h6>
                        </div>
                        <div class="row">
                            <div class="col-sm-6 mb-3">
                                <label id="nominator-name-label">Your Name</label>
                                <input type="text" name="nominator_name" id="nominator_name" value="<?= htmlspecialchars($_POST['nominator_name'] ?? '') ?>" placeholder="Your full name">
                            </div>
                            <div class="col-sm-6 mb-3">
                                <label id="nominator-email-label">Your Email <span style="color:#ed1c24;">*</span></label>
                                <input type="email" name="nominator_email" id="nominator_email" value="<?= htmlspecialchars($_POST['nominator_email'] ?? '') ?>" placeholder="your@email.com" required>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label>Your Phone</label>
                            <input type="tel" name="nominator_phone" id="nominator_phone" value="<?= htmlspecialchars($_POST['nominator_phone'] ?? '') ?>">
                        </div>

                        <button type="button" id="nom-submit-btn" class="theme-btn btn-style-one" onclick="NomForm.submit()">
                            <span class="btn-title"><i class="fas fa-paper-plane me-2"></i>Submit Nomination</span>
                        </button>

                        <?php else: ?>
                        <input type="hidden" name="event_slug" value="<?= htmlspecialchars($selectedSlug) ?>">
                        <p class="text-muted" style="padding:20px 0;">Select an event above to see available categories.</p>
                        <?php endif; ?>

                    </form>
                </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
</div>
<div class="scroll-to-top scroll-to-target" data-target="html"><span class="fa fa-angle-up"></span></div>

<!-- ── Nomination Confirmation Modal ── -->
<div class="modal fade" id="nom-confirm-modal" tabindex="-1" role="dialog" aria-labelledby="nomConfirmTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:480px;" role="document">
        <div class="modal-content" style="border-radius:16px;border:none;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,0.18);">
            <!-- Header -->
            <div class="modal-header" style="background:linear-gradient(135deg,#1e1548 0%,#2d1f6b 100%);border:none;padding:20px 24px;align-items:center;">
                <div style="display:flex;align-items:center;gap:12px;flex:1;">
                    <div style="width:38px;height:38px;background:rgba(255,255,255,0.12);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="fas fa-clipboard-check" style="color:#fff;font-size:.95rem;"></i>
                    </div>
                    <div>
                        <h5 id="nomConfirmTitle" style="margin:0;color:#fff;font-weight:800;font-size:1rem;line-height:1.2;">Review Your Nomination</h5>
                        <p style="margin:0;color:rgba(255,255,255,0.55);font-size:.76rem;">Please confirm the details before submitting</p>
                    </div>
                </div>
                <button type="button" data-dismiss="modal" aria-label="Close" style="color:rgba(255,255,255,0.6);font-size:1.5rem;line-height:1;background:none;border:none;cursor:pointer;padding:0;margin-left:12px;">&times;</button>
            </div>
            <!-- Body -->
            <div class="modal-body" style="padding:20px 24px 16px;">
                <div id="nom-confirm-body">
                    <!-- populated by JS -->
                </div>
            </div>
            <!-- Footer -->
            <div class="modal-footer" style="border-top:2px solid #f5f5f5;padding:14px 24px;display:flex;justify-content:flex-end;gap:10px;">
                <button type="button" data-dismiss="modal"
                        style="padding:10px 18px;border-radius:9px;border:2px solid #e0e0e0;background:#fff;font-weight:700;font-size:.85rem;color:#666;cursor:pointer;">
                    <i class="fas fa-arrow-left" style="margin-right:5px;"></i>Go Back
                </button>
                <button type="button" id="nom-confirm-btn" onclick="NomForm._doSubmit()"
                        style="padding:10px 22px;border-radius:9px;border:none;background:#ed1c24;color:#fff;font-weight:700;font-size:.85rem;cursor:pointer;transition:background .2s;">
                    <span id="nom-confirm-btn-inner"><i class="fas fa-paper-plane" style="margin-right:6px;"></i>Confirm &amp; Submit</span>
                </button>
            </div>
        </div>
    </div>
</div>
<?php include 'includes/footer-links.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/js/intlTelInput.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- Toast notification -->
<div id="nom-toast" style="position:fixed;top:24px;left:24px;z-index:9999;min-width:280px;max-width:360px;display:none;">
    <div id="nom-toast-inner" style="border-radius:10px;padding:14px 18px 14px 14px;box-shadow:0 8px 30px rgba(0,0,0,0.15);display:flex;align-items:flex-start;gap:12px;">
        <i id="nom-toast-icon" style="font-size:1.3rem;margin-top:1px;flex-shrink:0;"></i>
        <div style="flex:1;">
            <strong id="nom-toast-title" style="display:block;font-size:.9rem;margin-bottom:2px;"></strong>
            <span id="nom-toast-msg" style="font-size:.83rem;opacity:.85;"></span>
        </div>
        <button onclick="document.getElementById('nom-toast').style.display='none'" style="background:none;border:none;font-size:1.1rem;cursor:pointer;opacity:.5;padding:0;margin-left:4px;">&times;</button>
    </div>
</div>

<script>
function showNomToast(type, title, msg) {
    var t = document.getElementById('nom-toast');
    var inner = document.getElementById('nom-toast-inner');
    var icon = document.getElementById('nom-toast-icon');
    var titleEl = document.getElementById('nom-toast-title');
    var msgEl = document.getElementById('nom-toast-msg');
    var colors = {
        success: {bg:'#f0fdf4',border:'#86efac',text:'#166534',icon:'fas fa-check-circle',iconColor:'#16a34a'},
        error:   {bg:'#fff1f2',border:'#fca5a5',text:'#991b1b',icon:'fas fa-exclamation-circle',iconColor:'#dc2626'},
        warning: {bg:'#fffbeb',border:'#fcd34d',text:'#92400e',icon:'fas fa-exclamation-triangle',iconColor:'#d97706'},
    };
    var c = colors[type] || colors.error;
    inner.style.cssText = 'border-radius:10px;padding:14px 18px 14px 14px;box-shadow:0 8px 30px rgba(0,0,0,0.15);display:flex;align-items:flex-start;gap:12px;background:'+c.bg+';border:1.5px solid '+c.border+';color:'+c.text+';';
    icon.className = c.icon;
    icon.style.color = c.iconColor;
    titleEl.textContent = title;
    msgEl.textContent = msg;
    t.style.display = 'block';
    clearTimeout(t._timer);
    t._timer = setTimeout(function(){ t.style.display='none'; }, 5000);
}

var NomForm = {
    mode: 'other', // 'self' or 'other'

    setMode: function(mode) {
        this.mode = mode;
        var btnSelf  = document.getElementById('btn-self');
        var btnOther = document.getElementById('btn-other');
        var activeStyle  = 'flex:1;padding:13px;border-radius:10px;border:2px solid #1e1548;background:#1e1548;font-weight:700;font-size:.9rem;color:#fff;cursor:pointer;transition:all .2s;';
        var passiveStyle = 'flex:1;padding:13px;border-radius:10px;border:2px solid #eee;background:#fff;font-weight:700;font-size:.9rem;color:#888;cursor:pointer;transition:all .2s;';

        if (mode === 'self') {
            btnSelf.style.cssText  = activeStyle;
            btnOther.style.cssText = passiveStyle;
            document.getElementById('form-headline').textContent = 'Nominate Yourself';
            document.getElementById('form-subheadline').textContent = 'Put your own name forward for consideration.';
            document.getElementById('nominee-section-title').innerHTML = '<i class="fas fa-user me-2" style="color:#ed1c24;"></i>Your Details';
            document.getElementById('nominee-name-label').innerHTML = 'Your Full Name <span style="color:#ed1c24;">*</span>';
            document.getElementById('nominee-desc-label').textContent = 'Why do you deserve this award?';
            document.getElementById('nominee-photo-label').innerHTML = 'Your Photo <span style="color:#aaa;font-weight:400;font-size:.82rem;">(optional · JPG / PNG / WebP · max 2 MB)</span>';
            var cl = document.getElementById('nominee-country-label');
            if (cl) cl.innerHTML = 'Your Country <span style="color:#aaa;font-weight:400;font-size:.82rem;">(optional)</span>';
            document.getElementById('nom-submit-btn').querySelector('.btn-title').innerHTML = '<i class="fas fa-paper-plane me-2"></i>Submit My Nomination';
            // Hide separate nominator section (you ARE the nominee)
            document.getElementById('nominator-section-header').style.display = 'none';
            document.getElementById('nominator_name').closest('.col-sm-6').style.display = 'none';
            // Sync nominee_name → nominator_name automatically
            document.getElementById('nominee_name').oninput = function() {
                document.getElementById('nominator_name').value = this.value;
            };
            document.getElementById('nominator_email').closest('.col-sm-6').querySelector('label').innerHTML = 'Your Email <span style="color:#ed1c24;">*</span>';
            // Move email/phone under nominee section visually
            document.getElementById('nominator-section-header').nextElementSibling && null;
        } else {
            btnSelf.style.cssText  = passiveStyle;
            btnOther.style.cssText = activeStyle;
            document.getElementById('form-headline').textContent = 'Nominate Someone';
            document.getElementById('form-subheadline').textContent = 'Know someone who deserves recognition? Put their name forward.';
            document.getElementById('nominee-section-title').innerHTML = '<i class="fas fa-user me-2" style="color:#ed1c24;"></i>About the Nominee';
            document.getElementById('nominee-name-label').innerHTML = "Nominee's Full Name <span style='color:#ed1c24;'>*</span>";
            document.getElementById('nominee-desc-label').textContent = 'Why do they deserve this award?';
            document.getElementById('nominee-photo-label').innerHTML = "Nominee's Photo <span style='color:#aaa;font-weight:400;font-size:.82rem;'>(optional · JPG / PNG / WebP · max 2 MB)</span>";
            var cl2 = document.getElementById('nominee-country-label');
            if (cl2) cl2.innerHTML = "Nominee's Country <span style='color:#aaa;font-weight:400;font-size:.82rem;'>(optional)</span>";
            document.getElementById('nom-submit-btn').querySelector('.btn-title').innerHTML = '<i class="fas fa-paper-plane me-2"></i>Submit Nomination';
            document.getElementById('nominator-section-header').style.display = '';
            document.getElementById('nominator_name').closest('.col-sm-6').style.display = '';
            document.getElementById('nominee_name').oninput = null;
        }
    },

    submit: function() {
        var form     = document.getElementById('nom-form');
        var slug     = document.getElementById('event_slug_select')
                         ? document.getElementById('event_slug_select').value
                         : (form.querySelector('[name="event_slug"]') ? form.querySelector('[name="event_slug"]').value : '');
        var catEl    = document.getElementById('category_select');
        var catId    = catEl ? (parseInt(catEl.value) || 0) : 0;
        var nomName  = (form.querySelector('[name="nominee_name"]') || {}).value || '';
        var nomEmail = (form.querySelector('[name="nominator_email"]') || {}).value || '';

        // In self mode, sync name fields before submitting
        if (this.mode === 'self') {
            var nomEl = document.getElementById('nominator_name');
            if (nomEl) nomEl.value = nomName;
        }

        // ── Validation ──────────────────────────────────────────────
        if (!slug)  { showNomToast('warning','Missing field','Please select an event.'); return; }
        if (!catId) {
            var msg = document.getElementById('cat-required-msg');
            if (msg) msg.style.display = 'block';
            showNomToast('warning','Missing field','Please select an award category.'); return;
        }
        var catMsg = document.getElementById('cat-required-msg');
        if (catMsg) catMsg.style.display = 'none';
        if (!nomName)  { showNomToast('warning','Missing field','Please enter ' + (this.mode === 'self' ? 'your name.' : "the nominee's name.")); return; }
        if (!nomEmail) { showNomToast('warning','Missing field','Please enter your email address.'); return; }

        var photoInput = document.getElementById('nominee_photo');
        if (photoInput && photoInput.files[0] && photoInput.files[0].size > 2 * 1024 * 1024) {
            showNomToast('warning','Photo too large','Please choose a photo under 2 MB.');
            return;
        }

        // ── Build FormData (stored for _doSubmit) ───────────────────
        var fd = new FormData();
        fd.append('event_slug',      slug);
        fd.append('category_id',     catId);
        fd.append('nominee_name',    nomName);
        fd.append('nominee_desc',    (form.querySelector('[name="nominee_desc"]') || {}).value || '');
        fd.append('nominator_name',  this.mode === 'self' ? nomName : ((form.querySelector('[name="nominator_name"]') || {}).value || ''));
        fd.append('nominator_email', nomEmail);
        fd.append('nominator_phone', NomForm._itiPhone ? NomForm._itiPhone.getNumber() : ((form.querySelector('[name="nominator_phone"]') || {}).value || ''));
        fd.append('self_nomination', this.mode === 'self' ? '1' : '0');
        fd.append('nominee_country', (form.querySelector('[name="nominee_country"]') || {}).value || '');
        form.querySelectorAll('[name^="app_"]').forEach(function(el) {
            fd.append('application_answers[' + el.name.slice(4) + ']', el.value);
        });
        if (photoInput && photoInput.files[0]) {
            fd.append('nominee_photo', photoInput.files[0]);
        }
        this._pendingFd = fd;

        // ── Build confirmation summary ───────────────────────────────
        var eventEl   = document.getElementById('event_slug_select');
        var eventName = eventEl
            ? (eventEl.getAttribute('data-event-name') || (eventEl.tagName === 'SELECT' && eventEl.selectedIndex >= 0 ? eventEl.options[eventEl.selectedIndex].text : slug))
            : slug;
        var catName = (catEl && catEl.selectedIndex >= 0)
            ? catEl.options[catEl.selectedIndex].text.replace(/ — (Nomination|Full Application)$/, '').trim()
            : '';
        var countryEl   = document.getElementById('nominee_country');
        var countryText = (countryEl && countryEl.selectedIndex > 0) ? countryEl.options[countryEl.selectedIndex].text.trim() : '';
        var phoneNum    = NomForm._itiPhone ? NomForm._itiPhone.getNumber() : ((form.querySelector('[name="nominator_phone"]') || {}).value || '');
        var nomDesc     = (form.querySelector('[name="nominee_desc"]') || {}).value || '';

        var rows = [
            { label: 'Event',      value: eventName },
            { label: 'Category',   value: catName },
            { label: 'Nominee',    value: nomName },
            { label: 'Nominating', value: this.mode === 'self' ? 'Myself' : 'Someone else' },
        ];
        if (countryText) rows.push({ label: 'Country',    value: countryText });
        rows.push(        { label: 'Your Email', value: nomEmail });
        if (phoneNum)    rows.push({ label: 'Your Phone', value: phoneNum });
        if (nomDesc) {
            var descLabelEl = document.getElementById('nominee-desc-label');
            var descLabel = descLabelEl ? descLabelEl.textContent.trim() : 'Statement';
            rows.push({ label: descLabel, value: nomDesc.length > 120 ? nomDesc.slice(0, 120) + '…' : nomDesc });
        }

        // Add application form answers to summary
        var catSel = document.getElementById('category_select');
        var catOpt = catSel ? catSel.querySelector('option[value="' + catId + '"]') : null;
        var catType = catOpt ? (catOpt.getAttribute('data-type') || '') : '';
        var catQuestions = [];
        if (catOpt) { try { catQuestions = JSON.parse(catOpt.getAttribute('data-questions') || '[]'); } catch(e) {} }
        if (catType === 'application_form' && catQuestions.length) {
            catQuestions.forEach(function(q, qIdx) {
                var el = form.querySelector('[name="app_q' + qIdx + '"]');
                var val = el ? (el.value || '').trim() : '';
                if (val) {
                    var shortVal = val.length > 140 ? val.slice(0, 140) + '…' : val;
                    rows.push({ label: q.label || ('Q' + (qIdx + 1)), value: shortVal });
                }
            });
        }

        var photoSrc = document.getElementById('photo-preview-img') ? document.getElementById('photo-preview-img').src : '';
        var hasPhoto = !!(photoInput && photoInput.files[0] && photoSrc);

        var bodyEl = document.getElementById('nom-confirm-body');
        bodyEl.innerHTML = '';

        // Photo + name header strip (if photo attached)
        if (hasPhoto) {
            bodyEl.innerHTML +=
                '<div style="display:flex;align-items:center;gap:14px;padding:0 0 14px;border-bottom:2px solid #f0eeff;margin-bottom:6px;">' +
                '<img src="' + photoSrc + '" style="width:54px;height:54px;border-radius:50%;object-fit:cover;border:3px solid #ede9f6;flex-shrink:0;">' +
                '<div><p style="margin:0;font-weight:800;font-size:.95rem;color:#1e1548;">' + nomName + '</p>' +
                '<p style="margin:0;font-size:.78rem;color:#888;margin-top:2px;"><i class="fas fa-image" style="margin-right:4px;color:#ed1c24;"></i>Photo attached</p></div>' +
                '</div>';
        }

        // Data rows
        bodyEl.innerHTML += rows.map(function(r, i) {
            var last = i === rows.length - 1;
            return '<div style="display:flex;align-items:flex-start;gap:12px;padding:10px 0;' + (last ? '' : 'border-bottom:1px solid #f5f5f5;') + '">' +
                   '<span style="font-size:.72rem;font-weight:700;color:#aaa;text-transform:uppercase;letter-spacing:.6px;min-width:95px;flex-shrink:0;padding-top:3px;">' + r.label + '</span>' +
                   '<span style="font-size:.9rem;font-weight:600;color:#1e1548;flex:1;word-break:break-word;">' + r.value + '</span>' +
                   '</div>';
        }).join('');

        // Show modal
        if (typeof $ !== 'undefined') {
            $('#nom-confirm-modal').modal('show');
        }
    },

    _pendingFd: null,

    _doSubmit: function() {
        var fd = this._pendingFd;
        if (!fd) return;
        this._pendingFd = null;

        // Close modal
        if (typeof $ !== 'undefined') {
            $('#nom-confirm-modal').modal('hide');
        }

        var btn = document.getElementById('nom-submit-btn');
        var originalBtnHtml = btn.querySelector('.btn-title').innerHTML;
        btn.disabled = true;
        btn.querySelector('.btn-title').innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Submitting…';

        fetch('<?= API_BASE ?>/api/public/nominations', {
            method: 'POST',
            headers: {'Accept':'application/json'},
            body: fd
        })
        .then(function(r){ return r.json(); })
        .then(function(data) {
            btn.disabled = false;
            btn.querySelector('.btn-title').innerHTML = originalBtnHtml;
            if (data.success) {
                showNomToast('success','Nomination submitted!','Thank you — your nomination has been received.');
                var form = document.getElementById('nom-form');
                form.reset();
                if (typeof $ !== 'undefined' && $.fn.select2) {
                    $('#category_select').val(null).trigger('change');
                    $('#nominee_country').val(null).trigger('change');
                }
                document.getElementById('app-questions-container').innerHTML = '';
                var descWrap = document.getElementById('nominee-desc-wrap');
                if (descWrap) descWrap.style.display = '';
                NomForm._resetDropzone();
            } else {
                showNomToast('error','Submission failed', data.error || 'Could not submit. Please try again.');
            }
        })
        .catch(function() {
            btn.disabled = false;
            btn.querySelector('.btn-title').innerHTML = originalBtnHtml;
            showNomToast('error','Network error','Could not connect. Please check your connection and try again.');
        });
    },

    _itiPhone: null,

    selectCategory: function(el) {
        // Deselect all
        document.querySelectorAll('.cat-card').forEach(function(c) {
            c.style.border = '2px solid #eee';
            c.style.background = '#fafafa';
        });
        // Select clicked card
        el.style.border = '2px solid #1e1548';
        el.style.background = '#f0eeff';
        var catId = el.getAttribute('data-cat-id');
        document.getElementById('category_id_hidden').value = catId;
        var msg = document.getElementById('cat-required-msg');
        if (msg) msg.style.display = 'none';
        // Get type/questions from card attrs
        var type = el.getAttribute('data-type') || '';
        var questions = [];
        try { questions = JSON.parse(el.getAttribute('data-questions') || '[]'); } catch(e) {}
        NomForm.onCategoryChange(catId, type, questions);
    },

    _resetDropzone: function() {
        document.getElementById('photo-empty-state').style.display = 'block';
        document.getElementById('photo-preview-state').style.display = 'none';
        document.getElementById('photo-size-warning').style.display = 'none';
        document.getElementById('photo-dropzone').style.borderColor = '#ddd';
        document.getElementById('photo-dropzone').style.background = '#fafafa';
        document.getElementById('photo-preview-img').src = '';
    },

    onCategoryChange: function(catId, typeOverride, questionsOverride) {
        var container = document.getElementById('app-questions-container');
        var descWrap  = document.getElementById('nominee-desc-wrap');
        if (!container) return;

        var type, questions;
        if (typeOverride !== undefined) {
            type      = typeOverride;
            questions = questionsOverride || [];
        } else {
            var card = document.querySelector('.cat-card[data-cat-id="' + catId + '"]');
            if (card) {
                type = card.getAttribute('data-type') || '';
                try { questions = JSON.parse(card.getAttribute('data-questions') || '[]'); } catch(e) { questions = []; }
            } else {
                var sel = document.getElementById('category_select');
                var opt = sel ? sel.querySelector('option[value="' + catId + '"]') : null;
                if (!opt) return;
                type = opt.getAttribute('data-type') || '';
                try { questions = JSON.parse(opt.getAttribute('data-questions') || '[]'); } catch(e) { questions = []; }
            }
        }

        // Clear previous dynamic questions
        container.innerHTML = '';

        if (type === 'application_form' && questions.length) {
            // Hide generic description field (application has its own fields)
            if (descWrap) descWrap.style.display = 'none';

            questions.forEach(function(q, qIdx) {
                var wrap = document.createElement('div');
                wrap.className = 'app-question-wrap';

                var lbl = document.createElement('label');
                lbl.innerHTML = q.label + (q.required ? ' <span style="color:#ed1c24;">*</span>' : '');
                wrap.appendChild(lbl);

                var input;
                if (q.type === 'textarea') {
                    input = document.createElement('textarea');
                    input.rows = 4;
                } else if (q.type === 'select' && q.options) {
                    input = document.createElement('select');
                    var defOpt = document.createElement('option');
                    defOpt.value = ''; defOpt.textContent = '— Select —';
                    input.appendChild(defOpt);
                    q.options.forEach(function(o) {
                        var op = document.createElement('option');
                        op.value = o; op.textContent = o;
                        input.appendChild(op);
                    });
                } else {
                    input = document.createElement('input');
                    input.type = q.type === 'number' ? 'number' : 'text';
                }
                input.name = 'app_q' + qIdx;
                if (q.required) input.required = true;
                wrap.appendChild(input);
                container.appendChild(wrap);
            });
        } else {
            if (descWrap) descWrap.style.display = '';
        }
    }
};

// ── Photo Dropzone ─────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function() {
    var dropzone  = document.getElementById('photo-dropzone');
    var fileInput = document.getElementById('nominee_photo');
    if (!dropzone || !fileInput) return;

    function handleFile(file) {
        if (!file) return;

        // Type check
        var allowed = ['image/jpeg','image/png','image/webp'];
        if (!allowed.includes(file.type)) {
            showNomToast('warning','Invalid file type','Please upload a JPG, PNG or WebP image.');
            return;
        }

        // Size warning (block >2 MB at submit time, warn here)
        var sizeWarn = document.getElementById('photo-size-warning');
        sizeWarn.style.display = file.size > 2 * 1024 * 1024 ? 'block' : 'none';

        // Preview
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('photo-preview-img').src = e.target.result;
            document.getElementById('photo-preview-name').textContent = file.name;
            document.getElementById('photo-preview-size').textContent = (file.size / 1024).toFixed(0) + ' KB';
            document.getElementById('photo-empty-state').style.display = 'none';
            document.getElementById('photo-preview-state').style.display = 'flex';
            dropzone.style.borderColor = '#1e1548';
            dropzone.style.background  = '#f7f5ff';
        };
        reader.readAsDataURL(file);
    }

    // Click to browse
    fileInput.addEventListener('change', function() {
        if (this.files[0]) handleFile(this.files[0]);
    });

    // Remove button
    document.getElementById('photo-remove-btn').addEventListener('click', function(e) {
        e.stopPropagation();
        fileInput.value = '';
        NomForm._resetDropzone();
    });

    // Drag & drop
    dropzone.addEventListener('dragover', function(e) {
        e.preventDefault();
        this.style.borderColor = '#ed1c24';
        this.style.background  = '#fff5f5';
    });
    dropzone.addEventListener('dragleave', function() {
        this.style.borderColor = '#ddd';
        this.style.background  = '#fafafa';
    });
    dropzone.addEventListener('drop', function(e) {
        e.preventDefault();
        this.style.borderColor = '#ddd';
        this.style.background  = '#fafafa';
        var file = e.dataTransfer.files[0];
        if (file) {
            // Inject into the file input so FormData picks it up
            var dt = new DataTransfer();
            dt.items.add(file);
            fileInput.files = dt.files;
            handleFile(file);
        }
    });
});

// intl-tel-input + Select2 country picker
document.addEventListener('DOMContentLoaded', function() {
    var phoneEl = document.getElementById('nominator_phone');
    if (phoneEl && window.intlTelInput) {
        NomForm._itiPhone = window.intlTelInput(phoneEl, {
            initialCountry: 'ke',
            preferredCountries: ['ke','ug','tz','rw','et','gh','ng','za','gb','us','ae'],
            separateDialCode: true,
            utilsScript: 'https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/js/utils.js',
        });
    }

    if (typeof $ !== 'undefined' && $.fn.select2) {
        // Category picker
        $('#category_select').select2({
            placeholder: '— Select a category —',
            width: '100%',
        });
        $('#category_select').on('change', function() {
            var catId = $(this).val();
            var opt   = this.options[this.selectedIndex];
            var type  = opt ? (opt.getAttribute('data-type') || '') : '';
            var questions = [];
            try { questions = JSON.parse((opt ? opt.getAttribute('data-questions') : null) || '[]'); } catch(e) {}
            NomForm.onCategoryChange(catId, type, questions);
            var msg = document.getElementById('cat-required-msg');
            if (msg) msg.style.display = 'none';
        });

        // Country picker
        $('#nominee_country').select2({
            placeholder: '🌍  Select Country',
            allowClear: true,
            width: '100%',
            dropdownAutoWidth: false,
        });

        // Sync phone flag when country changes (Select2 triggers native change)
        $('#nominee_country').on('change', function() {
            var code = $(this).val();
            if (code && NomForm._itiPhone) {
                NomForm._itiPhone.setCountry(code.toLowerCase());
            }
        });
    }
});

// On page load: set mode, then trigger category questions if pre-selected
document.addEventListener('DOMContentLoaded', function() {
    NomForm.setMode(<?= json_encode($_GET['mode'] ?? 'other') ?> === 'self' ? 'self' : 'other');
    var preselect = <?= $selectedCatId ?>;
    if (preselect) {
        NomForm.onCategoryChange(preselect);
    }
});
</script>
</body>
</html>
