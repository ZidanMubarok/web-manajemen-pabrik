<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <h1 class="mb-4">Selamat Datang di Dashboard Anda, <?= esc(session()->get('username')) ?>!</h1>

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

    <div class="row">

        <div class="col-xl-4 col-md-6 mb-4">
            <a href="<?= base_url('distributor/orders/incoming') ?>" class="card card-hover-effect card-dashboard-lg bg-primary shadow h-100 py-3 text-decoration-none">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xl font-weight-bold text-white text-uppercase mb-1 fs-4">
                                Order Masuk</div>
                            <div class="h3 mb-0 font-weight-bold text-white"><?= esc($incomingOrders) ?></div>
                            <div class="text-md mt-2 text-white fs-5">Lihat Detail <i class="fas fa-arrow-circle-right"></i></div>
                        </div>
                        <div class="col-auto p-3 icon-shape bg-primary text-white rounded-circle">
                            <i class="fas fa-box-open fa-3x"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <a href="<?= base_url('distributor/invoicesme?status=unpaid&invoice_number=&start_date=&end_date=') ?>" class="card card-hover-effect card-dashboard-lg bg-warning shadow h-100 py-3 text-decoration-none">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xl font-weight-bold text-white text-uppercase mb-1">
                                Tagihan Saya Belum Lunas</div>
                            <div class="h3 mb-0 font-weight-bold text-white"><?= esc($myUnpaidInvoices) ?></div>
                            <div class="text-md mt-2 text-white">Lihat Detail <i class="fas fa-arrow-circle-right"></i></div>
                        </div>
                        <!-- <div class="col-auto"> -->
                        <div class="col-auto p-3 icon-shape bg-warning text-white rounded-circle">
                            <i class="fas fa-file-invoice-dollar fa-3x"></i>
                            <!-- <i class="fas fa-file-invoice-dollar fa-3x text-gray-300"></i> -->
                        </div>
                        <!-- </div> -->
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <a href="<?= base_url('distributor/invoices?invoice_id=&order_id=&agent_name=&status=unpaid&start_date=&end_date=') ?>" class="card card-hover-effect card-dashboard-lg bg-danger shadow h-100 py-3 text-decoration-none">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xl font-weight-bold text-white text-uppercase mb-1">
                                Tagihan Agen Belum Lunas</div>
                            <div class="h3 mb-0 font-weight-bold text-white"><?= esc($agenUnpaidInvoices) ?></div>
                            <div class="text-md mt-2 text-white">Lihat Detail <i class="fas fa-arrow-circle-right"></i></div>
                        </div>
                        <div class="col-auto p-3 icon-shape bg-danger text-white rounded-circle">
                            <i class="fas fa-hand-holding-dollar fa-3x"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-xl-4 col-md-6 mb-4">
            <a href="<?= base_url('distributor/invoicesme?status=paid&invoice_number=&start_date=&end_date=') ?>" class="card card-hover-effect card-dashboard-lg border-start-success shadow h-100 py-3 text-decoration-none">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xl font-weight-bold text-success text-uppercase mb-1">
                                Tagihan Saya Sudah Lunas</div>
                            <div class="h3 mb-0 font-weight-bold text-gray-800"><?= esc($myPaidInvoices) ?></div>
                            <div class="text-md mt-2 text-success">Lihat Detail <i class="fas fa-arrow-circle-right"></i></div>
                        </div>
                        <!-- <div class="col-auto"> -->
                        <div class="col-auto p-3 icon-shape bg-success text-white rounded-circle">
                            <!-- <i class="fas fa-file-invoice-dollar fa-3x"></i> -->
                            <i class="fa-solid fa-money-check-dollar fa-3x"></i>
                            <!-- <i class="fas fa-file-invoice-dollar fa-3x text-gray-300"></i> -->
                        </div>
                        <!-- </div> -->
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <a href="<?= base_url('distributor/invoices?invoice_id=&order_id=&agent_name=&status=paid&start_date=&end_date=') ?>" class="card card-hover-effect card-dashboard-lg border-start-success shadow h-100 py-3 text-decoration-none">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xl font-weight-bold text-success text-uppercase mb-1">
                                Tagihan Agen Sudah Lunas</div>
                            <div class="h3 mb-0 font-weight-bold text-gray-800"><?= esc($agenPaidInvoices) ?></div>
                            <div class="text-md mt-2 text-success">Lihat Detail <i class="fas fa-arrow-circle-right"></i></div>
                        </div>
                        <div class="col-auto p-3 icon-shape bg-success text-white rounded-circle">
                            <i class="fas fa-check-double fa-3x"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <a href="<?= base_url('distributor/orders/history-to-pabrik?order_id=&start_date=&end_date=&status=shipped') ?>" class="card card-hover-effect card-dashboard-lg border-start-info shadow h-100 py-3 text-decoration-none">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xl font-weight-bold text-info text-uppercase mb-1">
                                Order sedang Dikirim</div>
                            <div class="h3 mb-0 font-weight-bold text-gray-800"><?= esc($ordersShipped) ?></div>
                            <div class="text-md mt-2 text-info">Lihat Detail <i class="fas fa-arrow-circle-right"></i></div>
                        </div>
                        <div class="col-auto p-3 icon-shape bg-info text-white rounded-circle">
                            <i class="fas fa-shipping-fast fa-3x"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <a href="<?= base_url('distributor/orders/history') . '?order_id=&agent_name=&status=completed&start_date=' . esc($thisMonthStartDate) . '&end_date=' . esc($thisMonthEndDate) ?>" class="card card-hover-effect card-dashboard-lg border-start-secondary shadow h-100 py-3 text-decoration-none">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xl font-weight-bold text-secondary text-uppercase mb-1">
                                Total Order Selesai Bulan Ini</div>
                            <div class="h3 mb-0 font-weight-bold text-gray-800"><?= esc($ordersCompletedThisMonth) ?></div>
                            <div class="text-md mt-2 text-secondary">Lihat Detail <i class="fas fa-arrow-circle-right"></i></div>
                        </div>
                        <div class="col-auto p-3 icon-shape bg-secondary text-white rounded-circle">
                            <i class="fas fa-clipboard-check fa-3x"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <a href="<?= base_url('distributor/agents') ?>" class="card card-hover-effect card-dashboard-lg border-start-primary shadow h-100 py-3 text-decoration-none">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xl font-weight-bold text-primary text-uppercase mb-1">
                                Data Agen Saya</div>
                            <div class="h3 mb-0 font-weight-bold text-gray-800"><?= esc($totalAgents) ?></div>
                            <div class="text-md mt-2 text-primary">Lihat Detail <i class="fas fa-arrow-circle-right"></i></div>
                        </div>
                        <div class="col-auto p-3 icon-shape bg-primary text-white rounded-circle">
                            <i class="fas fa-users fa-3x"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <a href="<?= base_url('distributor/invoices') . '?invoice_id=&order_id=&agent_name=&status=paid' . '&start_date=' . esc($thisMonthStartDate) . '&end_date=' . esc($thisMonthEndDate)  ?>" class="card card-hover-effect card-dashboard-lg border-start-success shadow h-100 py-3 text-decoration-none">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xl font-weight-bold text-success text-uppercase mb-1">
                                Pendapatan Bulan Ini</div>
                            <div class="h3 mb-0 font-weight-bold text-gray-800">Rp <?= esc(number_format($profitThisMonth, 0, ',', '.')) ?></div>
                            <div class="text-md mt-2 text-success">Lihat Detail <i class="fas fa-arrow-circle-right"></i></div>
                        </div>
                        <div class="col-auto p-3 icon-shape bg-success text-white rounded-circle">
                            <i class="fas fa-sack-dollar fa-3x"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>

    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-primary text-white">
            <h6 class="m-0 font-weight-bold"><i class="fas fa-shipping-fast me-2"></i> Status Pengiriman Terbaru</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Order ID</th>
                            <th>Agen</th>
                            <th>Tgl Kirim</th>
                            <th>No. Resi</th>
                            <th>Status Pengiriman</th>
                            <th>Total Order Agen (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($latestShipments)): ?>
                            <tr>
                                <td colspan="7" class="text-center">Belum ada riwayat pengiriman terbaru.</td>
                            </tr>
                        <?php else: ?>
                            <?php $no = 1;
                            foreach ($latestShipments as $shipment): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><a href="<?= base_url('distributor/orders/detail-to-pabrik/' . $shipment['order_id']) ?>" class="text-decoration-none text-primary fw-bold">ORD-<?= esc($shipment['order_id'] ?? 'N/A') ?></a></td>
                                    <td><?= esc($agentUsernames[$shipment['agen_id']] ?? 'N/A') ?></td>
                                    <td><?= esc(date('d/m/Y', strtotime($shipment['shipping_date']))) ?></td>
                                    <td><?= esc($shipment['tracking_number'] ?? 'N/A') ?></td>
                                    <td>
                                        <?php
                                        $status = strtolower($shipment['delivery_status'] ?? 'pending');
                                        $badgeClass = '';
                                        switch ($status) {
                                            case 'pending':
                                                $badgeClass = 'bg-secondary';
                                                break;
                                            case 'on_transit':
                                                $badgeClass = 'bg-warning';
                                                break;
                                            case 'delivered':
                                                $badgeClass = 'bg-success';
                                                break;
                                            case 'failed':
                                                $badgeClass = 'bg-danger';
                                                break;
                                            default:
                                                $badgeClass = 'bg-info';
                                                break;
                                        }
                                        ?>
                                        <span class="badge <?= $badgeClass ?>"><?= esc(ucfirst(str_replace('_', ' ', $status))) ?></span>
                                    </td>
                                    <td>Rp <?= esc(number_format($shipment['total_amount'], 0, ',', '.')) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    /* Custom CSS untuk efek hover pada card */
    .card-hover-effect {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }

    .card-hover-effect:hover {
        transform: translateY(-5px);
        /* Sedikit naik */
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        /* Bayangan lebih jelas */
    }

    /* Ukuran card yang lebih besar dan padding */
    .card-dashboard-lg {
        padding: 1.5rem;
        /* Menambah padding internal */
        min-height: 150px;
        /* Tinggi minimum untuk konsistensi */
        display: flex;
        /* Untuk aligment konten vertikal */
        align-items: center;
        /* Vertikal tengah */
    }

    .card-dashboard-lg .card-body {
        width: 100%;
        /* Memastikan body mengisi card */
    }

    .card-dashboard-lg .text-xl {
        font-size: 1.1rem;
        /* Ukuran font lebih besar untuk judul */
    }

    .card-dashboard-lg .h3 {
        font-size: 2rem;
        /* Ukuran font lebih besar untuk angka */
    }

    .card-dashboard-lg .fa-3x {
        font-size: 3em;
        /* Ukuran ikon lebih besar */
    }

    .card-dashboard-lg .text-sm {
        font-size: 0.85rem;
        /* Ukuran font untuk "Lihat Detail" */
    }


    /* Border kiri untuk card dengan warna yang lebih ekspresif/bervariasi */
    .border-start-primary {
        border-left: .25rem solid #0d6efd !important;
    }

    /* Blue */
    .border-start-success {
        border-left: .25rem solid #198754 !important;
    }

    /* Green */
    .border-start-info {
        border-left: .25rem solid #0dcaf0 !important;
    }

    /* Cyan */
    .border-start-warning {
        border-left: .25rem solid #ffc107 !important;
    }

    /* Yellow */
    .border-start-danger {
        border-left: .25rem solid #dc3545 !important;
    }

    /* Red */
    .border-start-secondary {
        border-left: .25rem solid #6c757d !important;
    }

    /* Gray */
    .border-start-light {
        border-left: .25rem solid #f8f9fa !important;
    }

    /* Light gray */


    /* Text colors (sesuaikan dengan warna Bootstrap Anda) */
    .text-primary {
        color: #0d6efd !important;
    }

    .text-success {
        color: #198754 !important;
    }

    .text-info {
        color: #0dcaf0 !important;
    }

    .text-warning {
        color: #ffc107 !important;
    }

    .text-danger {
        color: #dc3545 !important;
    }

    .text-secondary {
        color: #6c757d !important;
    }

    .text-light {
        color: #f8f9fa !important;
    }

    /* Untuk teks coming soon */

    .text-gray-800 {
        color: #343a40 !important;
    }

    /* Darker text for main numbers */
    .text-gray-300 {
        color: #dee2e6 !important;
    }

    /* Lighter text for icons */

    /* Font weight */
    .fw-bold {
        font-weight: 700 !important;
    }

    /* Bootstrap 5 standard */
    .font-weight-bold {
        font-weight: 700 !important;
    }

    /* Keeping for compatibility if needed */
    .text-uppercase {
        text-transform: uppercase !important;
    }

    /* Fix for no-gutters for older templates, though modern BS5 uses g-X for gutters */
    .no-gutters {
        margin-right: 0;
        margin-left: 0;
    }

    .no-gutters>.col,
    .no-gutters>[class*="col-"] {
        padding-right: 0;
        padding-left: 0;
    }
</style>

<?= $this->endSection() ?>