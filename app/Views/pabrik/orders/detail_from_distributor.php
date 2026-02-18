<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('head'); ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-file-invoice-dollar text-primary me-2"></i> <?= $title ?></h1>
        <a href="<?= base_url('pabrik/orders/history') ?>" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Kembali ke Riwayat</a>
    </div>

    <!-- Struktur Informasi dalam Kartu Terpisah -->
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
                                'pending'    => ['class' => 'bg-secondary', 'text' => 'Tertunda', 'icon' => 'fas fa-clock'],
                                'approved'   => ['class' => 'bg-info', 'text' => 'Disetujui', 'icon' => 'fas fa-thumbs-up'],
                                'processing' => ['class' => 'bg-warning', 'text' => 'Diproses', 'icon' => 'fas fa-cogs'],
                                'shipped'    => ['class' => 'bg-primary', 'text' => 'Dikirim', 'icon' => 'fas fa-truck'],
                                'completed'  => ['class' => 'bg-success', 'text' => 'Selesai', 'icon' => 'fas fa-check-circle'],
                                'rejected'   => ['class' => 'bg-danger', 'text' => 'Ditolak', 'icon' => 'fas fa-times-circle'],
                            ];
                            $currentStatus = $statusConfig[strtolower($order['status'])] ?? ['class' => 'bg-dark', 'text' => 'N/A', 'icon' => 'fas fa-question-circle'];
                            ?>
                            <span class="badge rounded-pill <?= $currentStatus['class'] ?> status-badge">
                                <i class="<?= $currentStatus['icon'] ?>"></i>
                                <span><?= esc($currentStatus['text']) ?></span>
                            </span>
                        </dd>
                        <dt class="col-sm-5">Total Bayar</dt>
                        <dd class="col-sm-7 fw-bold fs-5 text-success">Rp <?= esc(number_format($order['total_amount'], 0, ',', '.')) ?></dd>

                        <?php if (!empty($order['notes'])): ?>
                            <dt class="col-sm-12">
                                <hr class="my-2">
                            </dt>
                            <dt class="col-sm-5">Catatan</dt>
                            <dd class="col-sm-7 fst-italic">"<?= esc($order['notes']) ?>"</dd>
                        <?php endif; ?>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Kartu Informasi Kontak -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4"><i class="fas fa-users text-primary me-2"></i>Pihak Terkait</h5>
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

    <!-- Kartu Aksi -->
    <?php if (in_array($order['status'], ['pending', 'approved'])): ?>
        <div class="card shadow-sm">
            <div class="card-body text-end">
                <?php if ($order['status'] === 'pending'): ?>
                    <button type="button" class="btn btn-danger" onclick="showRejectModal(<?= $order['id'] ?>)"><i class="fas fa-times me-2"></i> Tolak Order</button>
                    <button type="button" class="btn btn-success" onclick="updateOrderStatus(<?= $order['id'] ?>, 'approved')"><i class="fas fa-check me-2"></i> Setujui Order</button>
                <?php elseif ($order['status'] === 'approved'): ?>
                    <a href="<?= base_url('pabrik/shipments/create/' . $order['id']) ?>" class="btn btn-primary"><i class="fas fa-truck me-2"></i> Buat Pengiriman</a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

</div>

<!-- LOGIKA MODAL DAN FORM TIDAK BERUBAH, HANYA STYLING -->
<form id="orderStatusUpdateFormPabrikDetail" action="" method="post" style="display: none;"><?= csrf_field() ?><input type="hidden" name="status" id="newOrderStatusInputPabrikDetail"><input type="hidden" name="notes" id="statusNotesInputPabrikDetail"></form>

<div class="modal fade" id="rejectOrderModalPabrikDetail" tabindex="-1" aria-labelledby="rejectOrderModalLabelPabrikDetail" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="rejectOrderModalLabelPabrikDetail"><i class="fas fa-exclamation-triangle me-2"></i>Tolak Order</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Anda akan menolak order <strong>ORD-<?= esc($order['id']) ?></strong>. Mohon berikan alasan penolakan.</p>
                <form id="rejectFormPabrikDetail" action="" method="post">
                    <?= csrf_field() ?><input type="hidden" name="status" value="rejected">
                    <div class="form-floating"><textarea class="form-control" id="rejectNotesPabrikDetail" name="notes" rows="4" placeholder="Alasan penolakan" required></textarea><label for="rejectNotesPabrikDetail">Alasan Penolakan</label></div>
                </form>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button type="button" class="btn btn-danger" onclick="confirmRejectPabrikDetail()"><i class="fas fa-times-circle me-2"></i>Konfirmasi Tolak</button></div>
        </div>
    </div>
</div>

<!-- SCRIPT DENGAN PENINGKATAN UX (SWEETALERT) -- LOGIKA UTAMA SAMA -->
<script>
    let currentOrderIdToRejectPabrikDetail = null;

    function updateOrderStatus(orderId, newStatus) {
        Swal.fire({
            title: 'Anda Yakin?',
            text: `Status order akan diubah menjadi DISEETUJUI. Tindakan ini tidak dapat diurungkan.`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#198754',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Setujui!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('orderStatusUpdateFormPabrikDetail');
                form.action = `<?= base_url('pabrik/orders/update-status-from-distributor/') ?>${orderId}`;
                document.getElementById('newOrderStatusInputPabrikDetail').value = newStatus;
                document.getElementById('statusNotesInputPabrikDetail').value = ''; // Pastikan notes kosong untuk approve
                form.submit();
            }
        });
    }

    function showRejectModal(orderId) {
        currentOrderIdToRejectPabrikDetail = orderId;
        // Reset field notes setiap kali modal dibuka
        document.getElementById('rejectNotesPabrikDetail').value = '';
        const rejectModal = new bootstrap.Modal(document.getElementById('rejectOrderModalPabrikDetail'));
        rejectModal.show();
    }

    function confirmRejectPabrikDetail() {
        if (currentOrderIdToRejectPabrikDetail) {
            const form = document.getElementById('rejectFormPabrikDetail');
            const notes = document.getElementById('rejectNotesPabrikDetail').value;

            if (notes.trim() === '') {
                Swal.fire('Gagal', 'Alasan penolakan tidak boleh kosong.', 'error');
                return;
            }

            form.action = `<?= base_url('pabrik/orders/update-status-from-distributor/') ?>${currentOrderIdToRejectPabrikDetail}`;
            form.submit();
        }
    }
</script>
<?= $this->endSection() ?>