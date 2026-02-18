<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('title') ?>

<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-12">
            <h3 class="fw-bold"><?= esc($pageTitle ?? $title) ?></h3>

            <div class="card shadow-sm mt-4 mb-4">
                <div class="card-header bg-secondary text-white">
                    <h6 class="m-0"><i class="fas fa-filter me-2"></i>Filter dan Pencarian Tagihan</h6>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('distributor/invoices') ?>" method="get" class="row g-3 align-items-end">
                        <div class="col-md-3 col-lg-2">
                            <label for="invoice_id" class="form-label">ID Tagihan:</label>
                            <input type="text" class="form-control" id="invoice_id" name="invoice_id" placeholder="Cari ID Tagihan..." value="<?= esc($searchInvoiceId ?? '') ?>">
                        </div>
                        <div class="col-md-3 col-lg-2">
                            <label for="order_id" class="form-label">ID Order:</label>
                            <input type="text" class="form-control" id="order_id" name="order_id" placeholder="Cari ID Order..." value="<?= esc($searchOrderId ?? '') ?>">
                        </div>
                        <div class="col-md-3 col-lg-2">
                            <label for="agent_name" class="form-label">Nama Agen:</label>
                            <select class="form-select" id="agent_name" name="agent_name">
                                <option value="">Semua Agen</option>
                                <?php foreach ($agents as $agent): ?>
                                    <option value="<?= esc($agent['username']) ?>" <?= (isset($searchAgentName) && $searchAgentName == $agent['username']) ? 'selected' : '' ?>>
                                        <?= esc($agent['username']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3 col-lg-2">
                            <label for="status" class="form-label">Status Tagihan:</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">Semua Status</option>
                                <option value="unpaid" <?= $statusFilter === 'unpaid' ? 'selected' : '' ?>>Belum Dibayar</option>
                                <option value="paid" <?= $statusFilter === 'paid' ? 'selected' : '' ?>>Lunas</option>
                                <option value="cancelled" <?= $statusFilter === 'cancelled' ? 'selected' : '' ?>>Dibatalkan</option>
                                <!-- <?php # foreach ($allInvoiceStatuses as $statusOption): 
                                        ?>
                                    <option value="<?php # esc($statusOption) 
                                                    ?>" <?php # (isset($statusFilter) && $statusFilter == $statusOption) ? 'selected' : '' 
                                                        ?>>
                                        <?php # esc(ucfirst($statusOption)) 
                                        ?>
                                    </option>
                                <?php #endforeach; 
                                ?> -->
                            </select>
                        </div>
                        <div class="col-md-3 col-lg-2">
                            <label for="start_date" class="form-label">Dari Tanggal:</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" value="<?= esc($startDate ?? '') ?>">
                        </div>
                        <div class="col-md-3 col-lg-2">
                            <label for="end_date" class="form-label">Sampai Tanggal:</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" value="<?= esc($endDate ?? '') ?>">
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-search me-1"></i> Cari/Filter</button>
                        </div>
                        <div class="col-auto">
                            <a href="<?= base_url('distributor/invoices') ?>" class="btn btn-secondary"><i class="fas fa-redo me-1"></i> Reset</a>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-primary text-white">
                    <h6 class="m-0"><i class="fas fa-file-invoice-dollar me-2"></i>Daftar Tagihan</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <?php if (empty($invoices)): ?>
                            <div class="text-center py-5">
                                <i class="fas fa-folder-open fa-3x text-secondary mb-3"></i>
                                <p class="text-muted">Tidak ada tagihan yang tersedia.</p>
                            </div>
                        <?php else: ?>
                            <table class="table table-bordered table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col">No.</th>
                                        <th scope="col">Id Order</th>
                                        <th scope="col">Agen</th>
                                        <th scope="col" class="text-center">Jumlah Total</th>
                                        <th scope="col" class="text-center d-none d-lg-table-cell">Tanggal Tagihan</th>
                                        <!-- <th scope="col" class="text-center d-none d-lg-table-cell">Tanggal Pembayaran</th> -->
                                        <th scope="col" class="text-center">Status</th>
                                        <th scope="col" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    foreach ($invoices as $invoice): ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td>
                                                <div class="small text-muted">Tagihan: <?= esc($invoice['id']) ?></div>
                                                <div class="small text-muted">Order: <?= esc($invoice['order_id']) ?></div>
                                            </td>
                                            <td><?= esc($invoice['agent_username'] ?? 'N/A') ?></td>
                                            <td class="text-end fw-bold">Rp. <?= number_format($invoice['amount_total'], 0, ',', '.') ?></td>
                                            <td class="text-center d-none d-lg-table-cell"><?= esc(date('d/m/Y H:i', strtotime($invoice['invoice_date']))) ?></td>
                                            <td class="text-center">
                                                <span class="badge rounded-pill text-bg-<?= ($invoice['status'] == 'paid' ? 'success' : ($invoice['status'] == 'unpaid' ? 'warning' : 'danger')) ?>">
                                                    <?php
                                                    if ($invoice['status'] == 'paid') {
                                                        echo 'Lunas';
                                                    } elseif ($invoice['status'] == 'unpaid') {
                                                        echo 'Belum Dibayar';
                                                    } else {
                                                        echo 'Dibatalkan'; // Atau status lain yang sesuai
                                                    }
                                                    ?>
                                                </span>
                                                <?php if ($invoice['status'] == 'paid' && !empty($invoice['payment_date'])): ?>
                                                    <div class="small text-muted mt-1">
                                                        <i class="far fa-clock me-1"></i><?= esc(date('d M Y, H:i', strtotime($invoice['payment_date']))) ?>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <?php if ($invoice['status'] == 'unpaid'): ?>
                                                    <button class="btn btn-info btn-sm mark-as-paid-btn" data-invoice-id="<?= esc($invoice['id']) ?>" title="Tandai Sudah Dibayar">
                                                        <i class="fas fa-check me-1"></i> Tandai Lunas
                                                    </button>
                                                <?php else: ?>
                                                    <span class="text-success fw-bold"><i class="fas fa-check-circle me-1"></i>Selesai</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            <!-- Pager atau Pagination -->
                            <div class="d-flex justify-content-between align-items-center mt-4">
                                <div>
                                    <?php
                                    $totalItems = $pager->getTotal();
                                    $firstItem = ($totalItems > 0) ? (($pager->getCurrentPage() - 1) * $pager->getPerPage()) + 1 : 0;
                                    $lastItem = ($totalItems > 0) ? min($firstItem + $pager->getPerPage() - 1, $totalItems) : 0;
                                    ?>
                                    Menampilkan <?= $firstItem ?> sampai <?= $lastItem ?> dari <?= $totalItems ?> Tagihan Agen
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
        const markAsPaidButtons = document.querySelectorAll('.mark-as-paid-btn');
        markAsPaidButtons.forEach(button => {
            button.addEventListener('click', function() {
                const invoiceId = this.dataset.invoiceId;
                Swal.fire({
                    title: 'Konfirmasi Pembayaran',
                    html: `Anda yakin ingin menandai tagihan <strong>#${invoiceId}</strong> sebagai <strong>LUNAS</strong>?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Lunas!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(`<?= base_url('distributor/invoices/markAsPaid/') ?>${invoiceId}`, { // Pastikan URL benar
                                method: 'POST',
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Content-Type': 'application/json',
                                    '<?= csrf_header() ?>': '<?= csrf_hash() ?>' // Tambahkan CSRF token
                                },
                                body: JSON.stringify({
                                    invoice_id: invoiceId,
                                    // Anda bisa menambahkan data lain jika diperlukan
                                })
                            })
                            .then(response => response.json())
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
                                    title: 'Error!',
                                    text: 'Terjadi kesalahan saat menghubungi server.',
                                    icon: 'error'
                                });
                            });
                    }
                });
            });
        });
    });
</script>
<?= $this->endSection() ?>