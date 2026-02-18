<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\OrderModel;
use CodeIgniter\Controller;
use App\Models\InvoiceModel;
use App\Models\ProductModel;
use App\Models\OrderItemModel;
use App\Controllers\BaseController;


class AgentController extends BaseController
{
    protected $orderModel;
    protected $userModel;
    protected $invoiceModel;
    protected $orderItemModel;
    protected $productModel;

    public function __construct()
    {
        $this->orderModel = new OrderModel();
        $this->userModel = new UserModel();
        $this->invoiceModel = new InvoiceModel();
        $this->orderItemModel = new OrderItemModel();
        $this->productModel = new ProductModel();
        helper(['form', 'url']);
    }


    // public function index()
    // {
    //     // Pastikan hanya user dengan role 'agen' yang bisa mengakses
    //     if (session()->get('role') !== 'agen') {
    //         return redirect()->to(base_url('/login'))->with('error', 'Anda tidak memiliki akses ke halaman ini.');
    //     }

    //     $agenId = session()->get('id'); // Ambil ID agen yang sedang login

    //     // Dapatkan data agen dari tabel users
    //     $agenData = $this->userModel->find($agenId);

    //     // Dapatkan distributor_id dari parent_id agen
    //     $distributorId = $agenData['parent_id'] ?? null;

    //     // Jika agen tidak terhubung ke distributor (parent_id null atau 0), tampilkan error atau data kosong
    //     if (!$distributorId) {
    //         $data = [
    //             'title'               => 'Dashboard Agen',
    //             'totalOrders'         => 0,
    //             'pendingOrders'       => 0,
    //             'shippedOrders'       => 0,
    //             'completedOrders'     => 0,
    //             'totalPaidInvoices'   => 0,
    //             'totalUnpaidInvoices' => 0,
    //             'recentOrders'        => [],
    //             'distributorName'     => 'Tidak Terhubung ke Distributor',
    //             'error'               => 'Akun Anda belum terhubung ke Distributor. Silakan hubungi admin.'
    //         ];
    //         return view('agen/index', $data);
    //     }

    //     // Dapatkan semua order_id yang dimiliki oleh agen ini dan ditujukan ke distributor ini
    //     $agenOrderIds = $this->orderModel
    //         ->select('id') // Hanya ambil kolom 'id' (order_id)
    //         ->where('agen_id', $agenId)
    //         ->where('distributor_id', $distributorId)
    //         ->findAll(); // Menggunakan findAll() untuk mendapatkan semua ID

    //     // Konversi hasil ke array sederhana dari ID order
    //     $orderIds = array_column($agenOrderIds, 'id');

    //     // Jika tidak ada order_id yang ditemukan, set 0 untuk semua metrik tagihan
    //     if (empty($orderIds)) {
    //         $totalPaidInvoices = 0;
    //         $totalUnpaidInvoices = 0;
    //     } else {
    //         // Hitung total tagihan yang sudah dibayar
    //         $totalPaidInvoices = $this->invoiceModel
    //             ->whereIn('order_id', $orderIds) // Filter berdasarkan order_id yang dimiliki agen
    //             ->where('status', 'paid')
    //             ->countAllResults();

    //         // Hitung total tagihan yang belum dibayar
    //         $totalUnpaidInvoices = $this->invoiceModel
    //             ->whereIn('order_id', $orderIds) // Filter berdasarkan order_id yang dimiliki agen
    //             ->whereIn('status', ['unpaid', 'pending'])
    //             ->countAllResults();
    //     }
    //     // ******************************************************


    //     // Hitung total order yang diajukan agen ke distributornya
    //     $totalOrders = $this->orderModel
    //         ->where('agen_id', $agenId)
    //         ->where('distributor_id', $distributorId)
    //         ->countAllResults();

