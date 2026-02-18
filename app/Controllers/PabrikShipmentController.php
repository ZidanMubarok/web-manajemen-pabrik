<?php

namespace App\Controllers;

use App\Models\OrderModel;
use App\Models\InvoiceModel;
use App\Models\ShipmentModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Models\ProductModel; // Untuk nama produk
use App\Models\OrderItemModel; // Untuk detail produk di order
use App\Models\UserModel; // Untuk mendapatkan nama distributor/agen

class PabrikShipmentController extends BaseController
{
    protected $shipmentModel;
    protected $orderModel;
    protected $userModel;
    protected $orderItemModel;
    protected $invoiceModel;
    protected $productModel;

    public function __construct()
    {
        $this->shipmentModel = new ShipmentModel();
        $this->orderModel = new OrderModel();
        $this->userModel = new UserModel();
        $this->orderItemModel = new OrderItemModel();
        $this->invoiceModel = new InvoiceModel();
        $this->productModel = new ProductModel();
        helper(['form', 'url']);
    }

    // public function index()
    // {
    //     // Pastikan hanya user dengan role 'pabrik' yang bisa mengakses
    //     if (session()->get('role') !== 'pabrik') {
    //         return redirect()->to(base_url('/pabrik'))->with('error', 'Anda tidak memiliki akses ke halaman ini.');
    //     }

    //     $search = $this->request->getVar('search'); // Ambil parameter pencarian
    //     $startDate = $this->request->getVar('start_date'); // Ambil filter tanggal mulai
    //     $endDate = $this->request->getVar('end_date');     // Ambil filter tanggal akhir
    //     $perPage = 10; // Jumlah pengiriman per halaman

    //     // Query dasar: Hanya tampilkan pengiriman yang sudah selesai (terkirim atau gagal)
    //     $query = $this->shipmentModel->whereIn('delivery_status', ['delivered', 'failed']);

    //     // Tambahkan filter pencarian jika ada
    //     if (!empty($search)) {
    //         // Untuk pencarian berdasarkan username distributor/agen
    //         $foundOrderIds = [];
    //         $foundUserIds = [];
    //         $users = $this->userModel->like('username', $search)->findAll();
    //         foreach ($users as $user) {
    //             $foundUserIds[] = $user['id'];
    //         }

    //         // Cari order yang terkait dengan user yang ditemukan
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
    //             ->like('id', $search) // Cari berdasarkan ID Pengiriman
    //             ->orLike('tracking_number', $search); // Cari berdasarkan No. Resi

    //         if (!empty($foundOrderIds)) {
    //             $query->orWhereIn('order_id', $foundOrderIds); // Cari berdasarkan Order ID yang terkait
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
    //     $query->orderBy('shipping_date', 'DESC'); // DESC agar yang terbaru di atas

    //     // Dapatkan data pengiriman dengan pagination
    //     $shipments = $query->paginate($perPage, 'default', $this->request->getVar('page'));
    //     $pager = $this->shipmentModel->pager;

    //     // Ambil data terkait (order, user, dll.) untuk pengiriman yang ditampilkan
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

    //     if (!empty($uniqueDistributorIds)) {
    //         $distributors = $this->userModel->whereIn('id', array_keys($uniqueDistributorIds))->findAll();
    //         foreach ($distributors as $distributor) {
    //             $distributorUsernames[$distributor['id']] = $distributor['username'];
    //         }
    //     }

    //     if (!empty($uniqueAgenIds)) {
    //         $agens = $this->userModel->whereIn('id', array_keys($uniqueAgenIds))->findAll();
    //         foreach ($agens as $agen) {
    //             $agenUsernames[$agen['id']] = $agen['username'];
    //         }
    //     }

    //     $data = [
    //         'title'                => 'Riwayat Pengiriman',
    //         'shipments'            => $shipments,
    //         'ordersData'           => $ordersData,
    //         'distributorUsernames' => $distributorUsernames,
    //         'agenUsernames'        => $agenUsernames,
    //         'pager'                => $pager,
    //         'search'               => $search,
    //         'startDate'            => $startDate, // Kirim ke view
    //         'endDate'              => $endDate,   // Kirim ke view
    //     ];

    //     return view('pabrik/riwayat_pengiriman/index', $data);
    // }

    // public function index()
    // {
    //     // Pastikan hanya user dengan role 'pabrik' yang bisa mengakses
    //     if (session()->get('role') !== 'pabrik') {
    //         return redirect()->to(base_url('/pabrik'))->with('error', 'Anda tidak memiliki akses ke halaman ini.');
    //     }

