<?php
include 'config/config.php';
include 'libs/App.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">

<!-- SEO -->
<title>Pricing | Tuqio Hub — Transparent Event Platform Fees</title>
<meta name="description" content="Simple, transparent pricing for Tuqio Hub. Pay only when you earn — 5% on ticketing and from 20% on voting. No setup fees, no monthly subscription.">
<meta name="keywords" content="Tuqio Hub pricing, event ticketing fees Kenya, voting platform pricing, event management cost Kenya, affordable ticketing platform">
<meta name="author" content="Tuqio Hub">
<meta name="robots" content="index, follow">
<link rel="canonical" href="https://tuqiohub.africa/pricing">

<!-- Schema.org microdata -->
<meta itemprop="name" content="Pricing | Tuqio Hub">
<meta itemprop="description" content="Simple pay-as-you-earn pricing for events, ticketing, and voting.">
<meta itemprop="image" content="<?= OG_IMAGE ?>">

<!-- Open Graph -->
<meta property="og:title" content="Pricing | Tuqio Hub — Transparent Event Platform Fees">
<meta property="og:type" content="website">
<meta property="og:image" content="<?= OG_IMAGE ?>">
<meta property="og:image:type" content="image/webp">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:url" content="https://tuqiohub.africa/pricing">
<meta property="og:description" content="Simple, transparent pricing for Tuqio Hub. Pay only when you earn — 5% on ticketing and from 20% on voting.">
<meta property="og:site_name" content="Tuqio Hub">

<!-- Twitter Card -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:site" content="@tuqiohub">
<meta name="twitter:title" content="Pricing | Tuqio Hub — Transparent Event Platform Fees">
<meta name="twitter:description" content="Simple, transparent pricing for Tuqio Hub. Pay only when you earn — 5% on ticketing and from 20% on voting.">
<meta name="twitter:image" content="<?= OG_IMAGE ?>">

<!-- Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-XXXXXXXXXX"></script>
<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','G-XXXXXXXXXX');</script>

<!-- JSON-LD: Organization -->
<script type="application/ld+json">
{"@context":"https://schema.org/","@type":"Organization","name":"Tuqio Hub","url":"https://tuqiohub.africa","description":"Kenya's premier event management and awards platform.","contactPoint":{"@type":"ContactPoint","telephone":"+254757140682","email":"info@tuqiohub.africa","contactType":"customer support"},"sameAs":["https://www.facebook.com/share/p/1DJyLwtvqf/","https://www.instagram.com/p/DV0RJ11ii-7/?igsh=MXNiemxwbXdzMzJ6aw==","https://twitter.com/tuqiohub","https://www.tiktok.com/@tuqiohubke"]}
</script>

<!-- JSON-LD: BreadcrumbList -->
<script type="application/ld+json">
{"@context":"https://schema.org","@type":"BreadcrumbList","itemListElement":[{"@type":"ListItem","position":1,"name":"Home","item":"https://tuqiohub.africa/"},{"@type":"ListItem","position":2,"name":"About","item":"https://tuqiohub.africa/about"},{"@type":"ListItem","position":3,"name":"Pricing","item":"https://tuqiohub.africa/pricing"}]}
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
/* ── Pricing page styles ─────────────────────────────────── */
.pricing-section { padding: 80px 0 60px; background: #f9fafb; }

.pricing-intro {
    text-align: center;
    max-width: 640px;
    margin: 0 auto 60px;
}
.pricing-intro .badge-pill {
    display: inline-block;
    background: rgba(237,28,36,0.1);
    color: #ed1c24;
    font-size: .72rem;
    font-weight: 700;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    padding: 5px 16px;
    border-radius: 20px;
    margin-bottom: 14px;
}
.pricing-intro h2 {
    font-size: 2rem;
    font-weight: 800;
    color: #1e1548;
    line-height: 1.25;
    margin-bottom: 14px;
}
.pricing-intro p {
    font-size: 1rem;
    color: #555;
    line-height: 1.7;
}

/* Cards */
.plan-card {
    background: #fff;
    border-radius: 18px;
    overflow: hidden;
    box-shadow: 0 6px 32px rgba(0,0,0,0.07);
    transition: transform .25s, box-shadow .25s;
    height: 100%;
    display: flex;
    flex-direction: column;
}
.plan-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 16px 48px rgba(0,0,0,0.12);
}
.plan-card.featured {
    border: 2px solid #ed1c24;
    position: relative;
}
.plan-card .plan-badge {
    position: absolute;
    top: -1px;
    right: 24px;
    background: #ed1c24;
    color: #fff;
    font-size: .68rem;
    font-weight: 700;
    letter-spacing: 1px;
    text-transform: uppercase;
    padding: 4px 14px;
    border-radius: 0 0 8px 8px;
}

