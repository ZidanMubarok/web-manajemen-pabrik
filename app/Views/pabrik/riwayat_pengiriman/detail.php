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
        height: 100%;
    }

    .card-header.bg-light {
        border-bottom: 1px solid #e3e6f0;
    }

    .info-list dt {
        font-weight: 600;
        color: #6c757d;
    }

    .info-list dd {
        font-weight: 500;
        color: #343a40;
    }

    .table-hover>tbody>tr:hover {
        background-color: rgba(var(--bs-primary-rgb), 0.05);
    }

    /* Gaya konsisten untuk status badge */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: .5rem;
        font-size: .9rem;
        padding: .5em .9em;
    }

    .status-badge i {
        font-size: 1.1em;
    }

    /* Warna subtle dari Bootstrap 5.3 */
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

    .badge.bg-warning {
        background-color: var(--bs-warning-bg-subtle) !important;
        color: var(--bs-warning-text-emphasis) !important;
        border: 1px solid var(--bs-warning-border-subtle) !important;
    }

    .badge.bg-info {
        background-color: var(--bs-info-bg-subtle) !important;
        color: var(--bs-info-text-emphasis) !important;
        border: 1px solid var(--bs-info-border-subtle) !important;
    }

    .badge.bg-primary {
        background-color: var(--bs-primary-bg-subtle) !important;
        color: var(--bs-primary-text-emphasis) !important;
        border: 1px solid var(--bs-primary-border-subtle) !important;
    }

    .badge.bg-secondary {
        background-color: var(--bs-secondary-bg-subtle) !important;
        color: var(--bs-secondary-text-emphasis) !important;
        border: 1px solid var(--bs-secondary-border-subtle) !important;
    }
</style>
<?= $this->endSection(); ?>


