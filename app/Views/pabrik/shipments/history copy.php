<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('title') ?>
Manajemen Pengiriman (On Transit)
<?= $this->endSection() ?>

<?= $this->section('head'); ?>
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<!-- SweetAlert2 untuk dialog konfirmasi yang lebih baik -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
    /* (CSS SAMA SEPERTI SEBELUMNYA, TIDAK ADA PERUBAHAN) */
    body {
        background-color: #f8f9fc;
    }

    .card {
        border: none;
        border-radius: .75rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease-in-out;
    }

    .card:hover {
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
    }

    .card-header.bg-primary {
        border-top-left-radius: .75rem;
        border-top-right-radius: .75rem;
    }

    .table-hover>tbody>tr:hover {
        background-color: rgba(var(--bs-primary-rgb), 0.05);
    }

    .form-floating>.form-control {
        height: calc(3.5rem + 2px);
        padding: 1rem .75rem;
    }

    .form-floating>label {
        padding: 1rem .75rem;
    }

    .badge.bg-warning {
        background-color: var(--bs-warning-bg-subtle) !important;
        color: var(--bs-warning-text-emphasis) !important;
        border: 1px solid var(--bs-warning-border-subtle) !important;
    }

    .pagination .page-link {
        border-radius: .375rem;
        margin: 0 .2rem;
    }

    .pagination .page-item.active .page-link {
        box-shadow: 0 4px 8px rgba(var(--bs-primary-rgb), 0.3);
    }

    .action-btn-group .dropdown-menu {
        border-radius: .5rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .action-btn-group .dropdown-item {
        display: flex;
        align-items: center;
        gap: .5rem;
    }

    .action-btn-group .dropdown-item i {
        width: 1rem;
        text-align: center;
    }
</style>
<?= $this->endSection(); ?>

<?= $this->section('content') ?>
<div class="container-fluid pt-4">

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-shipping-fast text-primary me-2"></i> Manajemen Pengiriman (On Transit)</h1>
    </div>

    <!-- Flash Messages (Sama) -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success d-flex align-items-center alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            <div><?= session()->getFlashdata('success') ?></div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger d-flex align-items-center alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <div><?= session()->getFlashdata('error') ?></div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Filter Card (Sama) -->
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3"><i class="fas fa-filter me-2"></i> Filter Pencarian</h5>
            <form action="<?= base_url('pabrik/shipments/history') ?>" method="get" class="row g-3 align-items-end">
                <div class="col-lg-4 col-md-6">
                    <div class="form-floating">
                        <input type="text" name="search" id="search" class="form-control" placeholder="Cari ID, Resi, Distributor..." value="<?= esc($search ?? '') ?>">
                        <label for="search">Cari ID, Resi, Distributor, Agen</label>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="form-floating">
                        <input type="date" name="start_date" id="start_date" class="form-control" value="<?= esc($startDate ?? '') ?>">
                        <label for="start_date">Dari Tanggal</label>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="form-floating">
                        <input type="date" name="end_date" id="end_date" class="form-control" value="<?= esc($endDate ?? '') ?>">
                        <label for="end_date">Sampai Tanggal</label>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100 h-100">
                        <i class="fas fa-search me-1"></i> Cari
                    </button>
                    <?php if (!empty($search) || !empty($startDate) || !empty($endDate)): ?>
                        <a href="<?= base_url('pabrik/shipments/history') ?>" class="btn btn-outline-secondary" data-bs-toggle="tooltip" data-bs-placement="top" title="Reset Filter">
                            <i class="fas fa-sync-alt"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="card shadow-sm mb-4">
        <div class="card-header py-3 bg-primary text-white d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold"><i class="fas fa-truck me-2"></i> Daftar Pengiriman Sedang Transit</h6>
        </div>
        <div class="card-body">
            <form action="<?= base_url('pabrik/shipments/bulk-print') ?>" method="post" id="bulkPrintForm" target="_blank">
                <?= csrf_field() ?>
                <div id="hidden-inputs-container"></div>

                <!-- Action Bar for Bulk Print -- PERUBAHAN DI SINI -->
                <div class="mb-3 d-flex align-items-center gap-2">
                    <button type="submit" class="btn btn-success" id="bulkPrintButton" disabled>
                        <i class="fas fa-print me-2"></i> <span id="bulkPrintButtonText">Cetak Dokumen Terpilih</span>
                    </button>
                    <!-- Tombol baru untuk hapus seleksi -->
                    <button type="button" class="btn btn-outline-danger" id="clearSelectionButton" style="display: none;" data-bs-toggle="tooltip" title="Hapus semua pilihan di semua halaman">
                        <i class="fas fa-times"></i> Hapus Seleksi
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="dataTable" width="100%" cellspacing="0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width: 50px;">
                                    <input class="form-check-input" type="checkbox" id="selectAllShipments" data-bs-toggle="tooltip" title="Pilih Semua di Halaman Ini">
                                </th>
                                <th style="width: 50px;">No</th>
                                <th>ID Pengiriman</th>
                                <th>Distributor / Agen</th>
                                <th>Alamat Tujuan / Agen</th>
                                <th>Tanggal Kirim</th>
                                <th class="text-center">Status</th>
                                <th>No. Resi</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($shipments)): ?>
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                                        <h5 class="mb-1">Tidak Ada Data Pengiriman</h5>
                                        <p class="text-muted">Saat ini tidak ada pengiriman yang sedang dalam perjalanan.</p>
                                    </td>
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
                                        <td class="text-center">
                                            <input class="form-check-input shipment-checkbox" type="checkbox" value="<?= esc($shipment['id']) ?>">
                                        </td>
                                        <td><?= $no++ ?></td>
                                        <td>
                                            <div class="fw-bold">SHP-<?= esc($shipment['id']) ?></div>
                                            <small class="text-muted">ORD-<?= esc($shipment['order_id']) ?></small>
                                        </td>
                                        <td>
                                            <div class="fw-bold"><?= esc($distributorUsernames[$order['distributor_id']] ?? 'N/A') ?></div>
                                            <small class="text-muted"><?= esc($agenUsernames[$order['agen_id']] ?? '-') ?></small>
                                        </td>
                                        <td>
                                            <div class="fw-bold">
                                                <address>
                                                    <span><?= $agenAlamats[$order['agen_id']] ?? '_'; ?></span>
                                                </address>
                                            </div>
                                        </td>
                                        <td><?= date('d M Y', strtotime($shipment['shipping_date'])) ?></td>
                                        <td class="text-center">
                                            <span class="badge rounded-pill bg-warning text-dark px-3 py-2">
                                                <i class="fas fa-truck-loading me-1"></i> Sedang Transit
                                            </span>
                                        </td>
                                        <td><span class="font-monospace"><?= esc($shipment['tracking_number'] ?? '-') ?></span></td>
                                        <td class="text-center action-btn-group">
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                    Aksi
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li><a class="dropdown-item" href="<?= base_url('pabrik/shipments/detail/' . $shipment['id']) ?>"><i class="fas fa-eye text-info"></i> Lihat Detail</a></li>
                                                    <li>
                                                        <hr class="dropdown-divider">
                                                    </li>
                                                    <li><a class="dropdown-item" href="#" onclick="updateShipmentStatus(event, <?= $shipment['id'] ?>, 'delivered')"><i class="fas fa-check-circle text-success"></i> Tandai Diterima</a></li>
                                                    <li><a class="dropdown-item" href="#" onclick="updateShipmentStatus(event, <?= $shipment['id'] ?>, 'failed')"><i class="fas fa-times-circle text-danger"></i> Tandai Gagal</a></li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </form>
            <!-- Pagination & Info (Sama) -->
            <div class="d-flex justify-content-between align-items-center mt-4 flex-wrap gap-3">
                <div class="text-muted">
                    <?php
                    $totalItems = $pager->getTotal();
                    $firstItem = ($totalItems > 0) ? (($pager->getCurrentPage() - 1) * $pager->getPerPage()) + 1 : 0;
                    $lastItem = ($totalItems > 0) ? min($firstItem + $pager->getPerPage() - 1, $totalItems) : 0;
                    ?>
                    Menampilkan <b><?= $firstItem ?></b>-<b><?= $lastItem ?></b> dari <b><?= $totalItems ?></b> data
                </div>
                <div class="d-flex justify-content-end">
                    <?= $pager->links('default', 'bootstrap_pagination') ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hidden Form for status update (Sama) -->
