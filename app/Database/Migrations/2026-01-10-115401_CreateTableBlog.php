<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTableBlog extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'                => ['type' => 'INT', 'auto_increment' => true, 'unsigned' => true],
            'slug'              => ['type' => 'VARCHAR', 'constraint' => 200],
            'title'             => ['type' => 'VARCHAR', 'constraint' => 255],
            'excerpt'           => ['type' => 'TEXT', 'null' => true],
            'content'           => ['type' => 'LONGTEXT'],
            'cover_image'       => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'author_id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'status'            => ['type' => 'ENUM', 'constraint' => ['draft', 'published'], 'default' => 'draft'],
            'published_at'      => ['type' => 'DATETIME', 'null' => true],

            'seo_title'         => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'seo_description'   => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],

            'created_at'        => ['type' => 'DATETIME'],
            'updated_at'        => ['type' => 'DATETIME'],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('slug');
        $this->forge->addForeignKey('author_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('blog_posts');
    }

    public function down()
    {
        $this->forge->dropTable('blog_posts');
    }
}
