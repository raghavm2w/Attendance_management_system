<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUpdatedAtToUserShifts extends Migration
{
    public function up()
    {
         $this->forge->addColumn('user_shifts', [
            'updated_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
                'after'   => 'created_at',
            ],
        ]);
    }

    public function down()
    {
         $this->forge->dropColumn('user_shifts', 'updated_at');
    }
}
