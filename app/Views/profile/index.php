<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<main class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800"><?= $title ?></h1>

    <div class="row">
        <!-- Kolom Informasi Profil -->
        <div class="col-xl-4 col-lg-5 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center d-flex flex-column justify-content-center">
                    <div class="mb-3">
                        <i class="fas fa-user-circle fa-6x text-primary"></i>
                    </div>
                    <h4 class="card-title font-weight-bold mb-1"><?= esc($user['username']) ?></h4>
                    <p class="text-muted small mb-3"><?= esc($user['email']) ?></p>

                    <div class="px-3">
                        <span class="badge bg-primary-soft text-primary mb-3" style="font-size: 0.9rem;"><?= ucfirst(esc($user['role'])) ?></span>

                        <p class="card-text text-gray-700">
                            <?php
                            if ($user['role'] === 'agen' && isset($parent_info)) {
                                echo 'Anda adalah agen dari distributor <strong>' . esc($parent_info['username']) . '</strong>.';
                            } elseif ($user['role'] === 'distributor' && isset($parent_info)) {
                                echo 'Anda adalah distributor dari pabrik <strong>' . esc($parent_info['username']) . '</strong>.';
                            } elseif ($user['role'] === 'pabrik') {
                                echo 'Anda terdaftar sebagai <strong>Pabrik</strong>.';
                            }
                            ?>
                        </p>
                    </div>
                </div>
                <div class="card-footer bg-light text-center">
                    <p class="small mb-0"><i class="fas fa-phone-alt me-2"></i><?= esc($user['no_telpon']) ?></p>
                </div>
            </div>
        </div>

        <!-- Kolom Form Edit Profil -->
        <div class="col-xl-8 col-lg-7 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header py-3 bg-primary">
                    <h6 class="m-0 font-weight-bold text-white"><i class="fas fa-user-edit me-2"></i> Ubah Data Profil</h6>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('errors')) : ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <h6 class="alert-heading fw-bold">Terdapat kesalahan validasi:</h6>
                            <ul class="mb-0">
                                <?php foreach (session()->getFlashdata('errors') as $error) : ?>
                                    <li><?= esc($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <form action="<?= base_url('profile/update') ?>" method="post">
                        <?= csrf_field() ?>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="username" class="form-label fw-semibold"><i class="fas fa-user me-2"></i>Username</label>
                                <input type="text" class="form-control <?= ($validation->hasError('username')) ? 'is-invalid' : ''; ?>" id="username" name="username" value="<?= old('username', $user['username']) ?>" required>
                                <div class="invalid-feedback"><?= $validation->getError('username') ?></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label fw-semibold"><i class="fas fa-envelope me-2"></i>Email</label>
                                <input type="email" class="form-control <?= ($validation->hasError('email')) ? 'is-invalid' : ''; ?>" id="email" name="email" value="<?= old('email', $user['email']) ?>" required>
                                <div class="invalid-feedback"><?= $validation->getError('email') ?></div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="no_telpon" class="form-label fw-semibold"><i class="fas fa-mobile-alt me-2"></i>No. Telepon</label>
                                <input type="text" class="form-control <?= ($validation->hasError('no_telpon')) ? 'is-invalid' : ''; ?>" id="no_telpon" name="no_telpon" value="<?= old('no_telpon', $user['no_telpon']) ?>" required>
                                <div class="invalid-feedback"><?= $validation->getError('no_telpon') ?></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label fw-semibold"><i class="fas fa-key me-2"></i>Password Baru</label>
                                <input type="password" class="form-control <?= ($validation->hasError('password')) ? 'is-invalid' : ''; ?>" id="password" name="password" placeholder="Kosongkan jika tidak diubah">
                                <div class="invalid-feedback"><?= $validation->getError('password') ?></div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="alamat" class="form-label fw-semibold"><i class="fas fa-map-marker-alt me-2"></i>Alamat</label>
                            <textarea class="form-control <?= ($validation->hasError('alamat')) ? 'is-invalid' : ''; ?>" id="alamat" name="alamat" rows="3"><?= old('alamat', $user['alamat']) ?></textarea>
                            <div class="invalid-feedback"><?= $validation->getError('alamat') ?></div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex justify-content-end">
                            <a href="<?= base_url(session()->get('role')) ?>" class="btn btn-secondary me-2"><i class="fas fa-times me-1"></i> Batal</a>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>
<style>
    .bg-primary-soft {
        background-color: rgba(var(--bs-primary-rgb), 0.1);
    }
</style>
<?= $this->endSection() ?>