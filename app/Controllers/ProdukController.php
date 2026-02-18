<?php

namespace App\Controllers;

use App\Models\ProductModel;
use CodeIgniter\Controller;

class ProdukController extends BaseController
{
    protected $productModel;

    public function __construct()
    {
        $this->productModel = new ProductModel();
        helper(['form', 'url']);
    }

    // // Menampilkan daftar produk milik pabrik
    // public function index()
    // {
    //     // Pastikan hanya user dengan role 'pabrik' yang bisa mengakses
    //     if (session()->get('role') !== 'pabrik') {
    //         return redirect()->to(base_url('/error'))->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
    //     }

    //     // Ambil produk yang user_id-nya sama dengan id pabrik yang login
    //     $userId = session()->get('id'); // ID user pabrik yang sedang login
    //     $products = $this->productModel->where('user_id', $userId)->findAll();

    //     $data = [
    //         'title'    => 'Manajemen Produk',
    //         'products' => $products,
    //     ];

    //     return view('pabrik/produk/index', $data);
    // }

    public function index()
    {
        // Pastikan hanya user dengan role 'pabrik' yang bisa mengakses
        if (session()->get('role') !== 'pabrik') {
            return redirect()->to(base_url('/error'))->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
        }

        $userId = session()->get('id'); // ID user pabrik yang sedang login
        $search = $this->request->getVar('search'); // Ambil parameter pencarian dari URL
        $perPage = 8; // Jumlah item per halaman, Anda bisa menyesuaikannya

        // Query dasar untuk produk milik pabrik yang login
        $query = $this->productModel->where('user_id', $userId);

        // Tambahkan filter pencarian jika ada
        if (!empty($search)) {
            $query->groupStart()
                ->like('product_name', $search)
                ->orLike('description', $search)
                ->groupEnd();
        }

        // Dapatkan data dengan pagination
        $products = $query->paginate($perPage, 'default', $this->request->getVar('page'));
        $pager = $this->productModel->pager;

        $data = [
            'title'    => 'Manajemen Produk',
            'products' => $products,
            'pager'    => $pager, // Kirim objek pager ke view
            'search'   => $search, // Kirim nilai pencarian ke view agar form bisa mengingatnya
        ];

        return view('pabrik/produk/index', $data);
    }
    
    // Menampilkan form tambah produk
    public function create()
    {
        if (session()->get('role') !== 'pabrik') {
            return redirect()->to(base_url('/error'))->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
        }

        $data = [
            'title' => 'Tambah Produk Baru',
        ];
        return view('pabrik/produk/create', $data);
    }

    // Menyimpan data produk baru
    public function store()
    {
        if (session()->get('role') !== 'pabrik') {
            return redirect()->to(base_url('/error'))->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
        }

        $userId = session()->get('id'); // ID user pabrik yang sedang login

        // Menggunakan validasi dari model
        if (!$this->validate($this->productModel->validationRules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $dataToSave = [
            'user_id'      => $userId, // Otomatis isi user_id dengan id pabrik yang login
            'product_name' => $this->request->getPost('product_name'),
            'description'  => $this->request->getPost('description'),
            'base_price'   => $this->request->getPost('base_price'),
        ];

        $this->productModel->save($dataToSave);

        if ($this->productModel->errors()) {
            log_message('error', 'Model Errors during product creation: ' . json_encode($this->productModel->errors()));
            return redirect()->back()->withInput()->with('errors', $this->productModel->errors());
        } elseif (!$this->productModel->insertID()) {
            log_message('error', 'Product creation failed: No insert ID generated.');
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan produk. Silakan coba lagi.');
        }

        // Kirimkan flash data
        session()->setFlashdata([
            'swal_icon' => 'success',
            'swal_title' => 'Berhasil',
            'swal_text' => 'Data Produk Berhasil Ditambahkan !'
        ]);

        return redirect()->to(base_url('pabrik/products'))->with('success', 'Produk berhasil ditambahkan.');
    }

    // Menampilkan form edit produk
    public function edit($id = null)
    {
        if (session()->get('role') !== 'pabrik') {
            return redirect()->to(base_url('/error'))->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
        }

        $userId = session()->get('id');
        $product = $this->productModel->where('user_id', $userId)->find($id); // Pastikan hanya bisa mengedit produk miliknya sendiri

        if (!$product) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Produk tidak ditemukan atau Anda tidak memiliki akses: ' . $id);
        }

        $data = [
            'title'   => 'Edit Produk',
            'product' => $product,
        ];
        return view('pabrik/produk/edit', $data);
    }

    // Memperbarui data produk
    public function update($id = null)
    {
        if (session()->get('role') !== 'pabrik') {
            return redirect()->to(base_url('/error'))->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
        }

        $userId = session()->get('id');
        $product = $this->productModel->where('user_id', $userId)->find($id); // Pastikan hanya bisa mengupdate produk miliknya sendiri

        if (!$product) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Produk tidak ditemukan atau Anda tidak memiliki akses: ' . $id);
        }

        // Aturan validasi (sama dengan store, bisa diatur di model)
        $rules = $this->productModel->validationRules;
        // Khusus untuk update, jika ada validasi unique, perlu dikecualikan id saat ini
        $rules['product_name'] = "required|min_length[3]|max_length[255]"; // tidak perlu is_unique jika hanya nama produk
        // Jika Anda ingin product_name unik, Anda perlu modifikasi aturan validasinya
        // 'product_name' => "required|min_length[3]|max_length[255]|is_unique[products.product_name,id,{$id}]",

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $dataToUpdate = [
            'product_name' => $this->request->getPost('product_name'),
            'description'  => $this->request->getPost('description'),
            'base_price'   => $this->request->getPost('base_price'),
        ];

        $this->productModel->update($id, $dataToUpdate);

        if ($this->productModel->errors()) {
            log_message('error', 'Model Errors during product update: ' . json_encode($this->productModel->errors()));
            return redirect()->back()->withInput()->with('errors', $this->productModel->errors());
        }

        // Kirimkan flash data
        session()->setFlashdata([
            'swal_icon' => 'success',
            'swal_title' => 'Berhasil',
            'swal_text' => 'Data Produk Berhasil Di edit !'
        ]);

        return redirect()->to(base_url('pabrik/products'))->with('success', 'Produk berhasil diperbarui.');
    }

    // Menghapus data produk
    public function delete($id = null)
    {
        if (session()->get('role') !== 'pabrik') {
            return redirect()->to(base_url('/error'))->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
        }

        $userId = session()->get('id');
        $product = $this->productModel->where('user_id', $userId)->find($id); // Pastikan hanya bisa menghapus produk miliknya sendiri

        if (!$product) {
            return redirect()->to(base_url('pabrik/products'))->with('error', 'Produk tidak ditemukan atau Anda tidak memiliki akses.');
        }

        if ($this->productModel->delete($id)) {

            // Kirimkan flash data
            session()->setFlashdata([
                'swal_icon' => 'success',
                'swal_title' => 'Berhasil',
                'swal_text' => 'Data Produk Berhasil Dihapus !'
            ]);
            return redirect()->to(base_url('pabrik/products'))->with('success', 'Produk berhasil dihapus.');
        } else {
            return redirect()->to(base_url('pabrik/products'))->with('error', 'Gagal menghapus produk.');
        }
    }
}
