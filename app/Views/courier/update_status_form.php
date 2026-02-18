<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ubah Status Pengiriman | AMDK App</title>
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
        }

        .container {
            max-width: 600px;
        }

        .card {
            border-radius: 15px;
            border: none;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            /* Untuk memastikan border-radius bekerja pada header */
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

        .form-control-lg,
        .form-select-lg {
            border-radius: 10px;
            padding: 0.8rem 1.2rem;
            font-size: 1.1rem;
            border: 1px solid #ced4da;
            transition: border-color 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }

        .form-control-lg:focus,
        .form-select-lg:focus {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }

        .form-label {
            color: #333;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .custom-button {
            background-color: #28a745;
            /* Green for success */
            border-color: #28a745;
            padding: 0.9rem 1.8rem;
            font-size: 1.25rem;
            border-radius: 10px;
            transition: background-color 0.3s ease, transform 0.2s ease;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            /* Agar tombol selebar container */
        }

        .custom-button:hover {
            background-color: #218838;
            border-color: #1e7e34;
            transform: translateY(-2px);
        }

        .alert {
            border-radius: 10px;
            font-size: 0.95rem;
        }

        .invalid-feedback {
            font-size: 0.875em;
            color: #dc3545;
            /* Warna merah Bootstrap */
        }

        .text-muted {
            font-size: 0.85rem;
        }

        .info-link {
            text-align: center;
            margin-top: 1.5rem;
        }

        .info-link a {
            color: #007bff;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s ease;
        }

        .info-link a:hover {
            color: #0056b3;
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-shipping-fast me-2"></i> Perbarui Status Pengiriman
            </div>
            <div class="card-body">
                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= session()->getFlashdata('success') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= session()->getFlashdata('error') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <form action="<?= base_url('courier/process-update-status') ?>" method="post" novalidate>
                    <?= csrf_field() ?>

                    <div class="mb-4">
                        <label for="tracking_number" class="form-label">Nomor Resi (Tracking Number):</label>
                        <input type="text" class="form-control form-control-lg
                            <?= session('errors.tracking_number') ? 'is-invalid' : '' ?>"
                            id="tracking_number" name="tracking_number"
                            value="<?= old('tracking_number') ?>"
                            required
                            pattern="^[0-9A-Z]{5}$"
                            title="Format: 5 karakter heksadesimal (0-9, A-Z). Contoh: SHP-6Z88F"
                            placeholder="Contoh:68B8F">
                        <div class="invalid-feedback">
                            <?= session('errors.tracking_number') ?>
                        </div>
                        <small class="form-text text-muted">Masukkan nomor resi pengiriman yang akan diubah statusnya.</small>
                    </div>

                    <div class="mb-5">
                        <label for="delivery_status" class="form-label">Ubah Status Pengiriman Menjadi:</label>
                        <select class="form-select form-select-lg
                            <?= session('errors.delivery_status') ? 'is-invalid' : '' ?>"
                            id="delivery_status" name="delivery_status" required>
                            <option value="">-- Pilih Status --</option>
                            <!-- <option value="on_transit" <?= (old('delivery_status') == 'on_transit') ? 'selected' : '' ?>>Dalam Perjalanan (On Transit)</option> -->
                            <option value="delivered" <?= (old('delivery_status') == 'delivered') ? 'selected' : '' ?>>Terkirim (Delivered)</option>
                            <!-- <option value="failed" <?php # (old('delivery_status') == 'failed') ? 'selected' : '' ?>>Gagal (Failed)</option> -->
                        </select>
                        <div class="invalid-feedback">
                            <?= session('errors.delivery_status') ?>
                        </div>
                        <small class="form-text text-muted">Pilih status pengiriman baru untuk nomor resi ini.</small>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn custom-button">
                            <i class="fas fa-check-circle me-2"></i> Perbarui Status Pesanan
                        </button>
                        <a href="<?= base_url('/'); ?>" class="btn btn-primary mt-2 p-3"><i class="fas fa-home"></i> Kembali ke Halaman Utama</a>
                    </div>
                </form>
            </div>
            <div class="card-footer bg-white border-top-0 info-link">
                <a href="<?= base_url('courier/info') ?>">
                    <i class="fas fa-info-circle me-1"></i> Butuh Bantuan? Klik di sini untuk panduan.
                </a>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Opsional: Kode JavaScript untuk validasi Bootstrap client-side (jika ingin lebih advance)
        // Namun, tanpa JS kustom, Bootstrap akan otomatis menampilkan kelas invalid-feedback
        // ketika form disubmit dan ada input 'required' atau 'pattern' yang tidak terpenuhi.
        // Untuk kontrol penuh, Anda bisa menambahkan:
        (function() {
            'use strict'
            var forms = document.querySelectorAll('.needs-validation') // Anda harus menambahkan class 'needs-validation' ke form

            Array.prototype.slice.call(forms)
                .forEach(function(form) {
                    form.addEventListener('submit', function(event) {
                        if (!form.checkValidity()) {
                            event.preventDefault()
                            event.stopPropagation()
                        }
                        form.classList.add('was-validated')
                    }, false)
                })
        })()
    </script>
</body>

</html>