<form id="shipmentStatusUpdateForm" action="" method="post" style="display: none;">
    <?= csrf_field() ?>
    <input type="hidden" name="status" id="newShipmentStatusInput">
</form>
<script>

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
<!-- PERUBAHAN UTAMA ADA DI DALAM BLOK SCRIPT DI BAWAH INI -->
<script>
    // Inisialisasi Tooltip Bootstrap 5 (Sama)
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    // Fungsi update status (Sama, tidak berubah)
    function updateShipmentStatus(event, shipmentId, newStatus) {
        event.preventDefault();
        let config = {
            delivered: {
                title: 'Konfirmasi Penerimaan',
                html: `Anda yakin pengiriman ini telah <strong>diterima</strong>?`,
                confirmButtonText: 'Ya, Sudah Diterima',
                icon: 'success'
            },
            failed: {
                title: 'Konfirmasi Kegagalan',
                html: `Anda yakin pengiriman ini <strong>gagal</strong>?`,
                confirmButtonText: 'Ya, Gagal Kirim',
                icon: 'error'
            }
        };
        let dialogConfig = config[newStatus];
        Swal.fire({
            title: dialogConfig.title,
            html: dialogConfig.html,
            icon: dialogConfig.icon,
            showCancelButton: true,
            confirmButtonColor: (newStatus === 'delivered' ? '#198754' : '#dc3545'),
            cancelButtonColor: '#6c757d',
            confirmButtonText: dialogConfig.confirmButtonText,
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

    // --- LOGIKA CETAK MASSAL YANG DIPERBARUI DAN DIPERBAIKI ---
    // $(document).ready(function() {

    //     // --- Variabel & State ---
    //     const bulkPrintForm = $('#bulkPrintForm');
    //     const bulkPrintButton = $('#bulkPrintButton');
    //     const bulkPrintButtonText = $('#bulkPrintButtonText');
    //     const clearSelectionBtn = $('#clearSelectionButton');
    //     const hiddenInputsContainer = $('#hidden-inputs-container');
    //     const selectAllCheckbox = $('#selectAllShipments');
    //     const shipmentCheckboxes = $('.shipment-checkbox');
    //     const sessionStorageKey = 'selectedShipments_on_transit';

    //     // Ambil data dari session storage saat halaman dimuat
    //     let selectedShipments = JSON.parse(sessionStorage.getItem(sessionStorageKey)) || {};

    //     // --- Fungsi Utama ---

    //     function updateSessionStorage() {
    //         sessionStorage.setItem(sessionStorageKey, JSON.stringify(selectedShipments));
    //     }

    //     // Fungsi terpusat untuk memperbarui semua elemen UI
    //     function updateUI() {
    //         const selectionCount = Object.keys(selectedShipments).length;
    //         const hasSelection = selectionCount > 0;

    //         // Update tombol cetak
    //         bulkPrintButton.prop('disabled', !hasSelection);
    //         bulkPrintButtonText.text(hasSelection ? `Cetak (${selectionCount}) Dokumen Terpilih` : 'Cetak Dokumen Terpilih');

    //         // Tampilkan atau sembunyikan tombol hapus seleksi
    //         clearSelectionBtn.toggle(hasSelection);

    //         // Sinkronkan checkbox di halaman saat ini
    //         let allOnPageChecked = shipmentCheckboxes.length > 0;
    //         shipmentCheckboxes.each(function() {
    //             const id = $(this).val();
    //             const isChecked = selectedShipments[id] !== undefined;
    //             $(this).prop('checked', isChecked);
    //             if (!isChecked) {
    //                 allOnPageChecked = false;
    //             }
    //         });
    //         selectAllCheckbox.prop('checked', allOnPageChecked);
    //     }

    //     function clearAllSelections() {
    //         selectedShipments = {};
    //         updateSessionStorage();
    //         updateUI(); // Perbarui UI untuk merefleksikan perubahan
    //     }

    //     // --- Event Handlers ---

    //     // Klik "Pilih Semua" (hanya untuk halaman ini)
    //     selectAllCheckbox.on('change', function() {
    //         const isChecked = $(this).prop('checked');
    //         shipmentCheckboxes.each(function() {
    //             const id = $(this).val();
    //             if (isChecked) {
    //                 selectedShipments[id] = true;
    //             } else {
    //                 delete selectedShipments[id];
    //             }
    //         });
    //         updateSessionStorage();
    //         updateUI();
    //     });

    //     // Klik checkbox individual
    //     shipmentCheckboxes.on('change', function() {
    //         const id = $(this).val();
    //         const isChecked = $(this).prop('checked');
    //         if (isChecked) {
    //             selectedShipments[id] = true;
    //         } else {
    //             delete selectedShipments[id];
    //         }
    //         updateSessionStorage();
    //         updateUI();
    //     });

    //     // Klik tombol baru "Hapus Seleksi"
    //     clearSelectionBtn.on('click', function() {
    //         Swal.fire({
    //             title: 'Hapus Semua Pilihan?',
    //             text: "Anda akan menghapus semua pilihan pengiriman yang sudah ditandai di semua halaman.",
    //             icon: 'warning',
    //             showCancelButton: true,
    //             confirmButtonColor: '#d33',
    //             cancelButtonColor: '#3085d6',
    //             confirmButtonText: 'Ya, Hapus Semua!',
    //             cancelButtonText: 'Batal'
    //         }).then((result) => {
    //             if (result.isConfirmed) {
    //                 clearAllSelections();
    //                 Swal.fire('Berhasil!', 'Semua pilihan telah dihapus.', 'success');
    //             }
    //         })
    //     });

    //     // Logika saat form cetak disubmit
    //     bulkPrintForm.on('submit', function(event) {
    //         const selectedIds = Object.keys(selectedShipments);

    //         if (selectedIds.length === 0) {
    //             event.preventDefault(); // Hentikan submit jika tidak ada yang dipilih
    //             Swal.fire('Tidak Ada Pilihan', 'Silakan pilih setidaknya satu pengiriman untuk dicetak.', 'warning');
    //             return;
    //         }

    //         // Bersihkan input lama dan tambahkan yang baru
    //         hiddenInputsContainer.empty();
    //         selectedIds.forEach(id => {
    //             $('<input>').attr({
    //                 type: 'hidden',
    //                 name: 'shipment_ids[]',
    //                 value: id
    //             }).appendTo(hiddenInputsContainer);
    //         });

    //         // Biarkan form tersubmit secara normal.
    //         // Hapus seleksi setelah beberapa saat agar pengguna merasakan feedback
    //         setTimeout(() => {
    //             clearAllSelections();
    //         }, 1500);
    //     });

    //     // --- Initial Load ---
    //     // Panggil updateUI saat halaman pertama kali dimuat untuk menyamakan state
    //     updateUI();
    // });
</script>

<?= $this->endSection() ?>