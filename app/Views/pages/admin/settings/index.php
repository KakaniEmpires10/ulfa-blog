<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<?= $this->include('components/admin/settings/tabs_setting') ?>

<section id="general_setting">
    <?= $this->include('components/admin/settings/general_setting_form') ?>
</section>

<?= $this->endSection() ?>