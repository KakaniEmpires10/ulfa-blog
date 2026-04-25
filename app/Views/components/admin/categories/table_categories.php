<div
    class="table-container"
    x-data="fetchCategories({ endpoint: '<?= esc($categoriesDataUrl, 'js') ?>' })"
    @filter-categories.window="filters.name = $event.detail.name; fetchCategories()"
>
    <table class="table is-striped is-fullwidth mb-5">
        <thead>
            <tr>
                <th style="width: 5%;" class="has-text-centered">No.</th>
                <th style="width: 75%;">Nama Kategori</th>
                <th style="width: 20%;" class="has-text-centered">Aksi</th>
            </tr>
        </thead>
        <tbody id="categories-table-body">

            <template x-if="loading">
                <template x-for="i in 4" :key="i">
                    <tr>
                        <td class="is-vcentered has-text-centered">
                            <div class="is-skeleton" style="width: 20px; height: 20px; margin: 0 auto;"></div>
                        </td>
                        <td class="is-vcentered">
                            <div class="is-skeleton" style="width: 80%; height: 2rem;"></div>
                        </td>
                        <td class="is-vcentered has-text-centered">
                            <div class="is-flex is-justify-content-center" style="gap: 0.5rem;">
                                <div class="is-skeleton" style="width: 32px;height: 2.25rem;"></div>
                                <div class="is-skeleton" style="width: 32px;height: 2.25rem;"></div>
                            </div>
                        </td>
                    </tr>
                </template>
            </template>

            <template x-if="!loading && categories.length === 0 && !error">
                <tr>
                    <td colspan="3" class="has-text-centered has-text-grey py-5">
                        <span class="icon">
                            <i class="fas fa-info-circle"></i>
                        </span>
                        <span>Belum ada kategori yang tersedia.</span>
                    </td>
                </tr>
            </template>

            <template x-if="!loading && categories.length > 0">
                <template x-for="(category, index) in categories" :key="category.id">
                    <tr>
                        <td class="has-text-centered has-text-weight-semibold" x-text="index + 1"></td>
                        <td x-text="category.name"></td>
                        <td class="has-text-centered">
                            <button
                                class="button is-icon is-small is-primary"
                                @click="$dispatch('open-modal', category)"
                                @mouseenter="$store.tooltip.show($el, 'Edit', 'top')"
                                @mouseleave="$store.tooltip.hide()">
                                <span class="icon is-small"><i class="fas fa-pencil-alt"></i></span>
                            </button>
                            <button
                                class="button is-icon is-small is-danger"
                                @click="$store.deleteModal.open({
                                    title: 'Hapus Kategori',
                                    description: `Anda akan menghapus kategori '${category.name}'. Postingan dengan kategori ini akan kehilangan referensinya.`,
                                    deleteLabel: 'Ya, Hapus Kategori',
                                    url: `<?= site_url('admin/categories') ?>/${category.id}`
                                })"
                                @mouseenter="$store.tooltip.show($el, 'Hapus', 'top')"
                                @mouseleave="$store.tooltip.hide()">
                                <span class="icon is-small"><i class="fas fa-trash"></i></span>
                            </button>
                        </td>
                    </tr>
                </template>
            </template>

            <template x-if="error">
                <tr>
                    <td colspan="3" class="has-text-centered has-text-danger has-text-weight-semibold py-5">
                        <span class="icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </span>
                        <span x-text="error"></span>
                    </td>
                </tr>
            </template>
        </tbody>

    </table>
</div>