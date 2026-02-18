<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('head'); ?>
<!-- Script untuk Notifikasi Opsional jika dibutuhkan -->
<style>
    /* Menggunakan gaya yang konsisten dengan halaman sebelumnya */
    body {
        background-color: #f8f9fc;
    }

    .card {
        border: none;
        border-radius: .75rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease-in-out;
    }

    .card:hover {
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
    }

    .card-header.bg-primary {
        border-top-left-radius: .75rem;
        border-top-right-radius: .75rem;
    }

    .table-hover>tbody>tr:hover {
        background-color: rgba(var(--bs-primary-rgb), 0.05);
    }

    .form-floating>.form-control {
        height: calc(3.5rem + 2px);
        padding: 1rem .75rem;
    }

    .form-floating>label {
        padding: 1rem .75rem;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: .5rem;
        font-size: .85rem;
        padding: .45em .8em;
    }

    .badge.bg-success {
        background-color: var(--bs-success-bg-subtle) !important;
        color: var(--bs-success-text-emphasis) !important;
        border: 1px solid var(--bs-success-border-subtle) !important;
    }

    .badge.bg-danger {
        background-color: var(--bs-danger-bg-subtle) !important;
        color: var(--bs-danger-text-emphasis) !important;
        border: 1px solid var(--bs-danger-border-subtle) !important;
    }

    .pagination .page-link {
        border-radius: .375rem;
        margin: 0 .2rem;
    }

    .pagination .page-item.active .page-link {
        box-shadow: 0 4px 8px rgba(var(--bs-primary-rgb), 0.3);
    }
</style>
<?= $this->endSection(); ?>

<?= $this->section('content') ?>
<div class="container-fluid pt-4">

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-history text-primary me-2"></i> <?= $title ?></h1>
    </div>

    <!-- Notifikasi/Flash Message yang lebih baik -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success d-flex align-items-center alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            <div><?= session()->getFlashdata('success') ?></div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger d-flex align-items-center alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <div><?= session()->getFlashdata('error') ?></div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Kartu Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3"><i class="fas fa-filter me-2"></i> Filter Riwayat</h5>
            <form action="<?= base_url('pabrik/riwayat_pengiriman') ?>" method="get" class="row g-3 align-items-end">
                <div class="col-lg-4 col-md-12">
                    <div class="form-floating">
                        <input type="text" name="search" id="search" class="form-control" placeholder="Cari ID, Resi, Distributor..." value="<?= esc($search ?? '') ?>">
                        <label for="search">Cari ID, Resi, Distributor, Agen</label>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="form-floating">
                        <input type="date" name="start_date" id="start_date" class="form-control" value="<?= esc($startDate ?? '') ?>">
                        <label for="start_date">Dari Tanggal</label>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="form-floating">
                        <input type="date" name="end_date" id="end_date" class="form-control" value="<?= esc($endDate ?? '') ?>">
                        <label for="end_date">Sampai Tanggal</label>
                    </div>
                </div>
                <div class="col-lg-2 col-md-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100 h-100">
                        <i class="fas fa-search me-1"></i> Cari
                    </button>
                    <?php if (!empty($search) || !empty($startDate) || !empty($endDate)): ?>
                        <a href="<?= base_url('pabrik/riwayat_pengiriman') ?>" class="btn btn-outline-secondary" data-bs-toggle="tooltip" title="Reset Filter">
                            <i class="fas fa-sync-alt"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <!-- Kartu Data Riwayat Pengiriman -->
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h6 class="m-0 font-weight-bold"><i class="fas fa-list-ul me-2"></i> Daftar Riwayat Pengiriman</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle" id="dataTable" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>ID Pengiriman</th>
                            <th>Tujuan</th>
                            <th>Tanggal Kirim</th>
                            <th>Tanggal Selesai</th>
                            <th class="text-center">Status</th>
                            <th>No. Resi</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($shipments)): ?>
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                                    <h5 class="mb-1">Riwayat Tidak Ditemukan</h5>
                                    <p class="text-muted">Tidak ada data riwayat pengiriman yang cocok dengan filter Anda.</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php
                            $currentPage = $pager->getCurrentPage();
                            $perPage = $pager->getPerPage();
                            $no = (($currentPage - 1) * $perPage) + 1;
                            foreach ($shipments as $shipment): ?>
                                <?php $order = $ordersData[$shipment['order_id']] ?? null; ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td>
                                        <div class="fw-bold">SHP-<?= esc($shipment['id']) ?></div>
                                        <small class="text-muted">ORD-<?= esc($shipment['order_id']) ?></small>
                                    </td>
                                    <td>
                                        <div class="fw-bold"><?= esc($distributorUsernames[$order['distributor_id']] ?? 'N/A') ?></div>
                                        <small class="text-muted"><?= esc($agenUsernames[$order['agen_id']] ?? 'Tujuan: Distributor') ?></small>
                                    </td>
                                    <td><?= date('d M Y', strtotime($shipment['shipping_date'])) ?></td>
                                    <td><?= date('d M Y', strtotime($shipment['updated_at'])) ?></td>
                                    <td class="text-center">
                                        <?php
                                        $isDelivered = strtolower($shipment['delivery_status']) === 'delivered';
                                        ?>
                                        <span class="badge rounded-pill <?= $isDelivered ? 'bg-success' : 'bg-danger' ?> status-badge">
                                            <i class="fas <?= $isDelivered ? 'fa-check-circle' : 'fa-times-circle' ?>"></i>
                                            <span><?= $isDelivered ? 'Terkirim' : 'Gagal' ?></span>
                                        </span>
                                    </td>
                                    <td><span class="font-monospace"><?= esc($shipment['tracking_number'] ?? '-') ?></span></td>
                                    <td class="text-center">
                                        <a href="<?= base_url('pabrik/riwayat_pengiriman/detail/' . $shipment['id']) ?>" class="btn btn-info btn-sm" data-bs-toggle="tooltip" title="Lihat Detail Riwayat">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Informasi Total Data dan Pagination Links -->
            <div class="d-flex justify-content-between align-items-center mt-4 flex-wrap gap-3">
                <div class="text-muted">
                    <?php
                    $totalItems = $pager->getTotal();
                    $firstItem = ($totalItems > 0) ? (($pager->getCurrentPage() - 1) * $pager->getPerPage()) + 1 : 0;
                    $lastItem = ($totalItems > 0) ? min($firstItem + $pager->getPerPage() - 1, $totalItems) : 0;
                    ?>
                    Menampilkan <b><?= $firstItem ?></b>-<b><?= $lastItem ?></b> dari <b><?= $totalItems ?></b> data
                </div>
                <div>
                    <?= $pager->links('default', 'bootstrap_pagination') ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Inisialisasi Tooltip Bootstrap 5
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
</script>
<?= $this->endSection() ?>