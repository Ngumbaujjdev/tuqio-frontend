<?php
include 'config/config.php';
include 'libs/App.php';

$filterSlug = trim($_GET['event'] ?? '');
$apiPath     = '/api/public/gallery' . ($filterSlug ? '?event=' . urlencode($filterSlug) : '');
$resp        = tuqio_api($apiPath);
$photos      = $resp['photos'] ?? [];
$events      = $resp['events'] ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">

<!-- SEO -->
<title>Photo Gallery | Tuqio Hub</title>
<meta name="description" content="Browse photos and highlights from Tuqio Hub events across Kenya — awards ceremonies, conferences, summits, and community moments.">
<meta name="keywords" content="event photos Kenya, Tuqio Hub gallery, awards ceremony photos, Kenya events highlights, event photography Nairobi">
<meta name="author" content="Tuqio Hub">
<meta name="robots" content="index, follow">
<link rel="canonical" href="https://tuqiohub.africa/gallery.php">

<!-- Schema.org microdata -->
<meta itemprop="name" content="Photo Gallery | Tuqio Hub">
<meta itemprop="description" content="Browse photos and highlights from Tuqio Hub events across Kenya.">
<meta itemprop="image" content="<?= OG_IMAGE ?>">

<!-- Open Graph -->
<meta property="og:title" content="Photo Gallery | Tuqio Hub">
<meta property="og:type" content="website">
<meta property="og:image" content="<?= OG_IMAGE ?>">
<meta property="og:image:type" content="image/webp">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:url" content="https://tuqiohub.africa/gallery.php">
<meta property="og:description" content="Browse photos and highlights from Tuqio Hub events across Kenya.">
<meta property="og:site_name" content="Tuqio Hub">

<!-- Twitter Card -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:site" content="@tuqiohub">
<meta name="twitter:title" content="Photo Gallery | Tuqio Hub">
<meta name="twitter:description" content="Browse photos and highlights from Tuqio Hub events across Kenya.">
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
{"@context":"https://schema.org","@type":"BreadcrumbList","itemListElement":[{"@type":"ListItem","position":1,"name":"Home","item":"https://tuqiohub.africa/"},{"@type":"ListItem","position":2,"name":"Gallery","item":"https://tuqiohub.africa/gallery.php"}]}
</script>

<!-- JSON-LD: WebPage -->
<script type="application/ld+json">
{"@context":"https://schema.org","@type":"WebPage","name":"Photo Gallery | Tuqio Hub","url":"https://tuqiohub.africa/gallery.php","description":"Browse photos and highlights from Tuqio Hub events."}
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
/* ── Filter buttons ── */
.gallery-filters {
    display: flex; flex-wrap: wrap; gap: 8px;
    margin-bottom: 36px; justify-content: center;
}
.gf-btn {
    padding: 9px 22px; border-radius: 6px;
    font-size: .84rem; font-weight: 700;
    border: 2px solid #eee; background: #fff;
    color: #555; cursor: pointer; text-decoration: none;
    transition: all .2s; letter-spacing: .3px;
}
.gf-btn:hover, .gf-btn.active {
    background: #ed1c24; border-color: #ed1c24;
    color: #fff; text-decoration: none;
}

/* ── Isotope grid ── */
.gallery-items .gallery-block {
    margin-bottom: 20px;
    transition: opacity .35s ease, transform .35s ease;
}
.gallery-items .gallery-block.hidden-item {
    opacity: 0; pointer-events: none;
    transform: scale(0.95);
}

/* ── Override gallery-block image height for uniform grid ── */
.gallery-items .gallery-block .image-box { height: 240px; }
.gallery-items .gallery-block .image     { height: 100%; }
.gallery-items .gallery-block .image img { height: 100%; object-fit: cover; }
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
            <h1>Photo Gallery</h1>
            <ul class="page-breadcrumb">
                <li><a href="<?= SITE_URL ?>">Home</a></li>
                <li>Gallery</li>
            </ul>
        </div>
    </div>
</section>

