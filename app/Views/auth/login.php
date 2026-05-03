<?= $this->extend(config('Auth')->views['layout']) ?>

<?= $this->section('title') ?>Masuk | <?= esc(get_setting('site_name', 'Ulfa Blog')) ?><?= $this->endSection() ?>

<?= $this->section('main') ?>
<?php $loginField = setting('Auth.validFields')[0] ?? 'username'; ?>

<section class="section page-section login-section">
    <div class="container">
        <div class="columns is-centered">
            <div class="column is-5-desktop is-7-tablet">
                <div class="login-card">
                    <span class="script-label">Selamat datang kembali</span>
                    <div class="login-intro">
                        <h1 class="login-title">Masuk untuk melanjutkan</h1>
                        <p class="login-copy">Gunakan akun Anda untuk melanjutkan pengelolaan tulisan dan isi blog.</p>
                    </div>

                    <?php if (session('error') !== null) : ?>
                        <?= view('components/notification', [
                            'variant' => 'danger',
                            'title'   => 'Gagal Masuk',
                            'message' => session('error'),
                            'icon'    => 'fa-solid fa-circle-exclamation',
                        ]) ?>
                    <?php endif ?>
                    <?php if (session('errors') !== null) : ?>
                        <?= view('components/notification', [
                            'variant'  => 'danger',
                            'title'    => 'Periksa kembali isian Anda',
                            'messages' => (array) session('errors'),
                            'icon'     => 'fa-solid fa-triangle-exclamation',
                        ]) ?>
                    <?php endif ?>
                    <?php if (session('message') !== null) : ?>
                        <?= view('components/notification', [
                            'variant' => 'success',
                            'title'   => 'Berhasil',
                            'message' => session('message'),
                            'icon'    => 'fa-solid fa-circle-check',
                        ]) ?>
                    <?php endif ?>

                    <form
                        action="<?= url_to('login') ?>"
                        method="post"
                        class="validate-form"
                        novalidate
                        x-data="{ 
                            isLoading: false,
                            handleSubmit(event) {
                                if (!event.currentTarget.checkValidity()) {
                                    event.preventDefault();
                                    this.isLoading = false;
                                    event.currentTarget.reportValidity();
                                    return;
                                }

                                this.isLoading = true;
                            }
                        }"
                        @submit="handleSubmit($event)">

                        <?= csrf_field() ?>

                        <div class="field">
                            <label class="label" for="loginField">Username</label>
                            <div class="control has-icons-left">
                                <input class="input" id="loginField" type="text" name="<?= esc($loginField) ?>" value="<?= esc(old($loginField)) ?>" placeholder="Masukkan username" required autocomplete="username" minlength="3">
                                <span class="icon is-small is-left"><i class="fa-regular fa-user"></i></span>
                            </div>
                        </div>

                        <div class="field">
                            <label class="label" for="password">Password</label>
                            <div class="control has-icons-left">
                                <input class="input" id="password" type="password" name="password" required autocomplete="current-password" placeholder="Masukkan password" minlength="8">
                                <span class="icon is-small is-left"><i class="fa-solid fa-lock"></i></span>
                            </div>
                        </div>

                        <?php if (setting('Auth.sessionConfig')['allowRemembering']) : ?>
                            <label class="checkbox remember-box">
                                <input type="checkbox" name="remember" <?php if (old('remember')) : ?>checked<?php endif ?>>
                                Ingat saya
                            </label>
                        <?php endif; ?>

                        <button
                            type="submit"
                            class="button is-primary is-fullwidth is-rounded"
                            :class="{ 'is-loading': isLoading }"
                            :disabled="isLoading">
                            <span>Masuk</span>
                            <span class="icon" aria-hidden="true">
                                <i class="fa-solid fa-arrow-right-to-bracket"></i>
                            </span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>