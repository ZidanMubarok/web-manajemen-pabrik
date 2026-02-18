<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('title') ?>
Manajemen Pengiriman (On Transit)
<?= $this->endSection() ?>

<?= $this->section('head'); ?>
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<style>
    .form-label {
        font-weight: 600;
        /* Label lebih tebal */
        color: #343a40;
        /* Warna teks gelap */
    }

    .form-control {
        border-radius: 8px;
        /* Sudut input lebih membulat */
        padding: 10px 15px;
        /* Padding input */
        border: 1px solid #ced4da;
        transition: border-color 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }

    .form-control:focus {
        border-color: #86b7fe;
        /* Warna border saat fokus */
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        /* Shadow biru saat fokus */
    }

    /* Styling untuk Tombol Cari */
    .btn-search-custom {
        display: flex;
        /* Menggunakan flexbox untuk ikon dan teks */
        align-items: center;
        justify-content: center;
        gap: 8px;
        /* Jarak antara ikon dan teks */
        font-weight: bold;
        padding: 10px 20px;
        /* Padding yang disesuaikan */
        border-radius: 8px;
        transition: all 0.3s ease;
        white-space: nowrap;
        /* Mencegah teks melipat */
        border: 1px solid #0d6efd;
        /* Border sewarna teks */
        color: #0d6efd;
        background-color: transparent;
        /* Awalnya transparan */
    }

    .btn-search-custom:hover {
        background-color: #0d6efd;
        /* Latar belakang saat hover */
        color: #fff;
        /* Warna teks saat hover */
        transform: translateY(-2px);
        /* Sedikit naik saat di-hover */
        box-shadow: 0 4px 12px rgba(13, 110, 253, 0.2);
        /* Bayangan halus saat hover */
    }

    .btn-search-custom:active {
        transform: translateY(0);
        box-shadow: none;
        background-color: #0a58ca;
        /* Warna sedikit lebih gelap saat aktif */
        border-color: #0a58ca;
    }

    .btn-search-custom i {
        font-size: 1.1em;
    }

    /* Styling untuk Tombol Reset */
    .btn-reset-custom {
        display: flex;
        /* Menggunakan flexbox untuk ikon */
        align-items: center;
        justify-content: center;
        padding: 10px;
        /* Padding lebih kecil untuk ikon saja */
        border-radius: 8px;
        transition: all 0.3s ease;
        border: 1px solid #6c757d;
        /* Border sewarna secondary */
        color: #6c757d;
        background-color: transparent;
    }

    .btn-reset-custom:hover {
        background-color: #6c757d;
        color: #fff;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(108, 117, 125, 0.2);
    }

    .btn-reset-custom:active {
        transform: translateY(0);
        box-shadow: none;
        background-color: #5c636a;
        border-color: #5c636a;
    }

    .btn-reset-custom i {
        font-size: 1.1em;
    }

    /* Responsif untuk Kolom Input dan Tombol */
    @media (max-width: 767.98px) {

        .col-md-4,
        .col-md-3,
        .col-md-2 {
            margin-bottom: 10px;
            /* Jarak antar kolom di layar kecil */
        }

        .col-md-2.d-flex {
            flex-direction: row;
            /* Tetap satu baris untuk tombol */
            justify-content: space-between;
            /* Spasi antar tombol */
            gap: 10px;
            /* Jarak antara tombol Cari dan Reset */
        }

        .btn-search-custom,
        .btn-reset-custom {
            width: 100%;
            /* Tombol memenuhi lebar di layar kecil */
            flex-grow: 1;
            /* Biarkan tombol mengambil ruang yang tersedia */
        }
    }
