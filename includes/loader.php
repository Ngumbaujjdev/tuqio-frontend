<!-- ===== Tuqio Preloader ===== -->
<div id="loading">
    <div id="loading-center">
        <div id="loading-logo">
            <img src="<?php echo SITE_URL; ?>/assets/images/logo/tuqio-logo.png" alt="Tuqio Hub" style="width:160px; margin-bottom:20px;">
        </div>
        <div id="loading-center-absolute">
            <div class="object" id="first_object"></div>
            <div class="object" id="second_object"></div>
            <div class="object" id="third_object"></div>
        </div>
    </div>
</div>
<!-- ===== / Preloader ===== -->
<script>
// Force-hide preloader after 3s in case window.load is delayed
setTimeout(function(){
    var l = document.getElementById('loading');
    if (l && l.style.display !== 'none') { l.style.opacity='0'; l.style.transition='opacity .4s'; setTimeout(function(){ l.style.display='none'; }, 400); }
}, 3000);
</script>
