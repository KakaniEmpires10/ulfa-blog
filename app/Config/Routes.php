<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('about', 'AboutController::index');
$routes->get('contact', 'ContactController::index');
$routes->post('contact', 'ContactController::send');
$routes->get('post/(:segment)', 'PostController::show/$1');

$routes->group('admin', [
    'namespace' => 'App\Controllers\Admin',
    'filter'    => ['group:admin,superadmin'],
], static function ($routes) {
    $routes->get('/', 'DashboardController::index', ['as' => 'dashboard']);
    $routes->get('dashboard-data', 'DashboardController::data');
    $routes->get('media', 'PageController::media');

    // upload image form rich text editor
    $routes->post('upload/image', 'AdminPostController::uploadImage', ['as' => 'upload_image']);

    // Post related settings
    $routes->get('posts', 'AdminPostController::index', ['as' => 'posts']);
    $routes->get('posts-data', 'AdminPostController::data', ['as' => 'posts_data']);
    $routes->get('posts/create', 'AdminPostController::create', ['as' => 'posts_create']);
    $routes->post('posts', 'AdminPostController::store', ['as' => 'posts_store']);
    $routes->get('posts/(:num)/edit', 'AdminPostController::edit/$1', ['as' => 'posts_edit']);
    $routes->put('posts/(:num)', 'AdminPostController::update/$1', ['as' => 'posts_update']);
    $routes->patch('posts/(:num)/status', 'AdminPostController::updateStatus/$1', ['as' => 'posts_update_status']);
    $routes->delete('posts/(:num)', 'AdminPostController::delete/$1', ['as' => 'posts_delete']);
    
    // tags related settings
    $routes->get('tags', 'MasterController::tagsIndex', [ 'as' => 'tags' ]);
    $routes->get('tags-data', 'MasterController::tagsData', [ 'as' => 'tags_data' ]);
    $routes->post('tags', 'MasterController::tagsCreate', [ 'as' => 'tags_create' ]);
    $routes->put('tags/(:num)', 'MasterController::tagsUpdate/$1', [ 'as' => 'tags_update' ]);
    $routes->delete('tags/(:num)', 'MasterController::tagsDelete/$1', [ 'as' => 'tags_delete' ]);

    // categories related settings
    $routes->get('categories', 'MasterController::categoriesIndex', [ 'as' => 'categories' ]);
    $routes->get('categories-data', 'MasterController::categoriesData', [ 'as' => 'categories_data' ]);
    $routes->post('categories', 'MasterController::categoriesCreate', [ 'as' => 'categories_create' ]);
    $routes->put('categories/(:num)', 'MasterController::categoriesUpdate/$1', [ 'as' => 'categories_update' ]);
    $routes->delete('categories/(:num)', 'MasterController::categoriesDelete/$1', [ 'as' => 'categories_delete' ]);

    // Group Settings
    $routes->group('settings', static function ($routes) {

        // General Settings (admin/settings)
        $routes->get('/', 'SettingsController::index', ['as' => 'settings']);
        $routes->post('update', 'SettingsController::updateGeneral', ['as' => 'settings_update']);

        // Image Profile (admin/settings/image-profile)
        $routes->get('image-profile', 'SettingsController::imageProfile', ['as' => 'settings_image']);
        $routes->post('image-profile/update/cover', 'SettingsController::updateProfileCover', ['as' => 'settings_image_update_cover']);
        // $routes->post('image-profile/update/logo', 'SettingsController::updateLogo', ['as' => 'settings_image_update_logo']);
        $routes->post('image-profile/update/avatar', 'SettingsController::updateProfileAvatar', ['as' => 'settings_image_update_avatar']);

        // User Profile (admin/settings/profile)
        $routes->get('profile', 'SettingsController::profile', ['as' => 'settings_profile']);
        $routes->post('profile', 'SettingsController::updateProfile', ['as' => 'settings_profile_update']);
    });
});

service('auth')->routes($routes, ['except' => ['register']]);
