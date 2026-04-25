<?php if ($sliderPosts !== []) : ?>
    <section class="hero-slider-section section">
        <div class="container">
            <div class="hero-slider" x-data="heroSlider(<?= count($sliderPosts) ?>)" x-init="start()" @mouseenter="pause()" @mouseleave="start()">
                <div class="hero-slider-track" :style="trackStyle">
                    <?php foreach ($sliderPosts as $index => $post) : ?>
                        <article class="hero-slide" x-cloak>
                        <div class="hero-slide-bg" style="background-image: linear-gradient(rgba(20, 24, 28, 0.35), rgba(20, 24, 28, 0.55)), url('<?= esc($post['cover_url']) ?>');"></div>
                        <div class="hero-slide-content">
                            <div class="hero-overlay-card">
                                <?php if (! empty($post['primary_category'])) : ?>
                                    <a class="script-label hero-category-link" href="<?= category_filter_url($post['primary_category']['slug']) ?>"><?= esc($post['primary_category']['name']) ?></a>
                                <?php endif; ?>
                                <h2><?= esc($post['title']) ?></h2>
                                <p><?= esc(excerpt_text($post['excerpt_display'], 140)) ?></p>
                                <span class="hero-date"><?= esc(blog_date($post['published_at'])) ?></span>
                                <a class="hero-slide-cta" href="<?= site_url('/post/' . $post['slug']) ?>">
                                    Baca selengkapnya
                                    <i class="fa-solid fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                        <a class="hero-slide-link" href="<?= site_url('/post/' . $post['slug']) ?>" aria-label="<?= esc($post['title']) ?>"></a>
                        </article>
                    <?php endforeach; ?>
                </div>

                <button class="hero-nav hero-nav-prev" type="button" @click="prev()" aria-label="Slide sebelumnya">
                    <i class="fa-solid fa-chevron-left"></i>
                </button>
                <button class="hero-nav hero-nav-next" type="button" @click="next()" aria-label="Slide berikutnya">
                    <i class="fa-solid fa-chevron-right"></i>
                </button>

                <div class="hero-dots">
                    <?php foreach ($sliderPosts as $index => $post) : ?>
                        <button type="button" :class="{ 'is-active': active === <?= $index ?> }" @click="goTo(<?= $index ?>)" aria-label="Buka slide <?= $index + 1 ?>"></button>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>
<?php endif; ?>