    //     $search = $this->request->getVar('search'); // Ambil parameter pencarian
    //     $startDate = $this->request->getVar('start_date'); // Ambil filter tanggal mulai
    //     $endDate = $this->request->getVar('end_date');     // Ambil filter tanggal akhir
    //     $deliveryStatus = $this->request->getVar('delivery_status'); // [BARU] Ambil filter status pengiriman
    //     $perPage = 10; // Jumlah pengiriman per halaman

    //     // Query dasar: Hanya tampilkan pengiriman yang sudah selesai (terkirim atau gagal)
    //     $query = $this->shipmentModel->whereIn('delivery_status', ['delivered', 'failed']);

    //     // [BARU] Tambahkan filter status pengiriman jika dipilih
    //     if (!empty($deliveryStatus)) {
    //         $query->where('delivery_status', $deliveryStatus);
    //     }

    //     // Tambahkan filter pencarian jika ada
    //     if (!empty($search)) {
    //         // Untuk pencarian berdasarkan username distributor/agen
    //         $foundOrderIds = [];
    //         $foundUserIds = [];
    //         $users = $this->userModel->like('username', $search)->findAll();
    //         foreach ($users as $user) {
    //             $foundUserIds[] = $user['id'];
    //         }

    //         // Cari order yang terkait dengan user yang ditemukan
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
    //             ->like('id', $search) // Cari berdasarkan ID Pengiriman
    //             ->orLike('tracking_number', $search); // Cari berdasarkan No. Resi

    //         if (!empty($foundOrderIds)) {
    //             $query->orWhereIn('order_id', $foundOrderIds); // Cari berdasarkan Order ID yang terkait
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
    //     $query->orderBy('shipping_date', 'DESC'); // DESC agar yang terbaru di atas

    //     // Dapatkan data pengiriman dengan pagination
    //     $shipments = $query->paginate($perPage, 'default', $this->request->getVar('page'));
    //     $pager = $this->shipmentModel->pager;

    //     // Ambil data terkait (order, user, dll.) untuk pengiriman yang ditampilkan
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

    //     if (!empty($uniqueDistributorIds)) {
    //         $distributors = $this->userModel->whereIn('id', array_keys($uniqueDistributorIds))->findAll();
    //         foreach ($distributors as $distributor) {
    //             $distributorUsernames[$distributor['id']] = $distributor['username'];
    //         }
    //     }

    //     if (!empty($uniqueAgenIds)) {
    //         $agens = $this->userModel->whereIn('id', array_keys($uniqueAgenIds))->findAll();
    //         foreach ($agens as $agen) {
    //             $agenUsernames[$agen['id']] = $agen['username'];
    //         }
    //     }

    //     $data = [
    //         'title'                => 'Riwayat Pengiriman',
    //         'shipments'            => $shipments,
    //         'ordersData'           => $ordersData,
    //         'distributorUsernames' => $distributorUsernames,
    //         'agenUsernames'        => $agenUsernames,
    //         'pager'                => $pager,
    //         'search'               => $search,
    //         'startDate'            => $startDate,
    //         'endDate'              => $endDate,
    //         'deliveryStatus'       => $deliveryStatus, // [BARU] Kirim ke view
    //     ];

    //     return view('pabrik/riwayat_pengiriman/index', $data);
    // }
    private function _buildShipmentHistoryQuery()
    {
        $search = $this->request->getVar('search');
        $startDate = $this->request->getVar('start_date');
        $endDate = $this->request->getVar('end_date');
        $deliveryStatus = $this->request->getVar('delivery_status');

        $query = $this->shipmentModel->whereIn('delivery_status', ['delivered', 'failed']);

        if (!empty($deliveryStatus)) {
            $query->where('delivery_status', $deliveryStatus);
        }

        if (!empty($search)) {
            $foundOrderIds = [];
            $users = $this->userModel->like('username', $search)->findAll();
            if ($users) {
                $foundUserIds = array_column($users, 'id');
                $ordersByUsers = $this->orderModel
                    ->whereIn('distributor_id', $foundUserIds)
                    ->orWhereIn('agen_id', $foundUserIds)
                    ->select('id')->findAll();
                if ($ordersByUsers) {
                    $foundOrderIds = array_column($ordersByUsers, 'id');
                }
            }

            $query->groupStart()
                ->like('id', $search)
                ->orLike('tracking_number', $search);

            if (!empty($foundOrderIds)) {
                $query->orWhereIn('order_id', $foundOrderIds);
            }
            $query->groupEnd();
        }

        if (!empty($startDate)) {
            $query->where('shipping_date >=', $startDate . ' 00:00:00');
        }
        if (!empty($endDate)) {
            $query->where('shipping_date <=', $endDate . ' 23:59:59');
        }

        return $query;
    }

