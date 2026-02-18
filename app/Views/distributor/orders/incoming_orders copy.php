<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <h1 class="mb-4"><?= $title ?></h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-primary text-white">
            <h6 class="m-0 font-weight-bold"><i class="fas fa-receipt me-2"></i> Daftar Order Baru dari Agen</h6>
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

            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Order ID</th>
                            <th>Agen</th>
                            <th>Alamat</th>
                            <th>No Telpon</th>
                            <th>Tanggal Order</th>
                            <th>Total Jumlah (Rp)</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($incomingOrders)): ?>
                            <tr>
                                <td colspan="9" class="text-center">Belum ada order masuk dari Agen.</td>
                            </tr>
                        <?php else: ?>
                            <?php $no = 1;
                            foreach ($incomingOrders as $order): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td>ORD-<?= esc($order['id']) ?></td>
                                    <td><?= esc($order['agen_username'] ?? 'N/A') ?></td>
                                    <td><?= esc($order['agen_alamat'] ?? 'N/A') ?></td>
                                    <td><?= esc($order['agen_no_telpon'] ?? 'N/A') ?></td>
                                    <td><?= date('d/m/Y H:i', strtotime($order['order_date'])) ?></td>
                                    <td>Rp <?= esc(number_format($order['total_amount'], 0, ',', '.')) ?></td>
                                    <td>
                                        <?php
                                        $status = strtolower($order['status']);
                                        $badgeClass = '';
                                        switch ($status) {
                                            case 'pending':
                                                $badgeClass = 'bg-secondary';
                                                break;
                                            case 'approved':
                                                $badgeClass = 'bg-info';
                                                break;
                                            case 'processing':
                                                $badgeClass = 'bg-warning';
                                                break;
                                            case 'shipped':
                                                $badgeClass = 'bg-primary';
                                                break;
                                            case 'completed':
                                                $badgeClass = 'bg-success';
                                                break;
                                            case 'rejected':
                                                $badgeClass = 'bg-danger';
                                                break;
                                            default:
                                                $badgeClass = 'bg-secondary';
                                                break;
                                        }
                                        ?>
                                        <span class="badge <?= $badgeClass ?>"><?= esc(ucfirst(str_replace('_', ' ', $status))) ?></span>
                                    </td>
                                    <td>
                                        <a href="<?= base_url('distributor/orders/detail/' . $order['id']) ?>" class="btn btn-info btn-sm me-1"><i class="fas fa-eye"></i> Detail</a>
                                        <button type="button" class="btn btn-success btn-sm me-1" onclick="updateOrderStatus(<?= $order['id'] ?>, 'approved')"><i class="fas fa-check"></i> Setujui</button>
                                        <button type="button" class="btn btn-danger btn-sm" onclick="showRejectModal(<?= $order['id'] ?>)"><i class="fas fa-times"></i> Tolak</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<form id="orderStatusUpdateForm" action="" method="post" style="display: none;">
    <?= csrf_field() ?>
    <input type="hidden" name="status" id="newOrderStatusInput">
    <input type="hidden" name="notes" id="statusNotesInput">
</form>

<div class="modal fade" id="rejectOrderModal" tabindex="-1" role="dialog" aria-labelledby="rejectOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="rejectOrderModalLabel">Tolak Order</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Anda yakin ingin menolak order ini? Mohon berikan alasan penolakan.</p>
                <form id="rejectForm" action="" method="post">
                    <?= csrf_field() ?>
                    <input type="hidden" name="status" value="rejected">
                    <div class="mb-3">
                        <label for="rejectNotes" class="form-label">Alasan Penolakan:</label>
                        <textarea class="form-control" id="rejectNotes" name="notes" rows="3" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" onclick="confirmReject()">Tolak Order</button>
            </div>
        </div>
    </div>
</div>
<script>
    let currentOrderIdToReject = null;

    // Fungsi untuk menampilkan konfirmasi persetujuan dengan SweetAlert2
    function updateOrderStatus(orderId, newStatus) {
        Swal.fire({
            title: 'Konfirmasi Perubahan Status',
            html: `Anda yakin ingin mengubah status order ini menjadi <strong>${newStatus.toUpperCase()}</strong>?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745', // Warna hijau modern
            cancelButtonColor: '#dc3545', // Warna merah untuk batal
            confirmButtonText: 'Ya, Yakin',
            cancelButtonText: 'Batal',
            reverseButtons: true, // Tombol "Batal" di kiri, "Ya, Yakin" di kanan
            customClass: {
                popup: 'swal2-modern', // Kelas kustom untuk styling tambahan jika diperlukan
                confirmButton: 'swal2-btn-confirm',
                cancelButton: 'swal2-btn-cancel'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('orderStatusUpdateForm');
                form.action = `<?= base_url('distributor/orders/update-status/') ?>${orderId}`;
                document.getElementById('newOrderStatusInput').value = newStatus;
                document.getElementById('statusNotesInput').value = ''; // Clear notes for approve
                form.submit();
            }
        });
    }

    function showRejectModal(orderId) {
        currentOrderIdToReject = orderId;
        const rejectModal = new bootstrap.Modal(document.getElementById('rejectOrderModal'));
        rejectModal.show();
    }

    function confirmReject() {
        if (currentOrderIdToReject) {
            // Dapatkan modal
            const rejectModalInstance = bootstrap.Modal.getInstance(document.getElementById('rejectOrderModal'));
            if (rejectModalInstance) {
                rejectModalInstance.hide(); // Sembunyikan modal setelah tombol 'Tolak Order' diklik
            }

            const rejectNotesTextarea = document.getElementById('rejectNotes');
            const notes = rejectNotesTextarea.value;

            if (notes.trim() === '') {
                Swal.fire({ // Gunakan SweetAlert untuk notifikasi validasi
                    icon: 'warning',
                    title: 'Perhatian!',
                    text: 'Alasan penolakan tidak boleh kosong.',
                    confirmButtonColor: '#0d6efd'
                });
                return;
            }

            // Gunakan form utama yang tersembunyi untuk submit, agar lebih konsisten
            const form = document.getElementById('orderStatusUpdateForm');
            form.action = `<?= base_url('distributor/orders/update-status/') ?>${currentOrderIdToReject}`;
            document.getElementById('newOrderStatusInput').value = 'rejected';
            document.getElementById('statusNotesInput').value = notes; // Set notes di form utama
            form.submit();
        }
    }

    // Reset textarea ketika modal ditutup (opsional, untuk UX)
    document.getElementById('rejectOrderModal').addEventListener('hidden.bs.modal', function() {
        document.getElementById('rejectNotes').value = ''; // Kosongkan textarea saat modal ditutup
        currentOrderIdToReject = null; // Reset order ID
    });
    // function confirmReject() {
    //     if (currentOrderIdToReject) {
    //         const form = document.getElementById('rejectForm');
    //         const notes = document.getElementById('rejectNotes').value;

    //         if (notes.trim() === '') {
    //             alert('Alasan penolakan tidak boleh kosong.');
    //             return;
    //         }

    //         // Set action dan notes untuk form penolakan
    //         form.action = `<?= base_url('distributor/orders/update-status/') ?>${currentOrderIdToReject}`;
    //         // Set hidden input 'notes' di form reject, karena form action-nya berubah
    //         let notesInput = form.querySelector('input[name="notes"]');
    //         if (!notesInput) {
    //             notesInput = document.createElement('input');
    //             notesInput.type = 'hidden';
    //             notesInput.name = 'notes';
    //             form.appendChild(notesInput);
    //         }
    //         notesInput.value = notes;

    //         form.submit();
    //     }
    // }
</script>

<?= $this->endSection() ?>