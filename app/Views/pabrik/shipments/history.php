<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('title') ?>
Manajemen Pengiriman (On Transit)
<?= $this->endSection() ?>

<?= $this->section('head'); ?>
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
    /* (CSS tidak ada perubahan signifikan, hanya penyesuaian kecil) */
    body {
        background-color: #f8f9fc;
    }

    .card {
        border: none;
        border-radius: .75rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease-in-out;
    }

    /* ### PERUBAHAN: Menambahkan style agar header filter bisa diklik ### */
    .card-header[data-bs-toggle="collapse"] {
        cursor: pointer;
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

    <!-- Flash Messages (Tidak diubah) -->
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

    <!-- ### PERUBAHAN DIMULAI: Tampilan Filter Card Diubah ### -->
    <div class="card shadow-sm mb-4">
        <div class="card-header py-3" data-bs-toggle="collapse" href="#collapseFilter" role="button" aria-expanded="true" aria-controls="collapseFilter">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-filter me-2"></i> Filter Pengiriman</h6>
        </div>
        <div class="collapse show" id="collapseFilter">
            <div class="card-body">
                <form action="<?= base_url('pabrik/shipments/history') ?>" method="get" class="row g-3 align-items-end">
                    <div class="col-lg-4 col-md-12">
                        <label for="search" class="form-label">Cari ID, Resi, Distributor, atau Agen:</label>
                        <input type="text" name="search" id="search" class="form-control" placeholder="Ketik kata kunci..." value="<?= esc($search ?? '') ?>">
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <label for="start_date" class="form-label">Dari Tanggal Kirim:</label>
                        <input type="date" name="start_date" id="start_date" class="form-control" value="<?= esc($startDate ?? '') ?>">
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <label for="end_date" class="form-label">Sampai Tanggal Kirim:</label>
                        <input type="date" name="end_date" id="end_date" class="form-control" value="<?= esc($endDate ?? '') ?>">
                    </div>
                    <div class="col-lg-2 col-md-12">
                        <div class="btn-group w-100" role="group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Cari
                            </button>
                            <?php if (!empty($search) || !empty($startDate) || !empty($endDate)): ?>
                                <a href="<?= base_url('pabrik/shipments/history') ?>" class="btn btn-secondary" data-bs-toggle="tooltip" title="Reset Filter">
                                    <i class="fas fa-sync-alt"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- ### PERUBAHAN SELESAI ### -->

    <!-- Data Table Card (Struktur dan Logika tidak diubah) -->
    <div class="card shadow-sm mb-4">
        <div class="card-header py-3 bg-primary text-white d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold"><i class="fas fa-truck me-2"></i> Daftar Pengiriman Sedang Transit</h6>
        </div>
        <div class="card-body">
            <form action="<?= base_url('pabrik/shipments/bulk-print') ?>" method="post" id="bulkPrintForm" target="_blank">
                <?= csrf_field() ?>
                <div id="hidden-inputs-container"></div>

                <!-- Action Bar for Bulk Print (Logika tidak diubah) -->
                <div class="mb-3 d-flex align-items-center gap-2">
                    <button type="submit" class="btn btn-success" id="bulkPrintButton" disabled>
                        <i class="fas fa-print me-2"></i> <span id="bulkPrintButtonText">Cetak Dokumen Terpilih</span>
                    </button>
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
                                <th>Alamat Tujuan</th>
                                <th>Tanggal Kirim</th>
                                <th class="text-center">Status</th>
                                <th>No. Resi</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($shipments)): ?>
                                <tr>
                                    <td colspan="9" class="text-center py-5">
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
                                            <small class="text-muted"><?= esc($agenUsernames[$order['agen_id']] ?? 'Tujuan Distributor') ?></small>
                                        </td>
                                        <td>
                                            <?php
                                            // Logika untuk menampilkan alamat tujuan yang relevan
                                            $alamatTujuan = '-';
                                            if (!empty($order['agen_id']) && isset($agenAlamats[$order['agen_id']])) {
                                                $alamatTujuan = $agenAlamats[$order['agen_id']];
                                            } elseif (isset($distributorAlamats[$order['distributor_id']])) {
                                                $alamatTujuan = $distributorAlamats[$order['distributor_id']];
                                            }
                                            ?>
                                            <div style="max-width: 250px; white-space: normal;"><?= esc($alamatTujuan) ?></div>
                                        </td>
                                        <td><?= date('d M Y', strtotime($shipment['shipping_date'])) ?></td>
                                        <td class="text-center">
                                            <span class="badge rounded-pill bg-warning px-3 py-2">
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
            <!-- Pagination & Info (Tidak diubah) -->
            <div class="d-flex justify-content-between align-items-center mt-4 flex-wrap gap-3">
                <div class="text-muted">
                    <?php
                    $totalItems = $pager->getTotal();
                    $firstItem = ($totalItems > 0) ? (($pager->getCurrentPage() - 1) * $pager->getPerPage()) + 1 : 0;
                    $lastItem = ($totalItems > 0) ? min($firstItem + $pager->getPerPage() - 1, $totalItems) : 0;
                    ?>
                    Menampilkan <b><?= $firstItem ?></b>-<b><?= $lastItem ?></b> dari <b><?= $totalItems ?></b> Pengiriman.
                </div>
                <div class="d-flex justify-content-end">
                    <?= $pager->links('default', 'bootstrap_pagination') ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hidden Form for status update (Tidak diubah) -->