</style>
<?= $this->endSection(); ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <h1 class="mb-4">Manajemen Pengiriman (On Transit)</h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-primary text-white">
            <h6 class="m-0 font-weight-bold"><i class="fas fa-truck me-2"></i> Daftar Pengiriman Sedang Transit</h6>
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

            <!-- Form Filter yang Diubah -->
            <div class="mb-3">
                <form action="<?= base_url('pabrik/shipments/history') ?>" method="get" class="row g-2 align-items-center">
                    <div class="col-md-3">
                        <label for="search" class="form-label">Cari ID Pengiriman, Resi, Distributor, Agen</label>
                        <input type="text" name="search" id="search" class="form-control" placeholder="Cari..." value="<?= esc($search ?? '') ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="start_date" class="form-label">Dari Tanggal</label>
                        <input type="date" name="start_date" id="start_date" class="form-control" value="<?= esc($startDate ?? '') ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="end_date" class="form-label">Sampai Tanggal</label>
                        <input type="date" name="end_date" id="end_date" class="form-control" value="<?= esc($endDate ?? '') ?>">
                    </div>
                    <div class="col-md-3 d-flex gap-2"> <button type="submit" class="btn btn-outline-primary w-100 btn-search-custom">
                            <i class="fas fa-search"></i> Cari
                        </button>
                        <?php if (!empty($search) || !empty($startDate) || !empty($endDate)): ?>
                            <a href="<?= base_url('pabrik/shipments/history') ?>" class="btn btn-outline-secondary btn-reset-custom" title="Reset Filter">
                                <i class="fas fa-sync-alt"></i> </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- FORM UNTUK CETAK MASSAL -->
            <form action="<?= base_url('pabrik/shipments/bulk-print') ?>" method="post" id="bulkPrintForm" target="_blank">
                <?= csrf_field() ?>
                <div id="hidden-inputs-container"></div>

                <div class="mb-3">
                    <button type="submit" class="btn btn-success" id="bulkPrintButton">
                        <i class="fas fa-print me-2"></i> Cetak Dokumen Terpilih
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="selectAllShipments"></th>
                                <th>No</th>
                                <th>ID Pengiriman</th>
                                <th>Order ID</th>
                                <th>Distributor</th>
                                <th>Agen</th>
                                <th>Tanggal Kirim</th>
                                <th>Status</th>
                                <th>No. Resi</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($shipments)): ?>
                                <tr>
                                    <td colspan="10" class="text-center">Tidak ada pengiriman yang sedang dalam transit.</td>
                                </tr>
                            <?php else: ?>
                                <?php
                                $currentPage = $pager->getCurrentPage();
                                $perPage = $pager->getPerPage();
                                $no = (($currentPage - 1) * $perPage) + 1;
                                foreach ($shipments as $shipment):
                                    $order = $ordersData[$shipment['order_id']] ?? null;
                                ?>
                                    <tr>
                                        <td>
                                            <input type="checkbox" class="shipment-checkbox" value="<?= esc($shipment['id']) ?>">
                                        </td>
                                        <td><?= $no++ ?></td>
                                        <td>SHP-<?= esc($shipment['id']) ?></td>
                                        <td>ORD-<?= esc($shipment['order_id']) ?></td>
                                        <td><?= esc($distributorUsernames[$order['distributor_id']] ?? 'N/A') ?></td>
                                        <td><?= esc($agenUsernames[$order['agen_id']] ?? '-') ?></td>
                                        <td><?= date('d/m/Y', strtotime($shipment['shipping_date'])) ?></td>
                                        <td>
                                            <span class="badge bg-warning text-dark">Sedang Transit</span>
                                        </td>
                                        <td><?= esc($shipment['tracking_number'] ?? '-') ?></td>
                                        <td>
                                            <a href="<?= base_url('pabrik/shipments/detail/' . $shipment['id']) ?>" class="btn btn-info btn-sm me-1" title="Lihat Detail"><i class="fas fa-eye"></i></a>
                                            <button type="button" class="btn btn-success btn-sm me-1" onclick="updateShipmentStatus(<?= $shipment['id'] ?>, 'delivered')" title="Tandai Sudah Diterima"><i class="fas fa-check"></i></button>
                                            <button type="button" class="btn btn-danger btn-sm" onclick="updateShipmentStatus(<?= $shipment['id'] ?>, 'failed')" title="Tandai Gagal"><i class="fas fa-times"></i></button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </form>

            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div>
                    <?php
                    $totalItems = $pager->getTotal();
                    $firstItem = ($totalItems > 0) ? (($pager->getCurrentPage() - 1) * $pager->getPerPage()) + 1 : 0;
                    $lastItem = ($totalItems > 0) ? min($firstItem + $pager->getPerPage() - 1, $totalItems) : 0;
                    ?>
                    Menampilkan <?= $firstItem ?> sampai <?= $lastItem ?> dari <?= $totalItems ?> total pengiriman
                </div>
                <div>
                    <?= $pager->links('default', 'bootstrap_pagination') ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Form untuk update status (tidak berubah) -->
