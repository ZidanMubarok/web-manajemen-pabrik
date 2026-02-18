<?php

namespace App\Controllers;

use DateTime;
use DatePeriod;
use DateInterval;
use App\Models\UserModel;
use App\Models\OrderModel;
use App\Models\InvoiceModel;
use App\Models\ProductModel;
use App\Models\OrderItemModel;
use App\Models\UserProductModel;
use App\Models\NotificationModel;
use App\Controllers\BaseController;
use App\Models\DistributorInvoiceModel;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
// Untuk fitur pencetakan excel 
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use App\Models\ShipmentModel; // Tambahkan ini

class PabrikController extends BaseController
{
    protected $userModel;
    protected $productModel;
    protected $userProductModel;
    protected $orderModel;
    protected $orderItemModel;
    protected $invoiceModel;
    protected $distributorInvoiceModel;
    protected $notificationModel;
    protected $shipmentModel; // Tambahkan ini
    protected $db;

    public function __construct()
    {

        $this->userProductModel = new UserProductModel(); // Inisialisasi UserProductModel

        $this->userModel = new UserModel();
        $this->productModel = new ProductModel();
        $this->orderModel = new OrderModel();
        $this->orderItemModel = new OrderItemModel();
        $this->invoiceModel = new InvoiceModel();
        $this->distributorInvoiceModel = new DistributorInvoiceModel();
        $this->notificationModel = new NotificationModel();
        $this->shipmentModel = new ShipmentModel(); // Instansiasi model shipment
        $this->db = \Config\Database::connect();
        helper(['form', 'url']);
    }

    // public function index()
    // {
    //     // Pastikan hanya pabrik yang bisa mengakses
    //     if (session()->get('role') !== 'pabrik') {
    //         return redirect()->back()->with('error', 'Anda tidak diberikan akses untuk masuk ke halaman tersebut.');
    //     }

    //     // --- Data untuk Summary Cards ---
    //     $currentMonth = date('Y-m');
    //     $currentMonthStart = date('Y-m-01 00:00:00'); // Awal bulan ini
    //     $nextMonthStart = date('Y-m-01 00:00:00', strtotime('+1 month')); // Awal bulan depan
    //     $currentMonthEnd = date('Y-m-t 23:59:59'); // Akhir bulan ini (tanggal terakhir bulan)


    //     // 1. Order Masuk (Pending)
    //     $orderMasuk = $this->orderModel->where('status', 'approved')->countAllResults();

    //     // 2. Order Di Proses (Processing)
    //     $orderDiproses = $this->orderModel->where('status', 'processing')->countAllResults();

    //     // 3. Order Di Kirim (Shipped)
    //     $orderDikirim = $this->orderModel->where('status', 'shipped')->countAllResults();

    //     // 4. Order Selesai Bulan Ini
    //     $orderSelesaiBulanIni = $this->orderModel
    //         ->where('status', 'completed')
    //         ->where('DATE_FORMAT(delivery_date, "%Y-%m") =', $currentMonth)
    //         ->countAllResults();

    //     // 5. Order Gagal Bulan Ini
    //     $orderGagalBulanIni = $this->orderModel
    //         ->where('status', 'rejected')
    //         ->where('DATE_FORMAT(updated_at, "%Y-%m") =', $currentMonth)
    //         ->countAllResults();

    //     // 6. Tagihan Distributor Belum Lunas
    //     $tagihanBelumLunasResult = $this->distributorInvoiceModel
    //         ->selectSum('total_amount', 'total_unpaid')
    //         ->whereIn('status', ['unpaid', 'partially_paid'])
    //         ->first();
    //     $tagihanBelumLunas = $tagihanBelumLunasResult['total_unpaid'] ?? 0;

    //     // 7. Pendapatan Bulan Ini (Dari tagihan distributor yang lunas)
    //     $pendapatanBulanIniResult = $this->distributorInvoiceModel
    //         ->selectSum('total_amount', 'total_paid_month')
    //         ->where('status', 'paid')
    //         ->where('DATE_FORMAT(invoice_date, "%Y-%m") =', $currentMonth)
    //         ->first();
    //     $pendapatanBulanIni = $pendapatanBulanIniResult['total_paid_month'] ?? 0;

    //     // 8. Manajemen Pengiriman (Ini hanya link, tidak perlu query)

    //     // 9. Data Distributor (Jumlah Distributor)
    //     $totalDistributors = $this->userModel->countUsersByRole('distributor');

    //     // --- Data untuk Tabel Distributor Aktif Terbaru ---
    //     $distributorAktifTerbaru = $this->userModel
    //         ->where('role', 'distributor')
    //         ->orderBy('created_at', 'DESC')
    //         ->limit(5)
    //         ->findAll();

    //     // --- Data untuk Grafik Pendapatan Harian (30 Hari Terakhir) ---
    //     $dailySalesData = $this->distributorInvoiceModel
    //         ->where('payment_date >=', date('Y-m-d', strtotime('-30 days')))
    //         ->where('status', 'paid')
    //         ->select("DATE(payment_date) as sales_day, SUM(total_amount) as total_revenue")
    //         ->groupBy('sales_day')
    //         ->orderBy('sales_day', 'ASC')
    //         ->findAll();

    //     $chartLabels = [];
    //     $chartData = [];
    //     $period = new DatePeriod(
    //         new DateTime('-30 days'),
    //         new DateInterval('P1D'),
    //         new DateTime('+1 day')
    //     );

    //     $dailyRevenueMap = [];
    //     foreach ($dailySalesData as $data) {
    //         $dailyRevenueMap[$data['sales_day']] = $data['total_revenue'];
    //     }

    //     foreach ($period as $date) {
    //         $currentDate = $date->format('Y-m-d');
    //         $chartLabels[] = $date->format('d M');
    //         $chartData[] = (float) ($dailyRevenueMap[$currentDate] ?? 0);
    //     }

    //     // --- Data untuk dikirim ke View ---
    //     $data = [
    //         'title'                     => 'Dashboard Pabrik',
    //         'user_data'                 => session()->get(),
    //         'orderMasuk'                => $orderMasuk,
    //         'orderDiproses'             => $orderDiproses,
    //         'orderDikirim'              => $orderDikirim,
    //         'orderSelesaiBulanIni'      => $orderSelesaiBulanIni,
    //         'orderGagalBulanIni'        => $orderGagalBulanIni,
    //         'tagihanBelumLunas'         => $tagihanBelumLunas,
    //         'pendapatanBulanIni'        => $pendapatanBulanIni,
    //         'totalDistributors'         => $totalDistributors,
    //         'distributorAktifTerbaru'   => $distributorAktifTerbaru,
    //         'thisMonthStartDate'         => date('Y-m-d', strtotime($currentMonthStart)), // Format YYYY-MM-DD
    //         'thisMonthEndDate'           => date('Y-m-d', strtotime($currentMonthEnd)),
    //         'chartLabels'               => json_encode($chartLabels),
    //         'chartData'                 => json_encode($chartData),
    //     ];

