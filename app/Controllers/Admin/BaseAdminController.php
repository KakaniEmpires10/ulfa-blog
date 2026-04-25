<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserProfileModel;

abstract class BaseAdminController extends BaseController
{
    protected function renderAdmin(string $view, array $data = []): string
    {
        $user    = auth()->user();
        $profile = $user !== null ? (new UserProfileModel())->getProfileByUserId((int) $user->id) : null;

        return view($view, $data + [
            'adminUser'     => $user,
            'adminProfile'  => $profile ?? [],
            'adminNavItems' => admin_nav_items(),
        ]);
    }
}
