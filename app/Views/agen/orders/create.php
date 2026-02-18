<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    /* CSS Kustom untuk input kuantitas */
    .quantity-selector {
        display: flex;
        align-items: center;
        max-width: 150px;
    }

    .quantity-selector .btn {
        min-width: 38px;
    }

    .quantity-selector .form-control {
        text-align: center;
        border-left: 0;
        border-right: 0;
        border-radius: 0;
    }

    .quantity-selector .form-control:focus {
        box-shadow: none;
        z-index: 1;
    }

    /* Responsif untuk mobile */
    @media (max-width: 767.98px) {
        .product-item .col-md-4 {
            margin-top: 0.75rem;
        }

        .product-item .col-md-3 {
            margin-top: 0.5rem;
            text-align: left !important;
        }
    }
</style>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800"><?= $title ?></h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-primary">
            <h6 class="m-0 font-weight-bold text-white"><i class="fas fa-shopping-cart me-2"></i> Ajukan Order Baru ke Distributor Anda</h6>
        </div>
        <div class="card-body">
            <!-- Notifikasi (Flashdata) -->
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= session()->getFlashdata('success') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            <!-- ... (notifikasi error lainnya tetap sama) ... -->

            <?php if (empty($availableProducts)): ?>
                <div class="alert alert-warning text-center" role="alert">
                    Belum ada produk yang tersedia dari distributor Anda.
                </div>
            <?php else: ?>
                <form action="<?= base_url('agen/orders/store') ?>" method="post">
                    <?= csrf_field() ?>

                    <!-- [SOLUSI 1] Kolom Pencarian Produk -->
                    <div class="mb-4">
                        <label for="productSearch" class="form-label fw-bold">Cari Produk</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fas fa-search"></i></span>
                            <input type="search" id="productSearch" class="form-control" placeholder="Ketik nama produk untuk menyaring daftar...">
                        </div>
                    </div>

                    <!-- Daftar Produk Berbasis Card -->
                    <div class="product-list">
                        <?php foreach ($availableProducts as $product): ?>
                            <div class="card product-item mb-3 border">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <!-- Info Produk -->
                                        <div class="col-md-5 col-lg-6">
                                            <h5 class="fw-bold mb-1 product-name"><?= esc($product['product_name']) ?></h5>
                                            <p class="text-muted small mb-2 d-none d-md-block">
                                                <?= esc(substr($product['description'] ?? '', 0, 100)) ?><?= (strlen($product['description'] ?? '') > 100) ? '...' : '' ?>
                                            </p>
                                            <p class="mb-0"><strong>Harga:</strong> <span class="text-primary fw-bold">Rp <span class="unit-price" data-price="<?= esc($product['custom_price']) ?>"><?= esc(number_format($product['custom_price'], 0, ',', '.')) ?></span></span></p>
                                        </div>
                                        <!-- Input Kuantitas -->
                                        <div class="col-md-4 col-lg-3">
                                            <label class="form-label d-block mb-1 small">Kuantitas:</label>
                                            <div class="quantity-selector">
                                                <button type="button" class="btn btn-outline-secondary btn-sm quantity-minus">-</button>
                                                <input type="number" name="product_quantities[<?= esc($product['product_id']) ?>]" class="form-control form-control-sm quantity-input" min="0" value="0" data-product-id="<?= esc($product['product_id']) ?>">
                                                <button type="button" class="btn btn-outline-secondary btn-sm quantity-plus">+</button>
                                            </div>
                                        </div>
                                        <!-- Subtotal -->
                                        <div class="col-md-3 col-lg-3 text-md-end">
                                            <label class="form-label d-block d-md-none mb-1 small">Subtotal:</label>
                                            <h5 class="mb-0 fw-normal">Rp <span class="sub-total" id="sub_total_<?= esc($product['product_id']) ?>">0</span></h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- [SOLUSI 1] Pesan jika pencarian tidak ditemukan -->
                    <div id="noResultsMessage" class="alert alert-warning text-center" style="display: none;">
                        Produk yang Anda cari tidak ditemukan.
                    </div>

                    <div class="mt-4 d-flex justify-content-between">
                        <!-- Pager atau Pagination -->
                        <div>
                            <?php
                            $totalItems = $pager->getTotal();
                            $firstItem = ($totalItems > 0) ? (($pager->getCurrentPage() - 1) * $pager->getPerPage()) + 1 : 0;
                            $lastItem = ($totalItems > 0) ? min($firstItem + $pager->getPerPage() - 1, $totalItems) : 0;
                            ?>
                            Menampilkan <?= $firstItem ?> sampai <?= $lastItem ?> dari <?= $totalItems ?> Produk
                        </div>
                        <div>
                            <!-- Pager atau Pagination -->
                            <?= $pager->links('default', 'bootstrap_pagination') ?>
                        </div>
                    </div>
                    <!-- Total & Tombol Aksi -->
                    <hr>
                    <div class="d-flex justify-content-end align-items-center mt-4 flex-wrap">
                        <div class="text-end me-4 mb-2 mb-md-0">
                            <h5 class="mb-0">Total Seluruh Order:</h5>
                            <h4 class="fw-bold text-success">Rp <span id="grand_total">0</span></h4>
                        </div>
                        <div>
                            <a href="<?= base_url('agen') ?>" class="btn btn-secondary"><i class="fas fa-times me-1"></i> Batal</a>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane me-1"></i> Ajukan Order</button>
                        </div>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- JavaScript dengan logika Pencarian dan Kalkulasi -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Elemen-elemen yang dibutuhkan
        const searchInput = document.getElementById('productSearch');
        const productItems = document.querySelectorAll('.product-item');
        const noResultsMessage = document.getElementById('noResultsMessage');
        const grandTotalSpan = document.getElementById('grand_total');

        /**
         * FUNGSI UNTUK MENYARING PRODUK
         */
        function filterProducts() {
            const searchTerm = searchInput.value.toLowerCase().trim();
            let productsFound = 0;

            productItems.forEach(item => {
                const productName = item.querySelector('.product-name').textContent.toLowerCase();

                if (productName.includes(searchTerm)) {
                    item.style.display = 'block'; // Tampilkan card jika nama produk cocok
                    productsFound++;
                } else {
                    item.style.display = 'none'; // Sembunyikan jika tidak cocok
                }
            });

            // Tampilkan atau sembunyikan pesan "tidak ditemukan"
            noResultsMessage.style.display = productsFound === 0 ? 'block' : 'none';
        }

        /**
         * FUNGSI UNTUK MENGHITUNG TOTAL
         * (Tidak ada perubahan pada fungsi ini)
         */
        function calculateTotals() {
            let grandTotal = 0;
            productItems.forEach(item => {
                const input = item.querySelector('.quantity-input');
                const quantity = parseInt(input.value) || 0;
                const unitPrice = parseFloat(item.querySelector('.unit-price').dataset.price);
                const subTotal = quantity * unitPrice;
                const productId = input.dataset.productId;

                document.getElementById(`sub_total_${productId}`).textContent = new Intl.NumberFormat('id-ID').format(subTotal);
                grandTotal += subTotal;
            });
            grandTotalSpan.textContent = new Intl.NumberFormat('id-ID').format(grandTotal);
        }

        // --- EVENT LISTENERS ---

        // 1. Event listener untuk kolom pencarian
        searchInput.addEventListener('keyup', filterProducts);
        searchInput.addEventListener('search', filterProducts); // Untuk menangani saat pengguna menekan tombol 'x' di kolom search

        // 2. Event listener untuk input kuantitas (tombol +/-, dan ketik manual)
        productItems.forEach(item => {
            const input = item.querySelector('.quantity-input');
            const minusBtn = item.querySelector('.quantity-minus');
            const plusBtn = item.querySelector('.quantity-plus');

            input.addEventListener('input', calculateTotals);
            minusBtn.addEventListener('click', () => {
                let currentValue = parseInt(input.value) || 0;
                if (currentValue > 0) {
                    input.value = currentValue - 1;
                    input.dispatchEvent(new Event('input', {
                        bubbles: true
                    }));
                }
            });
            plusBtn.addEventListener('click', () => {
                let currentValue = parseInt(input.value) || 0;
                input.value = currentValue + 1;
                input.dispatchEvent(new Event('input', {
                    bubbles: true
                }));
            });
        });

        // Hitung total saat halaman pertama kali dimuat
        calculateTotals();
    });
</script>
<?= $this->endSection() ?>