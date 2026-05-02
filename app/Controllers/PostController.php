<?php

namespace App\Controllers;

use App\Models\BlogPostModel;
use App\Models\UserProfileModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class PostController extends BaseController
{
    public function show(string $slug): string
    {
        $postModel = new BlogPostModel();
        $canPreviewDraft = auth()->loggedIn();
        $post            = $postModel->getPostBySlug($slug, $canPreviewDraft);

        if ($post === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        $isPublished = strtolower((string) ($post['status'] ?? '')) === 'published';
        if (!$isPublished && !$canPreviewDraft) {
            throw PageNotFoundException::forPageNotFound();
        }

        if ($isPublished) {
            $postModel->incrementViewCount((int) $post['id']);
        }

        $profileModel = new UserProfileModel();
        $disqusShortname = trim((string) get_setting('disqus_shortname', ''));
        $hasValidDisqusShortname = preg_match('/^[a-z0-9-]+$/', $disqusShortname) === 1;
        $commentsEnabled = get_setting('enable_comment', get_setting('enable_comments', '0')) === '1';

        return view('pages/post_detail', [
            'title'             => $post['title'],
            'seoTitle'          => $post['seo_title_value'],
            'seoDescription'    => $post['seo_description_value'],
            'post'              => $post,
            'profile'           => $profileModel->getPrimaryProfile(),
            'recentPosts'       => $postModel->getRecentPostsByAuthor((int) $post['author_id'], 3, (int) $post['id']),
            'popularPosts'      => $postModel->getPopularPosts(4, (int) $post['id']),
            'sidebarCategories' => $postModel->getSidebarCategories(),
            'isPreview'         => !$isPublished,
            'showComments'      => $commentsEnabled && $isPublished && $hasValidDisqusShortname,
            'disqusShortname'   => $disqusShortname,
        ]);
    }
}