<form id="shipmentStatusUpdateForm" action="" method="post" style="display: none;">
    <?= csrf_field() ?>
    <input type="hidden" name="status" id="newShipmentStatusInput">
</form>

<script>
    // Fungsi untuk update status pengiriman (tidak berubah)
    function updateShipmentStatus(shipmentId, newStatus) {
        let titleText = '';
        let confirmButtonText = '';
        let iconType = 'question';

        if (newStatus === 'delivered') {
            titleText = 'Konfirmasi Penerimaan Pengiriman';
            confirmButtonText = 'Ya, Diterima';
            iconType = 'success';
        } else if (newStatus === 'failed') {
            titleText = 'Konfirmasi Kegagalan Pengiriman';
            confirmButtonText = 'Ya, Gagal';
            iconType = 'error';
        }

        Swal.fire({
            title: titleText,
            html: `Anda yakin ingin mengubah status pengiriman ini menjadi <strong>${newStatus.toUpperCase().replace('_', ' ')}</strong>?`,
            icon: iconType,
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#dc3545',
            confirmButtonText: confirmButtonText,
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('shipmentStatusUpdateForm');
                form.action = `<?= base_url('pabrik/shipments/update-status/') ?>${shipmentId}`;
                document.getElementById('newShipmentStatusInput').value = newStatus;
                form.submit();
            }
        });
    }

    // Script untuk cetak massal (tidak berubah)
    $(document).ready(function() {
        const bulkPrintButton = $('#bulkPrintButton');
        let selectedShipments = JSON.parse(sessionStorage.getItem('selectedShipments')) || {};

        function updateBulkPrintButtonState() {
            const hasSelection = Object.keys(selectedShipments).length > 0;
            bulkPrintButton.prop('disabled', !hasSelection);
        }

        function syncCheckboxesOnPage() {
            let allCheckedOnPage = $('.shipment-checkbox').length > 0;
            $('.shipment-checkbox').each(function() {
                const shipmentId = $(this).val();
                if (selectedShipments[shipmentId]) {
                    $(this).prop('checked', true);
                } else {
                    $(this).prop('checked', false);
                    allCheckedOnPage = false;
                }
            });
            $('#selectAllShipments').prop('checked', allCheckedOnPage);
        }

        $('#selectAllShipments').on('change', function() {
            const isChecked = this.checked;
            $('.shipment-checkbox').each(function() {
                const shipmentId = $(this).val();
                $(this).prop('checked', isChecked);
                if (isChecked) {
                    selectedShipments[shipmentId] = true;
                } else {
                    delete selectedShipments[shipmentId];
                }
            });
            sessionStorage.setItem('selectedShipments', JSON.stringify(selectedShipments));
            updateBulkPrintButtonState();
        });

        $(document).on('change', '.shipment-checkbox', function() {
            const shipmentId = $(this).val();
            if (this.checked) {
                selectedShipments[shipmentId] = true;
            } else {
                delete selectedShipments[shipmentId];
            }
            sessionStorage.setItem('selectedShipments', JSON.stringify(selectedShipments));
            updateBulkPrintButtonState();
            syncCheckboxesOnPage();
        });

        $('#bulkPrintForm').on('submit', function(e) {
            const selectedIds = Object.keys(selectedShipments);
            if (selectedIds.length === 0) {
                e.preventDefault();
                Swal.fire('Peringatan', 'Pilih setidaknya satu pengiriman untuk dicetak.', 'warning');
                return;
            }
            const container = $('#hidden-inputs-container');
            container.empty();
            selectedIds.forEach(id => {
                $('<input>').attr({
                    type: 'hidden',
                    name: 'shipment_ids[]',
                    value: id
                }).appendTo(container);
            });
            setTimeout(() => {
                sessionStorage.removeItem('selectedShipments');
                $('.shipment-checkbox, #selectAllShipments').prop('checked', false);
                updateBulkPrintButtonState();
            }, 1500);
        });

        syncCheckboxesOnPage();
        updateBulkPrintButtonState();
    });
</script>

<?= $this->endSection() ?>```