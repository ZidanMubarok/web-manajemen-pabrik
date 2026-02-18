<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->renderSection('title') ?><?= $this->renderSection('title') ? ' | ' : '' ?>AMDK Web</title>
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

<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand ms-5" href="<?= base_url('/') ?>">
                <img src="<?= base_url('assets/img/favicon.png'); ?>" alt="." width="30" class="d-inline-block align-text-top me-2"><span>NuCless</span>
                <!-- <i class="fas fa-solid fa-bottle-water me-2"></i>AMDK App -->
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="<?= base_url('/') ?>"><i class="fas fa-home me-1"></i> Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="<?= base_url('/courier/update-status') ?>"><i class="fas fa-shipping-fast me-2"></i> Kurir</a>
                    </li>
                    <?php if (session()->get('isLoggedIn')): ?>
                        <?php
                        $role = session()->get('role');
                        $dashboard_link = base_url($role); // Menggunakan role sebagai segmen URL
                        $dashboard_text = 'Dashboard ' . ucfirst($role);
                        ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= $dashboard_link ?>"><i class="fas fa-tachometer-alt me-1"></i> <?= $dashboard_text ?></a>
                        </li>
                    <?php endif; ?>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <?php if (session()->get('isLoggedIn')): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user-circle me-1"></i> <?= session()->get('username') ?? 'Profil' ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="<?= base_url('profile') ?>"><i class="fas fa-cogs me-1"></i> Pengaturan</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item" href="<?= base_url('logout') ?>"><i class="fas fa-sign-out-alt me-1"></i> Logout</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link me-4" href="<?= base_url('login') ?>"><i class="fas fa-sign-in-alt me-1"></i> Login</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container content-wrapper">
        <?= $this->renderSection('content') ?>
    </div>

    <footer class="text-center">
        <div class="container">
            <p>&copy; <?= date('Y') ?> AMDK Web. All Rights Reserved.</p>
            <p>Made with <i class="fas fa-heart text-danger"></i> by PT.Imersa Solusi Teknologi.</p>
        </div>
    </footer>
    <!-- Bootstrap JS (CDN) - Menggunakan CDN untuk kemudahan -->
    <script src="<?= base_url('/assets//js//bootstrap.bundle.min.js') ?>"></script>
    <?= $this->renderSection('scripts') ?>
</body>

</html>