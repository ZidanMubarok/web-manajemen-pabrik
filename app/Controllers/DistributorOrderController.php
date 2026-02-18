<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\OrderModel;
use App\Models\InvoiceModel;
use App\Models\ProductModel;
use App\Models\OrderItemModel;
use App\Models\DistributorInvoiceModel;

class DistributorOrderController extends BaseController
{
    protected $userModel;
    protected $orderModel;
    protected $orderItemModel;
    protected $productModel;
    protected $invoiceModel;
    protected $distributorInvoiceModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->orderModel = new OrderModel();
        $this->orderItemModel = new OrderItemModel();
        $this->productModel = new ProductModel();
        $this->invoiceModel = new InvoiceModel();
        $this->distributorInvoiceModel = new DistributorInvoiceModel();
        helper(['form', 'url']);
    }

    // Menampilkan daftar order masuk dari agen (status 'pending')
    // public function incomingOrders()
    // {
    //     // Pastikan hanya distributor yang bisa mengakses
    //     if (session()->get('role') !== 'distributor') {
    //         return redirect()->to(base_url('/login'))->with('error', 'Anda tidak memiliki akses ke halaman ini.');
    //     }

    //     $distributorId = session()->get('id');

    //     // Ambil order dengan status 'pending' yang ditujukan ke distributor ini
    //     // Lakukan JOIN dengan tabel 'users' untuk mendapatkan data agen (username, alamat, no_telpon)
    //     $incomingOrders = $this->orderModel
    //         ->select('orders.*, users.username as agen_username, users.alamat as agen_alamat, users.no_telpon as agen_no_telpon')
    //         ->join('users', 'users.id = orders.agen_id', 'left') // Gunakan LEFT JOIN
    //         ->where('orders.distributor_id', $distributorId)
    //         ->where('orders.status', 'pending')
    //         ->orderBy('orders.order_date', 'ASC')
    //         ->findAll();

    //     $data = [
    //         'title'          => 'Order Masuk dari Agen',
    //         'incomingOrders' => $incomingOrders,
    //         // Variabel $agenUsernames dan $agen tidak lagi diperlukan karena data sudah di-join
    //     ];

    //     return view('distributor/orders/incoming_orders', $data);
    // }
    public function incomingOrders()
    {
        // Pastikan hanya distributor yang bisa mengakses
        if (session()->get('role') !== 'distributor') {
            return redirect()->to(base_url('/login'))->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        $distributorId = session()->get('id');

        // Mengambil parameter dari URL untuk filter tanggal dan pagination
        $startDate = $this->request->getVar('start_date');
        $endDate = $this->request->getVar('end_date');
        $perPage = 10; // Jumlah item per halaman, bisa disesuaikan atau diambil dari config

        $builder = $this->orderModel
            ->select('orders.*, users.username as agen_username, users.alamat as agen_alamat, users.no_telpon as agen_no_telpon')
            ->join('users', 'users.id = orders.agen_id', 'left')
            ->where('orders.distributor_id', $distributorId)
            ->where('orders.status', 'pending');

        // Tambahkan filter tanggal jika ada
        if (!empty($startDate)) {
            $builder->where('orders.order_date >=', $startDate . ' 00:00:00'); // Mulai dari awal hari
        }
        if (!empty($endDate)) {
            $builder->where('orders.order_date <=', $endDate . ' 23:59:59'); // Sampai akhir hari
        }

        // Urutkan dari yang terlama
        $builder->orderBy('orders.order_date', 'ASC');

        // Dapatkan data dengan pagination
        $incomingOrders = $builder->paginate($perPage);
        $pager = $this->orderModel->pager;

        $data = [
            'title'          => 'Order Masuk dari Agen',
            'incomingOrders' => $incomingOrders,
            'pager'          => $pager,
            'startDate'      => $startDate, // Kirim kembali nilai filter ke view
            'endDate'        => $endDate,   // Kirim kembali nilai filter ke view
        ];

        return view('distributor/orders/incoming_orders', $data);
    }

    public function updateStatus($orderId = null)
    {
        if (session()->get('role') !== 'distributor') {
            return redirect()->to(base_url('/login'))->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        $distributorId = session()->get('id');

        $order = $this->orderModel
            ->where('id', $orderId)
            ->where('distributor_id', $distributorId)
            ->first();

        if (!$order) {
            return redirect()->to(base_url('distributor/orders/incoming'))->with('error', 'Order tidak ditemukan atau Anda tidak memiliki akses.');
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

        $this->orderModel->transStart(); // Mulai transaksi database
        try {
            $dataToUpdate = [
                'status' => $newStatus,
                'notes'  => $notes,
            ];

            $this->orderModel->update($orderId, $dataToUpdate);

            // --- BAGIAN BARU: LOGIKA PEMBUATAN TAGIHAN DISTRIBUTOR KE PABRIK ---
            if ($newStatus === 'approved') {
                // 1. Ambil semua item pesanan beserta detail produk (base_price)
                $orderItemsWithProductDetails = $this->orderItemModel->getItemsWithProductDetails($orderId);

                $factoryTotalAmount = 0;
                if (!empty($orderItemsWithProductDetails)) {
                    foreach ($orderItemsWithProductDetails as $item) {
                        // Pastikan 'base_price' adalah harga produk dari pabrik
                        $factoryTotalAmount += $item['quantity'] * $item['base_price'];
                    }
                } else {
                    // Log atau berikan pesan jika tidak ada item pesanan
                    log_message('warning', 'Order ID: ' . $orderId . ' tidak memiliki item pesanan untuk membuat tagihan pabrik.');
                    // Anda bisa memutuskan apakah ingin membatalkan transaksi atau tetap melanjutkan
                    // Jika Anda ingin membatalkan, tambahkan throw new \Exception("No order items found.");
                }

                // 2. Buat data untuk tagihan distributor baru
                $distributorInvoiceData = [
                    'order_id'       => $order['id'],
                    'invoice_date'   => date('Y-m-d H:i:s'), // Tanggal saat ini
                    'due_date'       => date('Y-m-d H:i:s', strtotime('+30 days')), // Contoh: Jatuh tempo 30 hari dari sekarang
                    'total_amount'   => $factoryTotalAmount,
                    'status'         => 'unpaid', // Status awal: belum dibayar
                    'invoice_number' => 'FI-' . $order['id'] . '-' . time(), // Contoh: Factory Invoice - Order ID - Timestamp
                    'notes'          => 'Tagihan dari pabrik untuk pesanan #' . $order['id'] . ' yang disetujui distributor.',
                ];

                // 3. Simpan data tagihan distributor ke database
                $insertDistributorInvoiceResult = $this->distributorInvoiceModel->insert($distributorInvoiceData);

                if (!$insertDistributorInvoiceResult) {
                    // Jika gagal insert tagihan distributor, rollback transaksi
                    throw new \Exception('Gagal membuat tagihan pabrik untuk distributor.');
                }
            }
            // --- AKHIR BAGIAN BARU ---

            // --- BAGIAN LAMA: PERBARUI STATUS INVOICE AGEN ---
            // Ini adalah invoice agen, yang sudah ada di kode Anda
            $invoice = $this->invoiceModel->where('order_id', $orderId)->first();
            if ($invoice) {
                if ($newStatus === 'rejected') {
                    $this->invoiceModel->update($invoice['id'], ['status' => 'cancelled']); // Jika ditolak, batalkan invoice agen
                }
                // Jika approved, status invoice agen tetap 'unpaid' (sesuai logika Anda)
            } else {
                log_message('warning', 'Invoice agen tidak ditemukan untuk order ID: ' . $orderId . ' selama update status.');
            }
            // --- AKHIR BAGIAN LAMA ---

            $this->orderModel->transComplete(); // Selesaikan transaksi

            if ($this->orderModel->transStatus() === false) {
                // Ini akan menangkap error jika transComplete() gagal (misalnya, ada error SQL)
                return redirect()->back()->with('error', 'Gagal memperbarui status order dan tagihan. Silakan coba lagi.');
            }
        } catch (\Exception $e) {
            $this->orderModel->transRollback(); // Rollback transaksi jika ada exception
            log_message('error', 'Error updating order status and invoices for order ID ' . $orderId . ': ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memperbarui status order: ' . $e->getMessage());
        }

        $message = 'Order #ORD-' . $orderId . ' berhasil ' . ($newStatus === 'approved' ? 'disetujui.' : 'ditolak.');
        return redirect()->to(base_url('distributor/orders/incoming'))->with('success', $message);
    }

    // Menampilkan detail order
    public function detail($orderId = null)
    {
        if (session()->get('role') !== 'distributor') {
            return redirect()->to(base_url('/login'))->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        $distributorId = session()->get('id');

        $order = $this->orderModel
            ->where('id', $orderId)
            ->where('distributor_id', $distributorId) // Pastikan distributor hanya bisa melihat order untuknya
            ->first();

        if (!$order) {
            return redirect()->to(base_url('distributor/orders/incoming'))->with('error', 'Order tidak ditemukan atau Anda tidak memiliki akses ke order ini.');
        }

        $orderItems = $this->orderItemModel->where('order_id', $order['id'])->findAll();

        // Ambil nama produk untuk setiap item
        $productNames = [];
        foreach ($orderItems as $item) {
            $product = $this->productModel->find($item['product_id']);
            $productNames[$item['product_id']] = $product ? $product['product_name'] : 'Produk Tidak Ditemukan';
        }

        // Ambil informasi agen yang mengajukan order
        $agen = $this->userModel->find($order['agen_id']);

        $data = [
            'title'        => 'Detail Order #ORD-' . $order['id'],
            'order'        => $order,
            'orderItems'   => $orderItems,
            'productNames' => $productNames,
            'agen'         => $agen,
        ];

        return view('distributor/orders/detail', $data);
    }

    // public function updateStatus($orderId = null)
    // {
    //     if (session()->get('role') !== 'distributor') {
    //         return redirect()->to(base_url('/login'))->with('error', 'Anda tidak memiliki akses ke halaman ini.');
    //     }

    //     $distributorId = session()->get('id');

    //     $order = $this->orderModel
    //         ->where('id', $orderId)
    //         ->where('distributor_id', $distributorId)
    //         ->first();

    //     if (!$order) {
    //         return redirect()->to(base_url('distributor/orders/incoming'))->with('error', 'Order tidak ditemukan atau Anda tidak memiliki akses.');
    //     }

    //     if ($order['status'] !== 'pending') {
    //         return redirect()->back()->with('error', 'Order ini sudah tidak dalam status "pending" dan tidak bisa diubah.');
    //     }

    //     $newStatus = $this->request->getPost('status');
    //     $notes = $this->request->getPost('notes');

    //     $rules = [
    //         'status' => 'required|in_list[approved,rejected]',
    //     ];

    //     if (!$this->validate($rules)) {
    //         return redirect()->back()->with('errors', $this->validator->getErrors());
    //     }

    //     $this->orderModel->transStart();
    //     try {
    //         $dataToUpdate = [
    //             'status' => $newStatus,
    //             'notes'  => $notes,
    //         ];

    //         $this->orderModel->update($orderId, $dataToUpdate);

    //         // --- BAGIAN BARU: PERBARUI STATUS INVOICE ---
    //         $invoice = $this->invoiceModel->where('order_id', $orderId)->first();
    //         if ($invoice) {
    //             if ($newStatus === 'rejected') {
    //                 $this->invoiceModel->update($invoice['id'], ['status' => 'cancelled']); // Jika ditolak, batalkan invoice
    //             }
    //             // Jika approved, status invoice tetap 'unpaid'
    //         } else {
    //             // Ini seharusnya tidak terjadi jika invoice dibuat saat order dibuat
    //             log_message('warning', 'Invoice not found for order ID: ' . $orderId . ' during status update.');
    //         }
    //         // --- AKHIR BAGIAN BARU ---

    //         $this->orderModel->transComplete();

    //         if ($this->orderModel->transStatus() === false) {
    //             return redirect()->back()->with('error', 'Gagal memperbarui status order dan tagihan. Silakan coba lagi.');
    //         }
    //     } catch (\Exception $e) {
    //         $this->orderModel->transRollback();
    //         log_message('error', 'Error updating order status and invoice for order ID ' . $orderId . ': ' . $e->getMessage());
    //         return redirect()->back()->with('error', 'Terjadi kesalahan saat memperbarui status order: ' . $e->getMessage());
    //     }

    //     $message = 'Order #ORD-' . $orderId . ' berhasil ' . ($newStatus === 'approved' ? 'disetujui.' : 'ditolak.');
    //     return redirect()->to(base_url('distributor/orders/incoming'))->with('success', $message);
    // }

    // public function history()
    // {
    //     if (session()->get('role') !== 'distributor') {
    //         return redirect()->to(base_url('/login'))->with('error', 'Anda tidak memiliki akses ke halaman ini.');
    //     }

    //     $distributorId = session()->get('id');

    //     // Ambil input pencarian dan filter dari URL
    //     $searchOrderId = $this->request->getVar('order_id');
    //     $searchAgentName = $this->request->getVar('agent_name');
    //     $statusFilter = $this->request->getVar('status');
    //     $startDate = $this->request->getVar('start_date');
    //     $endDate = $this->request->getVar('end_date');

    //     // Panggil model dengan parameter filter
    //     $orders = $this->orderModel->getHistoryOrders(
    //         $distributorId,
    //         $searchOrderId,
    //         $searchAgentName,
    //         $statusFilter,
    //         $startDate,
    //         $endDate
    //     );

    //     // Ambil daftar agen yang terhubung dengan distributor ini untuk dropdown filter
    //     $agents = $this->userModel
    //         ->where('role', 'agen')
    //         ->where('parent_id', $distributorId) // Asumsi ada kolom parent_id di tabel users
    //         ->findAll();

    //     // Daftar semua status order yang mungkin (selain 'pending' yang sudah difilter di model)
    //     $allOrderStatuses = ['approved', 'processing', 'shipped', 'completed', 'rejected'];

    //     $data = [
    //         'title'             => 'Riwayat Order',
    //         'orders'            => $orders,
    //         'searchOrderId'     => $searchOrderId,
    //         'searchAgentName'   => $searchAgentName,
    //         'statusFilter'      => $statusFilter,
    //         'startDate'         => $startDate,
    //         // 'pager'              => $pager,
    //         // 'perPage'            => $perPage,
    //         'endDate'           => $endDate,
    //         'agents'            => $agents, // Kirim daftar agen ke view
    //         'allOrderStatuses'  => $allOrderStatuses, // Kirim daftar status ke view
    //     ];

    //     return view('distributor/orders/history', $data);
    // }
    public function history()
    {
        if (session()->get('role') !== 'distributor') {
            return redirect()->to(base_url('/login'))->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        $distributorId = session()->get('id');

        // Ambil input pencarian dan filter dari URL
        $searchOrderId = $this->request->getVar('order_id');
        $searchAgentName = $this->request->getVar('agent_name');
        $statusFilter = $this->request->getVar('status');
        $startDate = $this->request->getVar('start_date');
        $endDate = $this->request->getVar('end_date');

        // Jumlah item per halaman
        $perPage = 10; // Anda bisa atur jumlah baris per halaman

        // Panggil metode dari model yang mengembalikan Query Builder
        $ordersBuilder = $this->orderModel->getHistoryOrders(
            $distributorId,
            $searchOrderId,
            $searchAgentName,
            $statusFilter,
            $startDate,
            $endDate
        );

        // Ambil data dengan pagination
        $orders = $ordersBuilder->paginate($perPage); // Panggil paginate() pada objek builder
        $pager = $this->orderModel->pager; // Dapatkan objek pager dari model

        // Ambil daftar agen yang terhubung dengan distributor ini untuk dropdown filter
        $agents = $this->userModel
            ->where('role', 'agen')
            ->where('parent_id', $distributorId) // Asumsi ada kolom parent_id di tabel users
            ->findAll();

        // Daftar semua status order yang mungkin (selain 'pending' yang sudah difilter di model)
        $allOrderStatuses = ['completed', 'rejected'];

        $data = [
            'title'             => 'Riwayat Order',
            'orders'            => $orders,
            'pager'             => $pager, // Kirim objek pager ke view
            'perPage'           => $perPage, // Kirimkan juga perPage untuk penomoran
            'currentPage'       => $this->request->getVar('page') ?? 1, // Untuk penomoran urut
            'searchOrderId'     => $searchOrderId,
            'searchAgentName'   => $searchAgentName,
            'statusFilter'      => $statusFilter,
            'startDate'         => $startDate,
            'endDate'           => $endDate,
            'agents'            => $agents,
            'allOrderStatuses'  => $allOrderStatuses,
        ];

        return view('distributor/orders/history', $data);
    }

    public function history_detail($orderId = null)
    {
        if (session()->get('role') !== 'distributor') {
            return redirect()->to(base_url('/login'))->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        $distributorId = session()->get('id');

        $order = $this->orderModel
            ->where('id', $orderId)
            ->where('distributor_id', $distributorId) // Pastikan distributor hanya bisa melihat order untuknya
            ->first();

        if (!$order) {
            return redirect()->to(base_url('distributor/orders/incoming'))->with('error', 'Order tidak ditemukan atau Anda tidak memiliki akses ke order ini.');
        }

        $orderItems = $this->orderItemModel->where('order_id', $order['id'])->findAll();

        // Ambil nama produk untuk setiap item
        $productNames = [];
        foreach ($orderItems as $item) {
            $product = $this->productModel->find($item['product_id']);
            $productNames[$item['product_id']] = $product ? $product['product_name'] : 'Produk Tidak Ditemukan';
        }

        // Ambil informasi agen yang mengajukan order
        $agen = $this->userModel->find($order['agen_id']);

        $data = [
            'title'        => 'Detail Order #ORD-' . $order['id'],
            'order'        => $order,
            'orderItems'   => $orderItems,
            'productNames' => $productNames,
            'agen'         => $agen,
        ];

        return view('distributor/orders/detail_history', $data);
    }

    // Menampilkan riwayat order distributor ke pabrik
    // public function historyOrderToPabrik()
    // {
    //     if (session()->get('role') !== 'distributor') {
    //         return redirect()->to(base_url('/login'))->with('error', 'Anda tidak memiliki akses ke halaman ini.');
    //     }

    //     $distributorId = session()->get('id');

    //     // Ambil ID Pabrik
    //     $pabrik = $this->userModel->where('role', 'pabrik')->first();
    //     $pabrikId = $pabrik['id'] ?? null;

    //     $perPage = 7;
    //     if (!$pabrikId) {
    //         return redirect()->to(base_url('distributor'))->with('error', 'Data Pabrik tidak ditemukan. Tidak dapat melihat riwayat order ke Pabrik.');
    //     }


    //     $orders = $this->orderModel
    //         ->where('distributor_id', $distributorId)
    //         ->where('pabrik_id', $pabrikId) // Filter order yang ditujukan ke pabrik
    //         ->whereIn('status', ['approved', 'processing', 'shipped']) // Exclude 'pending'
    //         ->orderBy('order_date', 'ASC')
    //         ->paginate($perPage);

    //     $pager = $this->orderModel->pager; // Dapatkan objek pager dari model
    //     $data = [
    //         'title'  => 'Riwayat Order Anda ke Pabrik',
    //         'orders'    => $orders,
    //         'pager'    => $pager, // Kirim objek pager ke view
    //         'perPage'  => $perPage,
    //     ];

    //     return view('distributor/orders/history_to_pabrik', $data);
    // }

    public function historyOrderToPabrik()
    {
        if (session()->get('role') !== 'distributor') {
            return redirect()->to(base_url('/login'))->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        $distributorId = session()->get('id');
        $pabrik = $this->userModel->where('role', 'pabrik')->first();
        $pabrikId = $pabrik['id'] ?? null;

        if (!$pabrikId) {
            return redirect()->to(base_url('distributor'))->with('error', 'Data Pabrik tidak ditemukan.');
        }

        // --- AWAL MODIFIKASI ---

        // 1. Ambil data filter dari request GET
        $filters = [
            'order_id'   => $this->request->getGet('order_id'),
            'start_date' => $this->request->getGet('start_date'),
            'end_date'   => $this->request->getGet('end_date'),
            'status'     => $this->request->getGet('status')
        ];

        // 2. Bangun query dasar
        $query = $this->orderModel
            ->where('distributor_id', $distributorId)
            ->where('pabrik_id', $pabrikId);

        // 3. Terapkan filter secara dinamis
        if (!empty($filters['order_id'])) {
            // Menghilangkan prefix "ORD-" jika ada untuk pencarian berdasarkan ID
            $orderId = ltrim($filters['order_id'], 'ORD-');
            $query->where('id', $orderId);
        }

        if (!empty($filters['start_date'])) {
            $query->where('order_date >=', $filters['start_date'] . ' 00:00:00');
        }

        if (!empty($filters['end_date'])) {
            $query->where('order_date <=', $filters['end_date'] . ' 23:59:59');
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        } else {
            // Jika tidak ada filter status, gunakan status default
            $query->whereIn('status', ['approved', 'processing', 'shipped']);
        }

        // 4. Lakukan paginasi setelah filter diterapkan
        $perPage = 7;
        $orders = $query->orderBy('order_date', 'DESC')->paginate($perPage, 'default');

        // 5. Dapatkan pager dan siapkan data untuk view
        $pager = $this->orderModel->pager;

        $data = [
            'title'     => 'Order Yang Aktif',
            'orders'    => $orders,
            'pager'     => $pager,
            'perPage'   => $perPage,
            'filters'   => $filters, // Kirim nilai filter kembali ke view
            'pabrikName' => $pabrik['username'] ?? 'N/A' // contoh mengambil nama pabrik
        ];

        // --- AKHIR MODIFIKASI ---

        return view('distributor/orders/history_to_pabrik', $data);
    }

    // Menampilkan detail order distributor ke pabrik
    public function detailOrderToPabrik($orderId = null)
    {
        if (session()->get('role') !== 'distributor') {
            return redirect()->to(base_url('/login'))->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        $distributorId = session()->get('id');

        // Ambil ID Pabrik
        $pabrik = $this->userModel->where('role', 'pabrik')->first();
        $pabrikId = $pabrik['id'] ?? null;

        if (!$pabrikId) {
            return redirect()->to(base_url('distributor'))->with('error', 'Data Pabrik tidak ditemukan. Tidak dapat melihat detail order.');
        }

        $order = $this->orderModel
            ->where('id', $orderId)
            ->where('distributor_id', $distributorId) // Pastikan distributor hanya bisa melihat ordernya sendiri
            ->where('pabrik_id', $pabrikId) // Dan order tersebut untuk pabrik
            ->first();

        if (!$order) {
            return redirect()->to(base_url('distributor/orders/history-to-pabrik'))->with('error', 'Order tidak ditemukan atau Anda tidak memiliki akses ke order ini.');
        }

        $orderItems = $this->orderItemModel->where('order_id', $order['id'])->findAll();

        // Ambil nama produk untuk setiap item
        $productNames = [];
        foreach ($orderItems as $item) {
            $product = $this->productModel->find($item['product_id']);
            $productNames[$item['product_id']] = $product ? $product['product_name'] : 'Produk Tidak Ditemukan';
        }

        // Ambil info pabrik
        $pabrikData = $this->userModel->find($order['pabrik_id']);
        $agenData = $this->userModel->find($order['agen_id']);

        $data = [
            'title'        => 'Detail Order ke Pabrik #ORD-' . $order['id'],
            'order'        => $order,
            'orderItems'   => $orderItems,
            'productNames' => $productNames,
            'pabrikData'   => $pabrikData,
            'agenData'   => $agenData,
        ];

        return view('distributor/orders/detail_to_pabrik', $data);
    }

    // // Sepertinya function function di bawah sudah tidak diperlukan lagi
    // public function createOrderToPabrik()
    // {
    //     if (session()->get('role') !== 'distributor') {
    //         return redirect()->to(base_url('/login'))->with('error', 'Anda tidak memiliki akses ke halaman ini.');
    //     }

    //     $distributorId = session()->get('id');

    //     // Ambil ID Pabrik (asumsi ada satu user dengan role 'pabrik')
    //     $pabrik = $this->userModel->where('role', 'pabrik')->first();
    //     $pabrikId = $pabrik['id'] ?? null;

    //     if (!$pabrikId) {
    //         return redirect()->to(base_url('distributor'))->with('error', 'Data Pabrik tidak ditemukan. Tidak dapat membuat order.');
    //     }

    //     // Ambil semua produk dari pabrik (menggunakan base_price)
    //     $availableProducts = $this->productModel->findAll();

    //     $data = [
    //         'title'             => 'Buat Order ke Pabrik',
    //         'availableProducts' => $availableProducts,
    //         'pabrikId'          => $pabrikId,
    //         'distributorId'     => $distributorId,
    //     ];

    //     return view('distributor/orders/create_to_pabrik', $data);
    // }

    // // Memproses pengajuan order baru ke Pabrik
    // public function storeOrderToPabrik()
    // {
    //     if (session()->get('role') !== 'distributor') {
    //         return redirect()->to(base_url('/login'))->with('error', 'Anda tidak memiliki akses ke halaman ini.');
    //     }

    //     $distributorId = session()->get('id');

    //     $pabrik = $this->userModel->where('role', 'pabrik')->first();
    //     $pabrikId = $pabrik['id'] ?? null;

    //     if (!$pabrikId) {
    //         return redirect()->to(base_url('distributor'))->with('error', 'Data Pabrik tidak ditemukan. Order gagal dibuat.');
    //     }

    //     $rules = [
    //         'product_quantities' => 'required|array',
    //         'product_quantities.*' => 'permit_empty|integer|greater_than_equal_to[0]',
    //     ];

    //     if (!$this->validate($rules)) {
    //         return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
    //     }

    //     $productQuantities = $this->request->getPost('product_quantities');
    //     $orderItemsData = [];
    //     $totalAmount = 0;

    //     foreach ($productQuantities as $productId => $quantity) {
    //         $quantity = (int) $quantity;
    //         if ($quantity <= 0) {
    //             continue;
    //         }

    //         $product = $this->productModel->find($productId);

    //         if (!$product || !isset($product['base_price'])) {
    //             return redirect()->back()->withInput()->with('error', 'Harga untuk salah satu produk tidak ditemukan.');
    //         }

    //         $unitPrice = $product['base_price'];
    //         $subTotal = $unitPrice * $quantity;

    //         $orderItemsData[] = [
    //             'product_id' => $productId,
    //             'quantity'   => $quantity,
    //             'unit_price' => $unitPrice,
    //             'sub_total'  => $subTotal,
    //         ];
    //         $totalAmount += $subTotal;
    //     }

    //     if (empty($orderItemsData)) {
    //         return redirect()->back()->withInput()->with('error', 'Anda harus memilih setidaknya satu produk dengan kuantitas lebih dari 0.');
    //     }

    //     $this->orderModel->transStart();
    //     try {
    //         // Simpan Order Utama
    //         $orderData = [
    //             'distributor_id' => $distributorId,
    //             'pabrik_id'      => $pabrikId,
    //             'order_date'     => date('Y-m-d H:i:s'),
    //             'total_amount'   => $totalAmount,
    //             'status'         => 'pending',
    //         ];

    //         $this->orderModel->insert($orderData);
    //         $orderId = $this->orderModel->getInsertID();

    //         // Simpan Item Order
    //         foreach ($orderItemsData as $item) {
    //             $item['order_id'] = $orderId;
    //             $this->orderItemModel->insert($item);
    //         }

    //         // --- BAGIAN BARU: BUAT INVOICE OTOMATIS UNTUK ORDER KE PABRIK ---
    //         $invoiceData = [
    //             'order_id'      => $orderId,
    //             'invoice_date'  => date('Y-m-d H:i:s'),
    //             'amount_total'  => $totalAmount,
    //             'status'        => 'unpaid', // Status awal invoice adalah 'unpaid'
    //         ];
    //         $this->invoiceModel->insert($invoiceData);
    //         // --- AKHIR BAGIAN BARU ---

    //         $this->orderModel->transComplete();

    //         if ($this->orderModel->transStatus() === false) {
    //             return redirect()->back()->with('error', 'Gagal membuat order dan tagihan ke pabrik. Terjadi kesalahan database.');
    //         }
    //     } catch (\Exception $e) {
    //         $this->orderModel->transRollback();
    //         log_message('error', 'Error creating order and invoice to pabrik for distributor ' . $distributorId . ': ' . $e->getMessage());
    //         return redirect()->back()->with('error', 'Terjadi kesalahan saat membuat order dan tagihan ke pabrik: ' . $e->getMessage());
    //     }

    //     return redirect()->to(base_url('distributor/orders/history-to-pabrik'))->with('success', 'Order Anda ke Pabrik berhasil diajukan! Tagihan pembayaran otomatis dibuat. Menunggu validasi.');
    // }
}
