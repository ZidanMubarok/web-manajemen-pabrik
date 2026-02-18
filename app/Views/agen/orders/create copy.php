<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <h1 class="mb-4"><?= $title ?></h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-primary text-white">
            <h6 class="m-0 font-weight-bold"><i class="fas fa-shopping-cart me-2"></i> Ajukan Order Baru ke Distributor Anda</h6>
        </div>
        <div class="card-body">
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
            <?php if (session()->getFlashdata('errors')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        <?php foreach (session()->getFlashdata('errors') as $error): ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (empty($availableProducts)): ?>
                <div class="alert alert-warning text-center" role="alert">
                    Belum ada produk yang tersedia dari distributor Anda. Silakan hubungi distributor.
                </div>
            <?php else: ?>
                <form action="<?= base_url('agen/orders/store') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="productsTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Produk</th>
                                    <th>Deskripsi</th>
                                    <th>Harga per Unit (Rp)</th>
                                    <th>Kuantitas</th>
                                    <th>Sub Total (Rp)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1;
                                foreach ($availableProducts as $product): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= esc($product['product_name']) ?></td>
                                        <td><?= esc(substr($product['description'] ?? '', 0, 100)) ?><?= (strlen($product['description'] ?? '') > 100) ? '...' : '' ?></td>
                                        <td>Rp <span class="unit-price" data-price="<?= esc($product['custom_price']) ?>"><?= esc(number_format($product['custom_price'], 0, ',', '.')) ?></span></td>
                                        <td>
                                            <input type="number" name="product_quantities[<?= esc($product['product_id']) ?>]"
                                                class="form-control quantity-input" min="0" value="0"
                                                data-product-id="<?= esc($product['product_id']) ?>"
                                                style="width: 80px;">
                                        </td>
                                        <td>Rp <span class="sub-total" id="sub_total_<?= esc($product['product_id']) ?>">0</span></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="5" class="text-end">Total Seluruh Order:</th>
                                    <th>Rp <span id="grand_total">0</span></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <button type="submit" class="btn btn-primary mt-3"><i class="fas fa-paper-plane me-1"></i> Ajukan Order</button>
                    <a href="<?= base_url('agen') ?>" class="btn btn-secondary mt-3 ms-2"><i class="fas fa-times me-1"></i> Batal</a>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const quantityInputs = document.querySelectorAll('.quantity-input');
        const grandTotalSpan = document.getElementById('grand_total');

        function calculateTotals() {
            let grandTotal = 0;
            quantityInputs.forEach(input => {
                const productId = input.dataset.productId;
                const quantity = parseInt(input.value);
                const unitPriceElement = input.closest('tr').querySelector('.unit-price');
                const unitPrice = parseFloat(unitPriceElement.dataset.price); // Ambil dari data-price

                let subTotal = 0;
                if (!isNaN(quantity) && quantity > 0) {
                    subTotal = quantity * unitPrice;
                }

                document.getElementById(`sub_total_${productId}`).textContent = new Intl.NumberFormat('id-ID').format(subTotal);
                grandTotal += subTotal;
            });
            grandTotalSpan.textContent = new Intl.NumberFormat('id-ID').format(grandTotal);
        }

        quantityInputs.forEach(input => {
            input.addEventListener('input', calculateTotals);
            // Inisialisasi pada saat load, jaga-jaga jika ada nilai awal dari old input
            input.dispatchEvent(new Event('input'));
        });

        calculateTotals(); // Hitung total saat halaman pertama kali dimuat
    });
</script>
<?= $this->endSection() ?>