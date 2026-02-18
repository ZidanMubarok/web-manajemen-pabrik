<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('title') ?>
<?= $title ?>
<?= $this->endSection() ?>
<?= $this->section('content') ?>
<div class="container-fluid">
    <h1 class="mb-4"><?= $title ?></h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-primary text-white">
            <h6 class="m-0 font-weight-bold"><i class="fas fa-info-circle me-2"></i> Informasi Order #ORD-<?= esc($order['id']) ?></h6>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <h5>Detail Order</h5>
                    <p><strong>Order ID:</strong> ORD-<?= esc($order['id']) ?></p>
                    <p><strong>Tanggal Order:</strong> <?= date('d/m/Y H:i', strtotime($order['order_date'])) ?></p>
                    <p><strong>Status Order:</strong>
                        <?php
                        $status = strtolower($order['status']);
                        $translatedStatus = '';

                        switch ($status) {
                            case 'pending':
                                $translatedStatus = 'Menunggu Konfirmasi';
                                break;
                            case 'approved':
                                $translatedStatus = 'Disetujui';
                                break;
                            case 'processing':
                                $translatedStatus = 'Diproses';
                                break;
                            case 'shipped':
                                $translatedStatus = 'Dikirim';
                                break;
                            case 'completed':
                                $translatedStatus = 'Selesai';
                                break;
                            case 'rejected':
                                $translatedStatus = 'Ditolak';
                                break;
                            default:
                                $translatedStatus = ucfirst($status); // Fallback jika status tidak dikenal
                                break;
                        }
                        ?>
                        <span class="col-sm-7 col-md-8">: <?= esc($translatedStatus) ?></span>
                    </p>
                    <p><strong>Total Jumlah:</strong> Rp <?= esc(number_format($order['total_amount'], 0, ',', '.')) ?></p>
                    <?php if ($order['delivery_date']): ?>
                        <p><strong>Tanggal Diterima:</strong> <?= date('d/m/Y H:i', strtotime($order['delivery_date'])) ?></p>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <h5>Informasi Distributor</h5>
                    <p><strong>Nama Distributor:</strong> <?= esc($distributor['username'] ?? 'N/A') ?></p>
                    <p><strong>Email Distributor:</strong> <?= esc($distributor['email'] ?? 'N/A') ?></p>
                    <p><strong>No Telpon Distributor:</strong> <?= esc($distributor['no_telpon'] ?? 'N/A') ?></p>
                    <p><strong>Alamat Distributor:</strong> <?= esc($distributor['alamat'] ?? 'N/A') ?></p>
                </div>
            </div>

            <h5>Item Produk dalam Order</h5>
            <div class="table-responsive">
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
            <a href="<?= base_url('agen/orders/history') ?>" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i> Kembali ke Riwayat Order</a>
        </div>
    </div>
</div>
<?= $this->endSection() ?>