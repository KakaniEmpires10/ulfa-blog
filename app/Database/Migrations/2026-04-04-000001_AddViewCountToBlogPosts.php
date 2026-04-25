<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddViewCountToBlogPosts extends Migration
{
    public function up()
    {
        if (! $this->db->fieldExists('view_count', 'blog_posts')) {
            $this->forge->addColumn('blog_posts', [
                'view_count' => [
                    'type'       => 'INT',
                    'unsigned'   => true,
                    'default'    => 0,
                    'after'      => 'seo_description',
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('view_count', 'blog_posts')) {
            $this->forge->dropColumn('blog_posts', 'view_count');
        }
    }
}