    //     return view('pabrik/index', $data);
    // }
    public function index()
    {
        // Pastikan hanya pabrik yang bisa mengakses
        if (session()->get('role') !== 'pabrik') {
            return redirect()->back()->with('error', 'Anda tidak diberikan akses untuk masuk ke halaman tersebut.');
        }

        // --- Data untuk Summary Cards ---
        $currentMonth = date('Y-m');
        $currentMonthStart = date('Y-m-01 00:00:00'); // Awal bulan ini
        $nextMonthStart = date('Y-m-01 00:00:00', strtotime('+1 month')); // Awal bulan depan
        $currentMonthEnd = date('Y-m-t 23:59:59'); // Akhir bulan ini (tanggal terakhir bulan)


        // 1. Order Masuk (Pending)
        $orderMasuk = $this->orderModel->where('status', 'approved')->countAllResults();

        // 2. Order Di Proses (Processing)
        $orderDiproses = $this->orderModel->where('status', 'processing')->countAllResults();

        // 3. Order Di Kirim (Shipped)
        $orderDikirim = $this->orderModel->where('status', 'shipped')->countAllResults();

        // 4. Order Selesai Bulan Ini
        $orderSelesaiBulanIni = $this->orderModel
            ->where('status', 'completed')
            ->where('DATE_FORMAT(delivery_date, "%Y-%m") =', $currentMonth)
            ->countAllResults();

        // 5. Order Gagal Bulan Ini
        $orderGagalBulanIni = $this->orderModel
            ->where('status', 'rejected')
            ->where('DATE_FORMAT(updated_at, "%Y-%m") =', $currentMonth)
            ->countAllResults();

        // 6. Tagihan Distributor Belum Lunas
        $tagihanBelumLunasResult = $this->distributorInvoiceModel
            ->selectSum('total_amount', 'total_unpaid')
            ->whereIn('status', ['unpaid', 'partially_paid'])
            ->first();
        $tagihanBelumLunas = $tagihanBelumLunasResult['total_unpaid'] ?? 0;

        // 7. Pendapatan Bulan Ini (Dari tagihan distributor yang lunas)
        $pendapatanBulanIniResult = $this->distributorInvoiceModel
            ->selectSum('total_amount', 'total_paid_month')
            ->where('status', 'paid')
            ->where('DATE_FORMAT(invoice_date, "%Y-%m") =', $currentMonth)
            ->first();
        $pendapatanBulanIni = $pendapatanBulanIniResult['total_paid_month'] ?? 0;

        // 8. Manajemen Pengiriman (Ini hanya link, tidak perlu query)

        // 9. Data Distributor (Jumlah Distributor)
        $totalDistributors = $this->userModel->countUsersByRole('distributor');

        // --- Data untuk Tabel Distributor Aktif Terbaru (6) ---
        $distributorAktifTerbaru = $this->userModel
            ->where('role', 'distributor')
            ->orderBy('created_at', 'DESC')
            ->limit(6)
            ->findAll();

        // --- Data untuk Grafik Pendapatan Harian (30 Hari Terakhir) ---
        $dailySalesData = $this->distributorInvoiceModel
            ->where('payment_date >=', date('Y-m-d', strtotime('-30 days')))
            ->where('status', 'paid')
            ->select("DATE(payment_date) as sales_day, SUM(total_amount) as total_revenue")
            ->groupBy('sales_day')
            ->orderBy('sales_day', 'ASC')
            ->findAll();

        $chartLabels = [];
        $chartData = [];
        $period = new DatePeriod(
            new DateTime('-30 days'),
            new DateInterval('P1D'),
            new DateTime('+1 day')
        );

        $dailyRevenueMap = [];
        foreach ($dailySalesData as $data) {
            $dailyRevenueMap[$data['sales_day']] = $data['total_revenue'];
        }

        foreach ($period as $date) {
            $currentDate = $date->format('Y-m-d');
            $chartLabels[] = $date->format('d M');
            $chartData[] = (float) ($dailyRevenueMap[$currentDate] ?? 0);
        }

        // --- Data untuk Grafik Produk Terlaris (Doughnut Chart) ---
        // Mengambil data dari model order_items
        $topProductsData = $this->orderItemModel
            ->select('order_items.product_name, SUM(order_items.quantity) as total_sold')
            ->join('orders', 'orders.id = order_items.order_id')
            ->where('orders.status', 'completed') // Hanya dari order yang sudah selesai
            ->groupBy('order_items.product_name')
            ->orderBy('total_sold', 'DESC')
            ->limit(5) // Mengambil 5 produk teratas
            ->findAll();


        $topProductLabels = [];
        $topProductQuantities = [];
        foreach ($topProductsData as $product) {
            $topProductLabels[] = $product['product_name'];
            $topProductQuantities[] = (int)$product['total_sold'];
        }

        // --- Data Grafik Status Order Bulan Ini (Pie Chart) ---
        $orderStatusThisMonth = $this->orderModel
            ->select('status, COUNT(id) as count')
            ->where('created_at >=', $currentMonthStart)
            ->where('created_at <=', $currentMonthEnd)
            ->groupBy('status')
            ->findAll();

        $orderStatusLabels = [];
        $orderStatusCounts = [];
        // Peta untuk label yang lebih ramah pengguna
        $statusMap = [
            'pending'    => 'Pending',
            'approved'   => 'Disetujui',
            'processing' => 'Diproses',
            'shipped'    => 'Dikirim',
            'completed'  => 'Selesai',
            'rejected'   => 'Ditolak',
        ];

        foreach ($orderStatusThisMonth as $status) {
            // Gunakan label dari peta, atau gunakan status asli jika tidak ditemukan
            $orderStatusLabels[] = $statusMap[$status['status']] ?? ucfirst($status['status']);
            $orderStatusCounts[] = (int)$status['count'];
        }

        // --- Data untuk dikirim ke View ---
        $data = [
            'title'                     => 'Dashboard Pabrik',
            'user_data'                 => session()->get(),
            'orderMasuk'                => $orderMasuk,
            'orderDiproses'             => $orderDiproses,
            'orderDikirim'              => $orderDikirim,
            'orderSelesaiBulanIni'      => $orderSelesaiBulanIni,
            'orderGagalBulanIni'        => $orderGagalBulanIni,
            'tagihanBelumLunas'         => $tagihanBelumLunas,
            'pendapatanBulanIni'        => $pendapatanBulanIni,
            'totalDistributors'         => $totalDistributors,
            'distributorAktifTerbaru'   => $distributorAktifTerbaru,
            'thisMonthStartDate'         => date('Y-m-d', strtotime($currentMonthStart)),
            'thisMonthEndDate'           => date('Y-m-d', strtotime($currentMonthEnd)),
            'chartLabels'               => json_encode($chartLabels),
            'chartData'                 => json_encode($chartData),
            'topProductLabels'          => json_encode($topProductLabels),
            'topProductData'            => json_encode($topProductQuantities),
            'orderStatusLabels'         => json_encode($orderStatusLabels),
            'orderStatusData'           => json_encode($orderStatusCounts),
        ];

        return view('pabrik/index', $data);
    }

    public function products()
    {
        if (session()->get('role') !== 'pabrik') {
            return redirect()->back()->with('error', 'Anda tidak di berikan akses untuk masuk ke halaman tersebut');
        }

        $data = [
            'title'   => 'Manajemen Produk',
            'products' => $this->productModel->findAll(),
        ];
        return view('pabrik/product_list', $data);
    }

    // Metode untuk menampilkan daftar order
    public function orders()
    {
        if (session()->get('role') !== 'pabrik') {
            return redirect()->back()->with('error', 'Anda tidak di berikan akses untuk masuk ke halaman tersebut');
        }

        $data = [
            'title'  => 'Manajemen Order',
            'orders' => $this->orderModel->getOrdersWithAgentAndDistributorUsers(), // Mengambil order dengan join ke users
        ];
        return view('pabrik/order_list', $data);
    }

    // public function daftarHarga()
    // {
    //     if (session()->get('role') !== 'pabrik') {
    //         return redirect()->to(base_url('/pabrik'))->with('error', 'Anda tidak memiliki akses ke halaman ini.');
    //     }

    //     $selectedDistributorId = $this->request->getVar('distributor_id'); // Ambil filter distributor_id dari URL

    //     // Ambil semua produk dari pabrik
    //     $allProducts = $this->productModel->findAll();

