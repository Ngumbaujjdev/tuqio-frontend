<?php
include 'config/config.php';
include 'libs/App.php';

$slug = trim($_GET['slug'] ?? '');
if (!$slug) { header('Location: ' . SITE_URL . '/blog'); exit; }

$resp = tuqio_api('/api/public/blog/' . urlencode($slug));
if (empty($resp['post'])) { header('Location: ' . SITE_URL . '/blog'); exit; }

$post    = $resp['post'];
$related = $resp['related'] ?? [];
$featImg = (!empty($post['featured_image']) && $post['featured_image'] !== 'null') ? $post['featured_image'] : null;

// Fetch all posts for sidebar widgets (recent posts + categories)
$allResp   = tuqio_api('/api/public/blog');
$allPosts  = $allResp['data'] ?? [];

// Recent posts (exclude current, max 4)
$recentPosts = array_filter($allPosts, fn($p) => $p['slug'] !== $post['slug']);
$recentPosts = array_slice(array_values($recentPosts), 0, 4);

// Unique categories with counts
$catMap = [];
foreach ($allPosts as $p) {
    if (!empty($p['category'])) {
        $cid = $p['category']['id'];
        if (!isset($catMap[$cid])) {
            $catMap[$cid] = ['name' => $p['category']['name'], 'count' => 0];
        }
        $catMap[$cid]['count']++;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">

<?php
$seoPostTitle = htmlspecialchars($post['title'] ?? 'Article');
$seoPostDesc  = htmlspecialchars(strip_tags($post['excerpt'] ?? mb_substr($post['body'] ?? '', 0, 155)));
$seoPostDesc  = mb_strimwidth($seoPostDesc, 0, 160, '...');
$seoPostImg   = !empty($featImg) ? API_STORAGE . $featImg : OG_IMAGE;
$seoPostSlug  = urlencode($post['slug'] ?? '');
$seoPostUrl   = 'https://tuqio.independentkenyawomenawards.com/blog-single.php?slug=' . $seoPostSlug;
$seoPostDate  = $post['published_at'] ?? $post['created_at'] ?? '';
?>

<!-- SEO -->
<title><?= $seoPostTitle ?> | Tuqio Hub</title>
<meta name="description" content="<?= $seoPostDesc ?>">
<meta name="keywords" content="<?= $seoPostTitle ?>, Tuqio Hub blog, Kenya events article, event news Kenya">
<meta name="author" content="<?= htmlspecialchars($post['author_name'] ?? 'Tuqio Hub') ?>">
<meta name="robots" content="index, follow">
<link rel="canonical" href="<?= $seoPostUrl ?>">

<!-- Schema.org microdata -->
<meta itemprop="name" content="<?= $seoPostTitle ?>">
<meta itemprop="description" content="<?= $seoPostDesc ?>">
<meta itemprop="image" content="<?= $seoPostImg ?>">

<!-- Open Graph -->
<meta property="og:title" content="<?= $seoPostTitle ?> | Tuqio Hub">
<meta property="og:type" content="article">
<meta property="og:image" content="<?= $seoPostImg ?>">
<meta property="og:image:type" content="image/jpeg">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:url" content="<?= $seoPostUrl ?>">
<meta property="og:description" content="<?= $seoPostDesc ?>">
<meta property="og:site_name" content="Tuqio Hub">
<?php if ($seoPostDate): ?><meta property="article:published_time" content="<?= $seoPostDate ?>"><?php endif; ?>

<!-- Twitter Card -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:site" content="@tuqiohub">
<meta name="twitter:title" content="<?= $seoPostTitle ?> | Tuqio Hub">
<meta name="twitter:description" content="<?= $seoPostDesc ?>">
<meta name="twitter:image" content="<?= $seoPostImg ?>">

<!-- Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-XXXXXXXXXX"></script>
<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','G-XXXXXXXXXX');</script>

<!-- JSON-LD: Organization -->
<script type="application/ld+json">
{"@context":"https://schema.org/","@type":"Organization","name":"Tuqio Hub","url":"https://tuqio.independentkenyawomenawards.com","contactPoint":{"@type":"ContactPoint","telephone":"+254757140682","email":"tuqio@independentkenyawomenawards.com","contactType":"customer support"},"sameAs":["https://www.instagram.com/tuqiohub","https://www.facebook.com/tuqiohub","https://twitter.com/tuqiohub"]}
</script>

<!-- JSON-LD: BreadcrumbList -->
<script type="application/ld+json">
{"@context":"https://schema.org","@type":"BreadcrumbList","itemListElement":[{"@type":"ListItem","position":1,"name":"Home","item":"https://tuqio.independentkenyawomenawards.com/"},{"@type":"ListItem","position":2,"name":"Blog","item":"https://tuqio.independentkenyawomenawards.com/blog.php"},{"@type":"ListItem","position":3,"name":"<?= addslashes($post['title'] ?? '') ?>","item":"<?= $seoPostUrl ?>"}]}
</script>

<!-- JSON-LD: Article -->
<script type="application/ld+json">
{"@context":"https://schema.org","@type":"Article","headline":"<?= addslashes($post['title'] ?? '') ?>","description":"<?= addslashes($seoPostDesc) ?>","image":"<?= $seoPostImg ?>","url":"<?= $seoPostUrl ?>","datePublished":"<?= $seoPostDate ?>","author":{"@type":"Person","name":"<?= addslashes($post['author_name'] ?? 'Tuqio Hub') ?>"},"publisher":{"@type":"Organization","name":"Tuqio Hub","url":"https://tuqio.independentkenyawomenawards.com"}}
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
.post-content { font-size:.97rem;line-height:1.9;color:#444; }
.post-content h2,.post-content h3 { color:#1e1548;font-weight:700;margin:28px 0 14px; }
.post-content img { max-width:100%;border-radius:8px;margin:20px 0; }
.post-content a { color:#ed1c24; }

/* Sidebar widgets */
.sidebar .sidebar-widget { margin-bottom:36px; }
.sidebar .sidebar-title {
    font-size:1rem;font-weight:700;color:#1e1548;text-transform:uppercase;
    letter-spacing:1.5px;padding-bottom:12px;border-bottom:2px solid #ed1c24;
    margin-bottom:20px;
}

/* Search */
.sidebar-widget.search-box .form-group { position:relative;margin:0; }
.sidebar-widget.search-box input[type="search"] {
    width:100%;border:1px solid #e0e0e0;border-radius:30px;
    padding:10px 48px 10px 20px;font-size:.88rem;outline:none;
    transition:border-color .2s;
}
.sidebar-widget.search-box input[type="search"]:focus { border-color:#ed1c24; }
.sidebar-widget.search-box button {
    position:absolute;right:6px;top:50%;transform:translateY(-50%);
    background:#ed1c24;border:none;border-radius:50%;width:34px;height:34px;
    color:#fff;cursor:pointer;font-size:.85rem;
}
.sidebar-widget.search-box button:hover { background:#c41820; }

/* Categories */
.blog-categories { list-style:none;padding:0;margin:0; }
.blog-categories li { border-bottom:1px solid #f0f0f0; }
.blog-categories li:last-child { border-bottom:none; }
.blog-categories li a {
    display:flex;justify-content:space-between;align-items:center;
    padding:9px 0;color:#555;text-decoration:none;font-size:.88rem;
    transition:color .2s;
}
.blog-categories li a:hover { color:#ed1c24; }
.blog-categories li a span {
    background:#f4f4f4;color:#888;font-size:.75rem;
    padding:2px 8px;border-radius:20px;
}
.blog-categories li a:hover span { background:#ed1c24;color:#fff; }

/* Author widget */
.author-block { background:#f8f8f8;border-radius:12px;padding:22px;text-align:center; }
.author-block .author-image {
    width:80px;height:80px;border-radius:50%;overflow:hidden;
    margin:0 auto 12px;border:3px solid #ed1c24;
}
.author-block .author-image img { width:100%;height:100%;object-fit:cover; }
.author-block .author-image.no-img {
    background:linear-gradient(135deg,#1e1548,#2d1f6b);
    display:flex;align-items:center;justify-content:center;
}
.author-block .author-image.no-img i { font-size:1.8rem;color:rgba(255,255,255,.7); }
.author-block h5 { font-size:.95rem;font-weight:700;color:#1e1548;margin-bottom:6px; }
.author-block p { font-size:.8rem;color:#888;margin-bottom:0; }

/* Recent posts */
.sidebar-widget .post { margin-bottom:16px;padding-bottom:16px;border-bottom:1px solid #f0f0f0; }
.sidebar-widget .post:last-child { border-bottom:none;margin-bottom:0;padding-bottom:0; }
.sidebar-widget .post .post-inner { display:flex;gap:12px;align-items:flex-start; }
.sidebar-widget .post .post-thumb {
    width:68px;height:54px;flex-shrink:0;border-radius:6px;overflow:hidden;
}
.sidebar-widget .post .post-thumb img { width:100%;height:100%;object-fit:cover; }
.sidebar-widget .post .post-thumb.no-img {
    background:linear-gradient(135deg,#1e1548,#2d1f6b);
    display:flex;align-items:center;justify-content:center;border-radius:6px;
}
.sidebar-widget .post .post-thumb.no-img i { font-size:1.1rem;color:rgba(255,255,255,.5); }
.sidebar-widget .post .post-info { font-size:.72rem;color:#999;margin-bottom:4px; }
.sidebar-widget .post .post-info i { color:#ed1c24;margin-right:3px; }
.sidebar-widget .post h6 { margin:0;font-size:.82rem;font-weight:600;line-height:1.35; }
.sidebar-widget .post h6 a { color:#1e1548;text-decoration:none; }
.sidebar-widget .post h6 a:hover { color:#ed1c24; }

/* Tags */
.popular-tags .widget-content { display:flex;flex-wrap:wrap;gap:8px; }
.popular-tags .widget-content a {
    background:#f4f4f4;color:#666;font-size:.77rem;padding:5px 12px;
    border-radius:20px;text-decoration:none;transition:background .2s,color .2s;
    border:1px solid #e8e8e8;
}
.popular-tags .widget-content a:hover { background:#ed1c24;color:#fff;border-color:#ed1c24; }

/* Share row */
.blog-meta-row { display:flex;flex-wrap:wrap;gap:12px;align-items:center;margin-bottom:24px;padding-bottom:16px;border-bottom:1px solid #eee; }
.blog-meta-item { font-size:.83rem;color:#888; }
.blog-meta-icon { color:#ed1c24;margin-right:5px; }
.blog-cat-pill { background:#ed1c24;color:#fff;font-size:.72rem;font-weight:700;padding:3px 12px;border-radius:20px;text-transform:uppercase;letter-spacing:.8px; }
.blog-share-row { display:flex;flex-wrap:wrap;align-items:center;gap:10px;margin-top:32px;padding-top:20px;border-top:1px solid #eee; }
.blog-share-label { font-size:.85rem;color:#888;font-weight:600; }
.share-btn { display:inline-flex;align-items:center;justify-content:center;width:36px;height:36px;border-radius:50%;color:#fff;font-size:.85rem;transition:transform .2s,opacity .2s; }
.share-btn:hover { transform:translateY(-2px);opacity:.85;color:#fff; }
.share-btn-fb { background:#3b5998; }
.share-btn-tw { background:#1da1f2; }
.share-btn-li { background:#0077b5; }
.share-btn-wa { background:#25d366; }
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

<section class="page-title" style="background-image:url(<?= $featImg ? htmlspecialchars($featImg) : SITE_URL . '/assets/slides/kenya-breadcrump.webp' ?>);">
    <div class="anim-icons full-width"><span class="icon icon-bull-eye"></span><span class="icon icon-dotted-circle"></span></div>
    <div class="auto-container">
        <div class="title-outer">
            <h1 class="title-sm"><?= htmlspecialchars($post['title']) ?></h1>
            <ul class="page-breadcrumb">
                <li><a href="<?= SITE_URL ?>">Home</a></li>
                <li><a href="<?= SITE_URL ?>/blog">Blog</a></li>
                <li><?= htmlspecialchars(mb_strimwidth($post['title'], 0, 40, '…')) ?></li>
            </ul>
        </div>
    </div>
</section>

<div class="sidebar-page-container sidebar-page-padded">
    <div class="auto-container">
        <div class="row clearfix">

            <!-- ─── Post Content ─────────────────────────────── -->
            <div class="content-side col-lg-8 col-md-12 col-sm-12">

                <!-- Meta -->
                <div class="blog-meta-row">
                    <?php if (!empty($post['published_at'])): ?>
                    <span class="blog-meta-item"><i class="fa fa-calendar-alt blog-meta-icon"></i><?= date('d M Y', strtotime($post['published_at'])) ?></span>
                    <?php endif; ?>
                    <?php if (!empty($post['author'])): ?>
                    <span class="blog-meta-item"><i class="fa fa-user blog-meta-icon"></i><?= htmlspecialchars($post['author']['name']) ?></span>
                    <?php endif; ?>
                    <?php if (!empty($post['category'])): ?>
                    <span class="blog-cat-pill"><?= htmlspecialchars($post['category']['name']) ?></span>
                    <?php endif; ?>
                </div>

                <!-- Featured Image -->
                <div class="post-featured-image mb-4">
                    <?php if ($featImg): ?>
                    <img src="<?= htmlspecialchars($featImg) ?>"
                         alt="<?= htmlspecialchars($post['title']) ?>"
                         style="width:100%;border-radius:10px;object-fit:cover;max-height:420px;"
                         onerror="this.outerHTML='<div style=\'width:100%;height:320px;border-radius:10px;background:linear-gradient(135deg,#1e1548 0%,#2d1f6b 60%,#ed1c24 100%);display:flex;flex-direction:column;align-items:center;justify-content:center;gap:12px;\'><i class=\'fas fa-newspaper\' style=\'font-size:3rem;color:rgba(255,255,255,.35);\'></i><span style=\'color:rgba(255,255,255,.5);font-size:.85rem;letter-spacing:1px;text-transform:uppercase;\'>Tuqio Hub</span></div>'">
                    <?php else: ?>
                    <div style="width:100%;height:320px;border-radius:10px;background:linear-gradient(135deg,#1e1548 0%,#2d1f6b 60%,#ed1c24 100%);display:flex;flex-direction:column;align-items:center;justify-content:center;gap:12px;">
                        <i class="fas fa-newspaper" style="font-size:3rem;color:rgba(255,255,255,.35);"></i>
                        <span style="color:rgba(255,255,255,.5);font-size:.85rem;letter-spacing:1px;text-transform:uppercase;">Tuqio Hub</span>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Content -->
                <div class="post-content">
                    <?= $post['content'] ?>
                </div>

                <!-- Share -->
                <div class="blog-share-row">
                    <span class="blog-share-label">Share:</span>
                    <?php $shareUrl = urlencode(SITE_URL . '/blog-single?slug=' . $post['slug']); ?>
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?= $shareUrl ?>" target="_blank" class="share-btn share-btn-fb" title="Share on Facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="https://twitter.com/intent/tweet?url=<?= $shareUrl ?>&text=<?= urlencode($post['title']) ?>" target="_blank" class="share-btn share-btn-tw" title="Share on Twitter">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?= $shareUrl ?>" target="_blank" class="share-btn share-btn-li" title="Share on LinkedIn">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                    <a href="https://wa.me/?text=<?= urlencode($post['title'] . ' ' . SITE_URL . '/blog-single?slug=' . $post['slug']) ?>" target="_blank" class="share-btn share-btn-wa" title="Share on WhatsApp">
                        <i class="fab fa-whatsapp"></i>
                    </a>
                </div>

            </div>

            <!-- ─── Sidebar ──────────────────────────────────── -->
            <div class="sidebar-side col-lg-4 col-md-12 col-sm-12">
                <aside class="sidebar padding-left">

                    <!-- Search Widget -->
                    <div class="sidebar-widget search-box">
                        <form method="get" action="<?= SITE_URL ?>/blog">
                            <div class="form-group">
                                <input type="search" name="search" placeholder="Search articles…"
                                    value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                                <button type="submit"><i class="flaticon-search"></i></button>
                            </div>
                        </form>
                    </div>

                    <!-- Categories Widget -->
                    <?php if (!empty($catMap)): ?>
                    <div class="sidebar-widget categories">
                        <h5 class="sidebar-title">Categories</h5>
                        <div class="widget-content">
                            <ul class="blog-categories">
                                <?php foreach ($catMap as $cat): ?>
                                <li>
                                    <a href="<?= SITE_URL ?>/blog?category=<?= urlencode($cat['name']) ?>">
                                        <?= htmlspecialchars($cat['name']) ?>
                                        <span><?= $cat['count'] ?></span>
                                    </a>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Author Widget -->
                    <?php if (!empty($post['author'])): ?>
                    <div class="sidebar-widget author-widget">
                        <h5 class="sidebar-title">About the Author</h5>
                        <div class="widget-content">
                            <div class="author-block">
                                <div class="author-image no-img">
                                    <i class="fas fa-user"></i>
                                </div>
                                <h5><?= htmlspecialchars($post['author']['name']) ?></h5>
                                <?php if (!empty($post['category'])): ?>
                                <p>Writing about <?= htmlspecialchars($post['category']['name']) ?> and more.</p>
                                <?php else: ?>
                                <p>Contributor at Tuqio Hub.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Recent Posts Widget -->
                    <?php if (!empty($recentPosts)): ?>
                    <div class="sidebar-widget popular-posts">
                        <h5 class="sidebar-title">Recent Posts</h5>
                        <div class="widget-content">
                            <?php foreach ($recentPosts as $rp): ?>
                            <article class="post">
                                <div class="post-inner">
                                    <?php if (!empty($rp['featured_image']) && $rp['featured_image'] !== 'null'): ?>
                                    <figure class="post-thumb">
                                        <a href="<?= SITE_URL ?>/blog-single?slug=<?= urlencode($rp['slug']) ?>">
                                            <img src="<?= htmlspecialchars($rp['featured_image']) ?>"
                                                 alt="<?= htmlspecialchars($rp['title']) ?>"
                                                 onerror="this.parentElement.className='post-thumb no-img';this.parentElement.innerHTML='<i class=\'fas fa-newspaper\'></i>'">
                                        </a>
                                    </figure>
                                    <?php else: ?>
                                    <div class="post-thumb no-img"><i class="fas fa-newspaper"></i></div>
                                    <?php endif; ?>
                                    <div>
                                        <?php if (!empty($rp['published_at'])): ?>
                                        <div class="post-info">
                                            <i class="fa fa-calendar-alt"></i><?= date('d M Y', strtotime($rp['published_at'])) ?>
                                        </div>
                                        <?php endif; ?>
                                        <h6><a href="<?= SITE_URL ?>/blog-single?slug=<?= urlencode($rp['slug']) ?>"><?= htmlspecialchars($rp['title']) ?></a></h6>
                                    </div>
                                </div>
                            </article>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Tags Widget -->
                    <?php
                    // Gather tags from categories + extract from titles
                    $tags = [];
                    foreach ($catMap as $cat) { $tags[] = $cat['name']; }
                    $extraTags = ['Awards', 'Programs', 'Community', 'Events', 'Kenya', 'Youth', 'Leadership', 'Innovation'];
                    $tags = array_unique(array_merge($tags, $extraTags));
                    ?>
                    <div class="sidebar-widget popular-tags">
                        <h5 class="sidebar-title">Tags</h5>
                        <div class="widget-content">
                            <?php foreach ($tags as $tag): ?>
                            <a href="<?= SITE_URL ?>/blog?search=<?= urlencode($tag) ?>"><?= htmlspecialchars($tag) ?></a>
                            <?php endforeach; ?>
                        </div>
                    </div>

                </aside>
            </div>

        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
</div>
<div class="scroll-to-top scroll-to-target" data-target="html"><span class="fa fa-angle-up"></span></div>
<?php include 'includes/footer-links.php'; ?>
</body>
</html>
