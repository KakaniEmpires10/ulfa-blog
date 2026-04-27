<?php

use App\Models\WebSettingsModel;
use CodeIgniter\I18n\Time;

if (! function_exists('get_setting')) {
    function get_setting(string $key, $default = null)
    {
        static $settings;

        if (! $settings) {
            $model    = new WebSettingsModel();
            $settings = array_column($model->findAll(), 'value', 'key');
        }

        return $settings[$key] ?? $default;
    }
}

if (! function_exists('get_settings')) {
    function get_settings(): array
    {
        static $settings;

        if ($settings === null) {
            $model    = new WebSettingsModel();
            $settings = array_column($model->findAll(), 'value', 'key');
        }

        return $settings;
    }
}

if (! function_exists('hex_to_hsl')) {
    function hex_to_hsl(string $hex): ?array
    {
        $value = ltrim(trim($hex), '#');

        if ($value === '') {
            return null;
        }

        if (strlen($value) === 3) {
            $value = preg_replace('/(.)/', '$1$1', $value);
        }

        if ($value === null || strlen($value) !== 6 || ! ctype_xdigit($value)) {
            return null;
        }

        $red   = hexdec(substr($value, 0, 2)) / 255;
        $green = hexdec(substr($value, 2, 2)) / 255;
        $blue  = hexdec(substr($value, 4, 2)) / 255;

        $max   = max($red, $green, $blue);
        $min   = min($red, $green, $blue);
        $delta = $max - $min;
        $light = ($max + $min) / 2;

        if ($delta === 0.0) {
            $hue = 0.0;
            $sat = 0.0;
        } else {
            $sat = $delta / (1 - abs(2 * $light - 1));

            if ($max === $red) {
                $hue = 60 * fmod((($green - $blue) / $delta), 6);
            } elseif ($max === $green) {
                $hue = 60 * ((($blue - $red) / $delta) + 2);
            } else {
                $hue = 60 * ((($red - $green) / $delta) + 4);
            }
        }

        if ($hue < 0) {
            $hue += 360;
        }

        return [
            'h' => round($hue) . 'deg',
            's' => round($sat * 100) . '%',
            'l' => round($light * 100) . '%',
        ];
    }
}

if (! function_exists('bulma_radius_tokens')) {
    function bulma_radius_tokens(int $radiusPx): array
    {
        $base = max(4, $radiusPx) / 16;
        $toRem = static fn (float $value): string => rtrim(rtrim(number_format($value, 3, '.', ''), '0'), '.') . 'rem';

        return [
            'radius' => $toRem($base),
            'small'  => $toRem(max(0.25, $base * 0.667)),
            'medium' => $toRem(max(0.5, $base)),
            'large'  => $toRem(max(0.75, $base * 1.333)),
        ];
    }
}

if (! function_exists('nav_is_active')) {
    function nav_is_active(string $path = ''): bool
    {
        // 1. Bersihkan path target dari slash di awal/akhir
        $path = trim($path, '/');

        // 2. Jika target adalah homepage
        if ($path === '') {
            // Cek apakah URI saat ini kosong atau hanya '/'
            return service('uri')->getPath() === '/' || service('uri')->getPath() === '';
        }

        // 3. Gunakan url_is() bawaan CI4
        // url_is('about') akan return true jika url-nya adalah domain.com/about
        // url_is('about*') akan return true untuk domain.com/about/team, dll.
        return url_is($path) || url_is($path . '/*');
    }
}

