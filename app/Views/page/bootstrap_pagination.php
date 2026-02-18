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
    <ul class="pagination custom-pagination shadow-sm rounded-pill overflow-hidden">
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
    /* Mengganti class pagination-lg dengan custom-pagination untuk kontrol penuh */
    .custom-pagination .page-item:first-child .page-link {
        border-top-left-radius: 2.25rem;
        border-bottom-left-radius: 2.25rem;
    }

    .custom-pagination .page-item:last-child .page-link {
        border-top-right-radius: 2.25rem;
        border-bottom-right-radius: 2.25rem;
    }

    .custom-pagination .page-item .page-link {
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        padding: 0.75rem 1.25rem;
        /* Padding lebih besar */
        min-width: 44px;
        /* Menjamin ukuran minimum untuk aksesibilitas sentuhan */
        justify-content: center;
        /* Pusatkan konten */
        color: #6c757d;
        /* Warna teks default yang lebih lembut */
        border: 1px solid #dee2e6;
        /* Border standar */
        margin: 0 2px;
        /* Sedikit spasi antar item */
    }

    /* Efek hover untuk semua link */
    .custom-pagination .page-item .page-link:hover {
        background-color: #e9ecef;
        /* Warna hover ringan */
        color: #0d6efd;
        /* Warna primary Bootstrap */
        border-color: #c9d6de;
        /* Border sedikit lebih gelap saat hover */
        transform: translateY(-2px);
        /* Efek angkat ringan */
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        /* Bayangan lebih halus */
    }

    /* Styling untuk halaman aktif */
    .custom-pagination .page-item.active .page-link {
        background-color: #0d6efd;
        /* Warna primary Bootstrap */
        border-color: #0d6efd;
        color: #fff;
        box-shadow: 0 6px 12px rgba(13, 110, 253, 0.3);
        /* Bayangan lebih kuat saat aktif */
        transform: translateY(-1px);
        /* Sedikit angkat juga saat aktif */
        z-index: 1;
        /* Pastikan di atas elemen lain saat aktif */
    }

    /* Hilangkan border pada page-link aktif dan sebelumnya/berikutnya untuk tampilan yang lebih mulus */
    .custom-pagination .page-item.active+.page-item .page-link,
    .custom-pagination .page-item:has(+ .active) .page-link {
        border-left-color: transparent;
        border-right-color: transparent;
    }

    /* Responsif: Sembunyikan teks di perangkat kecil untuk tombol 'Pertama', 'Sebelumnya', 'Berikutnya', 'Terakhir' */
    @media (max-width: 768px) {

        /* Menggunakan breakpoint yang sedikit lebih besar */
        .custom-pagination .page-link .d-sm-inline {
            display: none !important;
        }

        .custom-pagination .page-item .page-link {
            padding: 0.75rem 0.9rem;
            /* Padding lebih kecil pada layar kecil */
            min-width: 40px;
            /* Ukuran minimum sedikit lebih kecil */
        }
    }

    /* Responsif: Menyembunyikan beberapa angka halaman pada layar yang sangat kecil */
    @media (max-width: 480px) {
        .custom-pagination .page-item:not(.active):nth-child(n+4):nth-last-child(n+4) {
            display: none;
            /* Sembunyikan link angka tengah jika terlalu banyak */
        }

        /* Tambahkan ellipsis jika ada halaman yang disembunyikan */
        .custom-pagination .page-item:nth-child(3):not(.active)~.page-item:nth-last-child(3):not(.active) .page-link::after {
            content: "...";
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            display: block;
            opacity: 0;
            /* Awalnya tersembunyi */
            transition: opacity 0.3s ease;
        }

        .custom-pagination .page-item:nth-child(3):not(.active)~.page-item:nth-last-child(3):not(.active):hover .page-link::after {
            opacity: 1;
            /* Tampilkan saat hover */
        }
    }
</style>