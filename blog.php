<?php
include 'config/config.php';
include 'libs/App.php';

$page      = (int)($_GET['page'] ?? 1);
$search    = trim($_GET['search'] ?? '');
$catFilter = trim($_GET['category'] ?? '');

$queryParams = [
    'page'     => $page,
    'search'   => $search,
    'category' => $catFilter
];
$queryString = http_build_query(array_filter($queryParams));

$resp     = tuqio_api('/api/public/blog?' . $queryString);
$posts       = $resp['data'] ?? [];
$currentPage = $resp['current_page'] ?? 1;
$lastPage    = $resp['last_page'] ?? 1;
$totalItems  = $resp['total'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">

<!-- SEO -->
<title>Articles &amp; News | Tuqio Hub</title>
<meta name="description" content="Stay up to date with the latest news, articles, and event updates from Tuqio Hub — Kenya's premier event management platform.">
<meta name="keywords" content="Tuqio Hub blog, Kenya events news, awards articles Kenya, event updates Nairobi, Tuqio Hub articles">
<meta name="author" content="Tuqio Hub">
<meta name="robots" content="index, follow">
<link rel="canonical" href="https://tuqio.independentkenyawomenawards.com/blog.php">

<!-- Schema.org microdata -->
<meta itemprop="name" content="Articles & News | Tuqio Hub">
<meta itemprop="description" content="Latest news, articles, and event updates from Tuqio Hub.">
<meta itemprop="image" content="<?= OG_IMAGE ?>">

<!-- Open Graph -->
<meta property="og:title" content="Articles &amp; News | Tuqio Hub">
<meta property="og:type" content="website">
<meta property="og:image" content="<?= OG_IMAGE ?>">
<meta property="og:image:type" content="image/webp">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:url" content="https://tuqio.independentkenyawomenawards.com/blog.php">
<meta property="og:description" content="Latest news, articles, and event updates from Tuqio Hub.">
<meta property="og:site_name" content="Tuqio Hub">

<!-- Twitter Card -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:site" content="@tuqiohub">
<meta name="twitter:title" content="Articles &amp; News | Tuqio Hub">
<meta name="twitter:description" content="Latest news, articles, and event updates from Tuqio Hub.">
<meta name="twitter:image" content="<?= OG_IMAGE ?>">

<!-- Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-XXXXXXXXXX"></script>
<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','G-XXXXXXXXXX');</script>

<!-- JSON-LD: Organization -->
<script type="application/ld+json">
{"@context":"https://schema.org/","@type":"Organization","name":"Tuqio Hub","url":"https://tuqio.independentkenyawomenawards.com","contactPoint":{"@type":"ContactPoint","telephone":"+254757140682","email":"tuqio@independentkenyawomenawards.com","contactType":"customer support"},"sameAs":["https://www.instagram.com/tuqiohub","https://www.facebook.com/tuqiohub","https://twitter.com/tuqiohub"]}
</script>

<!-- JSON-LD: BreadcrumbList -->
<script type="application/ld+json">
{"@context":"https://schema.org","@type":"BreadcrumbList","itemListElement":[{"@type":"ListItem","position":1,"name":"Home","item":"https://tuqio.independentkenyawomenawards.com/"},{"@type":"ListItem","position":2,"name":"Blog","item":"https://tuqio.independentkenyawomenawards.com/blog.php"}]}
</script>

<!-- JSON-LD: Blog -->
<script type="application/ld+json">
{"@context":"https://schema.org","@type":"Blog","name":"Articles & News | Tuqio Hub","url":"https://tuqio.independentkenyawomenawards.com/blog.php","description":"Latest news, articles, and updates from Tuqio Hub.","publisher":{"@type":"Organization","name":"Tuqio Hub","url":"https://tuqio.independentkenyawomenawards.com"}}
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
.blog-card { background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.06);height:100%;transition:transform .3s,box-shadow .3s; }
.blog-card:hover { transform:translateY(-5px);box-shadow:0 14px 36px rgba(0,0,0,0.11); }
.blog-card .card-thumb { height:200px;overflow:hidden;position:relative; }
.blog-card .card-thumb img { width:100%;height:100%;object-fit:cover;transition:transform .4s; }
.blog-card:hover .card-thumb img { transform:scale(1.05); }
.blog-card .cat-tag {
    position:absolute;top:14px;left:14px;background:#ed1c24;color:#fff;
    font-size:.7rem;font-weight:700;padding:3px 10px;border-radius:20px;
    text-transform:uppercase;letter-spacing:1px;
}
.blog-card .card-body { padding:22px; }
.blog-card .post-meta { display:flex;gap:14px;flex-wrap:wrap;margin-bottom:10px; }
.blog-card .post-meta span { font-size:.78rem;color:#999; }
.blog-card .post-meta i { color:#ed1c24;margin-right:3px; }
.blog-card h4 { font-size:1.05rem;font-weight:700;color:#1e1548;margin-bottom:10px;line-height:1.35; }
.blog-card h4 a { color:inherit;text-decoration:none; }
.blog-card h4 a:hover { color:#ed1c24; }
.blog-card .excerpt { font-size:.85rem;color:#777;line-height:1.65;margin-bottom:16px; }
.placeholder-thumb { height:200px;background:linear-gradient(135deg,#1e1548,#2d1f6b);display:flex;align-items:center;justify-content:center; }
.placeholder-thumb i { font-size:2.5rem;color:rgba(255,255,255,.3); }
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
            <h1>Blog &amp; News</h1>
            <ul class="page-breadcrumb">
                <li><a href="<?= SITE_URL ?>">Home</a></li>
                <li>Blog</li>
            </ul>
        </div>
    </div>
</section>

<section class="section-light">
    <div class="auto-container">

        <?php if ($search !== '' || $catFilter !== ''): ?>
        <div class="filter-notice mb-4 d-flex align-items-center gap-3 flex-wrap">
            <span class="text-muted" style="font-size:.9rem;">
                <?php if ($catFilter !== ''): ?>
                Showing articles in <strong><?= htmlspecialchars($catFilter) ?></strong>
                <?php elseif ($search !== ''): ?>
                Search results for "<strong><?= htmlspecialchars($search) ?></strong>"
                <?php endif; ?>
                — <?= $totalItems ?> found
            </span>
            <a href="<?= SITE_URL ?>/blog" class="badge bg-secondary text-decoration-none" style="font-size:.78rem;">
                <i class="fas fa-times me-1"></i>Clear filter
            </a>
        </div>
        <?php endif; ?>

        <?php if (empty($posts)): ?>
        <div class="text-center empty-state">
            <i class="fas fa-newspaper empty-icon"></i>
            <h4>No Articles Yet</h4>
            <p class="text-muted">Check back soon for news and updates.</p>
        </div>
        <?php else: ?>
        <div class="row">
            <?php foreach ($posts as $post): ?>
            <div class="col-lg-4 col-md-6 mb-4 wow fadeInUp">
                <div class="blog-card">
                    <div class="card-thumb">
                        <?php if (!empty($post['featured_image']) && $post['featured_image'] !== 'null'): ?>
                        <img src="<?= htmlspecialchars($post['featured_image']) ?>"
                             alt="<?= htmlspecialchars($post['title']) ?>"
                             onerror="this.parentElement.innerHTML='<div class=\'placeholder-thumb\'><i class=\'fas fa-newspaper\'></i></div>'">
                        <?php else: ?>
                        <div class="placeholder-thumb"><i class="fas fa-newspaper"></i></div>
                        <?php endif; ?>
                        <?php if (!empty($post['category'])): ?>
                        <span class="cat-tag"><?= htmlspecialchars($post['category']['name']) ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <div class="post-meta">
                            <?php if (!empty($post['published_at'])): ?>
                            <span><i class="fa fa-calendar-alt"></i><?= date('d M Y', strtotime($post['published_at'])) ?></span>
                            <?php endif; ?>
                            <?php if (!empty($post['author'])): ?>
                            <span><i class="fa fa-user"></i><?= htmlspecialchars($post['author']['name']) ?></span>
                            <?php endif; ?>
                        </div>
                        <h4><a href="<?= SITE_URL ?>/blog-single?slug=<?= urlencode($post['slug']) ?>"><?= htmlspecialchars($post['title']) ?></a></h4>
                        <?php if (!empty($post['excerpt'])): ?>
                        <p class="excerpt"><?= htmlspecialchars(mb_strimwidth($post['excerpt'], 0, 110, '…')) ?></p>
                        <?php endif; ?>
                        <a href="<?= SITE_URL ?>/blog-single?slug=<?= urlencode($post['slug']) ?>" class="blog-readmore">
                            Read More <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <?php if ($lastPage > 1): ?>
        <div class="row mt-5">
            <div class="col-12 text-center">
                <nav aria-label="Blog Pagination">
                    <ul class="pagination justify-content-center">
                        <?php
                        $baseQuery = $_GET;
                        // Previous Button
                        if ($currentPage > 1) {
                            $baseQuery['page'] = $currentPage - 1;
                            $prevUrl = SITE_URL . '/blog?' . http_build_query(array_filter($baseQuery));
                            echo '<li class="page-item"><a class="page-link" href="' . htmlspecialchars($prevUrl) . '">Previous</a></li>';
                        } else {
                            echo '<li class="page-item disabled"><span class="page-link">Previous</span></li>';
                        }

                        // Page Numbers
                        for ($i = 1; $i <= $lastPage; $i++) {
                            $baseQuery['page'] = $i;
                            $pageUrl = SITE_URL . '/blog?' . http_build_query(array_filter($baseQuery));
                            $activeClass = ($i === $currentPage) ? 'active' : '';
                            echo '<li class="page-item ' . $activeClass . '"><a class="page-link" href="' . htmlspecialchars($pageUrl) . '">' . $i . '</a></li>';
                        }

                        // Next Button
                        if ($currentPage < $lastPage) {
                            $baseQuery['page'] = $currentPage + 1;
                            $nextUrl = SITE_URL . '/blog?' . http_build_query(array_filter($baseQuery));
                            echo '<li class="page-item"><a class="page-link" href="' . htmlspecialchars($nextUrl) . '">Next</a></li>';
                        } else {
                            echo '<li class="page-item disabled"><span class="page-link">Next</span></li>';
                        }
                        ?>
                    </ul>
                </nav>
            </div>
        </div>
        <?php endif; ?>

        <?php endif; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
</div>
<div class="scroll-to-top scroll-to-target" data-target="html"><span class="fa fa-angle-up"></span></div>
<?php include 'includes/footer-links.php'; ?>
</body>
</html>
