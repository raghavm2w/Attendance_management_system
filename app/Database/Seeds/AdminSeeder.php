<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run()
    {
        $this->db->table('users')->insert([
            'name'          => 'System Admin',
            'email'         => 'admin@gmail.com',
            'password_hash'=> password_hash('admin123', PASSWORD_DEFAULT),
            'role'          => 'admin',
            'is_active'        => 1,
            'created_at'    => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s'),
        ]);
    }
}
