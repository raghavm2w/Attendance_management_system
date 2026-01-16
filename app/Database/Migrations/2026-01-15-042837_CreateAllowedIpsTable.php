<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAllowedIpsTable extends Migration
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
            'ip_address' => [
                'type'       => 'VARCHAR',
                'constraint' => 50, // supports IPv4 + IPv6
                'null'       => false,
            ],
            'is_active' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
                 'comment'    => '0-inactive 1-active',
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
        $this->forge->addUniqueKey('ip_address'); 
        $this->forge->createTable('allowed_ips', true);
    
    }

    public function down()
    {
                $this->forge->dropTable('allowed_ips', true);

    }
}
