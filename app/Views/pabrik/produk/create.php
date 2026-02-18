<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <h1 class="mb-4"><?= $title ?></h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-primary text-white">
            <h6 class="m-0 font-weight-bold"><i class="fas fa-plus-square me-2"></i> Form Tambah Produk Baru</h6>
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

            <form action="<?= base_url('pabrik/products/store') ?>" method="post">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label for="product_name" class="form-label">Nama Produk</label>
                    <input type="text" class="form-control" id="product_name" name="product_name" value="<?= old('product_name') ?>" required>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Deskripsi</label>
                    <textarea class="form-control" id="description" name="description" rows="3"><?= old('description') ?></textarea>
                </div>
                <div class="mb-3">
                    <label for="base_price" class="form-label">Harga Dasar (Rp)</label>
                    <input type="number" step="0.01" class="form-control" id="base_price" name="base_price" value="<?= old('base_price') ?>" required min="0">
                </div>
                <input type="hidden" name="user_id" value="1">

                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Simpan Produk</button>
                <a href="<?= base_url('pabrik/products') ?>" class="btn btn-secondary"><i class="fas fa-times me-1"></i> Batal</a>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>