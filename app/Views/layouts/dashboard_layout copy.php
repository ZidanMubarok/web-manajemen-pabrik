<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->renderSection('title') ?><?= $this->renderSection('title') ? ' | ' : '' ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="<?= base_url('/assets//css//bootstrap.min.css'); ?>">

    <style>
        :root {
            --sidebar-width: 250px;
            --header-height: 60px;
            --primary-blue: #007bff;
            --dark-blue: #0A1C47;
            /* Warna navbar/sidebar yang lebih gelap */
            --light-gray: #f8f9fa;
            /* Warna background utama */
        }

        body {
            font-family: 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: var(--light-gray);
            overflow-x: hidden;
        }

        /* Navbar Top */
        .navbar-top {
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, .08);
            height: var(--header-height);
            position: fixed;
            width: 100%;
            z-index: 1030;
            display: flex;
            align-items: center;
            padding-left: calc(var(--sidebar-width) + 1rem);
        }

        .navbar-top .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--dark-blue);
        }

        .navbar-top .navbar-toggler {
            border: none;
        }

        /* Sidebar Navigasi */
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background-color: var(--dark-blue);
            color: #fff;
            padding-top: var(--header-height);
            overflow-y: auto;
            transition: all 0.3s;
            z-index: 1040;
            box-shadow: 2px 0 5px rgba(0, 0, 0, .1);
        }

        .sidebar .sidebar-header {
            padding: 1rem;
            text-align: center;
            font-size: 1.25rem;
            font-weight: bold;
            background-color: var(--dark-blue);
            position: absolute;
            top: 0;
            width: 100%;
            height: var(--header-height);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .sidebar .nav-link {
            color: #ccc;
            padding: 0.75rem 1.5rem;
            display: flex;
            align-items: center;
            border-left: 5px solid transparent;
            transition: all 0.2s;
        }

        .sidebar .nav-link:hover {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.1);
        }

        .sidebar .nav-link.active {
            color: #fff;
            background-color: var(--primary-blue);
            border-left-color: #fff;
            font-weight: bold;
        }

        .sidebar .nav-link i {
            margin-right: 0.75rem;
            font-size: 1.1rem;
        }

        /* Submenu (Monitoring, Validasi Order, Daftar Harga) */
        .sidebar .collapse.show .nav-item {
            background-color: rgba(0, 0, 0, 0.2);
        }

        .sidebar .collapse .nav-link {
            padding-left: 3rem;
            font-size: 0.95rem;
        }

        .sidebar .nav-link[data-bs-toggle="collapse"]::after {
            content: "\f107";
            font-family: "Font Awesome 5 Free";
            font-weight: 900;
            margin-left: auto;
            transition: transform 0.2s;
        }

        .sidebar .nav-link[data-bs-toggle="collapse"].collapsed::after {
            transform: rotate(-90deg);
        }

        .sidebar .nav-link[aria-expanded="true"]::after {
            transform: rotate(0deg);
        }

        /* Konten Utama */
        .main-content {
            margin-left: var(--sidebar-width);
            padding-top: var(--header-height);
            min-height: calc(100vh - var(--header-height));
            transition: margin-left 0.3s;
        }

        .content-area {
            padding: 1.5rem;
        }

        /* --- Perbaikan untuk Offcanvas --- */
        .offcanvas-header {
            background-color: var(--dark-blue);
            /* Menyamakan warna header offcanvas dengan sidebar */
            color: #fff;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .offcanvas-header .btn-close {
            filter: invert(1) grayscale(100%) brightness(200%);
            /* Membuat ikon close putih */
        }

        .offcanvas-body.sidebar-offcanvas-body {
            /* Menambahkan kelas baru untuk memisahkan styling offcanvas body */
            background-color: var(--dark-blue);
            /* Latar belakang untuk body offcanvas */
            padding-top: 0;
            /* Tidak perlu padding-top lagi di offcanvas body */
        }

        .offcanvas-body.sidebar-offcanvas-body .nav-item {
            padding-top: 0.25rem;
            /* Sedikit padding atas untuk item nav */
            padding-bottom: 0.25rem;
            /* Sedikit padding bawah untuk item nav */
        }

        /* --- Akhir Perbaikan Offcanvas --- */

        /* Responsive adjustments */
        @media (max-width: 991.98px) {
            .sidebar {
                left: -var(--sidebar-width);
            }

            .sidebar.show {
                left: 0;
            }

            .main-content {
                margin-left: 0;
            }

            .navbar-top {
                padding-left: 1rem;
            }
        }

        /* Aesthetic enhancements for cards and tables */
        .card {
            border: none;
            border-radius: 0.5rem;
            box-shadow: 0 4px 8px rgba(0, 0, 0, .05);
        }

        .card-header {
            border-bottom: 1px solid rgba(0, 0, 0, .125);
            font-weight: bold;
            background-color: #f2f2f7;
            color: #333;
        }

        .card-header.bg-primary {
            background-color: var(--primary-blue) !important;
            color: #fff !important;
        }

        .card-header.bg-info {
            background-color: #0dcaf0 !important;
            color: #fff !important;
        }

        .card-header.bg-success {
            background-color: #198754 !important;
            color: #fff !important;
        }

        .table {
            --bs-table-bg: #fff;
            --bs-table-color: #212529;
        }

        .table thead {
            background-color: #e9ecef;
        }

        .table th {
            font-weight: bold;
        }

        .alert-status {
            display: inline-block;
            padding: .25em .6em;
            font-size: .75em;
            font-weight: 700;
            line-height: 1;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: .375rem;
            color: #fff;
        }

        .alert-status.status-dikirim {
            background-color: #28a745;
        }

        .alert-status.status-diterima {
            background-color: #007bff;
        }

        .alert-status.status-pending {
            background-color: #ffc107;
            color: #333;
        }

        /* Untuk memastikan tinggi card tetap sama */
        .card.card-dashboard-summary {
            height: 100%;
            /* Memastikan card mengisi tinggi yang tersedia di kolomnya */
        }

        .card.card-dashboard-summary .card-body {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            /* Untuk menjaga konten card terdistribusi */
            height: 100%;
        }
    </style>

    <?= $this->renderSection('head') ?>
