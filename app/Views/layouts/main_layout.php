<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->renderSection('title') ?><?= $this->renderSection('title') ? ' | ' : '' ?>NuCless AMDK</title>

    <!-- Google Fonts (Optional, untuk tipografi yang lebih baik) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="<?= base_url('/assets/css/bootstrap.min.css'); ?>">
    <!-- PENAMBAHAN: Tautan ke file CSS kustom kita -->
    <link rel="stylesheet" href="<?= base_url('/assets/css/custom.css'); ?>">
    <link rel="shortcut icon" href="<?= base_url('assets/img/favicon.png'); ?>" type="image/x-icon">

    <?= $this->renderSection('head') ?>
</head>

<body>

    <!-- PENYESUAIAN: Navbar dibuat sedikit lebih modern -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="<?= base_url('/') ?>">
                <img src="<?= base_url('assets/img/favicon.png'); ?>" alt="NuCless Logo" width="30" class="d-inline-block align-text-top me-2">
                <span class="fw-bold text-primary">NuCless</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#home">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#produk">Produk</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#proses">Proses Kami</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#kontak">Kontak</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <?php if (session()->get('isLoggedIn')): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user-circle me-1"></i> <?= session()->get('username') ?? 'Profil' ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <?php
                                $role = session()->get('role');
                                $dashboard_link = base_url($role);
                                ?>
                                <li><a class="dropdown-item" href="<?= $dashboard_link ?>"><i class="fas fa-tachometer-alt me-1"></i> Dashboard</a></li>
                                <li><a class="dropdown-item" href="<?= base_url('profile'); ?>"><i class="fas fa-cogs me-1"></i> Pengaturan</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item" href="<?= base_url('logout') ?>"><i class="fas fa-sign-out-alt me-1"></i> Logout</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a href="<?= base_url('login') ?>" class="btn btn-primary btn-sm px-4"><i class="fas fa-sign-in-alt me-2"></i> Login</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- KONTEN UTAMA TIDAK PERLU CONTAINER, KARENA SETIAP SECTION AKAN MENGATURNYA SENDIRI -->
    <?= $this->renderSection('content') ?>

    <!-- PENYESUAIAN: Footer yang lebih profesional -->
    <footer class="bg-dark text-white pt-5 pb-4" id="kontak">
        <div class="container text-md-start">
            <div class="row text-md-start">

                <div class="col-md-3 col-lg-3 col-xl-3 mx-auto mt-3">
                    <h5 class="text-uppercase mb-4 fw-bold text-primary">NuCless AMDK</h5>
                    <p>Pabrik air minum dalam kemasan yang berkomitmen memberikan kemurnian dan kesegaran langsung dari sumber mata air terbaik untuk Anda.</p>
                </div>

                <div class="col-md-2 col-lg-2 col-xl-2 mx-auto mt-3">
                    <h5 class="text-uppercase mb-4 fw-bold text-primary">Navigasi</h5>
                    <p><a href="#" class="text-white" style="text-decoration: none;">Home</a></p>
                    <p><a href="#produk" class="text-white" style="text-decoration: none;">Produk</a></p>
                    <p><a href="#proses" class="text-white" style="text-decoration: none;">Proses Kami</a></p>
                    <p><a href="#" class="text-white" style="text-decoration: none;">Menjadi Mitra</a></p>
                </div>

                <div class="col-md-4 col-lg-3 col-xl-3 mx-auto mt-3">
                    <h5 class="text-uppercase mb-4 fw-bold text-primary">Kontak</h5>
                    <p><i class="fas fa-map-marker-alt me-3"></i> Sobowono, Yuwono, Kec. Kertosono, Kabupaten Nganjuk, Jawa Timur 64315</p>
                    <p><i class="fas fa-envelope me-3"></i> info@nucless.com</p>
                    <p><i class="fas fa-phone me-3"></i> +62 21 1234 5678</p>
                    <p><a href="https://wa.me/6285258555439?text=Halo%20saya%20tertarik%20dengan%20produk%20NuCless" target="_blank" class="text-white" style="text-decoration: none;"><i class="fab fa-whatsapp me-3"></i> +62 852 5855 5439</a></p>
                </div>
            </div>

            <hr class="mb-4">

            <div class="row align-items-center">
                <div class="col-md-7 col-lg-8">
                    <p>&copy; <?= date('Y') ?> NuCless AMDK. All Rights Reserved.
                        <br class="d-md-none"> Didesain oleh <a href="https://imersa.co.id/" class="text-primary" style="text-decoration: none;" target="_blank">PT. Imersa Solusi Teknologi</a>
                    </p>
                </div>
                <div class="col-md-5 col-lg-4">
                    <div class="text-center text-md-end">
                        <ul class="list-unstyled list-inline">
                            <li class="list-inline-item">
                                <a href="#" class="btn-floating btn-sm text-white" style="font-size: 23px;"><i class="fab fa-facebook"></i></a>
                            </li>
                            <li class="list-inline-item">
                                <a href="#" class="btn-floating btn-sm text-white" style="font-size: 23px;"><i class="fab fa-instagram"></i></a>
                            </li>
                            <li class="list-inline-item">
                                <a href="#" class="btn-floating btn-sm text-white" style="font-size: 23px;"><i class="fab fa-youtube"></i></a>
                            </li>
                            <li class="list-inline-item">
                                <a href="#" class="btn-floating btn-sm text-white" style="font-size: 23px;"><i class="fab fa-tiktok"></i></a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script src="<?= base_url('/assets/js/bootstrap.bundle.min.js') ?>"></script>
    <?= $this->renderSection('scripts') ?>
</body>

</html>