<form class="is-flex is-flex-direction-column" style="gap: 1rem;" action="<?= url_to('settings_update') ?>" method="POST" x-data="{ isLoading: false }" @submit="isLoading = true">
    <?= csrf_field() ?>

    <div class="card-panel">
        <div class="columns is-multiline">
            <div class="column is-12">
                <h2 class="script-label">Identitas Situs</h2>
            </div>

            <div class="column is-6">
                <div class="field">
                    <label class="label">Nama Situs</label>
                    <div class="control">
                        <input class="input" type="text" name="site_name" value="<?= old('site_name', $site_name ?? '') ?>" placeholder="Masukkan nama blog">
                    </div>
                </div>
            </div>

            <div class="column is-6">
                <div class="field">
                    <label class="label">Email Kontak</label>
                    <div class="control has-icons-left">
                        <input class="input" type="email" name="contact_email" value="<?= old('contact_email', $contact_email ?? '') ?>" placeholder="hello@example.com">
                        <span class="icon is-small is-left">
                            <i class="fas fa-envelope"></i>
                        </span>
                    </div>
                </div>
            </div>

            <div class="column is-12">
                <div class="field">
                    <label class="label">Tagline</label>
                    <div class="control">
                        <input class="input" type="text" name="site_tagline" value="<?= old('site_tagline', $site_tagline ?? '') ?>">
                    </div>
                </div>
            </div>

            <div class="column is-12">
                <div class="field">
                    <label class="label">Deskripsi Situs</label>
                    <div class="control">
                        <textarea class="textarea" name="site_description" rows="3"><?= old('site_description', $site_description ?? '') ?></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card-panel">
        <div class="columns is-multiline">
            <div class="column is-12">
                <h2 class="script-label">Tampilan & Tema</h2>
            </div>

            <div class="column is-4">
                <div class="field">
                    <label class="label">Warna Utama</label>
                    <div class="control">
                        <input class="input" type="color" name="primary_color" value="<?= old('primary_color', $primary_color ?? '#ce8460') ?>" style="height: 2.5rem; padding: 2px;">
                    </div>
                </div>
            </div>

            <div class="column is-4">
                <div class="field">
                    <label class="label">Warna Sekunder</label>
                    <div class="control">
                        <input class="input" type="color" name="secondary_color" value="<?= old('secondary_color', $secondary_color ?? '#1c1d1f') ?>" style="height: 2.5rem; padding: 2px;">
                    </div>
                </div>
            </div>

            <div class="column is-4">
                <div class="field">
                    <label class="label">Border Radius (px)</label>
                    <div class="control">
                        <input class="input" type="number" name="border_radius" value="<?= old('border_radius', $border_radius ?? '8') ?>">
                    </div>
                </div>
            </div>
        </div>
        <span class="has-text-grey is-size-7"><strong>**Note :</strong> Pengaturan pada section Tampilan & Tema ini masih belum stabil, disarankan jangan mengubahnya jika tidak diperlukan dulu.</span>
    </div>

    <div class="card-panel">
        <div class="columns is-multiline">
            <div class="column is-12">
                <h2 class="script-label">Fitur & Konten</h2>
            </div>

            <div class="column is-4">
                <div class="field">
                    <label class="label">Sumber Slider</label>
                    <div class="control is-expanded">
                        <div class="select is-fullwidth">
                            <select name="homepage_slider_source">
                                <option value="popular" <?= (old('homepage_slider_source', $homepage_slider_source ?? '') === 'popular' ? 'selected' : '') ?>>Populer</option>
                                <option value="latest" <?= (old('homepage_slider_source', $homepage_slider_source ?? '') === 'latest' ? 'selected' : '') ?>>Terbaru</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="column is-4">
                <div class="field">
                    <label class="label">Limit Slider</label>
                    <div class="control">
                        <input class="input" type="number" name="homepage_slider_limit" value="<?= old('homepage_slider_limit', $homepage_slider_limit ?? '3') ?>">
                    </div>
                </div>
            </div>

            <div class="column is-4">
                <div class="field">
                    <label class="label">Komentar</label>
                    <div class="control mt-3">
                        <label class="checkbox">
                            <input type="checkbox" name="enable_comment" value="1" <?= (old('enable_comment', $enable_comment ?? '0') == '1') ? 'checked' : '' ?>>
                            Aktifkan komentar pada postingan
                        </label>
                    </div>
                </div>
            </div>

            <div class="column is-8">
                <div class="field">
                    <label class="label">Disqus Shortname</label>
                    <div class="control has-icons-left">
                        <input class="input" type="text" name="disqus_shortname" value="<?= esc(old('disqus_shortname', $disqus_shortname ?? '')) ?>" placeholder="contoh: ulfa-blog" pattern="[A-Za-z0-9-]+">
                        <span class="icon is-small is-left">
                            <i class="fa-regular fa-comments"></i>
                        </span>
                    </div>
                    <p class="help">Isi dengan shortname dari Disqus. Komentar akan tampil jika komentar aktif dan shortname tersedia.</p>
                </div>
            </div>

            <div class="column is-12 mt-5 is-flex is-justify-content-end">
                <button type="submit" class="button is-primary" :class="{ 'is-loading': isLoading }" :disabled="isLoading">
                    <i class="fas fa-save mr-2"></i> Simpan Perubahan
                </button>
            </div>
        </div>
    </div>
</form>
