<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ModifyEndDateNullable extends Migration
{
    public function up()
    {
         $this->forge->modifyColumn('leaves', [
            'end_date' => [
                'type' => 'DATE',
                'null' => true,   
                'default' => null
            ],
        ]);
    }

    public function down()
    {
         $this->forge->modifyColumn('leaves', [
            'end_date' => [
                'type' => 'DATE',
                'null' => false,  
            ],
        ]);
    }
}
