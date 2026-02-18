<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    /* Style kustom untuk konsistensi tema, tidak perlu diubah */
    .card {
        border: none;
        border-radius: 0.5rem;
    }

    .card-header.main-header {
        background-color: transparent;
        border-bottom: none;
        padding: 1.5rem 1.5rem 0 1.5rem;
    }

    .table thead th {
        background-color: #f8f9fc;
        border-bottom-width: 1px;
        font-weight: bold;
    }

    .btn-circle {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0;
    }

    .badge {
        padding: 0.5em 0.9em;
        font-size: 0.85em;
        font-weight: 600;
    }
</style>

<div class="container-fluid">
    <!-- Mengganti H1 dengan Card Header untuk judul -->
    <div class="card-header main-header">
        <h1 class="h3 mb-0 text-gray-800"><?= $title ?></h1>
    </div>

    <!-- Filter Section -->
    <div class="card shadow-sm mb-4">
        <div class="card-header py-3" data-bs-toggle="collapse" href="#collapseFilter" role="button" aria-expanded="true" aria-controls="collapseFilter">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-filter me-2"></i> Filter Tagihan</h6>
        </div>
        <div class="collapse show" id="collapseFilter">
            <div class="card-body">
                <!-- FORM FILTER TETAP SAMA, HANYA LAYOUT BERUBAH -->
                <form action="<?= base_url('pabrik/distributor-invoices') ?>" method="get">
                    <div class="row align-items-end g-2">
                        <div class="col-md-3">
                            <label for="invoiceNumber" class="form-label">No. Tagihan/Distributor:</label>
                            <input type="text" class="form-control" id="invoiceNumber" name="invoice_number" value="<?= esc($invoiceNumber) ?>" placeholder="Cari...">
                        </div>
                        <div class="col-md-3">
                            <label for="statusFilter" class="form-label">Status:</label>
                            <select class="form-select" id="statusFilter" name="status">
                                <option value="">Semua Status</option>
                                <option value="unpaid" <?= $statusFilter === 'unpaid' ? 'selected' : '' ?>>Belum Dibayar</option>
                                <option value="paid" <?= $statusFilter === 'paid' ? 'selected' : '' ?>>Lunas</option>
                                <option value="partially_paid" <?= $statusFilter === 'partially_paid' ? 'selected' : '' ?>>Dibayar Sebagian</option>
                                <option value="cancelled" <?= $statusFilter === 'cancelled' ? 'selected' : '' ?>>Dibatalkan</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="startDate" class="form-label">Dari Tanggal:</label>
                            <input type="text" class="form-control" id="startDate" name="start_date" value="<?= esc($startDate) ?>" placeholder="Pilih tanggal" onfocus="(this.type='date')" onblur="(this.type='text')">
                        </div>
                        <div class="col-md-2">
                            <label for="endDate" class="form-label">Sampai Tanggal:</label>
                            <input type="text" class="form-control" id="endDate" name="end_date" value="<?= esc($endDate) ?>" placeholder="Pilih tanggal" onfocus="(this.type='date')" onblur="(this.type='text')">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100 me-2"><i class="fas fa-search"></i> Cari</button>
                            <a href="<?= base_url('pabrik/distributor-invoices') ?>" class="btn btn-secondary" title="Reset Filter"><i class="fas fa-sync-alt"></i></a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Data Table Section -->
    <div class="card shadow-sm mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-list-alt me-2"></i>Data Tagihan</h6>
        </div>
        <div class="card-body">
            <!-- Flashdata Messages (logika tidak diubah) -->
            <?php if (session()->getFlashdata('success')) : ?> <div class="alert alert-success alert-dismissible fade show" role="alert"><?= session()->getFlashdata('success') ?><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div> <?php endif; ?>
            <?php if (session()->getFlashdata('error')) : ?> <div class="alert alert-danger alert-dismissible fade show" role="alert"><?= session()->getFlashdata('error') ?><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div> <?php endif; ?>

            <div class="table-responsive">
                <table class="table table-hover" width="100%" cellspacing="0">
                    <thead>
                        <!-- KOLOM TABEL TETAP SESUAI KODE ASLI -->
                        <tr>
                            <th>No</th>
                            <th>No. Tagihan</th>
                            <th>Tgl. Tagihan</th>
                            <th>Jatuh Tempo</th>
                            <th>Distributor</th>
                            <th>Total (Rp)</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($invoices)): ?>
                            <tr>
                                <td colspan="8" class="text-center">Belum ada tagihan untuk ditampilkan.</td>
                            </tr>
                        <?php else: ?>
                            <?php
                            // Memperbaiki pemanggilan Pager agar konsisten
                            $currentPage = $pager->getCurrentPage('invoices_group');
                            $perPage = $pager->getPerPage('invoices_group');
                            $no = 1 + (($currentPage - 1) * $perPage);

                            foreach ($invoices as $invoice): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= esc($invoice['invoice_number']) ?></td>
                                    <td><?= date('d M Y', strtotime($invoice['invoice_date'])) ?></td>
                                    <td><?= date('d M Y', strtotime($invoice['due_date'])) ?></td>
                                    <td><?= esc($invoice['distributor_username'] ?? 'N/A') ?></td>
                                    <td><?= number_format($invoice['total_amount'], 0, ',', '.') ?></td>
                                    <td>
                                        <?php
                                        // Logika status tetap, hanya style badge diubah
                                        $status = strtolower($invoice['status']);
                                        $badgeClass = '';
                                        $textStatus = '';
                                        switch ($status) {
                                            case 'unpaid':
                                                $badgeClass = 'bg-danger-subtle text-danger-emphasis';
                                                $textStatus = 'Belum Dibayar';
                                                break;
                                            case 'paid':
                                                $badgeClass = 'bg-success-subtle text-success-emphasis';
                                                $textStatus = 'Lunas';
                                                break;
                                            case 'partially_paid':
                                                $badgeClass = 'bg-warning-subtle text-warning-emphasis';
                                                $textStatus = 'Dibayar Sebagian';
                                                break;
                                            case 'cancelled':
                                                $badgeClass = 'bg-secondary-subtle text-secondary-emphasis';
                                                $textStatus = 'Dibatalkan';
                                                break;
                                        }
                                        ?>
                                        <span class="badge rounded-pill <?= $badgeClass ?>"><?= esc($textStatus) ?></span>
                                    </td>
                                    <td>
                                        <!-- Aksi diubah menjadi ikon, logika tetap sama -->
                                        <a href="<?= base_url('pabrik/distributor-invoices/detail/' . $invoice['id']) ?>" class="btn btn-info btn-circle btn-sm" title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <?php if ($invoice['status'] !== 'paid' && $invoice['status'] !== 'cancelled'): ?>
                                            <button type="button" class="btn btn-success btn-circle btn-sm" title="Ubah Status" onclick="showChangeStatusModal(<?= $invoice['id'] ?>, '<?= esc($invoice['invoice_number']) ?>', '<?= esc($invoice['status']) ?>')">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        <?php else: ?>
                                            <button type="button" class="btn btn-secondary btn-circle btn-sm" title="Selesai" disabled>
                                                <i class="fas fa-check"></i>
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted">
                <?php
                // Logika pager dipertahankan dan diperbaiki agar konsisten
                if (!empty($invoices)) {
                    $total = $pager->getTotal('invoices_group');
                    $firstItem = ($total > 0) ? (($pager->getCurrentPage() - 1) * $pager->getPerPage()) + 1 : 0;
                    $lastItem = ($total > 0) ? min($firstItem + $pager->getPerPage() - 1, $total) : 0;
                    echo "Menampilkan <b>{$firstItem}</b> sampai<b>{$lastItem}</b> dari <b>{$total}</b> data.";
                } else {
                    echo "Tidak ada data ditemukan.";
                }
                ?>
                </div>
                <div>
                    <?php if ($pager) : ?>
                        <!-- FIX: Menggunakan grup 'invoices_group' agar konsisten -->
                        <?= $pager->links('invoices_group', 'bootstrap_pagination') ?>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
