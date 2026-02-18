<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <h1 class="mb-4"><?= $title ?></h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-primary text-white">
            <h6 class="m-0 font-weight-bold"><i class="fas fa-boxes me-2"></i> Daftar Produk Pabrik & Harga Kustom Anda</h6>
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
                            <th>Nama Produk</th>
                            <!-- <th>Deskripsi Produk</th> -->
                            <th>Harga Pabrik</th>
                            <th>Harga Jual Anda</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($allProducts)): ?>
                            <tr>
                                <td colspan="6" class="text-center">Belum ada produk dari pabrik.</td>
                            </tr>
                        <?php else: ?>
                            <?php $no = 1;
                            foreach ($allProducts as $product): ?>
                                <?php
                                $userProduct = $distributorPricesMap[$product['id']] ?? null;
                                // $customPrice = $userProduct ? number_format($userProduct['custom_price'], 0, ',', '.') : '- Belum diatur -';
                                $customPrice = $userProduct ? 'Rp. ' . number_format($userProduct['custom_price'], 0, ',', '.') . ',00' : '- Belum diatur -';
                                ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= esc($product['product_name']) ?></td>
                                    <!-- <td><?php # esc(substr($product['description'] ?? '', 0, 100)) ?><?php # (strlen($product['description'] ?? '') > 100) ? '...' : '' ?></td> -->
                                    <td>Rp. <?= esc(number_format($product['base_price'], 0, ',', '.')) ?>,00</td>
                                    <td>
                                        <span class="font-weight-bold <?= $userProduct ? 'text-success' : 'text-danger' ?>">
                                            <?= $customPrice ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-wrap g-1">
                                            <a href="<?= base_url('distributor/products-prices/set/' . $product['id']) ?>" class="btn btn-warning btn-sm me-1 mb-1">
                                                <i class="fas fa-money-bill-wave"></i> Atur Harga
                                            </a>
                                            <?php if ($userProduct): ?>
                                                <form action="<?= base_url('distributor/products-prices/delete/' . $userProduct['id']) ?>" method='get' enctype='multipart/form-data' class="form-delete">
                                                    <?= csrf_field(); ?>
                                                    <button type="button" class="btn btn-danger btn-sm mb-1" onclick="konfirmasi(this)">
                                                        <i class="fas fa-trash"></i> Hapus Harga
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
                <!-- Pager atau Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div>
                        <?php
                        $totalItems = $pager->getTotal();
                        $firstItem = ($totalItems > 0) ? (($pager->getCurrentPage() - 1) * $pager->getPerPage()) + 1 : 0;
                        $lastItem = ($totalItems > 0) ? min($firstItem + $pager->getPerPage() - 1, $totalItems) : 0;
                        ?>
                        Menampilkan <?= $firstItem ?> sampai <?= $lastItem ?> dari <?= $totalItems ?> Produk Pabrik
                    </div>
                    <div>
                        <?= $pager->links('default', 'bootstrap_pagination') ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>