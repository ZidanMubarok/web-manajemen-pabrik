<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('head'); ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
    /* ... (CSS Anda tidak perlu diubah, biarkan seperti semula) ... */
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
    <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert alert-success d-flex align-items-center alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            <div><?= session()->getFlashdata('success') ?></div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')) : ?>
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
                            // ### PERUBAHAN: Tambahkan status 'rejected' ###
                            $statusConfig = [
                                'approved'   => ['class' => 'bg-info', 'text' => 'Disetujui', 'icon' => 'fas fa-thumbs-up'],
                                'processing' => ['class' => 'bg-warning', 'text' => 'Diproses', 'icon' => 'fas fa-cogs'],
                                'shipped'    => ['class' => 'bg-primary', 'text' => 'Dikirim', 'icon' => 'fas fa-truck'],
                                'rejected'   => ['class' => 'bg-danger', 'text' => 'Ditolak', 'icon' => 'fas fa-times-circle'],
                            ];
                            $currentStatus = $statusConfig[strtolower($order['status'])] ?? ['class' => 'bg-secondary', 'text' => 'N/A', 'icon' => 'fas fa-question-circle'];
                            ?>
                            <span class="badge rounded-pill <?= $currentStatus['class'] ?> status-badge">
                                <i class="<?= $currentStatus['icon'] ?>"></i>
                                <span><?= esc($currentStatus['text']) ?></span>
                            </span>
                        </dd>

                        <!-- ### PERUBAHAN: Tampilkan alasan penolakan jika ada ### -->
                        <?php if ($order['status'] == 'rejected' && !empty($order['rejection_reason'])) : ?>
                            <dt class="col-sm-5 text-danger">Alasan Ditolak</dt>
                            <dd class="col-sm-7 text-danger"><?= esc($order['rejection_reason']) ?></dd>
                        <?php endif; ?>

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

                        <?php if (isset($agen['username']) && !empty($agen['username'])) : ?>
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
        <!-- ... Kartu Informasi Kontak (tidak ada perubahan) ... -->
    </div>
    <!-- ... Kartu Rincian Item (tidak ada perubahan) ... -->
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
                        <?php if (empty($orderItems)) : ?>
                            <tr>
                                <td colspan="5" class="text-center py-4">Tidak ada item dalam order ini.</td>
                            </tr>
                        <?php else : ?>
                            <?php $no = 1;
                            foreach ($orderItems as $item) : ?>
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

    <!-- ### PERUBAHAN: Logika Panel Aksi disesuaikan ### -->
    <!-- Panel Aksi hanya muncul jika status 'approved' atau 'processing' -->
    <?php if (in_array($order['status'], ['approved', 'processing'])) : ?>
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h5 class="m-0"><i class="fas fa-tasks text-primary me-2"></i>Tindakan Selanjutnya</h5>
            </div>
            <div class="card-body d-flex justify-content-end gap-2">
                <?php if ($order['status'] == 'approved') : ?>
                    <!-- Tombol Tolak Order -->
                    <button type="button" class="btn btn-danger" onclick="handleStatusUpdate(<?= $order['id'] ?>, 'rejected')">
                        <i class="fas fa-times-circle me-2"></i>Tolak Order
                    </button>
                    <!-- Tombol Proses Order -->
                    <button type="button" class="btn btn-warning" onclick="handleStatusUpdate(<?= $order['id'] ?>, 'processing')">
                        <i class="fas fa-cogs me-2"></i>Mulai Proses Order
                    </button>
                <?php elseif ($order['status'] == 'processing') : ?>
                    <button type="button" class="btn btn-danger" onclick="handleStatusUpdate(<?= $order['id'] ?>, 'rejected')">
                        <i class="fas fa-times-circle me-2"></i>Tolak Order
                    </button>
                    <!-- Tombol Kirim Order -->
                    <button type="button" class="btn btn-primary" onclick="handleStatusUpdate(<?= $order['id'] ?>, 'shipped')">
                        <i class="fas fa-truck me-2"></i>Kirim Order & Buat Pengiriman
                    </button>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

</div>

<!-- ### PERUBAHAN: Form ditambah input untuk alasan penolakan ### -->
<form id="statusUpdateForm" action="" method="post" style="display: none;">
    <?= csrf_field() ?>
    <input type="hidden" name="status" id="newStatusInput">
    <input type="hidden" name="rejection_reason" id="rejectionReasonInput">
</form>

<!-- ### PERUBAHAN: JavaScript disempurnakan untuk handle 'rejected' ### -->
<script>
    function handleStatusUpdate(orderId, newStatus) {
        const dialogConfigs = {
            processing: {
                title: 'Mulai Proses Order?',
                text: `Anda akan mengubah status Order ID ORD-${orderId} menjadi DIPROSES.`,
                icon: 'info',
                confirmButtonColor: '#ffc107',
                confirmButtonText: 'Ya, Mulai Proses!'
            },
            shipped: {
                title: 'Kirim Order Ini?',
                text: `Status order ID ORD-${orderId} akan diubah menjadi DIKIRIM dan data pengiriman akan dibuat.`,
                icon: 'question',
                confirmButtonColor: '#0d6efd',
                confirmButtonText: 'Ya, Kirim Sekarang!'
            },
            rejected: {
                title: 'Tolak Order Ini?',
                text: `Order ID ORD-${orderId} akan ditolak. Harap berikan alasan penolakan.`,
                icon: 'warning',
                input: 'textarea', // Ini akan menampilkan textarea untuk input
                inputPlaceholder: 'Ketik alasan penolakan di sini...',
                inputValidator: (value) => {
                    if (!value) {
                        return 'Anda harus menuliskan alasan penolakan!'
                    }
                },
                confirmButtonColor: '#dc3545', // Merah untuk 'danger'
                confirmButtonText: 'Ya, Tolak Order!'
            }
        };

        const config = dialogConfigs[newStatus];
        if (!config) {
            console.error('Konfigurasi status tidak ditemukan:', newStatus);
            return;
        }

        Swal.fire({
            ...config, // Gunakan semua konfigurasi dari objek
            showCancelButton: true,
            cancelButtonColor: '#6c757d',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('statusUpdateForm');
                form.action = `<?= base_url('pabrik/incoming-orders/update-status/') ?>${orderId}`;
                document.getElementById('newStatusInput').value = newStatus;

                // Jika statusnya 'rejected', ambil nilai dari input SweetAlert
                if (newStatus === 'rejected') {
                    document.getElementById('rejectionReasonInput').value = result.value;
                }

                form.submit();
            }
        });
    }
</script>
<?= $this->endSection() ?>