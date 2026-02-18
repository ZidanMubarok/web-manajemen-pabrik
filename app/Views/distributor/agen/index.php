<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .modal-header {
        background-color: #4e73df;
        color: white;
        border-bottom: none;
    }

    .modal-header .btn-close {
        filter: invert(1) grayscale(100%) brightness(200%);
    }

    .modal-title {
        font-weight: 500;
    }

    .modal-body .detail-label {
        font-weight: 600;
        color: #5a5c69;
    }

    .modal-body .detail-value {
        color: #333;
        word-wrap: break-word;
    }

    .modal-content {
        border: none;
        border-radius: 0.5rem;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <h1 class="mb-4"><?= $title ?></h1>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="<?= base_url('distributor/agents/create') ?>" class="btn btn-primary"><i class="fas fa-plus me-1"></i> Tambah Agen</a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-primary text-white">
            <h6 class="m-0 font-weight-bold"><i class="fas fa-user-tie me-2"></i> Daftar Agen Saya</h6>
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
            <div class="row">
                <div class="col-md-6">
                    <form action="<?= base_url('distributor/agents') ?>" method="get" class="mb-3">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Cari agen berdasarkan username, email..." name="keyword" value="<?= esc($keyword) ?>">
                            <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i> Cari</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>No Telpon</th>
                            <!-- <th>Alamat</th> -->
                            <!-- <th>Tanggal Daftar</th> -->
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($agents)): ?>
                            <tr>
                                <td colspan="7" class="text-center">
                                    <?php if ($keyword): ?>
                                        Data agen dengan kata kunci "<?= esc($keyword) ?>" tidak ditemukan.
                                    <?php else: ?>
                                        Belum ada data agen.
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php
                            // Inisialisasi nomor urut berdasarkan halaman saat ini
                            $no = ($currentPage - 1) * $perPage + 1;
                            foreach ($agents as $agent): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= esc($agent['username']) ?></td>
                                    <td><?= esc($agent['email']) ?></td>
                                    <td><?= esc($agent['no_telpon']) ?></td>
                                    <!-- <td><?php # esc($agent['alamat']) 
                                                ?></td> -->
                                    <!-- <td><?php # date('d/m/Y H:i', strtotime($agent['created_at'])) 
                                                ?></td> -->
                                    <td class="text-center align-middle">
                                        <button type="button" title="Lihat Detail" class="btn btn-primary btn-sm text-white me-1"
                                            onclick="showAgentDetails(this)"
                                            data-agent="<?= htmlspecialchars(json_encode($agent), ENT_QUOTES, 'UTF-8') ?>">
                                            <i class="fas fa-eye"></i>
                                        </button>

                                        <a title="Edit" href="<?= base_url('distributor/agents/edit/' . $agent['id']) ?>" class="btn btn-info btn-sm text-white me-1"><i class="fas fa-edit"></i></a>

                                        <form action="<?= base_url('distributor/agents/delete/' . $agent['id']) ?>" method='get' enctype='multipart/form-data' class="d-inline form-delete">
                                            <?= csrf_field(); ?>
                                            <button title="Hapus" type="button" class="btn btn-danger btn-sm" onclick="konfirmasi(this)"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div>
                        <?php if ($totalAgents > 0): ?>
                            <span class="text-muted">
                                Menampilkan <strong><?= $startNumber ?></strong> sampai <strong><?= $endNumber ?></strong> dari total <strong><?= $totalAgents ?></strong> agen.
                            </span>
                        <?php endif; ?>
                    </div>
                    <div>
                        <?php if ($pager) : ?>
                            <?= $pager->links('agents', 'bootstrap_pagination') ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
<!-- =================================================================
MODAL UNTUK DETAIL AGEN
================================================================== -->
<?= $this->section('modals') ?>
<div class="modal fade" id="agentDetailModal" tabindex="-1" aria-labelledby="agentDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="agentDetailModalLabel"><i class="fas fa-user-circle me-2"></i> Detail Agen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="agentDetailContent"></div>
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
    // Pastikan fungsi konfirmasi Anda sudah ada, jika belum, tambahkan.
    // function konfirmasi(button) {
    //     if (confirm("Apakah Anda yakin ingin menghapus data ini?")) {
    //         button.closest('form').submit();
    //     }
    // }

    // Fungsi baru untuk menampilkan detail agen di modal
    function showAgentDetails(button) {
        // 1. Ambil data JSON dari atribut data-agent
        const agentData = JSON.parse(button.getAttribute('data-agent'));

        // 2. Ubah judul modal
        document.getElementById('agentDetailModalLabel').innerHTML = `<i class="fas fa-user-circle me-2"></i> Detail: ${agentData.username}`;

        // 3. Format tanggal pendaftaran agar lebih mudah dibaca
        const registrationDate = new Date(agentData.created_at);
        const formattedDate = registrationDate.toLocaleDateString('id-ID', {
            day: '2-digit',
            month: 'long',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });

        // 4. Siapkan konten HTML untuk body modal
        const modalContent = `
            <dl class="row">
                <dt class="col-sm-4 col-5 detail-label">Username</dt>
                <dd class="col-sm-8 col-7 detail-value">: ${agentData.username}</dd>

                <dt class="col-sm-4 col-5 detail-label">Email</dt>
                <dd class="col-sm-8 col-7 detail-value">: ${agentData.email}</dd>

                <dt class="col-sm-4 col-5 detail-label">No. Telepon</dt>
                <dd class="col-sm-8 col-7 detail-value">: ${agentData.no_telpon}</dd>

                <dt class="col-sm-4 col-5 detail-label">Alamat</dt>
                <dd class="col-sm-8 col-7 detail-value">: ${agentData.alamat}</dd>

                <hr class="my-2">

                <dt class="col-sm-4 col-5 detail-label">Tanggal Daftar</dt>
                <dd class="col-sm-8 col-7 detail-value">: ${formattedDate}</dd>
            </dl>
        `;

        // 5. Masukkan konten ke dalam body modal
        document.getElementById('agentDetailContent').innerHTML = modalContent;

        // 6. Tampilkan modal menggunakan instance Bootstrap
        const agentModal = new bootstrap.Modal(document.getElementById('agentDetailModal'));
        agentModal.show();
    }
</script>
<?= $this->endSection() ?>