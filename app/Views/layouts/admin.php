<!DOCTYPE html>
<html lang="id" data-theme="<?= esc(get_setting('theme_mode', 'light')) ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc(($title ?? 'Admin') . ' | ' . get_setting('site_name', 'Ulfa Blog')) ?></title>
    <meta name="description" content="<?= esc($pageDescription ?? 'Area admin Ulfa Blog') ?>">
    <link rel="shortcut icon" href="<?= base_url('favicon.png') ?>" type="image/x-icon">
    <link rel="icon" href="<?= base_url('favicon.png') ?>" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Caveat:wght@500;600;700&family=Montserrat:wght@500;600;700&family=Open+Sans:wght@400;500;600&display=swap" rel="stylesheet">

    <meta name="csrf-token" content="<?= csrf_hash() ?>">
    <meta name="csrf-name" content="<?= csrf_token() ?>">

    <!-- css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="<?= base_url('assets/css/bulma.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/lib/ButterPop/butterpop.css') ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/airbnb.css">
    <?= $this->renderSection('page_style_lib') ?>
    <link rel="stylesheet" href="<?= base_url('assets/css/site.css') ?>">

    <?= $this->include('components/app_config') ?>
    <?= $this->renderSection('page_style') ?>

    <!-- js -->
    <script defer src="https://cdn.jsdelivr.net/npm/@floating-ui/core@1.7.4"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/@floating-ui/dom@1.7.5"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>
    <script defer src="<?= base_url('assets/lib/ButterPop/butterpop.js') ?>"></script>
    <?= $this->renderSection('page_script_lib') ?>
    <script defer src="<?= base_url('assets/js/globalScript.js') ?>"></script>
    <script defer src="<?= base_url('assets/js/alpineStore.js') ?>"></script>
    <script defer src="<?= base_url('assets/js/alpineData.js') ?>"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.15.11/dist/cdn.min.js"></script>
</head>

<body class="admin-shell" x-data="adminShell" @keydown.escape.window="closeSidebar()">
    <div
        class="admin-layout"
        data-variant="inset"
        :data-state="state()"
        :data-collapsible="sidebarCollapsed && !isMobile ? 'icon' : ''"
        :class="{ 'is-sidebar-collapsed': sidebarCollapsed && !isMobile }">
        <?= $this->include('components/admin/sidebar') ?>

        <div class="admin-main">
            <?= $this->include('components/admin/admin_nav') ?>
            <?= $this->include('components/admin/panel_header') ?>

            <main class="admin-content">
                <?php if (session('error')) : ?>
                    <?= view('components/notification', [
                        'variant' => 'danger',
                        'label'   => 'Gagal',
                        'message' => session('error'),
                        'icon'    => 'fa-solid fa-circle-exclamation',
                    ]) ?>
                <?php endif; ?>

                <?php if (session('warning')) : ?>
                    <?= view('components/notification', [
                        'variant' => 'warning',
                        'label'   => 'Peringatan',
                        'message' => session('warning'),
                        'icon'    => 'fa-solid fa-circle-exclamation',
                    ]) ?>
                <?php endif; ?>

                <?php if (session('success')) : ?>
                    <?= view('components/notification', [
                        'variant' => 'success',
                        'label'   => 'Berhasil',
                        'message' => session('success'),
                        'icon'    => 'fa-solid fa-circle-check',
                    ]) ?>
                <?php endif; ?>

                <?= $this->renderSection('content') ?>

                <?= $this->include('components/modal_delete') ?>
            </main>
        </div>
    </div>

    <button class="admin-overlay" type="button" x-show="isMobile && sidebarOpen" x-cloak @click="closeSidebar()" aria-label="Tutup panel navigasi"></button>

    <?= $this->renderSection('page_script') ?>
</body>

</html>