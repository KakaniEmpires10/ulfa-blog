<ul class="post-meta-list">
    <li>
        <i class="fa-regular fa-user"></i>
        <a href="<?= site_url('/about') ?>"><?= esc($post['author_name']) ?></a>
    </li>
    <li>
        <i class="fa-regular fa-calendar"></i>
        <span><?= esc(blog_date($post['published_at'] ?? null)) ?></span>
    </li>
    <?php if (! empty($post['primary_category'])) : ?>
        <li>
            <i class="fa-regular fa-folder-open"></i>
            <a href="<?= category_filter_url($post['primary_category']['slug']) ?>"><?= esc($post['primary_category']['name']) ?></a>
        </li>
    <?php endif; ?>
    <li>
        <i class="fa-regular fa-clock"></i>
        <span><?= esc($post['reading_time']) ?></span>
    </li>
</ul>
