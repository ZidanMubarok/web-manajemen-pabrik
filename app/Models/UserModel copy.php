<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table      = 'users';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    // Ubah 'email' menjadi 'username' di allowedFields
    protected $allowedFields = ['username', 'password', 'role', 'parent_id','created_at', 'updated_at'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation Rules
    protected $validationRules = [
        'username'  => 'required|min_length[3]|max_length[155]|alpha_dash', // Username harus unik dan hanya huruf, angka, dash, underscore
        'password'  => 'required|min_length[3]',
        'role'      => 'required|in_list[agen,distributor,pabrik]',
        'parent_id' => 'permit_empty|is_natural_no_zero',
    ];
    protected $validationMessages = [
        'username' => [
            'is_unique'    => 'Maaf, username ini sudah digunakan.',
            'min_length'   => 'Username minimal 3 karakter.',
            'max_length'   => 'Username maksimal 155 karakter.',
            'alpha_dash'   => 'Username hanya boleh berisi huruf, angka, dash (-), atau underscore (_).'
        ],
        'password' => [
            'min_length' => 'Password minimal 4 karakter.'
        ]
    ];
    protected $skipValidation = false;

    // Callback untuk hashing password sebelum disimpan (tidak berubah)
    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    protected function hashPassword(array $data)
    {
        if (isset($data['data']['password'])) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_BCRYPT);
        }
        return $data;
    }

    // Metode kustom untuk mendapatkan user berdasarkan role
    public function getUsersByRole(string $role, int $limit = 0, int $offset = 0)
    {
        $builder = $this->where('role', $role)->orderBy('created_at', 'DESC'); // Atau orderBy('last_login', 'DESC');
        if ($limit > 0) {
            $builder->limit($limit, $offset);
        }
        return $builder->findAll();
    }

    public function countUsersByRole(string $role)
    {
        return $this->where('role', $role)->countAllResults();
    }
}
