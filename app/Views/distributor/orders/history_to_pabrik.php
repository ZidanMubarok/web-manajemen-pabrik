<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('title') ?>
<?= esc($title) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <h1 class="mb-4"><?= esc($title) ?></h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-primary text-white d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold"><i class="fas fa-history me-2"></i> Daftar Riwayat Order ke Pabrik (<?= esc($pabrikName ?? 'N/A') ?>)</h6>
            <!-- Tombol untuk menampilkan/menyembunyikan filter -->
            <button class="btn btn-light btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse" aria-expanded="false" aria-controls="filterCollapse">
                <i class="fas fa-filter me-1"></i> Filter Data
            </button>
        </div>
        <div class="card-body">
            <!-- Area Filter yang bisa disembunyikan -->
            <div class="collapse mb-4" id="filterCollapse">
                <div class="card card-body" style="background-color: #f8f9fc;">
                    <form action="<?= base_url('distributor/orders/history-to-pabrik') ?>" method="get">
                        <div class="row align-items-end">
                            <div class="col-md-3 mb-3">
                                <label for="order_id" class="form-label">Order ID</label>
                                <input type="text" class="form-control" id="order_id" name="order_id" placeholder="Contoh: ORD-123" value="<?= esc($filters['order_id'] ?? '') ?>">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="start_date" class="form-label">Tanggal Mulai</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" value="<?= esc($filters['start_date'] ?? '') ?>">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="end_date" class="form-label">Tanggal Selesai</label>
                                <input type="date" class="form-control" id="end_date" name="end_date" value="<?= esc($filters['end_date'] ?? '') ?>">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="">Semua Status Aktif</option>
                                    <option value="approved" <?= ($filters['status'] ?? '') == 'approved' ? 'selected' : '' ?>>Disetujui</option>
                                    <option value="processing" <?= ($filters['status'] ?? '') == 'processing' ? 'selected' : '' ?>>Diproses</option>
                                    <option value="shipped" <?= ($filters['status'] ?? '') == 'shipped' ? 'selected' : '' ?>>Dikirim</option>
                                </select>
                            </div>
                            <div class="col-md-12 text-end">
                                <a href="<?= base_url('distributor/orders/history-to-pabrik') ?>" class="btn btn-secondary"><i class="fas fa-sync-alt me-1"></i> Reset</a>
                                <button type="submit" class="btn btn-primary"><i class="fas fa-search me-1"></i> Terapkan Filter</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Flash Messages -->
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
                    <thead>
                        <tr class="text-center">
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
                                <td colspan="6" class="text-center">Data order tidak ditemukan. Coba ubah kriteria filter Anda.</td>
                            </tr>
                        <?php else: ?>
                            <?php
                            // Mengambil nomor halaman saat ini untuk penomoran
                            $currentPage = $pager->getCurrentPage();
                            $perPage = $pager->getPerPage();
                            $no = (($currentPage - 1) * $perPage) + 1;
                            foreach ($orders as $order): ?>
                                <tr>
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td>ORD-<?= esc($order['id']) ?></td>
                                    <td><?= date('d/m/Y H:i', strtotime($order['order_date'])) ?></td>
                                    <td class="text-end">Rp <?= esc(number_format($order['total_amount'], 0, ',', '.')) ?></td>
                                    <td class="text-center">
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
                                                $text = 'Disetujui';
                                                break;
                                            case 'processing':
                                                $badgeClass = 'bg-warning text-dark';
                                                $text = 'Diproses';
                                                break;
                                            case 'shipped':
                                                $badgeClass = 'bg-primary';
                                                $text = 'Dikirim';
                                                break;
                                            case 'completed':
                                                $badgeClass = 'bg-success';
                                                $text = 'Selesai';
                                                break;
                                            case 'rejected':
                                                $badgeClass = 'bg-danger';
                                                $text = 'Ditolak';
                                                break;
                                            default:
                                                $badgeClass = 'bg-light text-dark';
                                                $text = 'Tidak Diketahui';
                                                break;
                                        }
                                        ?>
                                        <span class="badge <?= $badgeClass ?> p-2"><?= esc($text) ?></span>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?= base_url('distributor/orders/detail-to-pabrik/' . $order['id']) ?>" class="btn btn-info btn-sm"><i class="fas fa-eye"></i> Detail</a>
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
                    $firstItem = ($totalItems > 0) ? (($pager->getCurrentPage() - 1) * $perPage) + 1 : 0;
                    $lastItem = ($totalItems > 0) ? min($firstItem + $perPage - 1, $totalItems) : 0;
                    ?>
                    Menampilkan <?= $firstItem ?> - <?= $lastItem ?> dari <?= $totalItems ?> Order Aktif.
                </div>
                <div>
                    <?= $pager->links('default', 'bootstrap_pagination') ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>