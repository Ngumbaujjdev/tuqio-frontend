<section class="hidden-bar">

    <div class="inner-box">

        <div class="title-box">
            <h2>Contact Us</h2>
            <div class="cross-icon"><span class="fa fa-times"></span></div>
        </div>

        <div class="form-style-one">

            <form id="hidden-bar-form">

                <div class="form-group">
                    <input type="text" name="full_name" class="username" placeholder="Your Name *" required>
                </div>

                <div class="form-group">
                    <input type="email" name="email" class="email" placeholder="Your Email *" required>
                </div>

                <div class="form-group">
                    <input type="tel" name="phone" class="phone" placeholder="Your Phone">
                </div>

                <div class="form-group">
                    <textarea name="message" class="message" placeholder="Your Message *" required></textarea>
                </div>

                <input type="hidden" name="subject" value="Quick Contact (Sidebar)">

                <div class="form-group" id="hb-feedback" style="display:none; font-size:.9rem; padding:8px 12px; border-radius:4px;"></div>

                <div class="form-group">
                    <button class="theme-btn btn-style-one" type="submit" id="hb-submit-btn">
                        <span class="btn-title">Send Message</span>
                    </button>
                </div>

            </form>

        </div>

        <ul class="contact-list-one">
            <li><i class="flaticon-location"></i> Nairobi, Kenya <strong>Head Office</strong></li>
            <li><i class="flaticon-alarm-clock-1"></i> Monday – Friday, 9am – 6pm <strong>Working Hours</strong></li>
            <li><i class="flaticon-email-1"></i> <a href="mailto:hello@tuqio.com">hello@tuqio.com</a> <strong>Email Us</strong></li>
        </ul>

    </div>

</section>

<script>
(function(){
    var form = document.getElementById('hidden-bar-form');
    if (!form) return;

    form.addEventListener('submit', function(e){
        e.preventDefault();
        var btn      = document.getElementById('hb-submit-btn');
        var feedback = document.getElementById('hb-feedback');
        var data     = new FormData(form);
        var params   = new URLSearchParams(data).toString();

        btn.disabled = true;
        btn.querySelector('.btn-title').textContent = 'Sending…';
        feedback.style.display = 'none';

        fetch('<?php echo defined("API_BASE") ? API_BASE : "https://platform.tuqiohub.africa"; ?>/api/public/contact', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: params
        })
        .then(function(r){ return r.json(); })
        .then(function(res){
            feedback.style.display = 'block';
            if (res.success) {
                feedback.style.background = '#f0fff4';
                feedback.style.color      = '#276749';
                feedback.style.border     = '1px solid #c6f6d5';
                feedback.textContent      = res.message;
                form.reset();
            } else {
                feedback.style.background = '#fff5f5';
                feedback.style.color      = '#c53030';
                feedback.style.border     = '1px solid #fed7d7';
                feedback.textContent      = res.message;
            }
        })
        .catch(function(){
            feedback.style.display    = 'block';
            feedback.style.background = '#fff5f5';
            feedback.style.color      = '#c53030';
            feedback.style.border     = '1px solid #fed7d7';
            feedback.textContent      = 'Something went wrong. Please try again.';
        })
        .finally(function(){
            btn.disabled = false;
            btn.querySelector('.btn-title').textContent = 'Send Message';
        });
    });
})();
</script>
