<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('head'); ?>
<!-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> -->
<style>
    /* Menggunakan gaya yang konsisten dari halaman sebelumnya */
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

    /* Warna subtle Bootstrap 5.3 */
    .badge.bg-info {
        background-color: var(--bs-info-bg-subtle) !important;
        color: var(--bs-info-text-emphasis) !important;
        border: 1px solid var(--bs-info-border-subtle) !important;
    }

    .badge.bg-warning {
        background-color: var(--bs-warning-bg-subtle) !important;
        color: var(--bs-warning-text-emphasis) !important;
        border: 1px solid var(--bs-warning-border-subtle) !important;
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
        <a href="<?= base_url('pabrik/incoming-orders') ?>" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Kembali ke Order Masuk</a>
    </div>

    <!-- Flash Messages -->
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

    <!-- Kartu Informasi -->
    <div class="row g-4 mb-4">
        <!-- Kartu Ringkasan Order -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4"><i class="fas fa-info-circle text-primary me-2"></i>Ringkasan Order</h5>
                    <dl class="row info-list g-3">
                        <dt class="col-sm-5">Order ID</dt>
                        <dd class="col-sm-7 fw-bold">ORD-<?= esc($order['id']) ?></dd>
                        <dt class="col-sm-5">Tanggal Order</dt>
                        <dd class="col-sm-7"><?= date('d M Y, H:i', strtotime($order['order_date'])) ?></dd>
                        <dt class="col-sm-5">Status Order</dt>
                        <dd class="col-sm-7">
                            <?php
                            // Konfigurasi Badge Status
                            $statusConfig = [
                                'approved'   => ['class' => 'bg-info', 'text' => 'Disetujui', 'icon' => 'fas fa-thumbs-up'],
                                'processing' => ['class' => 'bg-warning', 'text' => 'Diproses', 'icon' => 'fas fa-cogs'],
                                'shipped'    => ['class' => 'bg-primary', 'text' => 'Dikirim', 'icon' => 'fas fa-truck'],
                            ];
                            $currentStatus = $statusConfig[strtolower($order['status'])] ?? ['class' => 'bg-secondary', 'text' => 'N/A', 'icon' => 'fas fa-question-circle'];
                            ?>
                            <span class="badge rounded-pill <?= $currentStatus['class'] ?> status-badge">
                                <i class="<?= $currentStatus['icon'] ?>"></i>
                                <span><?= esc($currentStatus['text']) ?></span>
                            </span>
                        </dd>
                        <dt class="col-sm-5">Total Bayar</dt>
                        <dd class="col-sm-7 fw-bold fs-5 text-success">Rp <?= esc(number_format($order['total_amount'], 0, ',', '.')) ?></dd>
                    </dl>
                </div>
            </div>
        </div>
        <!-- Kartu Informasi Kontak -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4"><i class="fas fa-users text-primary me-2"></i>Informasi Kontak & Tujuan</h5>
                    <dl class="row info-list g-3">
                        <dt class="col-sm-4">Distributor</dt>
                        <dd class="col-sm-8 fw-bold"><?= esc($distributor['username'] ?? 'N/A') ?></dd>
                        <dt class="col-sm-4">No. Telepon</dt>
                        <dd class="col-sm-8"><?= esc($distributor['no_telpon'] ?? 'N/A') ?></dd>

                        <?php if (isset($agen['username']) && !empty($agen['username'])): ?>
                            <dt class="col-sm-12">
                                <hr class="my-2">
                            </dt>
                            <dt class="col-sm-4">Agen Tujuan</dt>
                            <dd class="col-sm-8 fw-bold"><?= esc($agen['username']) ?></dd>
                            <dt class="col-sm-4">Alamat Agen</dt>
                            <dd class="col-sm-8"><?= esc($agen['alamat'] ?? 'N/A') ?></dd>
                        <?php endif; ?>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Kartu Rincian Item -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light">
            <h5 class="m-0"><i class="fas fa-boxes text-primary me-2"></i>Rincian Item Produk</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Produk</th>
                            <th class="text-center">Kuantitas</th>
                            <th class="text-end">Harga Satuan</th>
                            <th class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($orderItems)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-4">Tidak ada item dalam order ini.</td>
                            </tr>
                        <?php else: ?>
                            <?php $no = 1;
                            foreach ($orderItems as $item): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= esc($productDetails[$item['product_id']] ?? 'N/A') ?></td>
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

    <!-- Panel Aksi -->
    <?php if (in_array($order['status'], ['approved', 'processing'])): ?>
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h5 class="m-0"><i class="fas fa-tasks text-primary me-2"></i>Tindakan Selanjutnya</h5>
            </div>
            <div class="card-body text-end">
                <?php if ($order['status'] == 'approved'): ?>
                    <button type="button" class="btn btn-warning" onclick="confirmUpdateStatus(<?= $order['id'] ?>, 'processing', 'Proses Order')"><i class="fas fa-cogs me-2"></i>Mulai Proses Order</button>
                <?php elseif ($order['status'] == 'processing'): ?>
                    <a href="<?= base_url('pabrik/shipments/update-status/' . $order['id']) ?>" class="btn btn-primary"><i class="fas fa-truck me-2"></i>Buat Pengiriman untuk Order Ini</a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif ; ?>

</div>

<!-- FORM UNTUK UPDATE STATUS (Logika tidak berubah) -->
<form id="statusUpdateForm" action="" method="post" style="display: none;"></form>
    <?= csrf_field() ?>
    <input type="hidden" name="status" id="newStatusInput">
</form>

<script>
    // Fungsi konfirmasi dengan SweetAlert2 yang lebih kontekstual
    function confirmUpdateStatus(orderId, newStatus, actionText) {
        let config = {
            processing: {
                title: 'Mulai Proses Order?',
                text: `Anda akan mengubah status Order ID ORD-${orderId} menjadi DIPROSES.`,
                icon: 'info',
                confirmButtonColor: '#ffc107',
                confirmButtonText: 'Ya, Mulai Proses!'
            }
            // Konfigurasi lain bisa ditambahkan di sini jika perlu
        };

        let dialogConfig = config[newStatus] || {
            title: 'Konfirmasi Perubahan Status',
            text: `Anda yakin ingin mengubah status order ini menjadi "${actionText}"?`,
            icon: 'warning',
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Ubah!'
        };

        Swal.fire({
            title: dialogConfig.title,
            text: dialogConfig.text,
            icon: dialogConfig.icon,
            showCancelButton: true,
            confirmButtonColor: dialogConfig.confirmButtonColor,
            cancelButtonColor: '#6c757d',
            confirmButtonText: dialogConfig.confirmButtonText,
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('statusUpdateForm');
                form.action = `<?= base_url('pabrik/incoming-orders/update-status/') ?>${orderId}`;
                document.getElementById('newStatusInput').value = newStatus;
                form.submit();
            }
        });
    }
</script>
<?= $this->endSection() ?>