<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<section class="admin-placeholder-card">
    <span class="script-label">Segera dikerjakan</span>
    <h2><?= esc($pageTitle ?? 'Halaman admin') ?></h2>
    <p><?= esc($pageDescription ?? 'Halaman ini sudah disiapkan dan siap kita isi fitur satu per satu pada langkah berikutnya.') ?></p>

    <div class="admin-placeholder-actions">
        <a class="button is-primary is-rounded" href="<?= site_url('/admin') ?>">
            <span>Kembali ke dashboard</span>
            <span class="icon" aria-hidden="true"><i class="fa-solid fa-house"></i></span>
        </a>
    </div>
</section>
<?= $this->endSection() ?>
