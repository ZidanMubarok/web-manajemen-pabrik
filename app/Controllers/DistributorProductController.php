<?php

namespace App\Controllers;

use App\Models\ProductModel; // Untuk mengambil data produk pabrik
use App\Models\UserProductModel; // Untuk mengelola custom_price distributor
use CodeIgniter\Controller;

class DistributorProductController extends BaseController
{
    protected $productModel;
    protected $userProductModel;

    public function __construct()
    {
        $this->productModel = new ProductModel();
        $this->userProductModel = new UserProductModel();
        helper(['form', 'url']);
    }

    // Menampilkan daftar semua produk pabrik beserta custom_price distributor
    // public function index()
    // {
    //     // Pastikan hanya user dengan role 'distributor' yang bisa mengakses
    //     if (session()->get('role') !== 'distributor') {
    //         return redirect()->to(base_url('/error'))->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
    //     }

    //     $distributorId = session()->get('id');
    //     $perPage = 10; // Jumlah item per halaman

    //     // Ambil produk pabrik untuk halaman saat ini
    //     // Objek pager akan diambil dari panggilan paginate() ini
    //     $allProducts = $this->productModel->paginate($perPage);

    //     // Ambil ID produk dari halaman saat ini
    //     $productIds = array_column($allProducts, 'id');

    //     // Ambil custom_price yang sudah diset oleh distributor HANYA untuk produk-produk di halaman ini
    //     $distributorPrices = $this->userProductModel
    //         ->where('user_id', $distributorId)
    //         ->whereIn('product_id', $productIds) // Mengambil data berdasarkan product_id yang ada di halaman saat ini
    //         ->findAll(); // Gunakan findAll() karena data yang dicari sudah spesifik

    //     // Dapatkan objek pager dari model yang sudah dipaginasi
    //     $pager = $this->productModel->pager;

    //     // Konversi array distributorPrices menjadi associative array untuk memudahkan lookup
    //     $distributorPricesMap = [];
    //     foreach ($distributorPrices as $price) {
    //         $distributorPricesMap[$price['product_id']] = $price;
    //     }

    //     $data = [
    //         'title'              => 'Daftar Harga Produk Pabrik',
    //         'pager'              => $pager, // Pager akan berfungsi untuk allProducts
    //         'perPage'            => $perPage,
    //         'allProducts'        => $allProducts,
    //         'distributorPricesMap' => $distributorPricesMap,
    //     ];

    //     return view('distributor/daftar/index', $data);
    // }

