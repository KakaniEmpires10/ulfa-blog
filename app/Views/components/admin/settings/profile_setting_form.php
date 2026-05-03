<?php $accountUsername = old('username', $accountUser->username ?? $adminUser->username ?? ''); ?>

<form
    class="is-flex is-flex-direction-column"
    style="gap: 1.5rem;"
    action="<?= url_to('settings_profile_update') ?>"
    method="POST"
    x-ref="profileForm"
    x-data="{
        isLoading: false,
        confirmModalOpen: false,
        originalUsername: '<?= esc($accountUsername, 'js') ?>',
        username: '<?= esc($accountUsername, 'js') ?>',
        newPassword: '',
        newPasswordConfirmation: '',
        currentPassword: '',
        sensitiveChanged() {
            return this.username.trim() !== this.originalUsername.trim()
                || this.newPassword !== ''
                || this.newPasswordConfirmation !== '';
        },
        submitProfile(event) {
            if (this.sensitiveChanged() && this.currentPassword.trim() === '') {
                event.preventDefault();
                this.confirmModalOpen = true;
                this.$nextTick(() => {
                    if (this.$refs.currentPassword) {
                        this.$refs.currentPassword.focus();
                    }
                });
                return;
            }

            this.isLoading = true;
        },
        confirmAndSubmit() {
            if (this.currentPassword.trim() === '') {
                this.$refs.currentPassword.focus();
                return;
            }
            this.isLoading = true;
            this.confirmModalOpen = false;
            this.$nextTick(() => {
                this.$refs.profileForm.submit();
            });
        }
    }"
    @submit="submitProfile($event)"
    @keydown.escape.window="confirmModalOpen = false">
    <?= csrf_field() ?>

    <div class="card-panel">
        <div class="columns is-multiline">
            <div class="column is-12">
                <h2 class="script-label">Akun Login</h2>
            </div>

            <div class="column is-12">
                <div class="field">
                    <label class="label">Username</label>
                    <div class="control has-icons-left">
                        <input class="input" type="text" name="username" x-model.trim="username" value="<?= esc($accountUsername) ?>" autocomplete="username" required>
                        <span class="icon is-small is-left"><i class="fas fa-user"></i></span>
                    </div>
                    <p class="help">Perubahan username membutuhkan konfirmasi password saat ini.</p>
                </div>
            </div>

            <div class="column is-12 is-6-desktop">
                <div class="field">
                    <label class="label">Password Baru</label>
                    <div class="control has-icons-left">
                        <input class="input" type="password" name="new_password" x-model="newPassword" autocomplete="new-password" placeholder="Kosongkan jika tidak diganti">
                        <span class="icon is-small is-left"><i class="fas fa-lock"></i></span>
                    </div>
                </div>
            </div>

            <div class="column is-12 is-6-desktop">
                <div class="field">
                    <label class="label">Konfirmasi Password Baru</label>
                    <div class="control has-icons-left">
                        <input class="input" type="password" name="new_password_confirmation" x-model="newPasswordConfirmation" autocomplete="new-password" placeholder="Ulangi password baru">
                        <span class="icon is-small is-left"><i class="fas fa-shield-halved"></i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

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

    <div class="modal" :class="{ 'is-active': confirmModalOpen }" x-cloak>
        <div class="modal-background" @click="confirmModalOpen = false"></div>
        <div class="modal-card">
            <header class="modal-card-head">
                <p class="modal-card-title">Konfirmasi Akses</p>
                <button class="delete" type="button" aria-label="close" @click="confirmModalOpen = false"></button>
            </header>

            <section class="modal-card-body">
                <div class="field">
                    <label class="label">Password Saat Ini</label>
                    <div class="control has-icons-left">
                        <input class="input" type="password" name="current_password" x-model="currentPassword" x-ref="currentPassword" autocomplete="current-password" placeholder="Masukkan password saat ini">
                        <span class="icon is-small is-left"><i class="fas fa-key"></i></span>
                    </div>
                    <p class="help">Wajib diisi untuk menyimpan perubahan username atau password.</p>
                </div>
            </section>

            <footer class="modal-card-foot is-justify-content-flex-end" style="gap: 0.5rem;">
                <button class="button" type="button" @click="confirmModalOpen = false">Batal</button>
                <button
                    type="button"
                    class="button is-primary"
                    :class="{ 'is-loading': isLoading }"
                    :disabled="isLoading || currentPassword.trim() === ''"
                    @click="confirmAndSubmit()">
                    <i class="fas fa-check mr-2"></i> Konfirmasi &amp; Simpan
                </button>
            </footer>
        </div>
    </div>
</form>