    private function _getRelatedShipmentData(array $shipments): array
    {
        if (empty($shipments)) {
            return ['ordersData' => [], 'usernamesById' => []];
        }

        $ordersData = [];
        $usernamesById = [];

        $orderIds = array_column($shipments, 'order_id');
        $orders = $this->orderModel->whereIn('id', $orderIds)->findAll();

        $userIds = [];
        foreach ($orders as $order) {
            $ordersData[$order['id']] = $order;
            if (!empty($order['distributor_id'])) $userIds[] = $order['distributor_id'];
            if (!empty($order['agen_id'])) $userIds[] = $order['agen_id'];
        }

        if (!empty($userIds)) {
            $users = $this->userModel->whereIn('id', array_unique($userIds))->findAll();
            foreach ($users as $user) {
                $usernamesById[$user['id']] = $user['username'];
            }
        }

        return ['ordersData' => $ordersData, 'usernamesById' => $usernamesById];
    }

    public function index()
    {
        if (session()->get('role') !== 'pabrik') {
            return redirect()->to(base_url('/pabrik'))->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        $query = $this->_buildShipmentHistoryQuery();
        $perPage = 10;

        $shipments = $query->orderBy('shipping_date', 'DESC')
            ->paginate($perPage, 'default', $this->request->getVar('page'));

        $relatedData = $this->_getRelatedShipmentData($shipments);

        $data = [
            'title'                => 'Riwayat Pengiriman',
            'shipments'            => $shipments,
            'ordersData'           => $relatedData['ordersData'],
            // Menggabungkan username menjadi satu array lookup untuk kemudahan di view
            'distributorUsernames' => $relatedData['usernamesById'],
            'agenUsernames'        => $relatedData['usernamesById'],
            'pager'                => $this->shipmentModel->pager,
            'search'               => $this->request->getVar('search'),
            'startDate'            => $this->request->getVar('start_date'),
            'endDate'              => $this->request->getVar('end_date'),
            'deliveryStatus'       => $this->request->getVar('delivery_status'),
        ];

        return view('pabrik/riwayat_pengiriman/index', $data);
    }

    /**
     * Fungsi baru untuk ekspor riwayat pengiriman ke Excel.
     */
    public function exportShipmentHistory()
    {
        if (session()->get('role') !== 'pabrik') {
            return redirect()->to(base_url('/pabrik'))->with('error', 'Anda tidak memiliki akses.');
        }

        $query = $this->_buildShipmentHistoryQuery();
        $shipments = $query->orderBy('shipping_date', 'DESC')->findAll();

        if (empty($shipments)) {
            return redirect()->back()->with('error', 'Tidak ada data untuk diekspor berdasarkan filter yang dipilih.');
        }

        $relatedData = $this->_getRelatedShipmentData($shipments);
        $ordersData = $relatedData['ordersData'];
        $usernamesById = $relatedData['usernamesById'];

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'ID Pengiriman');
        $sheet->setCellValue('C1', 'Order ID');
        $sheet->setCellValue('D1', 'Distributor');
        $sheet->setCellValue('E1', 'Agen');
        $sheet->setCellValue('F1', 'Tanggal Kirim');
        $sheet->setCellValue('G1', 'Tanggal Selesai');
        $sheet->setCellValue('H1', 'Status');
        $sheet->setCellValue('I1', 'No. Resi');

        $row = 2;
        $no = 1;
        foreach ($shipments as $shipment) {
            $order = $ordersData[$shipment['order_id']] ?? null;
            $statusText = ($shipment['delivery_status'] == 'delivered') ? 'Terkirim' : 'Gagal';

            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, 'SHP-' . $shipment['id']);
            $sheet->setCellValue('C' . $row, 'ORD-' . $shipment['order_id']);
            $sheet->setCellValue('D' . $row, $order ? ($usernamesById[$order['distributor_id']] ?? 'N/A') : 'N/A');
            $sheet->setCellValue('E' . $row, $order ? ($usernamesById[$order['agen_id']] ?? 'N/A') : 'N/A');
            $sheet->setCellValue('F' . $row, date('d-m-Y H:i', strtotime($shipment['shipping_date'])));
            $sheet->setCellValue('G' . $row, date('d-m-Y H:i', strtotime($shipment['updated_at'])));
            $sheet->setCellValue('H' . $row, $statusText);
            $sheet->setCellValue('I' . $row, $shipment['tracking_number'] ?? '-');

            $row++;
        }