    //     // Hitung pesanan tertunda (status 'pending')
    //     $pendingOrders = $this->orderModel
    //         ->where('agen_id', $agenId)
    //         ->where('distributor_id', $distributorId)
    //         ->whereIn('status', ['pending','approved'])
    //         ->countAllResults();

    //     // Hitung pesanan diproses/dikirim (status 'processing' atau 'shipped')
    //     $shippedOrders = $this->orderModel
    //         ->where('agen_id', $agenId)
    //         ->where('distributor_id', $distributorId)
    //         ->whereIn('status', ['processing', 'shipped'])
    //         ->countAllResults();

    //     // Hitung pesanan selesai (status 'completed')
    //     $completedOrders = $this->orderModel
    //         ->where('agen_id', $agenId)
    //         ->where('distributor_id', $distributorId)
    //         ->where('status', 'completed')
    //         ->countAllResults();


    //     // Ambil order terbaru atau order yang masih aktif (misal 5 terbaru)
    //     $recentOrders = $this->orderModel
    //         ->where('agen_id', $agenId)
    //         ->where('distributor_id', $distributorId)
    //         ->orderBy('order_date', 'DESC')
    //         ->limit(5)
    //         ->findAll();

    //     // Untuk menampilkan nama distributor di dashboard agen
    //     $distributorName = 'Tidak Diketahui';
    //     $distributor = $this->userModel->find($distributorId);
    //     if ($distributor) {
    //         $distributorName = $distributor['username'];
    //     }

    //     $data = [
    //         'title'               => 'Dashboard Agen',
    //         'totalOrders'         => $totalOrders,
    //         'pendingOrders'       => $pendingOrders,
    //         'shippedOrders'       => $shippedOrders,
    //         'completedOrders'     => $completedOrders,
    //         'totalPaidInvoices'   => $totalPaidInvoices, // Data ini sekarang sudah benar
    //         'totalUnpaidInvoices' => $totalUnpaidInvoices, // Data ini sekarang sudah benar
    //         'recentOrders'        => $recentOrders,
    //         'distributorName'     => $distributorName,
    //         'error'               => null // Reset error jika sebelumnya ada
    //     ];

    //     return view('agen/index', $data);
    // }

