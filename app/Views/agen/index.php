<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('title') ?>
<?= $title ?>
<?= $this->endSection() ?>
<?= $this->section('head') ?>
<style>
    /* Custom styles for dashboard cards */
    .card-dashboard-custom {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        border-radius: 0.75rem;
        /* Slightly more rounded corners */
        background-color: #ffffff;
        /* White background for clean look */
    }

    .card-dashboard-custom:hover {
        transform: translateY(-5px);
        /* Lift effect on hover */
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        /* Stronger shadow on hover */
    }

    .card-dashboard-custom .card-body {
        padding: 1.5rem;
        /* More padding inside card body */
    }

    .card-dashboard-custom .text-uppercase {
        font-size: 0.8rem;
        /* Smaller, cleaner text for labels */
        letter-spacing: 0.05em;
        margin-bottom: 0.5rem !important;
        /* Adjust margin */
    }

    .card-dashboard-custom h3 {
        font-size: 2rem;
        /* Larger, bolder numbers */
        font-weight: 700;
        line-height: 1.2;
    }

    .icon-shape {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 3.5rem;
        /* Size of the circular icon background */
        height: 3.5rem;
        border-radius: 50% !important;
        /* Ensure perfect circle */
        font-size: 1.5rem;
        /* Size of the icon */
        color: white;
        /* Icon color */
    }

    /* Adjusting specific badge colors for the table to match bootstrap 5.3 nuances if needed */
    .badge.bg-secondary {
        background-color: #6c757d !important;
    }

    .badge.bg-info {
        background-color: #0dcaf0 !important;
    }

    .badge.bg-warning {
        background-color: #ffc107 !important;
        color: #212529 !important;
    }

    /* Warning text usually darker */
    .badge.bg-primary {
        background-color: #0d6efd !important;
    }

    .badge.bg-success {
        background-color: #198754 !important;
    }

    .badge.bg-danger {
        background-color: #dc3545 !important;
    }

    /* Responsive adjustments for row-cols */
    @media (min-width: 576px) {

        /* Small devices (sm) */
        .row-cols-sm-2>* {
            flex: 0 0 auto;
            width: 50%;
        }
    }

    @media (min-width: 768px) {

        /* Medium devices (md) */
        .row-cols-md-3>* {
            flex: 0 0 auto;
            width: 33.333333%;
        }
    }

    @media (min-width: 992px) {

        /* Large devices (lg) */
        .row-cols-lg-4>* {
            flex: 0 0 auto;
            width: 25%;
        }
    }