    public function index()
    {
        // Pastikan hanya user dengan role 'distributor' yang bisa mengakses
        if (session()->get('role') !== 'distributor') {
            return redirect()->to(base_url('/error'))->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
        }

        $distributorId = session()->get('id');
        $perPage = 10; // Jumlah item per halaman
        $searchTerm = $this->request->getGet('search'); // Ambil keyword pencarian

        // Modifikasi query untuk pencarian
        $productModel = $this->productModel;
        if ($searchTerm) {
            $productModel->like('product_name', $searchTerm);
        }

        // Ambil produk pabrik untuk halaman saat ini
        // Objek pager akan diambil dari panggilan paginate() ini
        $allProducts = $productModel->paginate($perPage);

        // Ambil ID produk dari halaman saat ini
        $productIds = array_column($allProducts, 'id');

        $distributorPrices = [];
        if (!empty($productIds)) {
            // Ambil custom_price yang sudah diset oleh distributor HANYA untuk produk-produk di halaman ini
            $distributorPrices = $this->userProductModel
                ->where('user_id', $distributorId)
                ->whereIn('product_id', $productIds) // Mengambil data berdasarkan product_id yang ada di halaman saat ini
                ->findAll(); // Gunakan findAll() karena data yang dicari sudah spesifik
        }
        
        // Dapatkan objek pager dari model yang sudah dipaginasi
        $pager = $this->productModel->pager;

        // Konversi array distributorPrices menjadi associative array untuk memudahkan lookup
        $distributorPricesMap = [];
        foreach ($distributorPrices as $price) {
            $distributorPricesMap[$price['product_id']] = $price;
        }

        $data = [
            'title'              => 'Daftar Harga Produk Pabrik',
            'pager'              => $pager, // Pager akan berfungsi untuk allProducts
            'perPage'            => $perPage,
            'allProducts'        => $allProducts,
            'distributorPricesMap' => $distributorPricesMap,
        ];

        return view('distributor/daftar/index', $data);
    }
    // Menampilkan form untuk mengatur/mengedit custom_price
    public function setPrice($productId = null)
    {
        if (session()->get('role') !== 'distributor') {
            return redirect()->to(base_url('/distributor'))->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        if (is_null($productId)) {
            return redirect()->to(base_url('distributor/products-prices'))->with('error', 'Produk tidak ditemukan.');
        }

        $product = $this->productModel->find($productId);
        if (!$product) {
            return redirect()->to(base_url('distributor/products-prices'))->with('error', 'Produk tidak ditemukan.');
        }

        $distributorId = session()->get('id');
        $userProduct = $this->userProductModel
            ->where('user_id', $distributorId)
            ->where('product_id', $productId)
            ->first();

        $data = [
            'title'       => 'Atur Harga Produk',
            'product'     => $product,
            'userProduct' => $userProduct, // Akan null jika belum diset harganya
        ];
        return view('distributor/daftar/set_price', $data);
    }

    // Menyimpan atau memperbarui custom_price
    public function savePrice()
    {
        if (session()->get('role') !== 'distributor') {
            return redirect()->to(base_url('/distributor'))->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        $distributorId = session()->get('id');
        $productId = $this->request->getPost('product_id');
        $customPrice = $this->request->getPost('custom_price');

        $rules = [
            'product_id'   => 'required|integer',
            'custom_price' => 'required|numeric|greater_than[0]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Cek apakah custom_price untuk produk ini sudah ada untuk distributor ini
        $existingUserProduct = $this->userProductModel
            ->where('user_id', $distributorId)
            ->where('product_id', $productId)
            ->first();

        $dataToSave = [
            'user_id'      => $distributorId,
            'product_id'   => $productId,
            'custom_price' => $customPrice,
        ];

        if ($existingUserProduct) {
            // Jika sudah ada, update
            $this->userProductModel->update($existingUserProduct['id'], $dataToSave);
            $message = 'Harga produk berhasil diperbarui.';
            session()->setFlashdata([
                'swal_icon'  => 'success',
                'swal_title' => 'Berhasil',
                'swal_text'  => 'Data Harga Berhasil Diperbarui! 🎉'
            ]);
        } else {
            // Jika belum ada, insert baru
            $this->userProductModel->save($dataToSave);
            session()->setFlashdata([
                'swal_icon'  => 'success',
                'swal_title' => 'Berhasil',
                'swal_text'  => 'Data Harga Berhasil Ditambahkan! 🎉'
            ]);
            $message = 'Harga produk berhasil ditambahkan.';
        }

        if ($this->userProductModel->errors()) {
            log_message('error', 'Model Errors during user product price save: ' . json_encode($this->userProductModel->errors()));
            return redirect()->back()->withInput()->with('errors', $this->userProductModel->errors());
        }

        return redirect()->to(base_url('distributor/products-prices'))->with('success', $message);
    }

    // Menghapus custom_price (reset ke default/tidak ada harga custom)
    public function deletePrice($id = null) // ID ini adalah ID dari tabel user_products, bukan product_id
    {
        if (session()->get('role') !== 'distributor') {
            return redirect()->to(base_url('/distributor'))->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        $userProduct = $this->userProductModel->find($id);

        if (!$userProduct || $userProduct['user_id'] != session()->get('id')) {
            return redirect()->to(base_url('distributor/products-prices'))->with('error', 'Data harga tidak ditemukan atau Anda tidak memiliki akses.');
        }

        if ($this->userProductModel->delete($id)) {
            session()->setFlashdata([
                'swal_icon'  => 'success',
                'swal_title' => 'Berhasil',
                'swal_text'  => 'Data Harga Berhasil Dhapus! 🎉'
            ]);
            return redirect()->to(base_url('distributor/products-prices'))->with('success', 'Harga kustom produk berhasil dihapus.');
        } else {
            return redirect()->to(base_url('distributor/products-prices'))->with('error', 'Gagal menghapus harga kustom produk.');
        }
    }
}
