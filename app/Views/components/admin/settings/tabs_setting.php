<div class="tabs is-centered mt-4">
    <ul>
        <li class="<?= url_is('admin/settings') ? 'is-active' : '' ?>">
            <a href="<?= url_to('settings') ?>">
                <span class="icon is-small"><i class="fas fa-gears" aria-hidden="true"></i></span>
                <span>Pengaturan Umum</span>
            </a>
        </li>
        <li class="<?= url_is('admin/settings/image-profile') ? 'is-active' : '' ?>">
            <a href="<?= url_to('settings_image') ?>">
                <span class="icon is-small"><i class="fas fa-image" aria-hidden="true"></i></span>
                <span>Pengaturan Gambar</span>
            </a>
        </li>
        <li class="<?= url_is('admin/settings/profile') ? 'is-active' : '' ?>">
            <a href="<?= url_to('settings_profile') ?>">
                <span class="icon is-small"><i class="fas fa-address-book" aria-hidden="true"></i></span>
                <span>Pengaturan Profil</span>
            </a>
        </li>
    </ul>
</div>
