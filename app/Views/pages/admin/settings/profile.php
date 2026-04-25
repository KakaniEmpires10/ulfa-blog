<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<?= $this->include('components/admin/settings/tabs_setting') ?>

<section id="profile_setting">
    <?= $this->include('components/admin/settings/profile_setting_form') ?>
</section>

<?= $this->endSection() ?>