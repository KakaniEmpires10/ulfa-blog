<?= $this->extend('components/layouts/main/layout') ?>

<?= $this->section('title') ?><?= $title ?><?= $this->endSection() ?>

<?= $this->section('main') ?>
<!-- Hero Section -->
<section class="hero is-medium">
    <div class="hero-body" x-data="{ view : false, name: 'Ulfa' }">
        <div class="container has-text-centered">

            <h1 class="title is-1 has-text-white">Selamat Datang di Blog Teman</h1>
            <h2 class="subtitle is-4 has-text-white">
                Berbagi pengetahuan, tips, dan inspirasi setiap hari
            </h2>

            <button class="button is-white is-medium is-outlined"
                @click="view = !view">
                Mulai Membaca
            </button>

            <div class="notification is-info is-dark mt-4"
                x-show="view"
                x-transition>
                Halo, <strong x-text="name"></strong>!
                Selamat menikmati artikel-artikel kami.
            </div>

        </div>
    </div>
</section>

<!-- Featured Article -->
<section class="section">
    <div class="container">
        <h2 class="title is-3 has-text-centered mb-6">
            <i class="fas fa-star"></i> Artikel Unggulan
        </h2>
        <div class="box featured-article">
            <div class="columns is-vcentered">
                <div class="column is-6">
                    <figure class="image">
                        <img src="<?= $featured_article['image'] ?>" alt="<?= $featured_article['title'] ?>" style="border-radius: 8px;">
                    </figure>
                </div>
                <div class="column is-6">
                    <span class="tag is-warning is-light category-tag mb-3">
                        <?= $featured_article['category'] ?>
                    </span>
                    <h3 class="title is-3 has-text-white mb-3">
                        <?= $featured_article['title'] ?>
                    </h3>
                    <p class="subtitle is-5 has-text-white mb-4">
                        <?= $featured_article['excerpt'] ?>
                    </p>
                    <div class="level">
                        <div class="level-left">
                            <div class="level-item">
                                <span class="icon-text has-text-white">
                                    <span class="icon">
                                        <i class="fas fa-user"></i>
                                    </span>
                                    <span><?= $featured_article['author'] ?></span>
                                </span>
                            </div>
                            <div class="level-item">
                                <span class="icon-text has-text-white">
                                    <span class="icon">
                                        <i class="fas fa-calendar"></i>
                                    </span>
                                    <span><?= $featured_article['date'] ?></span>
                                </span>
                            </div>
                            <div class="level-item">
                                <span class="icon-text has-text-white">
                                    <span class="icon">
                                        <i class="fas fa-clock"></i>
                                    </span>
                                    <span><?= $featured_article['reading_time'] ?></span>
                                </span>
                            </div>
                        </div>
                    </div>
                    <button class="button is-white is-medium">
                        Baca Selengkapnya <i class="fas fa-arrow-right ml-2"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Latest Articles -->
<section class="section">
    <div class="container">
        <h2 class="title is-3 has-text-centered mb-6">
            <i class="fas fa-newspaper"></i> Artikel Terbaru
        </h2>
        <div class="columns is-multiline">
            <?php foreach ($articles as $article): ?>
                <div class="column is-4">
                    <div class="card article-card">
                        <div class="card-image">
                            <figure class="image is-4by3">
                                <img src="<?= $article['image'] ?>" alt="<?= $article['title'] ?>">
                            </figure>
                        </div>
                        <div class="card-content">
                            <span class="tag is-primary is-light category-tag mb-3">
                                <?= $article['category'] ?>
                            </span>
                            <h3 class="title is-5 mb-3">
                                <?= $article['title'] ?>
                            </h3>
                            <p class="content">
                                <?= $article['excerpt'] ?>
                            </p>
                            <div class="level is-mobile">
                                <div class="level-left">
                                    <div class="level-item">
                                        <span class="icon-text is-size-7">
                                            <span class="icon">
                                                <i class="fas fa-user"></i>
                                            </span>
                                            <span><?= $article['author'] ?></span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="level is-mobile">
                                <div class="level-left">
                                    <div class="level-item">
                                        <span class="icon-text is-size-7">
                                            <span class="icon">
                                                <i class="fas fa-calendar"></i>
                                            </span>
                                            <span><?= $article['date'] ?></span>
                                        </span>
                                    </div>
                                    <div class="level-item">
                                        <span class="icon-text is-size-7">
                                            <span class="icon">
                                                <i class="fas fa-clock"></i>
                                            </span>
                                            <span><?= $article['reading_time'] ?></span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <footer class="card-footer">
                            <a href="/artikel/<?= $article['id'] ?>" class="card-footer-item">
                                <strong>Baca Artikel</strong>
                            </a>
                        </footer>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="has-text-centered mt-6">
            <button class="button is-primary is-medium">
                Lihat Semua Artikel <i class="fas fa-arrow-right ml-2"></i>
            </button>
        </div>
    </div>
</section>

<!-- Newsletter Section -->
<section class="section">
    <div class="container">
        <div class="columns is-centered">
            <div class="column is-6 has-text-centered">
                <h2 class="title is-4">
                    <i class="fas fa-envelope-open-text"></i> Berlangganan Newsletter
                </h2>
                <p class="subtitle is-6 mb-5">
                    Dapatkan artikel terbaru langsung ke email Anda setiap minggu
                </p>
                <div class="field has-addons has-addons-centered">
                    <div class="control is-expanded">
                        <input class="input is-medium" type="email" placeholder="Email Anda">
                    </div>
                    <div class="control">
                        <button class="button is-primary is-medium">
                            Berlangganan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>