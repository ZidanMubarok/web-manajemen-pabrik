<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-file-invoice-dollar me-2"></i><?= $title ?></h1>
    </div>

    <!-- Notifikasi -->
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

    <!-- [PERUBAHAN 1] Filter yang Dapat Dilipat (Accordion) -->
    <div class="accordion mb-4" id="filterAccordion">
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingOne">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                    <i class="fas fa-filter me-2"></i> Tampilkan Filter Pencarian
                </button>
            </h2>
            <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#filterAccordion">
                <div class="accordion-body">
                    <form action="<?= base_url('agen/invoices') ?>" method="get">
                        <div class="row g-3">
                            <div class="col-md-6 col-lg-3">
                                <label for="invoice_id" class="form-label">ID Tagihan:</label>
                                <input type="text" class="form-control" id="invoice_id" name="invoice_id" placeholder="#123" value="<?= esc($searchInvoiceId ?? '') ?>">
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <label for="order_id" class="form-label">ID Order:</label>
                                <input type="text" class="form-control" id="order_id" name="order_id" placeholder="#456" value="<?= esc($searchOrderId ?? '') ?>">
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <label for="status" class="form-label">Status:</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="">Semua Status</option>
                                    <?php foreach ($allInvoiceStatuses as $statusOption):
                                        $displayOption = match ($statusOption) {
                                            'unpaid' => 'Belum Lunas',
                                            'paid' => 'Lunas',
                                            'cancelled' => 'Dibatalkan',
                                            default => ucfirst($statusOption),
                                        };
                                    ?>
                                        <option value="<?= esc($statusOption) ?>" <?= (isset($statusFilter) && $statusFilter == $statusOption) ? 'selected' : '' ?>>
                                            <?= esc($displayOption) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <label for="start_date" class="form-label">Tanggal Mulai:</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" value="<?= esc($startDate ?? '') ?>">
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <label for="end_date" class="form-label">Tanggal Akhir:</label>
                                <input type="date" class="form-control" id="end_date" name="end_date" value="<?= esc($endDate ?? '') ?>">
                            </div>
                            <div class="col-12 d-flex justify-content-end gap-2">
                                <a href="<?= base_url('agen/invoices') ?>" class="btn btn-secondary"><i class="fas fa-sync-alt me-1"></i> Reset</a>
                                <button type="submit" class="btn btn-primary"><i class="fas fa-search me-1"></i> Terapkan Filter</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <!-- [PERUBAHAN 2] Mengganti Table dengan Daftar Card -->
    <div class="invoice-list">
        <?php if (empty($invoices)): ?>
            <div class="text-center p-5 card shadow-sm">
                <i class="far fa-folder-open fa-4x text-muted mb-3"></i>
                <p class="fs-5 text-muted">Tidak ada tagihan yang cocok dengan filter Anda.</p>
            </div>
        <?php else: ?>
            <?php foreach ($invoices as $invoice): ?>
                <div class="card shadow-sm mb-3">
                    <div class="card-body">
                        <div class="row g-2">
                            <!-- Kolom Info Utama -->
                            <div class="col-md-8">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h5 class="card-title fw-bold mb-0">Tagihan #<?= esc($invoice['id']) ?></h5>
                                </div>
                                <p class="card-text mb-1">
                                    <small class="text-muted">ID Order: <?= esc($invoice['order_id']) ?></small>
                                </p>
                                <p class="card-text">
                                    <strong class="me-2">Total Tagihan:</strong>
                                    <span class="fs-5 fw-bold text-success">Rp<?= number_format($invoice['amount_total'], 0, ',', '.') ?></span>
                                </p>
                            </div>
                            <!-- Kolom Status & Aksi -->
                            <div class="col-md-4 text-md-end">
                                <?php
                                $status = $invoice['status'];
                                $badgeClass = '';
                                $displayStatus = '';
                                switch ($status) {
                                    case 'unpaid':
                                        $badgeClass = 'bg-warning text-dark';
                                        $displayStatus = 'Belum Lunas';
                                        break;
                                    case 'paid':
                                        $badgeClass = 'bg-success text-white';
                                        $displayStatus = 'Lunas';
                                        break;
                                    case 'cancelled':
                                        $badgeClass = 'bg-danger text-white';
                                        $displayStatus = 'Dibatalkan';
                                        break;
                                    default:
                                        $badgeClass = 'bg-secondary text-white';
                                        $displayStatus = ucfirst($status);
                                        break;
                                }
                                ?>
                                <span class="badge rounded-pill fs-6 <?= $badgeClass ?> mb-2"><?= esc($displayStatus) ?></span>
                                <div class="text-muted small mb-3">
                                    Tgl. Tagihan: <?= date('d M Y', strtotime($invoice['invoice_date'])) ?>
                                </div>
                                <a href="<?= base_url('agen/invoices/detail/' . $invoice['id']) ?>" class="btn btn-sm btn-primary w-100 w-md-auto">
                                    <i class="fas fa-eye me-1"></i>Lihat Detail
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Paginasi -->
    <div class="mt-4 d-flex justify-content-between">
        <!-- Pager atau Pagination -->
        <div>
            <?php
            $totalItems = $pager->getTotal();
            $firstItem = ($totalItems > 0) ? (($pager->getCurrentPage() - 1) * $pager->getPerPage()) + 1 : 0;
            $lastItem = ($totalItems > 0) ? min($firstItem + $pager->getPerPage() - 1, $totalItems) : 0;
            ?>
            Menampilkan <?= $firstItem ?> sampai <?= $lastItem ?> dari <?= $totalItems ?> Tagihan anda
        </div>
        <div>
            <!-- Pager atau Pagination -->
            <?= $pager->links('default', 'bootstrap_pagination') ?>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>