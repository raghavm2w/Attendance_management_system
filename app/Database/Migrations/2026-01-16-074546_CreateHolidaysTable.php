<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateHolidaysTable extends Migration
{
    public function up()
    {
         $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'holiday_date' => [
                'type' => 'DATE',
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'is_optional' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'comment'    => '0=mandatory, 1=optional',
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
        $this->forge->addUniqueKey('holiday_date');
        $this->forge->createTable('holidays', true);
    }

    public function down()
    {
        $this->forge->dropTable('holidays', true);

    }
}
