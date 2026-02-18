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
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs font-weight-bold text-gray-800 text-uppercase mb-1">
                                Total Agen Saya</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= esc($totalAgents) ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs font-weight-bold text-gray-800 text-uppercase mb-1">
                                Order Masuk</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= esc($totalOrdersPending) ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-receipt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs font-weight-bold text-gray-800 text-uppercase mb-1">
                                Pengiriman Berlangsung</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= esc($totalShipmentsOnTransit) ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-truck-moving fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs font-weight-bold text-gray-800 text-uppercase mb-1">
                                Order Selesai</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= esc($totalOrdersCompleted) ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-primary text-white">
            <h6 class="m-0 font-weight-bold"><i class="fas fa-shipping-fast me-2"></i> Status Pengiriman Terbaru</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Order ID</th>
                            <th>Agen</th>
                            <!-- <th>Pabrik</th> -->
                            <th>Tgl Kirim</th>
                            <!-- <th>Estimasi Sampai</th> -->
                            <th>Jumlah Produk</th>
                            <th>No. Resi</th>
                            <th>Status Order</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($latestShipments)): ?>
                            <tr>
                                <td colspan="9" class="text-center">Belum ada riwayat pengiriman terbaru.</td>
                            </tr>
                        <?php else: ?>
                            <?php $no = 1;
                            foreach ($latestShipments as $shipment): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= esc($shipment['order_id'] ?? 'N/A') ?></td>
                                    <td><?= esc($agentUsernames[$shipment['agent_id']] ?? 'N/A') ?></td>
                                    <!-- <td><?= '.' #esc($pabrikUsernames[$orderData['pabrik_id']] ?? 'N/A') ?></td> -->
                                    <td><?= esc(date('d/m/Y', strtotime($shipment['shipping_date']))) ?></td>
                                    <!-- <td><?='.' #esc(date('d/m/Y', strtotime($shipment['delivery_datetime']))) ?></td> -->
                                    <td>
                                        <?php
                                        // Untuk Jumlah Produk, kita perlu mengambil dari order_items
                                        // Ini akan membutuhkan query tambahan atau join yang lebih kompleks
                                        // Untuk sementara, saya akan menampilkannya sebagai "Lihat Detail" atau "N/A"
                                        // Anda bisa mengambil total_amount dari orders.total_amount atau menghitung quantity dari order_items
                                        echo 'Lihat Detail'; // Placeholder
                                        ?>
                                    </td>
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