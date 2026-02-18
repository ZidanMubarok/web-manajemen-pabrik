<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('title') ?>
<?= $title ?>
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
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>No Telpon</th>
                            <th>Alamat</th>
                            <!-- <th>Parent ID</th> -->
                            <th>Tanggal Daftar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($agents)): ?>
                            <tr>
                                <td colspan="6" class="text-center">Belum ada data agen.</td>
                            </tr>
                        <?php else: ?>
                            <?php $no = 1;
                            foreach ($agents as $agent): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= esc($agent['username']) ?></td>
                                    <td><?= esc($agent['email']) ?></td>
                                    <td><?= esc($agent['no_telpon']) ?></td>
                                    <td><?= esc($agent['alamat']) ?></td>
                                    <!-- <td><?php # esc($agent['parent_id'] ?? '-') ?></td> -->
                                    <td><?= date('d/m/Y H:i', strtotime($agent['created_at'])) ?></td>
                                    <td>
                                        <form action="<?= base_url('distributor/agents/delete/' . $agent['id']) ?>" method='get' enctype='multipart/form-data' class="form-delete">
                                            <a title="Edit" href="<?= base_url('distributor/agents/edit/' . $agent['id']) ?>" class="btn btn-info btn-sm text-white me-1"><i class="fas fa-edit"></i></a>
                                            <?= csrf_field(); ?>
                                            <button title="Hapus" type="button" class="btn btn-danger btn-sm" onclick="konfirmasi(this)"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>