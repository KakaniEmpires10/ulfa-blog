<div class="modal" :class="{ 'is-active': $store.deleteModal.isOpen }" @keydown.escape.window="$store.deleteModal.close()">
    <div class="modal-background"></div>
    <div class="modal-card">
        <form id="global-delete-form" method="POST">
            <?= csrf_field() ?>
            <input type="hidden" name="_method" value="DELETE">

            <header class="modal-card-head has-background-danger-light">
                <p class="modal-card-title has-text-danger" x-text="$store.deleteModal.title"></p>
                <button class="delete" type="button" @click="$store.deleteModal.close()"></button>
            </header>

            <section class="modal-card-body">
                <p class="is-size-5 has-text-weight-medium" x-text="$store.deleteModal.description"></p>
                <p class="has-text-grey is-size-6 mt-2">Data yang dihapus tidak dapat dipulihkan kembali.</p>
            </section>

            <footer class="modal-card-foot is-justify-content-end" style="gap: 0.5rem;">
                <button type="button" class="button" @click="$store.deleteModal.close()" :disabled="$store.deleteModal.isLoading">Batal</button>
                <button
                    type="button"
                    class="button is-danger"
                    :class="{ 'is-loading': $store.deleteModal.isLoading }"
                    @click="$store.deleteModal.confirm()">
                    <span class="icon"><i class="fas fa-trash"></i></span>
                    <span x-text="$store.deleteModal.deleteLabel"></span>
                </button>
            </footer>
        </form>
    </div>
</div>