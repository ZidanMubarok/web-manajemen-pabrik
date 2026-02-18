<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Models\UserModel;

class UserSeeder extends Seeder
{
    public function run()
    {
        $userModel = new UserModel();

        // Data user yang akan ditambahkan (tanpa kolom 'name')
        $users = [
            [
                'username'  => 'pabrik_admin',
                'password'  => password_hash('password123', PASSWORD_BCRYPT),
                'role'      => 'pabrik',
                'parent_id' => null,
            ],
            [
                'username'  => 'dist_jkt',
                'password'  => password_hash('password123', PASSWORD_BCRYPT),
                'role'      => 'distributor',
                'parent_id' => 1, // Asumsi ID pabrik adalah 1. Sesuaikan jika beda.
            ],
            [
                'username'  => 'agen_jatiasih',
                'password'  => password_hash('password123', PASSWORD_BCRYPT),
                'role'      => 'agen',
                'parent_id' => 2, // Asumsi ID distributor adalah 2. Sesuaikan jika beda.
            ],
            [
                'username'  => 'dist_bdg',
                'password'  => password_hash('password123', PASSWORD_BCRYPT),
                'role'      => 'distributor',
                'parent_id' => 1, // Asumsi ID pabrik adalah 1. Sesuaikan jika beda.
            ],
            [
                'username'  => 'agen_sumberair',
                'password'  => password_hash('password123', PASSWORD_BCRYPT),
                'role'      => 'agen',
                'parent_id' => 4, // Asumsi ID distributor Bandung adalah 4. Sesuaikan jika beda.
            ],
        ];

        foreach ($users as $user) {
            $userModel->insert($user);
        }

        // Opsional: Untuk mereset AUTO_INCREMENT setiap kali seeder dijalankan
        $this->db->query("ALTER TABLE users AUTO_INCREMENT = 1");

        echo "User seeder berhasil dijalankan.\n";
    }
}
