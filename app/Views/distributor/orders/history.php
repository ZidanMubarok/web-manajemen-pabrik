<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">

    <!-- Header Halaman Modern dan Interaktif -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h1 class="h3 mb-0 text-gray-800"><?= $title ?></h1>
        <button class="btn btn-primary shadow-sm" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse" aria-expanded="false" aria-controls="filterCollapse">
            <i class="fas fa-filter me-2"></i> Pencarian & Filter
        </button>
    </div>

    <!-- Kontainer Filter yang Dapat Disembunyikan (Collapsible) -->
    <div class="collapse" id="filterCollapse">
        <div class="card shadow-sm mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-search me-2"></i> Opsi Filter & Pencarian</h6>
            </div>
            <div class="card-body">
                <form action="<?= base_url('distributor/orders/history') ?>" method="get" class="row g-3 align-items-end">
                    <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                        <label for="order_id" class="form-label">Order ID:</label>
                        <input type="text" class="form-control" id="order_id" name="order_id" placeholder="Cari Order ID..." value="<?= esc($searchOrderId ?? '') ?>">
                    </div>
                    <div class="col-12 col-sm-6 col-md-4 col-lg-2">
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
                    <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                        <label for="status" class="form-label">Status Order:</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">Semua Status</option>
                            <option value="rejected" <?= (isset($searchStatus) && $searchStatus == 'rejected') ? 'selected' : '' ?>>
                                Di Tolak
                            </option>
                            <option value="completed" <?= (isset($searchStatus) && $searchStatus == 'completed') ? 'selected' : '' ?>>
                                Selesai
                            </option>
                        </select>
                    </div>
                    <div class="col-12 col-sm-6 col-md-6 col-lg-2">
                        <label for="start_date" class="form-label">Dari Tanggal:</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" value="<?= esc($startDate ?? '') ?>">
                    </div>
                    <div class="col-12 col-sm-6 col-md-6 col-lg-2">
                        <label for="end_date" class="form-label">Sampai Tanggal:</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" value="<?= esc($endDate ?? '') ?>">
                    </div>
                    <div class="col-12 col-lg-auto mt-3 mt-lg-0">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-info w-100"><i class="fas fa-search me-1"></i> Cari</button>
                            <a href="<?= base_url('distributor/orders/history') ?>" class="btn btn-secondary w-100"><i class="fas fa-redo me-1"></i> Reset</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Daftar Riwayat Order -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-white d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-list-alt me-2"></i> Daftar Riwayat Order</h6>
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

            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Order ID</th>
                            <th>Agen</th>
                            <th>Tanggal Order</th>
                            <th>Total (Rp)</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($orders)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="fas fa-box-open fa-2x text-muted mb-2"></i>
                                    <p class="mb-0">Tidak ada riwayat order ditemukan.</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php $no = (($pager->getCurrentPage() - 1) * $pager->getPerPage()) + 1;
                            foreach ($orders as $order): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><strong>ORD-<?= esc($order['id']) ?></strong></td>
                                    <td><?= esc($order['agent_username'] ?? 'N/A') ?></td>
                                    <td><?= date('d M Y, H:i', strtotime($order['order_date'])) ?></td>
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
                                        <span class="badge rounded-pill <?= $badgeClass ?>"><?= esc($text) ?></span>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?= base_url('distributor/orders/history_detail/' . $order['id']) ?>" class="btn btn-info btn-sm" data-bs-toggle="tooltip" title="Lihat Detail Order">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Paginasi Responsif -->
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-4 gap-2">
                <div class="text-muted">
                    <?php
                    $totalItems = $pager->getTotal();
                    $firstItem = ($totalItems > 0) ? (($pager->getCurrentPage() - 1) * $pager->getPerPage()) + 1 : 0;
                    $lastItem = ($totalItems > 0) ? min($firstItem + $pager->getPerPage() - 1, $totalItems) : 0;
                    ?>
                    Menampilkan <strong><?= $firstItem ?>-<?= $lastItem ?></strong> dari <strong><?= $totalItems ?></strong> data
                </div>
                <div class="mt-2 mt-md-0">
                    <?= $pager->links('default', 'bootstrap_pagination') ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tambahkan script ini di akhir section 'content' jika belum ada -->
<script>
    // Inisialisasi Tooltip Bootstrap
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
</script>
<?= $this->endSection() ?>