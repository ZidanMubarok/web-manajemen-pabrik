<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table          = 'users';
    protected $primaryKey     = 'id';

    protected $useAutoIncrement = true;
    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    // Hanya kolom yang diizinkan untuk diisi secara massal
    protected $allowedFields = ['username', 'password', 'remember_token', 'remember_selector', 'email', 'role', 'no_telpon', 'alamat', 'parent_id'];

    // Dates (tetap dipertahankan untuk created_at dan updated_at otomatis)
    protected $useTimestamps = true;
    protected $dateFormat     = 'datetime';
    protected $createdField   = 'created_at';
    protected $updatedField   = 'updated_at';

    // *** TIDAK ADA LAGI validationRules, validationMessages, beforeInsert, beforeUpdate ***
    // *** TIDAK ADA LAGI method hashPassword() ***

    // Metode kustom untuk mendapatkan user berdasarkan role (tetap dipertahankan)
    public function getUsersByRole(string $role, int $limit = 0, int $offset = 0)
    {
        $builder = $this->where('role', $role)->orderBy('created_at', 'DESC');
        if ($limit > 0) {
            $builder->limit($limit, $offset);
        }
        return $builder->findAll();
    }

    public function countUsersByRole(string $role)
    {
        return $this->where('role', $role)->countAllResults();
    }
    // Get agents associated with a specific distributor
    public function getAgentsByDistributorId(int $distributorId)
    {
        return $this->where('role', 'agen')
            ->where('parent_id', $distributorId)
            ->findAll();
    }

    // Get a user by ID
    public function getUserById(int $id)
    {
        return $this->find($id);
    }


    // public function getDistributorPerformance($sortColumn = 'username', $sortOrder = 'asc')
    // {
    //     // Daftar kolom yang aman untuk di-sort
    //     $validSortColumns = [
    //         'username',
    //         'total_orders',
    //         'total_quantity',
    //         'active_orders',
    //         'paid_invoices',
    //         'unpaid_invoices'
    //     ];

    //     if (!in_array(strtolower($sortColumn), $validSortColumns)) {
    //         $sortColumn = 'username'; // Fallback ke default
    //     }

    //     // PENTING: Panggil metode select(), join(), dll. langsung pada $this.
    //     // Ini akan memodifikasi state query builder internal milik model.
    //     $this->select([
    //         'users.id',
    //         'users.username',
    //         'COUNT(DISTINCT orders.id) as total_orders',
    //         'COALESCE(SUM(order_items.quantity), 0) as total_quantity',
    //         "COUNT(DISTINCT CASE WHEN orders.status IN ('approved', 'processing', 'shipped') THEN orders.id END) as active_orders",
    //         "COUNT(DISTINCT CASE WHEN distributor_invoices.status = 'paid' THEN distributor_invoices.id END) as paid_invoices",
    //         "COUNT(DISTINCT CASE WHEN distributor_invoices.status IN ('unpaid', 'partially_paid') THEN distributor_invoices.id END) as unpaid_invoices",
    //     ])
    //         ->join('orders', 'users.id = orders.distributor_id', 'left')
    //         ->join('order_items', 'orders.id = order_items.order_id', 'left')
    //         ->join('distributor_invoices', 'orders.id = distributor_invoices.order_id', 'left')
    //         ->where('users.role', 'distributor')
    //         ->groupBy('users.id, users.username')
    //         ->orderBy($sortColumn, $sortOrder);

    //     // KEMBALIKAN INSTANCE MODEL ITU SENDIRI, bukan builder-nya.
    //     return $this;
    // }
    public function getDistributorPerformance($sortColumn = 'username', $sortOrder = 'asc', $startDate = null, $endDate = null)
    {
        $validSortColumns = ['username', 'total_orders', 'total_quantity', 'active_orders', 'paid_invoices', 'unpaid_invoices'];
        if (!in_array(strtolower($sortColumn), $validSortColumns)) {
            $sortColumn = 'username';
        }

        $this->select([
            'users.id',
            'users.username',
            'COUNT(DISTINCT orders.id) as total_orders',
            'COALESCE(SUM(order_items.quantity), 0) as total_quantity',
            "COUNT(DISTINCT CASE WHEN orders.status IN ('approved', 'processing', 'shipped') THEN orders.id END) as active_orders",
            "COUNT(DISTINCT CASE WHEN distributor_invoices.status = 'paid' THEN distributor_invoices.id END) as paid_invoices",
            "COUNT(DISTINCT CASE WHEN distributor_invoices.status IN ('unpaid', 'partially_paid') THEN distributor_invoices.id END) as unpaid_invoices",
        ])
            ->join('orders', 'users.id = orders.distributor_id', 'left')
            ->join('order_items', 'orders.id = order_items.order_id', 'left')
            ->join('distributor_invoices', 'orders.id = distributor_invoices.order_id', 'left')
            ->where('users.role', 'distributor');

        // ==== LOGIKA FILTER TANGGAL DITAMBAHKAN DI SINI ====
        if (!empty($startDate)) {
            $this->where('orders.order_date >=', $startDate);
        }
        if (!empty($endDate)) {
            // Kita tambahkan waktu 23:59:59 untuk memastikan seluruh hari pada tanggal akhir terhitung
            $this->where('orders.order_date <=', $endDate . ' 23:59:59');
        }
        // ====================================================

        $this->groupBy('users.id, users.username')->orderBy($sortColumn, $sortOrder);

        return $this;
    }
}
