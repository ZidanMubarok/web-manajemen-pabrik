<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('title') ?>
<?= $title; ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <h1 class="mb-4">Selamat Datang, <?= $user_data['username'] ?>!</h1>
    <p class="lead">Ini adalah dashboard utama untuk peran **Pabrik**. Anda memiliki kontrol penuh atas operasional global.</p>

    <div class="row mb-4 g-3">
        <div class="col-md-4 col-sm-6 mb-3">
            <div class="card text-white bg-primary h-100 card-dashboard-summary">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h5 class="card-title">Total Distributor</h5>
                            <p class="card-text fs-2 fw-bold"><?= $totalDistributors ?></p>
                        </div>
                        <i class="fas fa-users fa-3x"></i>
                    </div>
                    <div class="mt-auto">
                        <a href="<?= base_url('pabrik/users') ?>" class="btn btn-sm btn-outline-light">Lihat Detail <i class="fas fa-arrow-right ms-1"></i></a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-sm-6 mb-3">
            <div class="card text-white bg-success h-100 card-dashboard-summary">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h5 class="card-title">Produk Terjual Bulan Ini</h5>
                            <p class="card-text fs-2 fw-bold"><?= number_format($totalProductsSoldMonth, 0, ',', '.') ?> Unit</p>
                        </div>
                        <i class="fas fa-box-open fa-3x"></i>
                    </div>
                    <div class="mt-auto">
                        <a href="<?= base_url('pabrik/products') ?>" class="btn btn-sm btn-outline-light">Lihat Detail <i class="fas fa-arrow-right ms-1"></i></a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-sm-12 mb-3">
            <div class="card text-white bg-info h-100 card-dashboard-summary">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h5 class="card-title">Total Pendapatan</h5>
                            <p class="card-text fs-2 fw-bold">Rp <?= number_format($totalRevenue, 0, ',', '.') ?>,00</p>
                        </div>
                        <i class="fas fa-dollar-sign fa-3x"></i>
                    </div>
                    <div class="mt-auto">
                        <a href="<?= base_url('pabrik/orders/history') ?>" class="btn btn-sm btn-outline-light">Lihat Detail <i class="fas fa-arrow-right ms-1"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Grafik Chart Js -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <i class="fas fa-chart-line me-2"></i> Grafik Penjualan Global
        </div>
        <div class="card-body">
            <div style="position: relative; height:40vh; width:80vw;">
                <canvas id="salesChart"></canvas>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <i class="fas fa-table me-2"></i> Distributor Aktif Terbaru
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Username Distributor</th>
                            <th>Tanggal Daftar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($distributorAktifTerbaru)): ?>
                            <tr>
                                <td colspan="5" class="text-center">Belum ada data distributor.</td>
                            </tr>
                        <?php else: ?>
                            <?php $no = 1;
                            foreach ($distributorAktifTerbaru as $distributor): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= esc($distributor['username']) ?></td>
                                    <td><?= date('d/m/Y H:i', strtotime($distributor['created_at'])) ?></td>
                                    <td><a href="<?= base_url('pabrik/users/edit/' . $distributor['id']) ?>" class="btn btn-sm btn-info text-white"><i class="fas fa-edit"></i> Edit</a></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- <script>
    // Pastikan DOM sudah siap
    document.addEventListener('DOMContentLoaded', function() {
        // Ambil data yang dikirim dari controller
        const labels = JSON.parse('<?= $chartLabels ?>');
        const dataValues = JSON.parse('<?= $chartData ?>');

        // Data untuk Chart.js
        const data = {
            labels: labels,
            datasets: [{
                label: 'Total Penjualan (Rp)',
                data: dataValues,
                borderColor: '#198754', // Warna hijau modern
                backgroundColor: 'rgba(25, 135, 84, 0.2)', // Warna background transparan
                tension: 0.3, // Membuat garis sedikit melengkung (modern)
                fill: true,
            }]
        };

        // Konfigurasi Chart.js
        const config = {
            type: 'line',
            data: data,
            options: {
                responsive: true,
                maintainAspectRatio: false, // Penting untuk responsivitas di berbagai device
                plugins: {
                    legend: {
                        display: true,
                    },
                    title: {
                        display: true,
                        text: 'Total Pendapatan Bulanan',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Total Pendapatan (Rp)'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Bulan'
                        }
                    }
                }
            }
        };

        // Inisialisasi grafik baru
        const myChart = new Chart(
            document.getElementById('salesChart'),
            config
        );
    });
</script> -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const labels = JSON.parse('<?= $chartLabels ?>');
        const dataValues = JSON.parse('<?= $chartData ?>');

        const data = {
            labels: labels,
            datasets: [{
                label: 'Total Pendapatan Harian (Rp)', // Mengubah label agar lebih deskriptif
                data: dataValues,
                borderColor: '#198754',
                backgroundColor: 'rgba(25, 135, 84, 0.2)',
                tension: 0.3,
                fill: true,
            }]
        };

        const config = {
            type: 'line',
            data: data,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                    },
                    title: {
                        display: true,
                        text: 'Total Pendapatan Harian (30 Hari Terakhir)', // Mengubah judul grafik
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Total Pendapatan (Rp)'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Tanggal' // Mengubah label sumbu X
                        }
                    }
                }
            }
        };

        const myChart = new Chart(
            document.getElementById('salesChart'),
            config
        );
    });
</script>
<?= $this->endSection() ?>