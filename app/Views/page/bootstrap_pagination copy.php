<?php

/**
 * @var \CodeIgniter\Pager\PagerRenderer $pager
 */
$pager->setSurroundCount(2); // Menentukan berapa banyak link angka di sekitar halaman aktif

// Pastikan Anda sudah memuat Font Awesome Free CSS dan JavaScript di layout atau view utama Anda.
// Contoh (versi 6.x disarankan):
// <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
// <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
// <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

?>

<nav aria-label="Navigasi Halaman" class="d-flex justify-content-center my-4">
    <ul class="pagination pagination-lg shadow-sm rounded-pill overflow-hidden">
        <?php if ($pager->hasPrevious()): ?>
            <li class="page-item">
                <a class="page-link" href="<?= $pager->getFirst() ?>" aria-label="Pertama" data-bs-toggle="tooltip" data-bs-placement="top" title="Halaman Pertama">
                    <i class="fa-solid fa-angles-left"></i> <span class="d-none d-sm-inline ms-1">Pertama</span>
                </a>
            </li>
        <?php endif ?>

        <?php if ($pager->hasPrevious()): ?>
            <li class="page-item">
                <a class="page-link" href="<?= $pager->getPrevious() ?>" aria-label="Sebelumnya" data-bs-toggle="tooltip" data-bs-placement="top" title="Halaman Sebelumnya">
                    <i class="fa-solid fa-angle-left"></i> <span class="d-none d-sm-inline ms-1">Sebelumnya</span>
                </a>
            </li>
        <?php endif ?>

        <?php foreach ($pager->links() as $link): ?>
            <li class="page-item <?= $link['active'] ? 'active' : '' ?>">
                <a href="<?= $link['uri'] ?>" class="page-link <?= $link['active'] ? 'fw-bold' : '' ?>" <?= $link['active'] ? 'aria-current="page"' : '' ?>>
                    <?= $link['title'] ?>
                </a>
            </li>
        <?php endforeach ?>

        <?php if ($pager->hasNext()): ?>
            <li class="page-item">
                <a class="page-link" href="<?= $pager->getNext() ?>" aria-label="Berikutnya" data-bs-toggle="tooltip" data-bs-placement="top" title="Halaman Berikutnya">
                    <span class="d-none d-sm-inline me-1">Berikutnya</span>
                    <i class="fa-solid fa-angle-right"></i> </a>
            </li>
        <?php endif ?>

        <?php if ($pager->hasNext()): ?>
            <li class="page-item">
                <a class="page-link" href="<?= $pager->getLast() ?>" aria-label="Terakhir" data-bs-toggle="tooltip" data-bs-placement="top" title="Halaman Terakhir">
                    <span class="d-none d-sm-inline me-1">Terakhir</span>
                    <i class="fa-solid fa-angles-right"></i> </a>
            </li>
        <?php endif ?>
    </ul>
</nav>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    });
</script>

<style>
    .pagination.rounded-pill .page-item:first-child .page-link {
        border-top-left-radius: 2.25rem;
        /* Menyesuaikan dengan pagination-lg */
        border-bottom-left-radius: 2.25rem;
    }

    .pagination.rounded-pill .page-item:last-child .page-link {
        border-top-right-radius: 2.25rem;
        /* Menyesuaikan dengan pagination-lg */
        border-bottom-right-radius: 2.25rem;
    }

    .pagination .page-item .page-link {
        transition: all 0.3s ease;
        /* Transisi halus untuk hover */
        display: flex;
        /* Untuk menempatkan ikon dan teks sejajar */
        align-items: center;
        /* Pusat vertikal */
        padding: 0.75rem 1.25rem;
        /* Padding lebih besar */
    }

    .pagination .page-item.active .page-link {
        background-color: #0d6efd;
        /* Warna primary Bootstrap */
        border-color: #0d6efd;
        color: #fff;
        box-shadow: 0 4px 8px rgba(13, 110, 253, 0.2);
        /* Efek bayangan saat aktif */
    }

    .pagination .page-item .page-link:hover {
        background-color: #e9ecef;
        /* Warna hover ringan */
        color: #0d6efd;
        border-color: #dee2e6;
    }

    /* Responsif: Sembunyikan teks di perangkat kecil untuk tombol 'Pertama', 'Sebelumnya', 'Berikutnya', 'Terakhir' */
    @media (max-width: 576px) {
        .pagination .page-link .d-sm-inline {
            display: none !important;
        }
    }
</style>