        $sheet->getStyle('A1:I1')->getFont()->setBold(true);
        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'Riwayat_Pengiriman_' . date('Y-m-d') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit();
    }
    // Menampilkan detail pengiriman
    public function detail($shipmentId = null)
    {
        if (session()->get('role') !== 'pabrik') {
            return redirect()->to(base_url('/error'))->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        $shipment = $this->shipmentModel->find($shipmentId);
        if (!$shipment) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Pengiriman tidak ditemukan: ' . $shipmentId);
        }

        $order = $this->orderModel->find($shipment['order_id']);
        if (!$order) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Order terkait pengiriman tidak ditemukan.');
        }

        // Ambil item-item dari order terkait
        $orderItems = $this->orderItemModel->where('order_id', $order['id'])->findAll();

        // Ambil detail produk untuk setiap item order
        $productDetails = [];
        foreach ($orderItems as $item) {
            $product = $this->productModel->find($item['product_id']);
            $productDetails[$item['product_id']] = $product ? $product['product_name'] : 'Produk Tidak Ditemukan';
        }

        $distributor = $this->userModel->find($order['distributor_id']);
        $agen = $this->userModel->find($order['agen_id']);

        $data = [
            'title'          => 'Detail Riwayat Pengiriman',
            'shipment'       => $shipment,
            'order'          => $order,
            'orderItems'     => $orderItems,
            'productDetails' => $productDetails,
            'distributor'    => $distributor,
            'agen'           => $agen,
        ];

        return view('pabrik/riwayat_pengiriman/detail', $data);
    }
    // Mungkin ada fungsi untuk update status pengiriman (misal: dari on_transit ke delivered)
    // public function updateDeliveryStatus($shipmentId = null)
    // {
    //     if (session()->get('role') !== 'pabrik') {
    //         return redirect()->to(base_url('/error'))->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
    //     }

    //     $shipment = $this->shipmentModel->find($shipmentId);
    //     if (!$shipment) {
    //         return redirect()->to(base_url('pabrik/riwayat_pengiriman'))->with('error', 'Pengiriman tidak ditemukan.');
    //     }

    //     $newDeliveryStatus = $this->request->getPost('delivery_status'); // Menggunakan 'status' sesuai dengan input di view SweetAlert

    //     $rules = [
    //         'delivery_status' => 'required|in_list[on_transit,delivered,failed]', // Sesuaikan nama field dengan yang dikirim dari form
    //     ];

    //     if (!$this->validate($rules)) {
    //         // Log error validator untuk debugging
    //         log_message('error', 'Validation failed for shipment status update: ' . json_encode($this->validator->getErrors()));
    //         return redirect()->back()->with('errors', $this->validator->getErrors());
    //     }

    //     $dataToUpdateShipment = [ // Variabel untuk update tabel shipments
    //         'delivery_status' => $newDeliveryStatus,
    //     ];

    //     // Lakukan update pada tabel shipments
    //     $this->shipmentModel->update($shipmentId, $dataToUpdateShipment);

    //     // Periksa jika update shipment berhasil
    //     if ($this->shipmentModel->errors()) {
    //         log_message('error', 'Shipment status update failed: ' . json_encode($this->shipmentModel->errors()));
    //         return redirect()->back()->with('error', 'Gagal memperbarui status pengiriman: ' . implode(', ', $this->shipmentModel->errors()));
    //     }

    //     // Jika status pengiriman menjadi 'delivered', update juga delivery_date dan status di tabel orders
    //     if ($newDeliveryStatus === 'delivered') {
    //         $order = $this->orderModel->find($shipment['order_id']); // Ambil data order terkait

    //         if ($order) { // Pastikan order ditemukan
    //             $dataToUpdateOrder = [
    //                 'delivery_date' => date('Y-m-d H:i:s'), // Masukkan waktu saat ini ke delivery_date di tabel orders
    //                 'status'        => 'completed', // Ubah status order menjadi 'completed'
    //             ];

    //             // Periksa apakah status order saat ini bukan 'completed' sebelum mengupdate
    //             if ($order['status'] !== 'completed') {
    //                 $this->orderModel->update($order['id'], $dataToUpdateOrder);

    //                 if ($this->orderModel->errors()) {
    //                     log_message('error', 'Order status/delivery_date update failed: ' . json_encode($this->orderModel->errors()));
    //                     // Anda bisa memilih untuk mengembalikan error spesifik atau tetap melanjutkan dengan status pengiriman yang sudah update
    //                     return redirect()->back()->with('error', 'Status pengiriman berhasil diperbarui, tetapi gagal memperbarui status order: ' . implode(', ', $this->orderModel->errors()));
    //                 }
    //             }
    //         } else {
    //             log_message('warning', 'Order with ID ' . $shipment['order_id'] . ' not found for shipment ' . $shipmentId);
    //             // Opsional: berikan pesan ke user bahwa order tidak ditemukan
    //         }
    //     }

    //     // Kirimkan flash data SweetAlert2
    //     session()->setFlashdata([
    //         'swal_icon'  => 'success',
    //         'swal_title' => 'Berhasil',
    //         'swal_text'  => 'Status pengiriman berhasil diubah menjadi ' . ucfirst(str_replace('_', ' ', $newDeliveryStatus)) . '.',
    //     ]);

    //     return redirect()->to(base_url('pabrik/riwayat_pengiriman'));
    // }
}
