<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div x-data="{ 
    openModal: false,
    categoryData: { id: '', name: '' } 
}"
    @open-modal.window="
    openModal = true; 
    categoryData = $event.detail ? { ...$event.detail } : { id: '', name: '' };
">
    <section id="categories-filter" class="card-panel mb-4">
        <div class="field" style="max-width: 240px;">
            <label class="label is-small">Filter Nama</label>
            <div class="control has-icons-left">
                <input class="input is-small" type="text" placeholder="cari nama kategori..." name="name" @input.debounce.500ms="$dispatch('filter-categories', { name: $el.value })">
                <span class="icon is-small is-left">
                    <i class="fas fa-search"></i>
                </span>
            </div>
        </div>
    </section>

    <section id="categories-list" class="card-panel">
        <?= $this->include('components/admin/categories/table_categories') ?>
    </section>

    <?= $this->include('components/admin/categories/modal_categories') ?>
</div>

<?= $this->endSection() ?>