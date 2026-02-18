<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;

class ProfileController extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        helper(['form', 'url']);
    }

    public function index()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(base_url('/login'))->with('error', 'Anda harus login untuk mengakses halaman profil.');
        }

        $userId = session()->get('id');
        $user = $this->userModel->find($userId);

        if (!$user) {
            session()->destroy();
            return redirect()->to(base_url('/login'))->with('error', 'Data pengguna tidak ditemukan. Silakan login kembali.');
        }

        // AMBIL DATA PARENT (INDUK) JIKA ADA
        $parent_info = null;
        if (!empty($user['parent_id'])) {
            $parent_info = $this->userModel->select('username')->find($user['parent_id']);
        }

        $data = [
            'title'       => 'Pengaturan Profil',
            'user'        => $user,
            'parent_info' => $parent_info, // Kirim data parent ke view
            'validation'  => \Config\Services::validation()
        ];
        return view('profile/index', $data);
    }

    public function update()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(base_url('/login'))->with('error', 'Anda harus login untuk memperbarui profil.');
        }

        $userId = session()->get('id');
        $user = $this->userModel->find($userId);

        if (!$user) {
            session()->destroy();
            return redirect()->to(base_url('/login'))->with('error', 'Data pengguna tidak ditemukan. Silakan login kembali.');
        }

        $rules = [
            'username'  => "required|alpha_dash|min_length[3]|max_length[155]|is_unique[users.username,id,{$userId}]",
            'email'     => "required|valid_email|is_unique[users.email,id,{$userId}]",
            'no_telpon' => 'required|numeric|max_length[20]',
            'alamat'    => 'permit_empty|min_length[5]',
            'password'  => 'permit_empty|min_length[6]',
        ];

        $messages = [
            'username' => [
                'required'   => 'Username harus diisi.',
                'min_length' => 'Username minimal 3 karakter.',
                'max_length' => 'Username maksimal 155 karakter.',
                'alpha_dash' => 'Username hanya boleh berisi huruf, angka, dash (-), atau underscore (_).',
                'is_unique'  => 'Maaf, username ini sudah digunakan oleh akun lain.'
            ],
            'email' => [
                'required'    => 'Email harus diisi.',
                'valid_email' => 'Format email tidak valid.',
                'is_unique'   => 'Maaf, email ini sudah terdaftar oleh akun lain.'
            ],
            'no_telpon' => [
                'required'           => 'Nomor telepon harus diisi.',
                'numeric' => 'Nomor telepon hanya boleh berisi angka.',
                'max_length'         => 'Nomor telepon maksimal 20 karakter.'
            ],
            'alamat' => [
                'min_length' => 'Alamat minimal 5 karakter.'
            ],
            'password' => [
                'min_length' => 'Password minimal 6 karakter.'
            ],
        ];
        // Validasi input
        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $dataToUpdate = [
            'username'  => $this->request->getPost('username'),
            'email'     => $this->request->getPost('email'),
            'no_telpon' => $this->request->getPost('no_telpon'),
            'alamat'    => $this->request->getPost('alamat'),
        ];

        if ($this->request->getPost('password')) {
            $dataToUpdate['password'] = password_hash($this->request->getPost('password'), PASSWORD_BCRYPT);
        }

        if ($this->userModel->update($userId, $dataToUpdate)) {
            session()->set('username', $dataToUpdate['username']);
            session()->setFlashdata([
                'swal_icon'  => 'success',
                'swal_title' => 'Berhasil',
                'swal_text'  => 'Profil Anda berhasil diperbarui! 👍'
            ]);
        } else {
            log_message('error', 'Gagal memperbarui profil. Errors: ' . json_encode($this->userModel->errors()));
            session()->setFlashdata([
                'swal_icon'  => 'error',
                'swal_title' => 'Gagal',
                'swal_text'  => 'Terjadi kesalahan saat memperbarui profil.'
            ]);
        }
        
        // Redirect ke halaman profil
        return redirect()->to(base_url('profile'));
    }
}
