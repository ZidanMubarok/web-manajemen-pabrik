<?php

namespace App\Controllers;

use App\Models\DistributorInvoiceModel;
use App\Models\OrderModel;
use App\Models\OrderItemModel;
use App\Models\UserModel; // Untuk mengambil nama agen

class DistributorInvoiceController extends BaseController
{
    protected $distributorInvoiceModel;
    protected $orderModel;
    protected $orderItemModel;
    protected $userModel;

    public function __construct()
    {
        $this->distributorInvoiceModel = new DistributorInvoiceModel();
        $this->orderModel = new OrderModel();
        $this->orderItemModel = new OrderItemModel();
        $this->userModel = new UserModel();
        helper(['form', 'url']);
    }

    /**
     * Menampilkan daftar tagihan yang ditujukan kepada distributor yang sedang login.
     */
    // public function index()
    // {
    //     // Pastikan hanya peran 'distributor' yang bisa mengakses
    //     // Sesuaikan dengan sistem otentikasi/otorisasi Anda
    //     if (session()->get('role') !== 'distributor') {
    //         return redirect()->to(base_url('/login'))->with('error', 'Anda tidak memiliki akses ke halaman ini.');
    //     }

    //     $distributorId = session()->get('id'); // Ambil ID distributor yang sedang login

    //     if (!$distributorId) {
    //         return redirect()->to(base_url('/login'))->with('error', 'Sesi pengguna tidak valid.');
    //     }

    //     $statusFilter = $this->request->getGet('status') ?? '';
    //     $invoiceNumber = $this->request->getGet('invoice_number') ?? '';
    //     $startDate = $this->request->getGet('start_date') ?? '';
    //     $endDate = $this->request->getGet('end_date') ?? '';

    //     $query = $this->distributorInvoiceModel
    //         ->select('distributor_invoices.*, orders.agen_id, users.username as agen_username')
    //         ->join('orders', 'orders.id = distributor_invoices.order_id', 'left')
    //         ->join('users', 'users.id = orders.agen_id', 'left')
    //         ->where('orders.distributor_id', $distributorId); // FILTER PENTING: hanya tagihan untuk distributor ini

    //     if (!empty($statusFilter)) {
    //         $query->where('distributor_invoices.status', $statusFilter);
    //     }
    //     if (!empty($invoiceNumber)) {
    //         $query->like('distributor_invoices.invoice_number', $invoiceNumber);
    //     }
    //     if (!empty($startDate)) {
    //         $query->where('distributor_invoices.invoice_date >=', $startDate . ' 00:00:00');
    //     }
    //     if (!empty($endDate)) {
    //         $query->where('distributor_invoices.invoice_date <=', $endDate . ' 23:59:59');
    //     }

    //     $invoices = $query->orderBy('distributor_invoices.invoice_date', 'DESC')->findAll();
    //     $data = [
    //         'title' => 'Tagihan Saya',
    //         'invoices' => $invoices,
    //         'statusFilter' => $statusFilter,
    //         'invoiceNumber' => $invoiceNumber,
    //         'startDate' => $startDate,
    //         'endDate' => $endDate,
    //     ];

