<?php
$asset = static fn(string $path): string => function_exists('base_url')
    ? base_url($path)
    : '/' . ltrim($path, '/');

$homeUrl = $asset('/');
$siteName = get_setting('site_name', 'Ulfa Blog') ?? 'Ulfa Blog';
$errorMessage = ENVIRONMENT !== 'production'
    ? nl2br(esc($message))
    : "Maaf Kami tidak bisa menemukan halaman yang sedang kamu cari 🙏";
?>
<!DOCTYPE html>
<html lang="id" data-theme="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>404 | <?= esc($siteName) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Caveat:wght@500;600;700&family=Montserrat:wght@500;600;700&family=Open+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= esc($asset('assets/css/bulma.min.css')) ?>">
    <link rel="stylesheet" href="<?= esc($asset('assets/css/site.css')) ?>">
    <style>
        .error-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3rem 1.5rem;
            background-color: var(--bulma-scheme-main);
        }

        .error-wrap {
            max-width: 580px;
            width: 100%;
        }

        .error-num {
            font-family: var(--bulma-family-secondary);
            font-size: clamp(6rem, 22vw, 10rem);
            font-weight: 700;
            line-height: 1;
            letter-spacing: -4px;
            color: var(--bulma-border);
            margin: 0;
            user-select: none;
        }

        .error-headline {
            font-family: var(--bulma-family-secondary);
            font-size: clamp(1.6rem, 4vw, 2.25rem);
            font-weight: 700;
            color: var(--bulma-text-strong);
            line-height: 1.2;
            margin: 0.35rem 0 0;
        }

        .error-divider {
            width: 3rem;
            height: 2px;
            background-color: var(--bulma-primary);
            border-radius: 2px;
            margin: 1.4rem 0;
        }

        .error-body-text {
            font-size: 1rem;
            color: var(--bulma-text);
            line-height: 1.8;
            margin: 0 0 0.5rem;
            max-width: 40ch;
        }

        .error-detail-box {
            margin: 1.5rem 0 2rem;
            padding: 0.9rem 1.1rem;
            background: color-mix(in srgb, var(--bulma-primary) 7%, var(--bulma-scheme-main));
            border: 1px solid color-mix(in srgb, var(--bulma-primary) 18%, var(--bulma-border));
            border-radius: var(--bulma-radius-medium);
            font-size: 0.88rem;
            color: var(--bulma-text);
            line-height: 1.7;
        }

        .error-detail-label {
            display: block;
            font-family: var(--bulma-family-secondary);
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--bulma-primary);
            margin-bottom: 0.3rem;
        }

        .error-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            margin-bottom: 2.5rem;
        }

        .error-footer-note {
            font-size: 0.82rem;
            color: var(--bulma-text-weak);
            line-height: 1.65;
            border-top: 1px solid var(--bulma-border-weak);
            padding-top: 1.4rem;
        }

        .error-spring-dots {
            display: flex;
            align-items: center;
            gap: 5px;
            margin-bottom: 1.8rem;
        }

        .error-spring-dots span {
            display: inline-block;
            border-radius: 999px;
            background-color: var(--bulma-primary);
        }

        .error-spring-dots span:nth-child(1) {
            width: 6px;
            height: 6px;
            opacity: 0.28;
        }

        .error-spring-dots span:nth-child(2) {
            width: 8px;
            height: 8px;
            opacity: 0.45;
        }

        .error-spring-dots span:nth-child(3) {
            width: 11px;
            height: 11px;
            opacity: 0.72;
        }

        .error-spring-dots span:nth-child(4) {
            width: 8px;
            height: 8px;
            opacity: 0.45;
        }

        .error-spring-dots span:nth-child(5) {
            width: 6px;
            height: 6px;
            opacity: 0.28;
        }

        @media screen and (max-width: 480px) {
            .error-actions {
                flex-direction: column;
            }

            .error-actions .button {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>

<body>
    <main class="error-page">
        <div class="error-wrap">

            <!-- Kicker -->
            <p class="script-label">Halaman tidak ditemukan</p>

            <!-- Big 404 -->
            <p class="error-num">404</p>

            <!-- Headline -->
            <h1 class="error-headline">Cerita ini belum<br>ada di rak kami.</h1>

            <!-- Divider -->
            <div class="error-divider"></div>

            <!-- Body copy -->
            <p class="error-body-text">
                Tautan yang kamu buka mungkin sudah berubah, artikelnya dipindahkan, atau alamatnya belum tersedia.
                Kembali ke beranda — masih banyak tulisan yang menunggu dibaca.
            </p>

            <!-- Caveat note -->
            <p class="script-label" style="font-size: 1.15rem; margin-bottom: 1.5rem;">
                Tapi jangan pergi dulu, ya ✦
            </p>

            <!-- Spring dots -->
            <div class="error-spring-dots">
                <span></span><span></span><span></span><span></span><span></span>
            </div>

            <!-- Actions -->
            <div class="error-actions">
                <a href="<?= esc($homeUrl) ?>" class="button is-primary" style="border-radius: var(--bulma-radius-rounded);">
                    <span class="icon"><i class="fas fa-house"></i></span>
                    <span>Kembali ke Beranda</span>
                </a>
                <button type="button" onclick="window.history.back()" class="button" style="border-radius: var(--bulma-radius-rounded);">
                    <span class="icon"><i class="fas fa-arrow-left"></i></span>
                    <span>Halaman Sebelumnya</span>
                </button>
            </div>

            <!-- Error detail box -->
            <div class="error-detail-box">
                <span class="error-detail-label">Detail kesalahan</span>
                <?= $errorMessage ?>
            </div>

            <!-- Footer note -->
            <p class="error-footer-note">
                Jika kamu membuka tautan dari dalam dashboard atau artikel draft, pastikan status dan aksesnya memang tersedia.
            </p>

        </div>
    </main>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer">
</body>

</html>