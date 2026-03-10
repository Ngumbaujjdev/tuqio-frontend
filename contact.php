<?php
include 'config/config.php';
include 'libs/App.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">

<!-- SEO -->
<title>Contact Us | Tuqio Hub</title>
<meta name="description" content="Get in touch with Tuqio Hub. Contact us for event management inquiries, partnerships, nominations, or general support. Email: tuqio@independentkenyawomenawards.com">
<meta name="keywords" content="contact Tuqio Hub, Tuqio Hub support, event management inquiry Kenya, Tuqio Hub email, Nairobi events contact">
<meta name="author" content="Tuqio Hub">
<meta name="robots" content="index, follow">
<link rel="canonical" href="https://tuqio.independentkenyawomenawards.com/contact.php">

<!-- Schema.org microdata -->
<meta itemprop="name" content="Contact Us | Tuqio Hub">
<meta itemprop="description" content="Get in touch with Tuqio Hub for event management inquiries, partnerships, or support.">
<meta itemprop="image" content="<?= OG_IMAGE ?>">

<!-- Open Graph -->
<meta property="og:title" content="Contact Us | Tuqio Hub">
<meta property="og:type" content="website">
<meta property="og:image" content="<?= OG_IMAGE ?>">
<meta property="og:image:type" content="image/webp">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:url" content="https://tuqio.independentkenyawomenawards.com/contact.php">
<meta property="og:description" content="Get in touch with Tuqio Hub for event management inquiries, partnerships, or support.">
<meta property="og:site_name" content="Tuqio Hub">

<!-- Twitter Card -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:site" content="@tuqiohub">
<meta name="twitter:title" content="Contact Us | Tuqio Hub">
<meta name="twitter:description" content="Get in touch with Tuqio Hub for event management inquiries, partnerships, or support.">
<meta name="twitter:image" content="<?= OG_IMAGE ?>">

<!-- Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-XXXXXXXXXX"></script>
<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','G-XXXXXXXXXX');</script>

<!-- JSON-LD: Organization -->
<script type="application/ld+json">
{"@context":"https://schema.org/","@type":"Organization","name":"Tuqio Hub","url":"https://tuqio.independentkenyawomenawards.com","description":"Kenya's premier event management and awards platform.","contactPoint":{"@type":"ContactPoint","telephone":"+254757140682","email":"tuqio@independentkenyawomenawards.com","contactType":"customer support"},"sameAs":["https://www.instagram.com/tuqiohub","https://www.facebook.com/tuqiohub","https://twitter.com/tuqiohub"]}
</script>

<!-- JSON-LD: BreadcrumbList -->
<script type="application/ld+json">
{"@context":"https://schema.org","@type":"BreadcrumbList","itemListElement":[{"@type":"ListItem","position":1,"name":"Home","item":"https://tuqio.independentkenyawomenawards.com/"},{"@type":"ListItem","position":2,"name":"Contact","item":"https://tuqio.independentkenyawomenawards.com/contact.php"}]}
</script>

