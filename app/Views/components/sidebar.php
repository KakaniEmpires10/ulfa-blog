<aside class="sidebar-stack">
    <?php if (! empty($profile)) : ?>
        <section class="sidebar-widget">
            <h3 class="widget-title"><span>Tentang Penulis</span></h3>
            <div class="sidebar-profile">
                <img src="<?= render_cover_url(esc($profile['avatar_path'])) ?>" alt="<?= esc($profile['display_name']) ?>">
                <div>
                    <h4 class="has-text-weight-semibold"><?= esc($profile['display_name']) ?></h4>
                    <p><?= esc(excerpt_text($profile['bio'], 110)) ?></p>
                    <a class="sidebar-link-with-icon cta-link mt-2" href="<?= site_url('/about') ?>">
                        Lihat selengkapnya
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <section class="sidebar-widget">
        <h3 class="widget-title"><span>Postingan Populer</span></h3>
        <div class="sidebar-list">
            <?php foreach ($popularPosts as $popularPost) : ?>
                <a class="sidebar-post" href="<?= site_url('/post/' . $popularPost['slug']) ?>">
                    <img src="<?= render_cover_url(esc($popularPost['cover_url'])) ?>" alt="<?= esc($popularPost['title']) ?>">
                    <div>
                        <strong><?= esc($popularPost['title']) ?></strong>
                        <small><?= esc(blog_date($popularPost['published_at'])) ?></small>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="sidebar-widget">
        <h3 class="widget-title"><span>Kategori</span></h3>
        <ul class="category-list">
            <?php foreach ($sidebarCategories as $category) : ?>
                <li>
                    <a class="category-filter-link <?= ! empty($activeCategory) && $activeCategory['slug'] === $category['slug'] ? 'is-active' : '' ?>" href="<?= category_filter_url($category['slug']) ?>">
                        <span><?= esc($category['name']) ?></span>
                        <span><?= esc((string) $category['post_count']) ?></span>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>
</aside>