    public function index()
    {
        // Pastikan hanya user dengan role 'agen' yang bisa mengakses
        if (session()->get('role') !== 'agen') {
            return redirect()->to(base_url('/login'))->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        $agenId = session()->get('id'); // Ambil ID agen yang sedang login

        // Dapatkan data agen dari tabel users
        $agenData = $this->userModel->find($agenId);

        // Dapatkan distributor_id dari parent_id agen
        $distributorId = $agenData['parent_id'] ?? null;

        // Jika agen tidak terhubung ke distributor (parent_id null atau 0), tampilkan error atau data kosong
        if (!$distributorId) {
            $data = [
                'title'                 => 'Dashboard Agen',
                'totalOrders'           => 0,
                'pendingOrders'         => 0,
                'shippedOrders'         => 0,
                'completedOrders'       => 0,
                'rejectedOrders'        => 0, // Tambah ini
                'totalPaidInvoices'     => 0,
                'totalUnpaidInvoices'   => 0,
                'totalRejectedInvoices' => 0, // Tambah ini
                'recentOrders'          => [],
                'distributorName'       => 'Tidak Terhubung ke Distributor',
                'error'                 => 'Akun Anda belum terhubung ke Distributor. Silakan hubungi admin.'
            ];
            return view('agen/index', $data);
        }

        // Dapatkan semua order_id yang dimiliki oleh agen ini dan ditujukan ke distributor ini
        $agenOrderIds = $this->orderModel
            ->select('id')
            ->where('agen_id', $agenId)
            ->where('distributor_id', $distributorId)
            ->findAll();

        // Konversi hasil ke array sederhana dari ID order
        $orderIds = array_column($agenOrderIds, 'id');

        // *************** Perhitungan Order ***************
        // Hitung total order yang diajukan agen ke distributornya
        $totalOrders = $this->orderModel
            ->where('agen_id', $agenId)
            ->where('distributor_id', $distributorId)
            ->countAllResults();

        // Hitung pesanan tertunda (status 'pending')
        $pendingOrders = $this->orderModel
            ->where('agen_id', $agenId)
            ->where('distributor_id', $distributorId)
            ->whereIn('status', ['pending', 'approved'])
            ->countAllResults();

        // Hitung pesanan diproses/dikirim (status 'processing' atau 'shipped')
        $shippedOrders = $this->orderModel
            ->where('agen_id', $agenId)
            ->where('distributor_id', $distributorId)
            ->whereIn('status', ['processing', 'shipped'])
            ->countAllResults();

        // Hitung pesanan selesai (status 'completed')
        $completedOrders = $this->orderModel
            ->where('agen_id', $agenId)
            ->where('distributor_id', $distributorId)
            ->where('status', 'completed')
            ->countAllResults();

        // Hitung pesanan ditolak (status 'rejected') - TAMBAH INI
        $rejectedOrders = $this->orderModel
            ->where('agen_id', $agenId)
            ->where('distributor_id', $distributorId)
            ->where('status', 'rejected')
            ->countAllResults();
        // ******************************************************


        // *************** Perhitungan Tagihan ***************
        // Jika tidak ada order_id yang ditemukan, set 0 untuk semua metrik tagihan
        if (empty($orderIds)) {
            $totalPaidInvoices = 0;
            $totalUnpaidInvoices = 0;
            $totalRejectedInvoices = 0; // Tambah ini
        } else {
            // Hitung total tagihan yang sudah dibayar
            $totalPaidInvoices = $this->invoiceModel
                ->whereIn('order_id', $orderIds)
                ->where('status', 'paid')
                ->countAllResults();

            // Hitung total tagihan yang belum dibayar
            $totalUnpaidInvoices = $this->invoiceModel
                ->whereIn('order_id', $orderIds)
                ->whereIn('status', ['unpaid', 'pending'])
                ->countAllResults();

            // Hitung total tagihan yang ditolak - TAMBAH INI
            $totalRejectedInvoices = $this->invoiceModel
                ->whereIn('order_id', $orderIds)
                ->where('status', 'cancelled') // Asumsi ada status 'rejected' untuk invoice
                ->countAllResults();
        }
        // ******************************************************


        // Ambil order terbaru atau order yang masih aktif (misal 5 terbaru)
        $recentOrders = $this->orderModel
            ->where('agen_id', $agenId)
            ->where('distributor_id', $distributorId)
            ->orderBy('order_date', 'DESC')
            ->limit(5)
            ->findAll();

        // Untuk menampilkan nama distributor di dashboard agen
        $distributorName = 'Tidak Diketahui';
        $distributor = $this->userModel->find($distributorId);
        if ($distributor) {
            $distributorName = $distributor['username'];
        }

        $data = [
            'title'                 => 'Dashboard Agen',
            'totalOrders'           => $totalOrders,
            'pendingOrders'         => $pendingOrders,
            'shippedOrders'         => $shippedOrders,
            'completedOrders'       => $completedOrders,
            'rejectedOrders'        => $rejectedOrders, // Tambahkan ini ke data
            'totalPaidInvoices'     => $totalPaidInvoices,
            'totalUnpaidInvoices'   => $totalUnpaidInvoices,
            'totalRejectedInvoices' => $totalRejectedInvoices, // Tambahkan ini ke data
            'recentOrders'          => $recentOrders,
            'distributorName'       => $distributorName,
            'error'                 => null // Reset error jika sebelumnya ada
        ];

        return view('agen/index', $data);
    }

    // Untuk distributor dashboard yang menampilkan agen yang bersangkuatan dneganya !
    // public function list_agen()
    // {
    //     // Pastikan hanya user dengan role 'distributor' yang bisa mengakses
    //     if (session()->get('role') !== 'distributor') {
    //         return redirect()->to(base_url('/distributor'))->with('error', 'Anda tidak memiliki akses ke halaman ini.');
    //     }

    //     $distributorId = session()->get('id'); // ID distributor yang sedang login

    //     // Ambil agen yang parent_id-nya sama dengan id distributor yang login
    //     $agents = $this->userModel
    //         ->where('role', 'agen')
    //         ->where('parent_id', $distributorId)
    //         ->findAll();

    //     $data = [
    //         'title'  => 'Manajemen Agen Saya',
    //         'agents' => $agents,
    //     ];
    //     return view('distributor/agen/index', $data);
    // }
    public function list_agen()
    {
        if (session()->get('role') !== 'distributor') {
            return redirect()->to(base_url('/distributor'))->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        $distributorId = session()->get('id');
        $perPage = 10; // Jumlah item per halaman

        // Ambil keyword pencarian
        $keyword = $this->request->getGet('keyword');

        // Siapkan query dasar
        $agentQuery = $this->userModel
            ->where('role', 'agen')
            ->where('parent_id', $distributorId);

        // Jika ada keyword pencarian, tambahkan kondisi LIKE
        if ($keyword) {
            $agentQuery->groupStart()
                ->like('username', $keyword)
                ->orLike('email', $keyword)
                ->orLike('no_telpon', 'keyword')
                ->orLike('alamat', $keyword)
                ->groupEnd();
        }

        // Ambil data agen dengan paginasi
        $agents = $agentQuery->paginate($perPage, 'agents');

        // Dapatkan Pager instance
        $pager = $this->userModel->pager;

        // Dapatkan nomor halaman saat ini
        $currentPage = $pager->getCurrentPage('agents');

        // Dapatkan total hasil
        $totalAgents = $pager->getTotal('agents');

        // Hitung nomor awal (Z)
        $startNumber = ($currentPage - 1) * $perPage + 1;

        // Hitung nomor akhir (Y)
        $endNumber = $startNumber + count($agents) - 1;

        // Menangani kasus jika tidak ada agen sama sekali
        if ($totalAgents == 0) {
            $startNumber = 0;
        }

        $data = [
            'title'       => 'Manajemen Agen Saya',
            'agents'      => $agents,
            'pager'       => $pager,
            'keyword'     => $keyword,
            'currentPage' => $currentPage, // Masih berguna untuk penomoran tabel
            'perPage'     => $perPage,     // Masih berguna untuk penomoran tabel

            // --- DATA BARU UNTUK TEKS INFORMASI ---
            'totalAgents' => $totalAgents, // Total data (X)
            'startNumber' => $startNumber, // Nomor awal (Z)
            'endNumber'   => $endNumber,   // Nomor akhir (Y)
        ];

        return view('distributor/agen/index', $data);
    }
    // Menampilkan form tambah agen baru
    public function create()
    {
        if (session()->get('role') !== 'distributor') {
            return redirect()->to(base_url('/distributor'))->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        $data = [
            'title'      => 'Tambah Agen Baru',
            'validation' => \Config\Services::validation()
        ];

        return view('distributor/agen/create', $data);
    }

    // Menyimpan data agen baru
    public function store()
    {
        if (session()->get('role') !== 'distributor') {
            return redirect()->to(base_url('/distributor'))->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        // Mendefinisikan aturan validasi
        $rules = [
            'username'  => 'required|alpha_dash|min_length[3]|max_length[155]|is_unique[users.username]',
            'password'  => 'required|min_length[6]',
            'email'     => 'required|valid_email|is_unique[users.email]',
            'no_telpon' => 'required|numeric|max_length[20]',
            'alamat'    => 'required|min_length[10]',
        ];

        $messages = [
            'username' => [
                'required'   => 'Username harus diisi.',
                'alpha_dash' => 'Username hanya boleh berisi huruf, angka, dash (-), atau underscore (_).',
                'min_length' => 'Username minimal 3 karakter.',
                'max_length' => 'Username maksimal 155 karakter.',
                'is_unique'  => 'Maaf, username ini sudah digunakan.'
            ],
            'password' => [
                'required'   => 'Password harus diisi.',
                'min_length' => 'Password minimal 6 karakter.'
            ],
            'email' => [
                'required'    => 'Email harus diisi.',
                'valid_email' => 'Format email tidak valid.',
                'is_unique'   => 'Maaf, email ini sudah terdaftar.'
            ],
            'no_telpon' => [
                'required'           => 'Nomor telepon harus diisi.',
                'numeric' => 'Nomor telepon hanya boleh berisi angka.',
                'max_length'         => 'Nomor telepon maksimal 20 karakter.'
            ],
            'alamat' => [
                'required'   => 'Alamat harus diisi.',
                'min_length' => 'Alamat minimal 10 karakter.'
            ],
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $dataToSave = [
            'username'  => $this->request->getPost('username'),
            'password'  => password_hash($this->request->getPost('password'), PASSWORD_BCRYPT),
            'email'     => $this->request->getPost('email'),
            'no_telpon' => $this->request->getPost('no_telpon'),
            'alamat'    => $this->request->getPost('alamat'),
            'role'      => 'agen',
            'parent_id' => session()->get('id'), // Parent ID adalah ID distributor yang sedang login
        ];

        if ($this->userModel->save($dataToSave)) {
            session()->setFlashdata([
                'swal_icon'  => 'success',
                'swal_title' => 'Berhasil',
                'swal_text'  => 'Data Agen Berhasil Ditambahkan! 🎉'
            ]);
            return redirect()->to(base_url('distributor/agents'));
        } else {
            log_message('error', 'Gagal menyimpan agen baru. Errors: ' . json_encode($this->userModel->errors()));
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan agen. Silakan coba lagi.');
        }
    }

    // Menampilkan form edit agen
    public function edit($id = null)
    {
        if (session()->get('role') !== 'distributor') {
            return redirect()->to(base_url('/distributor'))->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        $distributorId = session()->get('id');
        $agent = $this->userModel
            ->where('id', $id)
            ->where('role', 'agen')
            ->where('parent_id', $distributorId) // Pastikan hanya bisa mengedit agen miliknya
            ->first();

        if (!$agent) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Agen tidak ditemukan atau Anda tidak memiliki akses: ' . $id);
        }

        $data = [
            'title'      => 'Edit Agen',
            'agent'      => $agent,
            'validation' => \Config\Services::validation()
        ];
        return view('distributor/agen/edit', $data);
    }

    // Memperbarui data agen
    public function update($id = null)
    {
        if (session()->get('role') !== 'distributor') {
            return redirect()->to(base_url('/distributor'))->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        $distributorId = session()->get('id');
        $agent = $this->userModel
            ->where('id', $id)
            ->where('role', 'agen')
            ->where('parent_id', $distributorId) // Pastikan hanya bisa mengupdate agen miliknya
            ->first();

        if (!$agent) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Agen tidak ditemukan atau Anda tidak memiliki akses: ' . $id);
        }

        $rules = [
            'username'  => "required|alpha_dash|min_length[3]|max_length[155]|is_unique[users.username,id,{$id}]",
            'password'  => 'permit_empty|min_length[6]',
            'email'     => "required|valid_email|is_unique[users.email,id,{$id}]",
            'no_telpon' => 'required|numeric|max_length[20]',
            'alamat'    => 'required|min_length[10]',
        ];


        $messages = [
            'username' => [
                'required'   => 'Username harus diisi.',
                'alpha_dash' => 'Username hanya boleh berisi huruf, angka, dash (-), atau underscore (_).',
                'min_length' => 'Username minimal 3 karakter.',
                'max_length' => 'Username maksimal 155 karakter.',
                'is_unique'  => 'Maaf, username ini sudah digunakan.'
            ],
            'password' => [
                'required'   => 'Password harus diisi.',
                'min_length' => 'Password minimal 6 karakter.'
            ],
            'email' => [
                'required'    => 'Email harus diisi.',
                'valid_email' => 'Format email tidak valid.',
                'is_unique'   => 'Maaf, email ini sudah terdaftar.'
            ],
            'no_telpon' => [
                'required'           => 'Nomor telepon harus diisi.',
                'numeric' => 'Nomor telepon hanya boleh berisi angka.',
                'max_length'         => 'Nomor telepon maksimal 20 karakter.'
            ],
            'alamat' => [
                'required'   => 'Alamat harus diisi.',
                'min_length' => 'Alamat minimal 10 karakter.'
            ],
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // if (!$this->validate($rules)) {
        //     return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        // }

        $dataToUpdate = [
            'username'  => $this->request->getPost('username'),
            'email'     => $this->request->getPost('email'),
            'no_telpon' => $this->request->getPost('no_telpon'),
            'alamat'    => $this->request->getPost('alamat'),
        ];

        if ($this->request->getPost('password')) {
            $dataToUpdate['password'] = password_hash($this->request->getPost('password'), PASSWORD_BCRYPT);
        }

        if ($this->userModel->update($id, $dataToUpdate)) {
            session()->setFlashdata([
                'swal_icon'  => 'success',
                'swal_title' => 'Berhasil',
                'swal_text'  => 'Data Agen Berhasil Diedit! ✅'
            ]);
            return redirect()->to(base_url('distributor/agents'));
        } else {
            log_message('error', 'Gagal memperbarui agen. Errors: ' . json_encode($this->userModel->errors()));
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui agen. Silakan coba lagi.');
        }
    }

    // Menghapus data agen
    public function delete($id = null)
    {
        if (session()->get('role') !== 'distributor') {
            return redirect()->to(base_url('/distributor'))->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        $distributorId = session()->get('id');
        $agent = $this->userModel
            ->where('id', $id)
            ->where('role', 'agen')
            ->where('parent_id', $distributorId) // Pastikan hanya bisa menghapus agen miliknya
            ->first();

        if (!$agent) {
            return redirect()->to(base_url('distributor/agents'))->with('error', 'Agen tidak ditemukan atau Anda tidak memiliki akses.');
        }

        if ($this->userModel->delete($id)) {
            session()->setFlashdata([
                'swal_icon'  => 'success',
                'swal_title' => 'Berhasil',
                'swal_text'  => 'Data Agen Berhasil Dihapus! 🗑️'
            ]);
        } else {
            session()->setFlashdata([
                'swal_icon'  => 'error',
                'swal_title' => 'Gagal',
                'swal_text'  => 'Gagal menghapus agen.'
            ]);
        }
        return redirect()->to(base_url('distributor/agents'));
    }
    // Invoice Tagihan saya (agen)
    public function invoices()
    {
        // Pastikan hanya user dengan role 'agen' yang bisa mengakses
        if (session()->get('role') !== 'agen') {
            return redirect()->to(base_url('/login'))->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        $agenId = session()->get('id'); // Dapatkan ID Agen yang sedang login

        // Ambil input pencarian dan filter dari URL
        $searchInvoiceId = $this->request->getVar('invoice_id');
        $searchOrderId = $this->request->getVar('order_id');
        $statusFilter = $this->request->getVar('status');
        $startDate = $this->request->getVar('start_date');
        $endDate = $this->request->getVar('end_date');

        // Mengambil semua tagihan yang terkait dengan order yang dibuat oleh Agen ini
        $builder = $this->invoiceModel
            ->select('invoices.*, orders.total_amount as order_total_amount, orders.order_date')
            ->join('orders', 'orders.id = invoices.order_id')
            ->where('orders.agen_id', $agenId);

        // Tambahkan kondisi pencarian berdasarkan ID Tagihan
        if ($searchInvoiceId) {
            $builder->like('invoices.id', $searchInvoiceId);
        }

        // Tambahkan kondisi pencarian berdasarkan ID Order
        if ($searchOrderId) {
            $builder->like('orders.id', $searchOrderId);
        }

        // Tambahkan kondisi filter status jika ada input status
        if ($statusFilter && $statusFilter !== '') {
            $builder->where('invoices.status', $statusFilter);
        }

        // Tambahkan kondisi filter tanggal jika ada input start_date dan end_date
        if ($startDate && $endDate) {
            $builder->where('invoices.invoice_date >=', $startDate . ' 00:00:00');
            $builder->where('invoices.invoice_date <=', $endDate . ' 23:59:59');
        } elseif ($startDate) {
            $builder->where('invoices.invoice_date >=', $startDate . ' 00:00:00');
        } elseif ($endDate) {
            $builder->where('invoices.invoice_date <=', $endDate . ' 23:59:59');
        }

        $perPage = 5;
        // $invoices = $builder->orderBy('invoices.invoice_date', 'DESC')->findAll()->paginate($perPage);
        $invoices = $builder->orderBy('invoices.invoice_date', 'DESC')->paginate($perPage);
        $pager = $this->invoiceModel->pager; // Dapatkan objek pager

        $data = [
            'title'             => 'Daftar Tagihan Anda',
            'invoices'          => $invoices,
            'searchInvoiceId'   => $searchInvoiceId,
            'searchOrderId'     => $searchOrderId,
            'statusFilter'      => $statusFilter,
            'startDate'         => $startDate,
            'endDate'           => $endDate,
            'pager'             => $pager, // Kirim objek pager ke view
            'perPage'            => $perPage, // Kirimkan juga perPage untuk penomoran
            'allInvoiceStatuses' => ['unpaid', 'paid', 'cancelled'], // Daftar semua status tagihan yang mungkin
        ];

        return view('agen/invoices/index', $data);
    }

    /**
     * Menampilkan detail dari tagihan spesifik.
     */
    public function invoiceDetail($invoiceId = null)
    {
        // Pastikan hanya user dengan role 'agen' yang bisa mengakses dan ID tagihan valid
        if (session()->get('role') !== 'agen' || !$invoiceId) {
            return redirect()->to(base_url('/login'))->with('error', 'Akses ditolak atau ID Tagihan tidak valid.');
        }

        $invoice = $this->invoiceModel->find($invoiceId);

        // Periksa apakah tagihan ditemukan
        if (!$invoice) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Tagihan tidak ditemukan: ' . $invoiceId);
        }

        $order = $this->orderModel->find($invoice['order_id']);

        // Penting: Pastikan order terkait juga milik agen yang login
        if (!$order || $order['agen_id'] !== session()->get('id')) {
            return redirect()->to(base_url('agen/invoices'))->with('error', 'Anda tidak memiliki akses ke tagihan ini.');
        }

        // Ambil item-item dari order terkait untuk detail produk
        $orderItems = $this->orderItemModel->where('order_id', $order['id'])->findAll();

        // Ambil detail nama produk untuk setiap item order
        $productDetails = [];
        foreach ($orderItems as $item) {
            $product = $this->productModel->find($item['product_id']);
            $productDetails[$item['product_id']] = $product ? $product['product_name'] : 'Produk Tidak Ditemukan';
        }

        // Ambil informasi distributor yang terkait dengan order (parent_id dari agen)
        $distributor = $this->userModel->find($order['distributor_id']);

        $data = [
            'title'        => 'Detail Tagihan',
            'invoice'      => $invoice,
            'order'        => $order,
            'orderItems'   => $orderItems,
            'productDetails' => $productDetails,
            'distributor'  => $distributor,
        ];

        return view('agen/invoices/detail', $data);
    }
}
