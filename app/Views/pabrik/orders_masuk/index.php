<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('head'); ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
    /* ... (Gaya CSS Anda tidak perlu diubah, biarkan seperti semula) ... */
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

    /* ### PERUBAHAN: Menambahkan style untuk header filter yang bisa diklik ### */
    .card-header[data-bs-toggle="collapse"] {
        cursor: pointer;
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

    .badge.bg-primary {
        background-color: var(--bs-primary-bg-subtle) !important;
        color: var(--bs-primary-text-emphasis) !important;
        border: 1px solid var(--bs-primary-border-subtle) !important;
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

    <!-- ### PERUBAHAN DIMULAI: Bagian Filter Diubah Menjadi Collapsible ### -->
    <div class="card shadow-sm mb-4">
        <div class="card-header py-3" data-bs-toggle="collapse" href="#collapseFilter" role="button" aria-expanded="true" aria-controls="collapseFilter">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-filter me-2"></i> Filter & Pencarian Order</h6>
        </div>
        <div class="collapse show" id="collapseFilter">
            <div class="card-body">
                <form action="<?= base_url('pabrik/incoming-orders') ?>" method="get">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-6">
                            <label for="searchFilter" class="form-label">Cari Order ID, Distributor, atau Agen:</label>
                            <input type="text" id="searchFilter" name="search" class="form-control" placeholder="Ketik di sini..." value="<?= esc($search ?? '') ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="statusFilter" class="form-label">Status Order:</label>
                            <select id="statusFilter" name="status" class="form-select">
                                <option value="">Semua Status Aktif</option>
                                <option value="approved" <?= ($statusFilter ?? '') == 'approved' ? 'selected' : '' ?>>Disetujui</option>
                                <option value="processing" <?= ($statusFilter ?? '') == 'processing' ? 'selected' : '' ?>>Diproses</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <div class="btn-group w-100 gap-2" role="group">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Cari</button>
                                <?php if (!empty($search) || !empty($statusFilter)) : ?>
                                    <a href="<?= base_url('pabrik/incoming-orders') ?>" class="btn btn-secondary" data-bs-toggle="tooltip" title="Reset Filter"><i class="fas fa-sync-alt"></i></a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- ### PERUBAHAN SELESAI ### -->

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
                        <?php if (empty($incomingOrders)) : ?>
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <i class="fas fa-check-square fa-3x text-muted mb-3"></i>
                                    <h5 class="mb-1">Semua Order Terproses!</h5>
                                    <p class="text-muted">Tidak ada order masuk yang memerlukan tindakan saat ini.</p>
                                </td>
                            </tr>
                        <?php else : ?>
                            <?php
                            $currentPage = $pager->getCurrentPage();
                            $perPage = $pager->getPerPage();
                            $no = (($currentPage - 1) * $perPage) + 1;
                            foreach ($incomingOrders as $order) : ?>
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
                                        // ### Konfigurasi status tidak diubah ###
                                        $statusConfig = [
                                            'approved'   => ['class' => 'bg-info', 'text' => 'Disetujui', 'icon' => 'fas fa-thumbs-up'],
                                            'processing' => ['class' => 'bg-warning', 'text' => 'Diproses', 'icon' => 'fas fa-cogs'],
                                            'shipped'    => ['class' => 'bg-primary', 'text' => 'Dikirim', 'icon' => 'fas fa-truck'],
                                            'rejected'   => ['class' => 'bg-danger', 'text' => 'Ditolak', 'icon' => 'fas fa-times-circle'],
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
                                                <?php if ($order['status'] == 'approved') : ?>
                                                    <li><a class="dropdown-item" href="#" onclick="handleStatusUpdate(<?= $order['id'] ?>, 'processing')"><i class="fas fa-cogs text-warning"></i> Proses Order</a></li>
                                                    <li><a class="dropdown-item" href="#" onclick="handleStatusUpdate(<?= $order['id'] ?>, 'rejected')"><i class="fas fa-times-circle text-danger"></i> Tolak Order</a></li>
                                                <?php elseif ($order['status'] == 'processing') : ?>
                                                    <li><a class="dropdown-item" href="#" onclick="handleStatusUpdate(<?= $order['id'] ?>, 'rejected')"><i class="fas fa-times-circle text-danger"></i> Tolak Order</a></li>
                                                    <li><a class="dropdown-item" href="#" onclick="handleStatusUpdate(<?= $order['id'] ?>, 'shipped')"><i class="fas fa-truck text-primary"></i> Kirim Order</a></li>
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

            <!-- ... Blok Pager tidak diubah ... -->
            <?php if ($pager && $pager->getPageCount() > 1) : ?>
                <div class="d-flex justify-content-center mt-4">
                    <?= $pager->links() ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- ### Formulir dan JavaScript tidak diubah ### -->
<form id="statusUpdateForm" action="" method="post" style="display: none;">
    <?= csrf_field() ?>
    <input type="hidden" name="status" id="newStatusInput">
    <input type="hidden" name="rejection_reason" id="rejectionReasonInput">
</form>

<script>
    // Inisialisasi Tooltip
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    /**
     * Fungsi tunggal untuk menangani semua pembaruan status melalui SweetAlert.
     * @param {number} orderId - ID dari order yang akan diupdate.
     * @param {string} newStatus - Status baru ('processing', 'shipped', atau 'rejected').
     */
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
                input: 'textarea',
                inputPlaceholder: 'Ketik alasan penolakan di sini...',
                inputValidator: (value) => {
                    if (!value) {
                        return 'Anda harus menuliskan alasan penolakan!'
                    }
                },
                confirmButtonColor: '#dc3545',
                confirmButtonText: 'Ya, Tolak Order!'
            }
        };

        const config = dialogConfigs[newStatus];
        if (!config) {
            console.error('Konfigurasi status tidak ditemukan:', newStatus);
            return;
        }

        Swal.fire({
            ...config,
            showCancelButton: true,
            cancelButtonColor: '#6c757d',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('statusUpdateForm');
                form.action = `<?= base_url('pabrik/incoming-orders/update-status/') ?>${orderId}`;
                document.getElementById('newStatusInput').value = newStatus;

                if (newStatus === 'rejected') {
                    document.getElementById('rejectionReasonInput').value = result.value;
                }

                form.submit();
            }
        });
    }
</script>
<?= $this->endSection() ?>