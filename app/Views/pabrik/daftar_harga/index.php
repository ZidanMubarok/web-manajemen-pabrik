<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('title') ?><?= $title ?><?= $this->endSection() ?>

<?= $this->section('head') ?>
<style>
    /* Style untuk modal yang lebih estetik */
    #priceDetailModal .modal-header {
        background-color: #0A1C47;
        color: #fff;
    }

    #priceDetailModal .modal-title {
        font-weight: bold;
    }

    #priceDetailModal .summary-box {
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: .375rem;
        padding: 1rem;
        margin-bottom: 1.5rem;
    }

    #priceDetailModal .summary-box strong {
        color: #0A1C47;
    }

    #priceDetailModal .price-card {
        border: 1px solid #e9ecef;
        border-radius: .375rem;
        transition: box-shadow .2s;
        background-color: #fff;
    }

    #priceDetailModal .price-card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, .08);
    }

    #priceDetailModal .price-card-header {
        background-color: #f8f9fa;
        font-weight: bold;
        padding: .75rem 1rem;
        border-bottom: 1px solid #e9ecef;
    }

    #priceDetailModal .price-card-body {
        padding: 1rem;
    }

    #priceDetailModal .price-card-body p {
        margin-bottom: .5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    #priceDetailModal .markup-profit {
        font-weight: bold;
        color: #198754;
    }

    #priceDetailModal .markup-loss {
        font-weight: bold;
        color: #dc3545;
    }

    #priceDetailModal .not-set {
        opacity: 0.7;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <h1 class="mb-4"><?= $title ?></h1>
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-primary text-white">
            <h6 class="m-0 font-weight-bold"><i class="fas fa-tags me-2"></i> Daftar Harga Produk & Harga Distributor</h6>
        </div>
        <div class="card-body">
            <!-- Filter, Notifikasi, Tabel, Pagination (SEMUA TIDAK BERUBAH) -->
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= session()->getFlashdata('success') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= session()->getFlashdata('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="mb-4 p-3 border rounded bg-light">
                <h5 class="mb-3">Filter & Pencarian:</h5>
                <form action="<?= base_url('pabrik/daftar_harga') ?>" method="get">
                    <div class="row g-3 align-items-center mb-3">
                        <div class="col-md-4">
                            <label for="distributor_select" class="form-label visually-hidden">Pilih Distributor:</label>
                            <select class="form-select" id="distributor_select" name="distributor_id">
                                <option value="">-- Semua Distributor --</option>
                                <?php foreach ($distributors as $distributor): ?>
                                    <option value="<?= esc($distributor['id']) ?>"
                                        <?= ($selectedDistributorId == $distributor['id']) ? 'selected' : '' ?>>
                                        <?= esc($distributor['username']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label for="search_product" class="form-label visually-hidden">Cari Produk:</label>
                            <input type="text" name="search" id="search_product" class="form-control" placeholder="Cari nama atau deskripsi produk" value="<?= esc($search ?? '') ?>">
                        </div>
                        <div class="col-md-3 d-flex">
                            <button type="submit" class="btn btn-info me-2"><i class="fas fa-filter me-1"></i> Terapkan</button>
                            <?php if ($selectedDistributorId || !empty($search)): ?>
                                <a href="<?= base_url('pabrik/daftar_harga') ?>" class="btn btn-secondary"><i class="fas fa-times me-1"></i> Reset</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Produk</th>
                            <th>Deskripsi Produk</th>
                            <th>Harga Dasar Pabrik (Rp)</th>
                            <?php if ($selectedDistributorId): ?>
                                <th>Harga Jual Distributor (Rp)</th>
                            <?php else: ?>
                                <th>Harga Jual Distributor (Rata-rata/Rentang) (Rp)</th>
                            <?php endif; ?>
                            <th>Detail Harga Distributor</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($allProducts)): ?>
                            <tr>
                                <td colspan="<?= $selectedDistributorId ? '6' : '6' ?>" class="text-center">Belum ada produk dari pabrik.</td>
                            </tr>
                        <?php else: ?>
                            <?php
                            $currentPage = $pager->getCurrentPage();
                            $perPage = $pager->getPerPage();
                            $no = (($currentPage - 1) * $perPage) + 1;
                            foreach ($allProducts as $product): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= esc($product['product_name']) ?></td>
                                    <td><?= esc(substr($product['description'] ?? '', 0, 100)) ?><?= (strlen($product['description'] ?? '') > 100) ? '...' : '' ?></td>
                                    <td>Rp <?= esc(number_format($product['base_price'], 0, ',', '.')) ?></td>
                                    <?php if ($selectedDistributorId): ?>
                                        <td>
                                            <?php
                                            $customPrice = $distributorPricesMap[$selectedDistributorId][$product['id']] ?? null;
                                            if ($customPrice !== null) {
                                                echo '<span class="font-weight-bold text-success">Rp ' . number_format($customPrice, 0, ',', '.') . '</span>';
                                            } else {
                                                echo '<span class="text-danger">- Belum diatur oleh distributor ini -</span>';
                                            }
                                            ?>
                                        </td>
                                    <?php else: ?>
                                        <td>
                                            <?php
                                            $pricesForProduct = [];
                                            foreach ($distributors as $distributor) {
                                                if (isset($distributorPricesMap[$distributor['id']][$product['id']])) {
                                                    $pricesForProduct[] = $distributorPricesMap[$distributor['id']][$product['id']];
                                                }
                                            }

                                            if (!empty($pricesForProduct)) {
                                                $minPrice = min($pricesForProduct);
                                                $maxPrice = max($pricesForProduct);
                                                if ($minPrice == $maxPrice) {
                                                    echo 'Rp ' . number_format($minPrice, 0, ',', '.');
                                                } else {
                                                    echo 'Rp ' . number_format($minPrice, 0, ',', '.') . ' - Rp ' . number_format($maxPrice, 0, ',', '.');
                                                }
                                                echo '<br><small class="text-muted">(Dari ' . count($pricesForProduct) . ' distributor)</small>';
                                            } else {
                                                echo '<span class="text-danger">- Belum diatur oleh distributor manapun -</span>';
                                            }
                                            ?>
                                        </td>
                                    <?php endif; ?>
                                    <td class="text-center">
                                        <!-- Menambahkan data-product-base-price untuk digunakan di JS -->
                                        <button type="button" class="btn btn-info btn-sm"
                                            data-bs-toggle="modal"
                                            data-bs-target="#priceDetailModal"
                                            data-product-id="<?= esc($product['id']) ?>"
                                            data-product-name="<?= esc($product['product_name']) ?>"
                                            data-product-base-price="<?= esc($product['base_price']) ?>">
                                            <i class="fas fa-eye"></i> Lihat Detail
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-4">
                <div>
                    <?php
                    $totalItems = $pager->getTotal();
                    $firstItem = ($totalItems > 0) ? (($pager->getCurrentPage() - 1) * $pager->getPerPage()) + 1 : 0;
                    $lastItem = ($totalItems > 0) ? min($firstItem + $pager->getPerPage() - 1, $totalItems) : 0;
                    ?>
                    Menampilkan <?= $firstItem ?> sampai <?= $lastItem ?> dari <?= $totalItems ?> total produk
                </div>
                <div>
                    <?= $pager->links('default', 'bootstrap_pagination') ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('modals') ?>
<div class="modal fade mt-4" id="priceDetailModal" tabindex="-1" aria-labelledby="priceDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="priceDetailModalLabel">
                    <i class="fas fa-chart-line me-2"></i> Analisis Harga: <span id="modalProductName" class="fw-bold"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="summary-box">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <p class="mb-1">Harga Dasar Pabrik: <strong id="modalBasePrice"></strong></p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1">Total Distributor: <strong id="modalDistributorCount"></strong></p>
                        </div>
                    </div>
                </div>
                <div id="modalPriceDetailsContainer" class="row g-3"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var priceDetailModal = document.getElementById('priceDetailModal');
        if (priceDetailModal) {
            priceDetailModal.addEventListener('show.bs.modal', function(event) {
                // --- BAGIAN 1: Mengambil data dari tombol yang diklik ---
                var button = event.relatedTarget;
                var productId = button.getAttribute('data-product-id');
                var productName = button.getAttribute('data-product-name');
                // PERBAIKAN PENTING: data-base-price sekarang ada di tombol
                var basePrice = parseFloat(button.getAttribute('data-product-base-price'));

                // --- BAGIAN 2: Menyiapkan Elemen-elemen Modal ---
                // Mengambil elemen-elemen dari HTML modal yang baru
                var modalProductName = priceDetailModal.querySelector('#modalProductName');
                var modalBasePriceElem = priceDetailModal.querySelector('#modalBasePrice');
                var modalDistributorCountElem = priceDetailModal.querySelector('#modalDistributorCount');
                // PERBAIKAN KUNCI: Referensi ke container kartu, BUKAN body tabel
                var container = priceDetailModal.querySelector('#modalPriceDetailsContainer');

                // --- BAGIAN 3: Mengisi Modal dengan Data ---
                // Update Judul & Ringkasan
                modalProductName.textContent = productName;
                modalBasePriceElem.textContent = `Rp ${new Intl.NumberFormat('id-ID').format(basePrice)}`;
                container.innerHTML = '<div class="col-12 text-center p-5"><i class="fas fa-spinner fa-spin fa-2x"></i><p class="mt-2">Memuat data...</p></div>';
                
                // Ambil data PHP dan ubah menjadi variabel JS
                const allDistributors = <?= json_encode($distributors) ?>;
                const distributorPricesMap = <?= json_encode($distributorPricesMap) ?>;

                modalDistributorCountElem.textContent = allDistributors.length;
                container.innerHTML = ''; // Kosongkan kontainer setelah data siap

                // --- BAGIAN 4: LOGIKA UTAMA - Merender Kartu untuk setiap Distributor ---
                if (allDistributors.length > 0) {
                    allDistributors.forEach(distributor => {
                        let customPrice = distributorPricesMap[distributor.id] ? distributorPricesMap[distributor.id][productId] : null;

                        let cardHtml = '';
                        const colDiv = document.createElement('div');
                        colDiv.className = 'col-md-6 col-lg-4';

                        if (customPrice !== null) {
                            customPrice = parseFloat(customPrice);
                            const markup = customPrice - basePrice;

                            // Menentukan style & teks untuk markup
                            const markupClass = markup >= 0 ? 'markup-profit' : 'markup-loss';
                            const markupText = `${markup >= 0 ? '+' : ''}Rp ${new Intl.NumberFormat('id-ID').format(markup)}`;

                            cardHtml = `
                            <div class="price-card h-100">
                                <div class="price-card-header"><i class="fas fa-user-tie me-2"></i>${distributor.username}</div>
                                <div class="price-card-body">
                                    <p><span>Harga Jual:</span> <strong>Rp ${new Intl.NumberFormat('id-ID').format(customPrice)}</strong></p>
                                    <p><span>Markup:</span> <span class="${markupClass}">${markupText}</span></p>
                                </div>
                            </div>`;
                        } else {
                            cardHtml = `
                            <div class="price-card h-100 not-set">
                                <div class="price-card-header"><i class="fas fa-user-tie me-2"></i>${distributor.username}</div>
                                <div class="price-card-body text-center d-flex flex-column justify-content-center align-items-center">
                                    <i class="fas fa-times-circle fa-2x text-danger mb-2"></i>
                                    <p class="text-muted mb-0">Harga belum diatur</p>
                                </div>
                            </div>`;
                        }
                        colDiv.innerHTML = cardHtml;
                        container.appendChild(colDiv);
                    });
                } else {
                    container.innerHTML = `<div class="col-12"><div class="alert alert-warning">Tidak ada data distributor untuk ditampilkan.</div></div>`;
                }
            });
        }
    });
</script>
<?= $this->endSection() ?>