<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class BlogPostSeeder extends Seeder
{
    public function run()
    {
        $author = $this->db->table('users')->where('username', 'ulfa')->get()->getRowArray();

        if (! $author) {
            return;
        }

        $this->seedCategories();
        $this->seedTags();
        $this->seedPosts((int) $author['id']);
    }

    protected function seedCategories(): void
    {
        $categories = [
            ['name' => 'Perjalanan', 'slug' => 'travel'],
            ['name' => 'Buku', 'slug' => 'books'],
            ['name' => 'Film', 'slug' => 'films'],
            ['name' => 'Catatan Hidup', 'slug' => 'life-notes'],
        ];

        foreach ($categories as $category) {
            $existing = $this->db->table('blog_categories')->where('slug', $category['slug'])->get()->getRowArray();

            if ($existing) {
                $this->db->table('blog_categories')->where('id', $existing['id'])->update($category);
            } else {
                $this->db->table('blog_categories')->insert($category);
            }
        }

        $this->mergeLegacyCategories([
            'buku'             => 'books',
            'film'             => 'films',
            'catatan-pribadi'  => 'life-notes',
        ]);
    }

    protected function seedTags(): void
    {
        $tags = [
            ['name' => 'Hidup Perlahan', 'slug' => 'mindful-living'],
            ['name' => 'Ulasan', 'slug' => 'review'],
            ['name' => 'Catatan Akhir Pekan', 'slug' => 'weekend-notes'],
            ['name' => 'Perjalanan Pelan', 'slug' => 'slow-travel'],
            ['name' => 'Kerja Kreatif', 'slug' => 'creative-work'],
        ];

        foreach ($tags as $tag) {
            $existing = $this->db->table('blog_tags')->where('slug', $tag['slug'])->get()->getRowArray();

            if ($existing) {
                $this->db->table('blog_tags')->where('id', $existing['id'])->update($tag);
            } else {
                $this->db->table('blog_tags')->insert($tag);
            }
        }
    }

    protected function mergeLegacyCategories(array $slugMap): void
    {
        foreach ($slugMap as $legacySlug => $canonicalSlug) {
            $legacy = $this->db->table('blog_categories')->where('slug', $legacySlug)->get()->getRowArray();

            if (! $legacy) {
                continue;
            }

            $canonical = $this->db->table('blog_categories')->where('slug', $canonicalSlug)->get()->getRowArray();

            if (! $canonical) {
                continue;
            }

            $relations = $this->db->table('blog_post_categories')
                ->where('category_id', $legacy['id'])
                ->get()
                ->getResultArray();

            foreach ($relations as $relation) {
                $exists = $this->db->table('blog_post_categories')
                    ->where('post_id', $relation['post_id'])
                    ->where('category_id', $canonical['id'])
                    ->get()
                    ->getRowArray();

                if (! $exists) {
                    $this->db->table('blog_post_categories')->insert([
                        'post_id'     => $relation['post_id'],
                        'category_id' => $canonical['id'],
                    ]);
                }
            }

            $this->db->table('blog_post_categories')->where('category_id', $legacy['id'])->delete();
            $this->db->table('blog_categories')->where('id', $legacy['id'])->delete();
        }
    }

    protected function seedPosts(int $authorId): void
    {
        $now          = date('Y-m-d H:i:s');
        $categoryIds  = $this->mapIds('blog_categories');
        $tagIds       = $this->mapIds('blog_tags');
        $posts        = $this->posts();

        foreach ($posts as $post) {
            $payload = [
                'slug'            => $post['slug'],
                'title'           => $post['title'],
                'excerpt'         => $post['excerpt'],
                'content'         => $post['content'],
                'cover_image'     => $post['cover_image'],
                'author_id'       => $authorId,
                'status'          => 'published',
                'published_at'    => $post['published_at'],
                'seo_title'       => $post['seo_title'] ?? $post['title'],
                'seo_description' => $post['seo_description'] ?? $post['excerpt'],
                'view_count'      => $post['view_count'],
                'updated_at'      => $now,
            ];

            $existing = $this->db->table('blog_posts')->where('slug', $post['slug'])->get()->getRowArray();

            if ($existing) {
                $this->db->table('blog_posts')->where('id', $existing['id'])->update($payload);
                $postId = (int) $existing['id'];
            } else {
                $payload['created_at'] = $now;
                $this->db->table('blog_posts')->insert($payload);
                $postId = (int) $this->db->insertID();
            }

            foreach ($post['categories'] as $slug) {
                $categoryId = $categoryIds[$slug] ?? null;

                if (! $categoryId) {
                    continue;
                }

                $relation = $this->db->table('blog_post_categories')
                    ->where('post_id', $postId)
                    ->where('category_id', $categoryId)
                    ->get()
                    ->getRowArray();

                if (! $relation) {
                    $this->db->table('blog_post_categories')->insert([
                        'post_id'     => $postId,
                        'category_id' => $categoryId,
                    ]);
                }
            }

            foreach ($post['tags'] as $slug) {
                $tagId = $tagIds[$slug] ?? null;

                if (! $tagId) {
                    continue;
                }

                $relation = $this->db->table('blog_post_tags')
                    ->where('post_id', $postId)
                    ->where('tag_id', $tagId)
                    ->get()
                    ->getRowArray();

                if (! $relation) {
                    $this->db->table('blog_post_tags')->insert([
                        'post_id' => $postId,
                        'tag_id'  => $tagId,
                    ]);
                }
            }

            $media = $this->db->table('media')->where('file_path', $post['cover_image'])->get()->getRowArray();

            if (! $media) {
                $this->db->table('media')->insert([
                    'file_name'   => basename(parse_url($post['cover_image'], PHP_URL_PATH) ?: $post['slug'] . '.jpg'),
                    'file_path'   => $post['cover_image'],
                    'mime_type'   => 'image/jpeg',
                    'size'        => 0,
                    'uploaded_by' => $authorId,
                    'created_at'  => $now,
                ]);

                $mediaId = (int) $this->db->insertID();
            } else {
                $mediaId = (int) $media['id'];
            }

            $coverRelation = $this->db->table('blog_post_media')
                ->where('post_id', $postId)
                ->where('media_id', $mediaId)
                ->where('type', 'cover')
                ->get()
                ->getRowArray();

            if (! $coverRelation) {
                $this->db->table('blog_post_media')->insert([
                    'post_id'  => $postId,
                    'media_id' => $mediaId,
                    'type'     => 'cover',
                ]);
            }
        }
    }

    protected function mapIds(string $table): array
    {
        $rows = $this->db->table($table)->get()->getResultArray();
        $map  = [];

        foreach ($rows as $row) {
            $map[$row['slug']] = (int) $row['id'];
        }

        return $map;
    }

    protected function posts(): array
    {
        return [
            [
                'title'        => 'Optimalisasi Perjalanan ke Jepang: menyusun itinerary yang tenang dan realistis',
                'slug'         => 'optimalisasi-perjalanan-ke-jepang',
                'excerpt'      => 'Catatan tentang menyusun perjalanan yang tidak tergesa, dari memilih area menginap sampai mengatur ritme harian yang lebih manusiawi.',
                'content'      => '<p>Perjalanan yang menyenangkan biasanya dimulai dari itinerary yang tidak terlalu penuh. Ketika setiap hari diisi terlalu banyak tempat, kita justru kehilangan ruang untuk menikmati suasana kota, menemukan toko kecil yang menarik, atau sekadar duduk lebih lama di kafe yang nyaman.</p><p>Untuk perjalanan ke Jepang, saya lebih suka membagi tujuan berdasarkan area daripada daftar tempat yang ingin dicentang. Cara ini membuat perpindahan lebih singkat, energi lebih terjaga, dan pengalaman terasa lebih utuh.</p><p>Hal lain yang penting adalah memberi jeda. Satu pagi tanpa agenda, satu sore tanpa target, atau satu hari yang dipakai hanya untuk berjalan santai bisa membuat seluruh perjalanan terasa jauh lebih hangat.</p>',
                'cover_image'  => 'https://images.unsplash.com/photo-1493976040374-85c8e12f0c0e?auto=format&fit=crop&w=1600&q=80',
                'categories'   => ['travel'],
                'tags'         => ['slow-travel', 'weekend-notes'],
                'published_at' => '2026-03-29 08:00:00',
                'view_count'   => 328,
            ],
            [
                'title'        => 'Membaca ulang Atomic Habits setelah satu tahun: apa yang benar-benar tinggal',
                'slug'         => 'membaca-ulang-atomic-habits',
                'excerpt'      => 'Buku ini tidak hanya soal kebiasaan kecil, tetapi juga soal cara kita menciptakan sistem yang ramah untuk diri sendiri.',
                'content'      => '<p>Setelah satu tahun, bagian paling berkesan dari Atomic Habits bukanlah daftar taktiknya, melainkan gagasan bahwa perubahan besar lebih mudah dipertahankan bila kita membangun lingkungan yang mendukung.</p><p>Saya menyadari bahwa konsistensi sering kali gagal bukan karena niat yang lemah, tetapi karena sistem yang terlalu rumit. Ketika target dibuat lebih sederhana, proses justru jadi lebih mungkin diulang.</p><p>Membaca ulang buku ini juga membuat saya lebih sabar. Tidak semua hasil datang cepat, tetapi kebiasaan baik yang kecil sering menjadi fondasi untuk banyak hal lain di kemudian hari.</p>',
                'cover_image'  => 'https://images.unsplash.com/photo-1512820790803-83ca734da794?auto=format&fit=crop&w=1600&q=80',
                'categories'   => ['books'],
                'tags'         => ['review', 'mindful-living'],
                'published_at' => '2026-03-24 09:30:00',
                'view_count'   => 214,
            ],
            [
                'title'        => 'Forrest Gump dan pelajaran tentang berjalan pelan tanpa kehilangan arah',
                'slug'         => 'forrest-gump-dan-berjalan-pelan',
                'excerpt'      => 'Film ini terasa sederhana di permukaan, tetapi justru menyimpan banyak pelajaran tentang ketulusan, ritme, dan keteguhan.',
                'content'      => '<p>Forrest Gump selalu terasa menenangkan untuk ditonton ulang. Ia tidak datang dengan dialog yang rumit, tetapi justru meninggalkan kesan yang panjang karena caranya memandang hidup begitu jujur.</p><p>Ada banyak momen yang mengingatkan bahwa tidak semua hal perlu dijelaskan secara berlebihan. Kadang, berjalan terus dengan niat yang baik sudah cukup untuk sampai ke tempat yang penting.</p><p>Film ini juga membuat saya percaya bahwa hidup yang baik bukan selalu hidup yang paling cepat, tetapi hidup yang dijalani dengan hati yang tetap lembut.</p>',
                'cover_image'  => 'https://images.unsplash.com/photo-1489599849927-2ee91cede3ba?auto=format&fit=crop&w=1600&q=80',
                'categories'   => ['films'],
                'tags'         => ['review', 'mindful-living'],
                'published_at' => '2026-03-18 19:15:00',
                'view_count'   => 276,
            ],
            [
                'title'        => 'Catatan Minggu Pagi: rumah, teh hangat, dan daftar kecil yang menenangkan',
                'slug'         => 'catatan-minggu-pagi',
                'excerpt'      => 'Ada masa ketika akhir pekan terbaik bukan yang paling sibuk, tetapi yang memberi ruang untuk pulih dan menata pikiran.',
                'content'      => '<p>Minggu pagi sering menjadi waktu favorit saya untuk mengembalikan ritme. Tidak ada target besar, hanya beberapa pekerjaan rumah sederhana, secangkir teh hangat, dan daftar kecil yang terasa mungkin diselesaikan.</p><p>Ruang yang tenang membuat banyak hal terlihat lebih jelas. Ide-ide yang sempat bising selama seminggu perlahan menemukan bentuk ketika kita tidak memaksa diri untuk bergerak terlalu cepat.</p><p>Semakin dewasa, saya merasa akhir pekan yang baik adalah akhir pekan yang membuat Senin terasa lebih ringan.</p>',
                'cover_image'  => 'https://images.unsplash.com/photo-1500534314209-a25ddb2bd429?auto=format&fit=crop&w=1600&q=80',
                'categories'   => ['life-notes'],
                'tags'         => ['weekend-notes', 'mindful-living'],
                'published_at' => '2026-03-12 07:10:00',
                'view_count'   => 167,
            ],
            [
                'title'        => 'Menyimpan kenangan perjalanan lewat foto yang lebih jujur',
                'slug'         => 'foto-perjalanan-yang-lebih-jujur',
                'excerpt'      => 'Bukan tentang mengambil gambar paling sempurna, tetapi tentang menyimpan momen yang benar-benar terasa dekat.',
                'content'      => '<p>Dulu saya sering merasa foto perjalanan harus selalu terlihat rapi dan spektakuler. Lama-kelamaan saya sadar, foto yang paling saya sukai justru yang menangkap suasana kecil: tiket kereta, cahaya sore di jendela, atau sudut jalan yang tidak terkenal.</p><p>Foto-foto seperti itu mungkin tidak selalu terlihat paling menarik di media sosial, tetapi justru paling kuat ketika dikenang kembali. Ia membawa kita pulang ke rasa yang pernah ada.</p><p>Karena itu, saya mulai memotret dengan lebih pelan. Lebih sedikit, tapi lebih jujur.</p>',
                'cover_image'  => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1600&q=80',
                'categories'   => ['travel', 'life-notes'],
                'tags'         => ['creative-work', 'slow-travel'],
                'published_at' => '2026-03-08 16:45:00',
                'view_count'   => 192,
            ],
            [
                'title'        => 'Belajar menulis lebih rutin tanpa membuat proses terasa berat',
                'slug'         => 'belajar-menulis-lebih-rutin',
                'excerpt'      => 'Kebiasaan menulis yang bertahan lama sering lahir dari target yang sederhana dan ruang kerja yang tidak mengintimidasi.',
                'content'      => '<p>Saya pernah mencoba banyak sistem menulis: target kata harian, jadwal editorial yang ketat, sampai template yang terlalu detail. Semuanya tampak bagus, tetapi tidak semuanya cocok untuk ritme hidup yang nyata.</p><p>Pada akhirnya, cara yang paling membantu justru yang paling sederhana. Menyiapkan daftar ide kecil, membuka dokumen tanpa menuntut hasil sempurna, dan memberi izin untuk menulis buruk di draft pertama.</p><p>Rutinitas yang lembut sering lebih bertahan lama daripada sistem yang terlihat rapi tetapi terlalu berat dijalani.</p>',
                'cover_image'  => 'https://images.unsplash.com/photo-1455390582262-044cdead277a?auto=format&fit=crop&w=1600&q=80',
                'categories'   => ['life-notes'],
                'tags'         => ['creative-work', 'mindful-living'],
                'published_at' => '2026-03-02 11:20:00',
                'view_count'   => 145,
            ],
        ];
    }
}