    //     return view('distributor/invoicesme/index', $data);
    // }
    public function index()
    {
        // Pastikan hanya peran 'distributor' yang bisa mengakses
        if (session()->get('role') !== 'distributor') {
            return redirect()->to(base_url('/login'))->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        $distributorId = session()->get('id'); // Ambil ID distributor yang sedang login

        if (!$distributorId) {
            return redirect()->to(base_url('/login'))->with('error', 'Sesi pengguna tidak valid.');
        }

        // --- AMBIL DATA FILTER ---
        $statusFilter = $this->request->getGet('status') ?? '';
        $invoiceNumber = $this->request->getGet('invoice_number') ?? '';
        $startDate = $this->request->getGet('start_date') ?? '';
        $endDate = $this->request->getGet('end_date') ?? '';

        // --- BANGUN QUERY DASAR ---
        $query = $this->distributorInvoiceModel
            ->select('distributor_invoices.*, orders.agen_id, users.username as agen_username')
            ->join('orders', 'orders.id = distributor_invoices.order_id', 'left')
            ->join('users', 'users.id = orders.agen_id', 'left')
            ->where('orders.distributor_id', $distributorId); // FILTER PENTING

        // --- TERAPKAN FILTER JIKA ADA ---
        if (!empty($statusFilter)) {
            $query->where('distributor_invoices.status', $statusFilter);
        }
        if (!empty($invoiceNumber)) {
            $query->like('distributor_invoices.invoice_number', $invoiceNumber);
        }
        if (!empty($startDate)) {
            $query->where('distributor_invoices.invoice_date >=', $startDate . ' 00:00:00');
        }
        if (!empty($endDate)) {
            $query->where('distributor_invoices.invoice_date <=', $endDate . ' 23:59:59');
        }

        // --- MODIFIKASI: Gunakan paginate() bukan findAll() ---
        $perPage = 10; // Tentukan jumlah item per halaman
        $invoices = $query->orderBy('distributor_invoices.invoice_date', 'DESC')->paginate($perPage, 'invoices');

        // --- BARU: Siapkan data untuk informasi "Menampilkan X dari Y" ---
        $pager = $this->distributorInvoiceModel->pager;
        $currentPage = $pager->getCurrentPage('invoices');
        $totalRows = $pager->getTotal('invoices');

        $startRow = ($currentPage - 1) * $perPage + 1;
        $endRow = min($currentPage * $perPage, $totalRows);

        // Jika tidak ada data, atur startRow menjadi 0
        if ($totalRows === 0) {
            $startRow = 0;
        }

        $data = [
            'title' => 'Tagihan Saya',
            'invoices' => $invoices,
            'pager' => $pager, // Kirim pager ke view
            'startRow' => $startRow, // Kirim info baris awal
            'endRow' => $endRow,     // Kirim info baris akhir
            'totalRows' => $totalRows, // Kirim info total data
            'statusFilter' => $statusFilter,
            'invoiceNumber' => $invoiceNumber,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ];

        return view('distributor/invoicesme/index', $data);
    }
    
    /**
     * Menampilkan detail tagihan distributor.
     */
    // public function detail($invoiceId)
    // {
    //     // Pastikan hanya peran 'distributor' yang bisa mengakses
    //     if (session()->get('role') !== 'distributor') {
    //         return redirect()->to(base_url('/login'))->with('error', 'Anda tidak memiliki akses ke halaman ini.');
    //     }

    //     $distributorId = session()->get('id');

    //     $invoice = $this->distributorInvoiceModel
    //         ->select('distributor_invoices.*, orders.agen_id, orders.id as order_ref_id, users.username as agen_username')
    //         ->join('orders', 'orders.id = distributor_invoices.order_id', 'left')
    //         ->join('users', 'users.id = orders.agen_id', 'left')
    //         ->where('distributor_invoices.id', $invoiceId)
    //         ->where('orders.distributor_id', $distributorId) // Pastikan tagihan ini milik distributor yang login
    //         ->first();

    //     if (!$invoice) {
    //         return redirect()->to(base_url('distributor/invoices'))->with('error', 'Tagihan tidak ditemukan atau Anda tidak memiliki akses.');
    //     }
    //     $orderItems = [];
    //     $productNames = [];

    //     if (!empty($invoice['order_id'])) {
    //         $orderItems = $this->orderModel->getOrderItemsByOrderId($invoice['order_id']);

    //         // Mengambil nama produk untuk setiap item
    //         $productIds = array_column($orderItems, 'product_id');
    //         if (!empty($productIds)) {
    //             $products = $this->orderModel->getProductsByIds($productIds); // Asumsi ini ada di OrderModel atau buat ProductModel
    //             foreach ($products as $product) {
    //                 $productNames[$product['id']] = $product['product_name'];
    //             }
    //         }
    //     }


    //     $data = [
    //         'title' => 'Detail Tagihan #' . esc($invoice['invoice_number']),
    //         'invoice' => $invoice,
    //         'orderItems' => $orderItems,
    //         'productNames' => $productNames,
    //     ];

    //     return view('distributor/invoicesme/detail', $data);
    // }

    public function detail($invoiceId)
    {
        // Pastikan hanya peran 'distributor' yang bisa mengakses
        if (session()->get('role') !== 'distributor') {
            return redirect()->to(base_url('/login'))->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        $distributorId = session()->get('id');

        $invoice = $this->distributorInvoiceModel
            ->select('distributor_invoices.*, orders.agen_id, orders.id as order_ref_id, users.username as agen_username')
            ->join('orders', 'orders.id = distributor_invoices.order_id', 'left')
            ->join('users', 'users.id = orders.agen_id', 'left')
            ->where('distributor_invoices.id', $invoiceId)
            ->where('orders.distributor_id', $distributorId) // Pastikan tagihan ini milik distributor yang login
            ->first();

        if (!$invoice) {
            return redirect()->to(base_url('distributor/invoices'))->with('error', 'Tagihan tidak ditemukan atau Anda tidak memiliki akses.');
        }

        $orderItems = [];
        if (!empty($invoice['order_id'])) {
            // Mengambil item pesanan beserta harga dasar dari tabel produk
            $orderItems = $this->orderItemModel // Ganti dengan model order_items Anda
                ->select('order_items.*, products.product_name, products.base_price')
                ->join('products', 'products.id = order_items.product_id')
                ->where('order_items.order_id', $invoice['order_id'])
                ->findAll();
        }

        $data = [
            'title'      => 'Detail Tagihan #' . esc($invoice['invoice_number']),
            'invoice'    => $invoice,
            'orderItems' => $orderItems,
        ];

        return view('distributor/invoicesme/detail', $data);
    }
}
