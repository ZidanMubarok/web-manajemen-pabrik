<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RoleFilter implements FilterInterface
{
    /**
     * Do whatever processing this filter needs to do.
     * By default it should not return anything during
     * normal execution. However, when an abnormal state
     * is found, it should return an instance of
     * CodeIgniter\HTTP\Response. If it does, script
     * execution will end and that Response will be
     * sent back to the client, allowing for error pages,
     * redirects, etc.
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return RequestInterface|ResponseInterface|string|void
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        if (empty($arguments)) {
            log_message('error', 'RoleFilter: Tidak ada peran yang disediakan dalam argumen filter. Cek konfigurasi route.');
            session()->setFlashdata('error', 'Konfigurasi akses tidak valid.');
            return redirect()->to(base_url('/')); // Atau ke halaman error umum
        }
        
        $userRole = session()->get('role'); // Ambil peran user dari session
        
        // Jika user tidak memiliki role SAMA SEKALI, atau rolenya tidak termasuk dalam daftar yang diizinkan
        if (! $userRole || ! in_array($userRole, $arguments)) {
            // Opsional: Jika user login tapi rolenya salah untuk halaman ini,
            // lebih baik log out dia dan arahkan ke login untuk mencegah loop atau kebingungan.
            if (session()->get('isLoggedIn')) {
                session()->destroy();
                session()->setFlashdata('error', 'Anda tidak bisa mengakses halaman tersebut.');
                return redirect()->to(base_url('login'));
            }
            // Jika user tidak login sama sekali (seharusnya sudah ditangani oleh AuthFilter),
            session()->setFlashdata('error', 'Login terlebih dahulu untuk mengakses halaman tersebut.');
            // tetap arahkan ke login.
            return redirect()->to(base_url('login'));
        }
    }

    /**
     * Allows After filters to inspect and modify the response
     * object as needed. This method does not allow any way
     * to stop execution of other after filters, short of
     * throwing an Exception or Error.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return ResponseInterface|void
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        //
    }
}