<form id="shipmentStatusUpdateForm" action="" method="post" style="display: none;">
    <?= csrf_field() ?>
    <input type="hidden" name="status" id="newShipmentStatusInput">
</form>

<!-- SCRIPT (LOGIKA TIDAK DIUBAH SAMA SEKALI) -->
<script>
    // --- LOGIKA CETAK MASSAL ---
    $(document).ready(function() {

        const bulkPrintForm = $('#bulkPrintForm');
        const bulkPrintButton = $('#bulkPrintButton');
        const bulkPrintButtonText = $('#bulkPrintButtonText');
        const clearSelectionBtn = $('#clearSelectionButton');
        const hiddenInputsContainer = $('#hidden-inputs-container');
        const selectAllCheckbox = $('#selectAllShipments');
        const shipmentCheckboxes = $('.shipment-checkbox');
        const sessionStorageKey = 'selectedShipments_on_transit';

        let selectedShipments = JSON.parse(sessionStorage.getItem(sessionStorageKey)) || {};

        function updateSessionStorage() {
            sessionStorage.setItem(sessionStorageKey, JSON.stringify(selectedShipments));
        }

        function updateUI() {
            const selectionCount = Object.keys(selectedShipments).length;
            const hasSelection = selectionCount > 0;

            bulkPrintButton.prop('disabled', !hasSelection);
            bulkPrintButtonText.text(hasSelection ? `Cetak (${selectionCount}) Dokumen Terpilih` : 'Cetak Dokumen Terpilih');

            clearSelectionBtn.toggle(hasSelection);

            let allOnPageChecked = shipmentCheckboxes.length > 0;
            shipmentCheckboxes.each(function() {
                const id = $(this).val();
                const isChecked = selectedShipments[id] !== undefined;
                $(this).prop('checked', isChecked);
                if (!isChecked) {
                    allOnPageChecked = false;
                }
            });
            selectAllCheckbox.prop('checked', allOnPageChecked);
        }

        function clearAllSelections() {
            selectedShipments = {};
            updateSessionStorage();
            updateUI();
        }

        selectAllCheckbox.on('change', function() {
            const isChecked = $(this).prop('checked');
            shipmentCheckboxes.each(function() {
                const id = $(this).val();
                if (isChecked) {
                    selectedShipments[id] = true;
                } else {
                    delete selectedShipments[id];
                }
            });
            updateSessionStorage();
            updateUI();
        });

        shipmentCheckboxes.on('change', function() {
            const id = $(this).val();
            const isChecked = $(this).prop('checked');
            if (isChecked) {
                selectedShipments[id] = true;
            } else {
                delete selectedShipments[id];
            }
            updateSessionStorage();
            updateUI();
        });

        clearSelectionBtn.on('click', function() {
            Swal.fire({
                title: 'Hapus Semua Pilihan?',
                text: "Anda akan menghapus semua pilihan pengiriman yang sudah ditandai di semua halaman.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus Semua!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    clearAllSelections();
                    Swal.fire({
                        title: 'Berhasil!',
                        text: 'Semua pilihan telah dihapus.',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            })
        });

        bulkPrintForm.on('submit', function(event) {
            const selectedIds = Object.keys(selectedShipments);

            if (selectedIds.length === 0) {
                event.preventDefault();
                Swal.fire('Tidak Ada Pilihan', 'Silakan pilih setidaknya satu pengiriman untuk dicetak.', 'warning');
                return;
            }

            hiddenInputsContainer.empty();
            selectedIds.forEach(id => {
                $('<input>').attr({
                    type: 'hidden',
                    name: 'shipment_ids[]',
                    value: id
                }).appendTo(hiddenInputsContainer);
            });

            setTimeout(() => {
                clearAllSelections();
            }, 1500);
        });

        updateUI();
    });
</script>
<script>
    // Inisialisasi Tooltip Bootstrap 5
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    // Fungsi update status
    function updateShipmentStatus(event, shipmentId, newStatus) {
        event.preventDefault();
        let config = {
            delivered: {
                title: 'Konfirmasi Penerimaan',
                html: `Anda yakin pengiriman <strong>SHP-${shipmentId}</strong> telah <strong>diterima</strong>?`,
                confirmButtonText: 'Ya, Sudah Diterima',
                icon: 'success'
            },
            failed: {
                title: 'Konfirmasi Kegagalan',
                html: `Anda yakin pengiriman <strong>SHP-${shipmentId}</strong> <strong>gagal</strong>?`,
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
</script>

<?= $this->endSection() ?>