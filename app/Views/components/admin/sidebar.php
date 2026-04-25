<aside
    class="admin-sidebar"
    :class="{ 'is-open': sidebarOpen, 'is-collapsed': sidebarCollapsed && !isMobile }">
    <div class="admin-sidebar-inner custom-scrollbar">
        <div class="admin-sidebar-header">
            <a class="admin-brand" href="<?= site_url('/admin') ?>"
                @mouseenter="if(sidebarCollapsed && !isMobile) $store.tooltip.show($el, '<?= esc(get_setting('site_name', 'Ulfa Blog'), 'js') ?>', 'right', 'is-link')"
                @mouseleave="$store.tooltip.hide()"
                @focus="if(sidebarCollapsed && !isMobile) $store.tooltip.show($el, '<?= esc(get_setting('site_name', 'Ulfa Blog'), 'js') ?>', 'right', 'is-link')"
                @blur="$store.tooltip.hide()">
                <span class="admin-brand-mark">
                    <i class="fa-solid fa-feather-pointed"></i>
                </span>
                <strong class="admin-brand-title"><?= esc(get_setting('site_name', 'Ulfa Blog')) ?></strong>
            </a>
        </div>

        <nav class="admin-nav">
            <?php foreach ($adminNavItems as $item) : ?>
                <a class="admin-nav-link <?= admin_nav_is_active($item['path']) ? 'is-active' : '' ?>"
                    href="<?= esc($item['url']) ?>"
                    @mouseenter="if(sidebarCollapsed && !isMobile) $store.tooltip.show($el, '<?= esc($item['label'], 'js') ?>', 'right', 'is-link')"
                    @mouseleave="$store.tooltip.hide()"
                    @focus="if(sidebarCollapsed && !isMobile) $store.tooltip.show($el, '<?= esc($item['label'], 'js') ?>', 'right', 'is-link')"
                    @blur="$store.tooltip.hide()">
                    <span class=" admin-nav-icon"><i class="<?= esc($item['icon']) ?>"></i></span>
                    <span class="admin-nav-copy" x-show="!sidebarCollapsed || isMobile">
                        <strong><?= esc($item['label']) ?></strong>
                        <small><?= esc($item['description']) ?></small>
                    </span>
                </a>
            <?php endforeach; ?>
        </nav>

        <div class="admin-sidebar-footer">
            <a class="admin-sidebar-link" href="<?= site_url('/') ?>"
                @mouseenter="if(sidebarCollapsed && !isMobile) $store.tooltip.show($el, 'Lihat situs publik', 'right', 'is-link')"
                @mouseleave="$store.tooltip.hide()"
                @focus="if(sidebarCollapsed && !isMobile) $store.tooltip.show($el, 'Lihat situs publik', 'right', 'is-link')"
                @blur="$store.tooltip.hide()">
                <i class="fa-solid fa-arrow-left"></i>
                <span class="admin-sidebar-link-label">Lihat situs publik</span>
            </a>
            <a class="admin-sidebar-link" href="<?= url_to('logout') ?>"
                @mouseenter="if(sidebarCollapsed && !isMobile) $store.tooltip.show($el, 'Keluar', 'right', 'is-link')"
                @mouseleave="$store.tooltip.hide()"
                @focus="if(sidebarCollapsed && !isMobile) $store.tooltip.show($el, 'Keluar', 'right', 'is-link')"
                @blur="$store.tooltip.hide()">
                <i class="fa-solid fa-arrow-right-from-bracket"></i>
                <span class="admin-sidebar-link-label">Keluar</span>
            </a>
        </div>
    </div>
</aside>