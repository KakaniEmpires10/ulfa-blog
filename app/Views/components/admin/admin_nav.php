<header class="card-panel is-flex is-align-items-center is-justify-content-space-between">
    <div class="is-flex is-align-items-center">
        <button
            class="button is-small"
            type="button"
            @click="toggleSidebar()"
            :aria-label="isMobile ? (sidebarOpen ? 'Tutup navigasi admin' : 'Buka navigasi admin') : (sidebarCollapsed ? 'Buka sidebar admin' : 'Ringkas sidebar admin')">
            <span class="icon is-small">
                <i
                    class="fa-solid"
                    :class="isMobile ? (sidebarOpen ? 'fa-xmark' : 'fa-bars') : (sidebarCollapsed ? 'fa-angles-right' : 'fa-angles-left')"></i>
            </span>
        </button>
        <span class="admin-topbar-divider" aria-hidden="true"></span>
        <p class="script-label">Area Admin</p>
    </div>
    <div class="admin-topbar-actions">
        <div class="dropdown is-right notif-button" x-data="{ open: false }" @click.outside="open = false" :class="{'is-active': open}">
            <div class="dropdown-trigger">
                <button class="button is-small" @click="open = !open" type="button">
                    <span class="icon is-small">
                        <i class="fa-solid fa-bell"></i>
                    </span>
                </button>
            </div>
            <div class="dropdown-menu" role="menu" style="min-width: 200px;">
                <div class="dropdown-content">
                    <div class="dropdown-item has-text-centered has-text-grey py-4">
                        <p class="is-size-7">Tidak ada notifikasi baru</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="dropdown is-right" x-data="{ open: false }" @click.outside="open = false" :class="{'is-active': open}">
            <div class="dropdown-trigger">
                <button
                    class="button admin-user-chip"
                    @click="open = !open"
                    type="button"
                    aria-haspopup="true"
                    :aria-expanded="open">

                    <?php if (! empty($adminProfile['avatar_path'])) : ?>
                        <img src="<?= render_cover_url(esc($adminProfile['avatar_path'])) ?>" alt="Avatar">
                    <?php else : ?>
                        <span class="admin-user-avatar">
                            <i class="fa-regular fa-user" style="font-size: 0.75rem;"></i>
                        </span>
                    <?php endif; ?>

                    <span class="has-text-weight-bold">
                        <?= esc($adminProfile['display_name'] ?? $adminUser->username ?? 'Admin') ?>
                    </span>
                </button>
            </div>

            <div class="dropdown-menu" role="menu" style="min-width: 240px;">
                <div class="dropdown-content">
                    <div class="dropdown-item py-3">
                        <p class="has-text-weight-bold is-size-6" style="line-height: 1.2;">
                            <?= esc($adminProfile['display_name'] ?? $adminUser->username ?? 'Admin') ?>
                        </p>
                        <p class="has-text-grey is-size-7" style="line-height: 1.2;">
                            <?= esc($adminUser->email ?? 'admin@example.com') ?>
                        </p>
                    </div>

                    <hr class="dropdown-divider">

                    <a href="<?= site_url('admin/settings/profile') ?>" class="dropdown-item">
                        <span class="icon-text">
                            <span class="icon is-small mt-1"><i class="fa-regular fa-circle-user"></i></span>
                            <span>Pengaturan Profil</span>
                        </span>
                    </a>

                    <hr class="dropdown-divider">

                    <a href="<?= site_url('logout') ?>" class="dropdown-item has-text-danger">
                        <span class="icon-text">
                            <span class="icon is-small mt-1"><i class="fa-solid fa-arrow-right-from-bracket"></i></span>
                            <span>Keluar</span>
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>