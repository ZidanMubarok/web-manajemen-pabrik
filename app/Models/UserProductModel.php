<?php

namespace App\Models;

use CodeIgniter\Model;

class UserProductModel extends Model
{
    protected $table            = 'user_products'; // Sesuaikan dengan nama tabel amdk_app_db_user_products
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    // Kolom yang diizinkan untuk diisi secara massal
    protected $allowedFields = [
        'user_id',      // Ini akan menjadi ID Distributor
        'product_id',   // Ini adalah ID Produk dari pabrik
        'custom_price',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation Rules
    protected $validationRules = [
        'user_id'      => 'required|integer',
        'product_id'   => 'required|integer',
        'custom_price' => 'required|numeric|greater_than[0]',
    ];

    protected $validationMessages = [
        'user_id' => [
            'required' => 'User ID harus diisi.',
            'integer'  => 'User ID harus berupa angka.'
        ],
        'product_id' => [
            'required' => 'Product ID harus diisi.',
            'integer'  => 'Product ID harus berupa angka.'
        ],
        'custom_price' => [
            'required'     => 'Harga kustom harus diisi.',
            'numeric'      => 'Harga kustom harus berupa angka.',
            'greater_than' => 'Harga kustom harus lebih besar dari 0.'
        ],
    ];

    protected $skipValidation = false;
}
