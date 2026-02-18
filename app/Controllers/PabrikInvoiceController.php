<?php

namespace App\Controllers;

use App\Models\DistributorInvoiceModel;
use App\Models\OrderModel; // Untuk mengambil detail order terkait
use App\Models\UserModel; // Untuk mengambil nama agen/distributor terkait
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class PabrikInvoiceController extends BaseController
{
    protected $distributorInvoiceModel;
    protected $orderModel;
    protected $userModel;

    public function __construct()
    {
        $this->distributorInvoiceModel = new DistributorInvoiceModel();
        $this->orderModel = new OrderModel();
        $this->userModel = new UserModel();
        helper(['form', 'url']);
    }

    /**
     * Menampilkan daftar tagihan distributor (dikelola oleh Pabrik).
     * Filter berdasarkan status, nomor invoice, atau rentang tanggal.
     */
    // public function index()
    // {
    //     // 1. Otentikasi: Sudah benar, tidak ada perubahan
    //     if (session()->get('role') !== 'pabrik' && session()->get('role') !== 'admin') {
    //         return redirect()->to(base_url('/login'))->with('error', 'Anda tidak memiliki akses ke halaman ini.');
    //     }

    //     // 2. Ambil nilai filter: Sudah benar, tidak ada perubahan
    //     $statusFilter = $this->request->getGet('status') ?? '';
    //     $searchQuery  = $this->request->getGet('invoice_number') ?? ''; // Ganti nama agar lebih jelas
    //     $startDate    = $this->request->getGet('start_date') ?? '';
    //     $endDate      = $this->request->getGet('end_date') ?? '';
    //     $perPage      = 10; // Item per halaman

    //     // 3. Bangun Query Dasar
    //     // Model harus sudah siap untuk menerima filter
    //     $this->distributorInvoiceModel
    //         ->select('distributor_invoices.*, users_agen.username as agen_username, users_distributor.username as distributor_username')
    //         ->join('orders', 'orders.id = distributor_invoices.order_id', 'left')
    //         ->join('users as users_agen', 'users_agen.id = orders.agen_id', 'left')
    //         ->join('users as users_distributor', 'users_distributor.id = orders.distributor_id', 'left');

    //     // 4. Terapkan Filter jika ada
    //     if (!empty($statusFilter)) {
    //         $this->distributorInvoiceModel->where('distributor_invoices.status', $statusFilter);
    //     }

    //     if (!empty($searchQuery)) {
    //         // PERBAIKAN: Pencarian yang lebih luas (sesuai placeholder di view)
    //         $this->distributorInvoiceModel->groupStart()
    //             ->like('distributor_invoices.invoice_number', $searchQuery)
    //             ->orLike('users_distributor.username', $searchQuery)
    //             ->groupEnd();
    //     }

    //     if (!empty($startDate)) {
    //         $this->distributorInvoiceModel->where('distributor_invoices.invoice_date >=', $startDate);
    //     }

    //     if (!empty($endDate)) {
    //         $this->distributorInvoiceModel->where('distributor_invoices.invoice_date <=', $endDate);
    //     }

    //     // 5. Eksekusi Paginate dan kirim data ke View
    //     $invoices = $this->distributorInvoiceModel
    //         ->orderBy('distributor_invoices.invoice_date', 'DESC')
    //         ->paginate($perPage, 'invoices_group'); // Gunakan 'invoices_group' secara konsisten

    //     $data = [
    //         'title'         => 'Daftar Tagihan Distributor',
    //         'invoices'      => $invoices,
    //         'pager'         => $this->distributorInvoiceModel->pager, // **Sangat Penting!** Mengambil pager dari model
    //         'statusFilter'  => $statusFilter,
    //         'invoiceNumber' => $searchQuery, // Gunakan variabel yang sama
    //         'startDate'     => $startDate,
    //         'endDate'       => $endDate,
    //     ];

    //     return view('pabrik/distributor_invoices/index', $data);
    // }
    private function _buildInvoiceQuery()
    {
        $statusFilter = $this->request->getGet('status');
        $searchQuery  = $this->request->getGet('invoice_number');
        $startDate    = $this->request->getGet('start_date');
        $endDate      = $this->request->getGet('end_date');

        // Memulai query dengan JOINs
        $query = $this->distributorInvoiceModel
            ->select('distributor_invoices.*, users_distributor.username as distributor_username')
            ->join('orders', 'orders.id = distributor_invoices.order_id', 'left')
            ->join('users as users_distributor', 'users_distributor.id = orders.distributor_id', 'left');

        // Terapkan filter
        if (!empty($statusFilter)) {
            $query->where('distributor_invoices.status', $statusFilter);
        }

        if (!empty($searchQuery)) {
            $query->groupStart()
                ->like('distributor_invoices.invoice_number', $searchQuery)
                ->orLike('users_distributor.username', $searchQuery)
                ->groupEnd();
        }

        if (!empty($startDate)) {
            $query->where('distributor_invoices.invoice_date >=', $startDate);
        }

        if (!empty($endDate)) {
            $query->where('distributor_invoices.invoice_date <=', $endDate);
        }

        return $query;
    }

    public function index()
    {
        if (session()->get('role') !== 'pabrik' && session()->get('role') !== 'admin') {
            return redirect()->to(base_url('/login'))->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        // Gunakan metode privat untuk membangun query
        $query = $this->_buildInvoiceQuery();

        $perPage = 10;
        $invoices = $query->orderBy('distributor_invoices.invoice_date', 'DESC')
            ->paginate($perPage, 'invoices_group');

        $data = [
            'title'         => 'Daftar Tagihan Distributor',
            'invoices'      => $invoices,
            'pager'         => $this->distributorInvoiceModel->pager,
            'statusFilter'  => $this->request->getGet('status'),
            'invoiceNumber' => $this->request->getGet('invoice_number'),
            'startDate'     => $this->request->getGet('start_date'),
            'endDate'       => $this->request->getGet('end_date'),
        ];

        return view('pabrik/distributor_invoices/index', $data);
    }

    /**
     * Fungsi baru untuk ekspor data tagihan ke Excel.
     */
    public function exportInvoices()
    {
        if (session()->get('role') !== 'pabrik' && session()->get('role') !== 'admin') {
            return redirect()->to(base_url('/login'))->with('error', 'Anda tidak memiliki akses.');
        }

        // Gunakan metode privat yang sama, tapi ambil semua data (bukan paginate)
        $query = $this->_buildInvoiceQuery();
        $invoices = $query->orderBy('distributor_invoices.invoice_date', 'DESC')->findAll();

        if (empty($invoices)) {
            return redirect()->back()->with('error', 'Tidak ada data tagihan untuk diekspor berdasarkan filter yang dipilih.');
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Judul Kolom
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'No. Tagihan');
        $sheet->setCellValue('C1', 'Tgl. Tagihan');
        $sheet->setCellValue('D1', 'Jatuh Tempo');
        $sheet->setCellValue('E1', 'Distributor');
        $sheet->setCellValue('F1', 'Total (Rp)');
        $sheet->setCellValue('G1', 'Status');

        // Mapping status untuk teks yang lebih ramah
        $statusMap = [
            'unpaid'         => 'Belum Dibayar',
            'paid'           => 'Lunas',
            'partially_paid' => 'Dibayar Sebagian',
            'cancelled'      => 'Dibatalkan'
        ];

        $row = 2;
        $no = 1;
        foreach ($invoices as $invoice) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $invoice['invoice_number']);
            $sheet->setCellValue('C' . $row, date('d-m-Y', strtotime($invoice['invoice_date'])));
            $sheet->setCellValue('D' . $row, date('d-m-Y', strtotime($invoice['due_date'])));
            $sheet->setCellValue('E' . $row, $invoice['distributor_username'] ?? 'N/A');
            $sheet->setCellValue('F' . $row, $invoice['total_amount']);
            $sheet->setCellValue('G' . $row, $statusMap[$invoice['status']] ?? $invoice['status']);

            $row++;
        }

        // Atur format & styling
        $sheet->getStyle('A1:G1')->getFont()->setBold(true);
        $sheet->getStyle('F2:F' . ($row - 1))->getNumberFormat()->setFormatCode('#,##0');
        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'Tagihan_Distributor_' . date('Y-m-d') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit();
    }

    /**
     * Memperbarui status tagihan distributor (hanya bisa oleh Pabrik).
     * Ini bisa dipanggil via AJAX atau form submit.
     */
    public function updateStatus($invoiceId)
    {
        // Otentikasi: Pastikan hanya peran 'pabrik' atau 'admin' yang bisa mengubah
        if (session()->get('role') !== 'pabrik' && session()->get('role') !== 'admin') {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Anda tidak memiliki izin untuk melakukan tindakan ini.']);
        }

        $invoice = $this->distributorInvoiceModel->find($invoiceId);

        if (!$invoice) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Tagihan tidak ditemukan.']);
        }

        $newStatus = $this->request->getPost('status');
        $rules = [
            'status' => 'required|in_list[unpaid,paid,partially_paid,cancelled]', // Status yang valid
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON(['status' => 'error', 'message' => $this->validator->getErrors()]);
        }

        $dataToUpdate = [
            'status' => $newStatus,
            'payment_date' => ($newStatus === 'paid' ? date('Y-m-d H:i:s') : null), // Set payment_date jika status jadi 'paid'
        ];

        $this->distributorInvoiceModel->transStart();
        try {
            $updateResult = $this->distributorInvoiceModel->update($invoiceId, $dataToUpdate);

            if (!$updateResult) {
                throw new \Exception('Gagal memperbarui status tagihan.');
            }

            $this->distributorInvoiceModel->transComplete();

            if ($this->distributorInvoiceModel->transStatus() === false) {
                throw new \Exception('Transaksi gagal saat memperbarui status tagihan.');
            }

            return $this->response->setJSON(['status' => 'success', 'message' => 'Status tagihan berhasil diperbarui.']);
        } catch (\Exception $e) {
            $this->distributorInvoiceModel->transRollback();
            log_message('error', 'Error updating distributor invoice status for ID ' . $invoiceId . ': ' . $e->getMessage());
            return $this->response->setJSON(['status' => 'error', 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    // public function detail($invoiceId)
    // {
    //     // Otentikasi: Pastikan hanya peran 'pabrik' atau 'admin' yang bisa mengakses
    //     if (session()->get('role') !== 'pabrik' && session()->get('role') !== 'admin') {
    //         return redirect()->to(base_url('/login'))->with('error', 'Anda tidak memiliki akses ke halaman ini.');
    //     }

    //     $invoice = $this->distributorInvoiceModel
    //         ->select('distributor_invoices.*, orders.agen_id, orders.distributor_id, orders.total_amount as order_total_amount, orders.order_date, orders.status as status_order , orders.delivery_date, users.username as agen_username, users.email as agen_email, users.no_telpon as agen_telpon, users.alamat as agen_alamat, distributors_table.username as distributor_username, distributors_table.email as distributor_email, distributors_table.no_telpon as distributor_telpon, distributors_table.alamat as distributor_alamat')
    //         ->join('orders', 'orders.id = distributor_invoices.order_id', 'left')
    //         ->join('users', 'users.id = orders.agen_id', 'left')
    //         ->join('users as distributors_table', 'distributors_table.id = orders.distributor_id', 'left')
    //         ->where('distributor_invoices.id', $invoiceId)
    //         ->first();

    //     if (!$invoice) {
    //         return redirect()->to(base_url('pabrik/distributor-invoices'))->with('error', 'Tagihan tidak ditemukan.');
    //     }

    //     // Ambil item order terkait untuk detail tagihan
    //     $orderItems = [];
    //     $productNames = [];

    //     if (!empty($invoice['order_id'])) {
    //         $orderItems = $this->orderModel->getOrderItemsByOrderId($invoice['order_id']);

    //         // Mengambil nama produk untuk setiap item
    //         $productIds = array_column($orderItems, 'product_id');
    //         if (!empty($productIds)) {
    //             $products = $this->orderModel->getProductsByIds($productIds);
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

    //     return view('pabrik/distributor_invoices/detail', $data);
    // }
    public function detail($invoiceId)
    {
        // Otentikasi: Pastikan hanya peran 'pabrik' atau 'admin' yang bisa mengakses
        if (session()->get('role') !== 'pabrik' && session()->get('role') !== 'admin') {
            return redirect()->to(base_url('/login'))->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        $invoice = $this->distributorInvoiceModel
            ->select('distributor_invoices.*, orders.agen_id, orders.distributor_id, orders.total_amount as order_total_amount, orders.order_date, orders.status as status_order , orders.delivery_date, users.username as agen_username, users.email as agen_email, users.no_telpon as agen_telpon, users.alamat as agen_alamat, distributors_table.username as distributor_username, distributors_table.email as distributor_email, distributors_table.no_telpon as distributor_telpon, distributors_table.alamat as distributor_alamat')
            ->join('orders', 'orders.id = distributor_invoices.order_id', 'left')
            ->join('users', 'users.id = orders.agen_id', 'left')
            ->join('users as distributors_table', 'distributors_table.id = orders.distributor_id', 'left')
            ->where('distributor_invoices.id', $invoiceId)
            ->first();

        if (!$invoice) {
            return redirect()->to(base_url('pabrik/distributor-invoices'))->with('error', 'Tagihan tidak ditemukan.');
        }

        // --- PERUBAHAN DIMULAI DI SINI ---

        // Ambil item order terkait beserta data produk (termasuk harga pabrik)
        $orderItems = [];
        if (!empty($invoice['order_id'])) {
            // Query yang lebih efisien dengan JOIN ke tabel products
            $orderItems = model('OrderItemModel') // Gunakan model untuk tabel 'order_items'
                ->select('order_items.*, products.product_name, products.base_price')
                ->join('products', 'products.id = order_items.product_id', 'left')
                ->where('order_items.order_id', $invoice['order_id'])
                ->findAll();
        }

        // Variabel $productNames tidak diperlukan lagi
        $data = [
            'title'      => 'Detail Tagihan #' . esc($invoice['invoice_number']),
            'invoice'    => $invoice,
            'orderItems' => $orderItems, // Kirim data item yang sudah lengkap
        ];

        // --- PERUBAHAN SELESAI ---

        return view('pabrik/distributor_invoices/detail', $data);
    }
}
