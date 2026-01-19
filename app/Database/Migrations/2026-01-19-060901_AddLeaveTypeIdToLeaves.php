<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddLeaveTypeIdToLeaves extends Migration
{
    public function up()
    {
         $this->forge->addColumn('leaves', [
            'leave_type_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
                'after'      => 'user_id', 
            ],
        ]);

        // Add foreign key
        $this->forge->addForeignKey(
            'leave_type_id',
            'leave_types',
            'id',
            'CASCADE',   // on delete
            'CASCADE'    // on update
        );

        // Apply FK
        $this->forge->processIndexes('leaves');
    }

    public function down()
    {
          // Drop foreign key first
        $this->forge->dropForeignKey('leaves', 'leaves_leave_type_id_foreign');

        // Drop column
        $this->forge->dropColumn('leaves', 'leave_type_id');
    
    }
}
