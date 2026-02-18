<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('title') ?>
<?= esc($title) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= esc($title) ?></h1>
        <!-- Tombol diubah menjadi button, bukan link -->
        <button id="cetakPdfButton" class="d-none d-sm-inline-block btn btn-sm btn-danger shadow-sm">
            <i class="fas fa-download fa-sm text-white-50"></i> Cetak Laporan (PDF)
        </button>
    </div>
    <div class="card shadow mb-4">
        <div class="card-header py-3" data-bs-toggle="collapse" href="#collapseFilter" role="button" aria-expanded="true" aria-controls="collapseFilter">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-filter me-2"></i> Filter Berdasarkan Tanggal Order</h6>
        </div>
        <div class="collapse show" id="collapseFilter">
            <div class="card-body">
                <form action="<?= base_url('pabrik/monitoring_distributor') ?>" method="get" class="row g-3 align-items-end">
                    <div class="col-md-5">
                        <label for="start_date" class="form-label">Dari Tanggal</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" value="<?= esc($startDate) ?>">
                    </div>
                    <div class="col-md-5">
                        <label for="end_date" class="form-label">Sampai Tanggal</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" value="<?= esc($endDate) ?>">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search me-1"></i> Terapkan</button>
                        <a href="<?= base_url('pabrik/monitoring_distributor') ?>" class="btn btn-secondary w-100 mt-2"><i class="fas fa-sync me-1"></i> Reset</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Chart Section (Sama seperti sebelumnya) -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-primary text-white">
            <h6 class="m-0 font-weight-bold"><i class="fas fa-chart-bar me-2"></i> Visualisasi Performa Distributor</h6>
        </div>
        <div class="card-body">
            <div class="chart-area" style="height: 320px;"><canvas id="distributorChart"></canvas></div>
        </div>
    </div>

    <!-- Table Section, yang akan kita cetak -->
    <div id="laporanContainer" class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-table me-2"></i> Data Detail Performa Distributor</h6>
        </div>
        <div class="card-body">
            <?php if (empty($distributorPerformance)): ?>
                <div class="alert alert-info text-center">Belum ada data performa distributor.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <?php
                                // FUNGSI SORTLINK DIPERBARUI UNTUK MEMBAWA FILTER
                                function sortLink($title, $column, $currentSort, $currentOrder, $startDate, $endDate)
                                {
                                    $order = ($currentSort == $column && $currentOrder == 'asc') ? 'desc' : 'asc';
                                    $icon = ($currentSort == $column) ? ($currentOrder == 'asc' ? ' <i class="fas fa-sort-up"></i>' : ' <i class="fas fa-sort-down"></i>') : '';

                                    // Bangun query string dengan semua parameter yang ada
                                    $queryParams = http_build_query(array_filter([
                                        'sort' => $column,
                                        'order' => $order,
                                        'start_date' => $startDate,
                                        'end_date' => $endDate
                                    ]));

                                    return '<a href="?' . $queryParams . '">' . $title . $icon . '</a>';
                                }
                                ?>
                                <th>No</th>
                                <th><?= sortLink('Nama Distributor', 'username', $sortColumn, $sortOrder, $startDate, $endDate) ?></th>
                                <th class="text-center"><?= sortLink('Total Order', 'total_orders', $sortColumn, $sortOrder, $startDate, $endDate) ?></th>
                                <th class="text-center"><?= sortLink('Total Kuantitas', 'total_quantity', $sortColumn, $sortOrder, $startDate, $endDate) ?></th>
                                <th class="text-center"><?= sortLink('Order Aktif', 'active_orders', $sortColumn, $sortOrder, $startDate, $endDate) ?></th>
                                <th class="text-center"><?= sortLink('Tagihan Lunas', 'paid_invoices', $sortColumn, $sortOrder, $startDate, $endDate) ?></th>
                                <th class="text-center"><?= sortLink('Belum Lunas', 'unpaid_invoices', $sortColumn, $sortOrder, $startDate, $endDate) ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = ($currentPage - 1) * $perPage + 1;
                            foreach ($distributorPerformance as $dp): ?>
                                <tr>
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td><?= esc($dp['username']) ?></td>
                                    <td class="text-center"><?= esc($dp['total_orders']) ?></td>
                                    <td class="text-center"><?= esc(number_format($dp['total_quantity'] ?? 0, 0, ',', '.')) ?></td>
                                    <td class="text-center"><?= esc($dp['active_orders']) ?></td>
                                    <td class="text-center text-success font-weight-bold"><?= esc($dp['paid_invoices']) ?></td>
                                    <td class="text-center text-danger font-weight-bold"><?= esc($dp['unpaid_invoices']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="mt-3 d-flex justify-content-end">
                    <!-- Pastikan Pager Anda sudah dikonfigurasi dengan baik di app/Config/Pager.php -->
                    <?= $pager->links('group1', 'bootstrap_pagination') ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- Muat Chart.js, jspdf, dan html2canvas dari CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<script>
    // Logika Chart (Sama seperti sebelumnya)
    document.addEventListener("DOMContentLoaded", function() {
        const performanceData = <?= json_encode($distributorPerformance) ?>;
        if (performanceData.length > 0) {
            const labels = performanceData.map(dp => dp.username);
            const totalOrdersData = performanceData.map(dp => dp.total_orders);
            new Chart(document.getElementById('distributorChart'), {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Total Order',
                        data: totalOrdersData,
                        backgroundColor: 'rgba(78, 115, 223, 0.8)'
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        // **LOGIKA BARU: Cetak PDF dengan jsPDF dan html2canvas**
        const cetakButton = document.getElementById('cetakPdfButton');
        const laporanContainer = document.getElementById('laporanContainer');

        cetakButton.addEventListener('click', function() {
            const originalButtonText = this.innerHTML;
            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Mencetak...';

            // Opsi untuk html2canvas agar kualitas lebih baik
            const options = {
                scale: 2, // Meningkatkan resolusi gambar
                useCORS: true,
                logging: false,
            };

            html2canvas(laporanContainer, options).then(canvas => {
                const imgData = canvas.toDataURL('image/png');
                const {
                    jsPDF
                } = window.jspdf;

                // Buat PDF dalam orientasi landscape (L) atau portrait (P)
                const pdf = new jsPDF('l', 'mm', 'a4');

                const pdfWidth = pdf.internal.pageSize.getWidth();
                const pdfHeight = pdf.internal.pageSize.getHeight();
                const canvasWidth = canvas.width;
                const canvasHeight = canvas.height;
                const ratio = canvasWidth / canvasHeight;

                // Sesuaikan lebar gambar dengan lebar PDF dikurangi margin
                let imgWidth = pdfWidth - 20; // 10mm margin kiri + 10mm margin kanan
                let imgHeight = imgWidth / ratio;

                // Jika tinggi gambar melebihi tinggi PDF, sesuaikan berdasarkan tinggi
                if (imgHeight > pdfHeight - 20) {
                    imgHeight = pdfHeight - 20;
                    imgWidth = imgHeight * ratio;
                }

                const x = (pdfWidth - imgWidth) / 2; // Posisi X agar gambar di tengah
                const y = 10; // Posisi Y (10mm dari atas)

                pdf.addImage(imgData, 'PNG', x, y, imgWidth, imgHeight);

                const tgl = new Date().toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: 'long',
                    year: 'numeric'
                });
                pdf.save(`Laporan_Distributor_${tgl}.pdf`);

                // Kembalikan tombol ke keadaan semula
                this.disabled = false;
                this.innerHTML = originalButtonText;

            }).catch(err => {
                console.error("Gagal membuat PDF:", err);
                alert("Maaf, terjadi kesalahan saat membuat PDF.");
                // Kembalikan tombol ke keadaan semula
                this.disabled = false;
                this.innerHTML = originalButtonText;
            });
        });
    });
</script>
<?= $this->endSection() ?>