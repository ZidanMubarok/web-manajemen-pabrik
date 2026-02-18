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

    .form-floating>.form-control,
    .form-floating>.form-select {
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

    .pagination .page-link {
        border-radius: .375rem;
        margin: 0 .2rem;
    }

    .pagination .page-item.active .page-link {
        box-shadow: 0 4px 8px rgba(var(--bs-primary-rgb), 0.3);
    }

    .action-btn-group .dropdown-item {
        display: flex;
        align-items: center;
        gap: .5rem;
    }

    .action-btn-group .dropdown-item i {
        width: 1rem;
        text-align: center;
    }
</style>
<?= $this->endSection(); ?>


<?= $this->section('content') ?>
<div class="container-fluid pt-4">

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-inbox text-primary me-2"></i> <?= $title ?></h1>
    </div>

    <!-- Flash Messages yang Lebih Baik -->
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
            <h5 class="card-title mb-3"><i class="fas fa-filter me-2"></i> Filter Order</h5>
            <form action="<?= base_url('pabrik/incoming-orders') ?>" method="get" class="row g-3 align-items-end">
                <div class="col-lg-7 col-md-12">
                    <div class="form-floating">
                        <input type="text" name="search" class="form-control" placeholder="Cari..." value="<?= esc($search ?? '') ?>">
                        <label>Cari Order ID, Distributor, atau Agen</label>
                    </div>
                </div>
                <div class="col-lg-3 col-md-8">
                    <div class="form-floating">
                        <select name="status" class="form-select">
                            <option value="">Semua Status Aktif</option>
                            <option value="approved" <?= ($statusFilter ?? '') == 'approved' ? 'selected' : '' ?>>Disetujui</option>
                            <option value="processing" <?= ($statusFilter ?? '') == 'processing' ? 'selected' : '' ?>>Diproses</option>
                        </select>
                        <!-- <label>Filter by Status</label> -->
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100 h-100"><i class="fas fa-search"></i> Cari</button>
                    <?php if (!empty($search) || !empty($statusFilter)): ?>
                        <a href="<?= base_url('pabrik/incoming-orders') ?>" class="btn btn-outline-secondary" data-bs-toggle="tooltip" title="Reset Filter"><i class="fas fa-sync-alt"></i></a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <!-- Kartu Data Order Masuk -->
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h6 class="m-0 font-weight-bold"><i class="fas fa-list-ul me-2"></i>Daftar Order Untuk Diproses</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle" id="dataTable" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Order ID</th>
                            <th>Tujuan</th>
                            <th>Tanggal Order</th>
                            <th class="text-end">Total (Rp)</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($incomingOrders)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <i class="fas fa-check-square fa-3x text-muted mb-3"></i>
                                    <h5 class="mb-1">Semua Order Terproses!</h5>
                                    <p class="text-muted">Tidak ada order masuk yang memerlukan tindakan saat ini.</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php
                            $currentPage = $pager->getCurrentPage();
                            $perPage = $pager->getPerPage();
                            $no = (($currentPage - 1) * $perPage) + 1;
                            foreach ($incomingOrders as $order): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td class="fw-bold">ORD-<?= esc($order['id']) ?></td>
                                    <td>
                                        <div class="fw-bold"><?= esc($distributorUsernames[$order['distributor_id']] ?? 'N/A') ?></div>
                                        <small class="text-muted"><?= esc($agenUsernames[$order['agen_id']] ?? 'Langsung ke Distributor') ?></small>
                                    </td>
                                    <td><?= date('d M Y H:i', strtotime($order['order_date'])) ?></td>
                                    <td class="text-end"><?= esc(number_format($order['total_amount'], 0, ',', '.')) ?></td>
                                    <td class="text-center">
                                        <?php
                                        // Konfigurasi Badge Status
                                        $statusConfig = [
                                            'approved' => ['class' => 'bg-info', 'text' => 'Disetujui', 'icon' => 'fas fa-thumbs-up'],
                                            'processing' => ['class' => 'bg-warning', 'text' => 'Diproses', 'icon' => 'fas fa-cogs'],
                                        ];
                                        $currentStatus = $statusConfig[strtolower($order['status'])] ?? ['class' => 'bg-secondary', 'text' => 'N/A', 'icon' => 'fas fa-question-circle'];
                                        ?>
                                        <span class="badge rounded-pill <?= $currentStatus['class'] ?> status-badge"><i class="<?= $currentStatus['icon'] ?>"></i> <span><?= $currentStatus['text'] ?></span></span>
                                    </td>
                                    <td class="text-center action-btn-group">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">Aksi</button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a class="dropdown-item" href="<?= base_url('pabrik/incoming-orders/detail/' . $order['id']) ?>"><i class="fas fa-eye text-info"></i> Lihat Detail</a></li>
                                                <li>
                                                    <hr class="dropdown-divider">
                                                </li>
                                                <?php if ($order['status'] == 'approved'): ?>
                                                    <li><a class="dropdown-item" href="#" onclick="confirmUpdateStatus(<?= $order['id'] ?>, 'processing', 'Proses Order')"><i class="fas fa-cogs text-warning"></i> Proses Order</a></li>
                                                <?php elseif ($order['status'] == 'processing'): ?>
                                                    <li><a class="dropdown-item" href="#" onclick="confirmUpdateStatus(<?= $order['id'] ?>, 'shipped', 'Kirim Order')"><i class="fas fa-truck text-primary"></i> Kirim Order</a></li>
                                                <?php endif; ?>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>


            <div class="d-flex justify-content-between align-items-center mt-4">
                <div>
                    <?php
                    $totalItems = $pager->getTotal();
                    $firstItem = ($totalItems > 0) ? (($pager->getCurrentPage() - 1) * $pager->getPerPage()) + 1 : 0;
                    $lastItem = ($totalItems > 0) ? min($firstItem + $pager->getPerPage() - 1, $totalItems) : 0;
                    ?>
                    Menampilkan <?= $firstItem ?> sampai <?= $lastItem ?> dari <?= $totalItems ?> total produk
                </div>
                <div>
                    <?= $pager->links('default', 'bootstrap_pagination') ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- FORM UNTUK UPDATE STATUS (Logika tidak berubah) -->
<form id="statusUpdateForm" action="" method="post" style="display: none;">
    <?= csrf_field() ?>
    <input type="hidden" name="status" id="newStatusInput">
</form>

<script>
    // Inisialisasi Tooltip Bootstrap 5
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    // Fungsi konfirmasi dengan SweetAlert2 yang lebih kontekstual
    function confirmUpdateStatus(orderId, newStatus, actionText) {
        let config = {
            processing: {
                title: 'Mulai Proses Order?',
                text: `Order ORD-${orderId} akan ditandai sedang diproses.`,
                icon: 'info',
                confirmButtonText: 'Ya, Proses!'
            },
            shipped: {
                title: 'Siap Kirim Order?',
                text: `Status order ORD-${orderId} akan diubah menjadi DIKIRIM.`,
                icon: 'success',
                confirmButtonText: 'Ya, Kirim!'
            },
        };

        let dialogConfig = config[newStatus] || {
            title: 'Konfirmasi',
            text: `Ubah status menjadi ${actionText}?`,
            icon: 'warning',
            confirmButtonText: 'Ya, Ubah!'
        };

        Swal.fire({
            title: dialogConfig.title,
            text: dialogConfig.text,
            icon: dialogConfig.icon,
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
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