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

    <div class="row mt-5">
        <!-- Grafik Pendapatan -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm h-100">
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

        <!-- Grafik Produk Terlaris -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-chart-pie me-2"></i>Produk Terlaris</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2" style="height: 350px;">
                        <canvas id="topProductsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Daftar Distributor Aktif Terbaru -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-user-plus me-2"></i>Distributor Daftar Terbaru(6)</h6>
                </div>
                <div class="card">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Username Distributor</th>
                                    <th>No Telpon Distributor</th>
                                    <th>Email Distributor</th>
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
                                            <td><?= esc($distributor['no_telpon']) ?></td>
                                            <td><?= esc($distributor['email']) ?></td>
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
        <!-- Grafik Status Order Bulan Ini -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-chart-pie me-2"></i>Status Order Bulan Ini</h6>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center">
                    <div style="height: 300px; width: 100%;">
                        <canvas id="orderStatusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- Grafik Pendapatan (Line Chart) ---
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

        // --- [BARU] Grafik Produk Terlaris (Doughnut Chart) ---
        const doughnutCtx = document.getElementById('topProductsChart');
        if (doughnutCtx) {
            const topProductLabels = JSON.parse('<?= $topProductLabels; ?>');
            const topProductData = JSON.parse('<?= $topProductData; ?>');

            new Chart(doughnutCtx, {
                type: 'doughnut',
                data: {
                    labels: topProductLabels.length > 0 ? topProductLabels : ["Data Kosong"],
                    datasets: [{
                        data: topProductData.length > 0 ? topProductData : [1],
                        backgroundColor: topProductData.length > 0 ? [
                            '#4e73df',
                            '#1cc88a',
                            '#36b9cc',
                            '#f6c23e',
                            '#e74a3b',
                        ] : ['#f0f0f0'],
                        hoverBackgroundColor: topProductData.length > 0 ? [
                            '#2e59d9',
                            '#17a673',
                            '#2c9faf',
                            '#f4b619',
                            '#e02d1b'
                        ] : ['#e0e0e0'],
                        hoverBorderColor: "rgba(234, 236, 244, 1)",
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    responsive: true,
                    plugins: {
                        legend: {
                            display: topProductLabels.length > 0, // Sembunyikan jika tidak ada data
                            position: 'bottom',
                            labels: {
                                boxWidth: 12,
                                padding: 15
                            }
                        },
                        tooltip: {
                            enabled: topProductData.length > 0, // Nonaktifkan jika tidak ada data
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed !== null) {
                                        label += context.parsed.toLocaleString('id-ID') + ' unit terjual';
                                    }
                                    return label;
                                }
                            }
                        }
                    },
                    cutout: '75%',
                },
            });
        }
        // --- [BARU] Grafik Status Order Bulan Ini (Pie Chart) ---
        const pieCtx = document.getElementById('orderStatusChart');
        if (pieCtx) {
            const orderStatusLabels = JSON.parse('<?= $orderStatusLabels; ?>');
            const orderStatusData = JSON.parse('<?= $orderStatusData; ?>');

            const statusColors = {
                'Pending': '#f6c23e', // Kuning
                'Disetujui': '#4e73df', // Biru
                'Diproses': '#36b9cc', // Cyan
                'Dikirim': '#1cc88a', // Hijau Muda
                'Selesai': '#28a745', // Hijau Tua
                'Ditolak': '#e74a3b', // Merah
            };
            const backgroundColors = orderStatusLabels.map(label => statusColors[label] || '#858796');

            new Chart(pieCtx, {
                type: 'pie',
                data: {
                    labels: orderStatusLabels.length > 0 ? orderStatusLabels : ["Belum Ada Order"],
                    datasets: [{
                        data: orderStatusData.length > 0 ? orderStatusData : [1],
                        backgroundColor: orderStatusData.length > 0 ? backgroundColors : ['#f0f0f0'],
                        hoverBorderColor: "rgba(234, 236, 244, 1)",
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    responsive: true,
                    plugins: {
                        legend: {
                            display: orderStatusLabels.length > 0,
                            position: 'bottom',
                        },
                        tooltip: {
                            enabled: orderStatusData.length > 0,
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    let value = context.parsed;
                                    let total = context.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                                    let percentage = total > 0 ? ((value / total) * 100).toFixed(1) + '%' : '0%';
                                    return ` ${label}: ${value} (${percentage})`;
                                }
                            }
                        }
                    },
                }
            });
        }
    });
</script>
<?= $this->endSection() ?>