<?= $this->extend('layouts/main') ?>

<?php $ckeditorThemeVersion = filemtime(FCPATH . 'assets/css/ckeditor-bulma.css'); ?>

<?= $this->section('page_style_lib') ?>
<link rel="stylesheet" href="<?= base_url('assets/lib/ckeditor5/ckeditor5-content.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/css/ckeditor-bulma.css?v=' . $ckeditorThemeVersion) ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<section class="section page-section">
    <div class="container">
        <div class="columns is-variable is-6">
            <div class="column is-8-desktop">
                <article class="post-detail-card">
                    <header class="post-detail-header">
                        <?php if (! empty($post['primary_category'])) : ?>
                            <a class="script-label post-category-link" href="<?= category_filter_url($post['primary_category']['slug']) ?>"><?= esc($post['primary_category']['name']) ?></a>
                        <?php endif; ?>
                        <h1 class="h2"><?= esc($post['title']) ?></h1>
                        <?= $this->setVar('post', $post)->include('components/post_meta') ?>
                    </header>

                    <div class="post-detail-cover">
                        <img src="<?= esc($post['cover_url']) ?>" alt="<?= esc($post['title']) ?>">
                    </div>

                    <div class="content post-detail-content ck-content">
                        <?= $post['content'] ?>
                    </div>

                    <?php if (! empty($post['tags'])) : ?>
                        <div class="tags">
                            <?php foreach ($post['tags'] as $tag) : ?>
                                <span class="tag is-rounded"><?= esc($tag['name']) ?></span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </article>
            </div>

            <div class="column is-4-desktop">
                <?= $this->include('components/sidebar') ?>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>
