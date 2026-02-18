<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <h1 class="mb-4"><?= $title ?></h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-primary text-white d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold"><i class="fas fa-boxes me-2"></i> Daftar Produk Pabrik & Harga Kustom Anda</h6>
            <button class="btn btn-light btn-sm" id="toggleSearchBtn" title="Tampilkan/Sembunyikan Pencarian">
                <i class="fas fa-search"></i>
            </button>
        </div>
        <div class="card-body">

            <!-- Search Form -->
            <div id="search-form-container" class="mb-4" style="display: none;">
                <form action="<?= base_url('distributor/products-prices') ?>" method="get" class="form-inline">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Cari nama produk..." value="<?= esc(request()->getGet('search')) ?>">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Cari</button>
                            <a href="<?= base_url('distributor/products-prices') ?>" class="btn btn-secondary"><i class="fas fa-sync-alt"></i> Reset</a>
                        </div>
                    </div>
                </form>
            </div>

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
                            <th>Harga Pabrik</th>
                            <th>Harga Jual Anda</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($allProducts)): ?>
                            <tr>
                                <td colspan="5" class="text-center">Belum ada produk dari pabrik atau produk tidak ditemukan.</td>
                            </tr>
                        <?php else: ?>
                            <?php $no = 1 + ($pager->getCurrentPage() - 1) * $pager->getPerPage();
                            foreach ($allProducts as $product): ?>
                                <?php
                                $userProduct = $distributorPricesMap[$product['id']] ?? null;
                                $customPrice = $userProduct ? 'Rp. ' . number_format($userProduct['custom_price'], 0, ',', '.') . ',00' : '- Belum diatur -';
                                ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= esc($product['product_name']) ?></td>
                                    <td>Rp. <?= esc(number_format($product['base_price'], 0, ',', '.')) ?>,00</td>
                                    <td>
                                        <span class="font-weight-bold <?= $userProduct ? 'text-success' : 'text-danger' ?>">
                                            <?= $customPrice ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-1">
                                            <button class="btn btn-info btn-sm btn-detail"
                                                data-name="<?= esc($product['product_name']) ?>"
                                                data-description="<?= esc($product['description']) ?>"
                                                data-price="Rp. <?= esc(number_format($product['base_price'], 0, ',', '.')) ?>,00">
                                                <i class="fas fa-eye"></i> Detail
                                            </button>
                                            <a href="<?= base_url('distributor/products-prices/set/' . $product['id']) ?>" class="btn btn-warning btn-sm">
                                                <i class="fas fa-money-bill-wave"></i> Atur Harga
                                            </a>
                                            <?php if ($userProduct): ?>
                                                <form action="<?= base_url('distributor/products-prices/delete/' . $userProduct['id']) ?>" method='get' class="form-delete d-inline">
                                                    <?= csrf_field(); ?>
                                                    <button type="button" class="btn btn-danger btn-sm" onclick="konfirmasi(this)">
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

<?= $this->section('scripts') ?>
<script>
    // Toggle Search Form
    document.getElementById('toggleSearchBtn').addEventListener('click', function() {
        var searchForm = document.getElementById('search-form-container');
        if (searchForm.style.display === 'none') {
            searchForm.style.display = 'block';
        } else {
            searchForm.style.display = 'none';
        }
    });

    // SweetAlert2 for Product Details
    document.querySelectorAll('.btn-detail').forEach(button => {
        button.addEventListener('click', function() {
            const productName = this.dataset.name;
            const productDesc = this.dataset.description;
            const productPrice = this.dataset.price;

            Swal.fire({
                title: `<strong>Detail Produk: ${productName}</strong>`,
                icon: 'info',
                html: `
                    <div style="text-align: left;">
                        <p><strong>Deskripsi:</strong></p>
                        <p>${productDesc || 'Tidak ada deskripsi.'}</p>
                        <hr>
                        <p><strong>Harga Pabrik:</strong> ${productPrice}</p>
                    </div>
                `,
                showCloseButton: true,
                focusConfirm: false,
                confirmButtonText: '<i class="fa fa-thumbs-up"></i> OK!',
                confirmButtonAriaLabel: 'Thumbs up, great!',
            });
        });
    });

    // Konfirmasi Hapus (jika Anda membutuhkannya)
    function konfirmasi(button) {
        Swal.fire({
            title: 'Anda Yakin?',
            text: "Harga kustom untuk produk ini akan dihapus!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                button.closest('form').submit();
            }
        })
    }
</script>
<?= $this->endSection() ?>