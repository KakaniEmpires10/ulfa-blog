<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<?= $this->include('components/home/hero_slider') ?>

<section class="section page-section">
    <div class="container">
        <div class="section-heading">
            <span class="script-label"><?= ! empty($activeCategory) ? 'Kategori Pilihan' : 'Tulisan Terbaru' ?></span>
            <h1><?= ! empty($activeCategory) ? 'Tulisan dalam kategori ' . esc($activeCategory['name']) : 'Catatan dari perjalanan, rak buku, film, dan hidup sehari-hari.' ?></h1>
            <p>
                <?= ! empty($activeCategory)
                    ? 'Sedang menampilkan tulisan yang difilter berdasarkan kategori yang kamu pilih.'
                    : 'Slider utama menampilkan postingan ' . ($sliderSource === 'popular' ? 'paling populer' : 'terbaru') . ', lalu daftar di bawahnya menampilkan tulisan terbaru.' ?>
            </p>
            <?php if (! empty($activeCategory)) : ?>
                <a class="clear-category-link" href="<?= site_url('/') ?>">
                    Lihat semua tulisan
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            <?php endif; ?>
        </div>

        <div class="columns is-desktop is-variable is-6">
            <div class="column is-8-desktop">
                <div class="post-list">
                    <?php foreach ($posts as $post) : ?>
                        <?= $this->setVar('post', $post)->include('components/post_card') ?>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="column is-4-desktop">
                <?= $this->include('components/sidebar') ?>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>
