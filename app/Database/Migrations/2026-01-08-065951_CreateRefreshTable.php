<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRefreshTable extends Migration
{
    public function up()
    {
         $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
                'comment'        => 'Primary key',
            ],

            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'comment'    => 'Reference to users table',
            ],

            'token' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'comment'    => 'Refresh JWT token',
            ],

            'expires_at' => [
                'type'    => 'DATETIME',
                'comment' => 'Token expiry time (UTC)',
            ],

            'created_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
                'comment' => 'Token creation time (UTC)',
            ],
        ]);

        // Primary key
        $this->forge->addKey('id', true);

        // Indexes
        $this->forge->addKey('user_id');
        $this->forge->addUniqueKey('token');

        // Foreign key
        $this->forge->addForeignKey(
            'user_id',
            'users',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->forge->createTable('refresh_tokens');
    }

    public function down()
    {
                $this->forge->dropTable('refresh_tokens');

    }
}
