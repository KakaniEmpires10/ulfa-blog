<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<section class="admin-dashboard-stack" x-data="adminDashboard({ endpoint: '<?= esc($dashboardDataUrl, 'js') ?>' })">
    <div class="admin-dashboard-error" x-cloak x-show="error">
        <div>
            <span class="script-label">Dashboard</span>
            <h2>Data belum berhasil dimuat.</h2>
            <p x-text="error"></p>
        </div>
        <button type="button" class="button is-primary is-rounded" @click="fetchData()">
            <span>Muat ulang</span>
            <span class="icon" aria-hidden="true"><i class="fa-solid fa-rotate-right"></i></span>
        </button>
    </div>

    <section class="admin-stat-grid">
        <template x-if="loading">
            <template x-for="item in 6" :key="'stat-skeleton-' + item">
                <article class="admin-stat-card is-skeleton">
                    <span class="admin-skeleton admin-skeleton-pill"></span>
                    <span class="admin-skeleton admin-skeleton-value"></span>
                    <span class="admin-skeleton admin-skeleton-line"></span>
                    <span class="admin-skeleton admin-skeleton-line short"></span>
                </article>
            </template>
        </template>

        <template x-for="(card, index) in stats" :key="'stat-' + index">
            <article class="admin-stat-card" :class="card.tone">
                <div class="admin-stat-head">
                    <span class="admin-stat-label has-text-weight-bold" x-text="card.label"></span>
                    <span class="tag is-rounded" :class="card.badgeTone">
                        <span class="icon is-small" aria-hidden="true"><i :class="card.icon"></i></span>
                        <span x-text="card.badge"></span>
                    </span>
                </div>
                <strong x-text="card.value"></strong>
                <p class="admin-stat-summary" x-text="card.summary"></p>
                <small x-text="card.note"></small>
            </article>
        </template>
    </section>

    <section class="admin-panel-grid">
        <article class="admin-panel">
            <div class="admin-panel-heading">
                <div>
                    <span class="script-label">Tulisan terbaru</span>
                    <h3>Aktivitas konten terakhir</h3>
                </div>
            </div>

            <div class="admin-post-list" x-show="loading">
                <template x-for="item in 5" :key="'post-skeleton-' + item">
                    <div class="admin-post-row is-skeleton-row">
                        <div class="admin-post-copy">
                            <span class="admin-skeleton admin-skeleton-line"></span>
                            <span class="admin-skeleton admin-skeleton-line short"></span>
                        </div>
                        <div class="admin-post-meta">
                            <span class="admin-skeleton admin-skeleton-pill"></span>
                        </div>
                    </div>
                </template>
            </div>

            <div class="admin-post-list" x-cloak x-show="!loading && recentPosts.length">
                <template x-for="(post, index) in recentPosts" :key="'post-' + index">
                    <div class="admin-post-row">
                        <div class="admin-post-copy">
                            <strong><a :href="post.url" target="_blank" rel="noreferrer" x-text="post.title"></a></strong>
                            <p class="is-size-7">
                                <span x-text="post.author_name"></span>
                                <span class="admin-dot">&middot;</span>
                                <span x-text="post.updated_label"></span>
                            </p>
                        </div>
                        <div class="admin-post-meta">
                            <small><span x-text="post.view_count"></span> lihat</small> <br>
                            <span class="tag is-rounded" :class="post.status_tag_class" x-text="post.status_label"></span>
                        </div>
                    </div>
                </template>
            </div>

            <p class="admin-empty-state" x-cloak x-show="!loading && !recentPosts.length">Belum ada aktivitas tulisan yang bisa ditampilkan.</p>
        </article>

        <article class="admin-panel">
            <div class="admin-panel-heading">
                <div>
                    <span class="script-label">Halaman admin</span>
                    <h3>Struktur area kerja yang sudah disiapkan</h3>
                </div>
            </div>

            <div class="admin-page-grid" x-show="loading">
                <template x-for="item in 6" :key="'page-skeleton-' + item">
                    <div class="admin-page-card is-skeleton-row">
                        <span class="admin-skeleton admin-skeleton-icon"></span>
                        <div class="admin-page-copy">
                            <span class="admin-skeleton admin-skeleton-line"></span>
                            <span class="admin-skeleton admin-skeleton-line short"></span>
                        </div>
                    </div>
                </template>
            </div>

            <div class="admin-page-grid" x-cloak x-show="!loading && adminPages.length">
                <template x-for="(page, index) in adminPages" :key="'page-' + index">
                    <a class="admin-page-card" :href="page.url">
                        <span class="admin-page-icon"><i :class="page.icon"></i></span>
                        <div class="admin-page-copy">
                            <strong x-text="page.label"></strong>
                            <p x-text="page.description"></p>
                        </div>
                    </a>
                </template>
            </div>

            <p class="admin-empty-state" x-cloak x-show="!loading && !adminPages.length">Belum ada halaman admin yang bisa ditampilkan.</p>
        </article>
    </section>
</section>
<?= $this->endSection() ?>