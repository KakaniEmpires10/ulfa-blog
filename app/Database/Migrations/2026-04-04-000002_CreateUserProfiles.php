<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUserProfiles extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('user_profiles')) {
            return;
        }

        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'display_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'bio' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'avatar_path' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'cover_image_path' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'about_heading' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'about_content' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'quote_text' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'social_links' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('user_id');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('user_profiles');
    }

    public function down()
    {
        if ($this->db->tableExists('user_profiles')) {
            $this->forge->dropTable('user_profiles');
        }
    }
}
