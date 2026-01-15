<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSettingsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
    'id' => [
        'type' => 'INT',
        'auto_increment' => true,
        'unsigned' => true,
    ],
    'key' => [
        'type' => 'VARCHAR',
        'constraint' => 100,
        'unique' => true,
    ],
    'value' => [
        'type' => 'VARCHAR',
        'constraint' => 255,
    ],
    'created_at' => ['type' => 'DATETIME', 'null' => true],
    'updated_at' => ['type' => 'DATETIME', 'null' => true],
]);
                $this->forge->addKey('id', true);

        $this->forge->createTable('settings', true);


    }

    public function down()
    {
        $this->forge->dropTable('settings', true);

    }
}
