<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductModel extends Model
{
    protected $table            = 'products'; // Sesuaikan dengan nama tabel produk Anda (amdk_app_db_products)
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    // Kolom yang diizinkan untuk diisi secara massal
    protected $allowedFields = [
        'user_id',
        'product_name',
        'description',
        'base_price',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    // protected $deletedField  = 'deleted_at'; // Jika menggunakan soft delete

    // Validation (opsional, bisa juga diatur di controller)
    protected $validationRules = [
        'product_name' => 'required|min_length[3]|max_length[150]',
        'description'  => 'permit_empty|max_length[1000]',
        'base_price'   => 'required|numeric|greater_than[0]',
        'user_id'      => 'required|integer', // user_id harus selalu ada
    ];

    protected $validationMessages = [
        'product_name' => [
            'required'   => 'Nama produk harus diisi.',
            'min_length' => 'Nama produk minimal 3 karakter.',
            'max_length' => 'Nama produk maksimal 150 karakter.'
        ],
        'base_price' => [
            'required'      => 'Harga dasar harus diisi.',
            'numeric'       => 'Harga dasar harus berupa angka.',
            'greater_than'  => 'Harga dasar harus lebih besar dari 0.'
        ],
        'user_id' => [
            'required' => 'User ID harus diisi.',
            'integer'  => 'User ID harus berupa angka.'
        ]
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;
}