<!-- JSON-LD: ContactPage -->
<script type="application/ld+json">
{"@context":"https://schema.org","@type":"ContactPage","name":"Contact Us | Tuqio Hub","url":"https://tuqio.independentkenyawomenawards.com/contact.php","description":"Get in touch with Tuqio Hub for event management inquiries, partnerships, or support."}
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
.contact-section { padding: 70px 0; background: #f9fafb; }

.contact-form-wrap {
    background: #fff;
    border-radius: 14px;
    padding: 40px;
    box-shadow: 0 4px 24px rgba(0,0,0,0.07);
}
.contact-form-wrap label {
    font-weight: 600;
    font-size: .88rem;
    color: #1e1548;
    margin-bottom: 6px;
    display: block;
}
.contact-form-wrap input,
.contact-form-wrap textarea,
.contact-form-wrap select {
    width: 100%;
    border: 2px solid #eee;
    border-radius: 8px;
    padding: 11px 14px;
    font-size: .9rem;
    color: #333;
    transition: border-color .2s;
    background: #fafafa;
}
.contact-form-wrap input:focus,
.contact-form-wrap textarea:focus,
.contact-form-wrap select:focus {
    border-color: #ed1c24;
    outline: none;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(237,28,36,0.1);
}

.contact-info-box {
    background: linear-gradient(135deg, #1e1548, #2d1f6b);
    border-radius: 14px;
    padding: 36px;
    color: #fff;
}
.contact-info-box h4 { font-weight: 800; margin-bottom: 24px; }

.ci-row { display: flex; gap: 14px; align-items: flex-start; margin-bottom: 22px; }
.ci-icon {
    width: 42px; height: 42px;
    border-radius: 10px;
    background: rgba(237,28,36,0.2);
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
    color: #ed1c24;
}
.ci-label { font-size: .72rem; text-transform: uppercase; letter-spacing: 1px; opacity: .6; margin-bottom: 2px; }
.ci-value { font-size: .9rem; color: rgba(255,255,255,.9); }
.ci-value a { color: rgba(255,255,255,.9); text-decoration: none; }
.ci-value a:hover { color: #ed1c24; }

.ci-social { padding-top: 24px; margin-top: 8px; border-top: 1px solid rgba(255,255,255,0.15); }
.ci-social .ci-label { margin-bottom: 14px; }
.ci-social-links { display: flex; gap: 10px; }
.ci-social-links a {
    width: 38px; height: 38px;
    border-radius: 50%;
    background: rgba(255,255,255,0.1);
    display: flex; align-items: center; justify-content: center;
    color: rgba(255,255,255,.7);
    font-size: .9rem;
    text-decoration: none;
    transition: background .2s, color .2s;
}
.ci-social-links a:hover { background: #ed1c24; color: #fff; }

.alert-success-custom {
    background: rgba(16,185,129,0.1);
    border: 1px solid rgba(16,185,129,0.3);
    border-radius: 8px;
    padding: 14px 18px;
    margin-bottom: 20px;
    color: #065f46;
    font-size: .9rem;
}
.alert-error-custom {
    background: rgba(237,28,36,0.08);
    border: 1px solid rgba(237,28,36,0.25);
    border-radius: 8px;
    padding: 14px 18px;
    margin-bottom: 20px;
    color: #c41820;
    font-size: .9rem;
}
.required-star { color: #ed1c24; }
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
            <h1>Contact Us</h1>
            <ul class="page-breadcrumb">
                <li><a href="<?= SITE_URL ?>">Home</a></li>
                <li>Contact</li>
            </ul>
        </div>
    </div>
</section>

<section class="contact-section">
    <div class="auto-container">
        <div class="row">
            <div class="col-lg-7 mb-5 mb-lg-0 wow fadeInLeft">
                <div class="contact-form-wrap">
                    <h3 style="font-weight:800;color:#1e1548;margin-bottom:6px;">Send Us a Message</h3>
                    <p class="text-muted mb-4" style="font-size:.9rem;">We'd love to hear from you. Fill in the form and we'll get back to you within 24 hours.</p>

                    <form id="contactForm">
                        <div class="row">
                            <div class="col-sm-6 mb-3">
                                <label>Full Name <span class="required-star">*</span></label>
                                <input type="text" name="name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" placeholder="Your full name">
                            </div>
                            <div class="col-sm-6 mb-3">
                                <label>Email Address <span class="required-star">*</span></label>
                                <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" placeholder="your@email.com">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6 mb-3">
                                <label>Phone Number</label>
                                <input type="tel" name="phone" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>" placeholder="+254 700 000 000">
                            </div>
                            <div class="col-sm-6 mb-3">
                                <label>Subject</label>
                                <select name="subject">
                                    <option value="">Select subject…</option>
                                    <option <?= ($_POST['subject'] ?? '') === 'General Enquiry' ? 'selected' : '' ?>>General Enquiry</option>
                                    <option <?= ($_POST['subject'] ?? '') === 'Event Partnership' ? 'selected' : '' ?>>Event Partnership</option>
                                    <option <?= ($_POST['subject'] ?? '') === 'Technical Support' ? 'selected' : '' ?>>Technical Support</option>
                                    <option <?= ($_POST['subject'] ?? '') === 'Nominations & Voting' ? 'selected' : '' ?>>Nominations &amp; Voting</option>
                                    <option <?= ($_POST['subject'] ?? '') === 'Ticketing' ? 'selected' : '' ?>>Ticketing</option>
                                    <option <?= ($_POST['subject'] ?? '') === 'Other' ? 'selected' : '' ?>>Other</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label>Message <span class="required-star">*</span></label>
                            <textarea name="message" rows="6" placeholder="Tell us how we can help…" style="resize:vertical;"><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
                        </div>
                        <button type="submit" id="contactSubmitBtn" class="theme-btn btn-style-one">
                            <span class="btn-title"><i class="fas fa-paper-plane me-2"></i>Send Message</span>
                        </button>
                    </form>
                </div>
            </div>

            <div class="col-lg-5 wow fadeInRight">
                <div class="contact-info-box">
                    <h4>Get in Touch</h4>
                    <div class="ci-row">
                        <div class="ci-icon"><i class="fas fa-map-marker-alt"></i></div>
                        <div>
                            <div class="ci-label">Location</div>
                            <div class="ci-value">Nairobi, Kenya</div>
                        </div>
                    </div>
                    <div class="ci-row">
                        <div class="ci-icon"><i class="fas fa-envelope"></i></div>
                        <div>
                            <div class="ci-label">Email</div>
                            <div class="ci-value"><a href="mailto:hello@tuqio.com">hello@tuqio.com</a></div>
                        </div>
                    </div>
                    <div class="ci-row">
                        <div class="ci-icon"><i class="fas fa-phone"></i></div>
                        <div>
                            <div class="ci-label">Phone</div>
                            <div class="ci-value"><a href="tel:+254700000000">+254 700 000 000</a></div>
                        </div>
                    </div>
                    <div class="ci-row">
                        <div class="ci-icon"><i class="fas fa-clock"></i></div>
                        <div>
                            <div class="ci-label">Working Hours</div>
                            <div class="ci-value">Mon – Fri: 9am – 6pm</div>
                        </div>
                    </div>

                    <div class="ci-social">
                        <div class="ci-label">Follow Us</div>
                        <div class="ci-social-links">
                            <?php foreach ([['fab fa-facebook-f','#'],['fab fa-twitter','#'],['fab fa-instagram','#'],['fab fa-linkedin-in','#']] as [$icon,$href]): ?>
                            <a href="<?= $href ?>" target="_blank" rel="noopener">
                                <i class="<?= $icon ?>"></i>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
</div>
<div class="scroll-to-top scroll-to-target" data-target="html"><span class="fa fa-angle-up"></span></div>
<?php include 'includes/footer-links.php'; ?>

<!-- Toast notification -->
<div id="contactToast" style="
    position:fixed;bottom:28px;right:28px;z-index:9999;
    padding:16px 22px;border-radius:12px;
    box-shadow:0 8px 28px rgba(0,0,0,0.2);
    font-size:.92rem;max-width:340px;display:none;
    color:#fff;border-left:4px solid #10b981;
    background:#1e1548;line-height:1.5;
"></div>

<script>
(function () {
    var API_BASE = '<?= API_BASE ?>';

    function showToast(msg, isError) {
        var t = document.getElementById('contactToast');
        t.textContent = msg;
        t.style.borderLeftColor = isError ? '#ed1c24' : '#10b981';
        t.style.display = 'block';
        t.style.opacity = '1';
        clearTimeout(t._timer);
        t._timer = setTimeout(function () { t.style.display = 'none'; }, 5000);
    }

    document.getElementById('contactForm').addEventListener('submit', function (e) {
        e.preventDefault();
        var form = this;
        var btn  = document.getElementById('contactSubmitBtn');
        var name    = form.querySelector('[name=name]').value.trim();
        var email   = form.querySelector('[name=email]').value.trim();
        var message = form.querySelector('[name=message]').value.trim();

        if (!name || !email || !message) {
            showToast('Please fill in your name, email and message.', true);
            return;
        }

        btn.disabled = true;
        btn.querySelector('.btn-title').textContent = 'Sending…';

        var payload = {
            name:    name,
            email:   email,
            phone:   form.querySelector('[name=phone]').value.trim(),
            subject: form.querySelector('[name=subject]').value.trim(),
            message: message
        };

        fetch(API_BASE + '/api/public/contact', {
            method:  'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body:    JSON.stringify(payload)
        })
        .then(function (r) { return r.json(); })
        .then(function (res) {
            if (res.success) {
                showToast('Message sent! We\'ll get back to you shortly.', false);
                form.reset();
            } else {
                var msg = res.message || 'Could not send your message. Please try again.';
                showToast(msg, true);
            }
        })
        .catch(function () {
            showToast('Network error. Please check your connection and try again.', true);
        })
        .finally(function () {
            btn.disabled = false;
            btn.querySelector('.btn-title').innerHTML = '<i class="fas fa-paper-plane" style="margin-right:8px;"></i>Send Message';
        });
    });
})();
</script>
</body>
</html>
