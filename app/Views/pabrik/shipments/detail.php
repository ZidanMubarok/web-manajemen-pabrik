<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('head'); ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
    /* Menggunakan gaya yang sama seperti halaman sebelumnya untuk konsistensi */
    body {
        background-color: #f8f9fc;
    }

    .card {
        border: none;
        border-radius: .75rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease-in-out;
        height: 100%;
        /* Membuat kartu dalam satu baris sama tinggi */
    }

    .card-header.bg-primary {
        border-top-left-radius: .75rem;
        border-top-right-radius: .75rem;
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

    /* Styling untuk Status Badge dengan Ikon */
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

    /* Menggunakan warna background subtle dari Bootstrap 5.3 */
    .badge.bg-warning {
        background-color: var(--bs-warning-bg-subtle) !important;
        color: var(--bs-warning-text-emphasis) !important;
        border: 1px solid var(--bs-warning-border-subtle) !important;
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
        <a href="<?= base_url('pabrik/shipments/history') ?>" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <!-- Informasi Utama dalam Kartu Terpisah -->
    <div class="row g-4 mb-4">
        <!-- Kartu Informasi Pengiriman -->
        <div class="col-lg-5">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4"><i class="fas fa-shipping-fast me-2 text-primary"></i>Informasi Pengiriman</h5>
                    <dl class="row info-list g-3">
                        <dt class="col-sm-5">ID Pengiriman</dt>
                        <dd class="col-sm-7 fw-bold">SHP-<?= esc($shipment['id']) ?></dd>

                        <dt class="col-sm-5">Order Terkait</dt>
                        <dd class="col-sm-7">ORD-<?= esc($shipment['order_id']) ?></dd>

                        <dt class="col-sm-5">Tgl. Kirim</dt>
                        <dd class="col-sm-7"><?= date('d M Y, H:i', strtotime($shipment['shipping_date'])) ?></dd>

                        <dt class="col-sm-5">Nomor Resi</dt>
                        <dd class="col-sm-7"><span class="font-monospace"><?= esc($shipment['tracking_number'] ?? '-') ?></span></dd>

                        <dt class="col-sm-5">Status</dt>
                        <dd class="col-sm-7">
                            <?php
                            $status = strtolower($shipment['delivery_status']);
                            $config = [
                                'pending' => ['class' => 'bg-secondary', 'text' => 'Tertunda', 'icon' => 'fas fa-clock'],
                                'on_transit' => ['class' => 'bg-warning', 'text' => 'Sedang Transit', 'icon' => 'fas fa-truck-loading'],
                                'delivered' => ['class' => 'bg-success', 'text' => 'Terkirim', 'icon' => 'fas fa-check-circle'],
                                'failed' => ['class' => 'bg-danger', 'text' => 'Gagal', 'icon' => 'fas fa-times-circle'],
                            ];
                            $current = $config[$status] ?? ['class' => 'bg-dark', 'text' => 'Tidak Diketahui', 'icon' => 'fas fa-question-circle'];
                            ?>
                            <span class="badge rounded-pill <?= $current['class'] ?> status-badge">
                                <i class="<?= $current['icon'] ?>"></i>
                                <span><?= esc($current['text']) ?></span>
                            </span>
                        </dd>

                        <dt class="col-sm-5">Tgl. Diterima</dt>
                        <dd class="col-sm-7"><?= !empty($shipment['delivery_date']) ? date('d M Y, H:i', strtotime($shipment['delivery_date'])) : '-' ?></dd>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Kartu Informasi Tujuan -->
        <div class="col-lg-7">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4"><i class="fas fa-map-marker-alt me-2 text-primary"></i>Informasi Tujuan</h5>
                    <dl class="row info-list g-3">
                        <dt class="col-sm-4">Nama Distributor</dt>
                        <dd class="col-sm-8"><?= esc($distributor['username'] ?? 'N/A') ?></dd>

                        <dt class="col-sm-4">No. Telpon</dt>
                        <dd class="col-sm-8"><?= esc($distributor['no_telpon'] ?? 'N/A') ?></dd>

                        <dt class="col-12">
                            <hr class="my-2">
                        </dt>

                        <?php if ($agen): ?>
                            <dt class="col-sm-4">Agen Tujuan</dt>
                            <dd class="col-sm-8 fw-bold"><?= esc($agen['username']) ?></dd>
                            <dt class="col-sm-4">No. Telpon Agen</dt>
                            <dd class="col-sm-8"><?= esc($agen['no_telpon']) ?></dd>
                            <dt class="col-sm-4">Alamat Agen</dt>
                            <dd class="col-sm-8"><?= esc($agen['alamat']) ?></dd>
                        <?php else: ?>
                            <dt class="col-sm-12">Tujuan pengiriman ini adalah langsung ke distributor.</dt>
                        <?php endif; ?>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Kartu Rincian Item -->
    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <h5 class="m-0"><i class="fas fa-box me-2 text-primary"></i>Rincian Item Produk</h5>
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
                                    <td><?= esc($productNames[$item['product_id']] ?? 'N/A') ?></td>
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

    <!-- Action Buttons -->
    <?php if ($shipment['delivery_status'] === 'on_transit'): ?>
        <div class="card mt-4">
            <div class="card-body d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-danger" onclick="updateShipmentStatus(<?= $shipment['id'] ?>, 'failed')">
                    <i class="fas fa-times-circle me-2"></i> Tandai Gagal
                </button>
                <button type="button" class="btn btn-success" onclick="updateShipmentStatus(<?= $shipment['id'] ?>, 'delivered')">
                    <i class="fas fa-check-circle me-2"></i> Tandai Sudah Diterima
                </button>
            </div>
        </div>
    <?php endif; ?>

</div>

<!-- FORM HIDDEN UNTUK UPDATE STATUS -- Logika tidak berubah -->
<form id="shipmentStatusUpdateFormDetail" action="" method="post" style="display: none;">
    <?= csrf_field() ?>
    <input type="hidden" name="status" id="newShipmentStatusInputDetail">
</form>

<!-- SCRIPT UPDATE STATUS -- Disesuaikan untuk menargetkan ID yang benar -->
<script>
    function updateShipmentStatus(shipmentId, newStatus) {
        let config = {
            delivered: {
                title: 'Konfirmasi Penerimaan',
                html: `Anda yakin pengiriman ini telah <strong>diterima</strong>? Status akan diubah dan tidak dapat dikembalikan.`,
                confirmButtonText: 'Ya, Diterima',
                confirmButtonColor: '#198754',
                icon: 'success'
            },
            failed: {
                title: 'Konfirmasi Kegagalan',
                html: `Anda yakin pengiriman ini <strong>gagal</strong> terkirim? Status akan diubah dan tidak dapat dikembalikan.`,
                confirmButtonText: 'Ya, Gagal Kirim',
                confirmButtonColor: '#dc3545',
                icon: 'error'
            }
        };

        let dialogConfig = config[newStatus];

        Swal.fire({
            title: dialogConfig.title,
            html: dialogConfig.html,
            icon: dialogConfig.icon,
            showCancelButton: true,
            confirmButtonColor: dialogConfig.confirmButtonColor,
            cancelButtonColor: '#6c757d',
            confirmButtonText: dialogConfig.confirmButtonText,
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Pastikan menargetkan ID form & input yang benar di halaman ini
                const form = document.getElementById('shipmentStatusUpdateFormDetail');
                form.action = `<?= base_url('pabrik/shipments/update-status/') ?>${shipmentId}`;
                document.getElementById('newShipmentStatusInputDetail').value = newStatus;
                form.submit();
            }
        });
    }
</script>
<?= $this->endSection() ?>