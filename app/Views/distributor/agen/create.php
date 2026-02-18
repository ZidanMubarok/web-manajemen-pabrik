<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800"><?= $title ?></h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-primary">
            <h6 class="m-0 font-weight-bold text-white"><i class="fas fa-user-plus me-2"></i> Form Tambah Agen Baru</h6>
        </div>
        <div class="card-body">
            <?php if (session()->getFlashdata('errors')) : ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <h6 class="alert-heading">Terdapat kesalahan validasi:</h6>
                    <ul class="mb-0">
                        <?php foreach (session()->getFlashdata('errors') as $error) : ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('distributor/agents/store') ?>" method="post">
                <?= csrf_field() ?>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control <?= ($validation->hasError('username')) ? 'is-invalid' : ''; ?>" id="username" name="username" value="<?= old('username') ?>" required>
                            <div class="invalid-feedback">
                                <?= $validation->getError('username') ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control <?= ($validation->hasError('password')) ? 'is-invalid' : ''; ?>" id="password" name="password" required>
                            <div class="invalid-feedback">
                                <?= $validation->getError('password') ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control <?= ($validation->hasError('email')) ? 'is-invalid' : ''; ?>" id="email" name="email" value="<?= old('email') ?>" placeholder="contoh@email.com" required>
                            <div class="invalid-feedback">
                                <?= $validation->getError('email') ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="no_telpon" class="form-label">No. Telepon</label>
                            <input type="text" class="form-control <?= ($validation->hasError('no_telpon')) ? 'is-invalid' : ''; ?>" id="no_telpon" name="no_telpon" value="<?= old('no_telpon') ?>" placeholder="08123456789" required>
                            <div class="invalid-feedback">
                                <?= $validation->getError('no_telpon') ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="alamat" class="form-label">Alamat</label>
                    <textarea class="form-control <?= ($validation->hasError('alamat')) ? 'is-invalid' : ''; ?>" id="alamat" name="alamat" rows="3" required><?= old('alamat') ?></textarea>
                    <div class="invalid-feedback">
                        <?= $validation->getError('alamat') ?>
                    </div>
                </div>

                <div class="alert alert-info mt-4" role="alert">
                    <i class="fas fa-info-circle"></i> Pengguna ini akan dibuat dengan peran sebagai <strong>Agen</strong> di bawah manajemen Anda.
                </div>

                <div class="d-flex justify-content-end">
                    <a href="<?= base_url('distributor/agents') ?>" class="btn btn-secondary me-2"><i class="fas fa-times me-1"></i> Batal</a>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Simpan Agen</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>