    //     // Ambil semua distributor
    //     $distributors = $this->userModel->where('role', 'distributor')->findAll();

    //     $distributorPrices = []; // Inisialisasi array untuk menyimpan harga kustom distributor
    //     if ($selectedDistributorId) {
    //         // Jika filter distributor_id dipilih, ambil harga kustom hanya untuk distributor itu
    //         $distributorPrices = $this->userProductModel
    //             ->where('user_id', $selectedDistributorId)
    //             ->findAll();
    //     } else {
    //         // Jika tidak ada filter, ambil semua harga kustom dari SEMUA distributor
    //         $distributorPrices = $this->userProductModel->findAll();
    //     }

    //     // Map harga kustom berdasarkan product_id dan user_id
    //     $distributorPricesMap = [];
    //     foreach ($distributorPrices as $price) {
    //         $distributorPricesMap[$price['user_id']][$price['product_id']] = $price['custom_price'];
    //     }

    //     $data = [
    //         'title'                 => 'Daftar Harga Produk & Harga Distributor',
    //         'allProducts'           => $allProducts,
    //         'distributors'          => $distributors, // Daftar semua distributor untuk filter
    //         'distributorPricesMap'  => $distributorPricesMap,
    //         'selectedDistributorId' => $selectedDistributorId,
    //     ];
    //     return view('pabrik/daftar_harga/index', $data);
    // }

