<header class="site-header" x-data="siteNav" :class="{ 'is-scrolled': scrolled, 'is-open': open }">
    <div class="container">
        <nav style="background-color: transparent;" class="navbar py-4">
            <div class="navbar-brand">
                <a class="navbar-item brand-mark" href="<?= site_url('/') ?>">
                    <span class="brand-pill brand-icon-mark">
                        <i class="fa-solid fa-feather-pointed"></i>
                    </span>
                    <span class="brand-copy">
                        <strong><?= esc(get_setting('site_name', 'Ulfa Blog')) ?></strong>
                        <small><?= esc(get_setting('site_tagline', 'Catatan yang layak disimpan')) ?></small>
                    </span>
                </a>

                <button class="navbar-burger" type="button" :class="{ 'is-active': open }" @click="open = !open" aria-label="Buka menu navigasi">
                    <span aria-hidden="true"></span>
                    <span aria-hidden="true"></span>
                    <span aria-hidden="true"></span>
                    <span aria-hidden="true"></span>
                </button>
            </div>

            <div style="background-color: transparent;" class="navbar-menu" :class="{ 'is-active': open }">
                <div class="navbar-end">
                    <a class="navbar-item <?= nav_is_active('') ? 'has-text-primary' : '' ?>" href="<?= site_url('/') ?>">Beranda</a>
                    <a class="navbar-item <?= nav_is_active('about') ? 'has-text-primary' : '' ?>" href="<?= site_url('/about') ?>">Tentang</a>
                    <?php if (auth()->loggedIn()) : ?>
                        <a class="navbar-item" href="<?= url_to('dashboard') ?>">Dashboard</a>
                    <?php endif; ?>
                </div>
            </div>
        </nav>
    </div>
</header>
