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
        helper(['form', 'url', 'cookie']);
    }

    public function login()
    {
        // Jika sudah login, redirect ke dashboard yang sesuai
        if (session()->get('isLoggedIn')) {
            return $this->redirectUser(session()->get('role'));
        }

        // Cek cookie "Ingat Saya"
        $rememberCookie = get_cookie('remember_me');
        if ($rememberCookie) {
            list($selector, $validator) = explode(':', $rememberCookie);

            $user = $this->userModel->where('remember_selector', $selector)->first();

            if ($user && hash_equals($user['remember_token'], hash('sha256', $validator))) {
                $this->createUserSession($user);
                return $this->redirectUser($user['role']);
            }
        }

        $data = [
            'title' => 'Login',
            // 'validation' => \Config\Services::validation()
            'validation' => session('validation') ?? \Config\Services::validation()
        ];
        return view('auth/login', $data);
    }

    public function processLogin()
    {
        $rules = [
            'username' => 'required|min_length[3]|max_length[155]|alpha_dash',
            'password' => 'required',
        ];

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

        if (! $this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');
        $rememberMe = $this->request->getPost('remember_me');

        $user = $this->userModel->where('username', $username)->first();

        if ($user && password_verify($password, $user['password'])) {
            $this->createUserSession($user);

            if ($rememberMe) {
                $this->setRememberMeCookie($user['id']);
            }

            return $this->redirectUser($user['role']);
        } else {
            session()->setFlashdata('error', 'Username atau Password salah.');
            return redirect()->back()->withInput();
        }
    }

    protected function createUserSession(array $user)
    {
        $ses_data = [
            'id'         => $user['id'],
            'username'   => $user['username'],
            'role'       => $user['role'],
            'parent_id'  => $user['parent_id'],
            'isLoggedIn' => TRUE
        ];
        session()->set($ses_data);
    }

    protected function setRememberMeCookie(int $userId)
    {
        $selector = base64_encode(random_bytes(9));
        $validator = base64_encode(random_bytes(18));
        $hashedValidator = hash('sha256', $validator);

        $this->userModel->update($userId, [
            'remember_selector' => $selector,
            'remember_token' => $hashedValidator
        ]);

        $cookie = [
            'name'   => 'remember_me',
            'value'  => $selector . ':' . $validator,
            'expire' => 30 * 24 * 60 * 60, // 30 hari
            'secure' => true, // Set true jika menggunakan HTTPS
            'httponly' => true,
        ];
        set_cookie($cookie);
    }

    protected function redirectUser(string $role)
    {
        switch ($role) {
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
    }

    // public function logout()
    // {
    //     // Hapus cookie "Ingat Saya"
    //     $rememberCookie = get_cookie('remember_me');
    //     if ($rememberCookie) {
    //         list($selector,) = explode(':', $rememberCookie);
    //         $user = $this->userModel->where('remember_selector', $selector)->first();
    //         if ($user) {
    //             $this->userModel->update($user['id'], ['remember_selector' => null, 'remember_token' => null]);
    //         }
    //         delete_cookie('remember_me');
    //     }

    //     session()->destroy();
    //     return redirect()->to(base_url('/login'))->with('success', 'Anda telah berhasil logout.');
    // }

    public function logout()
    {
        // 1. Dapatkan ID pengguna dari session yang sedang aktif
        $userId = session()->get('id');

        // 2. Jika ada pengguna yang login (berdasarkan session), hapus tokennya di database
        if ($userId) {
            $this->userModel->update($userId, [
                'remember_selector' => null,
                'remember_token'    => null
            ]);
        }

        // 3. Hapus cookie 'remember_me' dari browser (jika ada)
        // Ini untuk memastikan cookie di sisi klien juga bersih.
        delete_cookie('remember_me');

        // 4. Hancurkan session
        session()->destroy();

        // 5. Redirect ke halaman login dengan pesan sukses
        return redirect()->to(base_url('/login'))->with('success', 'Anda telah berhasil logout.');
    }
}
