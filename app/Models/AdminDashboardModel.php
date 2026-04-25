<?php

namespace App\Models;

use CodeIgniter\Database\BaseConnection;

class AdminDashboardModel
{
    protected BaseConnection $db;

    public function __construct()
    {
        $this->db = db_connect();
    }

    public function getOverview(): array
    {
        $views = $this->db->table('blog_posts')
            ->selectSum('view_count', 'total_views')
            ->get()
            ->getRowArray();

        return [
            'total_posts'     => $this->db->table('blog_posts')->countAllResults(),
            'published_posts' => $this->db->table('blog_posts')->where('status', 'published')->countAllResults(),
            'draft_posts'     => $this->db->table('blog_posts')->where('status', 'draft')->countAllResults(),
            'categories'      => $this->db->table('blog_categories')->countAllResults(),
            'tags'            => $this->db->table('blog_tags')->countAllResults(),
            'media'           => $this->db->table('media')->countAllResults(),
            'settings'        => $this->db->table('web_settings')->countAllResults(),
            'total_views'     => (int) ($views['total_views'] ?? 0),
        ];
    }

    public function getRecentPosts(int $limit = 6): array
    {
        return $this->db->table('blog_posts')
            ->select('blog_posts.id, blog_posts.title, blog_posts.slug, blog_posts.status, blog_posts.published_at, blog_posts.updated_at, blog_posts.view_count, COALESCE(user_profiles.display_name, users.username) AS author_name')
            ->join('users', 'users.id = blog_posts.author_id')
            ->join('user_profiles', 'user_profiles.user_id = users.id', 'left')
            ->orderBy('blog_posts.updated_at', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }
}
