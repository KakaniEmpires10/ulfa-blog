<div class="tabs is-centered mt-4">
    <ul>
        <li class="<?= url_is('admin/settings') ? 'is-active' : '' ?>">
            <a href="<?= site_url('/admin/settings') ?>">
                <span class="icon is-small"><i class="fas fa-gears" aria-hidden="true"></i></span>
                <span>Pengaturan Umum</span>
            </a>
        </li>
        <li class="<?= url_is('admin/settings/image-profile') ? 'is-active' : '' ?>">
            <a href="<?= site_url('/admin/settings/image-profile') ?>">
                <span class="icon is-small"><i class="fas fa-image" aria-hidden="true"></i></span>
                <span>Pengaturan Logo</span>
            </a>
        </li>
        <li class="<?= url_is('admin/settings/profile') ? 'is-active' : '' ?>">
            <a href="<?= site_url('/admin/settings/profile') ?>">
                <span class="icon is-small"><i class="fas fa-address-book" aria-hidden="true"></i></span>
                <span>Pengaturan Profil</span>
            </a>
        </li>
    </ul>
</div>