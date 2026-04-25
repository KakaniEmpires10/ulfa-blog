<?php

namespace App\Controllers;

use App\Models\BlogPostModel;
use App\Models\UserProfileModel;

class Home extends BaseController
{
    public function index(): string
    {
        $postModel    = new BlogPostModel();
        $profileModel = new UserProfileModel();
        $sliderSource = get_setting('homepage_slider_source', 'popular');
        $sliderLimit  = max(1, (int) get_setting('homepage_slider_limit', 3));
        $categorySlug = trim((string) $this->request->getGet('category'));
        $activeCategory = $categorySlug !== '' ? $postModel->findCategory($categorySlug) : null;

        $profile = $profileModel->getPrimaryProfile() ?? [];

        return view('pages/home', [
            'title'             => get_setting('site_name', 'Ulfa Blog'),
            'seoTitle'          => get_setting('site_name', 'Ulfa Blog'),
            'seoDescription'    => get_setting('site_description', 'Blog pribadi tentang perjalanan, buku, film, dan hidup yang berjalan perlahan.'),
            'sliderPosts'       => $postModel->getHeroPosts($sliderSource, $sliderLimit, $categorySlug ?: null),
            'posts'             => $postModel->getPublishedPosts(6, 0, null, $categorySlug ?: null),
            'popularPosts'      => $postModel->getPopularPosts(4, null, $categorySlug ?: null),
            'sidebarCategories' => $postModel->getSidebarCategories(),
            'profile'           => $profile,
            'sliderSource'      => $sliderSource,
            'activeCategory'    => $activeCategory,
        ]);
    }
}
