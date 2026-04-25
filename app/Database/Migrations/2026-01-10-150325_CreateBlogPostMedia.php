<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBlogPostMedia extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'post_id' => ['type' => 'INT', 'unsigned' => true],
            'media_id' => ['type' => 'INT', 'unsigned' => true],
            'type' => [
                'type' => 'ENUM',
                'constraint' => ['cover', 'inline', 'gallery'],
                'default' => 'inline'
            ],
        ]);

        $this->forge->addForeignKey('post_id', 'blog_posts', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('media_id', 'media', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('blog_post_media');
    }

    public function down()
    {
        $this->forge->dropTable('blog_post_media');
    }
}
