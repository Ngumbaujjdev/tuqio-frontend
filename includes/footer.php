<style>
@keyframes floatBubble {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-14px); }
}
</style>

<footer class="main-footer" style="background:linear-gradient(160deg,#15102e 0%,#1e1548 40%,#2d1f6b 100%);color:#ddd;position:relative;">

    <!-- Top accent line -->
    <div style="height:4px;background:linear-gradient(90deg,#ed1c24,#1e1548,#ed1c24);width:100%;position:relative;z-index:3;"></div>

    <!-- Decorative Bubbles -->
    <div style="position:absolute;top:0;left:0;width:100%;height:220px;pointer-events:none;z-index:0;overflow:hidden;">
        <div style="position:absolute;top:10px;left:3%;width:160px;height:160px;border-radius:50%;background:radial-gradient(circle,rgba(237,28,36,0.18) 0%,rgba(237,28,36,0.04) 65%,transparent 100%);border:1.5px solid rgba(237,28,36,0.3);animation:floatBubble 8s ease-in-out infinite;"></div>
        <div style="position:absolute;top:60px;left:20%;width:70px;height:70px;border-radius:50%;background:radial-gradient(circle,rgba(237,28,36,0.22) 0%,transparent 75%);border:1.5px solid rgba(237,28,36,0.4);animation:floatBubble 6s ease-in-out infinite 1.5s;"></div>
        <div style="position:absolute;top:20px;left:42%;width:100px;height:100px;border-radius:50%;background:radial-gradient(circle,rgba(255,255,255,0.08) 0%,transparent 70%);border:1px solid rgba(255,255,255,0.15);animation:floatBubble 7s ease-in-out infinite 0.8s;"></div>
        <div style="position:absolute;top:50px;left:63%;width:55px;height:55px;border-radius:50%;background:radial-gradient(circle,rgba(237,28,36,0.25) 0%,transparent 80%);border:2px solid rgba(237,28,36,0.4);animation:floatBubble 5s ease-in-out infinite 2s;"></div>
        <div style="position:absolute;top:5px;right:5%;width:140px;height:140px;border-radius:50%;background:radial-gradient(circle,rgba(255,180,100,0.1) 0%,transparent 70%);border:1px solid rgba(237,28,36,0.25);animation:floatBubble 9s ease-in-out infinite 0.3s;"></div>
    </div>

    <!-- Widgets Section -->
    <div class="widgets-section" style="padding:70px 0 40px;position:relative;z-index:2;">
        <div class="auto-container">
            <div class="row">

                <!-- Col 1: About Tuqio -->
                <div class="footer-column col-xl-4 col-lg-4 col-md-6 col-sm-12 mb-5 mb-lg-0">
                    <div class="footer-widget about-widget">
                        <div class="logo" style="margin-bottom:20px;">
                            <a href="<?php echo SITE_URL; ?>/">
                                <img src="<?php echo SITE_URL; ?>/assets/images/logo/tuqio-logo.svg"
                                     alt="Tuqio Hub" style="max-height:70px;width:auto;">
                            </a>
                        </div>
                        <p style="color:rgba(255,255,255,0.72);font-size:0.95rem;line-height:1.8;margin-bottom:20px;">
                            Kenya's premier event management and engagement platform. We power awards, conferences, voting, ticketing, and polls — all in one hub.
                        </p>
                        <ul class="social-icon-two" style="list-style:none;padding:0;display:flex;gap:10px;flex-wrap:wrap;">
                            <?php
                            $socials = [
                                ['fab fa-facebook-f', SOCIAL_FACEBOOK],
                                ['fab fa-twitter',    SOCIAL_TWITTER],
                                ['fab fa-instagram',  SOCIAL_INSTAGRAM],
                                ['fab fa-tiktok',     SOCIAL_TIKTOK],
                                ['fab fa-linkedin-in',SOCIAL_LINKEDIN],
                            ];
                            foreach ($socials as [$icon,$href]): ?>
                            <li>
                                <a href="<?php echo $href; ?>" target="_blank"
                                   style="display:inline-flex;align-items:center;justify-content:center;width:38px;height:38px;border-radius:50%;background:rgba(255,255,255,0.1);color:#ed1c24;font-size:16px;text-decoration:none;transition:all .3s;"
                                   onmouseover="this.style.background='#ed1c24';this.style.color='#fff';"
                                   onmouseout="this.style.background='rgba(255,255,255,0.1)';this.style.color='#ed1c24';">
                                    <i class="<?php echo $icon; ?>"></i>
                                </a>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>

                <!-- Col 2: Quick Links -->
                <div class="footer-column col-xl-2 col-lg-2 col-md-6 col-sm-12 mb-5 mb-lg-0">
                    <div class="footer-widget">
                        <h2 class="widget-title" style="color:#ed1c24;font-size:1.05rem;font-weight:700;text-transform:uppercase;letter-spacing:2px;margin-bottom:20px;padding-bottom:10px;border-bottom:2px solid rgba(237,28,36,0.4);">Quick Links</h2>
                        <ul style="list-style:none;padding:0;margin:0;">
                            <?php
                            $links = [
                                ['Home',           SITE_URL . '/'],
                                ['Browse Events',  SITE_URL . '/events'],
                                ['Nominees',       SITE_URL . '/nominees'],
                                ['Vote Now',       SITE_URL . '/vote'],
                                ['Polls',          SITE_URL . '/polls'],
                                ['Gallery',        SITE_URL . '/gallery'],
                                ['Blog',           SITE_URL . '/blog'],
                                ['Contact',        SITE_URL . '/contact'],
                            ];
                            foreach ($links as [$label,$href]): ?>
                            <li style="margin-bottom:10px;">
                                <a href="<?php echo $href; ?>"
                                   style="color:rgba(255,255,255,0.75);text-decoration:none;font-size:0.95rem;transition:color .3s;"
                                   onmouseover="this.style.color='#ed1c24';"
                                   onmouseout="this.style.color='rgba(255,255,255,0.75)';">→ <?php echo $label; ?></a>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>

                <!-- Col 3: For Organizers -->
                <div class="footer-column col-xl-3 col-lg-3 col-md-6 col-sm-12 mb-5 mb-lg-0">
                    <div class="footer-widget">
                        <h2 class="widget-title" style="color:#ed1c24;font-size:1.05rem;font-weight:700;text-transform:uppercase;letter-spacing:2px;margin-bottom:20px;padding-bottom:10px;border-bottom:2px solid rgba(237,28,36,0.4);">For Organizers</h2>
                        <ul style="list-style:none;padding:0;margin:0;">
                            <?php
                            $orgLinks = [
                                ['Host an Event',        SITE_URL . '/become-organizer'],
                                ['Nominations Setup',    SITE_URL . '/become-organizer'],
                                ['Ticketing & Payments', SITE_URL . '/become-organizer'],
                                ['Voting System',        SITE_URL . '/become-organizer'],
                                ['Live Polls',           SITE_URL . '/become-organizer'],
                                ['Get Started',          SITE_URL . '/become-organizer'],
                            ];
                            foreach ($orgLinks as [$label,$href]): ?>
                            <li style="margin-bottom:10px;">
                                <a href="<?php echo $href; ?>"
                                   style="color:rgba(255,255,255,0.75);text-decoration:none;font-size:0.95rem;transition:color .3s;"
                                   onmouseover="this.style.color='#ed1c24';"
                                   onmouseout="this.style.color='rgba(255,255,255,0.75)';">→ <?php echo $label; ?></a>
                            </li>
                            <?php endforeach; ?>
                        </ul>

                        <!-- CTA block -->
                        <div style="margin-top:24px;background:rgba(237,28,36,0.15);border:1px solid rgba(237,28,36,0.3);border-radius:8px;padding:16px;text-align:center;">
                            <p style="color:#fff;font-size:.85rem;margin-bottom:10px;">Ready to host your event on Tuqio?</p>
                            <a href="<?php echo SITE_URL; ?>/become-organizer"
                               class="theme-btn btn-style-one"
                               style="font-size:.8rem;padding:8px 18px;">
                                <span class="btn-title">Get Started</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Col 4: Contact -->
                <div class="footer-column col-xl-3 col-lg-3 col-md-6 col-sm-12">
                    <div class="footer-widget contact-widget">
                        <h2 class="widget-title" style="color:#ed1c24;font-size:1.05rem;font-weight:700;text-transform:uppercase;letter-spacing:2px;margin-bottom:20px;padding-bottom:10px;border-bottom:2px solid rgba(237,28,36,0.4);">Get in Touch</h2>
                        <ul style="list-style:none;padding:0;margin:0;">
                            <li style="display:flex;gap:14px;margin-bottom:18px;align-items:flex-start;">
                                <span style="color:#ed1c24;font-size:18px;margin-top:2px;min-width:20px;"><i class="flaticon-location"></i></span>
                                <div>
                                    <span style="color:rgba(255,255,255,0.75);font-size:.9rem;line-height:1.6;">Nairobi, Kenya</span><br>
                                    <span style="color:rgba(255,255,255,0.45);font-size:.8rem;">Head Office</span>
                                </div>
                            </li>
                            <li style="display:flex;gap:14px;margin-bottom:18px;align-items:flex-start;">
                                <span style="color:#ed1c24;font-size:18px;margin-top:2px;min-width:20px;"><i class="flaticon-email-1"></i></span>
                                <div>
                                    <a href="mailto:<?= ADMIN_EMAIL ?>"
                                       style="color:rgba(255,255,255,0.75);font-size:.9rem;text-decoration:none;"
                                       onmouseover="this.style.color='#ed1c24';"
                                       onmouseout="this.style.color='rgba(255,255,255,0.75)';"><?= ADMIN_EMAIL ?></a><br>
                                    <span style="color:rgba(255,255,255,0.45);font-size:.8rem;">Email Us</span>
                                </div>
                            </li>
                            <li style="display:flex;gap:14px;margin-bottom:18px;align-items:flex-start;">
                                <span style="color:#ed1c24;font-size:18px;margin-top:2px;min-width:20px;"><i class="flaticon-call-1"></i></span>
                                <div>
                                    <a href="tel:<?= SITE_PHONE ?>"
                                       style="color:rgba(255,255,255,0.75);font-size:.9rem;text-decoration:none;"
                                       onmouseover="this.style.color='#ed1c24';"
                                       onmouseout="this.style.color='rgba(255,255,255,0.75)';"><?= SITE_PHONE ?></a><br>
                                    <span style="color:rgba(255,255,255,0.45);font-size:.8rem;">Call Us</span>
                                </div>
                            </li>
                            <li style="display:flex;gap:14px;align-items:flex-start;">
                                <span style="color:#ed1c24;font-size:18px;margin-top:2px;min-width:20px;"><i class="flaticon-alarm-clock-1"></i></span>
                                <div>
                                    <span style="color:rgba(255,255,255,0.75);font-size:.9rem;">Mon – Fri: 9am – 6pm</span><br>
                                    <span style="color:rgba(255,255,255,0.45);font-size:.8rem;">Working Hours</span>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Footer Bottom Bar -->
    <div class="footer-bottom">
        <div class="auto-container">
            <div class="inner-container">
                <div class="copyright-text">
                    <p>&copy; <?php echo date('Y'); ?> <a href="<?php echo SITE_URL; ?>/">Tuqio Hub</a> &mdash; Kenya's Premier Event Platform. All Rights Reserved.</p>
                </div>
            </div>
        </div>
    </div>

</footer>
