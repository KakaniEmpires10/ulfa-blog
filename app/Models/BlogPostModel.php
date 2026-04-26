<?php

namespace App\Models;

use CodeIgniter\Model;

class BlogPostModel extends Model
{
    protected $table            = 'blog_posts';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useAutoIncrement = true;
    protected $useTimestamps    = true;
    protected $allowedFields    = [
        'slug',
        'title',
        'excerpt',
        'content',
        'cover_image',
        'author_id',
        'status',
        'published_at',
        'seo_title',
        'seo_description',
        'view_count',
    ];

    protected $validationRules = [
        'title'       => 'required|min_length[5]|max_length[255]',
        'content'     => 'required'
    ];

    protected $validationMessages = [
        'title' => [
            'required'   => 'Judul berita jangan dikosongkan ya, pembaca butuh judul untuk mulai membaca.',
            'min_length' => 'Judulnya terlalu pendek, coba buat minimal 5 karakter agar lebih jelas.',
            'max_length' => 'Wah, judulnya kepanjangan. Coba persingkat agar pas saat tampil di layar.',
        ],
        'content' => [
            'required'   => 'Isi beritanya masih kosong, yuk ceritakan sesuatu yang menarik!',
        ]
    ];

    protected $allowCallbacks = true;
    protected $beforeInsert   = ['generateSlug'];
    protected $beforeUpdate   = ['generateSlug'];

    protected function generateSlug(array $data)
    {
        if (!isset($data['data']['title'])) {
            return $data;
        }

        if (isset($data['id']) && !empty($data['data']['slug'])) {
            return $data;
        }

        helper('url');

        $baseSlug = url_title($data['data']['title'], '-', true);
        $slug = $baseSlug;
        $i = 1;

        while (true) {
            $builder = $this->builder();
            $builder->where('slug', $slug);

            if (isset($data['id'])) {
                $id = is_array($data['id']) ? $data['id'][0] : $data['id'];
                $builder->where('id !=', $id);
            }

            if ($builder->countAllResults() === 0) {
                break;
            }

            // Jika duplikat, tambahkan angka di belakangnya
            $slug = $baseSlug . '-' . $i;
            $i++;
        }

        $data['data']['slug'] = $slug;

        return $data;
    }

    public function getPublishedPosts(int $limit = 10, int $offset = 0, ?int $excludeId = null, ?string $categorySlug = null): array
    {
        $builder = $this->basePublicBuilder($categorySlug)
            ->orderBy('blog_posts.published_at', 'DESC')
            ->limit($limit, $offset);

        if ($excludeId !== null) {
            $builder->where('blog_posts.id !=', $excludeId);
        }

        return $this->hydratePosts($builder->findAll());
    }

    public function getHeroPosts(string $source = 'popular', int $limit = 3, ?string $categorySlug = null): array
    {
        $builder = $this->basePublicBuilder($categorySlug)->limit($limit);

        if ($source === 'latest') {
            $builder->orderBy('blog_posts.published_at', 'DESC');
        } else {
            $builder
                ->orderBy('blog_posts.view_count', 'DESC')
                ->orderBy('blog_posts.published_at', 'DESC');
        }

        return $this->hydratePosts($builder->findAll());
    }

    public function getAdminPosts(array $filters = [], int $perPage = 20): array
    {
        $builder = $this->select('blog_posts.id, blog_posts.title, blog_posts.slug, blog_posts.excerpt, blog_posts.content, blog_posts.status, blog_posts.created_at, blog_posts.cover_image, blog_posts.seo_title, blog_posts.seo_description');

        if (!empty($filters['title'])) {
            $builder->like('blog_posts.title', $filters['title']);
        }

        if (!empty($filters['status']) && $filters['status'] !== 'all') {
            $builder->where('blog_posts.status', $filters['status']);
        }

        if (!empty($filters['startDate']) && !empty($filters['endDate'])) {
            $builder->where('blog_posts.created_at >=', $filters['startDate'] . ' 00:00:00')
                ->where('blog_posts.created_at <=', $filters['endDate'] . ' 23:59:59');
        }

        $posts = $builder->orderBy('blog_posts.created_at', 'DESC')->paginate($perPage);

        return [
            'posts' => $this->hydratePosts($posts),
            'pager' => $this->pager
        ];
    }

    public function getPopularPosts(int $limit = 4, ?int $excludeId = null, ?string $categorySlug = null): array
    {
        $builder = $this->basePublicBuilder($categorySlug)
            ->orderBy('blog_posts.view_count', 'DESC')
            ->orderBy('blog_posts.published_at', 'DESC')
            ->limit($limit);

        if ($excludeId !== null) {
            $builder->where('blog_posts.id !=', $excludeId);
        }

        return $this->hydratePosts($builder->findAll());
    }

    public function getRecentPostsByAuthor(int $authorId, int $limit = 3, ?int $excludeId = null): array
    {
        $builder = $this->basePublicBuilder()
            ->where('blog_posts.author_id', $authorId)
            ->orderBy('blog_posts.published_at', 'DESC')
            ->limit($limit);

        if ($excludeId !== null) {
            $builder->where('blog_posts.id !=', $excludeId);
        }

        return $this->hydratePosts($builder->findAll());
    }

