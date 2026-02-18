<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Aplikasi AMDK</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="<?= base_url('/assets/css/bootstrap.min.css'); ?>">
    <link rel="shortcut icon" href="<?= base_url('favicon.ico'); ?>" type="image/x-icon">
    <style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .content-wrapper {
            flex: 1;
            padding-top: 20px;
            padding-bottom: 20px;
        }

        footer {
            background-color: #f8f9fa;
            padding: 20px 0;
            color: #6c757d;
        }
    </style>

    <?= $this->renderSection('head') ?>
</head>

<body class="bg-primary">
    <div class="container">
        <div class="row justify-content-center align-items-center" style="min-height: 90vh;">
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-lg">
                    <div class="card-body p-4 p-md-5">
                        <div class="text-center mb-4">
                            <i class="fas fa-tint fa-3x text-primary"></i>
                            <h3 class="fw-bold mt-2 mb-0">Selamat Datang</h3>
                            <p class="text-muted">Login untuk melanjutkan ke Aplikasi AMDK</p>
                        </div>

                        <?php if (session()->getFlashdata('error')): ?>
                            <div class="alert alert-danger d-flex align-items-center" role="alert">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <div><?= session()->getFlashdata('error') ?></div>
                            </div>
                        <?php endif; ?>
                        <?php if (session()->getFlashdata('success')): ?>
                            <div class="alert alert-success d-flex align-items-center" role="alert">
                                <i class="fas fa-check-circle me-2"></i>
                                <div><?= session()->getFlashdata('success') ?></div>
                            </div>
                        <?php endif; ?>

                        <form action="<?= base_url('login/process') ?>" method="post" class="needs-validation" novalidate>
                            <?= csrf_field() ?>

                            <div class="form-floating mb-3">
                                <input type="text" class="form-control <?= ($validation->hasError('username')) ? 'is-invalid' : ''; ?>"
                                    id="username" name="username" value="<?= old('username') ?>" placeholder="Username" required autocomplete="off">
                                <label for="username"><i class="fas fa-user me-2"></i>Username</label>
                                <?php if ($validation->hasError('username')): ?>
                                    <div class="invalid-feedback">
                                        <?= $validation->getError('username') ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="form-floating mb-3">
                                <input type="password" class="form-control <?= ($validation->hasError('password')) ? 'is-invalid' : ''; ?>"
                                    id="password" name="password" placeholder="Password" required autocomplete="off">
                                <label for="password"><i class="fas fa-lock me-2"></i>Password</label>
                                <?php if ($validation->hasError('password')): ?>
                                    <div class="invalid-feedback">
                                        <?= $validation->getError('password') ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember_me" id="remember_me" value="1">
                                    <label class="form-check-label" for="remember_me">
                                        Ingat Saya
                                    </label>
                                </div>
                                <!-- <a href="#" class="small text-decoration-none">Lupa Password?</a> -->
                            </div>


                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg fw-bold"><i class="fas fa-sign-in-alt me-2"></i> Login</button>
                                <a href="<?= base_url('/'); ?>" class="btn btn-secondary btn-lg fw-bold mt-2"><i class="fas fa-home"></i> Home</a>
                            </div>
                        </form>

                        <div class="text-center mt-4 text-muted">
                            <small>Belum punya akun? <a href="https://wa.me/6285258555439?text=Halo%20saya%20ingin%20bertanya" class="text-decoration-none">Hubungi Administrator</a></small>
                        </div>
                    </div>
                    <div class="card-footer text-center py-3 bg-light border-0">
                        <small class="text-muted">© <?= date('Y') ?> Aplikasi AMDK. All Rights Reserved.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="<?= base_url('/assets//js//bootstrap.bundle.min.js') ?>"></script>

</body>

</html>