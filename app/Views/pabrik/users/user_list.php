<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <h1 class="mb-4"><?= $title ?></h1>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="<?= base_url('pabrik/users/create') ?>" class="btn btn-primary"><i class="fas fa-plus me-1"></i> Tambah Distributor</a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-primary text-white">
            <h6 class="m-0 font-weight-bold"><i class="fas fa-users me-2"></i> Daftar Distributor</h6>
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

            <!-- Filter Form -->
            <div class="mb-3">
                <form action="<?= base_url('pabrik/users') ?>" method="get" class="d-flex">
                    <input type="text" name="search" class="form-control me-2" placeholder="Cari distributor (username, email, telepon, alamat)" value="<?= esc($search ?? '') ?>">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="fas fa-search"></i>
                    </button>
                    <?php if (!empty($search)): ?>
                        <a href="<?= base_url('pabrik/users') ?>" class="btn btn-outline-secondary ms-2"><i class="fas fa-sync-alt"></i></a>
                    <?php endif; ?>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>No Telpon</th>
                            <th>Alamat</th>
                            <th>Role</th>
                            <th>Tanggal Daftar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($users)): ?>
                            <tr>
                                <td colspan="8" class="text-center">Belum ada data distributor.</td>
                            </tr>
                        <?php else: ?>
                            <?php
                            // Hitung nomor awal untuk pagination
                            $currentPage = $pager->getCurrentPage();
                            $perPage = $pager->getPerPage();
                            $no = (($currentPage - 1) * $perPage) + 1;
                            foreach ($users as $user): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= esc($user['username']) ?></td>
                                    <td><?= esc($user['email']) ?></td>
                                    <td><?= esc($user['no_telpon']) ?></td>
                                    <td><?= esc($user['alamat']) ?></td>
                                    <td><span class="badge bg-primary"><?= esc(ucfirst($user['role'])) ?></span></td>
                                    <td><?= date('d/m/Y H:i', strtotime($user['created_at'])) ?></td>
                                    <td>
                                        <form action="<?= base_url('pabrik/users/delete/' . $user['id']) ?>" method='get' enctype='multipart/form-data' class="form-delete">
                                            <a href="<?= base_url('pabrik/users/edit/' . $user['id']) ?>" class="btn btn-sm btn-info text-white"><i class="fas fa-edit"></i>Edit</a>
                                            <?= csrf_field(); ?>
                                            <button type="button" class="btn btn-sm btn-danger" onclick="konfirmasi(this)"><i class="fas fa-trash"></i>Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Informasi Total Data dan Pagination Links -->
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div>
                    <?php
                    $totalItems = $pager->getTotal();
                    $firstItem = ($totalItems > 0) ? (($pager->getCurrentPage() - 1) * $pager->getPerPage()) + 1 : 0;
                    $lastItem = ($totalItems > 0) ? min($firstItem + $pager->getPerPage() - 1, $totalItems) : 0;
                    ?>
                    Menampilkan <?= $firstItem ?> sampai <?= $lastItem ?> dari <?= $totalItems ?> total distributor
                </div>
                <div>
                    <!-- Pager atau Pagination -->
                    <?= $pager->links('default', 'bootstrap_pagination') ?>
                </div>
            </div>

        </div>
    </div>
</div>
<?= $this->endSection() ?>