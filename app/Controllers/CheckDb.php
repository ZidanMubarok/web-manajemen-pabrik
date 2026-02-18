<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\Database\Exceptions\DatabaseException; // Penting: Import Exception ini

class CheckDb extends BaseController // Pastikan Anda extend BaseController jika itu base Anda
{
    public function index()
    {
        try {
            // Coba untuk mendapatkan instance koneksi database
            // Jika koneksi gagal, baris ini akan melemparkan DatabaseException
            $db = \Config\Database::connect();

            // Jika sampai di sini, berarti koneksi berhasil dibuat.
            // Tidak perlu isConnected(), karena itu tidak ada.
            echo "<h1>Koneksi Database Berhasil!</h1>";
            echo "<p>Anda berhasil terhubung ke database: <strong>" . $db->getDatabase() . "</strong></p>";

            // Opsional: Coba jalankan query sederhana untuk memastikan database bisa diakses
            try {
                $result = $db->query("SELECT 1+2 AS test")->getRow();
                echo "<p>Query test berhasil: 1+2 = " . $result->test . "</p>";

                // Contoh lain: Cek apakah tabel 'users' ada dan bisa diakses
                $userCount = $db->table('users')->countAllResults();
                echo "<p>Tabel 'users' ditemukan. Jumlah user: " . $userCount . "</p>";
            } catch (\Exception $e) {
                // Menangkap exception jika query test gagal (misal tabel tidak ada, permission terbatas)
                echo "<p style='color: orange;'>Gagal menjalankan query test atau mengakses tabel:</p>";
                echo "<p style='color: orange;'>Pesan Error: " . $e->getMessage() . "</p>";
                echo "<p style='color: orange;'>Mungkin masalah izin, atau tabel belum ada.</p>";
            }
        } catch (DatabaseException $e) {
            // Tangani exception jika ada masalah koneksi
            // Ini akan menangkap error seperti kredensial salah, host tidak ditemukan, server DB mati
            echo "<h1 style='color: red;'>Error Koneksi Database!</h1>";
            echo "<p style='color: red;'>Pesan Error: " . $e->getMessage() . "</p>";
            echo "<p style='color: red;'><strong>Kemungkinan Penyebab:</strong></p>";
            echo "<ul>";
            echo "<li>Server database (MySQL/MariaDB) belum berjalan.</li>";
            echo "<li>Hostname database salah (misal: bukan `localhost`).</li>";
            echo "<li>Nama database (`amdk_app_db`) salah.</li>";
            echo "<li>Username atau password database salah.</li>";
            echo "<li>Port database salah (default 3306).</li>";
            echo "</ul>";
            echo "<p style='color: red;'>Periksa kembali konfigurasi Anda di file `.env`.</p>";
        } catch (\Exception $e) {
            // Tangani error umum lainnya yang mungkin terjadi
            echo "<h1 style='color: red;'>Terjadi Kesalahan Tak Terduga!</h1>";
            echo "<p style='color: red;'>Pesan Error: " . $e->getMessage() . "</p>";
            echo "<p style='color: red;'>Ini mungkin bukan masalah koneksi langsung, tapi error lain dalam proses.</p>";
        }
    }
}
