<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBlogPostCategories extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'post_id'       => ['type' => 'INT', 'unsigned' => true],
            'category_id'   => ['type' => 'INT'],
        ]);
        $this->forge->addForeignKey('post_id', 'blog_posts', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('category_id', 'blog_categories', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('blog_post_categories');
    }

    public function down()
    {
        $this->forge->dropTable('blog_post_categories');
    }
}