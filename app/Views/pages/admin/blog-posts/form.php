<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<section id="form-post">
    <?= $this->include('components/admin/blog-posts/form') ?>
</section>
<?= $this->endSection('content') ?>