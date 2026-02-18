<?= $this->extend('layouts/main_layout') ?>

<?= $this->section('title') ?>
Register
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row justify-content-center mt-5 mb-5">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow-sm">
            <div class="card-header bg-success text-white text-center">
                <h4 class="mb-0"><i class="fas fa-user-plus me-2"></i> Register Akun Baru</h4>
            </div>
            <div class="card-body p-4">
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= session()->getFlashdata('error') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <form action="<?= base_url('register/process') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control <?= ($validation->hasError('username')) ? 'is-invalid' : ''; ?>"
                            id="username" name="username" value="<?= old('username') ?>" placeholder="Username unik Anda" required>
                        <?php if ($validation->hasError('username')): ?>
                            <div class="invalid-feedback">
                                <?= $validation->getError('username') ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control <?= ($validation->hasError('password')) ? 'is-invalid' : ''; ?>"
                            id="password" name="password" placeholder="Minimal 8 karakter" required>
                        <?php if ($validation->hasError('password')): ?>
                            <div class="invalid-feedback">
                                <?= $validation->getError('password') ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="pass_confirm" class="form-label">Konfirmasi Password</label>
                        <input type="password" class="form-control <?= ($validation->hasError('pass_confirm')) ? 'is-invalid' : ''; ?>"
                            id="pass_confirm" name="pass_confirm" placeholder="Ketik ulang password" required>
                        <?php if ($validation->hasError('pass_confirm')): ?>
                            <div class="invalid-feedback">
                                <?= $validation->getError('pass_confirm') ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="role" class="form-label">Daftar Sebagai</label>
                        <select class="form-select <?= ($validation->hasError('role')) ? 'is-invalid' : ''; ?>" id="role" name="role" required>
                            <option value="">Pilih Peran</option>
                            <option value="distributor" <?= old('role') == 'distributor' ? 'selected' : '' ?>>Distributor</option>
                            <option value="agen" <?= old('role') == 'agen' ? 'selected' : '' ?>>Agen</option>
                        </select>
                        <?php if ($validation->hasError('role')): ?>
                            <div class="invalid-feedback">
                                <?= $validation->getError('role') ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="parent_id" class="form-label">ID Induk (Parent ID)</label>
                        <input type="number" class="form-control <?= ($validation->hasError('parent_id')) ? 'is-invalid' : ''; ?>"
                            id="parent_id" name="parent_id" value="<?= old('parent_id') ?>" placeholder="ID Distributor/Pabrik Anda" required>
                        <div class="form-text">
                            *Isi dengan ID Distributor Anda jika Anda Agen. Isi dengan ID Pabrik Anda jika Anda Distributor.
                        </div>
                        <?php if ($validation->hasError('parent_id')): ?>
                            <div class="invalid-feedback">
                                <?= $validation->getError('parent_id') ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn btn-success btn-lg">Daftar</button>
                    </div>
                </form>

                <div class="text-center mt-3">
                    <p>Sudah punya akun? <a href="<?= base_url('login') ?>">Login sekarang</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>