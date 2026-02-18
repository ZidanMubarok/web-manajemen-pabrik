<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\OrderModel;
use App\Models\InvoiceModel;
use App\Models\ProductModel;
use App\Models\OrderItemModel; // Perhatikan namespace dan nama model
use App\Models\UserProductModel; // Ini untuk harga kustom user (distributor/agen)

class AgenOrderController extends BaseController
{
    protected $userModel;
    protected $productModel;
    protected $userProductModel;
    protected $orderModel;
    protected $orderItemModel;
    protected $invoiceModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->productModel = new ProductModel();
        $this->userProductModel = new UserProductModel();
        $this->orderModel = new OrderModel();
        $this->orderItemModel = new OrderItemModel();
        $this->invoiceModel = new InvoiceModel();
        helper(['form', 'url']);
    }
// 
    public function create()
    {
        // Pastikan pengguna adalah agen
        if (session()->get('role') !== 'agen') {
            return redirect()->to(base_url('/login'))->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        $agenId = session()->get('id');
        $agenData = $this->userModel->find($agenId);
        $distributorId = $agenData['parent_id'] ?? null; // Mengambil ID distributor (parent) dari agen

        // Jika agen belum terhubung dengan distributor
        if (!$distributorId) {
            return redirect()->back()->with('error', 'Anda belum terhubung dengan Distributor. Tidak dapat membuat order.');
        }

        $perPage = 7;

        // Ambil semua produk dasar dari tabel products
        $products = $this->productModel->paginate($perPage);

        // Ambil harga kustom yang diatur oleh DISTRIBUTOR (user_id = distributorId)
        $distributorCustomPrices = $this->userProductModel->where('user_id', $distributorId)->findAll();
        $customPriceMap = [];
        foreach ($distributorCustomPrices as $cp) {
            $customPriceMap[$cp['product_id']] = $cp['custom_price'];
        }

        $availableProducts = [];
        foreach ($products as $product) {
            // Menambahkan 'product_id' yang sesuai dengan 'id' produk
            $product['product_id'] = $product['id'];

            // Menentukan harga yang akan ditampilkan: harga kustom DISTRIBUTOR jika ada, jika tidak pakai harga dasar produk
            $product['custom_price'] = $customPriceMap[$product['id']] ?? $product['base_price'];
            $availableProducts[] = $product;
        }

        $data = [
            'title'             => 'Buat Order Baru',
            'availableProducts' => $availableProducts,
            'distributorId'     => $distributorId,
            'pager'             => $this->productModel->pager,
        ];

        return view('agen/orders/create', $data);
    }

    public function store()
    {
        // Pastikan pengguna adalah agen
        if (session()->get('role') !== 'agen') {
            return redirect()->to(base_url('/login'))->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        $agenId = session()->get('id');
        $agenData = $this->userModel->find($agenId);
        $distributorId = $agenData['parent_id'] ?? null; // Mengambil ID distributor (parent) dari agen

        // Jika agen belum terhubung dengan distributor
        if (!$distributorId) {
            return redirect()->back()->with('error', 'Anda belum terhubung dengan Distributor, tidak dapat membuat order.');
        }

        // Aturan validasi untuk kuantitas produk
        $rules = [
            'product_quantities.*' => 'permit_empty|integer|greater_than_equal_to[0]',
        ];

        // Jalankan validasi
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $productQuantities = $this->request->getPost('product_quantities');
        $orderItemsData = [];
        $totalAmount = 0;

        // Ambil semua harga produk dasar terlebih dahulu untuk efisiensi
        $products = $this->productModel->findAll();
        $productPriceMap = [];
        foreach ($products as $p) {
            $productPriceMap[$p['id']] = $p['base_price'];
        }

        // Ambil harga kustom yang diatur oleh DISTRIBUTOR (user_id = distributorId)
        $distributorCustomPrices = $this->userProductModel->where('user_id', $distributorId)->findAll();
        $customPriceMap = [];
        foreach ($distributorCustomPrices as $cp) {
            $customPriceMap[$cp['product_id']] = $cp['custom_price'];
        }

        // Iterasi melalui kuantitas yang dimasukkan oleh pengguna
        foreach ($productQuantities as $productId => $quantity) {
            $quantity = (int) $quantity;
            if ($quantity <= 0) {
                continue; // Abaikan produk dengan kuantitas nol atau negatif
            }

            // Tentukan harga unit: 우선 harga kustom distributor, jika tidak ada, gunakan harga dasar produk
            $unitPrice = $customPriceMap[$productId] ?? $productPriceMap[$productId] ?? null;

            if ($unitPrice === null) {
                return redirect()->back()->withInput()->with('error', 'Harga untuk salah satu produk tidak ditemukan.');
            }


            $productInfo = $this->productModel->find($productId);
            $productName = $productInfo ? $productInfo['product_name'] : 'Nama Produk Tidak Ditemukan'; // Fallback jika produk tidak ada

            // Tambahkan data item order ke array
            $orderItemsData[] = [
                'product_id' => $productId,
                'quantity'   => $quantity,
                'unit_price' => $unitPrice,
                'product_name' => $productName,
                // HAPUS BARIS 'sub_total' DARI ARRAY INI:
                // 'sub_total'  => $subTotal,
            ];
            $totalAmount += ($unitPrice * $quantity); // total_amount untuk tabel orders tetap dihitung
        }

        // Jika tidak ada produk yang dipilih dengan kuantitas lebih dari 0
        if (empty($orderItemsData)) {
            return redirect()->back()->withInput()->with('error', 'Anda harus memilih setidaknya satu produk dengan kuantitas lebih dari 0.');
        }

        // Memulai transaksi database untuk memastikan integritas data
        $this->orderModel->transStart();
        try {
                // // Simpan Order Utama ke tabel orders
                $orderData = [
                    'agen_id'        => $agenId,
                    'distributor_id' => $distributorId, // Order dari agen selalu terhubung ke distributor
                    'status'         => 'pending', // Status awal order
                    'order_date'     => date('Y-m-d H:i:s'),
                    'total_amount'   => $totalAmount,
                    'pabrik_id'      => 1,
            ];

            $this->orderModel->insert($orderData);
            $orderId = $this->orderModel->getInsertID();

            // dd($orderId);
            // Penting: Pastikan $orderId valid sebelum melanjutkan
            if (!$orderId) {
                throw new \Exception('Gagal mendapatkan Order ID setelah insert. Insert ke tabel orders mungkin gagal.');
            }

            // Simpan Item Order ke tabel order_items
            foreach ($orderItemsData as $item) {
                $item['order_id'] = $orderId;
                $this->orderItemModel->insert($item);
            }

            // BUAT INVOICE OTOMATIS ke tabel invoices
            $invoiceData = [
                'order_id'      => $orderId,
                'invoice_date'  => date('Y-m-d H:i:s'),
                'amount_total'  => $totalAmount,
                'status'        => 'unpaid', // Status awal invoice
            ];
            $this->invoiceModel->insert($invoiceData);

            $this->orderModel->transComplete(); // Selesaikan transaksi

            // Periksa status transaksi
            if ($this->orderModel->transStatus() === false) {
                return redirect()->back()->with('error', 'Gagal membuat order dan tagihan. Terjadi kesalahan database.');
            }


        } catch (\Exception $e) {
            $this->orderModel->transRollback(); // Batalkan transaksi jika terjadi error
            log_message('error', 'Error creating order and invoice for agen ' . $agenId . ': ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat membuat order dan tagihan: ' . $e->getMessage());
            // Tampilkan error secara langsung di halaman untuk debugging yang jelas
            // die('DEBUG: Terjadi kesalahan saat membuat order dan tagihan: ' . $e->getMessage() . '<br>Stack Trace:<pre>' . $e->getTraceAsString() . '</pre>');
        }
        session()->setFlashdata([
            'swal_icon'  => 'success',
            'swal_title' => 'Berhasil',
            'swal_text'  => 'Anda berhasil membuat order ! '
        ]);
        return redirect()->to(base_url('agen/orders/history'))->with('success', 'Order Anda berhasil diajukan! Menunggu persetujuan Distributor.');
    }

    // // Menampilkan riwayat order agen
    // public function history()
    // {
    //     if (session()->get('role') !== 'agen') {
    //         return redirect()->to(base_url('/login'))->with('error', 'Anda tidak memiliki akses ke halaman ini.');
    //     }

    //     $agenId = session()->get('id');
    //     $agenData = $this->userModel->find($agenId);
    //     $distributorId = $agenData['parent_id'] ?? null;

    //     if (!$distributorId) {
    //         return redirect()->to(base_url('agen'))->with('error', 'Akun Anda belum terhubung ke Distributor. Tidak dapat melihat riwayat order.');
    //     }

    //     // Ambil input pencarian dan filter dari URL
    //     $search = $this->request->getVar('search');
    //     $statusFilter = $this->request->getVar('status');

    //     // Jumlah item per halaman
    //     $perPage = 10; // Anda bisa atur jumlah baris per halaman

    //     // Mulai membangun query
    //     $builder = $this->orderModel->where('agen_id', $agenId)
    //         ->where('distributor_id', $distributorId);

    //     // Tambahkan kondisi pencarian jika ada input search
    //     if ($search) {
    //         $builder->like('id', $search); // Mencari berdasarkan Order ID
    //     }
    //     // Tambahkan kondisi filter status jika ada input status
    //     if (!empty($statusFilter)) {
    //         if (is_array($statusFilter)) {
    //             $validStatuses = [];
    //             $allowedStatuses = [
    //                 'pending',
    //                 'approved',
    //                 'processing',
    //                 'shipped',
    //                 'completed',
    //                 'rejected'
    //             ];

    //             foreach ($statusFilter as $status) {
    //                 $cleanStatus = strtolower(trim($status));
    //                 if (in_array($cleanStatus, $allowedStatuses)) {
    //                     $validStatuses[] = $cleanStatus;
    //                 }
    //             }

    //             if (!empty($validStatuses)) {
    //                 $builder->whereIn('status', $validStatuses);
    //             }
    //         } else {
    //             $cleanStatus = strtolower(trim($statusFilter));
    //             $allowedStatuses = [
    //                 'pending',
    //                 'approved',
    //                 'processing',
    //                 'shipped',
    //                 'completed',
    //                 'rejected'
    //             ];
    //             if (in_array($cleanStatus, $allowedStatuses)) {
    //                 $builder->where('status', $cleanStatus);
    //             }
    //         }
    //     }

    //     // Ambil data dengan pagination
    //     // paginate() akan otomatis menerapkan OFFSET dan LIMIT
    //     $orders = $builder->orderBy('order_date', 'DESC')->paginate($perPage);
    //     $pager = $this->orderModel->pager; // Dapatkan objek pager

    //     // Kirimkan data ke view
    //     $data = [
    //         'title'        => 'Riwayat Order Anda',
    //         'orders'       => $orders,
    //         'pager'        => $pager, // Kirim objek pager ke view
    //         'search'       => $search,
    //         'statusFilter' => $statusFilter,
    //         'allStatuses'  => ['pending', 'approved', 'processing', 'shipped', 'completed', 'rejected'],
    //         'perPage'      => $perPage, // Kirimkan juga perPage untuk penomoran
    //         'currentPage'  => $this->request->getVar('page') ?? 1, // Untuk penomoran urut
    //     ];

    //     return view('agen/orders/history', $data);
    // }

    // Menampilkan riwayat order agen
    public function history()
    {
        if (session()->get('role') !== 'agen') {
            return redirect()->to(base_url('/login'))->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        $agenId = session()->get('id');
        $agenData = $this->userModel->find($agenId);
        $distributorId = $agenData['parent_id'] ?? null;

        if (!$distributorId) {
            return redirect()->to(base_url('agen'))->with('error', 'Akun Anda belum terhubung ke Distributor. Tidak dapat melihat riwayat order.');
        }

        // Ambil input pencarian dan filter dari URL
        $search = $this->request->getVar('search');
        $statusFilter = $this->request->getVar('status');
        // Ambil input tanggal
        $startDate = $this->request->getVar('start_date');
        $endDate = $this->request->getVar('end_date');

        // Jumlah item per halaman
        $perPage = 10;

        // Mulai membangun query
        $builder = $this->orderModel->where('agen_id', $agenId)
            ->where('distributor_id', $distributorId);

        // Tambahkan kondisi pencarian jika ada input search
        if ($search) {
            $builder->like('id', $search); // Mencari berdasarkan Order ID
        }

        // Tambahkan kondisi filter status jika ada
        if (!empty($statusFilter)) {
            // ... (logika filter status Anda yang sudah ada tidak perlu diubah)
            if (is_array($statusFilter)) {
                $validStatuses = [];
                $allowedStatuses = ['pending', 'approved', 'processing', 'shipped', 'completed', 'rejected'];
                foreach ($statusFilter as $status) {
                    $cleanStatus = strtolower(trim($status));
                    if (in_array($cleanStatus, $allowedStatuses)) {
                        $validStatuses[] = $cleanStatus;
                    }
                }
                if (!empty($validStatuses)) {
                    $builder->whereIn('status', $validStatuses);
                }
            } else {
                $cleanStatus = strtolower(trim($statusFilter));
                $allowedStatuses = ['pending', 'approved', 'processing', 'shipped', 'completed', 'rejected'];
                if (in_array($cleanStatus, $allowedStatuses)) {
                    $builder->where('status', $cleanStatus);
                }
            }
        }

        // Tambahkan kondisi filter berdasarkan rentang tanggal
        if (!empty($startDate) && !empty($endDate)) {
            // Pastikan format tanggal valid sebelum digunakan dalam query
            $builder->where('order_date >=', $startDate . ' 00:00:00');
            $builder->where('order_date <=', $endDate . ' 23:59:59');
        } elseif (!empty($startDate)) {
            $builder->where('order_date >=', $startDate . ' 00:00:00');
        } elseif (!empty($endDate)) {
            $builder->where('order_date <=', $endDate . ' 23:59:59');
        }


        // Ambil data dengan pagination
        $orders = $builder->orderBy('order_date', 'DESC')->paginate($perPage);
        $pager = $this->orderModel->pager;

        // Kirimkan data ke view
        $data = [
            'title'        => 'Riwayat Order Anda',
            'orders'       => $orders,
            'pager'        => $pager,
            'search'       => $search,
            'statusFilter' => $statusFilter,
            // Kirim data tanggal ke view
            'startDate'    => $startDate,
            'endDate'      => $endDate,
            'allStatuses'  => ['pending', 'approved', 'processing', 'shipped', 'completed', 'rejected'],
            'perPage'      => $perPage,
            'currentPage'  => $this->request->getVar('page') ?? 1,
        ];

        return view('agen/orders/history', $data);
    }
    // Menampilkan detail order
    public function detail($orderId = null)
    {
        if (session()->get('role') !== 'agen') {
            return redirect()->to(base_url('/login'))->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        $agenId = session()->get('id');
        $agenData = $this->userModel->find($agenId);
        $distributorId = $agenData['parent_id'] ?? null;

        if (!$distributorId) {
            return redirect()->to(base_url('agen'))->with('error', 'Akun Anda belum terhubung ke Distributor. Tidak dapat melihat detail order.');
        }

        // Ambil detail order, pastikan agen hanya bisa melihat ordernya sendiri dan yang terhubung dengan distributornya
        $order = $this->orderModel
            ->where('id', $orderId)
            ->where('agen_id', $agenId)
            ->where('distributor_id', $distributorId)
            ->first();

        if (!$order) {
            return redirect()->to(base_url('agen/orders/history'))->with('error', 'Order tidak ditemukan atau Anda tidak memiliki akses ke order ini.');
        }

        // Ambil item-item yang terkait dengan order ini
        $orderItems = $this->orderItemModel->where('order_id', $order['id'])->findAll();

        // Ambil nama produk untuk setiap item order
        $productNames = [];
        foreach ($orderItems as $item) {
            $product = $this->productModel->find($item['product_id']);
            $productNames[$item['product_id']] = $product ? $product['product_name'] : 'Produk Tidak Ditemukan';
        }

        // Ambil informasi distributor
        $distributor = $this->userModel->find($order['distributor_id']);

        $data = [
            'title'        => 'Detail Order #ORD-' . $order['id'],
            'order'        => $order,
            'orderItems'   => $orderItems,
            'productNames' => $productNames,
            'distributor'  => $distributor,
        ];

        return view('agen/orders/detail', $data); // Memuat view detail order
    }
}
