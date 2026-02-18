<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('head'); ?>
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

    /* ### PERUBAHAN: Menambahkan style agar header filter bisa diklik ### */
    .card-header[data-bs-toggle="collapse"] {
        cursor: pointer;
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

    <!-- Page Header (Tidak diubah) -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-history text-primary me-2"></i> <?= $title ?></h1>
    </div>

    <!-- Notifikasi/Flash Message (Tidak diubah) -->
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

    <!-- ### PERUBAHAN DIMULAI: Kartu Filter Didesain Ulang ### -->
    <div class="card shadow-sm mb-4">
        <div class="card-header py-3" data-bs-toggle="collapse" href="#collapseFilter" role="button" aria-expanded="true" aria-controls="collapseFilter">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-filter me-2"></i> Filter Riwayat</h6>
        </div>
        <div class="collapse show" id="collapseFilter">
            <div class="card-body">
                <form action="<?= base_url('pabrik/riwayat_pengiriman') ?>" method="get" class="row g-3 align-items-end">
                    <div class="col-lg-3 col-md-6">
                        <label for="search" class="form-label">Cari ID, Resi, Tujuan:</label>
                        <input type="text" name="search" id="search" class="form-control" placeholder="Ketik kata kunci..." value="<?= esc($search ?? '') ?>">
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <label for="delivery_status" class="form-label">Status Pengiriman:</label>
                        <select name="delivery_status" id="delivery_status" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="delivered" <?= ($deliveryStatus ?? '') == 'delivered' ? 'selected' : '' ?>>Terkirim</option>
                            <option value="failed" <?= ($deliveryStatus ?? '') == 'failed' ? 'selected' : '' ?>>Gagal</option>
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <label for="start_date" class="form-label">Dari Tanggal Selesai:</label>
                        <input type="date" name="start_date" id="start_date" class="form-control" value="<?= esc($startDate ?? '') ?>">
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <label for="end_date" class="form-label">Sampai Tanggal Selesai:</label>
                        <input type="date" name="end_date" id="end_date" class="form-control" value="<?= esc($endDate ?? '') ?>">
                    </div>
                    <div class="col-lg-3 col-md-12">
                        <div class="btn-group w-100 gap-1" role="group">
                            <?php
                            // Logika untuk parameter filter tidak diubah
                            $filterParams = [
                                'search'          => $search ?? '',
                                'start_date'      => $startDate ?? '',
                                'end_date'        => $endDate ?? '',
                                'delivery_status' => $deliveryStatus ?? ''
                            ];
                            $activeFilters = array_filter($filterParams);
                            $exportQuery = http_build_query($activeFilters);
                            ?>
                            <button type="submit" class="btn btn-primary" title="Terapkan Filter">
                                <i class="fas fa-search"></i> Filter
                            </button>
                            <a href="<?= base_url('pabrik/riwayat_pengiriman/export') . ($exportQuery ? '?' . $exportQuery : '') ?>" class="btn btn-success" data-bs-toggle="tooltip" title="Export ke Excel">
                                <i class="fas fa-file-excel"></i>
                            </a>
                            <?php if (!empty($activeFilters)): ?>
                                <a href="<?= base_url('pabrik/riwayat_pengiriman') ?>" class="btn btn-secondary" data-bs-toggle="tooltip" title="Reset Filter">
                                    <i class="fas fa-sync-alt"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- ### PERUBAHAN SELESAI ### -->

    <!-- Kartu Data Riwayat Pengiriman (Tidak diubah) -->
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
                            <th>Distributor/Agen</th>
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
                            // Logika Pager tidak diubah
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
                                        // Logika status tidak diubah
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

            <!-- Informasi Total Data dan Pagination (Tidak diubah) -->
            <div class="d-flex justify-content-between align-items-center mt-4 flex-wrap gap-3">
                <div class="text-muted">
                    <?php
                    $totalItems = $pager->getTotal();
                    $firstItem = ($totalItems > 0) ? (($pager->getCurrentPage() - 1) * $pager->getPerPage()) + 1 : 0;
                    $lastItem = ($totalItems > 0) ? min($firstItem + $pager->getPerPage() - 1, $totalItems) : 0;
                    ?>
                    Menampilkan <b><?= $firstItem ?></b>-<b><?= $lastItem ?></b> dari <b><?= $totalItems ?></b> Riwayat Pengiriman.
                </div>
                <div>
                    <?= $pager->links('default', 'bootstrap_pagination') ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Script (Tidak diubah) -->
<script>
    // Inisialisasi Tooltip Bootstrap 5
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
</script>
<?= $this->endSection() ?>