<?php

namespace App\Controllers;

use App\Models\BlogPostModel;
use App\Models\UserProfileModel;

class AboutController extends BaseController
{
    public function index(): string
    {
        $profileModel = new UserProfileModel();
        $postModel    = new BlogPostModel();
        $profile      = $profileModel->getPrimaryProfile() ?? [];

        return view('pages/about', [
            'title'          => 'Tentang',
            'seoTitle'       => 'Tentang | ' . get_setting('site_name', 'Ulfa Blog'),
            'seoDescription' => excerpt_text($profile['about_content'] ?? get_setting('site_description', ''), 160),
            'profile'        => $profile,
            'recentPosts'    => $profile !== [] ? $postModel->getRecentPostsByAuthor((int) $profile['user_id'], 6) : [],
        ]);
    }
}
