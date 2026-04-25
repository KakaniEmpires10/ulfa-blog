<footer class="site-footer">
    <div class="container">
        <div class="columns is-variable is-6">
            <div class="column is-5">
                <span class="footer-kicker has-text-weight-bold">ULFA BLOG</span>
                <h2 class="footer-title"><?= esc(get_setting('site_tagline', 'Catatan yang layak disimpan')) ?></h2>
                <p><?= esc(get_setting('site_description', 'Blog pribadi yang tenang dan mudah dibaca.')) ?></p>
            </div>
            <div class="column is-3">
                <p class="footer-heading has-text-weight-bold">Navigasi</p>
                <a class="footer-link" href="<?= site_url('/') ?>">Beranda</a>
                <a class="footer-link" href="<?= site_url('/about') ?>">Tentang</a>
                <a class="footer-link" href="<?= site_url('/contact') ?>">Kontak</a>
                <a class="footer-link" href="<?= url_to('login') ?>">Masuk</a>
            </div>
            <div class="column is-4">
                <p class="footer-heading has-text-weight-bold">Hubungi</p>
                <p><?= esc(get_setting('contact_email', 'hello@ulfablog.test')) ?></p>
                <p class="footer-note">Dibangun dengan CodeIgniter 4, Bulma, dan Alpine.js.</p>
            </div>
        </div>
        <div class="scroll-top">
            <a href="#top" class="button is-link is-rounded shadow-sm" aria-label="Kembali ke atas">
                <i class="fa-solid fa-arrow-up"></i>
            </a>
        </div>
        <div class="footer-bottom has-text-centered is-size-7 mt-5">
            <div class="mb-2" style="opacity: 0.6;">
                <span>&copy; <?= date('Y') ?> </span>
                <strong class="has-text-dark"><?= esc(get_setting('site_name', 'Ulfa Blog')) ?></strong>
            </div>

            <div class="is-flex is-align-items-center is-justify-content-center" style="gap: 0.4rem; line-height: 1;">
                <span style="opacity: 0.8;">Ditulis dengan tenang & dibuat oleh</span>

                <a href="https://alimfrontend.my.id/"
                    target="_blank"
                    class="custom-footer-link has-text-weight-bold has-text-primary"
                    @mouseenter="$store.tooltip.show($el, 'Muhammad Alim Kakani', 'top', 'is-link')"
                    @mouseleave="$store.tooltip.hide()"
                >
                    Adiknya
                </a>

                <i class="fa-solid fa-heart heart-pulse"></i>
            </div>
        </div>
    </div>
</footer>