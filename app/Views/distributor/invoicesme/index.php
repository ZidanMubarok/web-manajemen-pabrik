<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('title') ?>
<?= esc($title) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= esc($title) ?></h1>
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

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-primary">
            <h6 class="m-0 font-weight-bold text-white"><i class="fas fa-file-invoice-dollar me-2"></i>Daftar Tagihan Saya</h6>
            <!-- BARU: Tombol untuk menampilkan/menyembunyikan filter -->
            <button class="btn btn-light btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse" aria-expanded="true" aria-controls="filterCollapse">
                <i class="fas fa-filter me-1"></i> Filter & Pencarian
            </button>
        </div>

        <div class="card-body">
            <!-- BARU: Wrapper untuk filter yang bisa di-collapse -->
            <div class="collapse show" id="filterCollapse">
                <form action="<?= base_url('distributor/invoicesme') ?>" method="get" class="mb-4 border-bottom pb-4">
                    <div class="row g-3">
                        <div class="col-md-6 col-lg-3">
                            <label for="invoiceNumber" class="form-label">No. Tagihan</label>
                            <input type="text" class="form-control" id="invoiceNumber" name="invoice_number" value="<?= esc($invoiceNumber) ?>" placeholder="Cari No. Tagihan...">
                        </div>
                        <div class="col-md-6 col-lg-2">
                            <label for="statusFilter" class="form-label">Status Pembayaran</label>
                            <select class="form-select" id="statusFilter" name="status">
                                <option value="">Semua Status</option>
                                <option value="unpaid" <?= $statusFilter === 'unpaid' ? 'selected' : '' ?>>Belum Dibayar</option>
                                <option value="paid" <?= $statusFilter === 'paid' ? 'selected' : '' ?>>Lunas</option>
                                <option value="partially_paid" <?= $statusFilter === 'partially_paid' ? 'selected' : '' ?>>Dibayar Sebagian</option>
                                <option value="cancelled" <?= $statusFilter === 'cancelled' ? 'selected' : '' ?>>Dibatalkan</option>
                            </select>
                        </div>
                        <div class="col-md-6 col-lg-2">
                            <label for="startDate" class="form-label">Dari Tanggal</label>
                            <input type="date" class="form-control" id="startDate" name="start_date" value="<?= esc($startDate) ?>">
                        </div>
                        <div class="col-md-6 col-lg-2">
                            <label for="endDate" class="form-label">Sampai Tanggal</label>
                            <input type="date" class="form-control" id="endDate" name="end_date" value="<?= esc($endDate) ?>">
                        </div>
                        <div class="col-lg-3 d-flex align-items-end">
                            <!-- <div class="d-grid gap-1"> -->
                            <div class="btn-group w-100 gap-2" role="group">
                                <button type="submit" class="btn btn-outline-primary"><i class="fas fa-search"></i> Cari</button>
                                <a href="<?= base_url('distributor/invoicesme') ?>" class="btn btn-outline-secondary"><i class="fas fa-sync-alt"></i> Reset</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>No. Tagihan</th>
                            <th>Tgl. Tagihan</th>
                            <th>Jatuh Tempo</th>
                            <th class="text-end">Total (Rp)</th>
                            <th class="text-center">Status</th>
                            <!-- <th>Tgl. Bayar</th> -->
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($invoices)): ?>
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <i class="fas fa-info-circle fa-2x text-muted mb-2"></i>
                                    <p class="mb-0">Tidak ada tagihan yang cocok dengan kriteria filter Anda.</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php $no = $startRow; // Nomor urut dimulai dari startRow
                            foreach ($invoices as $invoice): ?>
                                <tr class="align-middle">
                                    <td><?= $no++ ?></td>
                                    <td>
                                        <div class="fw-bold"><?= esc($invoice['invoice_number']) ?></div>
                                    </td>
                                    <td><?= date('d M Y', strtotime($invoice['invoice_date'])) ?></td>
                                    <td><?= date('d M Y', strtotime($invoice['due_date'])) ?></td>
                                    <td class="text-end fw-bold">
                                        <?= esc(number_format($invoice['total_amount'], 0, ',', '.')) ?>
                                    </td>
                                    <td class="text-center">
                                        <?php
                                        $status = strtolower($invoice['status']);
                                        $badgeClass = '';
                                        $text = '';
                                        switch ($status) {
                                            case 'unpaid':
                                                $badgeClass = 'bg-danger';
                                                $text = 'Belum Dibayar';
                                                break;
                                            case 'paid':
                                                $badgeClass = 'bg-success-soft text-success';
                                                $text = 'Lunas';
                                                break;
                                            case 'partially_paid':
                                                $badgeClass = 'bg-warning text-dark';
                                                $text = 'Bayar Sebagian';
                                                break;
                                            case 'cancelled':
                                                $badgeClass = 'bg-secondary';
                                                $text = 'Dibatalkan';
                                                break;
                                            default:
                                                $badgeClass = 'bg-info-soft text-info';
                                                $text = 'Tidak Diketahui';
                                                break;
                                        }
                                        ?>
                                        <span class="badge rounded-pill px-2 py-1 <?= $badgeClass ?>"><?= esc($text) ?></span>
                                        <?php if ($invoice['status'] == 'paid' && !empty($invoice['payment_date'])): ?>
                                            <div class="small text-muted mt-1" title="Tanggal Pembayaran">
                                                <i class="far fa-clock me-1"></i><?= esc(date('d M Y, H:i', strtotime($invoice['payment_date']))) ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?= base_url('distributor/invoicesme/detail/' . $invoice['id']) ?>" class="btn btn-info btn-sm" title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <!-- BARU: Informasi jumlah data dan pagination -->
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-2">
                <div class="text-muted mb-2 mb-md-0">
                    <?php if ($totalRows > 0) : ?>
                        Menampilkan <b><?= $startRow ?></b> sampai <b><?= $endRow ?></b> dari <b><?= $totalRows ?></b> Tagihan Saya.
                    <?php else : ?>
                        Tidak ada data untuk ditampilkan.
                    <?php endif; ?>
                </div>
                <!-- Pager akan ditampilkan di sini oleh CodeIgniter -->
                <div class="pagination-container">
                    <?= $pager->links('invoices', 'bootstrap_pagination') ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- PENTING: Hapus script inisialisasi DataTable dari sini -->
