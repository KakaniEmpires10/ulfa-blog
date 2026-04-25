<?php
$primaryHsl = hex_to_hsl((string) get_setting('primary_color', '#ce8460')) ?? [
    'h' => '20deg',
    's' => '53%',
    'l' => '59%',
];
$radiusTokens = bulma_radius_tokens((int) get_setting('border_radius', 12));
?>
<script>
    document.documentElement.style.setProperty('--bulma-primary-h', "<?= esc($primaryHsl['h']) ?>");
    document.documentElement.style.setProperty('--bulma-primary-s', "<?= esc($primaryHsl['s']) ?>");
    document.documentElement.style.setProperty('--bulma-primary-l', "<?= esc($primaryHsl['l']) ?>");
    document.documentElement.style.setProperty('--bulma-link-h', "<?= esc($primaryHsl['h']) ?>");
    document.documentElement.style.setProperty('--bulma-link-s', "<?= esc($primaryHsl['s']) ?>");
    document.documentElement.style.setProperty('--bulma-link-l', "<?= esc($primaryHsl['l']) ?>");
    document.documentElement.style.setProperty('--bulma-radius', "<?= esc($radiusTokens['radius']) ?>");
    document.documentElement.style.setProperty('--bulma-radius-small', "<?= esc($radiusTokens['small']) ?>");
    document.documentElement.style.setProperty('--bulma-radius-medium', "<?= esc($radiusTokens['medium']) ?>");
    document.documentElement.style.setProperty('--bulma-radius-large', "<?= esc($radiusTokens['large']) ?>");
    window.APP = {
        baseUrl: "<?= base_url() ?>",
        csrf: "<?= csrf_hash() ?>",
        siteName: "<?= get_setting('site_name', 'Ulfa Blog') ?>",
        themeMode: "<?= get_setting('theme_mode', 'light') ?>",
        sliderSource: "<?= get_setting('homepage_slider_source', 'popular') ?>",
        isLoggedIn: <?= auth()->loggedIn() ? 'true' : 'false' ?>,
        userRole: <?= auth()->loggedIn() ? json_encode(auth()->user()->getGroups()) : '[]' ?>
    }
</script>
