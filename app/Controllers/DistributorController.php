<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\InvoiceModel;
use App\Models\ProductModel;
use App\Models\OrderItemModel;
use App\Models\DistributorInvoiceModel;
use App\Models\OrderModel; // Asumsi Anda punya OrderModel
use App\Models\ShipmentModel; // Asumsi Anda punya ShipmentModel

class DistributorController extends BaseController
{
    protected $userModel;
    protected $orderModel;
    protected $productModel;
    protected $orderItemModel;
    protected $shipmentModel;
    protected $invoiceModel;
    protected $distributorInvoiceModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->orderModel = new OrderModel(); // Inisialisasi OrderModel
        $this->productModel = new ProductModel(); // Inisialisasi OrderModel
        $this->orderItemModel = new OrderItemModel(); // Inisialisasi OrderModel
        $this->shipmentModel = new ShipmentModel(); // Inisialisasi ShipmentModel
        $this->invoiceModel = new InvoiceModel();
        $this->distributorInvoiceModel = new DistributorInvoiceModel();
    }

    // public function index()
    // {
    //     // Pastikan hanya user dengan role 'distributor' yang bisa mengakses
    //     if (session()->get('role') !== 'distributor') {
    //         return redirect()->to(base_url('/'))->with('error', 'Anda tidak memiliki akses ke halaman ini.');
    //     }

    //     $distributorId = session()->get('id'); // ID distributor yang sedang login

    //     // 1. Hitung Jumlah Agen yang bersangkutan
    //     $totalAgents = $this->userModel
    //         ->where('role', 'agen')
    //         ->where('parent_id', $distributorId)
    //         ->countAllResults();

    //     // 2. Hitung Order Masuk (Pending)
    //     // Asumsi status 'pending' untuk order masuk
    //     $totalOrdersPending = $this->orderModel
    //         ->where('distributor_id', $distributorId)
    //         ->where('status', 'pending')
    //         ->countAllResults();

    //     // 3. Hitung Pengiriman (On Transit)
    //     // Asumsi status 'on_transit' untuk pengiriman
    //     // Ini mungkin perlu JOIN ke tabel orders atau langsung ke shipments jika ada order_id di shipments
    //     // Berdasarkan DB Anda, shipments memiliki order_id dan status pengiriman.
    //     $totalShipmentsOnTransit = $this->shipmentModel
    //         ->whereIn('delivery_status', ['on_transit', 'pending']) // 'pending' mungkin berarti belum dikirim tapi sudah disiapkan
    //         ->join('orders', 'orders.id = shipments.order_id')
    //         ->where('orders.distributor_id', $distributorId)
    //         ->countAllResults();

    //     // 4. Hitung Order Selesai (Completed / Delivered)
    //     // Asumsi status 'completed' untuk order selesai di tabel orders
    //     $totalOrdersCompleted = $this->orderModel
    //         ->where('distributor_id', $distributorId)
    //         ->where('status', 'completed')
    //         ->countAllResults();

    //     // Data Status Pengiriman Terbaru (dari shipments yang terkait dengan distributor ini)
    //     // Ini akan digunakan untuk tabel di bagian bawah dashboard
    //     $latestShipments = $this->shipmentModel
    //         ->select('shipments.*, orders.agen_id as agent_id, orders.order_date as order_date_from_order')
    //         ->join('orders', 'orders.id = shipments.order_id')
    //         ->where('orders.distributor_id', $distributorId)
    //         ->orderBy('shipments.created_at', 'DESC')
    //         ->limit(5) // Ambil 5 pengiriman terbaru
    //         ->findAll();

    //     // Fetch username agen untuk tampilan di tabel
    //     $agentUsernames = [];
    //     $pabrikUsernames = []; // Mungkin perlu ID pabrik dari OrderModel
    //     foreach ($latestShipments as $shipment) {
    //         if (!empty($shipment['agent_id']) && !isset($agentUsernames[$shipment['agent_id']])) {
    //             $agentData = $this->userModel->find($shipment['agent_id']);
    //             $agentUsernames[$shipment['agent_id']] = $agentData ? $agentData['username'] : 'N/A';
    //         }
    //         // Mendapatkan ID Pabrik dari Order (jika ada)
    //         $orderData = $this->orderModel->find($shipment['order_id']);
    //         if ($orderData && !empty($orderData['pabrik_id']) && !isset($pabrikUsernames[$orderData['pabrik_id']])) {
    //             $pabrikData = $this->userModel->find($orderData['pabrik_id']);
    //             $pabrikUsernames[$orderData['pabrik_id']] = $pabrikData ? $pabrikData['username'] : 'N/A';
    //         }
    //     }


    //     $data = [
    //         'title'                     => 'Dashboard Distributor',
    //         'totalAgents'               => $totalAgents,
    //         'totalOrdersPending'        => $totalOrdersPending,
    //         'totalShipmentsOnTransit'   => $totalShipmentsOnTransit,
    //         'totalOrdersCompleted'      => $totalOrdersCompleted,
    //         'latestShipments'           => $latestShipments,
    //         'agentUsernames'            => $agentUsernames,
    //         'pabrikUsernames'           => $pabrikUsernames,
    //     ];

    //     return view('distributor/index', $data);
    // }
    public function index()
    {
        // Pastikan hanya user dengan role 'distributor' yang bisa mengakses
        if (session()->get('role') !== 'distributor') {
            return redirect()->to(base_url('/'))->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
        }

        $distributorId = session()->get('id'); // ID distributor yang sedang login
        $currentMonthStart = date('Y-m-01 00:00:00'); // Awal bulan ini
        $nextMonthStart = date('Y-m-01 00:00:00', strtotime('+1 month')); // Awal bulan depan
        $currentMonthEnd = date('Y-m-t 23:59:59'); // Akhir bulan ini (tanggal terakhir bulan)

        $currentMonth = date('Y-m-01 00:00:00'); // Awal bulan ini
        $nextMonth = date('Y-m-01 00:00:00', strtotime('+1 month')); // Awal bulan depan
        $currentWeekStart = date('Y-m-d 00:00:00', strtotime('monday this week')); // Awal minggu ini (Senin)
        $currentWeekEnd = date('Y-m-d 23:59:59', strtotime('sunday this week')); // Akhir minggu ini (Minggu)

        // 1. Tagihan saya (Distributor) Belum Lunas (dari pabrik)
        // Ini adalah invoice yang ditujukan ke distributor (tabel 'distributor_invoices')
        // $myUnpaidInvoices = $this->distributorInvoiceModel
        //     ->where('distributor_id', $distributorId)
        //     ->where('status', 'unpaid') // Asumsi status 'unpaid'
        //     ->countAllResults();
        $myPaidInvoices = $this->distributorInvoiceModel
            ->join('orders', 'orders.id = distributor_invoices.order_id')
            ->where('orders.distributor_id', $distributorId)
            ->where('distributor_invoices.status', 'paid')
            ->countAllResults();

        $myUnpaidInvoices = $this->distributorInvoiceModel
            ->join('orders', 'orders.id = distributor_invoices.order_id')
            ->where('orders.distributor_id', $distributorId)
            ->whereIn('distributor_invoices.status', ['unpaid', 'partially_paid'])
            ->countAllResults();
        // 2. Tagihan agen saya Belum Lunas
        // Ini adalah invoice yang dibuat oleh distributor untuk agen (tabel 'invoices')
        // Kita perlu join ke tabel 'orders' untuk memastikan ordernya milik distributor ini
        $agenUnpaidInvoices = $this->invoiceModel
            ->select('invoices.id') // Ambil ID saja untuk hitungan
            ->join('orders', 'orders.id = invoices.order_id')
            ->where('orders.distributor_id', $distributorId)
            ->where('invoices.status', 'unpaid') // Asumsi status 'unpaid'
            ->countAllResults();

        // 3. Tagihan agen saya sudah Lunas
        $agenPaidInvoices = $this->invoiceModel
            ->select('invoices.id')
            ->join('orders', 'orders.id = invoices.order_id')
            ->where('orders.distributor_id', $distributorId)
            ->where('invoices.status', 'paid') // Asumsi status 'paid'
            ->countAllResults();

        // 4. Order Masuk (Pending)
        $incomingOrders = $this->orderModel
            ->where('distributor_id', $distributorId)
            ->where('status', 'pending')
            ->countAllResults();

        // 5. Order Dikirim Minggu ini
        // Menggunakan tabel 'shipments' dan 'orders'
        // $ordersShippedThisWeek = $this->shipmentModel
        //     ->select('shipments.id')
        //     ->join('orders', 'orders.id = shipments.order_id')
        //     ->where('orders.distributor_id', $distributorId)
        //     ->where('shipments.shipping_date >=', $currentWeekStart)
        //     ->where('shipments.shipping_date <=', $currentWeekEnd)
        //     ->countAllResults();
        $ordersShipped = $this->orderModel
            ->where('distributor_id', $distributorId)
            ->where('status', 'shipped')
            ->countAllResults();
        // 6. Order Bulan ini
        // Menggunakan tabel 'orders' dengan status 'completed'
        $ordersCompletedThisMonth = $this->orderModel
            ->where('distributor_id', $distributorId)
            ->where('status', 'completed') // Asumsi status yang menandakan order selesai
            ->where('order_date >=', $currentMonth) // Asumsi 'delivery_date' menandakan selesai
            ->where('order_date <', $nextMonth)
            ->countAllResults();

        // 7. Data agen saya
        $totalAgents = $this->userModel
            ->where('role', 'agen')
            ->where('parent_id', $distributorId) // Asumsi 'parent_id' di tabel 'users' menunjuk ke ID distributor
            ->countAllResults();

        // 8. Keuntungan/ Pendapatan saya Bulan ini (Dari tagihan agen yang sudah di lunasi)
        $profitThisMonth = $this->invoiceModel
            ->selectSum('invoices.amount_total', 'total_profit')
            ->join('orders', 'orders.id = invoices.order_id')
            ->where('orders.distributor_id', $distributorId)
            ->where('invoices.status', 'paid')
            ->where('invoices.invoice_date >=', $currentMonthStart) // Asumsi 'payment_date' adalah tanggal pembayaran
            ->where('invoices.invoice_date <', $currentMonthEnd)
            ->get()->getRow()->total_profit ?? 0; // Mengambil total atau 0 jika null

        // Data Status Pengiriman Terbaru (dari shipments yang terkait dengan distributor ini)
        $latestShipments = $this->shipmentModel
            ->select('shipments.*, orders.agen_id, orders.total_amount, orders.order_date as order_date_from_order')
            ->join('orders', 'orders.id = shipments.order_id')
            ->where('orders.distributor_id', $distributorId)
            ->orderBy('shipments.created_at', 'DESC')
            ->limit(5) // Ambil 5 pengiriman terbaru
            ->findAll();

        // Fetch username agen untuk tampilan di tabel
        $agentUsernames = [];
        foreach ($latestShipments as $shipment) {
            if (!empty($shipment['agen_id']) && !isset($agentUsernames[$shipment['agen_id']])) {
                $agentData = $this->userModel->find($shipment['agen_id']);
                $agentUsernames[$shipment['agen_id']] = $agentData ? $agentData['username'] : 'N/A';
            }
        }

        $data = [
            'title'                      => 'Dashboard Distributor',
            'myUnpaidInvoices'           => $myUnpaidInvoices,
            'myPaidInvoices'           => $myPaidInvoices,
            'agenUnpaidInvoices'         => $agenUnpaidInvoices,
            'agenPaidInvoices'           => $agenPaidInvoices,
            'incomingOrders'             => $incomingOrders,
            'ordersShipped'                => $ordersShipped,
            'ordersCompletedThisMonth'   => $ordersCompletedThisMonth,
            'totalAgents'                => $totalAgents,
            'profitThisMonth'            => $profitThisMonth,
            'latestShipments'            => $latestShipments,
            'agentUsernames'             => $agentUsernames,
            'thisMonthStartDate'         => date('Y-m-d', strtotime($currentMonthStart)), // Format YYYY-MM-DD
            'thisMonthEndDate'           => date('Y-m-d', strtotime($currentMonthEnd)),
            // 'pabrikUsernames'         => $pabrikUsernames, // Tidak digunakan di tabel terbaru
        ];

        return view('distributor/index', $data);
    }

    // public function invoicesAgen()
    // {
    //     // Pastikan hanya user dengan role 'distributor' yang bisa mengakses
    //     if (session()->get('role') !== 'distributor') {
    //         return redirect()->to(base_url('/'))->with('error', 'Anda tidak memiliki akses ke halaman ini.');
    //     }

    //     $distributorId = session()->get('id'); // Ambil ID distributor yang sedang login

    //     // Ambil input pencarian dan filter dari URL
    //     $searchInvoiceId = $this->request->getVar('invoice_id');
    //     $searchOrderId = $this->request->getVar('order_id');
    //     $searchAgentName = $this->request->getVar('agent_name'); // Nama agen untuk pencarian
    //     $statusFilter = $this->request->getVar('status');
    //     $startDate = $this->request->getVar('start_date');
    //     $endDate = $this->request->getVar('end_date');

    //     $invoices = $this->invoiceModel->getAgentInvoices(
    //         $distributorId,
    //         $searchInvoiceId,
    //         $searchOrderId,
    //         $searchAgentName,
    //         $statusFilter,
    //         $startDate,
    //         $endDate
    //     );

    //     // Ambil daftar agen yang terhubung dengan distributor ini untuk dropdown filter
    //     // Pastikan tabel users memiliki kolom 'parent_id' atau sejenisnya yang menunjuk ke ID distributor
    //     $agents = $this->userModel
    //         ->where('role', 'agen')
    //         ->where('parent_id', $distributorId)
    //         ->findAll();

    //     $data = [
    //         'title'              => 'Daftar Invoice Agen',
    //         'pageTitle'          => 'Daftar Invoice Agen',
    //         'invoices'           => $invoices,
    //         'searchInvoiceId'    => $searchInvoiceId,
    //         'searchOrderId'      => $searchOrderId,
    //         'searchAgentName'    => $searchAgentName,
    //         'statusFilter'       => $statusFilter,
    //         'startDate'          => $startDate,
    //         'endDate'            => $endDate,
    //         // 'pager'             => $pager, // Kirim objek pager ke view
    //         // 'perPage'            => $perPage, // Kirimkan juga perPage untuk penomoran
    //         'allInvoiceStatuses' => ['unpaid', 'paid', 'cancelled'], // Daftar semua status tagihan yang mungkin
    //         'agents'             => $agents, // Kirim daftar agen ke view
    //     ];

    //     return view('distributor/invoices/index', $data);
    // }
    public function invoicesAgen()
    {
        // Pastikan hanya user dengan role 'distributor' yang bisa mengakses
        if (session()->get('role') !== 'distributor') {
            return redirect()->to(base_url('/'))->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        $distributorId = session()->get('id');

        // Ambil input pencarian dan filter dari URL
        $searchInvoiceId = $this->request->getVar('invoice_id');
        $searchOrderId = $this->request->getVar('order_id');
        $searchAgentName = $this->request->getVar('agent_name');
        $statusFilter = $this->request->getVar('status');
        $startDate = $this->request->getVar('start_date');
        $endDate = $this->request->getVar('end_date');

        $perPage = 10; // Jumlah item per halaman

        // Mulai membangun query pada objek model
        $builder = $this->invoiceModel
            ->select('invoices.*, orders.agen_id, orders.distributor_id, orders.order_date, orders.total_amount AS order_total_amount, users.username AS agent_username, users.id AS agen_user_id')
            ->join('orders', 'orders.id = invoices.order_id')
            ->join('users', 'users.id = orders.agen_id')
            ->where('orders.distributor_id', $distributorId); // Filter berdasarkan distributor yang login

        // Tambahkan kondisi pencarian/filter
        if (!empty($searchInvoiceId)) {
            $builder->like('invoices.id', $searchInvoiceId);
        }
        if (!empty($searchOrderId)) {
            $builder->where('orders.id', $searchOrderId);
        }
        if (!empty($searchAgentName)) {
            $builder->like('users.username', $searchAgentName);
        }
        if (!empty($statusFilter)) {
            if (strpos($statusFilter, ',') !== false) {
                $statuses = explode(',', $statusFilter);
                $builder->whereIn('invoices.status', $statuses);
            } else {
                $builder->where('invoices.status', $statusFilter);
            }
        }
        if (!empty($startDate)) {
            $builder->where('invoices.created_at >=', $startDate . ' 00:00:00');
        }
        if (!empty($endDate)) {
            $builder->where('invoices.created_at <=', $endDate . ' 23:59:59');
        }

        // Terapkan ordering
        $builder->orderBy('invoices.created_at', 'DESC');

        // Ambil data dengan pagination langsung dari model setelah semua kondisi diterapkan
        $invoices = $builder->paginate($perPage); // Panggil paginate() pada objek model/builder yang sudah ada
        $pager = $this->invoiceModel->pager; // Dapatkan objek pager dari model

        // Ambil daftar agen yang terhubung dengan distributor ini untuk dropdown filter
        $agents = $this->userModel
            ->where('role', 'agen')
            ->where('parent_id', $distributorId)
            ->findAll();

        $data = [
            'title'              => 'Daftar Invoice Agen',
            'pageTitle'          => 'Daftar Invoice Agen',
            'invoices'           => $invoices,
            'pager'              => $pager,
            'perPage'            => $perPage,
            'currentPage'        => $this->request->getVar('page') ?? 1,
            'searchInvoiceId'    => $searchInvoiceId,
            'searchOrderId'      => $searchOrderId,
            'searchAgentName'    => $searchAgentName,
            'statusFilter'       => $statusFilter,
            'startDate'          => $startDate,
            'endDate'            => $endDate,
            'allInvoiceStatuses' => ['unpaid', 'paid', 'cancelled'],
            'agents'             => $agents,
        ];

        return view('distributor/invoices/index', $data);
    }

    public function invoiceAgenDetail($invoiceId)
    {
        // Pastikan hanya user dengan role 'distributor' yang bisa mengakses
        if (session()->get('role') !== 'distributor') {
            return redirect()->to(base_url('/'))->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        $distributorId = session()->get('id');

        // 1. Ambil data invoice utama beserta data order dan data agen
        $invoice = $this->invoiceModel
            ->select('invoices.*, o.order_date, o.total_amount as order_total, u.username as agent_username, u.email as agent_email, u.no_telpon as agent_phone, u.alamat as agent_address')
            ->join('orders as o', 'o.id = invoices.order_id')
            ->join('users as u', 'u.id = o.agen_id')
            ->where('invoices.id', $invoiceId)
            ->where('o.distributor_id', $distributorId) // Verifikasi kepemilikan
            ->first();

        // Jika invoice tidak ditemukan atau bukan milik distributor ini, kembalikan
        if (!$invoice) {
            return redirect()->to(base_url('distributor/invoices-agen'))->with('error', 'Tagihan tidak ditemukan atau Anda tidak memiliki akses.');
        }

        // 2. Ambil data item-item yang ada di dalam order tersebut
        $orderItems = $this->orderItemModel
            ->where('order_id', $invoice['order_id'])
            ->findAll();

        // 3. Ambil data distributor (perusahaan Anda)
        $distributor = $this->userModel->find($distributorId);

        $data = [
            'title'       => 'Detail Tagihan Agen - #' . $invoice['id'],
            'invoice'     => $invoice,
            'orderItems'  => $orderItems,
            'distributor' => $distributor,
        ];

        return view('distributor/invoices/detail', $data);
    }

    public function markAsPaid($invoiceId = null)
    {
        // Pastikan hanya user dengan role 'distributor' yang bisa mengakses
        if (session()->get('role') !== 'distributor') {
            return redirect()->to(base_url('/'))->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        if ($this->request->isAJAX() && $invoiceId) {
            $distributorId = session()->get('id');

            // Verifikasi bahwa invoice ini memang milik agen yang terkait dengan distributor ini
            // Logika ini masih perlu di controller karena melibatkan verifikasi user yang sedang login
            // dan relasi kompleks antar model (invoice -> order -> agen -> distributor)
            $invoice = $this->invoiceModel->find($invoiceId);
            if (!$invoice) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Invoice not found.']);
            }

            $order = $this->orderModel->find($invoice['order_id']);
            if (!$order) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Order for this invoice not found.']);
            }

            // Dapatkan agen yang terkait dengan distributor yang login
            $agents = $this->userModel->getAgentsByDistributorId($distributorId);
            $agenIdsOfDistributor = array_column($agents, 'id');

            // Periksa apakah agen dari order ini adalah salah satu agen di bawah distributor yang sedang login
            if (!in_array($order['agen_id'], $agenIdsOfDistributor)) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized to update this invoice.']);
            }

            // Update status
            $updated = $this->invoiceModel->updateStatus($invoiceId, 'paid', date('Y-m-d H:i:s'));

            if ($updated) {
                return $this->response->setJSON(['status' => 'success', 'message' => 'Invoice marked as paid.']);
            } else {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to mark invoice as paid.']);
            }
        }
        return redirect()->back(); // Redirect back if not AJAX or invalid request
    }
}
