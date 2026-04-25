<article class="post-card">
    <a class="post-card-media" href="<?= site_url('/post/' . $post['slug']) ?>">
        <img src="<?= esc($post['cover_url']) ?>" alt="<?= esc($post['title']) ?>">
    </a>
    <div class="post-card-body">
        <?php if (! empty($post['primary_category'])) : ?>
            <a class="script-label post-category-link" href="<?= category_filter_url($post['primary_category']['slug']) ?>"><?= esc($post['primary_category']['name']) ?></a>
        <?php endif; ?>
        <h3 class="post-card-title">
            <a href="<?= site_url('/post/' . $post['slug']) ?>"><?= esc($post['title']) ?></a>
        </h3>
        <?= $this->setVar('post', $post)->include('components/post_meta') ?>
        <p class="post-card-excerpt"><?= esc($post['excerpt_display']) ?></p>
        <a class="post-card-link cta-link" href="<?= site_url('/post/' . $post['slug']) ?>">
            Baca selengkapnya
            <i class="fa-solid fa-arrow-right"></i>
        </a>
    </div>
</article>
