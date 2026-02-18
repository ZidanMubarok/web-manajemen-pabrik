<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <h1 class="mb-4"><?= $title ?></h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-primary text-white">
            <h6 class="m-0 font-weight-bold"><i class="fas fa-file-invoice me-2"></i> Daftar Tagihan untuk Distributor</h6>
        </div>
        <div class="card-body">
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

            <form action="<?= base_url('pabrik/distributor-invoices') ?>" method="get" class="mb-4">
                <div class="row g-3">
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
                    <div class="col-md-3">
                        <label for="invoiceNumber" class="form-label">No. Tagihan:</label>
                        <input type="text" class="form-control" id="invoiceNumber" name="invoice_number" value="<?= esc($invoiceNumber) ?>" placeholder="Cari No. Tagihan">
                    </div>
                    <div class="col-md-2">
                        <label for="startDate" class="form-label">Dari Tanggal:</label>
                        <input type="date" class="form-control" id="startDate" name="start_date" value="<?= esc($startDate) ?>">
                    </div>
                    <div class="col-md-2">
                        <label for="endDate" class="form-label">Sampai Tanggal:</label>
                        <input type="date" class="form-control" id="endDate" name="end_date" value="<?= esc($endDate) ?>">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100 me-2"><i class="fas fa-filter"></i> Filter</button>
                        <a href="<?= base_url('pabrik/distributor-invoices') ?>" class="btn btn-secondary w-100"><i class="fas fa-sync-alt"></i> Reset</a>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>No. Tagihan</th>
                            <th>Tgl. Tagihan</th>
                            <th>Jatuh Tempo</th>
                            <th>Distributor</th>
                            <th>Agen Asal</th>
                            <th>Total (Rp)</th>
                            <th>Status</th>
                            <th>Tgl. Bayar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($invoices)): ?>
                            <tr>
                                <td colspan="10" class="text-center">Belum ada tagihan distributor.</td>
                            </tr>
                        <?php else: ?>
                            <?php $no = 1;
                            foreach ($invoices as $invoice): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= esc($invoice['invoice_number']) ?></td>
                                    <td><?= date('d/m/Y', strtotime($invoice['invoice_date'])) ?></td>
                                    <td><?= date('d/m/Y', strtotime($invoice['due_date'])) ?></td>
                                    <td><?= esc($invoice['distributor_username'] ?? 'N/A') ?></td>
                                    <td><?= esc($invoice['agen_username'] ?? 'N/A') ?></td>
                                    <td>Rp <?= esc(number_format($invoice['total_amount'], 0, ',', '.')) ?></td>
                                    <td>
                                        <?php
                                        $status = strtolower($invoice['status']);
                                        $badgeClass = '';
                                        $textStatus = '';
                                        switch ($status) {
                                            case 'unpaid':
                                                $badgeClass = 'bg-danger';
                                                $textStatus = 'Belum Di Bayar';
                                                break;
                                            case 'paid':
                                                $badgeClass = 'bg-success';
                                                $textStatus = 'Sudah Di Bayar !';
                                                break;
                                            case 'partially_paid':
                                                $badgeClass = 'bg-warning text-dark';
                                                $textStatus = 'Di Bayar Sebagian';
                                                break;
                                            case 'cancelled':
                                                $badgeClass = 'bg-secondary';
                                                $textStatus = 'Di Batalkan !';
                                                break;
                                            default:
                                                $badgeClass = 'bg-info';
                                                $textStatus = 'Tidak Diketahui !';
                                                break;
                                        }
                                        ?>
                                        <span class="badge <?= $badgeClass ?>"><?= esc(ucfirst(str_replace('_', ' ', $textStatus))) ?></span>
                                    </td>
                                    <td><?= !empty($invoice['payment_date']) ? date('d/m/Y', strtotime($invoice['payment_date'])) : '-' ?></td>
                                    <td>
                                        <a href="<?= base_url('pabrik/distributor-invoices/detail/' . $invoice['id']) ?>" class="btn btn-info btn-sm me-1">
                                            <i class="fas fa-eye"></i> Detail
                                        </a>
                                        <?php if ($invoice['status'] !== 'paid' && $invoice['status'] !== 'cancelled'): ?>
                                            <button type="button" class="btn btn-primary btn-sm" onclick="showChangeStatusModal(<?= $invoice['id'] ?>, '<?= esc($invoice['invoice_number']) ?>', '<?= esc($invoice['status']) ?>')">
                                                <i class="fas fa-edit"></i> Ubah Status
                                            </button>
                                        <?php else: ?>
                                            <button type="button" class="btn btn-secondary btn-sm" disabled><i class="fas fa-check"></i> Selesai</button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-4">

                <div>
                    <?php
                    // Ambil informasi dari pager
                    $total = $pager->getTotal('invoices_group');
                    $perPage = $pager->getPerPage('invoices_group');
                    $currentPage = $pager->getCurrentPage('invoices_group');

                    // Hitung nomor data awal dan akhir yang ditampilkan
                    $start = (($currentPage - 1) * $perPage) + 1;
                    $end = min(($currentPage * $perPage), $total);

                    // Tampilkan hanya jika ada data
                    if ($total > 0) {
                        echo "Menampilkan <b>$start</b> sampai <b>$end</b> dari total <b>$total</b> data tagihan.";
                    } else {
                        echo "Tidak ada data tagihan yang ditemukan.";
                    }
                    ?>
                </div>

                <div>
                    <?php if ($pager) : ?>
                        <?= $pager->links('default', 'bootstrap_pagination') // Ganti dengan template Anda 
                        ?>
                    <?php endif; ?>
                </div>

            </div>

        </div>
    </div>
</div>
<?= $this->endSection(); ?>
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
        const invoiceId = document.getElementById('modalInvoiceId').value;
        const newStatus = document.getElementById('newInvoiceStatus').value;
        const form = document.getElementById('updateInvoiceStatusForm');

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
                if (changeStatusModal) {
                    changeStatusModal.hide();
                }

                if (data.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.message,
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    let errorMessage = 'Gagal memperbarui status. Silakan coba lagi.';
                    if (typeof data.message === 'object') {
                        errorMessage = Object.values(data.message).join('<br>');
                    } else if (typeof data.message === 'string') {
                        errorMessage = data.message;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        html: errorMessage,
                        confirmButtonColor: '#dc3545'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Terjadi Kesalahan!',
                    text: 'Tidak dapat menghubungi server. Silakan coba lagi.',
                    confirmButtonColor: '#dc3545'
                });
            });
    }

    $(document).ready(function() {
        $('#dataTable').DataTable({
            "order": [
                [2, "desc"]
            ]
        });
    });
</script>
<?= $this->endSection() ?>