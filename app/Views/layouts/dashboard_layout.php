<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->renderSection('title') ?><?= $this->renderSection('title') ? ' | ' : '' ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="<?= base_url('/assets/css/bootstrap.min.css'); ?>">

    <style>
        :root {
            --sidebar-width: 250px;
            --sidebar-mini-width: 60px;
            --header-height: 60px;
            --primary-blue: #007bff;
            --dark-blue: #0A1C47;
            --light-gray: #f8f9fa;
            --sidebar-transition-duration: 0.3s;
        }

        body {
            font-family: 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: var(--light-gray);
            position: relative;
        }

        /* ------------------------------------- */
        /* BASE STYLES FOR ALL SCREEN SIZES    */
        /* ------------------------------------- */

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
            padding-left: 1rem;
            transition: padding-left var(--sidebar-transition-duration);
        }

        /* Tombol Hamburger */
        .hamburger-toggler {
            background: none;
            border: none;
            color: var(--dark-blue);
            font-size: 1.5rem;
            cursor: pointer;
            margin-right: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .hamburger-toggler:focus {
            outline: none;
            box-shadow: none;
        }

        /* Fullscreen Toggle Button */
        .fullscreen-toggler {
            background: none;
            border: none;
            color: #333;
            font-size: 1.25rem;
            cursor: pointer;
            margin-right: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .fullscreen-toggler:focus {
            outline: none;
            box-shadow: none;
        }


        /* Sidebar Navigasi (Hanya untuk Desktop) */
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
            transition: all var(--sidebar-transition-duration) ease-in-out;
            z-index: 1040;
            box-shadow: 2px 0 5px rgba(0, 0, 0, .1);
            display: none;
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
            overflow: hidden;
        }

        .sidebar .sidebar-header .navbar-brand {
            display: flex;
            align-items: center;
            color: #fff;
            text-decoration: none;
        }

        .sidebar .sidebar-header .navbar-brand i {
            margin-right: 0.5rem;
        }

        .sidebar .sidebar-header .navbar-brand span {
            opacity: 1;
            transition: opacity var(--sidebar-transition-duration) ease-in-out;
        }

        .sidebar .nav-link {
            color: #ccc;
            padding: 0.75rem 1.5rem;
            display: flex;
            align-items: center;
            border-left: 5px solid transparent;
            transition: all 0.2s;
            white-space: nowrap;
            overflow: hidden;
        }

        .sidebar .nav-link i {
            margin-right: 0.75rem;
            font-size: 1.1rem;
            transition: margin-right var(--sidebar-transition-duration) ease-in-out;
        }

        .sidebar .nav-link span {
            opacity: 1;
            transition: opacity var(--sidebar-transition-duration) ease-in-out;
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

        /* Submenu */
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
            margin-left: 0;
            padding-top: var(--header-height);
            min-height: calc(100vh - var(--header-height));
            transition: margin-left var(--sidebar-transition-duration) ease-in-out;
            z-index: 1020;
            position: relative;
        }

        .content-area {
            padding: 1.5rem;
        }

        /* ------------------------------------- */
        /* MEDIA QUERIES FOR DESKTOP (>= 992px) */
        /* ------------------------------------- */
        @media (min-width: 992px) {
            .sidebar {
                display: block;
            }

            body.sidebar-expanded .navbar-top {
                padding-left: calc(var(--sidebar-width) + 1rem);
            }

            body.sidebar-expanded .main-content {
                margin-left: var(--sidebar-width);
            }

            body.sidebar-expanded .sidebar {
                width: var(--sidebar-width);
                left: 0;
            }

            body.sidebar-expanded .sidebar .nav-link span,
            body.sidebar-expanded .sidebar .sidebar-header .navbar-brand span {
                opacity: 1;
                width: auto;
            }

            body.sidebar-expanded .sidebar .nav-link i {
                margin-right: 0.75rem;
            }

            body.sidebar-expanded .sidebar .nav-link[data-bs-toggle="collapse"]::after {
                display: block;
            }

            body.sidebar-expanded .sidebar .collapse {
                display: block !important;
            }

            body.sidebar-mini.sidebar-collapse .navbar-top {
                padding-left: calc(var(--sidebar-mini-width) + 1rem);
            }

            body.sidebar-mini.sidebar-collapse .main-content {
                margin-left: var(--sidebar-mini-width);
            }

            body.sidebar-mini.sidebar-collapse .sidebar {
                width: var(--sidebar-mini-width);
            }

            body.sidebar-mini.sidebar-collapse .sidebar .sidebar-header .navbar-brand span,
            body.sidebar-mini.sidebar-collapse .sidebar .nav-link span {
                opacity: 0;
                width: 0;
                overflow: hidden;
            }

            body.sidebar-mini.sidebar-collapse .sidebar .nav-link i {
                margin-right: 0;
                justify-content: center;
                width: 100%;
            }

            body.sidebar-mini.sidebar-collapse .sidebar .nav-link {
                justify-content: center;
                padding: 0.75rem 0.5rem;
            }

            body.sidebar-mini.sidebar-collapse .sidebar .nav-link[data-bs-toggle="collapse"]::after {
                display: none;
            }

            body.sidebar-mini.sidebar-collapse .sidebar .collapse {
                display: none !important;
            }
        }

        /* ------------------------------------- */
        /* MEDIA QUERIES FOR MOBILE (< 992px)   */
        /* ------------------------------------- */
        @media (max-width: 991.98px) {
            .sidebar {
                display: none !important;
            }

            .main-content {
                margin-left: 0;
            }

            .navbar-top {
                padding-left: 1rem;
            }

            .offcanvas {
                width: var(--sidebar-width);
                background-color: var(--dark-blue);
                color: #fff;
            }

            .offcanvas-header {
                background-color: var(--dark-blue);
                color: #fff;
                border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            }

            .offcanvas-header .btn-close {
                filter: invert(1) grayscale(100%) brightness(200%);
            }

            .offcanvas-body {
                padding-top: 0;
            }

            .offcanvas-body .nav-item {
                padding-top: 0.25rem;
                padding-bottom: 0.25rem;
            }

            .offcanvas-body .nav-link {
                color: #ccc;
                padding: 0.75rem 1.5rem;
                display: flex;
                align-items: center;
                border-left: 5px solid transparent;
            }

            .offcanvas-body .nav-link:hover {
                color: #fff;
                background-color: rgba(255, 255, 255, 0.1);
            }

            .offcanvas-body .nav-link.active {
                color: #fff;
                background-color: var(--primary-blue);
                border-left-color: #fff;
                font-weight: bold;
            }

            .offcanvas-body .nav-link i {
                margin-right: 0.75rem;
            }

            .offcanvas-backdrop.show {
                opacity: 0.5;
                background-color: #000;
            }
        }

        /* ------------------------------------- */
        /* AESTHETIC ENHANCEMENTS                */
        /* ------------------------------------- */
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

        .card.card-dashboard-summary {
            height: 100%;
        }

        .card.card-dashboard-summary .card-body {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100%;
        }
    </style>

    <?= $this->renderSection('head') ?>
</head>

<body class="sidebar-mini sidebar-expanded">
    <?php
    $currentUrl = current_url(true)->getPath();
    $role = session()->get('role');
    ?>

    <!-- Navbar Atas -->
    <nav class="navbar navbar-expand-lg navbar-top">
        <div class="container-fluid">
            <!-- Tombol Toggle untuk Sidebar (Desktop) dan Offcanvas (Mobile) -->
            <button class="hamburger-toggler" type="button" id="mainSidebarToggle" data-bs-toggle="offcanvas" data-bs-target="#sidebarOffcanvas">
                <i class="fas fa-bars"></i>
            </button>

            <div class="ms-auto d-flex align-items-center">
                <!-- Tombol Fullscreen -->
                <button class="fullscreen-toggler" type="button" id="fullscreenToggle">
                    <i class="fas fa-expand"></i>
                </button>

                <!-- Tombol Notifikasi -->
                <!-- <button class="btn btn-link text-decoration-none text-dark position-relative me-3">
                    <i class="fas fa-bell"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                        9+
                        <span class="visually-hidden">unread messages</span>
                    </span>
                </button> -->

                <!-- Dropdown User -->
                <div class="dropdown">
                    <a class="nav-link dropdown-toggle text-dark" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user-circle me-1"></i> <?= session()->get('username') ?? 'User' ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="<?= base_url('profile') ?>"><i class="fas fa-cogs me-1"></i> Pengaturan Profil</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="<?= base_url('logout') ?>"><i class="fas fa-sign-out-alt me-1"></i> Keluar</a></li>
                    </ul>
                </div>
            </div>
            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <button
                        class="btn btn-link nav-link py-2 px-0 px-lg-2 dropdown-toggle d-flex align-items-center"
                        id="bd-theme"
                        type="button"
                        aria-expanded="false"
                        data-bs-toggle="dropdown"
                        data-bs-display="static">
                        <span class="theme-icon-active">
                            <i class="my-1"></i>
                        </span>
                        <span class="d-lg-none ms-2" id="bd-theme-text">Toggle theme</span>
                    </button>
                    <ul
                        class="dropdown-menu dropdown-menu-end"
                        aria-labelledby="bd-theme-text"
                        style="--bs-dropdown-min-width: 8rem;">
                        <li>
                            <button
                                type="button"
                                class="dropdown-item d-flex align-items-center active"
                                data-bs-theme-value="light"
                                aria-pressed="false">
                                <i class="bi bi-sun-fill me-2"></i>
                                Light
                                <i class="bi bi-check-lg ms-auto d-none"></i>
                            </button>
                        </li>
                        <li>
                            <button
                                type="button"
                                class="dropdown-item d-flex align-items-center"
                                data-bs-theme-value="dark"
                                aria-pressed="false">
                                <i class="bi bi-moon-fill me-2"></i>
                                Dark
                                <i class="bi bi-check-lg ms-auto d-none"></i>
                            </button>
                        </li>
                        <li>
                            <button
                                type="button"
                                class="dropdown-item d-flex align-items-center"
                                data-bs-theme-value="auto"
                                aria-pressed="true">
                                <i class="bi bi-circle-fill-half-stroke me-2"></i>
                                Auto
                                <i class="bi bi-check-lg ms-auto d-none"></i>
                            </button>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Sidebar Statis (Hanya tampil di Desktop) -->
    <nav id="sidebar" class="sidebar">
        <div class="sidebar-header">
            <a href="<?= base_url('/'); ?>" class="navbar-brand">
                <i class="fas fa-bottle-water me-2"></i><span>AMDK</span>
            </a>
        </div>
        <ul class="nav flex-column mt-3">
            <li class="nav-item">
                <a class="nav-link <?= $currentUrl === base_url($role) ? 'active' : '' ?>" href="<?= base_url($role) ?>">
                    <i class="fas fa-tachometer-alt"></i> <span>Dashboard</span>
                </a>
            </li>
            <?php if ($role == 'pabrik') : ?>
                <li class="nav-item">
                    <a class="nav-link <?= strpos($currentUrl, 'pabrik/users') !== false ? 'active' : '' ?>" href="<?= base_url('pabrik/users'); ?>"><i class="fas fa-users"></i> <span>Manajemen Distributor</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= strpos($currentUrl, 'pabrik/products') !== false ? 'active' : '' ?>" href="<?= base_url('pabrik/products') ?>"><i class="fa-solid fa-boxes-stacked"></i> <span>Manajemen Produk</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= strpos($currentUrl, 'pabrik/monitoring_distributor') !== false ? 'active' : '' ?>" href="<?= base_url('pabrik/monitoring_distributor') ?>"><i class="fas fa-clock"></i> <span>Monitoring Distributor</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= strpos($currentUrl, 'pabrik/daftar_harga') !== false ? 'active' : '' ?>" href="<?= base_url('pabrik/daftar_harga') ?>"><i class="fas fa-money-bill"></i> <span>Daftar Harga</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= strpos($currentUrl, 'pabrik/incoming-orders') !== false ? 'active' : '' ?>" href="<?= base_url('pabrik/incoming-orders') ?>"><i class="fa-solid fa-basket-shopping"></i> <span>Order Masuk</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= strpos($currentUrl, 'pabrik/shipments/history') !== false ? 'active' : '' ?>" href="<?= base_url('pabrik/shipments/history') ?>"><i class="fa-solid fa-truck-arrow-right"></i> <span>Manajemen Pengiriman</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= strpos($currentUrl, 'pabrik/riwayat_pengiriman') !== false ? 'active' : '' ?>" href="<?= base_url('pabrik/riwayat_pengiriman') ?>"><i class="fas fa-truck"></i> <span>Riwayat Pengiriman</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= strpos($currentUrl, 'pabrik/orders/history') !== false ? 'active' : '' ?>" href="<?= base_url('pabrik/orders/history') ?>"><i class="fas fa-clipboard"></i> <span>History Order</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= strpos($currentUrl, 'pabrik/distributor-invoices') !== false ? 'active' : '' ?>" href="<?= base_url('pabrik/distributor-invoices') ?>"><i class="fa-solid fa-file-invoice-dollar"></i> <span>Tagihan Distributor</span></a>
                </li>
            <?php elseif ($role == 'distributor') : ?>
                <li class="nav-item">
                    <a class="nav-link <?= strpos($currentUrl, 'distributor/agents') !== false ? 'active' : '' ?>" href="<?= base_url('distributor/agents') ?>"><i class="fas fa-users"></i> <span>Manajemen Agen</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= strpos($currentUrl, 'distributor/products-prices') !== false ? 'active' : '' ?>" href="<?= base_url('distributor/products-prices') ?>">
                        <i class="fas fa-file-invoice-dollar"></i> <span>Daftar Harga</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= strpos($currentUrl, 'distributor/orders/incoming') !== false ? 'active' : '' ?>" href="<?= base_url('distributor/orders/incoming') ?>"><i class="fa-solid fa-basket-shopping"></i> <span>Order Masuk</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= strpos($currentUrl, 'distributor/orders/history-to-pabrik') !== false ? 'active' : '' ?>" href="<?= base_url('distributor/orders/history-to-pabrik') ?>"><i class="fa-solid fa-check-to-slot"></i> <span>Order Valid Pabrik</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= strpos($currentUrl, 'distributor/orders/history') !== false ? 'active' : '' ?>" href="<?= base_url('distributor/orders/history') ?>"><i class="fa-regular fa-clipboard"></i> <span>Riwayat Order</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= strpos($currentUrl, 'distributor/invoices/') !== false ? 'active' : '' ?>" href="<?= base_url('distributor/invoices'); ?>"><i class="fas fa-hand-holding-dollar"></i> <span>Tagihan Agen</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= strpos($currentUrl, 'distributor/invoicesme/') !== false ? 'active' : '' ?>" href="<?= base_url('distributor/invoicesme'); ?>"><i class="fa-solid fa-file-invoice-dollar"></i> <span>Tagihan Saya</span></a>
                </li>
            <?php elseif ($role == 'agen') : ?>
                <li class="nav-item">
                    <a class="nav-link <?= strpos($currentUrl, 'agen/orders/create') !== false ? 'active' : '' ?>" href="<?= base_url('agen/orders/create') ?>"><i class="fas fa-shopping-cart"></i> <span>Buat Order</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= strpos($currentUrl, 'agen/orders/history') !== false ? 'active' : '' ?>" href="<?= base_url('agen/orders/history') ?>"><i class="fas fa-history"></i> <span>Riwayat Order</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= strpos($currentUrl, 'agen/invoices') !== false ? 'active' : '' ?>" href="<?= base_url('agen/invoices') ?>"><i class="fas fa-receipt"></i> <span>Tagihan</span></a>
                </li>
            <?php endif; ?>
            <li class="nav-item">
                <a class="nav-link" href="<?= base_url('logout') ?>"><i class="fas fa-sign-out-alt"></i> <span>Keluar</span></a>
            </li>
        </ul>
    </nav>

    <!-- Sidebar Offcanvas (Hanya tampil di Mobile) -->
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
                    <a class="nav-link" href="<?= base_url('logout') ?>"><i class="fas fa-sign-out-alt"></i> Keluar</a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Konten Utama Aplikasi -->
    <main class="main-content">
        <div class="content-area">
            <?= $this->renderSection('content') ?>
        </div>
    </main>
    <?= $this->renderSection('modals') ?>

    <script src="<?= base_url('/assets/js/bootstrap.bundle.min.js') ?>"></script>
    <script src="<?= base_url('/assets/js/sweetalert2.all.min.js') ?>"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const body = document.body;
            const mainSidebarToggle = document.getElementById('mainSidebarToggle');
            const sidebarOffcanvasElement = document.getElementById('sidebarOffcanvas');
            const sidebarOffcanvas = new bootstrap.Offcanvas(sidebarOffcanvasElement);
            const fullscreenToggle = document.getElementById('fullscreenToggle');
            const fullscreenIcon = fullscreenToggle.querySelector('i');

            // -------------------------------------
            // LOGIC FOR ACTIVE MENU HIGHLIGHTING
            // -------------------------------------
            // (Tidak ada perubahan di bagian ini, tetap sama)
            const currentPathname = window.location.pathname;
            const navLinks = document.querySelectorAll('.sidebar .nav-link, .sidebar-offcanvas-body .nav-link');

            navLinks.forEach(link => {
                link.classList.remove('active');
                const linkPathname = new URL(link.href).pathname;

                if (currentPathname === linkPathname) {
                    link.classList.add('active');
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


            // -------------------------------------
            // SIDEBAR TOGGLE LOGIC (BAGIAN YANG DIPERBAIKI)
            // -------------------------------------
            function toggleDesktopSidebar() {
                if (body.classList.contains('sidebar-expanded')) {
                    body.classList.remove('sidebar-expanded');
                    body.classList.add('sidebar-collapse');
                    localStorage.setItem('sidebarState', 'collapsed');
                } else {
                    body.classList.add('sidebar-expanded');
                    body.classList.remove('sidebar-collapse');
                    localStorage.setItem('sidebarState', 'expanded');
                }
            }

            function handleScreenSize() {
                if (window.innerWidth >= 992) {
                    // --- LOGIKA DESKTOP ---
                    // Sembunyikan offcanvas jika terbuka saat resize ke desktop
                    sidebarOffcanvas.hide();

                    // Hapus atribut Bootstrap agar tidak konflik
                    mainSidebarToggle.removeAttribute('data-bs-toggle');
                    mainSidebarToggle.removeAttribute('data-bs-target');

                    // Tambahkan event click untuk toggle sidebar desktop
                    if (mainSidebarToggle) {
                        mainSidebarToggle.onclick = toggleDesktopSidebar;
                    }

                    // Terapkan state sidebar dari localStorage
                    const storedState = localStorage.getItem('sidebarState');
                    if (storedState === 'collapsed') {
                        body.classList.add('sidebar-mini', 'sidebar-collapse');
                        body.classList.remove('sidebar-expanded');
                    } else {
                        body.classList.add('sidebar-mini', 'sidebar-expanded');
                        body.classList.remove('sidebar-collapse');
                    }
                    body.classList.remove('offcanvas-active');
                } else {
                    // --- LOGIKA MOBILE ---
                    body.classList.remove('sidebar-mini', 'sidebar-collapse', 'sidebar-expanded');

                    // Kembalikan atribut Bootstrap agar offcanvas berfungsi
                    mainSidebarToggle.setAttribute('data-bs-toggle', 'offcanvas');
                    mainSidebarToggle.setAttribute('data-bs-target', '#sidebarOffcanvas');

                    // **PERBAIKAN KUNCI:** Hapus event handler .onclick manual
                    // Biarkan Bootstrap yang meng-handle klik melalui atribut data-bs-toggle
                    if (mainSidebarToggle) {
                        mainSidebarToggle.onclick = null;
                    }
                }
            }

            // Event listener untuk menambah/menghapus class saat offcanvas aktif
            sidebarOffcanvasElement.addEventListener('show.bs.offcanvas', function() {
                body.classList.add('offcanvas-active');
            });
            sidebarOffcanvasElement.addEventListener('hide.bs.offcanvas', function() {
                body.classList.remove('offcanvas-active');
            });

            // -------------------------------------
            // FULLSCREEN TOGGLE LOGIC
            // -------------------------------------
            // (Tidak ada perubahan di bagian ini, tetap sama)
            function toggleFullscreen() {
                if (!document.fullscreenElement) {
                    document.documentElement.requestFullscreen().catch(err => {
                        console.error(`Error attempting to enable full-screen mode: ${err.message} (${err.name})`);
                    });
                } else {
                    document.exitFullscreen();
                }
            }

            function updateFullscreenIcon() {
                if (document.fullscreenElement) {
                    fullscreenIcon.classList.remove('fa-expand');
                    fullscreenIcon.classList.add('fa-compress');
                } else {
                    fullscreenIcon.classList.remove('fa-compress');
                    fullscreenIcon.classList.add('fa-expand');
                }
            }

            if (fullscreenToggle) {
                fullscreenToggle.addEventListener('click', toggleFullscreen);
            }

            document.addEventListener('fullscreenchange', updateFullscreenIcon);
            document.addEventListener('webkitfullscreenchange', updateFullscreenIcon);
            document.addEventListener('mozfullscreenchange', updateFullscreenIcon);
            document.addEventListener('MSFullscreenChange', updateFullscreenIcon);


            // Panggil fungsi saat pertama kali load dan saat resize
            handleScreenSize();
            updateFullscreenIcon();

            window.addEventListener('resize', handleScreenSize);
        });
    </script>

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
            const form = button.closest('.form-delete');
            Swal.fire({
                title: "Apa kamu yakin?",
                text: "Kamu yakin ingin menghapus data ini!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ya, hapus saja!"
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }
    </script>

    <!-- Script dark mode  -->
    <script>
        (() => {
            "use strict";

            const storedTheme = localStorage.getItem("theme");

            const getPreferredTheme = () => {
                if (storedTheme) {
                    return storedTheme;
                }

                return window.matchMedia("(prefers-color-scheme: dark)").matches ?
                    "dark" :
                    "light";
            };

            const setTheme = function(theme) {
                if (
                    theme === "auto" &&
                    window.matchMedia("(prefers-color-scheme: dark)").matches
                ) {
                    document.documentElement.setAttribute("data-bs-theme", "dark");
                } else {
                    document.documentElement.setAttribute("data-bs-theme", theme);
                }
            };

            setTheme(getPreferredTheme());

            const showActiveTheme = (theme, focus = false) => {
                const themeSwitcher = document.querySelector("#bd-theme");

                if (!themeSwitcher) {
                    return;
                }

                const themeSwitcherText = document.querySelector("#bd-theme-text");
                const activeThemeIcon = document.querySelector(".theme-icon-active i");
                const btnToActive = document.querySelector(
                    `[data-bs-theme-value="${theme}"]`
                );
                const svgOfActiveBtn = btnToActive.querySelector("i").getAttribute("class");

                for (const element of document.querySelectorAll("[data-bs-theme-value]")) {
                    element.classList.remove("active");
                    element.setAttribute("aria-pressed", "false");
                }

                btnToActive.classList.add("active");
                btnToActive.setAttribute("aria-pressed", "true");
                activeThemeIcon.setAttribute("class", svgOfActiveBtn);
                const themeSwitcherLabel = `${themeSwitcherText.textContent} (${btnToActive.dataset.bsThemeValue})`;
                themeSwitcher.setAttribute("aria-label", themeSwitcherLabel);

                if (focus) {
                    themeSwitcher.focus();
                }
            };

            window
                .matchMedia("(prefers-color-scheme: dark)")
                .addEventListener("change", () => {
                    if (storedTheme !== "light" || storedTheme !== "dark") {
                        setTheme(getPreferredTheme());
                    }
                });

            window.addEventListener("DOMContentLoaded", () => {
                showActiveTheme(getPreferredTheme());

                for (const toggle of document.querySelectorAll("[data-bs-theme-value]")) {
                    toggle.addEventListener("click", () => {
                        const theme = toggle.getAttribute("data-bs-theme-value");
                        localStorage.setItem("theme", theme);
                        setTheme(theme);
                        showActiveTheme(theme, true);
                    });
                }
            });
        })();
    </script>

    <?= $this->renderSection('scripts') ?>
</body>

</html>