    public function getPostBySlug(string $slug, bool $includeDraft = false): ?array
    {
        $builder = $includeDraft ? $this->basePostDetailBuilder() : $this->basePublicBuilder();

        $post = $builder
            ->where('blog_posts.slug', $slug)
            ->first();

        if ($post === null) {
            return null;
        }

        return $this->hydratePosts([$post])[0] ?? null;
    }

    public function getSidebarCategories(): array
    {
        return $this->db->table('blog_categories')
            ->select('blog_categories.name, blog_categories.slug, COUNT(blog_posts.id) AS post_count')
            ->join('blog_post_categories', 'blog_post_categories.category_id = blog_categories.id', 'left')
            ->join('blog_posts', 'blog_posts.id = blog_post_categories.post_id AND blog_posts.status = "published"', 'left')
            ->groupBy('blog_categories.id, blog_categories.name, blog_categories.slug')
            ->having('COUNT(blog_posts.id) >', 0)
            ->orderBy('blog_categories.name', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function findCategory(string $slug): ?array
    {
        return $this->db->table('blog_categories')
            ->where('slug', $slug)
            ->get()
            ->getRowArray();
    }

    public function incrementViewCount(int $postId): bool
    {
        return $this->db->table($this->table)
            ->set('view_count', 'view_count + 1', false)
            ->where('id', $postId)
            ->update();
    }

    protected function basePublicBuilder(?string $categorySlug = null)
    {
        $builder = $this->basePostDetailBuilder()
            ->where('blog_posts.status', 'published');

        if ($categorySlug !== null && $categorySlug !== '') {
            $builder
                ->join('blog_post_categories AS filtered_categories', 'filtered_categories.post_id = blog_posts.id')
                ->join('blog_categories AS active_category', 'active_category.id = filtered_categories.category_id')
                ->where('active_category.slug', $categorySlug);
        }

        return $builder;
    }

    protected function basePostDetailBuilder()
    {
        return $this->select('blog_posts.*, users.username AS author_username, COALESCE(user_profiles.display_name, users.username) AS author_name, user_profiles.avatar_path AS author_avatar')
            ->join('users', 'users.id = blog_posts.author_id')
            ->join('user_profiles', 'user_profiles.user_id = users.id', 'left');
    }

    protected function hydratePosts(array $posts): array
    {
        if ($posts === []) {
            return [];
        }

        $postIds = array_column($posts, 'id');

        $categoryMap = [];
        $categories  = $this->db->table('blog_post_categories')
            ->select('blog_post_categories.post_id, blog_categories.name, blog_categories.slug')
            ->join('blog_categories', 'blog_categories.id = blog_post_categories.category_id')
            ->whereIn('blog_post_categories.post_id', $postIds)
            ->orderBy('blog_categories.name', 'ASC')
            ->get()
            ->getResultArray();

        foreach ($categories as $category) {
            $categoryMap[$category['post_id']][] = [
                'name' => $category['name'],
                'slug' => $category['slug'],
            ];
        }

        $tagMap = [];
        $tags   = $this->db->table('blog_post_tags')
            ->select('blog_post_tags.post_id, blog_tags.name, blog_tags.slug')
            ->join('blog_tags', 'blog_tags.id = blog_post_tags.tag_id')
            ->whereIn('blog_post_tags.post_id', $postIds)
            ->orderBy('blog_tags.name', 'ASC')
            ->get()
            ->getResultArray();

        foreach ($tags as $tag) {
            $tagMap[$tag['post_id']][] = [
                'name' => $tag['name'],
                'slug' => $tag['slug'],
            ];
        }

        $coverMap = [];
        $covers   = $this->db->table('blog_post_media')
            ->select('blog_post_media.post_id, media.file_path')
            ->join('media', 'media.id = blog_post_media.media_id')
            ->where('blog_post_media.type', 'cover')
            ->whereIn('blog_post_media.post_id', $postIds)
            ->get()
            ->getResultArray();

        foreach ($covers as $cover) {
            $coverMap[$cover['post_id']] = $cover['file_path'];
        }

        foreach ($posts as &$post) {
            $post['categories']      = $categoryMap[$post['id']] ?? [];
            $post['tags']            = $tagMap[$post['id']] ?? [];
            $post['primary_category'] = $post['categories'][0] ?? null;
            $coverPath               = $coverMap[$post['id']] ?? $post['cover_image'] ?? null;
            $post['cover_url']       = $this->resolveAssetUrl($coverPath) ?? 'https://images.unsplash.com/photo-1499750310107-5fef28a66643?auto=format&fit=crop&w=1600&q=80';
            $post['excerpt_display'] = excerpt_text($post['excerpt'] ?: $post['content'], 180);
            $post['reading_time']    = reading_time($post['content']);
            $post['seo_title_value'] = $post['seo_title'] ?: $post['title'];
            $post['seo_description_value'] = $post['seo_description'] ?: excerpt_text($post['excerpt'] ?: $post['content'], 160);
        }
        unset($post);

        return $posts;
    }

    protected function resolveAssetUrl(?string $path): ?string
    {
        if ($path === null || trim($path) === '') {
            return null;
        }

        $path = trim($path);

        if (preg_match('#^(?:https?:)?//#i', $path) || str_starts_with($path, 'data:')) {
            return $path;
        }

        return base_url(ltrim($path, '/'));
    }
}
