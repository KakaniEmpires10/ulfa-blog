<?php
$isEdit = isset($post);
$actionUrl = $isEdit ? site_url('admin/posts/update/' . $post['id']) : site_url('admin/posts');
$ckeditorThemeVersion = filemtime(FCPATH . 'assets/css/ckeditor-bulma.css');
$ckeditorScriptVersion = filemtime(FCPATH . 'assets/js/adminPostEditor.js');
?>

<?= $this->section('page_style_lib') ?>
<link rel="stylesheet" href="<?= base_url('assets/lib/ckeditor5/ckeditor5.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/css/ckeditor-bulma.css?v=' . $ckeditorThemeVersion) ?>">
<?= $this->endSection() ?>

<form
    action="<?= $actionUrl ?>"
    method="post"
    class="is-flex is-flex-direction-column"
    style="gap: 1rem;"
    enctype="multipart/form-data"
    x-data="{ isLoading: false }"
    @submit="isLoading = true">
    <?= csrf_field() ?>

    <?php if ($isEdit): ?>
        <input type="hidden" name="_method" value="PUT">
        <input type="hidden" name="slug" value="<?= $post['slug'] ?>">
    <?php endif; ?>

    <div class="card-panel">
        <div class="columns is-multiline">
            <div class="column is-12">
                <h2 class="script-label">Identitas Blog</h2>
            </div>

            <div class="column is-12">
                <div class="field">
                    <label class="label" for="title">Judul Blog</label>
                    <div class="control">
                        <input class="input" type="text" name="title" id="title" required
                            value="<?= old('title', $post['title'] ?? '') ?>"
                            placeholder="Contoh: Ketika Jawaban Murid Tidak Sesuai Ekspektasi...">
                    </div>
                </div>

                <div class="field">
                    <label class="label" for="excerpt">Ringkasan (Excerpt)</label>
                    <div class="control">
                        <textarea class="textarea" name="excerpt" id="excerpt" rows="5" placeholder="Tulis ringkasan singkat..."><?= old('excerpt', $post['excerpt'] ?? '') ?></textarea>
                    </div>
                </div>

                <div class="field" x-data="{ 
                    previewUrl: '<?= isset($post['cover_image']) ? render_cover_url($post['cover_image']) : '' ?>',
                    originalUrl: '<?= isset($post['cover_image']) ? render_cover_url($post['cover_image']) : '' ?>',
                    updatePreview(event) {
                        const file = event.target.files[0];
                        if (file) {
                            this.previewUrl = URL.createObjectURL(file);
                        }
                    },
                    resetImage() {
                        this.previewUrl = this.originalUrl;
                        document.getElementById('cover_image').value = '';
                    }
                }">
                    <label class="label">Thumbnail / Cover</label>
                    <div class="control">
                        <div class="preview-container mb-3" style="position: relative;">

                            <template x-if="previewUrl">
                                <button type="button"
                                    @click="resetImage"
                                    class="button is-danger is-small"
                                    style="position: absolute; top: 10px; right: 10px; z-index: 10; border-radius: 50%; width: 30px; height: 30px; padding: 0;">
                                    <i class="fas fa-times"></i>
                                </button>
                            </template>

                            <template x-if="previewUrl">
                                <figure class="image" style="height: 350px; border: 1px solid var(--bulma-input-border-color); border-radius: var(--bulma-input-radius); overflow: hidden;">
                                    <img :src="previewUrl" alt="Cover Preview"
                                        style="object-fit: contain; width: 100%; height: 100%;">
                                </figure>
                            </template>

                            <template x-if="!previewUrl">
                                <div class="is-flex is-align-items-center is-justify-content-center"
                                    style="height: 350px; border: 2px dashed var(--bulma-input-border-color); border-radius: var(--bulma-input-radius);">
                                    <div class="has-text-centered has-text-grey">
                                        <span class="icon is-large"><i class="fas fa-image fa-2x"></i></span>
                                        <p class="is-size-7">Belum ada gambar dipilih</p>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <div class="file is-small is-boxed is-fullwidth">
                            <label class="file-label">
                                <input class="file-input" type="file" name="cover_image" id="cover_image"
                                    accept="image/*" @change="updatePreview">
                                <span class="file-cta">
                                    <span class="file-icon"><i class="fas fa-upload"></i></span>
                                    <span class="file-label has-text-weight-semibold has-text-centered"
                                        x-text="previewUrl ? 'Ganti Gambar' : 'Pilih Gambar'">
                                    </span>
                                </span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card-panel">
        <div class="columns is-multiline">
            <div class="column is-12">
                <h2 class="script-label">Konten Utama</h2>
            </div>

            <div class="column is-12">
                <div class="field">
                    <div class="control">
                        <p class="help mb-3">Tulis Konten Utama Blog Anda Di Editor di Bawah Ini</p>

                        <div class="blog-editor-shell">
                            <textarea
                                name="content"
                                id="editor"
                                data-ckeditor="blog-post"
                                data-placeholder="Tulis konten utama blog Anda di sini..."><?= old('content', $post['content'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card-panel">
        <div class="columns is-multiline">
            <div class="column is-12">
                <h2 class="script-label">Pengaturan Blog</h2>
            </div>

            <div class="column is-6">
                <div class="field">
                    <label class="label" for="category_id">Kategori</label>
                    <div class="control">
                        <div class="select is-fullwidth">
                            <select name="category_id" id="category_id" required>
                                <option value="">Pilih Kategori</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>" <?= (old('category_id', $post['category_id'] ?? '') == $cat['id']) ? 'selected' : '' ?>>
                                        <?= $cat['name'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="column is-6">
                <div class="field">
                    <label class="label" for="published_at">Tanggal Publikasi</label>
                    <div class="control has-icons-left">
                        <input
                            class="input"
                            type="text"
                            name="published_at"
                            id="published_at"
                            placeholder="Pilih tanggal publikasi..."
                            readonly
                            x-init="flatpickr($el, {
                                enableTime: true,
                                locale: 'id',
                                dateFormat: 'Y-m-d H:i:S',
                                defaultDate: '<?= old('published_at', $post['published_at'] ?? date('Y-m-d H:i:S')) ?>',
                                time_24hr: true
                            })">
                        <span class="icon is-small is-left">
                            <i class="fas fa-calendar-alt"></i>
                        </span>
                    </div>
                </div>
            </div>

            <div class="column is-12">
                <p class="label">Tags</p>
                <hr class="mt-1" style="width: 240px;">
                <div class="columns is-multiline">
                    <?php foreach ($tags as $tag): ?>
                        <div class="column is-3 py-1">
                            <label class="checkbox is-size-6">
                                <input type="checkbox" name="tag_ids[]" value="<?= $tag['id'] ?>"
                                    <?= (in_array($tag['id'], old('tag_ids', $selectedTags ?? []))) ? 'checked' : '' ?>>
                                <?= $tag['name'] ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <label for="status" class="card-panel checkbox is-flex is-align-items-start">
        <input type="checkbox" name="status" id="status" value="PUBLISHED" <?= (old('status', $post['status'] ?? '') == 'PUBLISHED') ? 'checked' : '' ?>>
        <div>
            <span class="has-text-weight-semibold">Publikasikan Berita</span>
            <p class="help">Centang kotak ini untuk mempublikasikan berita ke halaman utama sehingga dapat dilihat oleh semua pengunjung website. Jika tidak di centang, berita hanya akan terlihat dalam mode <strong>DRAFT</strong>.</p>
        </div>
    </label>

    <div class="field is-grouped is-justify-content-flex-end my-4">
        <div class="control">
            <a href="<?= site_url('admin/posts') ?>" class="button">Kembali</a>
        </div>
        <div class="control">
            <button class="button is-link" type="submit" :class="{ 'is-loading': isLoading }" :disabled="isLoading">
                <span class="icon">
                    <i class="fas fa-save"></i>
                </span>
                <span><?= $isEdit ? 'Perbarui Post' : 'Simpan Post' ?></span>
            </button>
        </div>
    </div>
</form>


<?= $this->section('page_script_lib') ?>
<script src="<?= base_url('assets/lib/ckeditor5/ckeditor5.umd.js') ?>"></script>
<script defer src="<?= base_url('assets/js/adminPostEditor.js?v=' . $ckeditorScriptVersion) ?>"></script>
<?= $this->endSection() ?>

<?= $this->section('page_script') ?>

<script>
    window.APP = window.APP || {};
    window.APP.blogPostEditor = {
        selector: '#editor',
        uploadUrl: '<?= site_url('admin/upload/image') ?>',
        csrfHeader: '<?= csrf_header() ?>',
        csrfToken: '<?= csrf_hash() ?>',
        csrfTokenName: '<?= csrf_token() ?>'
    };
</script>

<?= $this->endSection() ?>
