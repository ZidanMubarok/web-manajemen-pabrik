<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\ProductModel;
use App\Models\UserProductModel;
use App\Models\OrderModel;
use App\Models\OrderItemModel;
use App\Models\InvoiceModel;
use App\Models\ShipmentModel;
use App\Models\NotificationModel; // Tambahkan ini

class DistributorOrderController extends BaseController
{
    protected $userModel;
    protected $productModel;
    protected $userProductModel;
    protected $orderModel;
    protected $orderItemModel;
    protected $invoiceModel;
    protected $shipmentModel; // Distributor akan mengelola Shipment (konfirmasi delivered)
    protected $notificationModel; // Tambahkan ini

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->productModel = new ProductModel();
        $this->userProductModel = new UserProductModel();
        $this->orderModel = new OrderModel();
        $this->orderItemModel = new OrderItemModel();
        $this->invoiceModel = new InvoiceModel();
        $this->shipmentModel = new ShipmentModel(); // Inisialisasi ShipmentModel
        $this->notificationModel = new NotificationModel(); // Inisialisasi NotificationModel
        helper(['form', 'url']);
    }

    // --- Order Masuk dari Agen (Status 'pending' atau 'approved') ---
    public function incomingOrders()
    {
        if (session()->get('role') !== 'distributor') {
            return redirect()->to(base_url('/login'))->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        $distributorId = session()->get('id');

        // Order yang masuk ke Distributor adalah yang statusnya 'pending' (menunggu persetujuan Distributor)
        // atau 'approved' (sudah disetujui Distributor, menunggu persetujuan Pabrik)
        // atau 'processing' (sudah disetujui Pabrik, menunggu Distributor mengirim)
        $incomingOrders = $this->orderModel
            ->where('distributor_id', $distributorId)
            ->where('agen_id IS NOT NULL') // Memastikan ini order dari Agen
            ->whereIn('status', ['pending', 'approved', 'processing', 'shipped']) // Tambahkan status yang relevan
            ->orderBy('order_date', 'ASC')
            ->findAll();

        $agenUsernames = [];
        foreach ($incomingOrders as $order) {
            if (!isset($agenUsernames[$order['agen_id']])) {
                $agen = $this->userModel->find($order['agen_id']);
                $agenUsernames[$order['agen_id']] = $agen ? $agen['username'] : 'N/A';
            }
        }

        $data = [
            'title'          => 'Order Masuk dari Agen',
            'incomingOrders' => $incomingOrders,
            'agenUsernames'  => $agenUsernames,
        ];

        return view('distributor/orders/incoming_orders', $data);
    }

    // Detail Order Masuk dari Agen
    public function detail($orderId = null)
    {
        if (session()->get('role') !== 'distributor') {
            return redirect()->to(base_url('/login'))->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        $distributorId = session()->get('id');

        $order = $this->orderModel
            ->where('id', $orderId)
            ->where('distributor_id', $distributorId)
            ->where('agen_id IS NOT NULL')
            ->first();

        if (!$order) {
            return redirect()->to(base_url('distributor/orders/incoming'))->with('error', 'Order tidak ditemukan atau Anda tidak memiliki akses ke order ini.');
        }

        $orderItems = $this->orderItemModel->where('order_id', $order['id'])->findAll();

        $productNames = [];
        foreach ($orderItems as $item) {
            $product = $this->productModel->find($item['product_id']);
            $productNames[$item['product_id']] = $product ? $product['product_name'] : 'Produk Tidak Ditemukan';
        }

        $agenData = $this->userModel->find($order['agen_id']);
        $invoiceData = $this->invoiceModel->where('order_id', $orderId)->first();
        $shipmentData = $this->shipmentModel->where('order_id', $orderId)->first(); // Ambil data shipment jika ada

        $data = [
            'title'        => 'Detail Order #ORD-' . $order['id'],
            'order'        => $order,
            'orderItems'   => $orderItems,
            'productNames' => $productNames,
            'agenData'     => $agenData,
            'invoiceData'  => $invoiceData,
            'shipmentData' => $shipmentData, // Kirim data shipment ke view
        ];

        return view('distributor/orders/detail', $data);
    }

    // Memperbarui status order (approve/reject) oleh Distributor
    public function updateStatus($orderId = null)
    {
        if (session()->get('role') !== 'distributor') {
            return redirect()->to(base_url('/login'))->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        $distributorId = session()->get('id');

        $order = $this->orderModel
            ->where('id', $orderId)
            ->where('distributor_id', $distributorId)
            ->where('agen_id IS NOT NULL')
            ->first();

        if (!$order) {
            return redirect()->to(base_url('distributor/orders/incoming'))->with('error', 'Order tidak ditemukan atau Anda tidak memiliki akses.');
        }

        // Distributor hanya bisa mengubah status 'pending'
        if ($order['status'] !== 'pending') {
            return redirect()->back()->with('error', 'Order ini sudah tidak dalam status "pending" dan tidak bisa diubah oleh Anda.');
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
                log_message('warning', 'Invoice not found for order ID: ' . $orderId . ' during status update by Distributor.');
            }

            // --- Notifikasi ke Pabrik jika status menjadi 'approved' ---
            if ($newStatus === 'approved') {
                $pabrikId = $order['pabrik_id']; // Ambil ID Pabrik dari order
                if ($pabrikId) {
                    $notificationData = [
                        'sender_id'   => $distributorId, // ID Distributor yang mengirim
                        'receiver_id' => $pabrikId,
                        'order_id'    => $orderId,
                        'type'        => 'order_approved_by_distributor',
                        'message'     => 'Distributor ' . session()->get('username') . ' telah menyetujui order dari Agen ' . $order['agen_id'] . ' (ID Order: ' . $orderId . '). Mohon persetujuan Anda.',
                        'is_read'     => false,
                    ];
                    $this->notificationModel->insert($notificationData);
                } else {
                    log_message('warning', 'Pabrik ID not found for order ID: ' . $orderId . '. Cannot send notification to Pabrik.');
                }
            }

            $this->orderModel->transComplete();

            if ($this->orderModel->transStatus() === false) {
                return redirect()->back()->with('error', 'Gagal memperbarui status order dan tagihan. Silakan coba lagi.');
            }
        } catch (\Exception $e) {
            $this->orderModel->transRollback();
            log_message('error', 'Error updating order status and invoice for order ID ' . $orderId . ': ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memperbarui status order: ' . $e->getMessage());
        }

        $message = 'Order #ORD-' . $orderId . ' berhasil ' . ($newStatus === 'approved' ? 'disetujui dan notifikasi dikirim ke Pabrik.' : 'ditolak.');

        session()->setFlashdata([
            'swal_icon'  => 'success',
            'swal_title' => 'Berhasil',
            'swal_text'  => 'Data Order Berhasil Diperbarui ! '
        ]);
        return redirect()->to(base_url('distributor/orders/incoming'))->with('success', $message);
    }

    // Riwayat Order Distributor (Order dari Agen)
    public function history()
    {
        if (session()->get('role') !== 'distributor') {
            return redirect()->to(base_url('/login'))->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        $distributorId = session()->get('id');

        $orders = $this->orderModel
            ->where('distributor_id', $distributorId)
            ->where('agen_id IS NOT NULL')
            ->whereNotIn('status', ['pending']) // Exclude pending orders from history
            ->orderBy('order_date', 'DESC')
            ->findAll();

        $agenUsernames = [];
        foreach ($orders as $order) {
            if (!isset($agenUsernames[$order['agen_id']])) {
                $agen = $this->userModel->find($order['agen_id']);
                $agenUsernames[$order['agen_id']] = $agen ? $agen['username'] : 'N/A';
            }
        }

        $data = [
            'title'         => 'Riwayat Order dari Agen',
            'orders'        => $orders,
            'agenUsernames' => $agenUsernames,
        ];

        return view('distributor/orders/history', $data);
    }

    // --- Fungsionalitas Konfirmasi Pengiriman oleh Distributor ---
    public function confirmDelivery($orderId = null)
    {
        if (session()->get('role') !== 'distributor') {
            return redirect()->to(base_url('/login'))->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        $distributorId = session()->get('id');

        $order = $this->orderModel
            ->where('id', $orderId)
            ->where('distributor_id', $distributorId)
            ->where('agen_id IS NOT NULL')
            ->first();

        if (!$order) {
            return redirect()->to(base_url('distributor/orders/incoming'))->with('error', 'Order tidak ditemukan atau Anda tidak memiliki akses.');
        }

        // Distributor hanya bisa mengkonfirmasi pengiriman jika status order 'shipped'
        if ($order['status'] !== 'shipped') {
            return redirect()->back()->with('error', 'Order ini belum dalam status "Dikirim" (Shipped) oleh Pabrik.');
        }

        $shipment = $this->shipmentModel->where('order_id', $orderId)->first();

        // Jika shipment belum ada (harusnya sudah dibuat oleh Pabrik saat status 'shipped')
        if (!$shipment) {
            return redirect()->back()->with('error', 'Data pengiriman tidak ditemukan untuk order ini. Mohon hubungi Pabrik.');
        }

        // Jika shipment sudah delivered, tidak bisa diubah lagi
        if ($shipment['delivery_status'] === 'delivered') {
            return redirect()->back()->with('error', 'Order ini sudah dikonfirmasi telah diterima.');
        }

        $trackingNumber = $this->request->getPost('tracking_number'); // Ambil nomor resi (opsional)

        $this->orderModel->transStart();
        try {
            // Update status pengiriman di tabel shipments
            $this->shipmentModel->update($shipment['id'], [
                'delivery_status' => 'delivered',
                'tracking_number' => $trackingNumber, // Update tracking number jika ada
            ]);

            // Cek status invoice (asumsi sudah lunas)
            $invoice = $this->invoiceModel->where('order_id', $orderId)->first();
            $isInvoicePaid = ($invoice && $invoice['status'] === 'paid');

            // Jika invoice sudah lunas DAN pengiriman sudah delivered, set order status ke 'completed'
            if ($isInvoicePaid) {
                $this->orderModel->update($orderId, ['status' => 'completed']);
            }

            $this->orderModel->transComplete();

            if ($this->orderModel->transStatus() === false) {
                return redirect()->back()->with('error', 'Gagal mengkonfirmasi pengiriman. Silakan coba lagi.');
            }
        } catch (\Exception $e) {
            $this->orderModel->transRollback();
            log_message('error', 'Error confirming delivery for order ID ' . $orderId . ': ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengkonfirmasi pengiriman: ' . $e->getMessage());
        }

        session()->setFlashdata([
            'swal_icon'  => 'success',
            'swal_title' => 'Berhasil',
            'swal_text'  => 'Pengiriman Order #ORD-' . $orderId . ' berhasil dikonfirmasi telah diterima.'
        ]);
        return redirect()->to(base_url('distributor/orders/incoming'));
    }

    // --- Fungsionalitas Manajemen Harga Produk untuk Agen ---
    public function productPrices()
    {
        if (session()->get('role') !== 'distributor') {
            return redirect()->to(base_url('/login'))->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        $distributorId = session()->get('id');

        // Ambil semua produk dasar
        $products = $this->productModel->findAll();

        // Ambil harga kustom yang sudah diatur oleh distributor ini
        $customPrices = $this->userProductModel->where('user_id', $distributorId)->findAll();
        $customPriceMap = [];
        foreach ($customPrices as $cp) {
            $customPriceMap[$cp['product_id']] = $cp['custom_price'];
        }

        $data = [
            'title'        => 'Atur Harga Produk untuk Agen',
            'products'     => $products,
            'customPrices' => $customPriceMap,
        ];

        return view('distributor/products/prices', $data);
    }

    public function saveProductPrices()
    {
        if (session()->get('role') !== 'distributor') {
            return redirect()->to(base_url('/login'))->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        $distributorId = session()->get('id');
        $productPrices = $this->request->getPost('product_prices');

        if (empty($productPrices)) {
            return redirect()->back()->with('error', 'Tidak ada harga yang dikirimkan.');
        }

        $this->userProductModel->transStart();
        try {
            foreach ($productPrices as $productId => $price) {
                $price = (float) $price; // Pastikan harga adalah float

                // Cek apakah sudah ada harga kustom untuk produk dan distributor ini
                $existingPrice = $this->userProductModel
                    ->where('user_id', $distributorId)
                    ->where('product_id', $productId)
                    ->first();

                if ($existingPrice) {
                    // Update harga yang sudah ada
                    $this->userProductModel->update($existingPrice['id'], ['custom_price' => $price]);
                } else {
                    // Buat entri harga kustom baru
                    $this->userProductModel->insert([
                        'user_id'      => $distributorId,
                        'product_id'   => $productId,
                        'custom_price' => $price,
                    ]);
                }
            }

            $this->userProductModel->transComplete();

            if ($this->userProductModel->transStatus() === false) {
                return redirect()->back()->with('error', 'Gagal menyimpan harga produk. Silakan coba lagi.');
            }
        } catch (\Exception $e) {
            $this->userProductModel->transRollback();
            log_message('error', 'Error saving product prices for distributor ' . $distributorId . ': ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan harga produk: ' . $e->getMessage());
        }

        session()->setFlashdata([
            'swal_icon'  => 'success',
            'swal_title' => 'Berhasil',
            'swal_text'  => 'Harga produk berhasil diperbarui ! '
        ]);
        return redirect()->to(base_url('distributor/products/prices'));
    }

    // --- Fungsionalitas Manajemen Agen oleh Distributor ---
    public function manageAgents()
    {
        if (session()->get('role') !== 'distributor') {
            return redirect()->to(base_url('/login'))->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        $distributorId = session()->get('id');

        // Ambil semua agen yang parent_id-nya adalah distributor ini
        $agents = $this->userModel->where('parent_id', $distributorId)
            ->where('role', 'agen')
            ->findAll();

        $data = [
            'title'  => 'Kelola Agen Anda',
            'agents' => $agents,
        ];

        return view('distributor/agents/manage', $data);
    }

    public function createAgent()
    {
        if (session()->get('role') !== 'distributor') {
            return redirect()->to(base_url('/login'))->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        $data = [
            'title' => 'Buat Akun Agen Baru',
        ];

        return view('distributor/agents/create', $data);
    }

    public function saveAgent()
    {
        if (session()->get('role') !== 'distributor') {
            return redirect()->to(base_url('/login'))->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        $distributorId = session()->get('id');

        $rules = [
            'username' => 'required|min_length[3]|max_length[50]|is_unique[users.username]',
            'password' => 'required|min_length[6]',
            'confirm_password' => 'required_with[password]|matches[password]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $hashedPassword = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);

        $agentData = [
            'username'  => $this->request->getPost('username'),
            'password'  => $hashedPassword,
            'role'      => 'agen',
            'parent_id' => $distributorId, // Distributor adalah parent dari Agen
        ];

        $this->userModel->transStart();
        try {
            $this->userModel->insert($agentData);
            $this->userModel->transComplete();

            if ($this->userModel->transStatus() === false) {
                return redirect()->back()->with('error', 'Gagal membuat akun agen. Silakan coba lagi.');
            }
        } catch (\Exception $e) {
            $this->userModel->transRollback();
            log_message('error', 'Error creating agent account by distributor ' . $distributorId . ': ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat membuat akun agen: ' . $e->getMessage());
        }

        session()->setFlashdata([
            'swal_icon'  => 'success',
            'swal_title' => 'Berhasil',
            'swal_text'  => 'Akun Agen Berhasil Dibuat ! '
        ]);
        return redirect()->to(base_url('distributor/agents/manage'));
    }

    public function editAgent($agentId = null)
    {
        if (session()->get('role') !== 'distributor') {
            return redirect()->to(base_url('/login'))->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        $distributorId = session()->get('id');
        $agent = $this->userModel->where('id', $agentId)
            ->where('parent_id', $distributorId)
            ->where('role', 'agen')
            ->first();

        if (!$agent) {
            return redirect()->to(base_url('distributor/agents/manage'))->with('error', 'Agen tidak ditemukan atau Anda tidak memiliki akses.');
        }

        $data = [
            'title' => 'Edit Akun Agen',
            'agent' => $agent,
        ];

        return view('distributor/agents/edit', $data);
    }

    public function updateAgent($agentId = null)
    {
        if (session()->get('role') !== 'distributor') {
            return redirect()->to(base_url('/login'))->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        $distributorId = session()->get('id');
        $agent = $this->userModel->where('id', $agentId)
            ->where('parent_id', $distributorId)
            ->where('role', 'agen')
            ->first();

        if (!$agent) {
            return redirect()->to(base_url('distributor/agents/manage'))->with('error', 'Agen tidak ditemukan atau Anda tidak memiliki akses.');
        }

        $rules = [
            'username' => 'required|min_length[3]|max_length[50]|is_unique[users.username,id,' . $agentId . ']',
            'password' => 'permit_empty|min_length[6]',
            'confirm_password' => 'matches[password]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $dataToUpdate = [
            'username' => $this->request->getPost('username'),
        ];

        $newPassword = $this->request->getPost('password');
        if (!empty($newPassword)) {
            $dataToUpdate['password'] = password_hash($newPassword, PASSWORD_DEFAULT);
        }

        $this->userModel->transStart();
        try {
            $this->userModel->update($agentId, $dataToUpdate);
            $this->userModel->transComplete();

            if ($this->userModel->transStatus() === false) {
                return redirect()->back()->with('error', 'Gagal memperbarui akun agen. Silakan coba lagi.');
            }
        } catch (\Exception $e) {
            $this->userModel->transRollback();
            log_message('error', 'Error updating agent account by distributor ' . $distributorId . ': ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memperbarui akun agen: ' . $e->getMessage());
        }

        session()->setFlashdata([
            'swal_icon'  => 'success',
            'swal_title' => 'Berhasil',
            'swal_text'  => 'Akun Agen Berhasil Diperbarui ! '
        ]);
        return redirect()->to(base_url('distributor/agents/manage'));
    }

    public function deleteAgent($agentId = null)
    {
        if (session()->get('role') !== 'distributor') {
            return redirect()->to(base_url('/login'))->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        $distributorId = session()->get('id');
        $agent = $this->userModel->where('id', $agentId)
            ->where('parent_id', $distributorId)
            ->where('role', 'agen')
            ->first();

        if (!$agent) {
            return redirect()->to(base_url('distributor/agents/manage'))->with('error', 'Agen tidak ditemukan atau Anda tidak memiliki akses.');
        }

        $this->userModel->transStart();
        try {
            $this->userModel->delete($agentId);
            $this->userModel->transComplete();

            if ($this->userModel->transStatus() === false) {
                return redirect()->back()->with('error', 'Gagal menghapus akun agen. Silakan coba lagi.');
            }
        } catch (\Exception $e) {
            $this->userModel->transRollback();
            log_message('error', 'Error deleting agent account by distributor ' . $distributorId . ': ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus akun agen: ' . $e->getMessage());
        }

        session()->setFlashdata([
            'swal_icon'  => 'success',
            'swal_title' => 'Berhasil',
            'swal_text'  => 'Akun Agen Berhasil Dihapus ! '
        ]);
        return redirect()->to(base_url('distributor/agents/manage'));
    }

    // --- Fungsionalitas Order ke Pabrik (INI DIHAPUS SESUAI ALUR BARU) ---
    // public function storeOrderToPabrik()
    // {
    //     // Method ini tidak lagi relevan karena Pabrik tidak menerima order formal dari Distributor di aplikasi ini
    //     // Melainkan Pabrik yang mencatat pengeluaran stok ke Distributor, dan Distributor yang mengirim ke Agen.
    //     // Jika Anda tetap ingin ada fitur ini, perlu didiskusikan ulang bagaimana data 'agen_id' dan 'distributor_id'
    //     // akan digunakan di tabel 'orders' untuk order Distributor -> Pabrik.
    //     return redirect()->back()->with('error', 'Fungsionalitas ini tidak diaktifkan dalam alur bisnis saat ini.');
    // }
}
