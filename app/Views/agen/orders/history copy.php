<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <h1 class="mb-4"><?= $title ?></h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-primary text-white">
            <h6 class="m-0 font-weight-bold"><i class="fas fa-history me-2"></i> Daftar Riwayat Order Anda</h6>
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

            <form action="<?= base_url('agen/orders/history') ?>" method="get" class="mb-4">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label for="search" class="form-label">Cari Order ID:</label>
                        <input type="text" class="form-control" id="search" name="search" placeholder="Cari Order ID..." value="<?= esc($search ?? '') ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="status" class="form-label">Filter Status:</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">Semua Status</option>
                            <?php foreach ($allStatuses as $statusOption):
                                $displayOption = '';
                                switch ($statusOption) {
                                    case 'pending':
                                        $displayOption = 'Menunggu Konfirmasi';
                                        break;
                                    case 'approved':
                                        $displayOption = 'Disetujui';
                                        break;
                                    case 'processing':
                                        $displayOption = 'Diproses';
                                        break;
                                    case 'shipped':
                                        $displayOption = 'Dikirim';
                                        break;
                                    case 'completed':
                                        $displayOption = 'Selesai';
                                        break;
                                    case 'rejected':
                                        $displayOption = 'Ditolak';
                                        break;
                                    default:
                                        $displayOption = ucfirst(str_replace('_', ' ', $statusOption));
                                        break;
                                }
                            ?>
                                <option value="<?= esc($statusOption) ?>" <?= (isset($statusFilter) && $statusFilter == $statusOption) ? 'selected' : '' ?>>
                                    <?= esc($displayOption) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Filter/Cari</button>
                    </div>
                    <div class="col-md-2">
                        <a href="<?= base_url('agen/orders/history') ?>" class="btn btn-secondary w-100">Reset</a>
                    </div>
                </div>
            </form>
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Order ID</th>
                            <th>Tanggal Order</th>
                            <th>Total Jumlah (Rp)</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($orders)): ?>
                            <tr>
                                <td colspan="6">
                                    <div class="text-center p-5">
                                        <i class="far fa-sad-tear fa-4x text-muted mb-3"></i>
                                        <p class="fs-5 text-muted">Tidak ada riwayat order ditemukan.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php
                            $currentOffset = ($currentPage - 1) * $perPage;
                            $no = $currentOffset + 1;
                            foreach ($orders as $order): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td>ORD-<?= esc($order['id']) ?></td>
                                    <td><?= date('d/m/Y H:i', strtotime($order['order_date'])) ?></td>
                                    <td>Rp <?= esc(number_format($order['total_amount'], 0, ',', '.')) ?></td>
                                    <td>
                                        <?php
                                        $status = strtolower($order['status']);
                                        $badgeClass = '';
                                        $textStatus = '';
                                        switch ($status) {
                                            case 'pending':
                                                $badgeClass = 'bg-secondary';
                                                $textStatus = 'Tertunda';
                                                break;
                                            case 'approved':
                                                $badgeClass = 'bg-info';
                                                $textStatus = 'Di Setujui';
                                                break;
                                            case 'processing':
                                                $badgeClass = 'bg-warning';
                                                $textStatus = 'Di Proses';
                                                break;
                                            case 'shipped':
                                                $badgeClass = 'bg-primary';
                                                $textStatus = 'Di Kirim';
                                                break;
                                            case 'completed':
                                                $badgeClass = 'bg-success';
                                                $textStatus = 'Selesai';
                                                break;
                                            case 'rejected':
                                                $badgeClass = 'bg-danger';
                                                $textStatus = 'Di Tolak !';
                                                break;
                                            default:
                                                $badgeClass = 'bg-secondary';
                                                $textStatus = 'Tidak Diketahui !';
                                                break;
                                        }
                                        ?>
                                        <span class="badge <?= $badgeClass ?>"><?= esc(ucfirst(str_replace('_', ' ', $textStatus))) ?></span>
                                    </td>
                                    <td>
                                        <a href="<?= base_url('agen/orders/detail/' . $order['id']) ?>" class="btn btn-info btn-sm"><i class="fas fa-eye"></i> Detail</a>
                                        <?php if ($status === 'pending'): ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
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
                    Menampilkan <?= $firstItem ?> sampai <?= $lastItem ?> dari <?= $totalItems ?> Riwayat Order anda
                </div>
                <div>
                    <!-- Pager atau Pagination -->
                    <?= $pager->links('default', 'bootstrap_pagination') ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>