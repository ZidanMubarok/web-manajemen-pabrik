# 🏭 Factory Delivery Management System (FDMS)

[![Framework](https://img.shields.io/badge/Framework-CodeIgniter%204.5-orange?style=flat-square&logo=codeigniter)](https://codeigniter.com)
[![PHP Version](https://img.shields.io/badge/PHP-8.1%2B-blue?style=flat-square&logo=php)](https://php.net)
[![License](https://img.shields.io/badge/License-MIT-green?style=flat-square)](LICENSE)

**Factory Delivery Management System (FDMS)** adalah solusi enterprise berbasis web yang dirancang untuk mengotomatisasi dan memonitor seluruh alur pengiriman barang dari pabrik ke distributor atau pelanggan. Aplikasi ini menjamin akurasi data, efisiensi logistik, dan transparansi laporan.

---

## 📑 Daftar Isi
* [Fitur Utama](#-fitur-utama)
* [Spesifikasi Teknis](#-spesifikasi-teknis)
* [Persyaratan Sistem](#-persyaratan-sistem)
* [Arsitektur Folder](#-arsitektur-folder)
* [Instalasi & Setup](#-instalasi--setup)
* [Panduan Pengembangan](#-panduan-pengembangan)

---

## 🚀 Fitur Utama

### 1. Manajemen Logistik & Pengiriman
* **Order Processing:** Input dan validasi surat jalan secara digital.
* **Driver & Vehicle Assignment:** Alokasi armada dan supir berdasarkan kapasitas angkut.
* **Real-time Status:** Pelacakan status pengiriman (Pending, On Process, Delivered, Cancelled).

### 2. Inventaris & Gudang
* **Auto-Update Stock:** Stok barang otomatis berkurang saat surat jalan diterbitkan.
* **Batch Tracking:** Pelacakan barang berdasarkan nomor batch produksi.

### 3. Pelaporan Tingkat Tinggi (Advanced Reporting)
* **Excel Export:** Generate laporan pengiriman harian, mingguan, atau bulanan dalam format `.xlsx`.
* **Data Validation:** Menggunakan `Laminas Escaper` untuk memastikan data laporan bersih dari karakter berbahaya.

---

## 🛠 Spesifikasi Teknis

Aplikasi ini dibangun dengan standar industri menggunakan library terkini:

| Komponen | Teknologi | Deskripsi |
| :--- | :--- | :--- |
| **Backend** | CodeIgniter 4.5 | Framework MVC yang cepat dan ringan. |
| **Language** | PHP 8.1+ | Memanfaatkan fitur *typed properties* dan *readonly*. |
| **Spreadsheet** | PhpSpreadsheet ^4.5 | Engine utama untuk olah data laporan Excel. |
| **Database** | MySQL / MariaDB | Relational database untuk integritas data. |
| **Security** | Laminas Escaper | Proteksi terhadap serangan XSS pada view. |
| **Testing** | PHPUnit 11 | Automasi pengujian unit dan integrasi. |

---

## 📋 Persyaratan Sistem

Pastikan server Anda dikonfigurasi dengan ekstensi PHP berikut agar Framework dan Library berfungsi (Cek file `php.ini` Anda/ Setting server PHP, Jika menggunakan shared hosting biasanya defaultnya sudah di setting agar CI bisa berjalan):

| Extension | Kegunaan |
| :--- | :--- |
| `intl` | Internasionalisasi & Validasi Framework |
| `mbstring` | Pemrosesan string multi-byte |
| `gd` / `imagick` | (Opsional) Jika aplikasi mengolah foto bukti pengiriman |
| `mysqli` / `pdo_mysql` | Koneksi Database |
| `fileinfo` | Deteksi MIME type file yang diunggah |
| `xml` / `zip` | Wajib untuk **PhpSpreadsheet** (Membaca/Menulis file Excel) |

---

## 📂 Arsitektur Folder


```text
├── app/
│   ├── Config/          # Konfigurasi sistem
│   ├── Controllers/     # Logika bisnis aplikasi
│   ├── Database/        # Migrasi & Seeder (Testing & Setup)
│   ├── Models/          # Interaksi data ke database
│   └── Views/           # Tampilan antarmuka (User Interface)
├── public/              # Document root (CSS, JS, Images)
├── tests/               # Unit & Feature Testing
├── writable/            # Folder log, cache, dan hasil export
└── vendor/              # Composer dependencies
