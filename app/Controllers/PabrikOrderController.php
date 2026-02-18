<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\OrderModel;
use App\Models\InvoiceModel;
use App\Models\ProductModel;
use App\Models\OrderItemModel;
use App\Models\DistributorInvoiceModel;
use App\Models\ShipmentModel; // Diperlukan untuk mengubah status dan membuat shipment

class PabrikOrderController extends BaseController
{
    protected $orderModel;
    protected $orderItemModel;
    protected $userModel;
    protected $productModel;
    protected $shipmentModel;
    protected $invoiceModel;
    protected $distributorInvoiceModel;
    protected $db;

    public function __construct()
    {
        $this->orderModel = new OrderModel();
        $this->orderItemModel = new OrderItemModel();
        $this->userModel = new UserModel();
        $this->productModel = new ProductModel();
        $this->shipmentModel = new ShipmentModel(); // Inisialisasi ShipmentModel
        $this->invoiceModel = new InvoiceModel(); // ### P
        $this->distributorInvoiceModel = new DistributorInvoiceModel();
        $this->db = \Config\Database::connect();
        helper(['form', 'url']);
    }
    // // Menampilkan daftar order masuk untuk pabrik (status: approved)
    // public function index()
    // {
    //     // Pastikan hanya user dengan role 'pabrik' yang bisa mengakses
    //     if (session()->get('role') !== 'pabrik') {
    //         return redirect()->to(base_url('/error'))->with('error', 'Anda tidak memiliki akses ke halaman ini.');
    //     }

    //     // Ambil order dengan status 'approved' (atau 'processing' jika sudah diproses pabrik)
    //     // Order ini sudah divalidasi oleh distributor dan menunggu aksi dari pabrik
    //     $incomingOrders = $this->orderModel
    //         ->whereIn('status', ['approved', 'processing'])
    //         ->orderBy('order_date', 'ASC')
    //         ->findAll();

    //     $distributorUsernames = [];
    //     $agenUsernames = [];

    //     foreach ($incomingOrders as $order) {
    //         // Ambil username distributor
    //         if (!empty($order['distributor_id']) && !isset($distributorUsernames[$order['distributor_id']])) {
    //             $distributor = $this->userModel->find($order['distributor_id']);
    //             $distributorUsernames[$order['distributor_id']] = $distributor ? $distributor['username'] : 'N/A';
    //         }
    //         // Ambil username agen
    //         if (!empty($order['agen_id']) && !isset($agenUsernames[$order['agen_id']])) {
    //             $agen = $this->userModel->find($order['agen_id']);
    //             $agenUsernames[$order['agen_id']] = $agen ? $agen['username'] : 'N/A';
    //         }
    //     }

    //     $data = [
    //         'title'                => 'Daftar Order Masuk Pabrik',
    //         'incomingOrders'       => $incomingOrders,
    //         'distributorUsernames' => $distributorUsernames,
    //         'agenUsernames'        => $agenUsernames,
    //         // 'agen'                 => $agen,
    //     ];

