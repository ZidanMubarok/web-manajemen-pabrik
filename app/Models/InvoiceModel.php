<?php

namespace App\Models;

use CodeIgniter\Model;

class InvoiceModel extends Model
{
    protected $table            = 'invoices'; // Sesuaikan dengan nama tabel di DB Anda
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false; // Biasanya invoices tidak di-soft delete, tapi bisa disesuaikan

    protected $allowedFields = [
        'order_id',
        'invoice_date',
        'amount_total',
        'status', // 'unpaid', 'paid', 'cancelled'
        'payment_date',
    ];

    protected bool $allowEmptyInserts = false;
    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    // protected $deletedField  = 'deleted_at'; // Tidak digunakan jika useSoftDeletes = false

    // Validation
    protected $validationRules      = [
        'order_id'     => 'required|integer',
        'invoice_date' => 'required|valid_date',
        'amount_total' => 'required|decimal',
        'status'       => 'required|in_list[unpaid,paid,cancelled]',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $afterDelete    = [];

    // Get invoices for specific order IDs
    public function getInvoicesByOrderIds(array $orderIds)
    {
        return $this->whereIn('order_id', $orderIds)
            ->findAll();
    }
    // Metode untuk memperbarui status
    public function updateStatus(int $invoiceId, string $status, ?string $paymentDate = null)
    {
        $data = ['status' => $status];
        if ($paymentDate) {
            $data['payment_date'] = $paymentDate;
        }
        return $this->update($invoiceId, $data);
    }

    /**
     * Mengambil daftar invoice agen yang terkait dengan distributor tertentu.
     *
     * @param int $distributorId ID dari distributor yang sedang login.
     * @return array Daftar invoice dengan informasi agen dan order.
     */
    // public function getAgentInvoices(int $distributorId)
    // {
    //     // Pastikan Anda memiliki instance model lain jika mereka belum di-init
    //     $userModel = new UserModel();
    //     $orderModel = new OrderModel();

    //     // 1. Dapatkan semua agen yang terkait dengan distributor ini
    //     $agents = $userModel->getAgentsByDistributorId($distributorId);
    //     $agenIds = array_column($agents, 'id');

    //     // Jika tidak ada agen, kembalikan array kosong
    //     if (empty($agenIds)) {
    //         return [];
    //     }

    //     // 2. Dapatkan semua pesanan yang dibuat oleh agen-agen tersebut
    //     $orders = $orderModel->getOrdersByAgenIds($agenIds);
    //     $orderIds = array_column($orders, 'id');

    //     // Jika tidak ada pesanan, kembalikan array kosong
    //     if (empty($orderIds)) {
    //         return [];
    //     }

    //     // 3. Dapatkan semua tagihan yang terkait dengan pesanan tersebut
    //     $invoices = $this->getInvoicesByOrderIds($orderIds); // Menggunakan metode yang sudah ada

    //     // Tambahkan informasi agen dan pesanan ke setiap invoice untuk tampilan
    //     // Ini bisa juga di-join langsung di query jika diinginkan untuk performa
    //     // Tapi untuk saat ini, kita gabungkan secara manual
    //     foreach ($invoices as &$invoice) {
    //         $order = array_values(array_filter($orders, fn($o) => $o['id'] == $invoice['order_id']));
    //         if (!empty($order)) {
    //             $invoice['order'] = $order[0];
    //             $agent = array_values(array_filter($agents, fn($a) => $a['id'] == $order[0]['agen_id']));
    //             if (!empty($agent)) {
    //                 $invoice['agent'] = $agent[0];
    //             }
    //         }
    //     }

    //     return $invoices;
    // }
    // Modifikasi fungsi getAgentInvoices untuk mendukung filter
    // public function getAgentInvoices(
    //     int $distributorId,
    //     ?string $searchInvoiceId = null,
    //     ?string $searchOrderId = null,
    //     ?string $searchAgentName = null,
    //     ?string $statusFilter = null,
    //     ?string $startDate = null,
    //     ?string $endDate = null
    // ) {
    //     $builder = $this->db->table('invoices');
    //     $builder->select('invoices.*, orders.total_amount as order_total_amount, orders.order_date, users.username as agent_username, users.email as agent_email');
    //     $builder->join('orders', 'orders.id = invoices.order_id');
    //     $builder->join('users', 'users.id = orders.agen_id'); // Join ke tabel users untuk mendapatkan nama agen
    //     $builder->where('orders.distributor_id', $distributorId);

    //     // Tambahkan kondisi pencarian berdasarkan ID Tagihan
    //     if ($searchInvoiceId) {
    //         $builder->like('invoices.id', $searchInvoiceId);
    //     }

    //     // Tambahkan kondisi pencarian berdasarkan ID Order
    //     if ($searchOrderId) {
    //         $builder->like('orders.id', $searchOrderId);
    //     }

    //     // Tambahkan kondisi pencarian berdasarkan Nama Agen
    //     if ($searchAgentName) {
    //         $builder->like('users.username', $searchAgentName);
    //     }

    //     // Tambahkan kondisi filter status jika ada input status
    //     if ($statusFilter && $statusFilter !== '') {
    //         $builder->where('invoices.status', $statusFilter);
    //     }

    //     // Tambahkan kondisi filter tanggal jika ada input start_date dan end_date
    //     if ($startDate) {
    //         $builder->where('invoices.invoice_date >=', $startDate . ' 00:00:00');
    //     }
    //     if ($endDate) {
    //         $builder->where('invoices.invoice_date <=', $endDate . ' 23:59:59');
    //     }

    //     $builder->orderBy('invoices.invoice_date', 'DESC');

    //     return $builder->get()->getResultArray();
    // }
    public function getInvoiceWithAgentAndOrderData()
    {
        return $this->select('invoices.*, orders.agen_id, orders.distributor_id, orders.order_date, orders.total_amount AS order_total_amount, users.username AS agent_username, users.id AS agen_user_id')
            ->join('orders', 'orders.id = invoices.order_id')
            ->join('users', 'users.id = orders.agen_id');
    }
}
