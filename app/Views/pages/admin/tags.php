<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div x-data="{ 
    openModal: false,
    tagData: { id: '', name: '' } 
}"
    @open-modal.window="
    openModal = true; 
    tagData = $event.detail ? { ...$event.detail } : { id: '', name: '' };
">
    <section id="tags-filter" class="card-panel mb-4">
        <div class="field" style="max-width: 240px;">
            <label class="label is-small">Filter Nama</label>
            <div class="control has-icons-left">
                <input class="input is-small" type="text" placeholder="cari nama tag..." name="name" @input.debounce.500ms="$dispatch('filter-tags', { name: $el.value })">
                <span class="icon is-small is-left">
                    <i class="fas fa-search"></i>
                </span>
            </div>
        </div>
    </section>

    <section id="tags-list" class="card-panel">
        <?= $this->include('components/admin/tags/table_tags') ?>
    </section>

    <?= $this->include('components/admin/tags/modal_tags') ?>
</div>

<?= $this->endSection() ?>