</style>
<?= $this->endSection() ?>
<?= $this->section('content') ?>
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard Agen</h1>
        <a href="<?= base_url('agen/reports') ?>" class="btn btn-sm btn-primary shadow-sm"><i
                class="fas fa-download fa-sm text-white-50 me-1"></i> Generate Laporan</a>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($error) && $error): ?>
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <strong>Error:</strong> <?= esc($error) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php else: ?>
        <div class="alert alert-info alert-dismissible fade show mb-4" role="alert">
            Selamat datang, **<?= esc(session()->get('username')) ?>**! Anda terhubung dengan distributor **<?= esc($distributorName) ?>**.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>

        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-3 g-4 mb-4">
            <div class="col">
                <a href="<?= base_url('agen/orders/history') ?>" class="card card-dashboard-custom h-100 shadow border-0 text-decoration-none">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h6 class="text-uppercase text-muted mb-0">Total Pesanan Anda</h6>
                                    <h3 class="mb-0 text-primary fw-bold mt-2"><?= esc($totalOrders) ?></h3>
                                </div>
                                <div class="icon-shape bg-primary text-white rounded-circle">
                                    <i class="fas fa-receipt"></i>
                                </div>
                            </div>
                        </div>
                        <div class="text-end text-primary fw-bold mt-3 card-link-detail">
                            Lihat Detail <i class="fas fa-arrow-right ms-1"></i>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col">
                <a href="<?= base_url('agen/orders/history?status[]=pending&status[]=approved') ?>" class="card card-dashboard-custom h-100 shadow border-0 text-decoration-none">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h6 class="text-uppercase text-muted mb-0">Pesanan Tertunda/Di Setujui</h6>
                                    <h3 class="mb-0 text-warning fw-bold mt-2"><?= esc($pendingOrders) ?></h3>
                                </div>
                                <div class="icon-shape bg-warning text-white rounded-circle">
                                    <i class="fas fa-hourglass-half"></i>
                                </div>
                            </div>
                        </div>
                        <div class="text-end text-warning fw-bold mt-3 card-link-detail">
                            Lihat Detail <i class="fas fa-arrow-right ms-1"></i>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col">
                <a href="<?= base_url('agen/orders/history?status[]=processing&status[]=shipped') ?>" class="card card-dashboard-custom h-100 shadow border-0 text-decoration-none">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h6 class="text-uppercase text-muted mb-0">Pesanan Diproses/Dikirim</h6>
                                    <h3 class="mb-0 text-info fw-bold mt-2"><?= esc($shippedOrders) ?></h3>
                                </div>
                                <div class="icon-shape bg-info text-white rounded-circle">
                                    <i class="fas fa-truck"></i>
                                </div>
                            </div>
                        </div>
                        <div class="text-end text-info fw-bold mt-3 card-link-detail">
                            Lihat Detail <i class="fas fa-arrow-right ms-1"></i>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col">
                <a href="<?= base_url('agen/orders/history?status=completed') ?>" class="card card-dashboard-custom h-100 shadow border-0 text-decoration-none">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h6 class="text-uppercase text-muted mb-0">Pesanan Selesai</h6>
                                    <h3 class="mb-0 text-success fw-bold mt-2"><?= esc($completedOrders) ?></h3>
                                </div>
                                <div class="icon-shape bg-success text-white rounded-circle">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                            </div>
                        </div>
                        <div class="text-end text-success fw-bold mt-3 card-link-detail">
                            Lihat Detail <i class="fas fa-arrow-right ms-1"></i>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col">
                <a href="<?= base_url('agen/orders/history?status=rejected') ?>" class="card card-dashboard-custom h-100 shadow border-0 text-decoration-none">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h6 class="text-uppercase text-muted mb-0">Pesanan Ditolak</h6>
                                    <h3 class="mb-0 text-danger fw-bold mt-2"><?= esc($rejectedOrders) ?></h3>
                                </div>
                                <div class="icon-shape bg-danger text-white rounded-circle">
                                    <i class="fas fa-times-circle"></i>
                                </div>
                            </div>
                        </div>
                        <div class="text-end text-danger fw-bold mt-3 card-link-detail">
                            Lihat Detail <i class="fas fa-arrow-right ms-1"></i>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col">
                <a href="<?= base_url('agen/invoices?status=unpaid') ?>" class="card card-dashboard-custom h-100 shadow border-0 text-decoration-none">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h6 class="text-uppercase text-muted mb-0">Tagihan Belum Lunas</h6>
                                    <h3 class="mb-0 text-danger fw-bold mt-2"><?= esc($totalUnpaidInvoices) ?></h3>
                                </div>
                                <div class="icon-shape bg-danger text-white rounded-circle">
                                    <i class="fas fa-file-invoice-dollar"></i>
                                </div>
                            </div>
                        </div>
                        <div class="text-end text-danger fw-bold mt-3 card-link-detail">
                            Lihat Detail <i class="fas fa-arrow-right ms-1"></i>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col">
                <a href="<?= base_url('agen/invoices?status=paid') ?>" class="card card-dashboard-custom h-100 shadow border-0 text-decoration-none">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h6 class="text-uppercase text-muted mb-0">Tagihan Sudah Lunas</h6>
                                    <h3 class="mb-0 text-success fw-bold mt-2"><?= esc($totalPaidInvoices) ?></h3>
                                </div>
                                <div class="icon-shape bg-success text-white rounded-circle">
                                    <i class="fas fa-check-double"></i>
                                </div>
                            </div>
                        </div>
                        <div class="text-end text-success fw-bold mt-3 card-link-detail">
                            Lihat Detail <i class="fas fa-arrow-right ms-1"></i>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col">
                <a href="<?= base_url('agen/invoices?invoice_id=&order_id=&status=cancelled&start_date=&end_date=') ?>" class="card card-dashboard-custom h-100 shadow border-0 text-decoration-none">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h6 class="text-uppercase text-muted mb-0">Tagihan Dibatalkan</h6>
                                    <h3 class="mb-0 text-secondary fw-bold mt-2"><?= esc($totalRejectedInvoices) ?></h3>
                                </div>
                                <div class="icon-shape bg-secondary text-white rounded-circle">
                                    <i class="fas fa-times"></i>
                                </div>
                            </div>
                        </div>
                        <div class="text-end text-secondary fw-bold mt-3 card-link-detail">
                            Lihat Detail <i class="fas fa-arrow-right ms-1"></i>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col">
                <a href="<?= base_url('agen/orders/create') ?>" class="card card-dashboard-custom h-100 shadow border-0 text-decoration-none">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h6 class="text-uppercase text-muted mb-0">Pesan Produk</h6>
                                    <h3 class="mb-0 text-success fw-bold mt-2">Buat Order Baru</h3>
                                </div>
                                <div class="icon-shape bg-success text-white rounded-circle">
                                    <i class="fas fa-cart-plus"></i>
                                </div>
                            </div>
                        </div>
                        <div class="text-end text-success fw-bold mt-3 card-link-detail">
                            Mulai Sekarang <i class="fas fa-arrow-right ms-1"></i>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12 mb-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 bg-primary text-white">
                        <h6 class="m-0 font-weight-bold"><i class="fas fa-history me-2"></i> 5 Order Terbaru Anda</h6>
                    </div>
                    <div class="card-body">
                        <?php if (empty($recentOrders)): ?>
                            <div class="text-center text-muted">Belum ada order terbaru.</div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" id="recentOrdersTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Order ID</th>
                                            <th>Tanggal Order</th>
                                            <th>Total Jumlah</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recentOrders as $order): ?>
                                            <tr>
                                                <td>ORD-<?= esc($order['id']) ?></td>
                                                <td><?= date('d/m/Y H:i', strtotime($order['order_date'])) ?></td>
                                                <td>Rp <?= esc(number_format($order['total_amount'], 0, ',', '.')) ?></td>
                                                <td>
                                                    <?php
                                                    $status = strtolower($order['status']);
                                                    $badgeClass = '';
                                                    $displayText = '';

                                                    switch ($status) {
                                                        case 'pending':
                                                            $badgeClass = 'bg-secondary';
                                                            $displayText = 'Tertunda';
                                                            break;
                                                        case 'approved':
                                                            $badgeClass = 'bg-info';
                                                            $displayText = 'Disetujui';
                                                            break;
                                                        case 'processing':
                                                            $badgeClass = 'bg-warning';
                                                            $displayText = 'Diproses';
                                                            break;
                                                        case 'shipped':
                                                            $badgeClass = 'bg-primary';
                                                            $displayText = 'Dikirim';
                                                            break;
                                                        case 'completed':
                                                            $badgeClass = 'bg-success';
                                                            $displayText = 'Selesai';
                                                            break;
                                                        case 'rejected':
                                                            $badgeClass = 'bg-danger';
                                                            $displayText = 'Ditolak';
                                                            break;
                                                        default:
                                                            $badgeClass = 'bg-secondary';
                                                            $displayText = 'Tidak Diketahui';
                                                            break;
                                                    }
                                                    ?>
                                                    <span class="badge <?= $badgeClass ?>"><?= esc($displayText) ?></span>
                                                </td>
                                                <td>
                                                    <a href="<?= base_url('agen/orders/detail/' . $order['id']) ?>" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye me-1"></i>Lihat Detail
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>