</head>

<body>
    <?php
    $currentUrl = current_url(true)->getPath();
    $role = session()->get('role');
    ?>

    <nav class="navbar navbar-expand-lg navbar-top">
        <div class="container-fluid">
            <button class="btn btn-outline-secondary d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarOffcanvas" aria-controls="sidebarOffcanvas">
                <i class="fas fa-bars"></i>
            </button>

            <div class="ms-auto d-flex align-items-center">
                <button class="btn btn-link text-decoration-none text-dark position-relative me-3">
                    <i class="fas fa-bell"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                        9+
                        <span class="visually-hidden">unread messages</span>
                    </span>
                </button>
                <div class="dropdown">
                    <a class="nav-link dropdown-toggle text-dark" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user-circle me-1"></i> <?= session()->get('username') ?? 'User' ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="<?= base_url('profile') ?>"><i class="fas fa-cogs me-1"></i> Pengaturan Profil</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="<?= base_url('logout') ?>"><i class="fas fa-sign-out-alt me-1"></i> Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <nav id="sidebar" class="sidebar d-none d-lg-block">
        <div class="sidebar-header">
            <a href="<?= base_url('/'); ?>" class="navbar-brand">
                <i class="fas fa-bottle-water me-2"></i>AMDK
            </a>
        </div>
        <ul class="nav flex-column mt-3">
            <li class="nav-item">
                <a class="nav-link <?= $currentUrl === base_url($role) ? 'active' : '' ?>" href="<?= base_url($role) ?>">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            </li>
            <?php if ($role == 'pabrik') : ?>
                <li class="nav-item">
                    <a class="nav-link <?= strpos($currentUrl, 'pabrik/users') !== false ? 'active' : '' ?>" href="<?= base_url('pabrik/users'); ?>"><i class="fas fa-users"></i> Manajemen Distributor</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= strpos($currentUrl, 'pabrik/products') !== false ? 'active' : '' ?>" href="<?= base_url('pabrik/products') ?>"><i class="fa-solid fa-boxes-stacked"></i> Manajemen Produk</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= strpos($currentUrl, 'pabrik/monitoring_distributor') !== false ? 'active' : '' ?>" href="<?= base_url('pabrik/monitoring_distributor') ?>"><i class="fas fa-clock"></i> Monitoring Distributor</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= strpos($currentUrl, 'pabrik/daftar_harga') !== false ? 'active' : '' ?>" href="<?= base_url('pabrik/daftar_harga') ?>"><i class="fas fa-money-bill"></i> Daftar Harga</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= strpos($currentUrl, 'pabrik/incoming-orders') !== false ? 'active' : '' ?>" href="<?= base_url('pabrik/incoming-orders') ?>"><i class="fa-solid fa-basket-shopping"></i> Order Masuk</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= strpos($currentUrl, 'pabrik/shipments/history') !== false ? 'active' : '' ?>" href="<?= base_url('pabrik/shipments/history') ?>"><i class="fa-solid fa-truck-arrow-right"></i> Manajemen Pengiriman</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= strpos($currentUrl, 'pabrik/riwayat_pengiriman') !== false ? 'active' : '' ?>" href="<?= base_url('pabrik/riwayat_pengiriman') ?>"><i class="fas fa-truck"></i> Riwayat Pengiriman</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= strpos($currentUrl, 'pabrik/orders/history') !== false ? 'active' : '' ?>" href="<?= base_url('pabrik/orders/history') ?>"><i class="fas fa-clipboard"></i> History Order</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= strpos($currentUrl, 'pabrik/distributor-invoices') !== false ? 'active' : '' ?>" href="<?= base_url('pabrik/distributor-invoices') ?>"><i class="fa-solid fa-file-invoice-dollar"></i> Tagihan Distributor</a>
                </li>
            <?php elseif ($role == 'distributor') : ?>
                <li class="nav-item">
                    <a class="nav-link <?= strpos($currentUrl, 'distributor/agents') !== false ? 'active' : '' ?>" href="<?= base_url('distributor/agents') ?>"><i class="fas fa-users"></i> Manajemen Agen</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= strpos($currentUrl, 'distributor/products-prices') !== false ? 'active' : '' ?>" href="<?= base_url('distributor/products-prices') ?>">
                        <i class="fas fa-file-invoice-dollar"></i> Daftar Harga
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= strpos($currentUrl, 'distributor/orders/incoming') !== false ? 'active' : '' ?>" href="<?= base_url('distributor/orders/incoming') ?>"><i class="fa-solid fa-basket-shopping"></i> Order Masuk</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= strpos($currentUrl, 'distributor/orders/history-to-pabrik') !== false ? 'active' : '' ?>" href="<?= base_url('distributor/orders/history-to-pabrik') ?>"><i class="fa-solid fa-check-to-slot"></i> Order Valid Pabrik</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= strpos($currentUrl, 'distributor/orders/history') !== false ? 'active' : '' ?>" href="<?= base_url('distributor/orders/history') ?>"><i class="fa-regular fa-clipboard"></i> Riwayat Order</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= strpos($currentUrl, 'distributor/invoices') !== false ? 'active' : '' ?>" href="<?= base_url('distributor/invoices'); ?>"><i class="fas fa-hand-holding-dollar"></i> Tagihan Agen</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= strpos($currentUrl, 'distributor/invoicesme') !== false ? 'active' : '' ?>" href="<?= base_url('distributor/invoicesme'); ?>"><i class="fa-solid fa-file-invoice-dollar"></i> Tagihan Saya</a>
                </li>
            <?php elseif ($role == 'agen') : ?>
                <li class="nav-item">
                    <a class="nav-link <?= strpos($currentUrl, 'agen/orders/create') !== false ? 'active' : '' ?>" href="<?= base_url('agen/orders/create') ?>"><i class="fas fa-shopping-cart"></i> Buat Order</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= strpos($currentUrl, 'agen/orders/history') !== false ? 'active' : '' ?>" href="<?= base_url('agen/orders/history') ?>"><i class="fas fa-history"></i> Riwayat Order</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= strpos($currentUrl, 'agen/invoices') !== false ? 'active' : '' ?>" href="<?= base_url('agen/invoices') ?>"><i class="fas fa-receipt"></i> Tagihan</a>
                </li>
            <?php endif; ?>
            <li class="nav-item">
                <a class="nav-link" href="<?= base_url('logout') ?>"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </li>
        </ul>
    </nav>

    <div class="offcanvas offcanvas-start" tabindex="-1" id="sidebarOffcanvas" aria-labelledby="sidebarOffcanvasLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="sidebarOffcanvasLabel"><i class="fas fa-bottle-water me-2"></i>AMDK</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body sidebar-offcanvas-body">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link <?= $currentUrl === base_url($role) ? 'active' : '' ?>" href="<?= base_url($role) ?>">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                </li>
                <?php if ($role == 'pabrik') : ?>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos($currentUrl, 'pabrik/users') !== false ? 'active' : '' ?>" href="<?= base_url('pabrik/users'); ?>"><i class="fas fa-users"></i> Manajemen Distributor</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos($currentUrl, 'pabrik/products') !== false ? 'active' : '' ?>" href="<?= base_url('pabrik/products') ?>"><i class="fa-solid fa-boxes-stacked"></i> Manajemen Produk</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos($currentUrl, 'pabrik/monitoring_distributor') !== false ? 'active' : '' ?>" href="<?= base_url('pabrik/monitoring_distributor') ?>"><i class="fas fa-clock"></i> Monitoring Distributor</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos($currentUrl, 'pabrik/daftar_harga') !== false ? 'active' : '' ?>" href="<?= base_url('pabrik/daftar_harga') ?>"><i class="fas fa-money-bill"></i> Daftar Harga</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos($currentUrl, 'pabrik/incoming-orders') !== false ? 'active' : '' ?>" href="<?= base_url('pabrik/incoming-orders') ?>"><i class="fa-solid fa-basket-shopping"></i> Order Masuk</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos($currentUrl, 'pabrik/shipments/history') !== false ? 'active' : '' ?>" href="<?= base_url('pabrik/shipments/history') ?>"><i class="fa-solid fa-truck-arrow-right"></i> Manajemen Pengiriman</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos($currentUrl, 'pabrik/riwayat_pengiriman') !== false ? 'active' : '' ?>" href="<?= base_url('pabrik/riwayat_pengiriman') ?>"><i class="fas fa-truck"></i> Riwayat Pengiriman</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos($currentUrl, 'pabrik/orders/history') !== false ? 'active' : '' ?>" href="<?= base_url('pabrik/orders/history') ?>"><i class="fas fa-clipboard"></i> History Order</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos($currentUrl, 'pabrik/distributor-invoices') !== false ? 'active' : '' ?>" href="<?= base_url('pabrik/distributor-invoices') ?>"><i class="fa-solid fa-file-invoice-dollar"></i> Tagihan Distributor</a>
                    </li>
                <?php elseif ($role == 'distributor') : ?>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos($currentUrl, 'distributor/agents') !== false ? 'active' : '' ?>" href="<?= base_url('distributor/agents') ?>"><i class="fas fa-users"></i> Manajemen Agen</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos($currentUrl, 'distributor/products-prices') !== false ? 'active' : '' ?>" href="<?= base_url('distributor/products-prices') ?>">
                            <i class="fas fa-file-invoice-dollar"></i> Daftar Harga
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos($currentUrl, 'distributor/orders/incoming') !== false ? 'active' : '' ?>" href="<?= base_url('distributor/orders/incoming') ?>"><i class="fa-solid fa-basket-shopping"></i> Order Masuk</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos($currentUrl, 'distributor/orders/history-to-pabrik') !== false ? 'active' : '' ?>" href="<?= base_url('distributor/orders/history-to-pabrik') ?>"><i class="fa-solid fa-check-to-slot"></i> Order Valid Pabrik</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos($currentUrl, 'distributor/orders/history') !== false ? 'active' : '' ?>" href="<?= base_url('distributor/orders/history') ?>"><i class="fa-regular fa-clipboard"></i> Riwayat Order</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos($currentUrl, 'distributor/invoices/') !== false ? 'active' : '' ?>" href="<?= base_url('distributor/invoices'); ?>"><i class="fas fa-hand-holding-dollar"></i> Tagihan Agen</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos($currentUrl, 'distributor/invoicesme/') !== false ? 'active' : '' ?>" href="<?= base_url('distributor/invoicesme'); ?>"><i class="fa-solid fa-file-invoice-dollar"></i> Tagihan Saya</a>
                    </li>
                <?php elseif ($role == 'agen') : ?>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos($currentUrl, 'agen/orders/create') !== false ? 'active' : '' ?>" href="<?= base_url('agen/orders/create') ?>"><i class="fas fa-shopping-cart"></i> Buat Order</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos($currentUrl, 'agen/orders/history') !== false ? 'active' : '' ?>" href="<?= base_url('agen/orders/history') ?>"><i class="fas fa-history"></i> Riwayat Order</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos($currentUrl, 'agen/invoices') !== false ? 'active' : '' ?>" href="<?= base_url('agen/invoices') ?>"><i class="fas fa-receipt"></i> Tagihan</a>
                    </li>
                <?php endif; ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url('logout') ?>"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </li>
            </ul>
        </div>
    </div>


    <div class="main-content">
        <div class="content-area">
            <?= $this->renderSection('content') ?>
        </div>
    </div>

    <script src="<?= base_url('/assets//js//bootstrap.bundle.min.js') ?>"></script>
    <script src="<?= base_url('/assets//js//sweetalert2.all.min.js') ?>"></script>

    <script>
        // ... kode lainnya ...
        document.addEventListener('DOMContentLoaded', function() {
            // Ambil path URL saat ini (tanpa parameter GET)
            const currentPathname = window.location.pathname;

            const navLinks = document.querySelectorAll('.sidebar .nav-link, .sidebar-offcanvas-body .nav-link');

            navLinks.forEach(link => {
                // Ambil path dari href link (juga tanpa parameter)
                const linkPathname = new URL(link.href).pathname;

                link.classList.remove('active');

                // Bandingkan path URL saat ini dengan path dari link
                if (currentPathname === linkPathname) {
                    link.classList.add('active');

                    // Jika link adalah toggle untuk submenu, pastikan submenu terbuka
                    let parentCollapse = link.closest('.collapse');
                    if (parentCollapse) {
                        new bootstrap.Collapse(parentCollapse, {
                            toggle: false
                        }).show();
                        let toggleButton = document.querySelector(`[data-bs-target="#${parentCollapse.id}"]`);
                        if (toggleButton) {
                            toggleButton.classList.remove('collapsed');
                            toggleButton.setAttribute('aria-expanded', 'true');
                        }
                    }
                }
            });

            // Handle offcanvas sidebar untuk small screens
            const sidebarOffcanvas = document.getElementById('sidebarOffcanvas');
            if (sidebarOffcanvas) {
                sidebarOffcanvas.addEventListener('show.bs.offcanvas', function() {
                    navLinks.forEach(link => {
                        link.classList.remove('active');
                        if (currentPathname === new URL(link.href).pathname) {
                            link.classList.add('active');
                        }
                    });
                });
            }
        });
        // ... kode lainnya ...
    </script>
    <!-- <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Logika untuk menandai menu aktif berdasarkan URL
            // Perbaikan: gunakan perbandingan string yang lebih tepat
            const currentUrl = window.location.href;
            const navLinks = document.querySelectorAll('.sidebar .nav-link, .sidebar-offcanvas-body .nav-link');

            navLinks.forEach(link => {
                // Hapus kelas aktif dari semua link
                link.classList.remove('active');

                // Jika URL saat ini cocok persis dengan href link
                const linkHref = link.href;
                if (currentUrl === linkHref) {
                    link.classList.add('active');

                    // Jika link adalah toggle untuk submenu, pastikan submenu terbuka
                    let parentCollapse = link.closest('.collapse');
                    if (parentCollapse) {
                        new bootstrap.Collapse(parentCollapse, {
                            toggle: false
                        }).show();
                        let toggleButton = document.querySelector(`[data-bs-target="#${parentCollapse.id}"]`);
                        if (toggleButton) {
                            toggleButton.classList.remove('collapsed');
                            toggleButton.setAttribute('aria-expanded', 'true');
                        }
                    }
                }
            });

            // Handle offcanvas sidebar for small screens
            const sidebarOffcanvas = document.getElementById('sidebarOffcanvas');
            if (sidebarOffcanvas) {
                sidebarOffcanvas.addEventListener('show.bs.offcanvas', function() {
                    // Update active state ketika offcanvas ditampilkan
                    navLinks.forEach(link => {
                        link.classList.remove('active');
                        if (currentUrl === link.href) {
                            link.classList.add('active');
                        }
                    });
                });
            }
        });
    </script> -->
    <?php if (session()->getFlashdata('swal_icon')) : ?>
        <script>
            Swal.fire({
                title: "<?= session()->getFlashdata('swal_title'); ?>",
                text: "<?= session()->getFlashdata('swal_text'); ?>",
                icon: "<?= session()->getFlashdata('swal_icon'); ?>"
            });
        </script>
    <?php endif; ?>
    <script>
        function konfirmasi(button) {
            // Buat variable untuk menyimpan form hapus
            const form = button.closest('.form-delete');
            Swal.fire({
                title: "Apa kamu yakin?",
                text: "Kamu yakin ingin maghapus data ini!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ya, hapus saja!"
            }).then((result) => {
                // Jika user mengkil tombol ya hapus saja !
                if (result.isConfirmed) {
                    // jalankan form hapus
                    form.submit();
                }
            });
        }
    </script>


    <?= $this->renderSection('scripts') ?>
</body>

</html>