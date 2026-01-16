<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterAttendanceStatusToTinyint extends Migration
{
    public function up()
    {
         $this->forge->modifyColumn('attendance', [
            'status' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'null'       => true,
                'comment'    => '0=Absent,1=Present,2=Half-day,3=In-progress',
            ],
        ]);
    }

    public function down()
    {
         $this->forge->modifyColumn('attendance', [
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
            ],
        ]);
    }
}
