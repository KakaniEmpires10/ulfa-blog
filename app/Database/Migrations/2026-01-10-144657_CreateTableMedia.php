<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMediaTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],

            'file_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],

            'file_path' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],

            'mime_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],

            'size' => [
                'type'       => 'INT',
                'unsigned'   => true,
            ],

            'uploaded_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],

            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
        ]);

        $this->forge->addKey('id', true);

        // Foreign key ke Shield users table
        $this->forge->addForeignKey(
            'uploaded_by',
            'users',
            'id',
            'SET NULL',
            'CASCADE'
        );

        $this->forge->createTable('media');
    }

    public function down()
    {
        $this->forge->dropTable('media');
    }
}