.plan-header {
    padding: 36px 32px 28px;
    border-bottom: 1px solid #f0f0f0;
}
.plan-icon {
    width: 52px;
    height: 52px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.3rem;
    margin-bottom: 18px;
}
.plan-icon.navy { background: rgba(30,21,72,0.1); color: #1e1548; }
.plan-icon.red  { background: rgba(237,28,36,0.1); color: #ed1c24; }

.plan-name {
    font-size: .72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    color: #aaa;
    margin-bottom: 6px;
}
.plan-title {
    font-size: 1.3rem;
    font-weight: 800;
    color: #1e1548;
    margin-bottom: 8px;
}
.plan-price {
    display: flex;
    align-items: baseline;
    gap: 4px;
    margin-bottom: 8px;
}
.plan-price .pct {
    font-size: 2.6rem;
    font-weight: 900;
    color: #ed1c24;
    line-height: 1;
}
.plan-price .suffix {
    font-size: .85rem;
    color: #888;
}
.plan-desc {
    font-size: .88rem;
    color: #666;
    line-height: 1.6;
}

.plan-body {
    padding: 28px 32px;
    flex: 1;
    display: flex;
    flex-direction: column;
}
.plan-features {
    list-style: none;
    padding: 0;
    margin: 0 0 28px;
    flex: 1;
}
.plan-features li {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    font-size: .9rem;
    color: #444;
    margin-bottom: 12px;
    line-height: 1.5;
}
.plan-features li .chk {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: rgba(237,28,36,0.1);
    color: #ed1c24;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: .65rem;
    margin-top: 2px;
}
.plan-features li .chk.navy-chk {
    background: rgba(30,21,72,0.1);
    color: #1e1548;
}

/* Voting tiers */
.voting-tiers {
    background: #f9fafb;
    border-radius: 10px;
    padding: 16px 18px;
    margin-bottom: 24px;
}
.voting-tiers .tier-title {
    font-size: .72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: #aaa;
    margin-bottom: 10px;
}
.voting-tiers table {
    width: 100%;
    font-size: .85rem;
    border-collapse: collapse;
}
.voting-tiers td {
    padding: 6px 4px;
    color: #444;
    border-bottom: 1px solid #eee;
}
.voting-tiers tr:last-child td { border-bottom: none; }
.voting-tiers td:last-child { text-align: right; font-weight: 700; color: #1e1548; }

/* Enterprise banner */
.enterprise-banner {
    background: linear-gradient(135deg, #1e1548 0%, #2d1f6b 100%);
    border-radius: 18px;
    padding: 48px 40px;
    color: #fff;
    margin-top: 56px;
    position: relative;
    overflow: hidden;
}
.enterprise-banner::before {
    content: '';
    position: absolute;
    top: -40px;
    right: -40px;
    width: 220px;
    height: 220px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(237,28,36,0.2) 0%, transparent 70%);
}
.enterprise-banner::after {
    content: '';
    position: absolute;
    bottom: -50px;
    left: 20%;
    width: 160px;
    height: 160px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(255,255,255,0.06) 0%, transparent 70%);
}
.enterprise-banner h3 { font-size: 1.6rem; font-weight: 800; margin-bottom: 10px; position: relative; z-index: 1; }
.enterprise-banner p  { font-size: .95rem; color: rgba(255,255,255,0.75); max-width: 520px; line-height: 1.7; position: relative; z-index: 1; margin-bottom: 0; }
.enterprise-banner .ent-actions { display: flex; gap: 14px; flex-wrap: wrap; margin-top: 28px; position: relative; z-index: 1; }
.enterprise-banner .stat-col {
    text-align: right;
    position: relative;
    z-index: 1;
}
.enterprise-banner .stat-item { margin-bottom: 20px; }
.enterprise-banner .stat-item .stat-n { font-size: 2rem; font-weight: 900; color: #ed1c24; line-height: 1; }
.enterprise-banner .stat-item .stat-l { font-size: .8rem; color: rgba(255,255,255,0.6); margin-top: 2px; }

/* How it works */
.how-section { padding: 72px 0 60px; }
.how-section .sec-header { text-align: center; margin-bottom: 50px; }
.how-section .sec-header h2 { font-size: 1.75rem; font-weight: 800; color: #1e1548; }
.how-section .sec-header p { color: #666; font-size: .95rem; }

.step-card {
    text-align: center;
    padding: 32px 20px;
}
.step-num {
    width: 56px;
    height: 56px;
    border-radius: 50%;
    background: linear-gradient(135deg, #ed1c24, #c41820);
    color: #fff;
    font-size: 1.2rem;
    font-weight: 900;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 18px;
    box-shadow: 0 6px 18px rgba(237,28,36,0.3);
}
.step-card h5 { font-size: 1rem; font-weight: 700; color: #1e1548; margin-bottom: 8px; }
.step-card p  { font-size: .88rem; color: #666; line-height: 1.6; }

/* FAQ mini */
.pricing-faq { padding: 60px 0; background: #f9fafb; }
.pricing-faq .sec-header { text-align: center; margin-bottom: 40px; }
.pricing-faq .sec-header h2 { font-size: 1.6rem; font-weight: 800; color: #1e1548; }

.pfaq-item {
    background: #fff;
    border-radius: 12px;
    margin-bottom: 12px;
    border: 1px solid #eee;
    overflow: hidden;
}
.pfaq-q {
    padding: 18px 22px;
    font-size: .92rem;
    font-weight: 600;
    color: #1e1548;
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
    transition: background .2s;
}
.pfaq-q:hover { background: #fafafa; }
.pfaq-q .pfaq-icon { color: #ed1c24; font-size: .8rem; transition: transform .25s; }
.pfaq-q.open .pfaq-icon { transform: rotate(180deg); }
.pfaq-a {
    padding: 0 22px;
    max-height: 0;
    overflow: hidden;
    transition: max-height .3s ease, padding .3s;
    font-size: .88rem;
    color: #666;
    line-height: 1.7;
}
.pfaq-a.open { max-height: 200px; padding: 0 22px 18px; }

/* CTA bottom */
.pricing-cta {
    padding: 72px 0;
    background: linear-gradient(135deg, #15102e 0%, #1e1548 50%, #2d1f6b 100%);
    text-align: center;
    color: #fff;
}
.pricing-cta h2 { font-size: 1.9rem; font-weight: 800; margin-bottom: 12px; }
.pricing-cta p  { color: rgba(255,255,255,0.72); font-size: 1rem; max-width: 520px; margin: 0 auto 32px; line-height: 1.7; }
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
            <h1>Pricing</h1>
            <ul class="page-breadcrumb">
                <li><a href="<?= SITE_URL ?>">Home</a></li>
                <li><a href="<?= SITE_URL ?>/about">About</a></li>
                <li>Pricing</li>
            </ul>
        </div>
    </div>
</section>

<!-- ─── Plans ────────────────────────────────────────────── -->
<section class="pricing-section">
    <div class="auto-container">

        <div class="pricing-intro">
            <div class="badge-pill">Simple Pricing</div>
            <h2>Pay Only When You Earn</h2>
            <p>No setup fees. No monthly subscriptions. We charge a small percentage of revenue collected — so our success is tied directly to yours.</p>
        </div>

        <div class="row justify-content-center">

            <!-- Ticketing Plan -->
            <div class="col-lg-5 col-md-10 mb-4 wow fadeInLeft">
                <div class="plan-card">
                    <div class="plan-header">
                        <div class="plan-icon navy"><i class="fas fa-ticket-alt"></i></div>
                        <div class="plan-name">Ticketing</div>
                        <div class="plan-title">Event Tickets</div>
                        <div class="plan-price">
                            <span class="pct">5%</span>
                            <span class="suffix">of ticket revenue</span>
                        </div>
                        <p class="plan-desc">Sell tickets for conferences, galas, summits, and any in-person or virtual event. We handle payments and deliver tickets automatically.</p>
                    </div>
                    <div class="plan-body">
                        <ul class="plan-features">
                            <li><span class="chk navy-chk"><i class="fas fa-check"></i></span>Unlimited ticket tiers (VIP, Regular, Early Bird)</li>
                            <li><span class="chk navy-chk"><i class="fas fa-check"></i></span>M-Pesa &amp; card payments (Paystack)</li>
                            <li><span class="chk navy-chk"><i class="fas fa-check"></i></span>QR-code e-ticket delivery via email &amp; SMS</li>
                            <li><span class="chk navy-chk"><i class="fas fa-check"></i></span>Real-time sales dashboard</li>
                            <li><span class="chk navy-chk"><i class="fas fa-check"></i></span>Automatic revenue split to your account</li>
                            <li><span class="chk navy-chk"><i class="fas fa-check"></i></span>Attendee check-in app</li>
                            <li><span class="chk navy-chk"><i class="fas fa-check"></i></span>Post-event attendance reports</li>
                            <li><span class="chk navy-chk"><i class="fas fa-check"></i></span>Free event listing on Tuqio Hub</li>
                        </ul>
                        <a href="<?= ADMIN_URL ?>/register" target="_blank" rel="noopener"
                           class="theme-btn btn-style-two" style="text-align:center;display:block;">
                            <span class="btn-title">Get Started Free</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Voting Plan -->
            <div class="col-lg-5 col-md-10 mb-4 wow fadeInRight">
                <div class="plan-card featured">
                    <div class="plan-badge">Most Popular</div>
                    <div class="plan-header">
                        <div class="plan-icon red"><i class="fas fa-vote-yea"></i></div>
                        <div class="plan-name">Voting</div>
                        <div class="plan-title">Awards &amp; Voting</div>
                        <div class="plan-price">
                            <span class="pct">20%</span>
                            <span class="suffix">starting rate</span>
                        </div>
                        <p class="plan-desc">Power your awards ceremony with secure, high-volume voting. Trusted for events with millions of votes — rates improve at scale.</p>
                    </div>
                    <div class="plan-body">
                        <div class="voting-tiers">
                            <div class="tier-title">Volume Rates</div>
                            <table>
                                <tr>
                                    <td>Up to 500K votes</td>
                                    <td>20%</td>
                                </tr>
                                <tr>
                                    <td>500K – 5M votes</td>
                                    <td>18%</td>
                                </tr>
                                <tr>
                                    <td>5M – 20M votes</td>
                                    <td>15%</td>
                                </tr>
                                <tr>
                                    <td>20M+ votes</td>
                                    <td>Custom</td>
                                </tr>
                            </table>
                        </div>
                        <ul class="plan-features">
                            <li><span class="chk"><i class="fas fa-check"></i></span>M-Pesa voting (STK push, no app needed)</li>
                            <li><span class="chk"><i class="fas fa-check"></i></span>Card voting via Paystack</li>
                            <li><span class="chk"><i class="fas fa-check"></i></span>Real-time live vote counts &amp; leaderboard</li>
                            <li><span class="chk"><i class="fas fa-check"></i></span>Nominee profiles with photos &amp; bios</li>
                            <li><span class="chk"><i class="fas fa-check"></i></span>Anti-fraud &amp; duplicate vote protection</li>
                            <li><span class="chk"><i class="fas fa-check"></i></span>Nominations module included</li>
                            <li><span class="chk"><i class="fas fa-check"></i></span>Live polls &amp; audience engagement</li>
                            <li><span class="chk"><i class="fas fa-check"></i></span>Revenue split to organiser in real-time</li>
                        </ul>
                        <a href="<?= ADMIN_URL ?>/register" target="_blank" rel="noopener"
                           class="theme-btn btn-style-one" style="text-align:center;display:block;">
                            <span class="btn-title">Start Your Awards</span>
                        </a>
                    </div>
                </div>
            </div>

        </div>

        <!-- Enterprise Banner -->
        <div class="enterprise-banner wow fadeInUp">
            <div class="row align-items-center">
                <div class="col-lg-7">
                    <h3>Running a Large-Scale Event?</h3>
                    <p>For events expecting 5M+ votes or 10,000+ ticket sales, we offer custom pricing, dedicated account management, white-label options, and priority infrastructure support. Let's talk.</p>
                    <div class="ent-actions">
                        <a href="<?= SITE_URL ?>/contact" class="theme-btn btn-style-one">
                            <span class="btn-title">Talk to Us</span>
                        </a>
                        <a href="mailto:<?= ADMIN_EMAIL ?>"
                           style="display:inline-flex;align-items:center;gap:8px;color:rgba(255,255,255,0.8);font-size:.9rem;text-decoration:none;padding:12px 22px;border:1px solid rgba(255,255,255,0.25);border-radius:6px;transition:all .2s;"
                           onmouseover="this.style.borderColor='#ed1c24';this.style.color='#ed1c24';"
                           onmouseout="this.style.borderColor='rgba(255,255,255,0.25)';this.style.color='rgba(255,255,255,0.8)';">
                            <i class="fas fa-envelope"></i> <?= ADMIN_EMAIL ?>
                        </a>
                    </div>
                </div>
                <div class="col-lg-5 d-none d-lg-block stat-col">
                    <div class="stat-item">
                        <div class="stat-n">20M+</div>
                        <div class="stat-l">Votes managed on platform</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-n">99.9%</div>
                        <div class="stat-l">Platform uptime during voting windows</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-n">0 KES</div>
                        <div class="stat-l">Setup cost to get started</div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

<!-- ─── How It Works ─────────────────────────────────────── -->
<section class="how-section">
    <div class="auto-container">
        <div class="sec-header wow fadeInUp">
            <h2>How the Fees Work</h2>
            <p>We only charge a percentage of revenue that actually lands on the platform — nothing upfront.</p>
        </div>
        <div class="row">
            <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0ms">
                <div class="step-card">
                    <div class="step-num">1</div>
                    <h5>Create Your Event</h5>
                    <p>Register as an organiser, set up your event, configure ticket tiers or voting categories — takes under 30 minutes.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="100ms">
                <div class="step-card">
                    <div class="step-num">2</div>
                    <h5>Collect Payments</h5>
                    <p>Attendees and voters pay via M-Pesa or card. Paystack &amp; Daraja handle all transaction processing and security.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="200ms">
                <div class="step-card">
                    <div class="step-num">3</div>
                    <h5>Revenue Split Instantly</h5>
                    <p>At the moment of payment, your share lands directly in your Paystack sub-account. No waiting for payouts.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="300ms">
                <div class="step-card">
                    <div class="step-num">4</div>
                    <h5>Track &amp; Withdraw</h5>
                    <p>Monitor all revenue in your dashboard and withdraw to your bank account anytime via your Paystack account.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ─── FAQ ──────────────────────────────────────────────── -->
<section class="pricing-faq">
    <div class="auto-container">
        <div class="sec-header wow fadeInUp">
            <h2>Frequently Asked Questions</h2>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-8">

                <div class="pfaq-item wow fadeInUp">
                    <div class="pfaq-q" onclick="toggleFaq(this)">
                        Are there any setup or monthly fees?
                        <i class="fas fa-chevron-down pfaq-icon"></i>
                    </div>
                    <div class="pfaq-a">
                        None at all. Tuqio Hub is completely free to set up and there are no monthly charges. You only pay a percentage when payments are collected through the platform.
                    </div>
                </div>

                <div class="pfaq-item wow fadeInUp" data-wow-delay="50ms">
                    <div class="pfaq-q" onclick="toggleFaq(this)">
                        When do I receive my money?
                        <i class="fas fa-chevron-down pfaq-icon"></i>
                    </div>
                    <div class="pfaq-a">
                        Revenue is split at transaction time via Paystack's sub-account system. Your share lands in your account the moment a voter or ticket buyer pays — you don't have to wait for a weekly or monthly payout cycle.
                    </div>
                </div>

                <div class="pfaq-item wow fadeInUp" data-wow-delay="100ms">
                    <div class="pfaq-q" onclick="toggleFaq(this)">
                        What payment methods do attendees and voters use?
                        <i class="fas fa-chevron-down pfaq-icon"></i>
                    </div>
                    <div class="pfaq-a">
                        M-Pesa (STK push — no app download needed) and all major debit/credit cards via Paystack. Both methods are available on mobile and desktop.
                    </div>
                </div>

                <div class="pfaq-item wow fadeInUp" data-wow-delay="150ms">
                    <div class="pfaq-q" onclick="toggleFaq(this)">
                        Can I run both ticketing and voting for the same event?
                        <i class="fas fa-chevron-down pfaq-icon"></i>
                    </div>
                    <div class="pfaq-a">
                        Yes. Many of our clients run award ceremonies where attendees buy tickets AND the public votes for nominees. Both products can run simultaneously on the same event.
                    </div>
                </div>

                <div class="pfaq-item wow fadeInUp" data-wow-delay="200ms">
                    <div class="pfaq-q" onclick="toggleFaq(this)">
                        How does the voting rate decrease for large events?
                        <i class="fas fa-chevron-down pfaq-icon"></i>
                    </div>
                    <div class="pfaq-a">
                        Our volume tiers kick in automatically. For example, an event with 2M votes would be billed at the 18% tier. For very large events (20M+ votes), contact us for a custom negotiated rate and a dedicated support arrangement.
                    </div>
                </div>

                <div class="pfaq-item wow fadeInUp" data-wow-delay="250ms">
                    <div class="pfaq-q" onclick="toggleFaq(this)">
                        Is there a minimum event size?
                        <i class="fas fa-chevron-down pfaq-icon"></i>
                    </div>
                    <div class="pfaq-a">
                        No minimum. Whether you're running a small community poll or a national awards show with millions of votes, the same platform and pricing applies. Small events benefit from the same infrastructure that handles large-scale ones.
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

<!-- ─── Bottom CTA ───────────────────────────────────────── -->
<section class="pricing-cta">
    <div class="auto-container">
        <h2 class="wow fadeInUp">Ready to Host Your Event?</h2>
        <p class="wow fadeInUp" data-wow-delay="100ms">Create your organiser account in minutes. No credit card required — you only pay when your audience pays.</p>
        <div class="wow fadeInUp" data-wow-delay="200ms" style="display:flex;gap:16px;justify-content:center;flex-wrap:wrap;">
            <a href="<?= ADMIN_URL ?>/register" target="_blank" rel="noopener" class="theme-btn btn-style-one">
                <span class="btn-title">Create Free Account</span>
            </a>
            <a href="<?= SITE_URL ?>/contact" class="theme-btn btn-style-two">
                <span class="btn-title">Talk to Sales</span>
            </a>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
</div>
<div class="scroll-to-top scroll-to-target" data-target="html"><span class="fa fa-angle-up"></span></div>
<?php include 'includes/footer-links.php'; ?>

<script>
function toggleFaq(el) {
    var ans = el.nextElementSibling;
    var isOpen = ans.classList.contains('open');
    // Close all
    document.querySelectorAll('.pfaq-q').forEach(function(q) { q.classList.remove('open'); });
    document.querySelectorAll('.pfaq-a').forEach(function(a) { a.classList.remove('open'); });
    // Open clicked (if it was closed)
    if (!isOpen) {
        el.classList.add('open');
        ans.classList.add('open');
    }
}
</script>
</body>
</html>