    //     return view('pabrik/orders_masuk/index', $data);
    // }
    // Menampilkan daftar order masuk untuk pabrik (status: approved, processing)
    public function index()
    {
        // Pastikan hanya user dengan role 'pabrik' yang bisa mengakses
        if (session()->get('role') !== 'pabrik') {
            return redirect()->to(base_url('/error'))->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        $search = $this->request->getVar('search'); // Ambil parameter pencarian dari URL
        $statusFilter = $this->request->getVar('status'); // Ambil parameter filter status
        $perPage = 10; // Jumlah item per halaman, Anda bisa menyesuaikannya

        // Query dasar untuk order dengan status 'approved' atau 'processing'
        $query = $this->orderModel->whereIn('status', ['approved', 'processing']);

        // Tambahkan filter pencarian jika ada
        if (!empty($search)) {
            // Untuk pencarian berdasarkan username distributor/agen, kita perlu mencari ID user terlebih dahulu
            $foundUserIds = [];
            $users = $this->userModel->like('username', $search)->findAll();
            foreach ($users as $user) {
                $foundUserIds[] = $user['id'];
            }

            $query->groupStart()
                ->like('id', $search) // Cari berdasarkan Order ID (partial match)
                ->orLike('order_date', $search) // Cari berdasarkan tanggal order
                ->orWhereIn('distributor_id', $foundUserIds) // Cari berdasarkan ID distributor
                ->orWhereIn('agen_id', $foundUserIds) // Cari berdasarkan ID agen
                ->groupEnd();
        }
        // Tambahkan filter status jika ada
        if (!empty($statusFilter) && in_array($statusFilter, ['approved', 'processing'])) {
            $query->where('status', $statusFilter);
        }

        // Urutkan berdasarkan tanggal order
        $query->orderBy('order_date', 'ASC');

        // Dapatkan data dengan pagination
        $incomingOrders = $query->paginate($perPage, 'default', $this->request->getVar('page'));
        $pager = $this->orderModel->pager;

        // Ambil username untuk distributor dan agen dari order yang tampil di halaman ini
        $distributorUsernames = [];
        $agenUsernames = [];
        $uniqueDistributorIds = [];
        $uniqueAgenIds = [];

        foreach ($incomingOrders as $order) {
            if (!empty($order['distributor_id'])) {
                $uniqueDistributorIds[$order['distributor_id']] = true;
            }
            if (!empty($order['agen_id'])) {
                $uniqueAgenIds[$order['agen_id']] = true;
            }
        }

        // Fetch usernames for unique distributor IDs
        if (!empty($uniqueDistributorIds)) {
            $distributors = $this->userModel->whereIn('id', array_keys($uniqueDistributorIds))->findAll();
            foreach ($distributors as $distributor) {
                $distributorUsernames[$distributor['id']] = $distributor['username'];
            }
        }

        // Fetch usernames for unique agen IDs
        if (!empty($uniqueAgenIds)) {
            $agens = $this->userModel->whereIn('id', array_keys($uniqueAgenIds))->findAll();
            foreach ($agens as $agen) {
                $agenUsernames[$agen['id']] = $agen['username'];
            }
        }

        $data = [
            'title'                => 'Daftar Order Masuk Pabrik',
            'incomingOrders'       => $incomingOrders,
            'distributorUsernames' => $distributorUsernames,
            'agenUsernames'        => $agenUsernames,
            'pager'                => $pager, // Kirim objek pager ke view
            'search'               => $search, // Kirim nilai pencarian ke view
            'statusFilter'         => $statusFilter, // Kirim nilai filter status ke view
        ];

        return view('pabrik/orders_masuk/index', $data);
    }

    // Menampilkan detail order
    public function detail($orderId = null)
    {
        if (session()->get('role') !== 'pabrik') {
            return redirect()->to(base_url('/error'))->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        $order = $this->orderModel->find($orderId);
        if (!$order) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Order tidak ditemukan: ' . $orderId);
        }

        // Ambil item-item dari order tersebut
        $orderItems = $this->orderItemModel->where('order_id', $orderId)->findAll();

        // Ambil detail produk untuk setiap item order
        $productDetails = [];
        foreach ($orderItems as $item) {
            $product = $this->productModel->find($item['product_id']);
            $productDetails[$item['product_id']] = $product ? $product['product_name'] : 'Produk Tidak Ditemukan';
        }

        // Ambil username distributor dan agen
        $distributor = $this->userModel->find($order['distributor_id']);
        $agen = $this->userModel->find($order['agen_id']);

        $data = [
            'title'          => 'Detail Order Masuk',
            'order'          => $order,
            'orderItems'     => $orderItems,
            'productDetails' => $productDetails,
            'distributor'    => $distributor,
            'agen'           => $agen,
        ];

        return view('pabrik/orders_masuk/detail', $data);
    }

    // Mengubah status order (misal: dari 'approved' menjadi 'processing' atau 'shipped')
    public function updateStatus($orderId = null)
    {
        if (session()->get('role') !== 'pabrik') {
            return redirect()->to(base_url('/pabrik'))->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        $order = $this->orderModel->find($orderId);
        if (!$order) {
            return redirect()->to(base_url('pabrik/incoming-orders'))->with('error', 'Order tidak ditemukan.');
        }

        $newStatus = $this->request->getPost('status');
        $rules = [
            'status' => 'required|in_list[processing,shipped,rejected,completed]',
        ];

        if ($newStatus === 'rejected') {
            $rules['rejection_reason'] = 'required|max_length[50]';
        }

        if (!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
            $errorMessage = 'Gagal memperbarui status. ';
            if (isset($errors['rejection_reason'])) {
                $errorMessage .= 'Alasan penolakan wajib diisi.';
            }
            return redirect()->back()->with('error', $errorMessage);
        }

        // ### MULAI TRANSAKSI DATABASE ###
        $this->db->transStart();

        // Siapkan data utama untuk tabel 'orders'
        $dataToUpdate = [
            'status' => $newStatus,
        ];

        // ### LOGIKA UTAMA: Jika status diubah menjadi 'rejected' ###
        if ($newStatus === 'rejected') {
            // 1. Tambahkan alasan penolakan ke data update order
            $dataToUpdate['rejection_reason'] = $this->request->getPost('rejection_reason');

            // 2. Batalkan tagihan untuk Agen di tabel 'invoices'
            $invoice = $this->invoiceModel->where('order_id', $orderId)->first();
            if ($invoice) {
                $this->invoiceModel->update($invoice['id'], ['status' => 'cancelled']);
            }

            // 3. Batalkan tagihan untuk Distributor di tabel 'distributor_invoices'
            $distributorInvoice = $this->distributorInvoiceModel->where('order_id', $orderId)->first();
            if ($distributorInvoice) {
                $this->distributorInvoiceModel->update($distributorInvoice['id'], ['status' => 'cancelled']);
            }
        }

        // ### LOGIKA YANG SUDAH ADA: Jika status diubah menjadi 'shipped' ###
        if ($newStatus === 'shipped') {
            $existingShipment = $this->shipmentModel->where('order_id', $orderId)->first();
            if (!$existingShipment) {
                $shipmentData = [
                    'order_id'        => $orderId,
                    'shipping_date'   => date('Y-m-d H:i:s'),
                    'delivery_status' => 'on_transit',
                    'tracking_number' => $this->shipmentModel->generateUniqueTrackingNumber(),
                ];
                $this->shipmentModel->insert($shipmentData);
            } else {
                $this->shipmentModel->update($existingShipment['id'], ['delivery_status' => 'on_transit']);
            }
        }

        // ### LOGIKA YANG SUDAH ADA: Jika status diubah menjadi 'completed' ###
        if ($newStatus === 'completed') {
            $existingShipment = $this->shipmentModel->where('order_id', $orderId)->first();
            if ($existingShipment) {
                $this->shipmentModel->update($existingShipment['id'], ['delivery_status' => 'delivered']);
            }
        }

        // Terakhir, update tabel order itu sendiri
        $this->orderModel->update($orderId, $dataToUpdate);

        // ### SELESAIKAN TRANSAKSI DATABASE ###
        $this->db->transComplete();

        // Periksa apakah transaksi berhasil atau gagal
        if ($this->db->transStatus() === false) {
            // Transaksi gagal, kembalikan dengan pesan error
            log_message('error', 'Gagal memperbarui status order dan tagihan terkait untuk order ID: ' . $orderId);
            return redirect()->back()->with('error', 'Terjadi kesalahan pada database saat memperbarui status. Silakan coba lagi.');
        }

        // Pesan sukses yang dinamis
        $successMessage = 'Status order berhasil diperbarui menjadi ' . ucfirst($newStatus) . '.';
        if ($newStatus === 'rejected') {
            $successMessage = 'Order berhasil ditolak dan semua tagihan terkait telah dibatalkan.';
        } elseif ($newStatus === 'shipped') {
            $successMessage = 'Order berhasil dikirim dan data pengiriman telah dibuat.';
        }

        return redirect()->to(base_url('pabrik/incoming-orders/detail/' . $orderId))->with('success', $successMessage);
    }


    // // Mengubah status order (misal: dari 'approved' menjadi 'processing' atau 'shipped')
    // public function updateStatus($orderId = null)
    // {
    //     if (session()->get('role') !== 'pabrik') {
    //         return redirect()->to(base_url('/pabrik'))->with('error', 'Anda tidak memiliki akses ke halaman ini.');
    //     }

    //     $order = $this->orderModel->find($orderId);
    //     if (!$order) {
    //         return redirect()->to(base_url('pabrik/incoming-orders'))->with('error', 'Order tidak ditemukan.');
    //     }

    //     $newStatus = $this->request->getPost('status');
    //     $rules = [
    //         'status' => 'required|in_list[processing,shipped,completed,rejected]', // Sesuaikan status yang valid
    //     ];

    //     if (!$this->validate($rules)) {
    //         return redirect()->back()->with('errors', $this->validator->getErrors());
    //     }

    //     $dataToUpdate = [
    //         'status' => $newStatus,
    //         // Anda bisa menambahkan logika atau kolom lain jika diperlukan,
    //         // seperti tanggal update status atau user yang mengupdate
    //     ];

    //     $noResiBaru = $this->shipmentModel->generateUniqueTrackingNumber();

    //     // Jika status diubah menjadi 'shipped', buat entri di tabel shipments
    //     if ($newStatus === 'shipped') {
    //         // Periksa apakah sudah ada entri shipment untuk order ini
    //         $existingShipment = $this->shipmentModel->where('order_id', $orderId)->first();
    //         if (!$existingShipment) {
    //             $shipmentData = [
    //                 'order_id'        => $orderId,
    //                 'shipping_date'   => date('Y-m-d H:i:s'), // Tanggal pengiriman saat ini
    //                 'delivery_status' => 'on_transit', // Status awal pengiriman
    //                 'tracking_number' => $noResiBaru,
    //                 // 'tracking_number' => 'SHP-' . strtoupper(uniqid()), // Contoh no resi
    //             ];
    //             $this->shipmentModel->insert($shipmentData);
    //             if ($this->shipmentModel->errors()) {
    //                 log_message('error', 'Shipment creation failed: ' . json_encode($this->shipmentModel->errors()));
    //                 return redirect()->back()->with('error', 'Gagal membuat data pengiriman: ' . implode(', ', $this->shipmentModel->errors()));
    //             }
    //         } else {
    //             // Jika sudah ada shipment, update status delivery-nya juga
    //             $this->shipmentModel->update($existingShipment['id'], ['delivery_status' => 'on_transit']);
    //         }
    //     }

    //     // Jika status diubah menjadi 'completed', update status delivery di shipments juga
    //     if ($newStatus === 'completed') {
    //         $existingShipment = $this->shipmentModel->where('order_id', $orderId)->first();
    //         if ($existingShipment) {
    //             $this->shipmentModel->update($existingShipment['id'], ['delivery_status' => 'delivered']);
    //         }
    //     }


    //     $this->orderModel->update($orderId, $dataToUpdate);

    //     if ($this->orderModel->errors()) {
    //         log_message('error', 'Order status update failed: ' . json_encode($this->orderModel->errors()));
    //         return redirect()->back()->with('error', 'Gagal memperbarui status order: ' . implode(', ', $this->orderModel->errors()));
    //     }
    //     // Kirimkan flash data
    //     session()->setFlashdata([
    //         'swal_icon' => 'success',
    //         'swal_title' => 'Berhasil',
    //         'swal_text' => 'Order Berhasil Diubah Statusnya !'
    //     ]);

    //     return redirect()->to(base_url('pabrik/incoming-orders'))->with('success', 'Status order berhasil diperbarui menjadi ' . ucfirst($newStatus) . '.');
    // }

}
