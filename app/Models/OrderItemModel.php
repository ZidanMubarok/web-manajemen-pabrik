<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderItemModel extends Model
{
    protected $table = 'order_items'; // amdk_app_db_order_items
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = ['order_id', 'product_id', 'product_name', 'quantity', 'unit_price']; // pastikan semua kolom yang relevan ada
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getItemsWithProductDetails(int $orderId): array
    {
        return $this->db->table($this->table . ' oi') // Menggunakan $this->table untuk konsistensi
            ->select('oi.*, p.base_price, p.product_name') // Pilih semua kolom order_items dan base_price dari products
            ->join('products p', 'p.id = oi.product_id', 'left') // Gunakan LEFT JOIN
            ->where('oi.order_id', $orderId)
            ->get()
            ->getResultArray(); // Mengembalikan hasil sebagai array of arrays
    }
}
