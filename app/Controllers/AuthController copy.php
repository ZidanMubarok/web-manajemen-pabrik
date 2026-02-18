<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;

class AuthController extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        helper(['form', 'url']);
    }

    public function login()
    {
        if (session()->get('isLoggedIn')) {
            $role = session()->get('role');
            switch ($role) {
                case 'pabrik':
                    return redirect()->to(base_url('pabrik'));
                case 'distributor':
                    return redirect()->to(base_url('distributor'));
                case 'agen':
                    return redirect()->to(base_url('agen'));
                default:
                    return redirect()->to(base_url('/dashboard'));
            }
        }

        $data = [
            'title' => 'Login',
            'validation' => \Config\Services::validation()
        ];
        return view('auth/login', $data);
    }

    public function processLogin()
    {
        // Mendefinisikan aturan validasi langsung di controller
        $rules = [
            'username' => 'required|min_length[3]|max_length[155]|alpha_dash',
            'password' => 'required',
        ];

        // Mendefinisikan pesan validasi langsung di controller
        $messages = [
            'username' => [
                'required'   => 'Username harus diisi.',
                'min_length' => 'Username minimal 3 karakter.',
                'max_length' => 'Username maksimal 155 karakter.',
                'alpha_dash' => 'Username hanya boleh berisi huruf, angka, dash (-), atau underscore (_).'
            ],
            'password' => [
                'required' => 'Password harus diisi.',
            ],
        ];

        // Jalankan validasi
        if (! $this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $user = $this->userModel->where('username', $username)->first();

        // Verifikasi user dan password
        if ($user && password_verify($password, $user['password'])) {
            $ses_data = [
                'id'         => $user['id'],
                'username'   => $user['username'],
                'role'       => $user['role'],
                'parent_id'  => $user['parent_id'],
                'isLoggedIn' => TRUE
            ];
            session()->set($ses_data);

            switch ($user['role']) {
                case 'pabrik':
                    return redirect()->to(base_url('pabrik'));
                case 'distributor':
                    return redirect()->to(base_url('distributor'));
                case 'agen':
                    return redirect()->to(base_url('agen'));
                default:
                    session()->setFlashdata('error', 'Peran pengguna tidak dikenali. Silakan hubungi administrator.');
                    return redirect()->to(base_url('logout'));
            }
        } else {
            session()->setFlashdata('error', 'Username atau Password salah.');
            return redirect()->back()->withInput();
        }
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to(base_url('/login'))->with('success', 'Anda telah berhasil logout.');
    }
}
