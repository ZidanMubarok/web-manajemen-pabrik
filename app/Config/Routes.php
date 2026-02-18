<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// Route untuk menampilkan halaman form update status pengiriman
$routes->get('courier/update-status', 'CourierController::showUpdateForm');
$routes->post('courier/process-update-status', 'CourierController::updateDeliveryStatus');
$routes->get('courier/info', 'CourierController::showInfoPage'); // ROUTE BARU

$routes->get('/', 'Home::index');
$routes->get('/login', 'AuthController::login');
$routes->post('login/process', 'AuthController::processLogin');
$routes->get('/logout', 'AuthController::logout');

// Rute untuk Profil Pengguna (bisa diakses semua role yang login)
$routes->get('profile', 'ProfileController::index');
$routes->post('profile/update', 'ProfileController::update');

// Grup Route untuk Pabrik
$routes->group('pabrik', ['filter' => ['auth', 'role:pabrik']], function ($routes) {
    $routes->get('/', 'PabrikController::index'); // Dashboard utama pabrik
    $routes->get('get-sales-data', 'PabrikController::getSalesData');
    // Rute untuk Manajemen Pengguna (CRUD semua user)
    $routes->get('users', 'UserController::index');
    $routes->get('users/create', 'UserController::create');
    $routes->post('users/store', 'UserController::store');
    $routes->get('users/edit/(:num)', 'UserController::edit/$1');
    $routes->post('users/update/(:num)', 'UserController::update/$1');
    $routes->get('users/delete/(:num)', 'UserController::delete/$1');

    // Rute untuk Produk (CRUDS) - Asumsi ProductController terpisah
    $routes->get('products', 'ProdukController::index');
    $routes->get('products/create', 'ProdukController::create');
    $routes->post('products/store', 'ProdukController::store');
    $routes->get('products/edit/(:num)', 'ProdukController::edit/$1');
    $routes->post('products/update/(:num)', 'ProdukController::update/$1');
    $routes->get('products/delete/(:num)', 'ProdukController::delete/$1');

    // Rute BARU untuk Daftar Order Masuk Pabrik
    $routes->get('incoming-orders', 'PabrikOrderController::index');
    $routes->get('incoming-orders/detail/(:num)', 'PabrikOrderController::detail/$1');
    $routes->post('incoming-orders/update-status/(:num)', 'PabrikOrderController::updateStatus/$1');
    // Riwayat Order Pabrik
    $routes->get('orders/history', 'PabrikController::orderHistory');
    $routes->get('orders/export', 'PabrikController::exportHistory');
    // Order Masuk dari Distributor
    $routes->get('orders/detail_from_distributor/(:num)', 'PabrikController::detailOrderFromDistributor/$1');
    $routes->post('orders/update-status-from-distributor/(:num)', 'PabrikController::updateOrderStatusFromDistributor/$1');
    // Rute untuk Monitoring Distributor
    $routes->get('monitoring_distributor', 'PabrikController::monitoringDistributor');
    // Rute untuk Riwayat Pengiriman
    $routes->get('riwayat_pengiriman', 'PabrikShipmentController::index'); // Mengarahkan ke controller baru
    $routes->get('riwayat_pengiriman/export', 'PabrikShipmentController::exportShipmentHistory');
    $routes->get('riwayat_pengiriman/detail/(:num)', 'PabrikShipmentController::detail/$1');
    // $routes->post('riwayat_pengiriman/update_status/(:num)', 'PabrikShipmentController::updateDeliveryStatus/$1');

    // $routes->get('shipments/create/(:num)', 'PabrikController::createShipment/$1');
    // $routes->post('shipments/store', 'PabrikController::storeShipment');
    $routes->get('shipments/history', 'PabrikController::shipmentHistory');
    $routes->get('shipments/detail/(:num)', 'PabrikController::detailShipment/$1');
    $routes->post('shipments/update-status/(:num)', 'PabrikController::updateShipmentStatus/$1');
    $routes->post('shipments/bulk-print', 'PabrikController::bulkPrintShipments');

    // Route untuk tagihan distributor
    $routes->get('distributor-invoices', 'PabrikInvoiceController::index');
    $routes->get('distributor-invoices/export', 'PabrikInvoiceController::exportInvoices');
    $routes->post('distributor-invoices/update-status/(:num)', 'PabrikInvoiceController::updateStatus/$1'); // Menggunakan POST dengan _method=PUT/PATCH
    $routes->get('distributor-invoices/detail/(:num)', 'PabrikInvoiceController::detail/$1');
    // Rute untuk Daftar Harga (TERMASUK FILTER)
    $routes->get('daftar_harga', 'PabrikController::daftarHarga'); // Ini akan menangani parameter query string
});
// Grup Route untuk Dashboard Distributor
// Filter: 'auth' (harus login), 'role:distributor' (harus user dengan role 'distributor')
$routes->group('distributor', ['filter' => ['auth', 'role:distributor']], function ($routes) {
    $routes->get('/', 'DistributorController::index'); // Dashboard utama distributor
    // Routes untuk kelola agen CRUD
    $routes->get('agents', 'AgentController::list_agen');
    $routes->get('agents/create', 'AgentController::create');
    $routes->post('agents/store', 'AgentController::store');
    $routes->get('agents/edit/(:num)', 'AgentController::edit/$1');
    $routes->post('agents/update/(:num)', 'AgentController::update/$1');
    $routes->get('agents/delete/(:num)', 'AgentController::delete/$1');
    // Rute untuk Manajemen Harga Produk (Produk Pabrik untuk Distributor)
    $routes->get('products-prices', 'DistributorProductController::index');
    $routes->get('products-prices/set/(:num)', 'DistributorProductController::setPrice/$1');
    $routes->post('products-prices/save', 'DistributorProductController::savePrice');
    $routes->get('products-prices/delete/(:num)', 'DistributorProductController::deletePrice/$1');
    // Rute untuk Daftar Order Masuk dari Agen
    $routes->get('orders/incoming', 'DistributorOrderController::incomingOrders');
    $routes->get('orders/detail/(:num)', 'DistributorOrderController::detail/$1');
    $routes->post('orders/update-status/(:num)', 'DistributorOrderController::updateStatus/$1');
    // Rute untuk Pengajuan Order ke Pabrik
    // $routes->get('orders/create-to-pabrik', 'DistributorOrderController::createOrderToPabrik');
    // $routes->post('orders/store-to-pabrik', 'DistributorOrderController::storeOrderToPabrik');
    $routes->get('orders/history-to-pabrik', 'DistributorOrderController::historyOrderToPabrik');
    $routes->get('orders/detail-to-pabrik/(:num)', 'DistributorOrderController::detailOrderToPabrik/$1');
    // Rute untuk Riwayat Order Distributor
    $routes->get('orders/history', 'DistributorOrderController::history');
    $routes->get('orders/history_detail/(:num)', 'DistributorOrderController::history_detail/$1');
    // Invoices agent
    $routes->get('invoices', 'DistributorController::invoicesAgen');
    $routes->get('invoices/detail/(:num)', 'DistributorController::invoiceAgenDetail/$1');
    $routes->post('invoices/markAsPaid/(:num)', 'DistributorController::markAsPaid/$1');
    // Invoices me
    $routes->get('invoicesme', 'DistributorInvoiceController::index');
    $routes->get('invoicesme/detail/(:num)', 'DistributorInvoiceController::detail/$1');
});

// Grup Route untuk Dashboard Agen
// Filter: 'auth' (harus login), 'role:agen' (harus user dengan role 'agen')
$routes->group('agen', ['filter' => ['auth', 'role:agen']], function ($routes) {
    $routes->get('/', 'AgentController::index'); // Dashboard utama agen
    // Rute untuk Pengajuan Order Agen
    $routes->get('orders/create', 'AgenOrderController::create');
    $routes->post('orders/store', 'AgenOrderController::store');
    // Rute untuk Riwayat Order Agen
    $routes->get('orders/history', 'AgenOrderController::history');
    $routes->get('orders/detail/(:num)', 'AgenOrderController::detail/$1');
    // --- Rute untuk Tagihan Agen ---
    $routes->get('invoices', 'AgentController::invoices');
    $routes->get('invoices/detail/(:num)', 'AgentController::invoiceDetail/$1');
});
// Routes error
$routes->get('/error', function () {
    return view('error');
});
