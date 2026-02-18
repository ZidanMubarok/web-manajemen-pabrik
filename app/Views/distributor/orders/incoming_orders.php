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
            <!-- (Bagian Notifikasi dan Filter tidak berubah) -->
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

            <div class="mb-3">
                <form action="<?= base_url('distributor/orders/incoming') ?>" method="get" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label for="start_date" class="form-label">Dari Tanggal:</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" value="<?= esc($startDate) ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="end_date" class="form-label">Sampai Tanggal:</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" value="<?= esc($endDate) ?>">
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary me-2"><i class="fas fa-filter"></i> Filter</button>
                        <a href="<?= base_url('distributor/orders/incoming') ?>" class="btn btn-secondary"><i class="fas fa-redo"></i> Reset Filter</a>
                    </div>
                </form>
            </div>


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
                            <?php
                            $currentPage = $pager->getCurrentPage();
                            $perPage = $pager->getPerPage();
                            $no = ($currentPage - 1) * $perPage + 1;
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
                                    </td>
                                    <td>
                                        <a href="<?= base_url('distributor/orders/detail/' . $order['id']) ?>" class="btn btn-info btn-sm me-1"><i class="fas fa-eye"></i> Detail</a>
                                        <button type="button" class="btn btn-success btn-sm me-1" onclick="updateOrderStatus(<?= $order['id'] ?>, 'approved')"><i class="fas fa-check"></i> Setujui</button>
                                        <!-- PERBAIKAN 1: Mengubah onclick untuk memanggil fungsi baru -->
                                        <button type="button" class="btn btn-danger btn-sm" onclick="confirmRejectOrder(<?= $order['id'] ?>)"><i class="fas fa-times"></i> Tolak</button>
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
                    $totalItems = $pager->getTotal();
                    $firstItem = ($totalItems > 0) ? (($pager->getCurrentPage() - 1) * $pager->getPerPage()) + 1 : 0;
                    $lastItem = ($totalItems > 0) ? min($firstItem + $pager->getPerPage() - 1, $totalItems) : 0;
                    ?>
                    Menampilkan <?= $firstItem ?> sampai <?= $lastItem ?> dari <?= $totalItems ?> Order Masuk
                </div>
                <div>
                    <?= $pager->links('default', 'bootstrap_pagination') ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Form tersembunyi ini sudah sempurna, kita akan tetap menggunakannya -->
<form id="orderStatusUpdateForm" action="" method="post" style="display: none;">
    <?= csrf_field() ?>
    <input type="hidden" name="status" id="newOrderStatusInput">
    <input type="hidden" name="notes" id="statusNotesInput">
</form>

<!-- PERBAIKAN 2: Seluruh blok Modal di bawah ini DIHAPUS karena tidak lagi diperlukan. -->
<!-- 
<div class="modal fade" id="rejectOrderModal" ...>
    ...
</div>
-->

<script>
    // Fungsi untuk menyetujui order (tidak berubah, sudah baik)
    function updateOrderStatus(orderId, newStatus) {
        Swal.fire({
            title: 'Konfirmasi Perubahan Status',
            html: `Anda yakin ingin mengubah status order ini menjadi <strong>${newStatus.toUpperCase()}</strong>?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#dc3545',
            confirmButtonText: 'Ya, Yakin',
            cancelButtonText: 'Batal',
            reverseButtons: true,
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('orderStatusUpdateForm');
                form.action = `<?= base_url('distributor/orders/update-status/') ?>${orderId}`;
                document.getElementById('newOrderStatusInput').value = newStatus;
                document.getElementById('statusNotesInput').value = ''; // Kosongkan notes untuk persetujuan
                form.submit();
            }
        });
    }
    // PERBAIKAN 3: FUNGSI BARU untuk konfirmasi penolakan, menggantikan semua fungsi lama.
    function confirmRejectOrder(orderId) {
        Swal.fire({
            title: 'Konfirmasi Penolakan',
            text: "Anda yakin ingin menolak order ini? Tindakan ini tidak dapat dibatalkan.",
            icon: 'warning', // Menggunakan ikon 'warning' lebih cocok untuk penolakan
            showCancelButton: true,
            confirmButtonColor: '#dc3545', // Warna merah untuk tombol konfirmasi
            cancelButtonColor: '#6c757d', // Warna abu-abu untuk tombol batal
            confirmButtonText: 'Ya, Tolak Order',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            // Jika pengguna menekan tombol "Ya, Tolak Order"
            if (result.isConfirmed) {
                // Kita gunakan form tersembunyi yang sama
                const form = document.getElementById('orderStatusUpdateForm');

                // Set action form ke URL yang benar
                form.action = `<?= base_url('distributor/orders/update-status/') ?>${orderId}`;

                // Set nilai status menjadi 'rejected'
                document.getElementById('newOrderStatusInput').value = 'rejected';

                // Kosongkan notes karena alasan tidak lagi diperlukan
                document.getElementById('statusNotesInput').value = '';

                // Kirim form
                form.submit();
            }
        });
    }

    // PERBAIKAN 4: Fungsi showRejectModal(), confirmReject(), dan event listener DIHAPUS.
</script>

<?= $this->endSection() ?>