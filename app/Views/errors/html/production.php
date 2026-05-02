<?php
$asset = static fn(string $path): string => function_exists('base_url')
    ? base_url($path)
    : '/' . ltrim($path, '/');

$homeUrl = $asset('/');
$errorMessage = ENVIRONMENT !== 'production'
    ? nl2br(esc($message))
    : 'Terjadi kesalahan internal pada server. Tim kami sedang menangani masalah ini.';
?>
<!DOCTYPE html>
<html lang="id" data-theme="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>500 | Kesalahan Server</title>
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
            background-color: var(--bulma-danger);
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
            background: color-mix(in srgb, var(--bulma-danger) 6%, var(--bulma-scheme-main));
            border: 1px solid color-mix(in srgb, var(--bulma-danger) 20%, var(--bulma-border));
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
            color: var(--bulma-danger);
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
            background-color: var(--bulma-danger);
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

        .script-label-danger {
            color: var(--bulma-danger);
            display: inline-block;
            font-family: "Caveat", cursive;
            font-size: 1.35rem;
            font-weight: 600;
            letter-spacing: 0.04em;
            margin-bottom: 0.8rem;
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
            <p class="script-label-danger">Kesalahan server</p>
            <p class="error-num">500</p>
            <h1 class="error-headline">Ada yang tidak beres<br>di sisi kami.</h1>
            <div class="error-divider"></div>
            <p class="error-body-text">
                Server mengalami kesalahan internal yang tidak terduga. Ini bukan salah kamu — tim kami sedang menanganinya. Coba lagi beberapa saat.
            </p>
            <p class="script-label-danger" style="font-size: 1.15rem; margin-bottom: 1.5rem;">
                Sabar sebentar ya, kami sedang perbaiki ✦
            </p>
            <div class="error-spring-dots">
                <span></span><span></span><span></span><span></span><span></span>
            </div>
            <div class="error-actions">
                <a href="<?= esc($homeUrl) ?>" class="button is-danger" style="border-radius: var(--bulma-radius-rounded);">
                    <span class="icon"><i class="fas fa-house"></i></span>
                    <span>Kembali ke Beranda</span>
                </a>
                <button type="button" onclick="window.location.reload()" class="button" style="border-radius: var(--bulma-radius-rounded);">
                    <span class="icon"><i class="fas fa-rotate-right"></i></span>
                    <span>Coba Lagi</span>
                </button>
            </div>
            <div class="error-detail-box">
                <span class="error-detail-label">Detail kesalahan</span>
                <?= $errorMessage ?>
            </div>
            <p class="error-footer-note">
                Jika masalah berlanjut lebih dari beberapa menit, hubungi administrator situs dan sertakan waktu terjadinya kesalahan ini.
            </p>
        </div>
    </main>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
</body>

</html>