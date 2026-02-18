<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="container-fluid p-4 p-md-5">
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body d-flex align-items-center">
            <i class="fas fa-file-invoice fa-2x me-3" style="color: #8B5CF6;"></i>
            <h1 class="h3 mb-0 fw-bold text-dark"><?= $title ?? 'Detail Tagihan' ?></h1>
        </div>
    </div>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-light py-3">
            <h2 class="h5 mb-0 fw-bold">Informasi Tagihan #<?= esc($invoice['id']) ?></h2>
        </div>
        <div class="card-body p-4">
            <div class="row g-3">
                <div class="col-lg-6">
                    <dl class="row mb-0">
                        <dt class="col-sm-5 col-md-4 fw-semibold">Order ID</dt>
                        <dd class="col-sm-7 col-md-8">: <?= esc($invoice['order_id']) ?></dd>

                        <dt class="col-sm-5 col-md-4 fw-semibold">Tgl. Tagihan</dt>
                        <dd class="col-sm-7 col-md-8">: <?= date('d M Y', strtotime($invoice['invoice_date'])) ?></dd>

                        <dt class="col-sm-5 col-md-4 fw-semibold">Status Tagihan</dt>
                        <dd class="col-sm-7 col-md-8">:
                            <span class="badge rounded-pill
                                <?php
                                $status = $invoice['status'];
                                $displayStatus = ''; // Variabel untuk menyimpan status dalam Bahasa Indonesia

                                switch ($status) {
                                    case 'unpaid':
                                    case 'pending': // Jika 'pending' juga dianggap unpaid untuk konteks ini
                                        echo 'bg-warning-subtle text-warning-emphasis';
                                        $displayStatus = 'Belum Lunas';
                                        break;
                                    case 'paid':
                                        echo 'bg-success-subtle text-success-emphasis';
                                        $displayStatus = 'Lunas';
                                        break;
                                    case 'cancelled':
                                        echo 'bg-danger-subtle text-danger-emphasis';
                                        $displayStatus = 'Dibatalkan';
                                        break;
                                    default:
                                        echo 'bg-secondary-subtle text-secondary-emphasis';
                                        // Default jika status tidak dikenal, bisa disesuaikan lagi jika ada status lain
                                        // Misalnya: 'unknown' -> 'Tidak Diketahui'
                                        $displayStatus = ucfirst($status);
                                        break;
                                }
                                ?>">
                                <?= esc($displayStatus) ?>
                            </span>
                        </dd>
                        <dt class="col-sm-5 col-md-4 fw-semibold">Total Tagihan</dt>
                        <dd class="col-sm-7 col-md-8">:
                            <span class="fs-5 fw-bold text-success">
                                Rp<?= number_format($invoice['amount_total'], 0, ',', '.')
                                    ?>
                            </span>
                        </dd>


                        <?php if ($invoice['status'] === 'paid' && !empty($invoice['payment_date'])): ?>
                            <dt class="col-sm-5 col-md-4 fw-semibold">Tgl. Pembayaran</dt>
                            <dd class="col-sm-7 col-md-8">: <?= date('d M Y, H:i', strtotime($invoice['payment_date'])) ?></dd>
                        <?php endif; ?>
                    </dl>
                </div>
                <div class="col-lg-6">
                    <dl class="row mb-0">
                        <dt class="col-sm-5 col-md-4 fw-semibold">Distributor</dt>
                        <dd class="col-sm-7 col-md-8">: <?= esc($distributor['username'] ?? 'N/A') ?></dd>
                        <dt class="col-sm-5 col-md-4 fw-semibold">Distributor</dt>
                        <dd class="col-sm-7 col-md-8">: <?= esc($distributor['email'] ?? 'N/A') ?></dd>
                        <dt class="col-sm-5 col-md-4 fw-semibold">Distributor</dt>
                        <dd class="col-sm-7 col-md-8">: <?= esc($distributor['no_telpon'] ?? 'N/A') ?></dd>

                        <dt class="col-sm-5 col-md-4 fw-semibold">Tgl. Order</dt>
                        <dd class="col-sm-7 col-md-8">: <?= date('d M Y, H:i', strtotime($order['order_date'])) ?></dd>

                        <dt class="col-sm-5 col-md-4 fw-semibold">Status Order</dt>
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
                        <dd class="col-sm-7 col-md-8">: <?= esc($translatedStatus) ?></dd>

                    </dl>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-light py-3">
                <h2 class="h5 mb-0 fw-bold">Rincian Item Order</h2>
            </div>
            <div class="card-body p-4">
                <?php if (empty($orderItems)): ?>
                    <p class="text-muted mb-0">Tidak ada item dalam order ini.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" class="text-uppercase small">Produk</th>
                                    <th scope="col" class="text-uppercase small text-center">Kuantitas</th>
                                    <th scope="col" class="text-uppercase small text-end">Harga Satuan</th>
                                    <th scope="col" class="text-uppercase small text-end">Sub Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orderItems as $item): ?>
                                    <tr>
                                        <td class="fw-semibold"><?= esc($productDetails[$item['product_id']] ?? 'N/A') ?></td>
                                        <td class="text-center"><?= esc($item['quantity']) ?></td>
                                        <td class="text-end text-nowrap">Rp<?= number_format($item['unit_price'], 0, ',', '.') ?></td>
                                        <td class="text-end text-nowrap fw-semibold">Rp<?= number_format($item['sub_total'], 0, ',', '.') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
                <hr>
                <a href="<?= base_url('agen/invoices') ?>" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i> Kembali ke Halaman Tagihan</a>
            </div>
        </div>
    </div>

    <?= $this->endSection(); ?>