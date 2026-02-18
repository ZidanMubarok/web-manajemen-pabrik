<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <h1 class="mb-4"><?= $title ?></h1>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="<?= base_url('pabrik/products/create') ?>" class="btn btn-primary"><i class="fas fa-plus me-1"></i> Tambah Produk</a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-primary text-white">
            <h6 class="m-0 font-weight-bold"><i class="fas fa-box-open me-2"></i> Daftar Produk Saya</h6>
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

            <!-- Filter Form -->
            <div class="mb-3">
                <form action="<?= base_url('pabrik/products') ?>" method="get" class="d-flex">
                    <input type="text" name="search" class="form-control me-2" placeholder="Cari produk (nama, deskripsi)" value="<?= esc($search ?? '') ?>">
                    <button type="submit" class="btn btn-outline-primary"><i class="fas fa-search"></i></button>
                    <?php if (!empty($search)): ?>
                        <a href="<?= base_url('pabrik/products') ?>" class="btn btn-outline-secondary ms-2"><i class="fas fa-sync-alt"></i></a>
                    <?php endif; ?>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Produk</th>
                            <th>Deskripsi</th>
                            <th>Harga Dasar (Rp)</th>
                            <th>Tanggal Dibuat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($products)): ?>
                            <tr>
                                <td colspan="6" class="text-center">Belum ada produk yang ditambahkan.</td>
                            </tr>
                        <?php else: ?>
                            <?php
                            // Hitung nomor awal untuk pagination
                            $currentPage = $pager->getCurrentPage();
                            $perPage = $pager->getPerPage();
                            $no = (($currentPage - 1) * $perPage) + 1;
                            foreach ($products as $product): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= esc($product['product_name']) ?></td>
                                    <td><?= esc(substr($product['description'], 0, 100)) ?><?= (strlen($product['description']) > 100) ? '...' : '' ?></td>
                                    <td>Rp. <?= esc(number_format($product['base_price'], 0, ',', '.')) ?>,00</td>
                                    <td><?= date('d/m/Y H:i', strtotime($product['created_at'])) ?></td>
                                    <td>
                                        <form action="<?= base_url('pabrik/products/delete/' . $product['id']) ?>" method='get' enctype='multipart/form-data' class="form-delete">
                                            <a href="<?= base_url('pabrik/products/edit/' . $product['id']) ?>" class="btn btn-sm btn-info text-white"><i class="fas fa-edit"></i>Edit</a>
                                            <?= csrf_field(); ?>
                                            <button type="button" class="btn btn-danger btn-sm" onclick="konfirmasi(this)"><i class="fas fa-trash"></i>Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Informasi Total Data dan Pagination Links -->
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div>
                    <?php
                    $totalItems = $pager->getTotal();
                    $firstItem = ($totalItems > 0) ? (($pager->getCurrentPage() - 1) * $pager->getPerPage()) + 1 : 0;
                    $lastItem = ($totalItems > 0) ? min($firstItem + $pager->getPerPage() - 1, $totalItems) : 0;
                    ?>
                    Menampilkan <?= $firstItem ?> sampai <?= $lastItem ?> dari <?= $totalItems ?> total produk
                </div>
                <div>
                    <?= $pager->links('default', 'bootstrap_pagination') ?>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    function konfirmasi(button) {
        const form = button.closest('form');
        const productId = form.action.split('/').pop();

        const modalHtml = `
            <div class="modal fade" id="confirmDeleteProductModal" tabindex="-1" aria-labelledby="confirmDeleteProductModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="confirmDeleteProductModalLabel">Konfirmasi Penghapusan Produk</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            Apakah Anda yakin ingin menghapus produk ini?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="button" class="btn btn-danger" id="confirmDeleteProductButton">Hapus</button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        if (!document.getElementById('confirmDeleteProductModal')) {
            document.body.insertAdjacentHTML('beforeend', modalHtml);
        }

        const confirmDeleteProductModal = new bootstrap.Modal(document.getElementById('confirmDeleteProductModal'));
        confirmDeleteProductModal.show();

        document.getElementById('confirmDeleteProductButton').onclick = function() {
            form.submit();
            confirmDeleteProductModal.hide();
        };

        document.getElementById('confirmDeleteProductModal').addEventListener('hidden.bs.modal', function(event) {
            event.target.remove();
        });
    }
</script>
<?= $this->endSection() ?>