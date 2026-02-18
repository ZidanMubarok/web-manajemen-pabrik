<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('title') ?>
<?= $title; ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    /* Custom styles untuk dashboard card yang lebih modern */
    .dashboard-card {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        border: none;
        border-radius: 0.75rem;
    }

    .dashboard-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }

    .dashboard-card .card-body {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding: 1.5rem;
    }

    .dashboard-card .card-icon {
        font-size: 4rem;
        opacity: 0.6;
        transition: opacity 0.3s;
    }

    .dashboard-card:hover .card-icon {
        opacity: 1;
    }

    .dashboard-card .card-title {
        font-weight: 500;
        margin-bottom: 0.25rem;
    }

    .dashboard-card .card-value {
        font-size: 2.25rem;
        font-weight: 700;
    }

    .dashboard-card .card-link {
        text-decoration: none;
        color: inherit;
        font-weight: 500;
    }

    .dashboard-card .card-link i {
        transition: transform 0.2s;
    }

    .dashboard-card:hover .card-link i {
        transform: translateX(4px);
    }
</style>

<div class="container-fluid">
    <div class="mb-4">
        <h1 class="h3 mb-0 text-gray-800">Selamat Datang, <?= esc($user_data['username']) ?>!</h1>
        <p class="text-muted">Ringkasan operasional pabrik, semua dalam satu tempat.</p>
    </div>

    <!-- Grid untuk 9 Card Informasi -->
    <div class="row g-4">
        <!-- 1. Order Masuk -->
        <div class="col-xl-4 col-md-6">
            <div class="card dashboard-card bg-primary text-white h-100">
                <div class="card-body">
                    <div>
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h5 class="card-title">Order Masuk</h5>
                                <p class="card-value"><?= $orderMasuk ?></p>
                            </div>
                            <i class="fas fa-inbox card-icon"></i>
                        </div>
                    </div>
                    <a href="<?= base_url('pabrik/incoming-orders?search=&status=approved') ?>" class="card-link stretched-link mt-3">
                        Lihat Detail <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- 2. Order Di Proses -->
        <div class="col-xl-4 col-md-6">
            <div class="card dashboard-card bg-warning text-dark h-100">
                <div class="card-body">
                    <div>
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h5 class="card-title">Order Di Proses</h5>
                                <p class="card-value"><?= $orderDiproses ?></p>
                            </div>
                            <i class="fas fa-cogs card-icon"></i>
                        </div>
                    </div>
                    <a href="<?= base_url('pabrik/incoming-orders?search=&status=processing') ?>" class="card-link stretched-link mt-3">
                        Lihat Detail <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- 3. Order Di Kirim -->
        <div class="col-xl-4 col-md-6">
            <div class="card dashboard-card bg-info text-white h-100">
                <div class="card-body">
                    <div>
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h5 class="card-title">Order Di Kirim</h5>
                                <p class="card-value"><?= $orderDikirim ?></p>
                            </div>
                            <i class="fas fa-truck card-icon"></i>
                        </div>
                    </div>
                    <a href="<?= base_url('pabrik/shipments/history?search=&status=on_transit') ?>" class="card-link stretched-link mt-3">
                        Lihat Detail <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- 4. Order Selesai Bulan Ini -->
        <div class="col-xl-4 col-md-6">
            <div class="card dashboard-card bg-success text-white h-100">
                <div class="card-body">
                    <div>
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h5 class="card-title">Order Selesai (Bulan Ini)</h5>
                                <p class="card-value"><?= $orderSelesaiBulanIni ?></p>
                            </div>
                            <i class="fas fa-check-circle card-icon"></i>
                        </div>
                    </div>
                    <a href="<?= base_url('pabrik/orders/history') . '?start_date=' . esc($thisMonthStartDate) . '&end_date=' . esc($thisMonthEndDate) . '&status=completed&search=' ?>" class="card-link stretched-link mt-3">
                        Lihat Riwayat <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- 5. Order Gagal Bulan Ini -->
        <div class="col-xl-4 col-md-6">
            <div class="card dashboard-card bg-danger text-white h-100">
                <div class="card-body">
                    <div>
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h5 class="card-title">Order Gagal (Bulan Ini)</h5>
                                <p class="card-value"><?= $orderGagalBulanIni ?></p>
                            </div>
                            <i class="fas fa-times-circle card-icon"></i>
                        </div>
                    </div>
                    <a href="<?= base_url('pabrik/orders/history') . '?start_date=' . esc($thisMonthStartDate) . '&end_date=' . esc($thisMonthEndDate) . '&status=rejected&search=' ?>" class="card-link stretched-link mt-3">
                        Lihat Riwayat <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- 6. Tagihan Belum Lunas -->
        <div class="col-xl-4 col-md-6">
            <div class="card dashboard-card bg-secondary text-white h-100">
                <div class="card-body">
                    <div>
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h5 class="card-title">Tagihan Belum Lunas</h5>
                                <p class="card-value" style="font-size: 1.75rem;">Rp <?= number_format($tagihanBelumLunas, 0, ',', '.') ?></p>
                            </div>
                            <i class="fas fa-file-invoice-dollar card-icon"></i>
                        </div>
                    </div>
                    <a href="<?= base_url('pabrik/distributor-invoices?status=unpaid&invoice_number=&start_date=&end_date=') ?>" class="card-link stretched-link mt-3">
                        Lihat Tagihan <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- 7. Pendapatan Bulan Ini -->
        <div class="col-xl-4 col-md-6">
            <div class="card dashboard-card h-100" style="background-color: #28a745; color: white;">
                <div class="card-body">
                    <div>
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h5 class="card-title">Pendapatan (Bulan Ini)</h5>
                                <p class="card-value" style="font-size: 1.75rem;">Rp <?= number_format($pendapatanBulanIni, 0, ',', '.') ?></p>
                            </div>
                            <i class="fas fa-dollar-sign card-icon"></i>
                        </div>
                    </div>
                    <a href="<?= base_url('pabrik/distributor-invoices?status=paid&invoice_number=') . '&start_date=' . esc($thisMonthStartDate) . '&end_date=' . esc($thisMonthEndDate) ?>" class="card-link stretched-link mt-3">
                        Lihat Laporan <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- 8. Manajemen Pengiriman -->
        <div class="col-xl-4 col-md-6">
            <div class="card dashboard-card bg-dark text-white h-100">
                <div class="card-body">
                    <div>
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h5 class="card-title">Riwayat Pengiriman</h5>
                                <p class="card-value"><i class="fas fa-shipping-fast"></i></p>
                            </div>
                            <i class="fas fa-route card-icon"></i>
                        </div>
                    </div>
                    <a href="<?= base_url('pabrik/riwayat_pengiriman') ?>" class="card-link stretched-link mt-3">
                        Lihat History Pengiriman <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- 9. Data Distributor -->
        <div class="col-xl-4 col-md-6">
            <div class="card dashboard-card bg-light text-dark h-100">
                <div class="card-body">
                    <div>
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h5 class="card-title">Total Distributor</h5>
                                <p class="card-value"><?= $totalDistributors ?></p>
                            </div>
                            <i class="fas fa-users card-icon"></i>
                        </div>
                    </div>
                    <a href="<?= base_url('pabrik/users') ?>" class="card-link stretched-link mt-3">
                        Kelola Distributor <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bagian Grafik dan Tabel -->
    <div class="row mt-4">
        <!-- Grafik Penjualan -->
        <div class="col-lg-7 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-chart-line me-2"></i>Pendapatan Harian (30 Hari Terakhir)</h6>
                </div>
                <div class="card-body">
                    <div style="height: 350px;">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Distributor Aktif Terbaru -->
        <div class="col-lg-5 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-user-plus me-2"></i>Distributor Aktif Terbaru</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Username</th>
                                    <th>Tanggal Daftar</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($distributorAktifTerbaru)): ?>
                                    <tr>
                                        <td colspan="3" class="text-center">Belum ada distributor baru.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($distributorAktifTerbaru as $distributor): ?>
                                        <tr>
                                            <td><?= esc($distributor['username']) ?></td>
                                            <td><?= date('d M Y', strtotime($distributor['created_at'])) ?></td>
                                            <td><a href="<?= base_url('pabrik/users/edit/' . $distributor['id']) ?>" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></a></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('salesChart');
        if (!ctx) return;

        const labels = JSON.parse('<?= $chartLabels ?>');
        const dataValues = JSON.parse('<?= $chartData ?>');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    data: dataValues,
                    borderColor: '#4e73df',
                    backgroundColor: 'rgba(78, 115, 223, 0.05)',
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#4e73df',
                    pointRadius: 4,
                    pointHoverRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += new Intl.NumberFormat('id-ID', {
                                        style: 'currency',
                                        currency: 'IDR'
                                    }).format(context.parsed.y);
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value, index, values) {
                                return 'Rp ' + (value / 1000) + 'k';
                            }
                        }
                    }
                }
            }
        });
    });
</script>
<?= $this->endSection() ?>