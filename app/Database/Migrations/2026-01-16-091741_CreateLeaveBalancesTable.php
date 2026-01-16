<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLeaveBalancesTable extends Migration
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
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'leave_type_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'year' => [
                'type'       => 'YEAR',
                'comment'    => 'Calendar year or financial year start',
            ],
            'total_allocated' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'default'    => 0,
            ],
            'used' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'default'    => 0,
            ],
            'remaining' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'default'    => 0,
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

        // Prevent duplicate yearly balance
        $this->forge->addUniqueKey(['user_id', 'leave_type_id', 'year']);

        // Foreign keys (optional but recommended)
        $this->forge->addForeignKey(
            'leave_type_id',
            'leave_types',
            'id',
            'CASCADE',
            'CASCADE'
        );

        // Add FK to users table if exists
        $this->forge->addForeignKey(
            'user_id',
            'users',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->forge->createTable('leave_balances', true);
    }

    public function down()
    {
                $this->forge->dropTable('leave_balances', true);

    }
}