<!-- <script>
    $(document).ready(function() {
        $('#dataTable').DataTable({...});
    });
</script> -->

<!-- CSS Tambahan untuk Tampilan Lebih Aesthetic (Letakkan di file CSS utama Anda atau di <style> block) -->
<style>
    .bg-danger-soft {
        background-color: rgba(220, 53, 69, 0.1);
    }

    .text-danger {
        color: #dc3545 !important;
    }

    .bg-success-soft {
        background-color: rgba(25, 135, 84, 0.1);
    }

    .text-success {
        color: #198754 !important;
    }

    .bg-warning-soft {
        background-color: rgba(255, 193, 7, 0.1);
    }

    .text-warning {
        color: #ffc107 !important;
    }

    .bg-secondary-soft {
        background-color: rgba(108, 117, 125, 0.1);
    }

    .text-secondary {
        color: #6c757d !important;
    }

    .bg-info-soft {
        background-color: rgba(13, 202, 240, 0.1);
    }

    .text-info {
        color: #0dcaf0 !important;
    }

    .pagination .page-link {
        border-radius: 0.25rem;
        margin: 0 2px;
    }

    .pagination .page-item.active .page-link {
        z-index: 3;
        color: #fff;
        background-color: #0d6efd;
        /* Warna primary Bootstrap */
        border-color: #0d6efd;
    }
</style>

<?= $this->endSection() ?>