</div>
<?= $this->endSection(); ?>


<!-- MODAL DAN SCRIPT TIDAK DIUBAH SAMA SEKALI, HANYA DIPINDAHKAN -->
<?= $this->section('modals') ?>
<div class="modal fade" id="changeStatusModal" tabindex="-1" aria-labelledby="changeStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="changeStatusModalLabel">Ubah Status Tagihan <span id="modalInvoiceNumber"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="updateInvoiceStatusForm" action="" method="post">
                    <?= csrf_field() ?>
                    <input type="hidden" name="_method" value="post">
                    <input type="hidden" name="invoice_id" id="modalInvoiceId">
                    <div class="mb-3">
                        <label for="newInvoiceStatus" class="form-label">Status Baru:</label>
                        <select class="form-select" id="newInvoiceStatus" name="status" required>
                            <option value="unpaid">Belum Dibayar</option>
                            <option value="partially_paid">Dibayar Sebagian</option>
                            <option value="paid">Lunas</option>
                            <option value="cancelled">Dibatalkan</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="submitStatusChange()">Simpan Perubahan</button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>

<?= $this->section('scripts') ?>
<script>
    function showChangeStatusModal(invoiceId, invoiceNumber, currentStatus) {
        document.getElementById('modalInvoiceId').value = invoiceId;
        document.getElementById('modalInvoiceNumber').innerText = invoiceNumber;
        document.getElementById('newInvoiceStatus').value = currentStatus;

        const changeStatusModal = new bootstrap.Modal(document.getElementById('changeStatusModal'));
        changeStatusModal.show();
    }

    function submitStatusChange() {
        const form = document.getElementById('updateInvoiceStatusForm');
        const invoiceId = document.getElementById('modalInvoiceId').value;
        form.action = `<?= base_url('pabrik/distributor-invoices/update-status/') ?>${invoiceId}`;

        fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                const changeStatusModal = bootstrap.Modal.getInstance(document.getElementById('changeStatusModal'));
                if (changeStatusModal) changeStatusModal.hide();

                if (data.status === 'success') {
                    Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: data.message,
                            showConfirmButton: false,
                            timer: 1500
                        })
                        .then(() => location.reload());
                } else {
                    let errorMessage = typeof data.message === 'object' ? Object.values(data.message).join('<br>') : data.message;
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        html: errorMessage,
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Terjadi Kesalahan!',
                    text: 'Tidak dapat menghubungi server.'
                });
            });
    }

    // Menghapus inisialisasi DataTable untuk menghindari konflik dengan script global
    // Jika Anda memerlukan fungsionalitas sorting dari DataTable, baris berikut dapat diaktifkan kembali.
    /*
    $(document).ready(function() {
        $('#dataTable').DataTable({
             "paging":   false, // Menonaktifkan paginasi dari DataTables karena kita pakai Pager CI4
             "ordering": true,
             "info":     false,
             "searching": false // Menonaktifkan pencarian DataTables karena kita punya filter sendiri
        });
    });
    */
</script>
<?= $this->endSection() ?>