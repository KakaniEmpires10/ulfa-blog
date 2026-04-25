<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<section class="section page-section">
    <div class="container">
        <div class="title-bordered mb-5 is-flex is-align-items-center">
            <h1 class="h4">Mari terhubung kapan saja</h1>
            <?php if (! empty($profile['social_links'])) : ?>
                <ul class="list-inline social-icons ml-auto mr-3 social-icons-inline">
                    <?php foreach ($profile['social_links'] as $platform => $link) : ?>
                        <li class="list-inline-item">
                            <a href="<?= esc($link) ?>" target="_blank" rel="noreferrer">
                                <i class="<?= esc(social_icon_class($platform)) ?>"></i>
                                <span><?= esc(social_label($platform)) ?></span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>

        <?php if (session('message')) : ?>
            <?= view('components/notification', [
                'variant' => 'success',
                'title'   => 'Pesan berhasil dikirim',
                'message' => session('message'),
                'icon'    => 'fa-solid fa-paper-plane',
            ]) ?>
        <?php endif; ?>
        <?php if (session('error')) : ?>
            <?= view('components/notification', [
                'variant' => 'danger',
                'title'   => 'Pesan belum terkirim',
                'message' => session('error'),
                'icon'    => 'fa-solid fa-circle-exclamation',
            ]) ?>
        <?php endif; ?>
        <?php if (session('errors')) : ?>
            <?= view('components/notification', [
                'variant'  => 'danger',
                'title'    => 'Masih ada isian yang perlu dibenahi',
                'messages' => (array) session('errors'),
                'icon'     => 'fa-solid fa-triangle-exclamation',
            ]) ?>
        <?php endif; ?>

        <div class="columns is-variable is-6">
            <div class="column is-5-desktop">
                <div class="content contact-copy">
                    <h2>Tanyakan apa saja, atau sekadar menyapa.</h2>
                    <p>Kalau kamu ingin berdiskusi, berbagi ide, atau meninggalkan pesan yang hangat, kamu bisa menghubungi saya lewat formulir ini.</p>
                    <h4 class="mt-6">Lebih nyaman lewat email?</h4>
                    <p><a href="mailto:<?= esc(get_setting('contact_email', 'hello@ulfablog.test')) ?>"><?= esc(get_setting('contact_email', 'hello@ulfablog.test')) ?></a></p>
                </div>
            </div>
            <div class="column is-7-desktop">
                <form method="post" action="<?= site_url('/contact') ?>" class="contact-form-card validate-form" novalidate>
                    <?= csrf_field() ?>
                    <div class="field">
                        <label class="label" for="name">Nama</label>
                        <div class="control">
                            <input class="input" type="text" id="name" name="name" value="<?= esc(old('name')) ?>" placeholder="Masukkan nama kamu" required minlength="2" maxlength="100">
                        </div>
                    </div>
                    <div class="field">
                        <label class="label" for="email">Email</label>
                        <div class="control">
                            <input class="input" type="email" id="email" name="email" value="<?= esc(old('email')) ?>" placeholder="nama@email.com" required maxlength="254">
                        </div>
                    </div>
                    <div class="field">
                        <label class="label" for="message">Pesan</label>
                        <div class="control">
                            <textarea class="textarea" id="message" name="message" placeholder="Tulis pesan kamu di sini..." required minlength="10" maxlength="2000"><?= esc(old('message')) ?></textarea>
                        </div>
                    </div>
                    <div class="contact-submit-wrap">
                        <button type="submit" class="button is-primary is-rounded contact-submit" data-loading-text="Mengirim pesan...">
                            <span>Kirim pesan</span>
                            <span class="icon" aria-hidden="true">
                                <i class="fa-solid fa-paper-plane"></i>
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>
