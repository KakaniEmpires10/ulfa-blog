<!DOCTYPE html>
<html lang="id" data-theme="<?= esc(get_setting('theme_mode', 'light')) ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($this->renderSection('title') ?: 'Masuk') ?></title>
    <meta name="description" content="<?= esc(get_setting('site_description', 'Halaman masuk Ulfa Blog')) ?>">
    <link rel="shortcut icon" href="<?= base_url('favicon.png') ?>" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Caveat:wght@500;600;700&family=Montserrat:wght@500;600;700&family=Open+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="<?= base_url('assets/css/bulma.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/site.css') ?>">
    <?= $this->include('components/app_config') ?>
    <script defer src="<?= base_url('assets/js/globalScript.js') ?>"></script>
    <script defer src="<?= base_url('assets/js/alpineStore.js') ?>"></script>
    <script defer src="<?= base_url('assets/js/alpineData.js') ?>"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.15.3/dist/cdn.min.js"></script>
</head>

<body id="top">
    <?= $this->include('components/navbar') ?>
    <main>
        <?= $this->renderSection('main') ?>
    </main>
    <?= $this->include('components/footer') ?>
</body>

</html>