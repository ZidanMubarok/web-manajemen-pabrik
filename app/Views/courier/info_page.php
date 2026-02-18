<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informasi Panduan | AMDK App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background-color: #f0f2f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px 0;
            /* Tambahkan padding agar tidak terlalu mepet */
        }

        .container {
            max-width: 800px;
        }

        .card {
            border-radius: 15px;
            border: none;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .card-header {
            background-color: #007bff;
            color: white;
            padding: 1.5rem;
            border-bottom: none;
            text-align: center;
            font-size: 1.5rem;
            font-weight: 600;
        }

        .card-body {
            padding: 2.5rem;
        }

        .card-body h5 {
            color: #007bff;
            margin-top: 1.5rem;
            margin-bottom: 0.8rem;
            font-weight: 600;
        }

        .card-body ul {
            list-style-type: none;
            padding-left: 0;
        }

        .card-body ul li {
            margin-bottom: 0.8rem;
            line-height: 1.6;
        }

        .card-body ul li i {
            color: #28a745;
            margin-right: 10px;
            font-size: 1.1em;
        }

        .card-footer {
            text-align: center;
            padding: 1.5rem;
            background-color: #f8f9fa;
            border-top: 1px solid #e9ecef;
        }

        .btn-back {
            background-color: #6c757d;
            /* Abu-abu untuk kembali */
            border-color: #6c757d;
            color: white;
            padding: 0.8rem 1.5rem;
            border-radius: 10px;
            text-decoration: none;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .btn-back:hover {
            background-color: #5a6268;
            border-color: #545b62;
            transform: translateY(-2px);
            color: white;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-question-circle me-2"></i> Panduan Penggunaan
            </div>
            <div class="card-body">
                <p class="lead text-center">Halo Kurir! Berikut adalah panduan langkah demi langkah untuk mengubah status pengiriman:</p>

                <h5><i class="fas fa-list-ol"></i> Langkah-Langkah Mengubah Status</h5>
                <ul>
                    <li><i class="fas fa-arrow-right"></i>
                        **Buka Halaman Utama:** Pastikan Anda berada di halaman utama untuk mengubah status pengiriman.
                    </li>
                    <li><i class="fas fa-arrow-right"></i>
                        **Masukkan Nomor Resi:**
                        <br>Di kolom **"Nomor Resi (Tracking Number)"**, masukkan kode pengiriman yang Anda terima dari pabrik.
                        <br>Contoh: `6ZX4F`
                        <br><small class="text-muted">Pastikan formatnya benar:  5 huruf atau angka. Jika salah, akan ada pesan peringatan berwarna merah.</small>
                    </li>
                    <li><i class="fas fa-arrow-right"></i>
                        **Pilih Status Baru:**
                        <br>Di kolom **"Ubah Status Pengiriman Menjadi"**, pilih status yang sesuai dengan kondisi pengiriman saat ini:
                        <ul>
                            <!-- <li><i class="fas fa-caret-right"></i>
                                **Dalam Perjalanan (On Transit):** Pilih ini jika pesanan baru saja Anda ambil dari pabrik dan sedang dalam perjalanan ke pelanggan. (Hanya bisa dipilih jika status sebelumnya 'Pending').
                            </li> -->
                            <li><i class="fas fa-caret-right"></i>
                                **Terkirim (Delivered):** Pilih ini jika pesanan sudah berhasil Anda serahkan kepada pelanggan. (Hanya bisa dipilih jika status sebelumnya 'Dalam Perjalanan'). Setelah ini, pesanan akan dianggap 'Selesai'.
                            </li>
                            <li><i class="fas fa-caret-right"></i>
                                **Gagal (Failed):** Pilih ini jika Anda tidak berhasil mengirimkan pesanan (misal: pelanggan tidak ada di tempat, alamat salah, dll.). (Hanya bisa dipilih jika status sebelumnya 'Dalam Perjalanan'). Setelah ini, pesanan akan dianggap 'Ditolak'.
                            </li>
                        </ul>
                    </li>
                    <li><i class="fas fa-arrow-right"></i>
                        **Tekan Tombol Perbarui:** Setelah Nomor Resi dan Status dipilih dengan benar, tekan tombol besar berwarna hijau **"Perbarui Status Pesanan"**.
                    </li>
                    <li><i class="fas fa-arrow-right"></i>
                        **Periksa Notifikasi:** Sistem akan memberitahu Anda apakah pembaruan berhasil atau ada kesalahan. Jika berhasil, Anda akan melihat pesan sukses!
                    </li>
                </ul>

                <h5><i class="fas fa-exclamation-triangle"></i> Penting untuk Diketahui:</h5>
                <ul>
                    <li><i class="fas fa-shield-alt"></i>
                        Anda tidak bisa mengubah status pengiriman yang sudah **"Terkirim (Delivered)"** atau **"Gagal (Failed)"** karena status tersebut sudah final.
                    </li>
                    <li><i class="fas fa-mobile-alt"></i>
                        Pastikan koneksi internet stabil saat melakukan pembaruan.
                    </li>
                    <li><i class="fas fa-headset"></i>
                        Jika Anda mengalami kesulitan, hubungi pihak administrator atau koordinator pengiriman Anda.
                    </li>
                </ul>
            </div>
            <div class="card-footer">
                <a href="<?= base_url('courier/update-status') ?>" class="btn-back">
                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Halaman Utama
                </a>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>