<section class="gallery-section">
    <div class="auto-container">

        <?php if (!empty($photos)): ?>

        <!-- Filter buttons -->
        <?php if (count($events) > 1): ?>
        <div class="gallery-filters">
            <a href="<?= SITE_URL ?>/gallery" class="gf-btn <?= !$filterSlug ? 'active' : '' ?>" data-filter="*">
                All (<?= count($photos) ?>)
            </a>
            <?php foreach ($events as $ev): ?>
            <a href="<?= SITE_URL ?>/gallery?event=<?= urlencode($ev['slug']) ?>"
               class="gf-btn <?= $filterSlug === $ev['slug'] ? 'active' : '' ?>"
               data-filter=".ev-<?= preg_replace('/[^a-z0-9]/', '-', strtolower($ev['slug'])) ?>">
                <?= htmlspecialchars($ev['name']) ?>
            </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Gallery grid -->
        <div class="row gallery-items">
            <?php foreach ($photos as $i => $photo):
                $filterClass = 'ev-' . preg_replace('/[^a-z0-9]/', '-', strtolower($photo['event_slug'] ?? ''));
            ?>
            <div class="gallery-block col-lg-3 col-md-4 col-sm-6 wow fadeIn <?= htmlspecialchars($filterClass) ?>" data-wow-delay="<?= ($i % 4) * 100 ?>ms">
                <div class="image-box">
                    <figure class="image">
                        <a href="<?= htmlspecialchars($photo['photo']) ?>"
                           data-fancybox="gallery"
                           data-caption="<?= htmlspecialchars(($photo['title'] ?? '') . ($photo['event_name'] ? ' — ' . $photo['event_name'] : '')) ?>">
                            <img src="<?= htmlspecialchars($photo['photo']) ?>"
                                 alt="<?= htmlspecialchars($photo['alt'] ?? $photo['title'] ?? '') ?>"
                                 onerror="this.closest('.gallery-block').style.display='none'">
                        </a>
                    </figure>
                    <div class="overlay-box">
                        <div class="icon"><span class="flaticon-zoom-1"></span></div>
                        <?php if (!empty($photo['title'])): ?>
                        <h3><a href="<?= htmlspecialchars($photo['photo']) ?>"
                               data-fancybox="gallery"><?= htmlspecialchars($photo['title']) ?></a></h3>
                        <?php endif; ?>
                        <?php if (!empty($photo['event_name'])): ?>
                        <div class="text"><?= htmlspecialchars($photo['event_name']) ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <?php else: ?>
        <div class="text-center" style="padding:80px 0;">
            <i class="fas fa-images" style="font-size:3rem;color:#ddd;display:block;margin-bottom:20px;"></i>
            <h4 style="color:#1e1548;font-weight:700;margin-bottom:10px;">No Photos Yet</h4>
            <p style="color:#999;margin-bottom:28px;">Event photos will appear here after each event.</p>
            <a href="<?= SITE_URL ?>/events" class="theme-btn btn-style-one">
                <span class="btn-title">Browse Events</span>
            </a>
        </div>
        <?php endif; ?>

    </div>
</section>

<?php include 'includes/footer.php'; ?>
</div>
<div class="scroll-to-top scroll-to-target" data-target="html"><span class="fa fa-angle-up"></span></div>
<?php include 'includes/footer-links.php'; ?>

<script>
/* ── FancyBox gallery ── */
$(function () {
    $('[data-fancybox="gallery"]').fancybox({
        buttons: ['slideShow', 'fullScreen', 'thumbs', 'close']
    });
});

/* ── Client-side filter (no page reload when JS is available) ── */
(function () {
    var allPhotos = document.querySelectorAll('.gallery-items .gallery-block');

    document.querySelectorAll('.gf-btn[data-filter]').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            var filter = this.getAttribute('data-filter');

            /* Update active state */
            document.querySelectorAll('.gf-btn').forEach(function (b) { b.classList.remove('active'); });
            this.classList.add('active');

            /* Show/hide items */
            allPhotos.forEach(function (item) {
                if (filter === '*' || item.classList.contains(filter.replace('.', ''))) {
                    item.classList.remove('hidden-item');
                } else {
                    item.classList.add('hidden-item');
                }
            });
        });
    });
})();
</script>
</body>
</html>
