<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <h1 class="mb-4"><?= $title ?></h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-primary text-white">
            <h6 class="m-0 font-weight-bold"><i class="fas fa-dollar-sign me-2"></i> Atur Harga Jual untuk Produk: <?= esc($product['product_name']) ?></h6>
        </div>
        <div class="card-body">
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

            <form action="<?= base_url('distributor/products-prices/save') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="product_id" value="<?= esc($product['id']) ?>">

                <div class="mb-3">
                    <label for="product_name" class="form-label">Nama Produk Pabrik</label>
                    <input type="text" class="form-control" id="product_name" value="<?= esc($product['product_name']) ?>" readonly disabled>
                </div>
                <div class="mb-3">
                    <label for="base_price" class="form-label">Harga Dasar Pabrik (Rp)</label>
                    <input type="text" class="form-control" id="base_price" value="<?= esc(number_format($product['base_price'], 0, ',', '.')) ?>" readonly disabled>
                </div>
                <div class="mb-3">
                    <label for="custom_price" class="form-label">Harga Jual Anda (Rp)</label>
                    <input type="number" step="0.01" class="form-control" id="custom_price" name="custom_price" value="<?= old('custom_price', $userProduct['custom_price'] ?? '') ?>" required min="0">
                    <div class="form-text">Masukkan harga jual yang akan Anda terapkan untuk produk ini kepada agen Anda.</div>
                </div>

                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Simpan Harga</button>
                <a href="<?= base_url('distributor/products-prices') ?>" class="btn btn-secondary"><i class="fas fa-times me-1"></i> Batal</a>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>