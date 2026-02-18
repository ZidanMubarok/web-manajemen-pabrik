<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <h1 class="mb-4"><?= $title ?></h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-primary text-white d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold"><i class="fas fa-history me-2"></i> Daftar Riwayat Order Anda</h6>
            <button class="btn btn-light btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse" aria-expanded="true" aria-controls="filterCollapse">
                <i class="fas fa-filter me-1"></i> Filter & Cari
            </button>
        </div>

        <div class="collapse show" id="filterCollapse">
            <div class="card-body border-bottom">
                <form action="<?= base_url('agen/orders/history') ?>" method="get" class="mb-0">
                    <div class="row g-3">
                        <!-- Kolom Pencarian -->
                        <div class="col-lg-4 col-md-12">
                            <label for="search" class="form-label">Cari Order ID:</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" class="form-control" id="search" name="search" placeholder="Masukkan ID Order..." value="<?= esc($search ?? '') ?>">
                            </div>
                        </div>

                        <!-- Kolom Filter Tanggal -->
                        <div class="col-lg-5 col-md-12">
                            <label class="form-label">Filter Berdasarkan Tanggal Order:</label>
                            <div class="row g-2">
                                <div class="col-sm-6">
                                    <input type="date" class="form-control" name="start_date" title="Tanggal Mulai" value="<?= esc($startDate ?? '') ?>">
                                </div>
                                <div class="col-sm-6">
                                    <input type="date" class="form-control" name="end_date" title="Tanggal Akhir" value="<?= esc($endDate ?? '') ?>">
                                </div>
                            </div>
                        </div>

                        <!-- Kolom Filter Status -->
                        <div class="col-lg-3 col-md-12">
                            <label for="status" class="form-label">Filter berdasrakan Status:</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">Semua Status</option>
                                <?php foreach ($allStatuses as $statusOption):
                                    // ... (logika switch case status Anda tidak berubah)
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
                    </div>

                    <!-- Baris Tombol Aksi -->
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-check me-2"></i>Terapkan Filter</button>
                                <a href="<?= base_url('agen/orders/history') ?>" class="btn btn-secondary"><i class="fas fa-redo me-2"></i>Reset Filter</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card-body">
            <!-- ... Sisa dari view Anda tidak perlu diubah ... -->
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

            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Order ID</th>
                            <th>Tanggal Order</th>
                            <th>Total (Rp)</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($orders)): ?>
                            <tr>
                                <td colspan="6">
                                    <div class="text-center p-5">
                                        <i class="far fa-folder-open fa-4x text-muted mb-3"></i>
                                        <p class="fs-5 text-muted">Tidak ada riwayat order ditemukan.</p>
                                        <p class="text-muted">Coba ubah filter atau reset pencarian Anda.</p>
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
                                    <td><strong>ORD-<?= esc($order['id']) ?></strong></td>
                                    <td><?= date('d M Y, H:i', strtotime($order['order_date'])) ?></td>
                                    <td>Rp <?= esc(number_format($order['total_amount'], 0, ',', '.')) ?></td>
                                    <td>
                                        <?php
                                        // ... (logika badge status Anda tidak berubah)
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
                                                $textStatus = 'Disetujui';
                                                break;
                                            case 'processing':
                                                $badgeClass = 'bg-warning text-dark';
                                                $textStatus = 'Diproses';
                                                break;
                                            case 'shipped':
                                                $badgeClass = 'bg-primary';
                                                $textStatus = 'Dikirim';
                                                break;
                                            case 'completed':
                                                $badgeClass = 'bg-success';
                                                $textStatus = 'Selesai';
                                                break;
                                            case 'rejected':
                                                $badgeClass = 'bg-danger';
                                                $textStatus = 'Ditolak';
                                                break;
                                            default:
                                                $badgeClass = 'bg-dark';
                                                $textStatus = 'Tidak Diketahui';
                                                break;
                                        }
                                        ?>
                                        <span class="badge rounded-pill <?= $badgeClass ?>"><?= esc(ucfirst($textStatus)) ?></span>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?= base_url('agen/orders/detail/' . $order['id']) ?>" class="btn btn-info btn-sm" title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Paginasi -->
            <div class="mt-4 d-flex flex-column flex-md-row justify-content-between align-items-center">
                <div class="text-muted mb-2 mb-md-0">
                    <!-- ... (logika info paginasi tidak berubah) ... -->
                    <?php
                    $totalItems = $pager->getTotal();
                    $firstItem = ($totalItems > 0) ? (($pager->getCurrentPage() - 1) * $pager->getPerPage()) + 1 : 0;
                    $lastItem = ($totalItems > 0) ? min($firstItem + $pager->getPerPage() - 1, $totalItems) : 0;
                    ?>
                    Menampilkan <strong><?= $firstItem ?>-<?= $lastItem ?></strong> dari <strong><?= $totalItems ?></strong> Riwayat Order
                </div>
                <div class="ms-md-auto">
                    <?php if ($pager->hasMore()) : ?>
                        <?= $pager->links('default', 'bootstrap_pagination') ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>