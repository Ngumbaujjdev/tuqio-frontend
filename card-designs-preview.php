<?php
include 'config/config.php';
include 'libs/App.php';

$eventsResp = tuqio_api('/api/public/events');
$allEvents  = $eventsResp['data'] ?? [];
$upcoming   = array_values(array_filter($allEvents, fn($e) => ($e['status'] ?? '') !== 'past'));
usort($upcoming, fn($a, $b) =>
    (!empty($b['banner_image']) || !empty($b['thumbnail_image'])) <=> (!empty($a['banner_image']) || !empty($a['thumbnail_image'])));
$events = array_slice($upcoming, 0, 3);

$phaseLabels = ['voting' => 'Voting Open', 'on_sale' => 'Tickets On Sale', 'nomination' => 'Nominations Open', 'upcoming' => 'Upcoming'];

function ev_banner($ev) {
    if (!empty($ev['banner_image']))    return API_STORAGE . $ev['banner_image'];
    if (!empty($ev['thumbnail_image'])) return API_STORAGE . $ev['thumbnail_image'];
    return '';
}

function render_lower_content(array $ev): string {
    $venue = trim(($ev['venue_name'] ? $ev['venue_name'] . ', ' : '') . ($ev['venue_city'] ?? ''), ', ');

    $pills = [];
    if (!empty($ev['has_ticketing']))   $pills[] = ['icon' => 'fa-ticket-alt', 'label' => 'Tickets'];
    if (!empty($ev['has_voting']))      $pills[] = ['icon' => 'fa-vote-yea',   'label' => 'Voting'];
    if (!empty($ev['has_nominations'])) $pills[] = ['icon' => 'fa-award',      'label' => 'Nominations'];
    if (!empty($ev['has_registration']))$pills[] = ['icon' => 'fa-user-check', 'label' => 'RSVP'];

    $formatBadge = '';
    if (!empty($ev['is_virtual']))                    $formatBadge = ['icon' => 'fa-video',        'label' => 'Virtual'];
    elseif (($ev['event_format'] ?? '') === 'hybrid')  $formatBadge = ['icon' => 'fa-layer-group',  'label' => 'Hybrid'];

    ob_start();
    ?>
    <div class="lower-content">
        <ul class="post-info">
            <?php if (!empty($ev['start_date'])): ?><li><i class="far fa-calendar-alt"></i><?= date('d M Y', strtotime($ev['start_date'])) ?></li><?php endif; ?>
            <?php if ($venue): ?><li><i class="fas fa-map-marker-alt"></i><?= htmlspecialchars($venue) ?></li><?php endif; ?>
            <?php if ($formatBadge): ?><li><i class="fas <?= $formatBadge['icon'] ?>"></i><?= $formatBadge['label'] ?></li><?php endif; ?>
        </ul>
        <h4><a href="#"><?= htmlspecialchars($ev['name']) ?></a></h4>
        <?php if (!empty($ev['tagline'])): ?>
        <div class="text"><?= htmlspecialchars($ev['tagline']) ?></div>
        <?php elseif (!empty($ev['short_description'])): ?>
        <div class="text"><?= htmlspecialchars(mb_substr($ev['short_description'], 0, 110)) ?>...</div>
        <?php endif; ?>
        <?php if ($pills): ?>
        <ul class="feature-pills">
            <?php foreach ($pills as $p): ?>
            <li><i class="fas <?= $p['icon'] ?>"></i><?= $p['label'] ?></li>
            <?php endforeach; ?>
        </ul>
        <?php endif; ?>
        <div class="btn-box"><a href="#" class="btn">View Details</a></div>
    </div>
    <?php
    return ob_get_clean();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Event Card Designs — Preview</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
    :root{ --navy:#1e1548; --red:#ed1c24; }
    *{box-sizing:border-box;}
    body{margin:0;font-family:'Segoe UI', Arial, sans-serif;background:#f2f1f7;color:var(--navy);}
    header.page-head{background:var(--navy);color:#fff;padding:36px 24px;text-align:center;}
    header.page-head h1{margin:0 0 8px;font-size:28px;}
    header.page-head p{margin:0;color:rgba(255,255,255,.7);font-size:15px;}

    .option-section{max-width:1200px;margin:0 auto;padding:48px 24px 8px;}
    .option-label{display:flex;align-items:center;gap:14px;margin-bottom:22px;}
    .option-number{background:var(--red);color:#fff;width:34px;height:34px;border-radius:50%;
        display:flex;align-items:center;justify-content:center;font-weight:700;flex-shrink:0;}
    .option-label h2{margin:0;font-size:20px;}
    .option-label span.desc{display:block;font-size:13.5px;color:#5a5470;font-weight:400;margin-top:2px;}

    .cards-row{display:grid;grid-template-columns:repeat(3,1fr);gap:26px;}
    @media (max-width:900px){ .cards-row{grid-template-columns:1fr;} }
    /* 2C: 2-up on mobile instead of 1 */
    @media (max-width:900px){ .cards-row.two-up-mobile{grid-template-columns:repeat(2,1fr);gap:14px;} }
    @media (max-width:480px){ .cards-row.two-up-mobile{grid-template-columns:1fr;gap:18px;} }

    .card{
        background:#fff;border-radius:10px;overflow:hidden;
        box-shadow:0 6px 22px rgba(30,21,72,.09);
        display:flex;flex-direction:column;height:100%;
        transition:transform .2s ease, box-shadow .2s ease;
    }
    .card:hover{transform:translateY(-4px);box-shadow:0 12px 30px rgba(30,21,72,.16);}

    .tag{
        position:absolute;left:16px;top:16px;z-index:2;
        background:var(--red);color:#fff;font-size:12.5px;font-weight:600;
        padding:5px 14px;border-radius:4px;text-transform:uppercase;letter-spacing:.4px;
    }
    .badge-corner{
        position:absolute;right:14px;top:14px;z-index:2;
        display:flex;align-items:center;gap:5px;
        background:rgba(255,255,255,.95);color:var(--navy);
        font-size:11.5px;font-weight:700;text-transform:uppercase;letter-spacing:.4px;
        padding:5px 12px;border-radius:20px;
        box-shadow:0 3px 10px rgba(0,0,0,.25);
    }
    .badge-corner.badge-new{color:#0d8a3f;}
    .badge-corner.badge-trending{color:#ed1c24;}
    .badge-corner i{font-size:11px;}

    /* ---- shared lower-content styling across all 3 options ---- */
    .lower-content{padding:22px 24px 26px;flex:1;display:flex;flex-direction:column;}
    .post-info{list-style:none;margin:0 0 14px;padding:0;display:flex;flex-wrap:wrap;gap:10px 18px;}
    .post-info li{display:flex;align-items:center;gap:6px;font-size:13px;font-weight:600;color:#5a5470;}
    .post-info li i{color:var(--red);font-size:13px;}
    .card h4{margin:0 0 8px;font-size:18px;line-height:1.35;min-height:2.7em;
        display:-webkit-box;-webkit-line-clamp:2;line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;}
    .card h4 a{color:var(--navy);text-decoration:none;}
    .card .text{font-size:14px;color:#6b6680;margin-bottom:0;line-height:1.6;
        display:-webkit-box;-webkit-line-clamp:3;line-clamp:3;-webkit-box-orient:vertical;overflow:hidden;}
    .feature-pills{list-style:none;margin:2px 0 4px;padding:0;display:flex;flex-wrap:wrap;gap:8px;}
    .feature-pills li{
        display:flex;align-items:center;gap:5px;
        background:#f3f2f8;color:var(--navy);
        font-size:11.5px;font-weight:600;
        padding:5px 11px;border-radius:20px;
    }
    .feature-pills li i{color:var(--red);font-size:11px;}
    .btn-box{margin-top:auto;padding-top:16px;}
    .btn{
        display:flex;align-items:center;justify-content:center;width:100%;
        background:var(--red);color:#fff;font-size:14px;font-weight:600;
        padding:12px 20px;border-radius:6px;text-decoration:none;letter-spacing:.2px;
    }

    /* ================= OPTION 1: Blurred backdrop, fixed height ================= */
    .opt1 .image-box{position:relative;height:240px;overflow:hidden;background:#12102a;}
    .opt1 .image-box .bg-blur{
        position:absolute;inset:0;background-size:cover;background-position:center;
        filter:blur(18px) brightness(0.55) saturate(1.2);transform:scale(1.15);
    }
    .opt1 .image-box img{position:relative;z-index:1;width:100%;height:100%;object-fit:contain;display:block;}

    /* ================= OPTION 2: Natural height, same width (current live design) ================= */
    .opt2 .image-box{position:relative;height:340px;overflow:hidden;}
    .opt2 .image-box img{width:100%;height:100%;object-fit:cover;object-position:top;display:block;}

    /* ================= OPTION 2B: Same height as each other, poster shown whole ================= */
    .opt2b .image-box{position:relative;height:240px;background:#f3f2f8;overflow:hidden;}
    .opt2b .image-box img{width:100%;height:100%;object-fit:contain;display:block;}

    /* ================= OPTION 2C: Same height + cinematic blurred backdrop + badge ================= */
    .opt2c .image-box{position:relative;height:240px;overflow:hidden;background:#0e0c22;}
    .opt2c .image-box .bg-blur{
        position:absolute;inset:0;background-size:cover;background-position:center;
        filter:blur(22px) brightness(0.45) saturate(1.3);transform:scale(1.2);
    }
    .opt2c .image-box:after{
        content:"";position:absolute;inset:0;z-index:1;
        background:linear-gradient(to bottom, rgba(0,0,0,0) 55%, rgba(0,0,0,.45) 100%);
    }
    .opt2c .image-box img{
        position:relative;z-index:1;width:100%;height:100%;object-fit:contain;display:block;
        filter:drop-shadow(0 8px 18px rgba(0,0,0,.45));
    }

    /* ================= OPTION 3: Portrait frame (3:4) ================= */
    .opt3 .image-box{position:relative;aspect-ratio:3/4;overflow:hidden;background:#eeecf6;}
    .opt3 .image-box img{width:100%;height:100%;object-fit:contain;display:block;background:#fff;}
    .opt3 .image-box:after{content:"";position:absolute;inset:0;box-shadow:inset 0 0 0 1px rgba(30,21,72,.06);pointer-events:none;}

    .image-box a{display:block;height:100%;}

    footer.note{max-width:1200px;margin:40px auto 60px;padding:0 24px;font-size:13.5px;color:#7a7590;line-height:1.6;}
    footer.note strong{color:var(--navy);}
</style>
</head>
<body>

<header class="page-head">
    <h1>Event Card — Design Options</h1>
    <p>Three ways to display the full event poster. Content section (date, title, button) is now styled identically and aligned across all three so you're only comparing the image treatment.</p>
</header>

<!-- OPTION 1 -->
<section class="option-section">
    <div class="option-label">
        <div class="option-number">1</div>
        <div>
            <h2>Blurred Backdrop</h2>
            <span class="desc">Poster shown whole (object-fit: contain), with a soft blurred zoom of the same image filling the space behind it. No cropping, no empty bars.</span>
        </div>
    </div>
    <div class="cards-row">
        <?php foreach ($events as $ev):
            $banner = ev_banner($ev);
            $phase  = $ev['current_phase'] ?? '';
            $phaseLabel = $phaseLabels[$phase] ?? '';
        ?>
        <div class="card opt1">
            <div class="image-box">
                <?php if ($phaseLabel): ?><span class="tag"><?= $phaseLabel ?></span><?php endif; ?>
                <?php if ($banner): ?>
                <div class="bg-blur" style="background-image:url('<?= htmlspecialchars($banner) ?>');"></div>
                <img src="<?= htmlspecialchars($banner) ?>" alt="<?= htmlspecialchars($ev['name']) ?>">
                <?php endif; ?>
            </div>
            <?= render_lower_content($ev) ?>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- OPTION 2 -->
<section class="option-section">
    <div class="option-label">
        <div class="option-number">2</div>
        <div>
            <h2>Fixed Height + Cinematic Backdrop + Badges (currently live)</h2>
            <span class="desc">All 3 cards now the same fixed 240px height — no more mismatch between the first two and the third. Poster shown whole, letterbox space filled with a blurred/darkened zoom of the same poster instead of flat grey. Corner badge added (New / Trending / Featured — placeholder, can wire to real logic later). On mobile this shows 2 cards per row instead of 1. This is exactly what's live on the homepage right now.</span>
        </div>
    </div>
    <div class="cards-row two-up-mobile">
        <?php foreach ($events as $i => $ev):
            $banner = ev_banner($ev);
            $phase  = $ev['current_phase'] ?? '';
            $phaseLabel = $phaseLabels[$phase] ?? '';
            $badges = [
                ['label' => 'New',       'class' => 'badge-new',       'icon' => 'fa-sparkles'],
                ['label' => 'Trending',  'class' => 'badge-trending',  'icon' => 'fa-fire'],
                ['label' => 'Featured',  'class' => '',                'icon' => 'fa-star'],
            ];
            $badge = $badges[$i % count($badges)];
        ?>
        <div class="card opt2c">
            <div class="image-box">
                <?php if ($phaseLabel): ?><span class="tag"><?= $phaseLabel ?></span><?php endif; ?>
                <span class="badge-corner <?= $badge['class'] ?>"><i class="fas <?= $badge['icon'] ?>"></i><?= $badge['label'] ?></span>
                <?php if ($banner): ?>
                <div class="bg-blur" style="background-image:url('<?= htmlspecialchars($banner) ?>');"></div>
                <img src="<?= htmlspecialchars($banner) ?>" alt="<?= htmlspecialchars($ev['name']) ?>">
                <?php endif; ?>
            </div>
            <?= render_lower_content($ev) ?>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- OPTION 2A (superseded) -->
<section class="option-section">
    <div class="option-label">
        <div class="option-number">2A</div>
        <div>
            <h2>Same Height, Cropped Fill + Badges</h2>
            <span class="desc">Fixed 340px height for all 3 cards, poster fills the frame edge-to-edge (object-fit: cover, top-anchored) — no grey bars, but the bottom of a tall poster may crop slightly. Badges + venue/format + feature pills added below.</span>
        </div>
    </div>
    <div class="cards-row">
        <?php foreach ($events as $i => $ev):
            $banner = ev_banner($ev);
            $phase  = $ev['current_phase'] ?? '';
            $phaseLabel = $phaseLabels[$phase] ?? '';
            $badges = [
                ['label' => 'New',       'class' => 'badge-new',       'icon' => 'fa-sparkles'],
                ['label' => 'Trending',  'class' => 'badge-trending',  'icon' => 'fa-fire'],
                ['label' => 'Featured',  'class' => '',                'icon' => 'fa-star'],
            ];
            $badge = $badges[$i % count($badges)];
        ?>
        <div class="card opt2">
            <div class="image-box">
                <?php if ($phaseLabel): ?><span class="tag"><?= $phaseLabel ?></span><?php endif; ?>
                <span class="badge-corner <?= $badge['class'] ?>"><i class="fas <?= $badge['icon'] ?>"></i><?= $badge['label'] ?></span>
                <?php if ($banner): ?>
                <img src="<?= htmlspecialchars($banner) ?>" alt="<?= htmlspecialchars($ev['name']) ?>">
                <?php endif; ?>
            </div>
            <?= render_lower_content($ev) ?>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- OPTION 2B -->
<section class="option-section">
    <div class="option-label">
        <div class="option-number">2B</div>
        <div>
            <h2>Same Height, Plain Background (comparison)</h2>
            <span class="desc">Same fixed 240px height for all 3 cards, poster shown whole on a light neutral background (no cinematic backdrop, no badges). Compare against Option 2 above.</span>
        </div>
    </div>
    <div class="cards-row">
        <?php foreach ($events as $ev):
            $banner = ev_banner($ev);
            $phase  = $ev['current_phase'] ?? '';
            $phaseLabel = $phaseLabels[$phase] ?? '';
        ?>
        <div class="card opt2b">
            <div class="image-box">
                <?php if ($phaseLabel): ?><span class="tag"><?= $phaseLabel ?></span><?php endif; ?>
                <?php if ($banner): ?>
                <img src="<?= htmlspecialchars($banner) ?>" alt="<?= htmlspecialchars($ev['name']) ?>">
                <?php endif; ?>
            </div>
            <?= render_lower_content($ev) ?>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- OPTION 3 -->
<section class="option-section">
    <div class="option-label">
        <div class="option-number">3</div>
        <div>
            <h2>Portrait Frame</h2>
            <span class="desc">A fixed tall (3:4) frame sized for portrait posters, image shown whole on a light neutral background. Reads like a flyer wall.</span>
        </div>
    </div>
    <div class="cards-row">
        <?php foreach ($events as $ev):
            $banner = ev_banner($ev);
            $phase  = $ev['current_phase'] ?? '';
            $phaseLabel = $phaseLabels[$phase] ?? '';
        ?>
        <div class="card opt3">
            <div class="image-box">
                <?php if ($phaseLabel): ?><span class="tag"><?= $phaseLabel ?></span><?php endif; ?>
                <?php if ($banner): ?>
                <img src="<?= htmlspecialchars($banner) ?>" alt="<?= htmlspecialchars($ev['name']) ?>">
                <?php endif; ?>
            </div>
            <?= render_lower_content($ev) ?>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<footer class="note">
    <strong>Note:</strong> temporary preview page (<code>card-designs-preview.php</code>), not linked from the site menu. Tell me the option number and I'll switch the homepage to it (Option 2 is what's live right now).
</footer>

</body>
</html>
