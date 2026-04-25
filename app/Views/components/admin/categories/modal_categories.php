<div class="modal" :class="{ 'is-active': openModal }" @keydown.escape.window="openModal = false">
    <div class="modal-background"></div>

    <div class="modal-card">
        <form :action="categoryData.id ? '<?= site_url('admin/categories') ?>/' + categoryData.id : '<?= site_url('admin/categories') ?>'"
            method="POST"
            x-data="{ isLoading: false }"
            @submit="isLoading = true">

            <?= csrf_field() ?>

            <input type="hidden" name="_method" :value="categoryData.id ? 'PUT' : 'POST'">

            <header class="modal-card-head">
                <p class="modal-card-title" x-text="categoryData.id ? 'Edit Kategori' : 'Tambah Kategori Baru'"></p>
                <button class="delete" type="button" aria-label="close" @click="openModal = false"></button>
            </header>

            <section class="modal-card-body">
                <div class="field">
                    <label class="label">Nama Kategori</label>
                    <div class="control">
                        <input class="input" type="text" placeholder="Perjanjian..." name="name" x-model="categoryData.name" required>
                    </div>
                </div>
            </section>

            <footer class="modal-card-foot is-justify-content-end" style="gap: 0.5rem;">
                <button type="button" class="button" @click="openModal = false">Batal</button>
                <button
                    class="button is-link"
                    :class="{ 'is-loading': isLoading }"
                    :disabled="isLoading">
                    <span class="icon">
                        <i class="fas fa-save"></i>
                    </span>
                    <span x-text="categoryData.id ? 'Update' : 'Simpan'"></span>
                </button>
            </footer>
        </form>
    </div>
</div>