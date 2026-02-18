<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    /* Custom styles for a more modern look and feel */
    .card-header.bg-secondary {
        background-color: #6c757d !important;
    }

    .btn-toggle-filter {
        transition: transform 0.3s ease;
    }

    .btn-toggle-filter.collapsed {
        transform: rotate(0deg);
    }

    .btn-toggle-filter:not(.collapsed) {
        transform: rotate(180deg);
    }

    .table-hover tbody tr:hover {
        background-color: #f8f9fa;
    }

    .badge {
        font-size: 0.8rem;
        padding: 0.5em 0.9em;
    }

    /* Responsive adjustments */
    @media (max-width: 767.98px) {
        .filter-form .row {
            display: flex;
            flex-direction: column;
        }

        .filter-form .col-md-3,
        .filter-form .col-auto {
            width: 100%;
            margin-bottom: 1rem;
        }
    }
</style>

<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-12">
            <h3 class="fw-bold"><?= esc($pageTitle ?? $title) ?></h3>

            <!-- Search and Filter Card - Collapsible -->
            <div class="card shadow-sm mt-4 mb-4">
                <div class="card-header bg-light d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#filterCollapse" role="button" aria-expanded="false" aria-controls="filterCollapse">
                    <h6 class="m-0 text-dark"><i class="fas fa-filter me-2"></i>Filter dan Pencarian Tagihan</h6>
                    <a class="btn btn-sm btn-light btn-toggle-filter" data-bs-toggle="collapse" href="#filterCollapse" role="button" aria-expanded="false" aria-controls="filterCollapse">
                        <i class="fas fa-chevron-down"></i>
                    </a>
                </div>
                <div class="collapse" id="filterCollapse">
                    <div class="card-body">
                        <form action="<?= base_url('distributor/invoices') ?>" method="get" class="row g-3 align-items-end filter-form">
                            <div class="col-md-3 col-lg-2">
                                <label for="invoice_id" class="form-label">ID Tagihan:</label>
                                <input type="text" class="form-control form-control-sm" id="invoice_id" name="invoice_id" placeholder="Cari ID Tagihan..." value="<?= esc($searchInvoiceId ?? '') ?>">
                            </div>
                            <div class="col-md-3 col-lg-2">
                                <label for="order_id" class="form-label">ID Order:</label>
                                <input type="text" class="form-control form-control-sm" id="order_id" name="order_id" placeholder="Cari ID Order..." value="<?= esc($searchOrderId ?? '') ?>">
                            </div>
                            <div class="col-md-3 col-lg-2">
                                <label for="agent_name" class="form-label">Nama Agen:</label>
                                <select class="form-select form-select-sm" id="agent_name" name="agent_name">
                                    <option value="">Semua Agen</option>
                                    <?php foreach ($agents as $agent) : ?>
                                        <option value="<?= esc($agent['username']) ?>" <?= (isset($searchAgentName) && $searchAgentName == $agent['username']) ? 'selected' : '' ?>>
                                            <?= esc($agent['username']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3 col-lg-2">
                                <label for="status" class="form-label">Status Tagihan:</label>
                                <select class="form-select form-select-sm" id="status" name="status">
                                    <option value="">Semua Status</option>
                                    <option value="unpaid" <?= $statusFilter === 'unpaid' ? 'selected' : '' ?>>Belum Dibayar</option>
                                    <option value="paid" <?= $statusFilter === 'paid' ? 'selected' : '' ?>>Lunas</option>
                                    <option value="cancelled" <?= $statusFilter === 'cancelled' ? 'selected' : '' ?>>Dibatalkan</option>
                                </select>
                            </div>
                            <div class="col-md-3 col-lg-2">
                                <label for="start_date" class="form-label">Dari Tanggal:</label>
                                <input type="date" class="form-control form-control-sm" id="start_date" name="start_date" value="<?= esc($startDate ?? '') ?>">
                            </div>
                            <div class="col-md-3 col-lg-2">
                                <label for="end_date" class="form-label">Sampai Tanggal:</label>
                                <input type="date" class="form-control form-control-sm" id="end_date" name="end_date" value="<?= esc($endDate ?? '') ?>">
                            </div>
                            <div class="col-12 col-md-auto d-flex gap-2">
                                <button type="submit" class="btn btn-primary btn-sm flex-grow-1"><i class="fas fa-search me-1"></i> Cari</button>
                                <a href="<?= base_url('distributor/invoices') ?>" class="btn btn-secondary btn-sm flex-grow-1"><i class="fas fa-redo me-1"></i> Reset</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Invoices Table Card -->
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-primary text-white">
                    <h6 class="m-0"><i class="fas fa-file-invoice-dollar me-2"></i>Daftar Tagihan</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <?php if (empty($invoices)) : ?>
                            <div class="text-center py-5">
                                <i class="fas fa-folder-open fa-4x text-muted mb-3"></i>
                                <h5 class="text-muted">Data Tidak Ditemukan</h5>
                                <p class="text-muted">Tidak ada tagihan yang cocok dengan kriteria filter Anda.</p>
                            </div>
                        <?php else : ?>
                            <table class="table table-bordered table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col" class="text-center">No.</th>
                                        <th scope="col">Detail Tagihan</th>
                                        <th scope="col">Agen</th>
                                        <th scope="col" class="text-end">Jumlah Total</th>
                                        <th scope="col" class="text-center d-none d-lg-table-cell">Tanggal Tagihan</th>
                                        <th scope="col" class="text-center">Status</th>
                                        <th scope="col" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = ($pager->getCurrentPage() - 1) * $pager->getPerPage() + 1;
                                    foreach ($invoices as $invoice) : ?>
                                        <tr>
                                            <td class="text-center"><?= $no++ ?></td>
                                            <td>
                                                <div class="fw-bold">ID: <?= esc($invoice['id']) ?></div>
                                                <div class="small text-muted">Order: <?= esc($invoice['order_id']) ?></div>
                                            </td>
                                            <td><?= esc($invoice['agent_username'] ?? 'N/A') ?></td>
                                            <td class="text-end fw-bold">Rp. <?= number_format($invoice['amount_total'], 0, ',', '.') ?></td>
                                            <td class="text-center d-none d-lg-table-cell"><?= esc(date('d M Y, H:i', strtotime($invoice['invoice_date']))) ?></td>
                                            <td class="text-center">
                                                <span class="badge rounded-pill text-bg-<?= ($invoice['status'] == 'paid' ? 'success' : ($invoice['status'] == 'unpaid' ? 'warning' : 'danger')) ?>">
                                                    <?= ($invoice['status'] == 'paid') ? 'Lunas' : (($invoice['status'] == 'unpaid') ? 'Belum Dibayar' : 'Dibatalkan') ?>
                                                </span>
                                                <?php if ($invoice['status'] == 'paid' && !empty($invoice['payment_date'])) : ?>
                                                    <div class="small text-muted mt-1" title="Tanggal Pembayaran">
                                                        <i class="far fa-clock me-1"></i><?= esc(date('d M Y, H:i', strtotime($invoice['payment_date']))) ?>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <?php if ($invoice['status'] == 'unpaid') : ?>
                                                    <button class="btn btn-success btn-sm mark-as-paid-btn" data-invoice-id="<?= esc($invoice['id']) ?>" title="Tandai Sudah Dibayar">
                                                        <i class="fas fa-check"></i> <span class="d-none d-md-inline">Tandai Lunas</span>
                                                    </button>
                                                    <a href="<?= base_url('distributor/invoices/detail/' . $invoice['id']) ?>" class="btn btn-info btn-sm" title="Lihat Detail">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                <?php else : ?>
                                                    <span class="text-success"><i class="fas fa-check-circle me-1"></i>Selesai</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>

                            <!-- Pagination -->
                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-4">
                                <div class="text-muted mb-2 mb-md-0">
                                    <?php
                                    $totalItems = $pager->getTotal();
                                    $firstItem = ($totalItems > 0) ? (($pager->getCurrentPage() - 1) * $pager->getPerPage()) + 1 : 0;
                                    $lastItem = ($totalItems > 0) ? min($firstItem + $pager->getPerPage() - 1, $totalItems) : 0;
                                    ?>
                                    Menampilkan <b><?= $firstItem ?></b>-<b><?= $lastItem ?></b> dari <b><?= $totalItems ?></b> hasil
                                </div>
                                <div>
                                    <?= $pager->links('default', 'bootstrap_pagination') ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Logika untuk tombol "Tandai Lunas" tetap sama
        const markAsPaidButtons = document.querySelectorAll('.mark-as-paid-btn');
        markAsPaidButtons.forEach(button => {
            button.addEventListener('click', function() {
                const invoiceId = this.dataset.invoiceId;
                Swal.fire({
                    title: 'Konfirmasi Pembayaran',
                    html: `Anda yakin ingin menandai tagihan <strong>#${invoiceId}</strong> sebagai <strong>LUNAS</strong>?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#198754',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="fas fa-check me-1"></i> Ya, Lunas!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(`<?= base_url('distributor/invoices/markAsPaid/') ?>${invoiceId}`, {
                                method: 'POST',
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Content-Type': 'application/json',
                                    '<?= csrf_header() ?>': '<?= csrf_hash() ?>'
                                },
                                body: JSON.stringify({
                                    invoice_id: invoiceId
                                })
                            })
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error('Network response was not ok');
                                }
                                return response.json();
                            })
                            .then(data => {
                                if (data.status === 'success') {
                                    Swal.fire({
                                        title: 'Berhasil!',
                                        text: data.message,
                                        icon: 'success',
                                        timer: 2000,
                                        showConfirmButton: false,
                                        timerProgressBar: true,
                                    }).then(() => {
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire({
                                        title: 'Gagal!',
                                        text: data.message || 'Gagal memperbarui status tagihan.',
                                        icon: 'error'
                                    });
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                Swal.fire({
                                    title: 'Terjadi Kesalahan!',
                                    text: 'Tidak dapat terhubung ke server. Silakan coba lagi nanti.',
                                    icon: 'error'
                                });
                            });
                    }
                });
            });
        });

        // Simpan status filter collapse di sessionStorage
        var filterCollapseElement = document.getElementById('filterCollapse');
        if (sessionStorage.getItem('filterCollapse') === 'true') {
            var bsCollapse = new bootstrap.Collapse(filterCollapseElement, {
                toggle: true
            });
        }

        filterCollapseElement.addEventListener('shown.bs.collapse', function() {
            sessionStorage.setItem('filterCollapse', 'true');
        });

        filterCollapseElement.addEventListener('hidden.bs.collapse', function() {
            sessionStorage.setItem('filterCollapse', 'false');
        });
    });
</script>
<?= $this->endSection() ?>