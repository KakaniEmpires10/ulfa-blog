<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<?= $this->include('components/admin/settings/tabs_setting') ?>

<?= $this->section('page_style') ?>
<style>
    [x-cloak] {
        display: none !important;
    }

    .upload-progress {
        transition: width 0.3s ease;
    }
</style>
<?= $this->endSection() ?>

<!-- Section Profile Image -->
<section class="card-panel mb-4">
    <h2 class="script-label">Pengaturan Gambar Profil</h2>
    <p class="help mb-2">Gambar Profil anda yang akan muncul di seluruh aplikasi.</p>

    <form action="<?= url_to('settings_image_update_avatar') ?>"
        method="post"
        enctype="multipart/form-data"
        x-data="imageUploader({
              type: 'profile',
              inputId: 'profile_image',
              previewUrl: '<?= isset($data['avatar_path']) ? render_cover_url($data['avatar_path']) : '' ?>',
              originalUrl: '<?= isset($data['avatar_path']) ? render_cover_url($data['avatar_path']) : '' ?>',
              isExternal: <?= isset($data['avatar_path']) && is_external_cover($data['avatar_path']) ? 'true' : 'false' ?>
          })"
        @submit.prevent="submitForm">

        <div class="field" style="position: relative;">
            <div class="control">

                <!-- Info External URL -->
                <div x-show="isExternal && !hasChanges" class="mb-3" x-cloak>
                    <div class="notification is-warning is-light is-paddingless p-2 has-text-centered">
                        <span class="icon-text">
                            <span class="icon"><i class="fas fa-link"></i></span>
                            <span class="is-size-7">Profil dari URL eksternal. Upload gambar baru untuk mengganti.</span>
                        </span>
                    </div>
                </div>

                <div class="preview-container mb-3" style="position: relative;">
                    <!-- Upload Progress -->
                    <div x-show="isLoading && uploadProgress > 0" x-cloak
                        style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 15; background: rgba(0,0,0,0.8); padding: 1rem; border-radius: 8px; text-align: center; min-width: 200px;">
                        <div class="has-text-white mb-2">Mengupload... <span x-text="uploadProgress"></span>%</div>
                        <progress class="progress is-primary" :value="uploadProgress" max="100" style="width: 100%;"></progress>
                    </div>

                    <template x-if="previewUrl && !isLoading">
                        <button type="button"
                            @click="resetImage"
                            class="button is-danger is-small"
                            style="position: absolute; top: 10px; right: 10px; z-index: 10; border-radius: 50%; width: 30px; height: 30px; padding: 0;">
                            <i class="fas fa-times"></i>
                        </button>
                    </template>

                    <template x-if="previewUrl">
                        <div class="is-flex is-align-items-center is-justify-content-center"
                            style="width: 200px; height: 200px; margin: 0 auto; border: 3px solid var(--bulma-primary); border-radius: 50%; overflow: hidden; background: var(--bulma-background);">
                            <img :src="previewUrl" alt="Profile Preview"
                                style="object-fit: cover; width: 100%; height: 100%;">
                        </div>
                    </template>

                    <template x-if="!previewUrl">
                        <div class="is-flex is-align-items-center is-justify-content-center"
                            style="width: 200px; height: 200px; margin: 0 auto; border: 2px dashed var(--bulma-input-border-color); border-radius: 50%;">
                            <div class="has-text-centered has-text-grey">
                                <span class="icon is-large"><i class="fas fa-user-circle fa-3x"></i></span>
                                <p class="is-size-7">Belum ada profil</p>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="file is-small is-boxed is-fullwidth" style="max-width: 300px; margin: 0 auto;">
                    <label class="file-label">
                        <input class="file-input" type="file" name="profile_image" id="profile_image"
                            accept="image/jpeg,image/jpg,image/png" @change="updatePreview">
                        <span class="file-cta">
                            <span class="file-icon"><i class="fas fa-upload"></i></span>
                            <span class="file-label has-text-weight-semibold has-text-centered"
                                x-text="previewUrl ? 'Ganti Profil' : 'Pilih Profil'">
                            </span>
                        </span>
                    </label>
                </div>

                <div class="has-text-centered mt-2">
                    <small class="has-text-grey">
                        <i class="fas fa-info-circle mr-1"></i>
                        Format: JPG, PNG | Maks: 4 MB | Rekomendasi: persegi (1:1)
                    </small>
                </div>

                <!-- Submit Button -->
                <div class="has-text-centered mt-3" x-show="hasChanges && !isLoading" x-cloak>
                    <button type="submit" class="button is-primary is-small" x-ref="submitButton">
                        <span class="icon"><i class="fas fa-save"></i></span>
                        <span>Simpan Perubahan</span>
                    </button>
                </div>

                <div class="has-text-centered mt-3" x-show="isLoading" x-cloak>
                    <button class="button is-primary is-loading" disabled>
                        <span>Mengupload... <span x-text="uploadProgress"></span>%</span>
                    </button>
                </div>

            </div>
        </div>
    </form>
