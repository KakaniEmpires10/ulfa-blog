<form class="is-flex is-flex-direction-column" style="gap: 1.5rem;" action="<?= site_url('admin/settings/profile') ?>" method="POST" x-data="{ isLoading: false }" @submit="isLoading = true">
    <?= csrf_field() ?>

    <div class="card-panel">
        <div class="columns is-multiline">
            <div class="column is-12">
                <h2 class="script-label">Informasi Publik</h2>
            </div>

            <div class="column is-12">
                <div class="field">
                    <label class="label">Nama Tampilan (Display Name)</label>
                    <div class="control">
                        <input class="input" type="text" name="display_name" value="<?= old('display_name', $profile['display_name'] ?? '') ?>" placeholder="Nama yang muncul di blog">
                    </div>
                </div>
            </div>

            <div class="column is-12">
                <div class="field">
                    <label class="label">Bio Singkat</label>
                    <div class="control">
                        <textarea class="textarea" name="bio" rows="2" placeholder="Tuliskan bio singkat Anda..."><?= old('bio', $profile['bio'] ?? '') ?></textarea>
                    </div>
                    <p class="help">Muncul di bawah postingan dan halaman depan.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card-panel">
        <div class="columns is-multiline">
            <div class="column is-12">
                <h2 class="script-label">Halaman Tentang</h2>
            </div>

            <div class="column is-12">
                <div class="field">
                    <label class="label">Judul Tentang (About Heading)</label>
                    <div class="control">
                        <input class="input" type="text" name="about_heading" value="<?= old('about_heading', $profile['about_heading'] ?? '') ?>">
                    </div>
                </div>
            </div>

            <div class="column is-12">
                <div class="field">
                    <label class="label">Konten Tentang</label>
                    <div class="control">
                        <textarea class="textarea" name="about_content" rows="5"><?= old('about_content', $profile['about_content'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>

            <div class="column is-12">
                <div class="field">
                    <label class="label">Teks Kutipan (Quote)</label>
                    <div class="control">
                        <textarea class="textarea" name="quote_text" rows="2"><?= old('quote_text', $profile['quote_text'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card-panel">
        <div class="columns is-multiline">
            <div class="column is-12">
                <h2 class="script-label">Tautan Sosial</h2>
            </div>

            <?php $social = json_decode($profile['social_links'] ?? '{}', true); ?>

            <div class="column is-3">
                <div class="field">
                    <label class="label">Facebook</label>
                    <div class="control has-icons-left">
                        <input class="input" type="url" name="social[facebook]" value="<?= old('social.facebook', $social['facebook'] ?? '') ?>" placeholder="https://facebook.com/...">
                        <span class="icon is-small is-left"><i class="fab fa-facebook"></i></span>
                    </div>
                </div>
            </div>

            <div class="column is-3">
                <div class="field">
                    <label class="label">Instagram</label>
                    <div class="control has-icons-left">
                        <input class="input" type="url" name="social[instagram]" value="<?= old('social.instagram', $social['instagram'] ?? '') ?>" placeholder="https://instagram.com/...">
                        <span class="icon is-small is-left"><i class="fab fa-instagram"></i></span>
                    </div>
                </div>
            </div>

            <div class="column is-3">
                <div class="field">
                    <label class="label">LinkedIn</label>
                    <div class="control has-icons-left">
                        <input class="input" type="url" name="social[linkedin]" value="<?= old('social.linkedin', $social['linkedin'] ?? '') ?>" placeholder="https://linkedin.com/in/...">
                        <span class="icon is-small is-left"><i class="fab fa-linkedin"></i></span>
                    </div>
                </div>
            </div>

            <div class="column is-3">
                <div class="field">
                    <label class="label">Twitter / X</label>
                    <div class="control has-icons-left">
                        <input class="input" type="url" name="social[twitter]" value="<?= old('social.twitter', $social['twitter'] ?? '') ?>" placeholder="https://twitter.com/...">
                        <span class="icon is-small is-left"><i class="fab fa-twitter"></i></span>
                    </div>
                </div>
            </div>

            <div class="column is-12 mt-5 is-flex is-justify-content-end">
                <button type="submit" class="button is-primary" :class="{ 'is-loading': isLoading }" :disabled="isLoading">
                    <i class="fas fa-save mr-2"></i> Perbarui Profil
                </button>
            </div>
        </div>
    </div>
</form>