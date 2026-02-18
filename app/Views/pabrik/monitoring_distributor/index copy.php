<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <h1 class="mb-4"><?= $title ?></h1>
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-primary text-white">
            <h6 class="m-0 font-weight-bold"><i class="fas fa-chart-line me-2"></i> Performa Distributor</h6>
        </div>
        <div class="card-body">
            <?php if (empty($distributorPerformance)): ?>
                <div class="alert alert-info text-center" role="alert">
                    Belum ada data performa distributor. Pastikan ada distributor terdaftar dan sudah ada order yang masuk.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Distributor</th>
                                <th>Total Order</th>
                                <th>Total Kuantitas Produk Dipesan</th>
                                <th>Total Pendapatan (Rp)</th>
                                <th>Order Aktif</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1;
                            foreach ($distributorPerformance as $dp): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= esc($dp['username']) ?></td>
                                    <td><?= esc($dp['total_orders']) ?></td>
                                    <td><?= esc(number_format($dp['total_products_ordered'], 0, ',', '.')) ?></td>
                                    <td>Rp <?= esc(number_format($dp['total_revenue'], 0, ',', '.')) ?></td>
                                    <td><?= esc($dp['active_orders']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>