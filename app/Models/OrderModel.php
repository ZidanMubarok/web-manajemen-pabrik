<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderModel extends Model
{
    protected $table            = 'orders';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    // PASTIKAN SEMUA KOLOM INI ADA DAN SESUAI DENGAN $orderData
    protected $allowedFields = ['agen_id', 'distributor_id', 'status', 'order_date', 'delivery_date', 'total_amount', 'created_at', 'updated_at', 'pabrik_id'];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        // 'id'  => 'required|is_unique[orders.order_id_unik,id,{id}]',
        // 'agen_id'        => 'required|integer', // Ini adalah user_id dari agen
        // 'distributor_id' => 'required|integer', // Ini adalah user_id dari distributor
        // 'status'   => 'required|in_list[pending,approved,processing,shipped,completed,rejected]',
        // 'order_date'  => 'required|valid_date',
        // 'delivery_date'  => 'valid_date',
        // 'total_amount'    => 'required|numeric|greater_than_equal_to[0]',
    ];
    protected $validationMessages = [];
    protected $skipValidation = false;
    // protected $cleanValidationRules = true;

    /**
     * Join dengan tabel users untuk mendapatkan nama agen dan distributor
     */
    public function getOrdersWithAgentAndDistributorUsers()
    {
        // Menggunakan alias untuk join ke tabel users dua kali
        return $this->select('orders.*,
                              agen.username as nama_agen,
                              distributor.username as nama_distributor')
            ->join('users as agen', 'agen.id = orders.agen_id', 'left')
            ->join('users as distributor', 'distributor.id = orders.distributor_id', 'left')
            ->findAll();
    }
    // Get orders by agen IDs
    public function getOrdersByAgenIds(array $agenIds)
    {
        return $this->whereIn('agen_id', $agenIds)
            ->findAll();
    }
    // public function getHistoryOrders(
    //     int $distributorId,
    //     ?string $searchOrderId = null,
    //     ?string $searchAgentName = null,
    //     // Ubah tipe data $statusFilter menjadi array atau string (jika hanya 1 status)
    //     // atau gunakan 'mixed' jika memungkinkan. Array lebih disarankan.
    //     $statusFilter = null,
    //     ?string $startDate = null,
    //     ?string $endDate = null
    // ) {
    //     $builder = $this->db->table('orders');
    //     $builder->select('orders.*, users.username as agen_username');
    //     $builder->join('users', 'users.id = orders.agen_id');
    //     $builder->where('orders.distributor_id', $distributorId);
    //     $builder->whereNotIn('orders.status', ['pending']); // Kecualikan 'pending' dari history

    //     // Tambahkan kondisi pencarian berdasarkan ID Order
    //     if ($searchOrderId) {
    //         $builder->like('orders.id', $searchOrderId);
    //     }

    //     // Tambahkan kondisi pencarian berdasarkan Nama Agen
    //     if ($searchAgentName) {
    //         $builder->like('users.username', $searchAgentName);
    //     }

    //     // --- Perubahan Penting di Sini ---
    //     // Tambahkan kondisi filter status jika ada input status
    //     if (!empty($statusFilter)) {
    //         // Jika $statusFilter adalah array (dari URL: ?status[]=s1&status[]=s2)
    //         if (is_array($statusFilter)) {
    //             // Pastikan setiap status valid sebelum dimasukkan ke query
    //             $validStatuses = [];
    //             $allowedStatuses = [
    //                 'approved',
    //                 'processing',
    //                 'shipped',
    //                 'completed',
    //                 'rejected'
    //                 // Tambahkan semua status yang valid di sistem kamu
    //             ];

    //             foreach ($statusFilter as $status) {
    //                 $cleanStatus = strtolower(trim($status));
    //                 if (in_array($cleanStatus, $allowedStatuses)) {
    //                     $validStatuses[] = $cleanStatus;
    //                 }
    //             }

    //             if (!empty($validStatuses)) {
    //                 $builder->whereIn('orders.status', $validStatuses);
    //             }
    //         } else {
    //             // Jika $statusFilter hanya string (dari URL: ?status=s1)
    //             // Lakukan sanitasi dan validasi juga
    //             $cleanStatus = strtolower(trim($statusFilter));
    //             $allowedStatuses = [
    //                 'approved',
    //                 'processing',
    //                 'shipped',
    //                 'completed',
    //                 'rejected'
    //             ];
    //             if (in_array($cleanStatus, $allowedStatuses)) {
    //                 $builder->where('orders.status', $cleanStatus);
    //             }
    //         }
    //     }
    //     // --- Akhir Perubahan Penting ---

    //     // Tambahkan kondisi filter tanggal jika ada input start_date dan end_date
    //     if ($startDate) {
    //         $builder->where('orders.order_date >=', $startDate . ' 00:00:00');
    //     }
    //     if ($endDate) {
    //         $builder->where('orders.order_date <=', $endDate . ' 23:59:59');
    //     }

    //     $builder->orderBy('orders.order_date', 'DESC');

    //     return $builder->get()->getResultArray();
    // }
    public function getHistoryOrders(
        $distributorId,
        $searchOrderId = null,
        $searchAgentName = null,
        $statusFilter = null,
        $startDate = null,
        $endDate = null
    ) {
        // Mulai membangun query pada objek model itu sendiri
        $builder = $this->select('orders.*, users.username AS agent_username') // Ambil juga username agen
            ->join('users', 'users.id = orders.agen_id') // Gabungkan dengan tabel users
            ->where('orders.distributor_id', $distributorId)
            ->whereIn('orders.status', ['completed', 'rejected']); // Exclude 'pending'

        // Tambahkan kondisi pencarian/filter
        if (!empty($searchOrderId)) {
            $builder->like('orders.id', $searchOrderId); // Cari berdasarkan Order ID
        }
        if (!empty($searchAgentName)) {
            $builder->like('users.username', $searchAgentName); // Cari berdasarkan nama agen
        }
        if (!empty($statusFilter)) {
            // Pastikan ini menangani multiple choice filter jika diperlukan,
            // saat ini hanya untuk single value
            $builder->where('orders.status', $statusFilter);
        }
        if (!empty($startDate)) {
            $builder->where('orders.order_date >=', $startDate . ' 00:00:00');
        }
        if (!empty($endDate)) {
            $builder->where('orders.order_date <=', $endDate . ' 23:59:59');
        }

        $builder->orderBy('orders.order_date', 'DESC');

        // *** PERUBAHAN PENTING DI SINI ***
        // Kembalikan objek Query Builder, bukan hasil findAll()
        return $builder;
    }
    public function getOrderItemsWithProducts(int $orderId): array
    {
        // Pastikan nama tabel di JOIN sesuai dengan struktur database Anda:
        // 'order_items' dan 'products'
        return $this->db->table('order_items oi')
            ->select('oi.id as order_item_id, oi.product_id, oi.quantity, p.base_price, p.product_name')
            ->join('products p', 'p.id = oi.product_id')
            ->where('oi.order_id', $orderId)
            ->get()
            ->getResultArray(); // Mengembalikan hasil sebagai array of arrays
    }

    public function getOrderItemsByOrderIds(array $orderIds)
    {
        if (empty($orderIds)) {
            return [];
        }
        return $this->db->table('order_items')
            ->whereIn('order_id', $orderIds)
            ->get()
            ->getResultArray();
    }

    public function getOrderItemsByOrderId(int $orderId)
    {
        // Asumsi ada tabel order_items
        return $this->db->table('order_items')
            ->where('order_id', $orderId)
            ->get()
            ->getResultArray();
    }

    public function getProductsByIds(array $productIds)
    {
        // Asumsi ada tabel products
        return $this->db->table('products')
            ->whereIn('id', $productIds)
            ->get()
            ->getResultArray();
    }
}
