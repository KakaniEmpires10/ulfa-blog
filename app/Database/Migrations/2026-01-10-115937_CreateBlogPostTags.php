<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBlogPostTags extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'post_id'  => ['type' => 'INT', 'unsigned' => true],
            'tag_id'   => ['type' => 'INT'],
        ]);
        $this->forge->addForeignKey('post_id', 'blog_posts', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('tag_id', 'blog_tags', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('blog_post_tags');
    }

    public function down()
    {
        $this->forge->dropTable('blog_post_tags');
    }
}