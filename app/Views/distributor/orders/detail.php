<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <h1 class="mb-4"><?= $title ?></h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-primary text-white">
            <h6 class="m-0 font-weight-bold"><i class="fas fa-info-circle me-2"></i> Informasi Order #ORD-<?= esc($order['id']) ?></h6>
        </div>
        <div class="card-body">
            <!-- (Bagian Notifikasi dan Detail Order tidak berubah) -->
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
                    <?php if (!empty($order['notes'])): ?>
                        <p><strong>Catatan Distributor:</strong> <?= esc($order['notes']) ?></p>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <h5>Informasi Agen Pemesan</h5>
                    <p><strong>Nama Agen:</strong> <?= esc($agen['username'] ?? 'N/A') ?></p>
                    <p><strong>Email Agen:</strong> <?= esc($agen['email'] ?? 'N/A') ?></p>
                    <p><strong>No Telpon Agen:</strong> <?= esc($agen['no_telpon'] ?? 'N/A') ?></p>
                    <p><strong>Alamat Agen:</strong> <?= esc($agen['alamat'] ?? 'N/A') ?></p>
                </div>
            </div>

            <h5>Item Produk dalam Order</h5>
            <div class="table-responsive">
                <!-- (Tabel item tidak berubah) -->
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
                                    <td><?= esc($productNames[$item['product_id']] ?? 'N/A') ?></td>
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
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <a href="<?= base_url('distributor/orders/incoming') ?>" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i> Kembali ke Order Masuk</a>
                </div>
                <?php if ($order['status'] === 'pending'): ?>
                    <div>
                        <button type="button" class="btn btn-success me-2" onclick="confirmApproveWithSweetAlert(<?= $order['id'] ?>, 'approved')"><i class="fas fa-check"></i> Setujui Order</button>
                        <!-- PERBAIKAN 1: Mengubah onclick untuk memanggil fungsi konfirmasi penolakan yang baru -->
                        <button type="button" class="btn btn-danger" onclick="confirmRejectOrderDetail(<?= $order['id'] ?>)"><i class="fas fa-times"></i> Tolak Order</button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Form tersembunyi ini sudah benar, kita pertahankan -->
<form id="orderStatusUpdateFormDetail" action="" method="post" style="display: none;">
    <?= csrf_field() ?>
    <input type="hidden" name="status" id="newOrderStatusInputDetail">
    <input type="hidden" name="notes" id="statusNotesInputDetail">
</form>

<!-- PERBAIKAN 2: Seluruh blok Modal di bawah ini DIHAPUS -->
<!-- 
<div class="modal fade" id="rejectOrderModalDetail" ...>
    ...
</div> 
-->

<script>
    // Fungsi untuk menyetujui, sudah benar dan tidak perlu diubah.
    function confirmApproveWithSweetAlert(orderId, newStatus) {
        Swal.fire({
            title: 'Konfirmasi Persetujuan Order',
            html: `Anda yakin ingin **menyetujui** order ini dan mengubah statusnya menjadi <strong>${newStatus.toUpperCase()}</strong>? Setelah disetujui, Anda akan mendapatkan tagihan dari pabrik.`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#dc3545',
            confirmButtonText: 'Ya, Setujui',
            cancelButtonText: 'Batal',
            reverseButtons: true,
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('orderStatusUpdateFormDetail');
                form.action = `<?= base_url('distributor/orders/update-status/') ?>${orderId}`;
                document.getElementById('newOrderStatusInputDetail').value = newStatus;
                document.getElementById('statusNotesInputDetail').value = '';
                form.submit();
            }
        });
    }

    // PERBAIKAN 3: FUNGSI BARU yang disederhanakan untuk menolak order.
    function confirmRejectOrderDetail(orderId) {
        Swal.fire({
            title: 'Konfirmasi Penolakan',
            text: "Anda yakin ingin menolak order ini? Tindakan ini tidak dapat dibatalkan.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Tolak Order',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Menggunakan form tersembunyi yang sudah ada di halaman detail
                const form = document.getElementById('orderStatusUpdateFormDetail');

                // Set action, status, dan notes (kosong)
                form.action = `<?= base_url('distributor/orders/update-status/') ?>${orderId}`;
                document.getElementById('newOrderStatusInputDetail').value = 'rejected';
                document.getElementById('statusNotesInputDetail').value = ''; // Alasan tidak diperlukan lagi

                // Kirim form
                form.submit();
            }
        });
    }

    // PERBAIKAN 4: Semua fungsi JavaScript lama terkait modal penolakan DIHAPUS (showRejectModal, confirmRejectDetail, event listener).
</script>
<?= $this->endSection() ?>