if (! function_exists('blog_date')) {
    function blog_date(?string $date): string
    {
        if (empty($date)) {
            return '';
        }

        $time   = Time::parse($date);
        $months = [
            1  => 'Januari',
            2  => 'Februari',
            3  => 'Maret',
            4  => 'April',
            5  => 'Mei',
            6  => 'Juni',
            7  => 'Juli',
            8  => 'Agustus',
            9  => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        return $time->getDay() . ' ' . $months[(int) $time->getMonth()] . ' ' . $time->getYear();
    }
}

if (! function_exists('excerpt_text')) {
    function excerpt_text(?string $text, int $limit = 180): string
    {
        $text = trim(strip_tags((string) $text));

        if ($text === '') {
            return '';
        }

        if (mb_strlen($text) <= $limit) {
            return $text;
        }

        return rtrim(mb_substr($text, 0, $limit - 3)) . '...';
    }
}

if (! function_exists('reading_time')) {
    function reading_time(?string $content, int $wordsPerMinute = 200): string
    {
        $wordCount = str_word_count(strip_tags((string) $content));
        $minutes   = max(1, (int) ceil($wordCount / max(1, $wordsPerMinute)));

        return $minutes . ' menit baca';
    }
}

if (! function_exists('decode_social_links')) {
    function decode_social_links(?string $value): array
    {
        if (empty($value)) {
            return [];
        }

        $decoded = json_decode($value, true);

        return is_array($decoded) ? array_filter($decoded) : [];
    }
}

if (! function_exists('social_icon_class')) {
    function social_icon_class(string $platform): string
    {
        return match (strtolower($platform)) {
            'instagram' => 'fa-brands fa-instagram',
            'linkedin'  => 'fa-brands fa-linkedin-in',
            'twitter', 'x' => 'fa-brands fa-x-twitter',
            'facebook'  => 'fa-brands fa-facebook-f',
            'youtube'   => 'fa-brands fa-youtube',
            'tiktok'    => 'fa-brands fa-tiktok',
            'website'   => 'fa-solid fa-globe',
            default     => 'fa-solid fa-link',
        };
    }
}

if (! function_exists('social_label')) {
    function social_label(string $platform): string
    {
        return match (strtolower($platform)) {
            'instagram' => 'Instagram',
            'linkedin'  => 'LinkedIn',
            'twitter', 'x' => 'Twitter / X',
            'facebook'  => 'Facebook',
            'youtube'   => 'YouTube',
            'tiktok'    => 'TikTok',
            'website'   => 'Website',
            default     => ucfirst($platform),
        };
    }
}

if (! function_exists('category_filter_url')) {
    function category_filter_url(string $slug): string
    {
        return site_url('/') . '?category=' . rawurlencode($slug);
    }
}

if (!function_exists('render_cover_url')) {
    function render_cover_url($coverPath)
    {
        if (empty($coverPath)) {
            return 'https://placehold.net/800x600.png';
        }

        if (preg_match('/^https?:\/\//', $coverPath)) {
            return $coverPath;
        }

        $coverPath = ltrim($coverPath, '/');
        return base_url($coverPath);
    }
}

if (!function_exists('is_external_cover')) {
    function is_external_cover($coverPath)
    {
        return !empty($coverPath) && preg_match('/^https?:\/\//', $coverPath);
    }
}

if (! function_exists('admin_nav_is_active')) {
    function admin_nav_is_active(string $path = 'admin'): bool
    {
        $path    = trim($path, '/');
        $current = trim(service('uri')->getPath(), '/');

        if ($path === 'admin') {
            return $current === 'admin';
        }

        return $current === $path || str_starts_with($current, $path . '/');
    }
}

if (! function_exists('admin_nav_items')) {
    function admin_nav_items(): array
    {
        return [
            [
                'label'       => 'Dashboard',
                'path'        => 'admin',
                'url'         => site_url('/admin'),
                'icon'        => 'fa-solid fa-chart-pie',
                'description' => 'Ringkasan cepat isi blog dan area kerja.',
            ],
            [
                'label'       => 'Postingan',
                'path'        => 'admin/posts',
                'url'         => site_url('/admin/posts'),
                'icon'        => 'fa-regular fa-newspaper',
                'description' => 'Tempat mengelola draft dan tulisan terbit.',
            ],
            [
                'label'       => 'Kategori',
                'path'        => 'admin/categories',
                'url'         => site_url('/admin/categories'),
                'icon'        => 'fa-solid fa-folder-tree',
                'description' => 'Susun kelompok topik agar konten tetap rapi.',
            ],
            [
                'label'       => 'Tag',
                'path'        => 'admin/tags',
                'url'         => site_url('/admin/tags'),
                'icon'        => 'fa-solid fa-tags',
                'description' => 'Tambahkan penanda kecil untuk pencarian cepat.',
            ],
            // [
            //     'label'       => 'Media',
            //     'path'        => 'admin/media',
            //     'url'         => site_url('/admin/media'),
            //     'icon'        => 'fa-regular fa-images',
            //     'description' => 'Kelola gambar dan aset visual blog.',
            // ],
            [
                'label'       => 'Pengaturan',
                'path'        => 'admin/settings',
                'url'         => site_url('/admin/settings'),
                'icon'        => 'fa-solid fa-sliders',
                'description' => 'Atur tema, identitas, dan preferensi situs.',
            ]
        ];
    }
}

if (! function_exists('admin_dashboard_pages')) {
    function admin_dashboard_pages(): array
    {
        return array_values(array_filter(
            admin_nav_items(),
            static fn (array $item): bool => $item['path'] !== 'admin'
        ));
    }
}

if (! function_exists('admin_dashboard_stat_cards')) {
    function admin_dashboard_stat_cards(array $overview): array
    {
        $draftCount = (int) ($overview['draft_posts'] ?? 0);

        return [
            [
                'label'   => 'Total Postingan',
                'value'   => (string) ($overview['total_posts'] ?? 0),
                'summary' => 'Semua tulisan tersimpan rapi dan siap dikelola.',
                'note'    => 'Pusat kerja utama untuk konten blog.',
                'badge'   => 'Pusat konten',
                'badgeTone' => 'is-link',
                'icon'    => 'fa-solid fa-pen-nib',
                'tone'    => 'is-primary',
            ],
            [
                'label'   => 'Sudah Terbit',
                'value'   => (string) ($overview['published_posts'] ?? 0),
                'summary' => 'Artikel publik aktif dan siap dibaca pengunjung.',
                'note'    => 'Menunjukkan ritme publikasi yang sedang berjalan.',
                'badge'   => 'Publik aktif',
                'badgeTone' => 'is-success',
                'icon'    => 'fa-solid fa-arrow-trend-up',
                'tone'    => 'is-success',
            ],
            [
                'label'   => 'Masih Draft',
                'value'   => (string) $draftCount,
                'summary' => 'Ruang kerja untuk tulisan yang masih dirapikan.',
                'note'    => 'Mudah dipantau sebelum masuk jadwal terbit.',
                'badge'   => $draftCount > 0 ? 'Perlu ditinjau' : 'Kosong',
                'badgeTone' => 'is-warning',
                'icon'    => 'fa-solid fa-hourglass-half',
                'tone'    => $draftCount > 0 ? 'is-warning' : 'is-muted',
            ],
            [
                'label'   => 'Total Dilihat',
                'value'   => (string) ($overview['total_views'] ?? 0),
                'summary' => 'Akumulasi pembacaan dari seluruh artikel yang tersedia.',
                'note'    => 'Gambaran awal performa konten di blog.',
                'badge'   => 'Performa',
                'badgeTone' => 'is-info',
                'icon'    => 'fa-solid fa-chart-line',
                'tone'    => 'is-info',
            ],
            [
                'label'   => 'Kategori & Tag',
                'value'   => (string) ($overview['categories'] ?? 0) . ' / ' . (string) ($overview['tags'] ?? 0),
                'summary' => 'Struktur topik dan penanda agar isi blog tetap tertata.',
                'note'    => 'Fondasi navigasi dan pengelompokan tulisan.',
                'badge'   => 'Struktur',
                'badgeTone' => 'is-primary',
                'icon'    => 'fa-solid fa-layer-group',
                'tone'    => 'is-primary-soft',
            ],
            [
                'label'   => 'Media & Pengaturan',
                'value'   => (string) ($overview['media'] ?? 0) . ' / ' . (string) ($overview['settings'] ?? 0),
                'summary' => 'Aset visual dan pengaturan situs sudah siap dipakai.',
                'note'    => 'Menjadi dasar identitas dan tampilan blog.',
                'badge'   => 'Aset dasar',
                'badgeTone' => 'is-dark',
                'icon'    => 'fa-solid fa-sliders',
                'tone'    => 'is-neutral',
            ],
        ];
    }
}

if (! function_exists('admin_dashboard_recent_posts')) {
    function admin_dashboard_recent_posts(array $posts): array
    {
        return array_map(static function (array $post): array {
            $date = $post['updated_at'] ?: $post['published_at'];

            return [
                'title'        => $post['title'],
                'author_name'  => $post['author_name'],
                'updated_label'=> blog_date($date),
                'status'       => $post['status'],
                'status_label' => $post['status'] === 'published' ? 'Terbit' : 'Draft',
                'status_tag_class' => $post['status'] === 'published' ? 'is-success is-light' : 'is-warning is-light',
                'view_count'   => (int) ($post['view_count'] ?? 0),
                'url'          => site_url('/post/' . $post['slug']),
            ];
        }, $posts);
    }
}
