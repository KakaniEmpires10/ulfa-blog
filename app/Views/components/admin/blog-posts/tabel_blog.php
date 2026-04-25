<div class="table-container"
    x-data="fetchPosts({ endpoint: '<?= esc($postDataUrl, 'js') ?>' })"
    @apply-filter.window="filters = { ...$event.detail }; currentPage = 1; fetchData()">
    <table class="table is-striped is-fullwidth mb-5">
        <thead>
            <tr>
                <th style="width: 5%;" class="has-text-centered">No.</th>
                <th style="width: 10%;"></th>
                <th style="width: 35%;">Judul</th>
                <th style="width: 15%;">Tag / Kategori</th>
                <th style="width: 10%;">Status</th>
                <th style="width: 10%;" class="has-text-centered">Tanggal</th>
                <th style="width: 15%;" class="has-text-centered">Aksi</th>
            </tr>
        </thead>
        <tbody id="posts-table-body">

            <template x-if="loading">
                <template x-for="i in 4" :key="i">
                    <tr>
                        <td class="is-vcentered has-text-centered">
                            <div class="is-skeleton" style="width: 20px; height: 20px; margin: 0 auto;"></div>
                        </td>
                        <td class="is-vcentered">
                            <div class="is-skeleton" style="width: 64px; height: 48px; border-radius: 4px;"></div>
                        </td>
                        <td class="is-vcentered">
                            <div class="is-skeleton mb-2" style="width: 90%; height: 1.25rem;"></div>
                            <div class="is-skeleton" style="width: 40%; height: 0.75rem;"></div>
                        </td>
                        <td class="is-vcentered">
                            <div class="is-flex" style="gap: 0.25rem;">
                                <div class="is-skeleton" style="width: 40px; height: 1.5rem; border-radius: 4px;"></div>
                                <div class="is-skeleton" style="width: 40px; height: 1.5rem; border-radius: 4px;"></div>
                            </div>
                        </td>
                        <td class="is-vcentered">
                            <div class="is-skeleton" style="width: 60px; height: 1.5rem; border-radius: 4px;"></div>
                        </td>
                        <td class="is-vcentered">
                            <div class="is-skeleton" style="width: 80%; height: 1rem;"></div>
                        </td>
                        <td class="is-vcentered has-text-centered">
                            <div class="is-flex is-justify-content-center" style="gap: 0.5rem;">
                                <div class="is-skeleton" style="width: 32px; height: 2.25rem;"></div>
                                <div class="is-skeleton" style="width: 32px; height: 2.25rem;"></div>
                            </div>
                        </td>
                    </tr>
                </template>
            </template>

            <template x-if="!loading && posts.length === 0 && !error">
                <tr>
                    <td colspan="7" class="has-text-centered has-text-grey py-6">
                        <span class="icon is-large">
                            <i class="fas fa-file-alt fa-2x"></i>
                        </span>
                        <p class="mt-2">Belum ada postingan blog yang ditemukan.</p>
                        <p class="is-size-7">Coba sesuaikan filter atau tambahkan postingan baru.</p>
                    </td>
                </tr>
            </template>

            <template x-if="error">
                <tr>
                    <td colspan="7" class="has-text-centered has-text-danger has-text-weight-semibold py-5">
                        <span class="icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </span>
                        <span x-text="error"></span>
                        <button class="button is-small is-danger is-outlined ml-2" @click="fetchData()">
                            Coba Lagi
                        </button>
                    </td>
                </tr>
            </template>

            <template x-if="!loading && posts.length > 0">
                <template x-for="(post, index) in posts" :key="post.id">
                    <tr>
                        <td class="is-vcentered has-text-centered has-text-weight-semibold" x-text="((currentPage - 1) * perPage) + index + 1""></td>
                        <td class=" is-vcentered">
                            <figure class="image is-4by3">
                                <img :src="$store.helpers.getCoverUrl(post.cover_url)" :alt="post.title" style="border-radius: 4px; object-fit: cover;">
                            </figure>
                        </td>
                        <td class="is-vcentered">
                            <p class="has-text-weight-bold mb-0" x-text="post.title"></p>
                            <p class="is-size-7 has-text-grey">
                                <i class="fas fa-link mr-1"></i> <span x-text="post.slug"></span>
                            </p>
                            <p class="is-size-7 mt-1 is-italic has-text-grey-dark" x-text="post.excerpt_display"></p>
                        </td>
                        <td class="is-vcentered">
                            <div class="tags">
                                <template x-for="cat in post.categories">
                                    <span class="tag is-small" style="background-color: #e3f2fd; color: #1976d2; font-weight: 500;" x-text="cat.name"></span>
                                </template>
                                <template x-for="tag in post.tags">
                                    <span class="tag is-small" style="background-color: #fbe9e7; color: #d84315; font-weight: 500;"" x-text="'#' + tag.name"></span>
                                </template>
                            </div>
                        </td>
                        <td class="is-vcentered">
                            <div class="dropdown is-hoverable">
                                <div class="dropdown-trigger">
                                    <button class="button is-small is-rounded"
                                        :class="{
                                            'is-loading': updatingStatus === post.id,
                                            'tag-status-publish': post.status === 'published',
                                            'tag-status-draft': post.status === 'draft'
                                        }"
                                        aria-haspopup="true"
                                        aria-controls="dropdown-menu">
                                        <span x-text="post.status.toUpperCase()"></span>
                                        <span class="icon is-small">
                                            <i class="fas fa-angle-down" aria-hidden="true"></i>
                                        </span>
                                    </button>
                                </div>
                                <div class="dropdown-menu" id="dropdown-menu" role="menu">
                                    <div class="dropdown-content" style="min-width: 120px;">
                                        <a href="javascript:void(0)" class="dropdown-item is-size-7"
                                            @click.prevent="updateStatus(post.id, 'published', '<?= site_url('admin/posts') ?>/' + post.id + '/status')"
                                            :class="{'is-active': post.status === 'published'}">
                                            <span class="icon has-text-success"><i class="fas fa-check-circle"></i></span>
                                            Publish
                                        </a>
                                        <a href="javascript:void(0)" class="dropdown-item is-size-7"
                                            @click.prevent="updateStatus(post.id, 'draft', '<?= site_url('admin/posts') ?>/' + post.id + '/status')"
                                            :class="{'is-active': post.status === 'draft'}">
                                            <span class="icon has-text-warning"><i class="fas fa-edit"></i></span>
                                            Draft
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td
                            class="is-vcentered has-text-centered has-text-weight-semibold is-size-7"
                            x-text="new Date(post.created_at).toLocaleDateString('id-ID', {
                                year: 'numeric',
                                month: 'long',
                                day: 'numeric'
                            })"></td>
                        <td class="is-vcentered has-text-centered">
                            <div class="buttons is-centered" style="gap: 0.25rem;">
                                <a :href="'<?= site_url('post/') ?>' + post.slug" target="_blank" rel="noopener noreferrer"
                                    class="button is-icon is-small is-success"
                                    @mouseenter="$store.tooltip.show($el, 'Lihat Post', 'top')"
                                    @mouseleave="$store.tooltip.hide()">
                                    <span class="icon is-small"><i class="fas fa-eye"></i></span>
                                </a>
                                <a :href="'<?= site_url('admin/posts/') ?>' + post.id + '/edit'"
                                    class="button is-icon is-small is-primary"
                                    @mouseenter="$store.tooltip.show($el, 'Edit Post', 'top')"
                                    @mouseleave="$store.tooltip.hide()">
                                    <span class="icon is-small"><i class="fas fa-pencil-alt"></i></span>
                                </a>
                                <button class="button is-icon is-small is-danger"
                                    @click="$store.deleteModal.open({
                                        title: 'Hapus Postingan',
                                        description: `Anda akan menghapus postingan '${post.title}'. Tindakan ini tidak dapat dibatalkan.`,
                                        deleteLabel: 'Ya, Hapus Post',
                                        url: `<?= site_url('admin/posts/') ?>${post.id}`
                                    })"
                                    @mouseenter="$store.tooltip.show($el, 'Hapus Post', 'top')"
                                    @mouseleave="$store.tooltip.hide()">
                                    <span class="icon is-small"><i class="fas fa-trash"></i></span>
                                </button>
                            </div>
                        </td>
                    </tr>
                </template>
            </template>
        </tbody>
    </table>

    <template x-if="totalPages > 1">
        <nav class="pagination is-small is-centered mt-5" role="navigation" aria-label="pagination">
            <button type="button" class="pagination-previous"
                :disabled="currentPage === 1"
                @click="setPage(currentPage - 1)">
                <i class="fas fa-chevron-left"></i>
            </button>

            <button type="button" class="pagination-next"
                :disabled="currentPage === totalPages"
                @click="setPage(currentPage + 1)">
                <i class="fas fa-chevron-right"></i>
            </button>

            <ul class="pagination-list">
                <template x-for="page in getPaginationRange()" :key="Math.random()">
                    <li>
                        <template x-if="page !== '...'">
                            <a class="pagination-link"
                                :class="{'is-current': page === currentPage}"
                                @click.prevent="setPage(page)"
                                x-text="page"></a>
                        </template>

                        <template x-if="page === '...'">
                            <span class="pagination-ellipsis">&hellip;</span>
                        </template>
                    </li>
                </template>
            </ul>
        </nav>
    </template>

    <div class="has-text-centered is-size-7 has-text-grey mt-2">
        Menampilkan halaman <span class="has-text-weight-bold" x-text="currentPage"></span>
        dari <span class="has-text-weight-bold" x-text="totalPages"></span> total halaman.
    </div>
</div>