    public function daftarHarga()
    {
        if (session()->get('role') !== 'pabrik') {
            return redirect()->to(base_url('/pabrik'))->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        $selectedDistributorId = $this->request->getVar('distributor_id'); // Ambil filter distributor_id dari URL
        $search = $this->request->getVar('search'); // Ambil parameter pencarian produk
        $perPage = 10; // Jumlah produk per halaman, Anda bisa menyesuaikannya

        // Ambil ID pabrik yang sedang login
        $pabrikId = session()->get('id');

        // Query dasar untuk produk dari pabrik yang login
        $productQuery = $this->productModel->where('user_id', $pabrikId);

        // Tambahkan filter pencarian produk jika ada
        if (!empty($search)) {
            $productQuery->groupStart()
                ->like('product_name', $search)
                ->orLike('description', $search)
                ->groupEnd();
        }

        // Dapatkan data produk dengan pagination
        $allProducts = $productQuery->paginate($perPage, 'default', $this->request->getVar('page'));
        $pager = $this->productModel->pager;

        // Ambil semua distributor (ini tidak dipaginasi karena dibutuhkan untuk dropdown filter dan modal detail)
        $distributors = $this->userModel->where('role', 'distributor')->findAll();

        $distributorPrices = []; // Inisialisasi array untuk menyimpan harga kustom distributor
        if ($selectedDistributorId) {
            // Jika filter distributor_id dipilih, ambil harga kustom hanya untuk distributor itu
            $distributorPrices = $this->userProductModel
                ->where('user_id', $selectedDistributorId)
                ->findAll();
        } else {
            // Jika tidak ada filter distributor, ambil semua harga kustom dari SEMUA distributor
            // Ini mungkin akan mengambil banyak data jika ada banyak distributor dan produk.
            // Namun, ini diperlukan untuk menampilkan "Harga Jual Distributor (Rata-rata/Rentang)"
            // dan untuk modal "Lihat Detail" yang menampilkan semua distributor.
            $distributorPrices = $this->userProductModel->findAll();
        }

        // Map harga kustom berdasarkan product_id dan user_id
        $distributorPricesMap = [];
        foreach ($distributorPrices as $price) {
            $distributorPricesMap[$price['user_id']][$price['product_id']] = $price['custom_price'];
        }

        $data = [
            'title'                 => 'Daftar Harga Produk & Harga Distributor',
            'allProducts'           => $allProducts, // Sekarang sudah dipaginasi
            'distributors'          => $distributors, // Daftar semua distributor untuk filter
            'distributorPricesMap'  => $distributorPricesMap,
            'selectedDistributorId' => $selectedDistributorId,
            'pager'                 => $pager, // Kirim objek pager ke view
            'search'                => $search, // Kirim nilai pencarian ke view
        ];
        return view('pabrik/daftar_harga/index', $data);
    }

    // public function monitoringDistributor()
    // {
    //     if (session()->get('role') !== 'pabrik') {
    //         return redirect()->to(base_url('/pabrik'))->with('error', 'Anda tidak memiliki akses ke halaman ini.');
    //     }

    //     $userModel = new UserModel();

    //     $sortColumn = $this->request->getGet('sort') ?? 'username';
    //     $sortOrder = $this->request->getGet('order') ?? 'asc';

    //     // Panggil metode yang baru diperbaiki, lalu chain dengan paginate()
    //     // Ini sekarang akan bekerja karena getDistributorPerformance() mengembalikan instance Model.
    //     $distributorPerformance = $userModel->getDistributorPerformance($sortColumn, $sortOrder)
    //         ->paginate(10, 'group1');

    //     $data = [
    //         'title'                  => 'Monitoring Distributor',
    //         'distributorPerformance' => $distributorPerformance,
    //         'pager'                  => $userModel->pager, // Ini sudah benar
    //         'sortColumn'             => $sortColumn,
    //         'sortOrder'              => $sortOrder,
    //         'currentPage'            => $userModel->pager->getCurrentPage('group1'),
    //         'perPage'                => $userModel->pager->getPerPage('group1'),
    //     ];

    //     return view('pabrik/monitoring_distributor/index', $data);
    // }

    public function monitoringDistributor()
    {
        if (session()->get('role') !== 'pabrik') {
            return redirect()->to(base_url('/pabrik'))->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        $userModel = new UserModel();

        // Ambil parameter dari URL (GET request)
        $sortColumn = $this->request->getGet('sort') ?? 'username';
        $sortOrder = $this->request->getGet('order') ?? 'asc';
        $startDate = $this->request->getGet('start_date') ?? ''; // BARU: Ambil tanggal mulai
        $endDate = $this->request->getGet('end_date') ?? '';     // BARU: Ambil tanggal akhir

        // Panggil method model dengan parameter filter tanggal
        $distributorPerformance = $userModel
            ->getDistributorPerformance($sortColumn, $sortOrder, $startDate, $endDate) // BARU: Teruskan tanggal ke model
            ->paginate(10, 'group1');

        $data = [
            'title'                  => 'Monitoring Distributor',
            'distributorPerformance' => $distributorPerformance,
            'pager'                  => $userModel->pager,
            'sortColumn'             => $sortColumn,
            'sortOrder'              => $sortOrder,
            'currentPage'            => $userModel->pager->getCurrentPage('group1'),
            'perPage'                => $userModel->pager->getPerPage('group1'),
            'startDate'              => $startDate, // BARU: Kirim tanggal ke view untuk ditampilkan di form
            'endDate'                => $endDate,   // BARU: Kirim tanggal ke view untuk ditampilkan di form
        ];

        return view('pabrik/monitoring_distributor/index', $data);
    }
    // Fungsi BARU untuk Cetak PDF
    // public function cetakLaporanDistributor()
    // {
    //     if (session()->get('role') !== 'pabrik') {
    //         // Sebaiknya return response JSON jika ini diakses via AJAX atau error page
    //         return $this->response->setStatusCode(403)->setBody('Akses Ditolak');
    //     }

    //     // Muat library Dompdf
    //     $options = new \Dompdf\Options();
    //     $options->set('isHtml5ParserEnabled', true);
    //     $options->set('isRemoteEnabled', true);
    //     $dompdf = new \Dompdf\Dompdf($options);

    //     // Query data TANPA pagination
    //     $db = \Config\Database::connect();
    //     $builder = $db->table('users');
    //     $distributorPerformance = $builder->select([
    //         'users.id',
    //         'users.username',
    //         'COUNT(DISTINCT orders.id) as total_orders',
    //         'SUM(order_items.quantity) as total_quantity',
    //         "SUM(CASE WHEN orders.status IN ('approved', 'processing', 'shipped') THEN 1 ELSE 0 END) as active_orders",
    //         "SUM(CASE WHEN distributor_invoices.status = 'paid' THEN 1 ELSE 0 END) as paid_invoices",
    //         "SUM(CASE WHEN distributor_invoices.status = 'unpaid' THEN 1 ELSE 0 END) as unpaid_invoices",
    //     ])
    //         ->join('orders', 'users.id = orders.distributor_id', 'left')
    //         ->join('order_items', 'orders.id = order_items.order_id', 'left')
    //         ->join('distributor_invoices', 'orders.id = distributor_invoices.order_id', 'left')
    //         ->where('users.role', 'distributor')
    //         ->groupBy('users.id, users.username')
    //         ->orderBy('username', 'asc')
    //         ->get()->getResultArray();

    //     $data = [
    //         'title' => 'Laporan Performa Distributor',
    //         'distributorPerformance' => $distributorPerformance,
    //         'tanggalCetak' => date('d F Y')
    //     ];

    //     // Render view khusus untuk PDF (HTML sederhana)
    //     $html = view('pabrik/monitoring_distributor/cetak_pdf', $data);
    //     $dompdf->loadHtml($html);
    //     $dompdf->setPaper('A4', 'landscape'); // Atur ukuran kertas
    //     $dompdf->render();

    //     // Tampilkan PDF di browser atau langsung download
    //     $dompdf->stream('Laporan_Distributor_' . date('Ymd') . '.pdf', ['Attachment' => 0]); // Attachment 0 = preview
    // }

    // Menampilkan detail order masuk dari Distributor
    public function detailOrderFromDistributor($orderId = null)
    {
        if (session()->get('role') !== 'pabrik') {
            return redirect()->to(base_url('/error'))->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
        }

        // Filter: order hanya berdasarkan status 'rejected' dan 'completed'
        $order = $this->orderModel
            ->where('id', $orderId)
            ->whereIn('status', ['rejected', 'completed'])
            ->orderBy('order_date', 'DESC')
            ->first();

        // Filter: order ini adalah order dari distributor (agen_id = NULL, distributor_id = NOT NULL)
        // $order = $this->orderModel
        //     ->where('agen_id', null) // Perbaikan di sini
        //     ->where('distributor_id IS NOT NULL') // Perbaikan di sini
        //     ->first();

        if (!$order) {
            return redirect()->to(base_url('pabrik'))->with('error', 'Order tidak ditemukan atau Anda tidak memiliki akses ke order ini.');
        }
        $agen = $order['agen_id'] ? $this->userModel->find($order['agen_id']) : null;

        $orderItems = $this->orderItemModel->where('order_id', $order['id'])->findAll();


        $productNames = [];
        foreach ($orderItems as $item) {
            $product = $this->productModel->find($item['product_id']);
            $productNames[$item['product_id']] = $product ? $product['product_name'] : 'Produk Tidak Ditemukan';
        }

        $distributor = $this->userModel->find($order['distributor_id']);

        $data = [
            'title'        => 'Detail Order #ORD-' . $order['id'],
            'order'        => $order,
            'orderItems'   => $orderItems,
            'productNames' => $productNames,
            'distributor'  => $distributor,
            'agen'         => $agen,
        ];

        return view('pabrik/orders/detail_from_distributor', $data);
    }

    // Memperbarui status order dari Distributor (approve/reject) dan status invoice
    public function updateOrderStatusFromDistributor($orderId = null)
    {
        if (session()->get('role') !== 'pabrik') {
            return redirect()->to(base_url('/login'))->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        // Filter: order ini adalah order dari distributor (agen_id = NULL, distributor_id = NOT NULL)
        $order = $this->orderModel
            ->where('id', $orderId)
            ->where('agen_id', null) // Perbaikan di sini
            ->where('distributor_id IS NOT NULL') // Perbaikan di sini
            ->first();

        if (!$order) {
            return redirect()->to(base_url('pabrik'))->with('error', 'Order tidak ditemukan atau Anda tidak memiliki akses.');
        }

        if ($order['status'] !== 'pending') {
            return redirect()->back()->with('error', 'Order ini sudah tidak dalam status "pending" dan tidak bisa diubah.');
        }

        $newStatus = $this->request->getPost('status');
        $notes = $this->request->getPost('notes');

        $rules = [
            'status' => 'required|in_list[approved,rejected]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        $this->orderModel->transStart();
        try {
            $dataToUpdate = [
                'status' => $newStatus,
                'notes'  => $notes,
            ];

            $this->orderModel->update($orderId, $dataToUpdate);

            $invoice = $this->invoiceModel->where('order_id', $orderId)->first();
            if ($invoice) {
                if ($newStatus === 'rejected') {
                    $this->invoiceModel->update($invoice['id'], ['status' => 'cancelled']);
                }
            } else {
                log_message('warning', 'Invoice not found for order ID: ' . $orderId . ' during status update by Pabrik.');
            }

            $this->orderModel->transComplete();

            if ($this->orderModel->transStatus() === false) {
                return redirect()->back()->with('error', 'Gagal memperbarui status order dan tagihan. Silakan coba lagi.');
            }
        } catch (\Exception $e) {
            $this->orderModel->transRollback();
            log_message('error', 'Error updating order status and invoice by Pabrik for order ID ' . $orderId . ': ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memperbarui status order: ' . $e->getMessage());
        }

        $message = 'Order #ORD-' . $orderId . ' berhasil ' . ($newStatus === 'approved' ? 'disetujui.' : 'ditolak.');
        return redirect()->to(base_url('pabrik'))->with('success', $message);
    }

    // public function orderHistory()
    // {
    //     if (session()->get('role') !== 'pabrik') {
    //         return redirect()->to(base_url('/login'))->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
    //     }

    //     $search = $this->request->getVar('search'); // Ambil parameter pencarian
    //     $startDate = $this->request->getVar('start_date'); // Ambil tanggal mulai
    //     $endDate = $this->request->getVar('end_date'); // Ambil tanggal akhir
    //     $statusFilter = $this->request->getVar('status'); // Ambil filter status
    //     $perPage = 10; // Jumlah order per halaman

    //     // Query dasar: order hanya berdasarkan status 'rejected' dan 'completed'
    //     $query = $this->orderModel->whereIn('status', ['rejected', 'completed']);

    //     // Tambahkan filter tanggal jika ada
    //     if (!empty($startDate)) {
    //         $query->where('order_date >=', $startDate . ' 00:00:00');
    //     }
    //     if (!empty($endDate)) {
    //         $query->where('order_date <=', $endDate . ' 23:59:59');
    //     }

    //     // Tambahkan filter status jika ada (selain 'rejected' dan 'completed' yang sudah default)
    //     if (!empty($statusFilter) && in_array($statusFilter, ['rejected', 'completed'])) {
    //         $query->where('status', $statusFilter);
    //     }

    //     // Tambahkan filter pencarian jika ada
    //     if (!empty($search)) {
    //         // Untuk pencarian berdasarkan username distributor/agen, kita perlu mencari ID user terlebih dahulu
    //         $foundUserIds = [];
    //         $users = $this->userModel->like('username', $search)->findAll();
    //         foreach ($users as $user) {
    //             $foundUserIds[] = $user['id'];
    //         }

    //         $query->groupStart()
    //             ->like('id', $search) // Cari berdasarkan Order ID
    //             ->orLike('total_amount', $search); // Cari berdasarkan total jumlah

    //         if (!empty($foundUserIds)) {
    //             $query->orWhereIn('distributor_id', $foundUserIds)
    //                 ->orWhereIn('agen_id', $foundUserIds);
    //         }
    //         $query->groupEnd();
    //     }

    //     // Urutkan berdasarkan tanggal order terbaru
    //     $query->orderBy('order_date', 'DESC');

    //     // Dapatkan data order dengan pagination
    //     $orders = $query->paginate($perPage, 'default', $this->request->getVar('page'));
    //     $pager = $this->orderModel->pager;

    //     // Ambil username distributor dan agen untuk order yang tampil di halaman ini
    //     $distributorUsernames = [];
    //     $uniqueUserIds = [];

    //     foreach ($orders as $order) {
    //         if (!empty($order['distributor_id'])) {
    //             $uniqueUserIds[$order['distributor_id']] = true;
    //         }
    //         if (!empty($order['agen_id'])) {
    //             $uniqueUserIds[$order['agen_id']] = true;
    //         }
    //     }

    //     if (!empty($uniqueUserIds)) {
    //         $users = $this->userModel->whereIn('id', array_keys($uniqueUserIds))->findAll();
    //         foreach ($users as $user) {
    //             $distributorUsernames[$user['id']] = $user['username']; // Menggunakan array ini untuk distributor dan agen
    //         }
    //     }

    //     $data = [
    //         'title'                => 'Riwayat Order Pabrik',
    //         'orders'               => $orders, // Sekarang sudah dipaginasi
    //         'distributorUsernames' => $distributorUsernames, // Digunakan untuk distributor dan agen
    //         'pager'                => $pager, // Kirim objek pager ke view
    //         'search'               => $search, // Kirim nilai pencarian ke view
    //         'startDate'            => $startDate, // Kirim tanggal mulai ke view
    //         'endDate'              => $endDate, // Kirim tanggal akhir ke view
    //         'statusFilter'         => $statusFilter, // Kirim filter status ke view
    //     ];

    //     return view('pabrik/orders/history', $data);
    // }

    private function _buildHistoryQuery()
    {
        $search = $this->request->getVar('search');
        $startDate = $this->request->getVar('start_date');
        $endDate = $this->request->getVar('end_date');
        $statusFilter = $this->request->getVar('status');

        // Query dasar
        $query = $this->orderModel->whereIn('status', ['rejected', 'completed']);

        // Filter tanggal
        if (!empty($startDate)) {
            $query->where('order_date >=', $startDate . ' 00:00:00');
        }
        if (!empty($endDate)) {
            $query->where('order_date <=', $endDate . ' 23:59:59');
        }

        // Filter status
        if (!empty($statusFilter) && in_array($statusFilter, ['rejected', 'completed'])) {
            $query->where('status', $statusFilter);
        }

        // Filter pencarian
        if (!empty($search)) {
            $foundUserIds = [];
            $users = $this->userModel->like('username', $search)->findAll();
            if ($users) {
                $foundUserIds = array_column($users, 'id');
            }

            $query->groupStart()
                ->like('id', $search)
                ->orLike('total_amount', $search);

            if (!empty($foundUserIds)) {
                $query->orWhereIn('distributor_id', $foundUserIds)
                    ->orWhereIn('agen_id', $foundUserIds);
            }
            $query->groupEnd();
        }

        return $query;
    }

    public function orderHistory()
    {
        if (session()->get('role') !== 'pabrik') {
            return redirect()->to(base_url('/login'))->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
        }

        $query = $this->_buildHistoryQuery();

        $perPage = 10;
        $orders = $query->orderBy('order_date', 'DESC')
            ->paginate($perPage, 'default', $this->request->getVar('page'));

        $pager = $this->orderModel->pager;

        // **PERUBAHAN NAMA VARIABEL UNTUK KEJELASAN**
        $usernamesById = []; // Dulu: $distributorUsernames
        $uniqueUserIds = [];
        foreach ($orders as $order) {
            if (!empty($order['distributor_id'])) $uniqueUserIds[$order['distributor_id']] = true;
            if (!empty($order['agen_id'])) $uniqueUserIds[$order['agen_id']] = true; // Baris ini sudah ada, bagus!
        }

        if (!empty($uniqueUserIds)) {
            $users = $this->userModel->whereIn('id', array_keys($uniqueUserIds))->findAll();
            foreach ($users as $user) {
                $usernamesById[$user['id']] = $user['username']; // Dulu: $distributorUsernames
            }
        }

        $data = [
            'title'                => 'Riwayat Order Pabrik',
            'orders'               => $orders,
            'usernamesById'        => $usernamesById, // **DIKIRIM DENGAN NAMA BARU**
            'pager'                => $pager,
            'search'               => $this->request->getVar('search'),
            'startDate'            => $this->request->getVar('start_date'),
            'endDate'              => $this->request->getVar('end_date'),
            'statusFilter'         => $this->request->getVar('status'),
        ];

        return view('pabrik/orders/history', $data);
    }

    public function exportHistory()
    {
        if (session()->get('role') !== 'pabrik') {
            return redirect()->to(base_url('/login'))->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
        }

        $query = $this->_buildHistoryQuery();
        $orders = $query->orderBy('order_date', 'DESC')->findAll();

        if (empty($orders)) {
            return redirect()->back()->with('error', 'Tidak ada data untuk diekspor berdasarkan filter yang dipilih.');
        }

        // **PERUBAHAN NAMA VARIABEL DAN LOGIKA UNTUK KEJELASAN**
        $usernamesById = []; // Dulu: $distributorUsernames
        $uniqueUserIds = [];
        foreach ($orders as $order) {
            if (!empty($order['distributor_id'])) $uniqueUserIds[$order['distributor_id']] = true;
            if (!empty($order['agen_id'])) $uniqueUserIds[$order['agen_id']] = true;
        }
        if (!empty($uniqueUserIds)) {
            $users = $this->userModel->whereIn('id', array_keys($uniqueUserIds))->findAll();
            foreach ($users as $user) {
                $usernamesById[$user['id']] = $user['username']; // Dulu: $distributorUsernames
            }
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // **PERUBAHAN: MENAMBAHKAN KOLOM AGEN**
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Order ID');
        $sheet->setCellValue('C1', 'Distributor');
        $sheet->setCellValue('D1', 'Agen'); // <-- KOLOM BARU
        $sheet->setCellValue('E1', 'Tanggal Order');
        $sheet->setCellValue('F1', 'Tanggal Selesai');
        $sheet->setCellValue('G1', 'Total (Rp)');
        $sheet->setCellValue('H1', 'Status');

        $row = 2;
        $no = 1;
        foreach ($orders as $order) {
            $statusText = (strtolower($order['status']) === 'completed') ? 'Selesai' : 'Ditolak';

            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, 'ORD-' . $order['id']);
            $sheet->setCellValue('C' . $row, $usernamesById[$order['distributor_id']] ?? 'N/A');
            $sheet->setCellValue('D' . $row, $usernamesById[$order['agen_id']] ?? 'N/A'); // <-- DATA BARU
            $sheet->setCellValue('E' . $row, date('d-m-Y H:i', strtotime($order['order_date'])));
            $sheet->setCellValue('F' . $row, date('d-m-Y H:i', strtotime($order['updated_at'])));
            $sheet->setCellValue('G' . $row, $order['total_amount']);
            $sheet->setCellValue('H' . $row, $statusText);

            $row++;
        }

        // **PERUBAHAN: MENYESUAIKAN RANGE STYLE**
        $sheet->getStyle('A1:H1')->getFont()->setBold(true);
        $sheet->getStyle('G2:G' . ($row - 1))->getNumberFormat()->setFormatCode('#,##0'); // Kolom Total sekarang G
        foreach (range('A', 'H') as $col) { // Range sampai H
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'Riwayat_Order_' . date('Y-m-d') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit();
    }

    // public function shipmentHistory()
    // {
    //     if (session()->get('role') !== 'pabrik') {
    //         return redirect()->to(base_url('/login'))->with('error', 'Anda tidak memiliki akses ke halaman ini.');
    //     }

    //     $search = $this->request->getVar('search'); // Ambil parameter pencarian
    //     $startDate = $this->request->getVar('start_date'); // Ambil filter tanggal mulai
    //     $endDate = $this->request->getVar('end_date');     // Ambil filter tanggal akhir
    //     $perPage = 10; // Jumlah pengiriman per halaman

    //     // Query dasar untuk pengiriman yang berstatus 'on_transit'
    //     $query = $this->shipmentModel->where('delivery_status', 'on_transit');

    //     // Tambahkan filter pencarian jika ada
    //     if (!empty($search)) {
    //         $foundOrderIds = [];
    //         $foundUserIds = [];
    //         $users = $this->userModel->like('username', $search)->findAll();
    //         foreach ($users as $user) {
    //             $foundUserIds[] = $user['id'];
    //         }

    //         if (!empty($foundUserIds)) {
    //             $ordersByDistributorOrAgen = $this->orderModel
    //                 ->whereIn('distributor_id', $foundUserIds)
    //                 ->orWhereIn('agen_id', $foundUserIds)
    //                 ->findAll();
    //             foreach ($ordersByDistributorOrAgen as $order) {
    //                 $foundOrderIds[] = $order['id'];
    //             }
    //         }

    //         $query->groupStart()
    //             ->like('id', $search) // Cari berdasarkan ID Pengiriman (Shipment ID)
    //             ->orLike('tracking_number', $search); // Cari berdasarkan No. Resi

    //         if (!empty($foundOrderIds)) {
    //             $query->orWhereIn('order_id', $foundOrderIds); // Cari berdasarkan Order ID yang terkait dengan user
    //         }
    //         $query->groupEnd();
    //     }

    //     // Tambahkan filter rentang tanggal jika ada
    //     if (!empty($startDate) && !empty($endDate)) {
    //         // Pastikan format tanggal valid sebelum digunakan dalam query
    //         $query->where('shipping_date >=', $startDate . ' 00:00:00');
    //         $query->where('shipping_date <=', $endDate . ' 23:59:59');
    //     }


    //     // Urutkan berdasarkan tanggal pengiriman terbaru
    //     $query->orderBy('shipping_date', 'ASC');

    //     // Dapatkan data pengiriman dengan pagination
    //     $shipments = $query->paginate($perPage, 'default', $this->request->getVar('page'));
    //     $pager = $this->shipmentModel->pager;

    //     // Ambil data order, username distributor, dan username agen untuk pengiriman yang tampil di halaman ini
    //     $ordersData = [];
    //     $distributorUsernames = [];
    //     $agenUsernames = [];
    //     $uniqueOrderIds = [];
    //     $uniqueDistributorIds = [];
    //     $uniqueAgenIds = [];

    //     foreach ($shipments as $shipment) {
    //         $uniqueOrderIds[$shipment['order_id']] = true;
    //     }

    //     if (!empty($uniqueOrderIds)) {
    //         $orders = $this->orderModel->whereIn('id', array_keys($uniqueOrderIds))->findAll();
    //         foreach ($orders as $order) {
    //             $ordersData[$order['id']] = $order;

    //             if (!empty($order['distributor_id'])) {
    //                 $uniqueDistributorIds[$order['distributor_id']] = true;
    //             }
    //             if (!empty($order['agen_id'])) {
    //                 $uniqueAgenIds[$order['agen_id']] = true;
    //             }
    //         }
    //     }

    //     // Fetch usernames and complete data for unique distributor IDs
    //     $distributorFullData = [];
    //     if (!empty($uniqueDistributorIds)) {
    //         $distributors = $this->userModel->whereIn('id', array_keys($uniqueDistributorIds))->findAll();
    //         foreach ($distributors as $distributor) {
    //             $distributorUsernames[$distributor['id']] = $distributor['username'];
    //             $distributorFullData[$distributor['id']] = $distributor;
    //         }
    //     }

    //     // Fetch usernames and complete data for unique agen IDs
    //     $agenFullData = [];
    //     if (!empty($uniqueAgenIds)) {
    //         $agens = $this->userModel->whereIn('id', array_keys($uniqueAgenIds))->findAll();
    //         foreach ($agens as $agen) {
    //             $agenUsernames[$agen['id']] = $agen['username'];
    //             $agenAlamats[$agen['id']] = $agen['alamat'];
    //             $agenFullData[$agen['id']] = $agen;
    //         }
    //     }

    //     // Mengambil Order Items dan Product Names
    //     $orderItems = [];
    //     $productNames = [];

    //     if (!empty($uniqueOrderIds)) {
    //         $allRelatedOrderItems = $this->orderModel->getOrderItemsByOrderIds(array_keys($uniqueOrderIds));
    //         foreach ($allRelatedOrderItems as $item) {
    //             $orderItems[$item['order_id']][] = $item;
    //         }

    //         $allProductIds = array_unique(array_column($allRelatedOrderItems, 'product_id'));
    //         if (!empty($allProductIds)) {
    //             $products = $this->orderModel->getProductsByIds($allProductIds);
    //             foreach ($products as $product) {
    //                 $productNames[$product['id']] = $product['product_name'];
    //             }
    //         }
    //     }

    //     $data = [
    //         'title'                 => 'Manajemen Pengiriman',
    //         'shipments'             => $shipments,
    //         'ordersData'            => $ordersData,
    //         'distributorUsernames'  => $distributorUsernames,
    //         'agenUsernames'         => $agenUsernames,
    //         'distributorFullData'   => $distributorFullData,
    //         'agenFullData'          => $agenFullData,
    //         // 'agenAlamats'           => $agenAlamats,
    //         'orderItems'            => $orderItems,
    //         'productNames'          => $productNames,
    //         'pager'                 => $pager,
    //         'search'                => $search,
    //         'startDate'             => $startDate, // Kirim ke view
    //         'endDate'               => $endDate,   // Kirim ke view
    //     ];

    //     return view('pabrik/shipments/history', $data);
    // }
    public function shipmentHistory()
    {
        if (session()->get('role') !== 'pabrik') {
            return redirect()->to(base_url('/login'))->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        $search = $this->request->getVar('search'); // Ambil parameter pencarian
        $startDate = $this->request->getVar('start_date'); // Ambil filter tanggal mulai
        $endDate = $this->request->getVar('end_date');     // Ambil filter tanggal akhir
        $perPage = 10; // Jumlah pengiriman per halaman

        // Query dasar untuk pengiriman yang berstatus 'on_transit'
        $query = $this->shipmentModel->where('delivery_status', 'on_transit');

        // Tambahkan filter pencarian jika ada
        if (!empty($search)) {
            $foundOrderIds = [];
            $foundUserIds = [];
            $users = $this->userModel->like('username', $search)->findAll();
            foreach ($users as $user) {
                $foundUserIds[] = $user['id'];
            }

            if (!empty($foundUserIds)) {
                $ordersByDistributorOrAgen = $this->orderModel
                    ->whereIn('distributor_id', $foundUserIds)
                    ->orWhereIn('agen_id', $foundUserIds)
                    ->findAll();
                foreach ($ordersByDistributorOrAgen as $order) {
                    $foundOrderIds[] = $order['id'];
                }
            }

            $query->groupStart()
                ->like('id', $search) // Cari berdasarkan ID Pengiriman (Shipment ID)
                ->orLike('tracking_number', $search); // Cari berdasarkan No. Resi

            if (!empty($foundOrderIds)) {
                $query->orWhereIn('order_id', $foundOrderIds); // Cari berdasarkan Order ID yang terkait dengan user
            }
            $query->groupEnd();
        }

        // Tambahkan filter rentang tanggal jika ada
        if (!empty($startDate) && !empty($endDate)) {
            // Pastikan format tanggal valid sebelum digunakan dalam query
            $query->where('shipping_date >=', $startDate . ' 00:00:00');
            $query->where('shipping_date <=', $endDate . ' 23:59:59');
        }


        // Urutkan berdasarkan tanggal pengiriman terbaru
        $query->orderBy('shipping_date', 'ASC');

        // Dapatkan data pengiriman dengan pagination
        $shipments = $query->paginate($perPage, 'default', $this->request->getVar('page'));
        $pager = $this->shipmentModel->pager;

        // Ambil data order, username distributor, dan username agen untuk pengiriman yang tampil di halaman ini
        $ordersData = [];
        $distributorUsernames = [];
        $agenUsernames = [];
        $agenAlamats = []; // Perbaikan: Inisialisasi array untuk alamat agen
        $uniqueOrderIds = [];
        $uniqueDistributorIds = [];
        $uniqueAgenIds = [];

        foreach ($shipments as $shipment) {
            $uniqueOrderIds[$shipment['order_id']] = true;
        }

        if (!empty($uniqueOrderIds)) {
            $orders = $this->orderModel->whereIn('id', array_keys($uniqueOrderIds))->findAll();
            foreach ($orders as $order) {
                $ordersData[$order['id']] = $order;

                if (!empty($order['distributor_id'])) {
                    $uniqueDistributorIds[$order['distributor_id']] = true;
                }
                if (!empty($order['agen_id'])) {
                    $uniqueAgenIds[$order['agen_id']] = true;
                }
            }
        }

        // Fetch usernames and complete data for unique distributor IDs
        $distributorFullData = [];
        if (!empty($uniqueDistributorIds)) {
            $distributors = $this->userModel->whereIn('id', array_keys($uniqueDistributorIds))->findAll();
            foreach ($distributors as $distributor) {
                $distributorUsernames[$distributor['id']] = $distributor['username'];
                $distributorFullData[$distributor['id']] = $distributor;
            }
        }

        // Fetch usernames and complete data for unique agen IDs
        $agenFullData = [];
        if (!empty($uniqueAgenIds)) {
            $agens = $this->userModel->whereIn('id', array_keys($uniqueAgenIds))->findAll();
            foreach ($agens as $agen) {
                $agenUsernames[$agen['id']] = $agen['username'];
                $agenAlamats[$agen['id']] = $agen['alamat']; // Baris ini sudah benar, memastikan data alamat diambil
                $agenFullData[$agen['id']] = $agen;
            }
        }

        // Mengambil Order Items dan Product Names
        $orderItems = [];
        $productNames = [];

        if (!empty($uniqueOrderIds)) {
            $allRelatedOrderItems = $this->orderModel->getOrderItemsByOrderIds(array_keys($uniqueOrderIds));
            foreach ($allRelatedOrderItems as $item) {
                $orderItems[$item['order_id']][] = $item;
            }

            $allProductIds = array_unique(array_column($allRelatedOrderItems, 'product_id'));
            if (!empty($allProductIds)) {
                $products = $this->orderModel->getProductsByIds($allProductIds);
                foreach ($products as $product) {
                    $productNames[$product['id']] = $product['product_name'];
                }
            }
        }

        $data = [
            'title'                 => 'Manajemen Pengiriman',
            'shipments'             => $shipments,
            'ordersData'            => $ordersData,
            'distributorUsernames'  => $distributorUsernames,
            'agenUsernames'         => $agenUsernames,
            'distributorFullData'   => $distributorFullData,
            'agenFullData'          => $agenFullData,
            'agenAlamats'           => $agenAlamats, // Perbaikan: Mengirimkan data alamat agen ke view
            'orderItems'            => $orderItems,
            'productNames'          => $productNames,
            'pager'                 => $pager,
            'search'                => $search,
            'startDate'             => $startDate,
            'endDate'               => $endDate,
        ];

        return view('pabrik/shipments/history', $data);
    }

    public function bulkPrintShipments()
    {
        if (session()->get('role') !== 'pabrik') {
            return redirect()->to(base_url('/login'))->with('error', 'Anda tidak memiliki akses.');
        }

        // 1. Ambil array ID pengiriman dari form yang disubmit
        $shipmentIds = $this->request->getPost('shipment_ids');

        if (empty($shipmentIds)) {
            return redirect()->back()->with('error', 'Tidak ada pengiriman yang dipilih untuk dicetak.');
        }

        // 2. Ambil semua data pengiriman berdasarkan ID yang dipilih (tanpa pagination)
        $shipments = $this->shipmentModel->whereIn('id', $shipmentIds)
            ->orderBy('shipping_date', 'DESC')
            ->findAll();

        if (empty($shipments)) {
            return redirect()->back()->with('error', 'Data pengiriman yang dipilih tidak ditemukan.');
        }

        // 3. Kumpulkan semua data terkait (sama seperti di method history)
        $orderIds = array_column($shipments, 'order_id');
        $ordersData = [];
        $distributorIds = [];
        $agenIds = [];

        if (!empty($orderIds)) {
            $orders = $this->orderModel->whereIn('id', $orderIds)->findAll();
            foreach ($orders as $order) {
                $ordersData[$order['id']] = $order;
                if (!empty($order['distributor_id'])) $distributorIds[] = $order['distributor_id'];
                if (!empty($order['agen_id'])) $agenIds[] = $order['agen_id'];
            }
        }

        $distributorFullData = [];
        if (!empty($distributorIds)) {
            $distributors = $this->userModel->whereIn('id', array_unique($distributorIds))->findAll();
            foreach ($distributors as $distributor) {
                $distributorFullData[$distributor['id']] = $distributor;
            }
        }

        $agenFullData = [];
        if (!empty($agenIds)) {
            $agens = $this->userModel->whereIn('id', array_unique($agenIds))->findAll();
            foreach ($agens as $agen) {
                $agenFullData[$agen['id']] = $agen;
            }
        }

        $orderItems = [];
        $productNames = [];
        if (!empty($orderIds)) {
            $allRelatedOrderItems = $this->orderModel->getOrderItemsByOrderIds($orderIds);
            foreach ($allRelatedOrderItems as $item) {
                $orderItems[$item['order_id']][] = $item;
            }

            $allProductIds = array_unique(array_column($allRelatedOrderItems, 'product_id'));
            if (!empty($allProductIds)) {
                $products = $this->orderModel->getProductsByIds($allProductIds);
                foreach ($products as $product) {
                    $productNames[$product['id']] = $product['product_name'];
                }
            }
        }

        // 4. Siapkan data untuk view cetak
        $data = [
            'title'               => 'Cetak Dokumen Pengiriman',
            'shipments'           => $shipments,
            'ordersData'          => $ordersData,
            'distributorFullData' => $distributorFullData,
            'agenFullData'        => $agenFullData,
            'orderItems'          => $orderItems,
            'productNames'        => $productNames,
        ];

        // 5. Load view khusus untuk mencetak
        return view('pabrik/shipments/bulk_print_view', $data);
    }

    // Opsional: Detail pengiriman dan update status pengiriman (misal: Delivered)
    public function detailShipment($shipmentId = null)
    {
        if (session()->get('role') !== 'pabrik') {
            return redirect()->to(base_url('/login'))->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        $shipment = $this->shipmentModel->find($shipmentId);
        if (!$shipment) {
            return redirect()->to(base_url('pabrik/shipments/history'))->with('error', 'Pengiriman tidak ditemukan.');
        }

        $order = $this->orderModel->find($shipment['order_id']);
        if (!$order) {
            return redirect()->to(base_url('pabrik/shipments/history'))->with('error', 'Order terkait pengiriman tidak ditemukan.');
        }

        $orderItems = $this->orderItemModel->where('order_id', $order['id'])->findAll();

        $productNames = [];
        foreach ($orderItems as $item) {
            $product = $this->productModel->find($item['product_id']);
            $productNames[$item['product_id']] = $product ? $product['product_name'] : 'Produk Tidak Ditemukan';
        }

        $distributor = $this->userModel->find($order['distributor_id']);
        $agen = $order['agen_id'] ? $this->userModel->find($order['agen_id']) : null;

        $data = [
            'title'        => 'Detail Pengiriman #' . $shipment['id'],
            'shipment'     => $shipment,
            'order'        => $order,
            'orderItems'   => $orderItems,
            'productNames' => $productNames,
            'distributor'  => $distributor,
            'agen'         => $agen,
        ];

        return view('pabrik/shipments/detail', $data);
    }

    // Opsional: Untuk menandai pengiriman sebagai "delivered"
    // public function updateShipmentStatus($shipmentId = null)
    // {
    //     if (session()->get('role') !== 'pabrik') {
    //         return redirect()->to(base_url('/login'))->with('error', 'Anda tidak memiliki akses ke halaman ini.');
    //     }

    //     $shipment = $this->shipmentModel->find($shipmentId);
    //     if (!$shipment) {
    //         return redirect()->to(base_url('pabrik/shipments/history'))->with('error', 'Pengiriman tidak ditemukan.');
    //     }

    //     // Hanya izinkan update jika statusnya masih on_transit
    //     if ($shipment['delivery_status'] !== 'on_transit') {
    //         return redirect()->back()->with('error', 'Status pengiriman ini tidak dapat diubah lagi.');
    //     }

    //     $newStatus = $this->request->getPost('status');
    //     $rules = [
    //         'status' => 'required|in_list[delivered,failed]',
    //     ];
    //     if (!$this->validate($rules)) {
    //         return redirect()->back()->with('errors', $this->validator->getErrors());
    //     }

    //     $this->shipmentModel->transStart();
    //     try {
    //         $dataToUpdate = [
    //             'delivery_status' => $newStatus,
    //             'delivery_date'   => date('Y-m-d H:i:s'), // Set tanggal delivery
    //         ];
    //         $this->shipmentModel->update($shipmentId, $dataToUpdate);

    //         // Update status order menjadi 'completed' jika pengiriman berhasil delivered
    //         if ($newStatus === 'delivered') {
    //             $this->orderModel->update($shipment['order_id'], ['status' => 'completed']);
    //         } elseif ($newStatus === 'failed') {
    //             // Mungkin perlu logika lain jika failed, misal: order kembali ke status 'approved' atau 'rejected'
    //             $this->orderModel->update($shipment['order_id'], ['status' => 'failed']); // Contoh
    //         }

    //         $this->shipmentModel->transComplete();

    //         if ($this->shipmentModel->transStatus() === false) {
    //             return redirect()->back()->with('error', 'Gagal memperbarui status pengiriman. Silakan coba lagi.');
    //         }
    //     } catch (\Exception $e) {
    //         $this->shipmentModel->transRollback();
    //         log_message('error', 'Error updating shipment status for shipment ID ' . $shipmentId . ': ' . $e->getMessage());
    //         return redirect()->back()->with('error', 'Terjadi kesalahan saat memperbarui status pengiriman: ' . $e->getMessage());
    //     }

    //     return redirect()->to(base_url('pabrik/shipments/history'))->with('success', 'Status pengiriman #' . $shipmentId . ' berhasil diperbarui menjadi ' . ucfirst($newStatus) . '.');
    // }
    public function updateShipmentStatus($shipmentId = null)
    {
        // 1. Validasi Akses Pengguna
        if (session()->get('role') !== 'pabrik') {
            return redirect()->to(base_url('/login'))->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        // 2. Ambil data pengiriman dari database
        $shipment = $this->shipmentModel->find($shipmentId);
        if (!$shipment) {
            return redirect()->to(base_url('pabrik/shipments/history'))->with('error', 'Data pengiriman tidak ditemukan.');
        }

        // Hanya izinkan update jika status sebelumnya 'on_transit'
        if ($shipment['delivery_status'] !== 'on_transit') {
            return redirect()->back()->with('error', 'Status pengiriman ini sudah final dan tidak dapat diubah lagi.');
        }

        // 3. Validasi input status baru
        $newStatus = $this->request->getPost('status');
        $rules = [
            'status' => 'required|in_list[delivered,failed]',
        ];
        if (!$this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        // Memulai database transaction untuk memastikan semua query berhasil
        $this->db->transStart();

        try {
            $orderId = $shipment['order_id'];

            // Skenario jika pengiriman BERHASIL (delivered)
            if ($newStatus === 'delivered') {
                // 1. Update status di tabel shipments menjadi 'delivered'
                $this->shipmentModel->update($shipmentId, [
                    'delivery_status' => 'delivered'
                ]);

                // 2. Update status di tabel orders menjadi 'completed' dan isi delivery_date
                $this->orderModel->update($orderId, [
                    'status'        => 'completed',
                    'delivery_date' => date('Y-m-d H:i:s') // Mengisi tanggal dan waktu saat ini
                ]);

                // Skenario jika pengiriman GAGAL (failed)
            } elseif ($newStatus === 'failed') {
                // 1. Update status di tabel shipments menjadi 'failed'
                $this->shipmentModel->update($shipmentId, [
                    'delivery_status' => 'failed'
                ]);

                // 2. Update status di tabel orders menjadi 'failed'
                // Pastikan Anda sudah menambahkan 'failed' ke dalam tipe ENUM di kolom status tabel orders
                $this->orderModel->update($orderId, [
                    'status' => 'rejected' // Atau 'failed' sesuai kebutuhan
                ]);

                // 3. Update status di tabel invoices menjadi 'cancelled'
                // Menggunakan where untuk keamanan agar hanya invoice dengan order_id yang sesuai yang diubah
                $this->invoiceModel->where('order_id', $orderId)->set(['status' => 'cancelled'])->update();

                // 4. Update status dan notes di tabel distributor_invoices menjadi 'cancelled'
                $customNotes = "Pengiriman gagal, invoice untuk pesanan ini telah dibatalkan secara otomatis.";
                $this->distributorInvoiceModel->where('order_id', $orderId)
                    ->set([
                        'status' => 'cancelled',
                        'notes'  => $customNotes
                    ])
                    ->update();
            }

            // Menyelesaikan transaction
            $this->db->transComplete();

            // Cek apakah transaction berhasil atau gagal
            if ($this->db->transStatus() === false) {
                // Jika gagal, transaction akan otomatis di-rollback oleh CodeIgniter
                return redirect()->back()->with('error', 'Gagal memperbarui status pengiriman. Terjadi kesalahan pada database.');
            }

            // Jika berhasil, redirect dengan pesan sukses
            return redirect()->to(base_url('pabrik/shipments/history'))->with('success', 'Status pengiriman untuk pesanan #' . $orderId . ' berhasil diperbarui menjadi ' . ucfirst($newStatus) . '.');
        } catch (\Exception $e) {
            // Jika terjadi exception, rollback transaction secara manual dan catat error
            $this->db->transRollback();
            log_message('error', '[ERROR] Gagal update status pengiriman ID ' . $shipmentId . ': ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem saat memperbarui status: ' . $e->getMessage());
        }
    }
}
