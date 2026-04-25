<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<section class="section page-section">
    <div class="container">
        <div class="title-bordered mb-5 is-flex is-align-items-center">
            <h1 class="h4"><?= esc($profile['about_heading'] ?? 'Halo, saya Ulfa') ?></h1>
            <?php if (! empty($profile['social_links'])) : ?>
                <ul class="list-inline social-icons ml-auto mr-3 social-icons-inline">
                    <?php foreach ($profile['social_links'] as $platform => $link) : ?>
                        <li class="list-inline-item">
                            <a href="<?= esc($link) ?>" target="_blank" rel="noreferrer">
                                <i class="<?= esc(social_icon_class($platform)) ?>"></i>
                                <span><?= esc(social_label($platform)) ?></span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>

        <img src="<?= esc($profile['cover_image_path'] ?? $profile['avatar_path']) ?>" class="mb-4 is-100 rounded-theme author-cover" alt="<?= esc($profile['display_name'] ?? 'Penulis') ?>">

        <div class="columns is-variable is-6">
            <div class="column is-4-desktop">
                <div class="author-summary-card">
                    <img src="<?= esc($profile['avatar_path']) ?>" alt="<?= esc($profile['display_name']) ?>">
                    <h2><?= esc($profile['display_name']) ?></h2>
                    <div class="profile-divider"></div>
                    <p><?= esc($profile['bio']) ?></p>
                </div>
            </div>

            <div class="column is-8-desktop">
                <div class="content about-content-block">
                    <p><?= esc($profile['about_content'] ?? '') ?></p>
                    <div class="quote">
                        <i class="fa-solid fa-quote-left"></i>
                        <div>
                            <p><?= esc($profile['quote_text'] ?? '') ?></p>
                            <span class="quote-by">- <?= esc($profile['display_name'] ?? 'Ulfa') ?></span>
                        </div>
                    </div>
                    <p>Ulfa Blog dirancang sebagai ruang menulis yang personal, rapi, dan cukup fleksibel untuk terus tumbuh menjadi template yang bisa dipakai lagi pada proyek berikutnya.</p>
                </div>
            </div>
        </div>

        <div class="section-heading recent-posts-heading">
            <span class="script-label">Tulisan Terbaru</span>
            <h2>Tulisan terbaru dari <?= esc($profile['display_name'] ?? 'Ulfa') ?></h2>
        </div>

        <div class="recent-post-grid">
            <?php foreach ($recentPosts as $post) : ?>
                <div class="recent-post-grid-item">
                    <?= $this->setVar('post', $post)->include('components/post_card') ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?= $this->endSection() ?>