<?= $this->section('content') ?>
<div class="container-fluid pt-4">

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-file-invoice text-primary me-2"></i> <?= $title ?></h1>
        <a href="<?= base_url('pabrik/riwayat_pengiriman') ?>" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Riwayat
        </a>
    </div>

    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success d-flex align-items-center alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            <div><?= session()->getFlashdata('success') ?></div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Kartu Informasi Terstruktur -->
    <div class="row g-4 mb-4">
        <!-- Kartu Ringkasan Order & Pengiriman -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4"><i class="fas fa-file-alt text-primary me-2"></i>Ringkasan</h5>
                    <dl class="row info-list g-3">
                        <dt class="col-sm-5">ID Pengiriman</dt>
                        <dd class="col-sm-7 fw-bold">SHP-<?= esc($shipment['id']) ?></dd>
                        <dt class="col-sm-5">ID Order</dt>
                        <dd class="col-sm-7">ORD-<?= esc($order['id']) ?></dd>

                        <dt class="col-sm-12">
                            <hr class="my-2">
                        </dt>

                        <dt class="col-sm-5">Tanggal Order</dt>
                        <dd class="col-sm-7"><?= date('d M Y, H:i', strtotime($order['order_date'])) ?></dd>
                        <dt class="col-sm-5">Tanggal Kirim</dt>
                        <dd class="col-sm-7"><?= date('d M Y, H:i', strtotime($shipment['shipping_date'])) ?></dd>
                        <dt class="col-sm-5">Tanggal Selesai</dt>
                        <dd class="col-sm-7"><?= date('d M Y, H:i', strtotime($shipment['updated_at'])) ?></dd>

                        <dt class="col-sm-12">
                            <hr class="my-2">
                        </dt>

                        <dt class="col-sm-5">Status Order</dt>
                        <dd class="col-sm-7">
                            <?php
                            // Logika untuk Badge Status Order
                            $orderStatusConfig = [
                                'pending' => ['class' => 'bg-secondary', 'text' => 'Tertunda', 'icon' => 'fas fa-clock'],
                                'approved' => ['class' => 'bg-info', 'text' => 'Disetujui', 'icon' => 'fas fa-thumbs-up'],
                                'processing' => ['class' => 'bg-warning', 'text' => 'Diproses', 'icon' => 'fas fa-cogs'],
                                'shipped' => ['class' => 'bg-primary', 'text' => 'Dikirim', 'icon' => 'fas fa-truck'],
                                'completed' => ['class' => 'bg-success', 'text' => 'Selesai', 'icon' => 'fas fa-check-circle'],
                                'rejected' => ['class' => 'bg-danger', 'text' => 'Ditolak', 'icon' => 'fas fa-times-circle'],
                            ];
                            $currentOrderStatus = $orderStatusConfig[strtolower($order['status'])] ?? ['class' => 'bg-secondary', 'text' => 'N/A', 'icon' => 'fas fa-question-circle'];
                            ?>
                            <span class="badge rounded-pill <?= $currentOrderStatus['class'] ?> status-badge"><i class="<?= $currentOrderStatus['icon'] ?>"></i> <span><?= esc($currentOrderStatus['text']) ?></span></span>
                        </dd>

                        <dt class="col-sm-5">Status Pengiriman</dt>
                        <dd class="col-sm-7">
                            <?php
                            // Logika untuk Badge Status Pengiriman
                            $shipmentStatusConfig = [
                                'delivered' => ['class' => 'bg-success', 'text' => 'Terkirim', 'icon' => 'fas fa-check-circle'],
                                'failed' => ['class' => 'bg-danger', 'text' => 'Gagal', 'icon' => 'fas fa-times-circle'],
                            ];
                            $currentShipmentStatus = $shipmentStatusConfig[strtolower($shipment['delivery_status'])] ?? ['class' => 'bg-secondary', 'text' => 'N/A', 'icon' => 'fas fa-question-circle'];
                            ?>
                            <span class="badge rounded-pill <?= $currentShipmentStatus['class'] ?> status-badge"><i class="<?= $currentShipmentStatus['icon'] ?>"></i> <span><?= esc($currentShipmentStatus['text']) ?></span></span>
                        </dd>

                        <dt class="col-sm-5">Nomor Resi</dt>
                        <dd class="col-sm-7"><span class="font-monospace"><?= esc($shipment['tracking_number'] ?? '-') ?></span></dd>
                    </dl>
                </div>
            </div>
        </div>
        <!-- Kartu Informasi Tujuan -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4"><i class="fas fa-map-signs text-primary me-2"></i>Informasi Tujuan & Kontak</h5>
                    <dl class="row info-list g-3">
                        <dt class="col-sm-4">Distributor</dt>
                        <dd class="col-sm-8 fw-bold"><?= esc($distributor['username'] ?? 'N/A') ?></dd>
                        <dt class="col-sm-4">Email</dt>
                        <dd class="col-sm-8"><?= esc($distributor['email'] ?? 'N/A') ?></dd>
                        <dt class="col-sm-4">No. Telepon</dt>
                        <dd class="col-sm-8"><?= esc($distributor['no_telpon'] ?? 'N/A') ?></dd>

                        <?php if (isset($agen['username']) && !empty($agen['username'])): ?>
                            <dt class="col-sm-12">
                                <hr class="my-2">
                            </dt>
                            <dt class="col-sm-4">Agen</dt>
                            <dd class="col-sm-8 fw-bold"><?= esc($agen['username']) ?></dd>
                            <dt class="col-sm-4">Email</dt>
                            <dd class="col-sm-8"><?= esc($agen['email'] ?? 'N/A') ?></dd>
                            <dt class="col-sm-4">No. Telepon</dt>
                            <dd class="col-sm-8"><?= esc($agen['no_telpon'] ?? 'N/A') ?></dd>
                            <dt class="col-sm-4">Alamat</dt>
                            <dd class="col-sm-8"><?= esc($agen['alamat'] ?? 'N/A') ?></dd>
                        <?php else: ?>
                            <dt class="col-sm-12">
                                <hr class="my-2">
                            </dt>
                            <dd class="col-sm-12 text-muted fst-italic">Pengiriman ini ditujukan langsung ke distributor.</dd>
                        <?php endif; ?>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Kartu Rincian Item -->
    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <h5 class="m-0"><i class="fas fa-boxes text-primary me-2"></i>Rincian Item Produk (Total: Rp <?= esc(number_format($order['total_amount'], 0, ',', '.')) ?>)</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>Produk</th>
                            <th class="text-center">Kuantitas</th>
                            <th class="text-end">Harga Satuan</th>
                            <th class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($orderItems)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    <i class="fas fa-exclamation-circle me-2"></i>Tidak ada item produk dalam order ini.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php $no = 1;
                            foreach ($orderItems as $item): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= esc($productDetails[$item['product_id']] ?? 'Produk Tidak Ditemukan') ?></td>
                                    <td class="text-center"><?= esc($item['quantity']) ?></td>
                                    <td class="text-end">Rp <?= esc(number_format($item['unit_price'], 0, ',', '.')) ?></td>
                                    <td class="text-end fw-bold">Rp <?= esc(number_format($item['sub_total'], 0, ',', '.')) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
<?= $this->endSection() ?>