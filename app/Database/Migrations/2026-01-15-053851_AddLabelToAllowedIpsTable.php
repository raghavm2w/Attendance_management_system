<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddLabelToAllowedIpsTable extends Migration
{
    public function up()
    {
          $this->forge->addColumn('allowed_ips', [
            'label' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'ip_address',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('allowed_ips', 'label');

    }
}
