<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <h1 class="mb-4"><?= $title ?></h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-primary text-white">
            <h6 class="m-0 font-weight-bold"><i class="fas fa-info-circle me-2"></i> Informasi Order #ORD-<?= esc($order['id']) ?> ke Pabrik</h6>
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
                        $badgeClass = '';
                        $text = '';
                        switch ($status) {
                            case 'pending':
                                $badgeClass = 'bg-secondary';
                                $text = 'Tertunda';
                                break;
                            case 'approved':
                                $badgeClass = 'bg-info';
                                $text = 'Di Setujui';
                                break;
                            case 'processing':
                                $badgeClass = 'bg-warning';
                                $text = 'Di Proses';
                                break;
                            case 'shipped':
                                $badgeClass = 'bg-primary';
                                $text = 'Di Kirim';
                                break;
                            case 'completed':
                                $badgeClass = 'bg-success';
                                $text = 'selesai !';
                                break;
                            case 'rejected':
                                $badgeClass = 'bg-danger';
                                $text = 'Di Tolak';
                                break;
                            default:
                                $badgeClass = 'bg-secondary';
                                $text = 'Tidak Di Ketahui !';
                                break;
                        }
                        ?>
                        <span class="badge <?= $badgeClass ?>"><?= esc(ucfirst(str_replace('_', ' ', $text))) ?></span>
                    </p>
                    <p><strong>Total Jumlah:</strong> Rp <?= esc(number_format($order['total_amount'], 0, ',', '.')) ?></p>
                    <?php if ($order['delivery_date']): ?>
                        <p><strong>Tanggal Diterima:</strong> <?= date('d/m/Y H:i', strtotime($order['delivery_date'])) ?></p>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <h5>Informasi Agen</h5>
                    <p><strong>Nama Agen:</strong> <?= esc($agenData['username'] ?? 'Tidak Ada !') ?></p>
                    <p><strong>Email Agen:</strong> <?= esc($agenData['email'] ?? 'Tidak Ada !') ?></p>
                    <p><strong>No Telpon Agen:</strong> <?= esc($agenData['no_telpon'] ?? 'Tidak Ada !') ?></p>
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
                                    <td><?= esc($productNames[$item['product_id']] ?? 'Tidak Ada !') ?></td>
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
            <a href="<?= base_url('distributor/orders/history-to-pabrik') ?>" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i> Kembali Order Valid Pabrik</a>
        </div>
    </div>
</div>
<?= $this->endSection() ?>