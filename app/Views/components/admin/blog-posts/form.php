<?php
$isEdit = isset($post);
$selectedCategories = $isEdit
    ? array_column($post['categories'] ?? [], 'id')
    : [];

$selectedTags = $isEdit
    ? array_column($post['tags'] ?? [], 'id')
    : [];
$actionUrl = $isEdit ? url_to('posts_update', $post['id']) : url_to('posts_store');
$ckeditorThemeVersion = filemtime(FCPATH . 'assets/css/ckeditor-bulma.css');
$ckeditorScriptVersion = filemtime(FCPATH . 'assets/js/adminPostEditor.js');
$errors = session('errors') ?? [];
$currentStatus = strtolower((string) old('status', $post['status'] ?? ''));
$publishedAtValue = old('published_at', $post['published_at'] ?? '');
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
    novalidate
    x-data="{ 
        isLoading: false,
        handleSubmit(e) {
            this.isLoading = true;
        }
    }"
    @submit="handleSubmit">
    <?= csrf_field() ?>

    <?php if ($isEdit): ?>
        <input type="hidden" name="_method" value="PUT">
        <input type="hidden" name="slug" value="<?= esc($post['slug']) ?>">
    <?php endif; ?>

    <?= view('components/notification', [
        'variant'  => 'warning',
        'title'    => 'Periksa kembali data postingan',
        'messages' => $errors,
        'icon'     => 'fa-solid fa-triangle-exclamation',
    ]) ?>

    <div class="card-panel">
        <div class="columns is-multiline">
            <div class="column is-12">
                <h2 class="script-label">Identitas Blog</h2>
            </div>

            <div class="column is-12">
                <div class="field">
                    <label class="label" for="title">Judul Blog</label>
                    <div class="control">
                        <input class="input <?= session('errors.title') ? 'is-danger' : '' ?>"
                            type="text" name="title" id="title"
                            value="<?= esc(old('title', $post['title'] ?? '')) ?>"
                            placeholder="Contoh: Ketika Jawaban Murid Tidak Sesuai Ekspektasi...">
                    </div>
                    <?php if (session('errors.title')): ?>
                        <p class="help is-danger"><?= session('errors.title') ?></p>
                    <?php endif; ?>
                </div>

                <div class="field">
                    <label class="label" for="excerpt">Ringkasan (Excerpt)</label>
                    <div class="control">
                        <textarea class="textarea <?= session('errors.excerpt') ? 'is-danger' : '' ?>" name="excerpt" id="excerpt" rows="5" placeholder="Tulis ringkasan singkat..."><?= esc(old('excerpt', $post['excerpt'] ?? '')) ?></textarea>
                    </div>
                    <?php if (session('errors.excerpt')): ?>
                        <p class="help is-danger"><?= session('errors.excerpt') ?></p>
                    <?php else: ?>
                        <p class="help">Opsional. Jika kosong, deskripsi SEO akan dibuat dari isi konten.</p>
                    <?php endif; ?>
                </div>

                <div class="field" x-data="{ 
                    previewUrl: '<?= isset($post['cover_image']) ? esc(render_cover_url($post['cover_image']), 'js') : '' ?>',
                    originalUrl: '<?= isset($post['cover_image']) ? esc(render_cover_url($post['cover_image']), 'js') : '' ?>',
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
                    <?php if (session('errors.cover_image')): ?>
                        <p class="help is-danger mt-0 mb-2"><?= session('errors.cover_image') ?></p>
                    <?php endif; ?>
                    <div class="control">
                        <div class="preview-container mb-3" style="position: relative;">
                            <template x-if="previewUrl">
                                <button type="button" @click="resetImage" class="button is-danger is-small"
                                    style="position: absolute; top: 10px; right: 10px; z-index: 10; border-radius: 50%; width: 30px; height: 30px; padding: 0;">
                                    <i class="fas fa-times"></i>
                                </button>
                            </template>

                            <template x-if="previewUrl">
                                <figure class="image" style="height: 350px; border: 1px solid var(--bulma-input-border-color); border-radius: var(--bulma-input-radius); overflow: hidden;">
                                    <img :src="previewUrl" alt="Cover Preview" style="object-fit: contain; width: 100%; height: 100%;">
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
                                        x-text="previewUrl ? 'Ganti Gambar' : 'Pilih Gambar'"></span>
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
                        <?php if (session('errors.content')): ?>
                            <p class="help is-danger mt-0 mb-1"><?= session('errors.content') ?></p>
                        <?php endif; ?>
                        <div class="blog-editor-shell <?= session('errors.content') ? 'has-error-border' : '' ?>">
                            <textarea name="content" id="editor" data-ckeditor="blog-post"
                                data-placeholder="Tulis konten utama blog Anda di sini..."><?= esc(old('content', $post['content'] ?? '')) ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card-panel">
        <div class="columns is-multiline">
            <div class="column is-6">
                <div class="field">
                    <label class="label" for="category_id">Kategori</label>
                    <div class="control">
                        <div class="select is-fullwidth <?= session('errors.category_id') ? 'is-danger' : '' ?>">
                            <select name="category_id" id="category_id">
                                <option value="">Pilih Kategori</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>"
                                        <?= (old('category_id', $selectedCategories[0] ?? '') == $cat['id']) ? 'selected' : '' ?>>
                                        <?= esc($cat['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <?php if (session('errors.category_id')): ?>
                        <p class="help is-danger"><?= session('errors.category_id') ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="column is-6">
                <div class="field">
                    <label class="label" for="published_at">Tanggal Publikasi</label>
                    <div class="control has-icons-left">
                        <input class="input" type="text" name="published_at" id="published_at" readonly
                            x-init="flatpickr($el, {
                                enableTime: true, locale: 'id', dateFormat: 'Y-m-d H:i:S',
                                defaultDate: window.APP.forms.resolveDateTimeDefault('<?= esc($publishedAtValue, 'js') ?>'),
                                time_24hr: true
                            })">
                        <span class="icon is-small is-left"><i class="fas fa-calendar-alt"></i></span>
                    </div>
                    <?php if (session('errors.published_at')): ?>
                        <p class="help is-danger"><?= session('errors.published_at') ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="column is-12">
                <p class="label">Tags</p>
                <hr class="mt-1" style="width: 240px;">
                <div class="columns is-multiline">
                    <?php
                    $oldTags = old('tag_ids');
                    $checkedTags = is_array($oldTags) ? $oldTags : $selectedTags;
                    foreach ($tags as $tag):
                    ?>
                        <div class="column is-3 py-1">
                            <label class="checkbox is-size-6">
                                <input type="checkbox" name="tag_ids[]" value="<?= $tag['id'] ?>"
                                    <?= in_array($tag['id'], $checkedTags) ? 'checked' : '' ?>>
                                <?= esc($tag['name']) ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php if (session('errors.tag_ids')): ?>
                    <p class="help is-danger"><?= session('errors.tag_ids') ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <label for="status" class="card-panel checkbox is-flex is-align-items-start">
        <input type="checkbox" name="status" id="status" value="published"
            <?= $currentStatus === 'published' ? 'checked' : '' ?>>
        <div>
            <span class="has-text-weight-semibold">Publikasikan Berita</span>
            <p class="help">Centang untuk mempublikasikan berita ke halaman utama.</p>
            <?php if (session('errors.status')): ?>
                <p class="help is-danger"><?= session('errors.status') ?></p>
            <?php endif; ?>
        </div>
    </label>

    <div class="field is-grouped is-justify-content-flex-end my-4">
        <div class="control">
            <a href="<?= url_to('posts') ?>" class="button">Kembali</a>
        </div>
        <div class="control">
            <button class="button is-link" type="submit" :class="{ 'is-loading': isLoading }" :disabled="isLoading">
                <span class="icon"><i class="fas fa-save"></i></span>
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
        uploadUrl: '<?= url_to('upload_image') ?>',
        csrfHeader: '<?= csrf_header() ?>',
        csrfToken: '<?= csrf_hash() ?>',
        csrfTokenName: '<?= csrf_token() ?>'
    };
</script>

<?= $this->endSection() ?>
