<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterRefreshTokenToText extends Migration
{
    public function up()
    {
                $this->forge->dropKey('refresh_tokens', 'token');

         $this->forge->modifyColumn('refresh_tokens', [
            'token' => [
                'type' => 'TEXT',
                'null' => false,
            ],
        ]);
    }

    public function down()
    {
         $this->forge->modifyColumn('refresh_tokens', [
            'token' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
        ]);
         $this->forge->addUniqueKey('token', 'token');

    }
}
