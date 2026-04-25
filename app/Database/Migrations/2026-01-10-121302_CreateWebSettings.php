<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateWebSettings extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'key'   => ['type' => 'VARCHAR', 'constraint' => 100],
            'value' => ['type' => 'TEXT', 'null' => true],
        ]);

        $this->forge->addKey('key', true);
        $this->forge->createTable('web_settings');
    }

    public function down()
    {
        $this->forge->dropTable('web_settings');
    }
}