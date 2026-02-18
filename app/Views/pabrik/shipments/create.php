<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <h1 class="mb-4"><?= $title ?></h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-primary text-white">
            <h6 class="m-0 font-weight-bold"><i class="fas fa-truck-loading me-2"></i> Detail Order untuk Pengiriman</h6>
        </div>
        <div class="card-body">
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= session()->getFlashdata('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('errors')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        <?php foreach (session()->getFlashdata('errors') as $error): ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="row mb-4">
                <div class="col-md-6">
                    <h5>Informasi Order</h5>
                    <p><strong>Order ID:</strong> ORD-<?= esc($order['id']) ?></p>
                    <p><strong>Tanggal Order:</strong> <?= date('d/m/Y H:i', strtotime($order['order_date'])) ?></p>
                    <p><strong>Total Jumlah:</strong> Rp <?= esc(number_format($order['total_amount'], 0, ',', '.')) ?></p>
                    <p><strong>Status Order:</strong> <span class="badge bg-info"><?= esc(ucfirst($order['status'])) ?></span></p>
                </div>
                <div class="col-md-6">
                    <h5>Informasi Distributor</h5>
                    <p><strong>Nama Distributor:</strong> <?= esc($distributor['username'] ?? 'N/A') ?></p>
                    <p><strong>Email Distributor:</strong> <?= esc($distributor['email'] ?? 'N/A') ?></p>
                </div>
            </div>

            <h5>Item Produk dalam Order</h5>
            <div class="table-responsive mb-4">
                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Produk</th>
                            <th>Kuantitas</th>
                            <th>Harga Satuan (Rp)</th>
                            <th>Sub Total (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($orderItems)): ?>
                            <tr>
                                <td colspan="5" class="text-center">Tidak ada item dalam order ini.</td>
                            </tr>
                        <?php else: ?>
                            <?php $no = 1;
                            foreach ($orderItems as $item): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= esc($productNames[$item['product_id']] ?? 'N/A') ?></td>
                                    <td><?= esc($item['quantity']) ?></td>
                                    <td>Rp <?= esc(number_format($item['unit_price'], 0, ',', '.')) ?></td>
                                    <td>Rp <?= esc(number_format($item['sub_total'], 0, ',', '.')) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <hr>

            <form action="<?= base_url('pabrik/shipments/store') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="order_id" value="<?= esc($order['id']) ?>">

                <div class="mb-3">
                    <label for="shipping_date" class="form-label">Tanggal Pengiriman:</label>
                    <input type="date" class="form-control" id="shipping_date" name="shipping_date" value="<?= old('shipping_date', date('Y-m-d')) ?>" required>
                </div>
                <div class="mb-3">
                    <label for="tracking_number" class="form-label">Nomor Resi / Tracking Number:</label>
                    <input type="text" class="form-control" id="tracking_number" name="tracking_number" value="<?= old('tracking_number') ?>" placeholder="Masukkan nomor resi unik" required>
                    <small class="form-text text-muted">Nomor resi harus unik dan akan digunakan untuk melacak pengiriman.</small>
                </div>

                <button type="submit" class="btn btn-primary"><i class="fas fa-shipping-fast me-1"></i> Buat Pengiriman</button>
                <a href="<?= base_url('pabrik') ?>" class="btn btn-secondary ms-2"><i class="fas fa-arrow-left me-1"></i> Batal</a>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>