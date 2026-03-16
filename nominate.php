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
{"@context":"https://schema.org/","@type":"Organization","name":"Tuqio Hub","url":"https://tuqiohub.africa","contactPoint":{"@type":"ContactPoint","telephone":"+254757140682","email":"info@tuqiohub.africa","contactType":"customer support"},"sameAs":["https://www.instagram.com/p/DV0RJ11ii-7/?igsh=MXNiemxwbXdzMzJ6aw==","https://www.facebook.com/share/p/1DJyLwtvqf/","https://twitter.com/tuqiohub","https://www.tiktok.com/@tuqiohubke"]}
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

                    <form method="post" action="" id="nom-form">
                        <!-- Event select -->
                        <div class="mb-3">
                            <label>Event <span style="color:#ed1c24;">*</span></label>
                            <select name="event_slug" id="event_slug_select" required
                                    onchange="this.form.submit()">
                                <option value="">— Select Event —</option>
                                <?php foreach ($nomEvents as $ev): ?>
                                <option value="<?= htmlspecialchars($ev['slug']) ?>"
                                        <?= ($ev['slug'] === $selectedSlug) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($ev['name']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <?php if ($selectedSlug && empty($categories)): ?>
                        <!-- Event selected but no open nomination categories -->
                        <div style="background:rgba(237,180,0,0.1);border:1px solid rgba(237,180,0,0.3);border-radius:8px;padding:18px 20px;margin:20px 0;color:#7a5c00;">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>No open nomination categories</strong> for this event right now.
                            All categories are either admin-curated or the nomination window has closed.
                        </div>
                        <input type="hidden" name="event_slug" value="<?= htmlspecialchars($selectedSlug) ?>">

                        <?php elseif (!empty($categories)): ?>

                        <!-- Category -->
                        <div class="mb-3">
                            <label>Award Category <span style="color:#ed1c24;">*</span></label>
                            <select name="category_id" id="category_select" required
                                    onchange="NomForm.onCategoryChange(this.value)">
                                <option value="">— Select Category —</option>
                                <?php foreach ($categories as $cat): ?>
                                <option value="<?= (int)$cat['id'] ?>"
                                        data-type="<?= htmlspecialchars($cat['nomination_type'] ?? '') ?>"
                                        data-questions="<?= htmlspecialchars(json_encode($cat['nomination_questions'] ?? [])) ?>"
                                        <?= ($selectedCatId === (int)$cat['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['name']) ?>
                                    <?php if (($cat['nomination_type'] ?? '') === 'application_form'): ?>
                                    (Full Application)
                                    <?php endif; ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Application form questions (rendered dynamically by JS) -->
                        <div id="app-questions-container">
                        <?php if ($selectedCat && ($selectedCat['nomination_type'] ?? '') === 'application_form' && !empty($selectedCat['nomination_questions'])): ?>
                            <?php foreach ($selectedCat['nomination_questions'] as $q): ?>
                            <div class="app-question-wrap">
                                <label>
                                    <?= htmlspecialchars($q['label']) ?>
                                    <?php if (!empty($q['required'])): ?><span style="color:#ed1c24;">*</span><?php endif; ?>
                                </label>
                                <?php
                                $fname = 'app_' . htmlspecialchars($q['key']);
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
                            <input type="tel" name="nominator_phone" id="nominator_phone" value="<?= htmlspecialchars($_POST['nominator_phone'] ?? '') ?>" placeholder="+254 700 000 000">
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
<?php include 'includes/footer-links.php'; ?>

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
            document.getElementById('nom-submit-btn').querySelector('.btn-title').innerHTML = '<i class="fas fa-paper-plane me-2"></i>Submit Nomination';
            document.getElementById('nominator-section-header').style.display = '';
            document.getElementById('nominator_name').closest('.col-sm-6').style.display = '';
            document.getElementById('nominee_name').oninput = null;
        }
    },

    submit: function() {
        var form   = document.getElementById('nom-form');
        var btn    = document.getElementById('nom-submit-btn');
        var slug   = document.getElementById('event_slug_select')
                       ? document.getElementById('event_slug_select').value
                       : (form.querySelector('[name="event_slug"]') ? form.querySelector('[name="event_slug"]').value : '');
        var catEl  = document.getElementById('category_select');
        var catId  = catEl ? parseInt(catEl.value) : 0;
        var nomName  = (form.querySelector('[name="nominee_name"]') || {}).value || '';
        var nomEmail = (form.querySelector('[name="nominator_email"]') || {}).value || '';

        // In self mode, sync name fields before submitting
        if (this.mode === 'self') {
            var nomEl = document.getElementById('nominator_name');
            if (nomEl) nomEl.value = nomName;
        }

        if (!slug)     { showNomToast('warning','Missing field','Please select an event.'); return; }
        if (!catId)    { showNomToast('warning','Missing field','Please select an award category.'); return; }
        if (!nomName)  { showNomToast('warning','Missing field','Please enter ' + (this.mode === 'self' ? 'your name.' : "the nominee's name.")); return; }
        if (!nomEmail) { showNomToast('warning','Missing field','Please enter your email address.'); return; }

        var payload = {
            event_slug:      slug,
            category_id:     catId,
            nominee_name:    nomName,
            nominee_desc:    (form.querySelector('[name="nominee_desc"]') || {}).value || '',
            nominator_name:  this.mode === 'self' ? nomName : ((form.querySelector('[name="nominator_name"]') || {}).value || ''),
            nominator_email: nomEmail,
            nominator_phone: (form.querySelector('[name="nominator_phone"]') || {}).value || '',
            self_nomination: this.mode === 'self',
        };

        // Gather application_form answers
        var answers = {};
        form.querySelectorAll('[name^="app_"]').forEach(function(el) {
            answers[el.name.slice(4)] = el.value;
        });
        if (Object.keys(answers).length) payload.application_answers = answers;

        btn.disabled = true;
        btn.querySelector('.btn-title').innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Submitting…';

        fetch('<?= API_BASE ?>/api/public/nominations', {
            method: 'POST',
            headers: {'Content-Type':'application/json','Accept':'application/json'},
            body: JSON.stringify(payload)
        })
        .then(function(r){ return r.json(); })
        .then(function(data) {
            btn.disabled = false;
            btn.querySelector('.btn-title').innerHTML = '<i class="fas fa-paper-plane me-2"></i>Submit Nomination';
            if (data.success) {
                showNomToast('success','Nomination submitted!','Thank you — your nomination has been received.');
                form.reset();
                document.getElementById('app-questions-container').innerHTML = '';
                var descWrap = document.getElementById('nominee-desc-wrap');
                if (descWrap) descWrap.style.display = '';
            } else {
                showNomToast('error','Submission failed', data.error || 'Could not submit. Please try again.');
            }
        })
        .catch(function() {
            btn.disabled = false;
            btn.querySelector('.btn-title').innerHTML = '<i class="fas fa-paper-plane me-2"></i>Submit Nomination';
            showNomToast('error','Network error','Could not connect. Please check your connection and try again.');
        });
    },

    onCategoryChange: function(catId) {
        var sel = document.getElementById('category_select');
        var opt = sel ? sel.querySelector('option[value="' + catId + '"]') : null;
        var container = document.getElementById('app-questions-container');
        var descWrap   = document.getElementById('nominee-desc-wrap');
        if (!opt || !container) return;

        var type      = opt.getAttribute('data-type') || '';
        var questions = [];
        try { questions = JSON.parse(opt.getAttribute('data-questions') || '[]'); } catch(e) {}

        // Clear previous dynamic questions
        container.innerHTML = '';

        if (type === 'application_form' && questions.length) {
            // Hide generic description field (application has its own fields)
            if (descWrap) descWrap.style.display = 'none';

            questions.forEach(function(q) {
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
                input.name = 'app_' + q.key;
                if (q.required) input.required = true;
                wrap.appendChild(input);
                container.appendChild(wrap);
            });
        } else {
            if (descWrap) descWrap.style.display = '';
        }
    }
};

// On page load: set mode, then auto-select category if pre-set via URL
document.addEventListener('DOMContentLoaded', function() {
    NomForm.setMode(<?= json_encode($_GET['mode'] ?? 'other') ?> === 'self' ? 'self' : 'other');
    var preselect = <?= $selectedCatId ?>;
    if (preselect) {
        var sel = document.getElementById('category_select');
        if (sel) {
            sel.value = preselect;
            NomForm.onCategoryChange(preselect);
        }
    }
});
</script>
</body>
</html>
