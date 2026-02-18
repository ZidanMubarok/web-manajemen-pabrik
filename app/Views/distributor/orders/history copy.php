<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <h1 class="mb-4"><?= $title ?></h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-secondary text-white">
            <h6 class="m-0 font-weight-bold"><i class="fas fa-filter me-2"></i> Filter dan Pencarian Riwayat Order</h6>
        </div>
        <div class="card-body">
            <form action="<?= base_url('distributor/orders/history') ?>" method="get" class="row g-3 align-items-end">
                <div class="col-sm-6 col-md-3 col-lg-2">
                    <label for="order_id" class="form-label">Order ID:</label>
                    <input type="text" class="form-control" id="order_id" name="order_id" placeholder="Cari Order ID..." value="<?= esc($searchOrderId ?? '') ?>">
                </div>
                <div class="col-xsm-6 col-md-3 col-lg-2">
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
                    <label for="status" class="form-label">Status Order:</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">Semua Status</option>
                        <option value="rejected" <?= (isset($searchAgentName) && $searchAgentName == 'rejected') ? 'selected' : '' ?>>
                            Di Tolak !
                        </option>
                        <option value="completed" <?= (isset($searchAgentName) && $searchAgentName == 'completed') ? 'selected' : '' ?>>
                            Selesai !
                        </option>
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
                    <a href="<?= base_url('distributor/orders/history') ?>" class="btn btn-secondary"><i class="fas fa-redo me-1"></i> Reset</a>
                </div>
            </form>
        </div>
    </div>
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-primary text-white">
            <h6 class="m-0 font-weight-bold"><i class="fas fa-list-alt me-2"></i> Daftar Riwayat Order</h6>
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
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Order ID</th>
                            <th>Agen</th>
                            <th>Tanggal Order</th>
                            <th>Total Jumlah (Rp)</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($orders)): ?>
                            <tr>
                                <td colspan="8" class="text-center">Tidak ada riwayat order yang sesuai dengan filter.</td>
                            </tr>
                        <?php else: ?>
                            <?php $no = 1;
                            foreach ($orders as $order): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td>ORD-<?= esc($order['id']) ?></td>
                                    <td><?= esc($order['agent_username'] ?? 'N/A') ?></td>
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
                                        <a href="<?= base_url('distributor/orders/history_detail/' . $order['id']) ?>" class="btn btn-info btn-sm"><i class="fas fa-eye"></i> Detail</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <!-- Pager atau Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div>
                    <?php
                    $totalItems = $pager->getTotal();
                    $firstItem = ($totalItems > 0) ? (($pager->getCurrentPage() - 1) * $pager->getPerPage()) + 1 : 0;
                    $lastItem = ($totalItems > 0) ? min($firstItem + $pager->getPerPage() - 1, $totalItems) : 0;
                    ?>
                    Menampilkan <?= $firstItem ?> sampai <?= $lastItem ?> dari <?= $totalItems ?> Order Yang Selesai & Di Tolak !
                </div>
                <div>
                    <?= $pager->links('default', 'bootstrap_pagination') ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>