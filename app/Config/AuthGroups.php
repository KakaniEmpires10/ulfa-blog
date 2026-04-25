<?php

declare(strict_types=1);

/**
 * This file is part of CodeIgniter Shield.
 *
 * (c) CodeIgniter Foundation <admin@codeigniter.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Config;

use CodeIgniter\Shield\Config\AuthGroups as ShieldAuthGroups;

class AuthGroups extends ShieldAuthGroups
{
    /**
     * --------------------------------------------------------------------
     * Default Group
     * --------------------------------------------------------------------
     * The group that a newly registered user is added to.
     */
    public string $defaultGroup = 'user';

    /**
     * --------------------------------------------------------------------
     * Groups
     * --------------------------------------------------------------------
     * An associative array of the available groups in the system, where the keys
     * are the group names and the values are arrays of the group info.
     *
     * Whatever value you assign as the key will be used to refer to the group
     * when using functions such as:
     *      $user->addGroup('superadmin');
     *
     * @var array<string, array<string, string>>
     *
     * @see https://codeigniter4.github.io/shield/quick_start_guide/using_authorization/#change-available-groups for more info
     */
    public array $groups = [
        'superadmin' => [
            'title'       => 'Super Admin',
            'description' => 'Full access to everything.',
        ],
        'admin' => [
            'title'       => 'Administrator',
            'description' => 'Manage site and all content.',
        ],
        'author' => [
            'title'       => 'Author',
            'description' => 'Write and manage own posts.',
        ],
        'user' => [
            'title'       => 'User',
            'description' => 'Regular reader.',
        ],
    ];


    /**
     * --------------------------------------------------------------------
     * Permissions
     * --------------------------------------------------------------------
     * The available permissions in the system.
     *
     * If a permission is not listed here it cannot be used.
     */
    public array $permissions = [
        'admin.access' => 'Access admin dashboard',
        'admin.settings' => 'Manage site settings',

        'posts.create' => 'Create blog posts',
        'posts.edit'   => 'Edit blog posts',
        'posts.delete' => 'Delete blog posts',
        'posts.publish' => 'Publish blog posts',

        'users.manage' => 'Manage users',
    ];


    /**
     * --------------------------------------------------------------------
     * Permissions Matrix
     * --------------------------------------------------------------------
     * Maps permissions to groups.
     *
     * This defines group-level permissions.
     */
    public array $matrix = [
        'superadmin' => [
            'admin.*',
            'posts.*',
            'users.*',
        ],

        'admin' => [
            'admin.access',
            'admin.settings',
            'posts.*',
            'users.manage',
        ],

        'author' => [
            'admin.access',
            'posts.create',
            'posts.edit',
            'posts.delete',
        ],

        'user' => [],
    ];
}
