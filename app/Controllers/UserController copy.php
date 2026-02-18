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

    public function index()
    {
        if (session()->get('role') !== 'pabrik') {
            return redirect()->to(base_url('/error'))->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        $users = $this->userModel->where('role', 'distributor')->findAll();

        $data = [
            'title'      => 'Manajemen Pengguna (Distributor)',
            'users'      => $users,
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

        // Mendefinisikan aturan validasi langsung di controller
        $rules = [
            'username'  => 'required|alpha_dash|min_length[3]|max_length[155]|is_unique[users.username]',
            'password'  => 'required|min_length[6]',
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
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $dataToSave = [
            'username'    => $this->request->getPost('username'),
            'password'    => password_hash($this->request->getPost('password'), PASSWORD_BCRYPT), // Hash password di sini
            'role'        => 'distributor', // Role selalu distributor jika dibuat pabrik
            'parent_id'   => session()->get('id'), // Parent ID adalah ID pabrik yang sedang login
        ];

        $this->userModel->save($dataToSave);

        if ($this->userModel->errors()) {
            log_message('error', 'Model Errors during user creation: ' . json_encode($this->userModel->errors()));
            return redirect()->back()->withInput()->with('errors', $this->userModel->errors());
        } elseif (!$this->userModel->insertID()) {
            log_message('error', 'User creation failed: No insert ID generated.');
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan pengguna. Silakan coba lagi.');
        }

        session()->setFlashdata([
            'swal_icon'  => 'success',
            'swal_title' => 'Berhasil',
            'swal_text'  => 'Data Distributor Berhasil Ditambahkan! 🎉'
        ]);

        return redirect()->to(base_url('pabrik/users'));
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

        if ($user['role'] !== 'distributor') {
            return redirect()->to(base_url('pabrik/users'))->with('error', 'Anda hanya dapat mengedit Distributor.');
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

        if ($user['role'] !== 'distributor') {
            return redirect()->to(base_url('pabrik/users'))->with('error', 'Anda hanya dapat memperbarui Distributor.');
        }

        // Mendefinisikan aturan validasi langsung di controller
        $rules = [
            'username' => "required|alpha_dash|min_length[3]|max_length[155]|is_unique[users.username,id,{$id}]",
            'password' => 'permit_empty|min_length[6]', // Password boleh kosong saat update
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
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $dataToUpdate = [
            'username'  => $this->request->getPost('username'),
        ];

        // Hanya hash password jika diisi oleh pengguna
        if ($this->request->getPost('password')) {
            $dataToUpdate['password'] = password_hash($this->request->getPost('password'), PASSWORD_BCRYPT);
        }

        $this->userModel->update($id, $dataToUpdate);

        if ($this->userModel->errors()) {
            log_message('error', 'Model Errors during user update: ' . json_encode($this->userModel->errors()));
            return redirect()->back()->withInput()->with('errors', $this->userModel->errors());
        }

        session()->setFlashdata([
            'swal_icon'  => 'success',
            'swal_title' => 'Berhasil',
            'swal_text'  => 'Data Distributor Berhasil Diedit! ✅'
        ]);

        return redirect()->to(base_url('pabrik/users'));
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

        if ($user['role'] !== 'distributor') {
            return redirect()->to(base_url('pabrik/users'))->with('error', 'Anda hanya dapat menghapus Distributor.');
        }

        if ($id == session()->get('id')) {
            return redirect()->to(base_url('pabrik/users'))->with('error', 'Anda tidak bisa menghapus akun Anda sendiri.');
        }

        if ($this->userModel->delete($id)) {
            session()->setFlashdata([
                'swal_icon'  => 'success',
                'swal_title' => 'Berhasil',
                'swal_text'  => 'Data Distributor Berhasil Dihapus! 🗑️'
            ]);

            return redirect()->to(base_url('pabrik/users'));
        } else {
            return redirect()->to(base_url('pabrik/users'))->with('error', 'Gagal menghapus distributor.');
        }
    }
}