</section>

<!-- Section Cover Image -->
<section class="card-panel">
    <h2 class="script-label">Pengaturan Gambar Cover</h2>
    <p class="help mb-2">Gambar Cover yang muncul dihalaman Tentang. <a href="<?= site_url('about') ?>" target="_blank" class="is-underlined">Lihat cover saat ini</a></p>

    <form action="<?= url_to('settings_image_update_cover') ?>"
        method="post"
        enctype="multipart/form-data"
        x-data="imageUploader({
              type: 'cover',
              inputId: 'cover_image',
              previewUrl: '<?= isset($data['cover_image_path']) ? render_cover_url($data['cover_image_path']) : '' ?>',
              originalUrl: '<?= isset($data['cover_image_path']) ? render_cover_url($data['cover_image_path']) : '' ?>',
              isExternal: <?= isset($data['cover_image_path']) && is_external_cover($data['cover_image_path']) ? 'true' : 'false' ?>
          })"
        @submit.prevent="submitForm">

        <div class="field" style="position: relative;">
            <div class="control">

                <!-- Info External URL -->
                <div x-show="isExternal && !hasChanges" class="mb-2" x-cloak>
                    <div class="notification is-warning is-light is-paddingless p-2">
                        <span class="icon-text">
                            <span class="icon"><i class="fas fa-link"></i></span>
                            <span class="is-size-7">Cover saat ini dari URL eksternal. Upload gambar baru untuk menggantinya.</span>
                        </span>
                    </div>
                </div>

                <div class="preview-container mb-3" style="position: relative;">
                    <!-- Upload Progress -->
                    <div x-show="isLoading && uploadProgress > 0" x-cloak
                        style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 15; background: rgba(0,0,0,0.8); padding: 1rem; border-radius: 8px; text-align: center; min-width: 200px;">
                        <div class="has-text-white mb-2">Mengupload... <span x-text="uploadProgress"></span>%</div>
                        <progress class="progress is-primary" :value="uploadProgress" max="100" style="width: 100%;"></progress>
                    </div>

                    <template x-if="previewUrl && !isLoading">
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
                                style="object-fit: cover; width: 100%; height: 100%;">
                        </figure>
                    </template>

                    <template x-if="!previewUrl">
                        <div class="is-flex is-align-items-center is-justify-content-center"
                            style="height: 350px; border: 2px dashed var(--bulma-input-border-color); border-radius: var(--bulma-input-radius);">
                            <div class="has-text-centered has-text-grey">
                                <span class="icon is-large"><i class="fas fa-image fa-2x"></i></span>
                                <p class="is-size-7">Belum ada cover dipilih</p>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="file is-small is-boxed is-fullwidth">
                    <label class="file-label">
                        <input class="file-input" type="file" name="cover_image" id="cover_image"
                            accept="image/jpeg,image/jpg,image/png" @change="updatePreview">
                        <span class="file-cta">
                            <span class="file-icon"><i class="fas fa-upload"></i></span>
                            <span class="file-label has-text-weight-semibold has-text-centered"
                                x-text="previewUrl ? 'Ganti Cover' : 'Pilih Cover'">
                            </span>
                        </span>
                    </label>
                </div>

                <div class="has-text-centered mt-2">
                    <small class="has-text-grey">
                        <i class="fas fa-info-circle mr-1"></i>
                        Format: JPG, PNG | Maks: 4 MB | Rekomendasi: 1200x400px
                    </small>
                </div>

                <!-- Submit Button -->
                <div class="has-text-centered mt-3" x-show="hasChanges && !isLoading" x-cloak>
                    <button type="submit" class="button is-primary is-small" x-ref="submitButton">
                        <span class="icon"><i class="fas fa-save"></i></span>
                        <span>Simpan Perubahan</span>
                    </button>
                </div>

                <div class="has-text-centered mt-3" x-show="isLoading" x-cloak>
                    <button class="button is-primary is-loading" disabled>
                        <span>Mengupload... <span x-text="uploadProgress"></span>%</span>
                    </button>
                </div>

            </div>
        </div>
    </form>
</section>

<?= $this->endSection() ?>
