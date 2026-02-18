<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;

class UserController extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        helper(['form', 'url']);
    }

    // public function index()
    // {
    //     if (session()->get('role') !== 'pabrik') {
    //         return redirect()->to(base_url('/error'))->with('error', 'Anda tidak memiliki akses ke halaman ini.');
    //     }

    //     // Ambil hanya distributor yang parent_id nya adalah id pabrik yang login
    //     $pabrikId = session()->get('id');
    //     $users = $this->userModel->where('role', 'distributor')
    //         ->where('parent_id', $pabrikId)
    //         ->findAll();

    //     $data = [
    //         'title'      => 'Manajemen Pengguna (Distributor)',
    //         'users'      => $users,
    //         'filterRole' => 'distributor',
    //         'validation' => \Config\Services::validation()
    //     ];
    //     return view('pabrik/users/user_list', $data);
    // }
    public function index()
    {
        if (session()->get('role') !== 'pabrik') {
            return redirect()->to(base_url('/error'))->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        $pabrikId = session()->get('id');
        $search = $this->request->getVar('search'); // Ambil parameter pencarian dari URL
        $perPage = 10; // Jumlah item per halaman, Anda bisa menyesuaikannya

        // Query dasar untuk distributor dengan parent_id yang sesuai
        $query = $this->userModel->where('role', 'distributor')
            ->where('parent_id', $pabrikId);

        // Tambahkan filter pencarian jika ada
        if (!empty($search)) {
            $query->groupStart()
                ->like('username', $search)
                ->orLike('email', $search)
                ->orLike('no_telpon', $search)
                ->orLike('alamat', $search)
                ->groupEnd();
        }

        // Dapatkan data dengan pagination
        $users = $query->paginate($perPage, 'default', $this->request->getVar('page'));
        $pager = $this->userModel->pager;

        $data = [
            'title'      => 'Manajemen Pengguna (Distributor)',
            'users'      => $users,
            'pager'      => $pager, // Kirim objek pager ke view
            'search'     => $search, // Kirim nilai pencarian ke view agar form bisa mengingatnya
            'filterRole' => 'distributor',
            'validation' => \Config\Services::validation()
        ];
        return view('pabrik/users/user_list', $data);
    }

    public function create()
    {
        if (session()->get('role') !== 'pabrik') {
            return redirect()->to(base_url('/error'))->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }
        $data = [
            'title'      => 'Tambah Distributor Baru',
            'validation' => \Config\Services::validation()
        ];
        return view('pabrik/users/user_create', $data);
    }

    public function store()
    {
        if (session()->get('role') !== 'pabrik') {
            return redirect()->to(base_url('/error'))->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        // Mendefinisikan aturan validasi
        $rules = [
            'username'  => 'required|alpha_dash|min_length[3]|max_length[155]|is_unique[users.username]',
            'password'  => 'required|min_length[6]',
            'email'     => 'required|valid_email|is_unique[users.email]',
            'no_telpon' => 'required|numeric|max_length[20]',
            // 'alamat'    => 'required|min_length[10]',
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
            // 'alamat' => [
            //     'required'   => 'Alamat harus diisi.',
            //     'min_length' => 'Alamat minimal 10 karakter.'
            // ],
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
            'role'      => 'distributor',
            'parent_id' => session()->get('id'), // Parent ID adalah ID pabrik yang sedang login
        ];

        if ($this->userModel->save($dataToSave)) {
            session()->setFlashdata([
                'swal_icon'  => 'success',
                'swal_title' => 'Berhasil',
                'swal_text'  => 'Data Distributor Berhasil Ditambahkan! 🎉'
            ]);
            return redirect()->to(base_url('pabrik/users'));
        } else {
            log_message('error', 'Gagal menyimpan pengguna baru. Errors: ' . json_encode($this->userModel->errors()));
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan pengguna. Silakan coba lagi.');
        }
    }

    public function edit($id = null)
    {
        if (session()->get('role') !== 'pabrik') {
            return redirect()->to(base_url('/pabrik'))->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        $user = $this->userModel->find($id);
        if (!$user) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Pengguna tidak ditemukan: ' . $id);
        }

        // Pastikan pabrik hanya bisa mengedit distributor miliknya
        if ($user['role'] !== 'distributor' || $user['parent_id'] != session()->get('id')) {
            return redirect()->to(base_url('pabrik/users'))->with('error', 'Anda tidak memiliki izin untuk mengedit pengguna ini.');
        }

        $data = [
            'title'      => 'Edit Distributor',
            'user'       => $user,
            'validation' => \Config\Services::validation()
        ];
        return view('pabrik/users/user_edit', $data);
    }
    
    public function update($id = null)
    {
        if (session()->get('role') !== 'pabrik') {
            return redirect()->to(base_url('/pabrik'))->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        $user = $this->userModel->find($id);
        if (!$user) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Pengguna tidak ditemukan: ' . $id);
        }

        // Pastikan pabrik hanya bisa mengupdate distributor miliknya
        if ($user['role'] !== 'distributor' || $user['parent_id'] != session()->get('id')) {
            return redirect()->to(base_url('pabrik/users'))->with('error', 'Anda tidak memiliki izin untuk memperbarui pengguna ini.');
        }

        // Mendefinisikan aturan validasi
        $rules = [
            'username'  => "required|alpha_dash|min_length[3]|max_length[155]|is_unique[users.username,id,{$id}]",
            'password'  => 'permit_empty|min_length[6]', // Password boleh kosong saat update
            'email'     => "required|valid_email|is_unique[users.email,id,{$id}]",
            'no_telpon' => 'required|numeric|max_length[20]',
            // 'alamat'    => 'required|min_length[10]',
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
                'min_length' => 'Password minimal 6 karakter.'
            ],
            'email' => [
                'required'    => 'Email harus diisi.',
                'valid_email' => 'Format email tidak valid.',
                'is_unique'   => 'Maaf, email ini sudah terdaftar.'
            ],
            'no_telpon' => [
                'required'           => 'Nomor telepon harus diisi.',
                'numeric' => 'Nomor telepon hanya boleh berisi angka dan simbol.',
                'max_length'         => 'Nomor telepon maksimal 20 karakter.'
            ],
            // 'alamat' => [
            //     'required'   => 'Alamat harus diisi.',
            //     'min_length' => 'Alamat minimal 10 karakter.'
            // ],
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $dataToUpdate = [
            'username'  => $this->request->getPost('username'),
            'email'     => $this->request->getPost('email'),
            'no_telpon' => $this->request->getPost('no_telpon'),
            'alamat'    => $this->request->getPost('alamat'),
        ];

        // Hanya hash dan update password jika diisi oleh pengguna
        if ($this->request->getPost('password')) {
            $dataToUpdate['password'] = password_hash($this->request->getPost('password'), PASSWORD_BCRYPT);
        }

        if ($this->userModel->update($id, $dataToUpdate)) {
            session()->setFlashdata([
                'swal_icon'  => 'success',
                'swal_title' => 'Berhasil',
                'swal_text'  => 'Data Distributor Berhasil Diedit! ✅'
            ]);
            return redirect()->to(base_url('pabrik/users'));
        } else {
            log_message('error', 'Gagal memperbarui pengguna. Errors: ' . json_encode($this->userModel->errors()));
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui pengguna. Silakan coba lagi.');
        }
    }

    public function delete($id = null)
    {
        if (session()->get('role') !== 'pabrik') {
            return redirect()->to(base_url('/error'))->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        $user = $this->userModel->find($id);
        if (!$user) {
            return redirect()->to(base_url('pabrik/users'))->with('error', 'Pengguna tidak ditemukan.');
        }

        // Pastikan pabrik hanya bisa menghapus distributor miliknya
        if ($user['role'] !== 'distributor' || $user['parent_id'] != session()->get('id')) {
            return redirect()->to(base_url('pabrik/users'))->with('error', 'Anda tidak memiliki izin untuk menghapus pengguna ini.');
        }

        if ($this->userModel->delete($id)) {
            session()->setFlashdata([
                'swal_icon'  => 'success',
                'swal_title' => 'Berhasil',
                'swal_text'  => 'Data Distributor Berhasil Dihapus! 🗑️'
            ]);
        } else {
            session()->setFlashdata([
                'swal_icon'  => 'error',
                'swal_title' => 'Gagal',
                'swal_text'  => 'Gagal menghapus distributor.'
            ]);
        }
        return redirect()->to(base_url('pabrik/users'));
    }
}
