<?= $this->extend('layouts/main_layout') ?>

<?= $this->section('title') ?>
Home - Kemurnian dari Sumber Terbaik
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- =================================== -->
<!-- HERO SECTION START                  -->
<!-- =================================== -->
<header class="hero-section" id="home">
    <video playsinline autoplay muted loop id="heroVideo">
        <source src="<?= base_url('assets/video/iklan.mp4'); ?>" type="video/mp4">
        Browser Anda tidak mendukung tag video.
    </video>
    <div class="overlay"></div>
    <div class="container z-index-1">
        <div class="row">
            <div class="col-md-8 mx-auto text-center">
                <h1 class="display-4 fw-bold mb-3">NuCless, Kemurnian dari Sumber Terbaik</h1>
                <p class="lead mb-4">Menghadirkan kesegaran air mineral alami yang diproses dengan teknologi modern untuk menjaga kualitas dan kemurniannya hingga ke tangan Anda.</p>
                <a href="#produk" class="btn btn-primary btn-lg px-5">Lihat Produk Kami</a>
            </div>
        </div>
    </div>
</header>

<!-- =================================== -->
<!-- KEUNGGULAN SECTION START            -->
<!-- =================================== -->
<section id="keunggulan" class="bg-light">
    <div class="container">
        <div class="section-title">
            <h2>Mengapa Memilih NuCless?</h2>
            <p>Kami berkomitmen pada standar tertinggi untuk setiap tetes air yang kami hasilkan.</p>
        </div>
        <div class="row text-center">
            <div class="col-md-4 mb-4">
                <div class="feature-icon"><i class="fas fa-mountain"></i></div>
                <h4 class="fw-bold">Sumber Terlindungi</h4>
                <p class="text-muted">Berasal dari mata air pegunungan pilihan yang ekosistemnya terjaga secara alami.</p>
            </div>
            <div class="col-md-4 mb-4">
                <div class="feature-icon"><i class="fas fa-cogs"></i></div>
                <h4 class="fw-bold">Teknologi Modern</h4>
                <p class="text-muted">Diproses melalui sistem filtrasi multi-tahap dan quality control yang ketat.</p>
            </div>
            <div class="col-md-4 mb-4">
                <div class="feature-icon"><i class="fas fa-check-circle"></i></div>
                <h4 class="fw-bold">Terjamin Higienis</h4>
                <p class="text-muted">Proses pembotolan otomatis tanpa sentuhan tangan untuk menjamin kebersihan produk.</p>
            </div>
        </div>
    </div>
</section>

<!-- =================================== -->
<!-- PRODUK SECTION START                -->
<!-- =================================== -->
<section id="produk">
    <div class="container">
        <div class="section-title">
            <h2>Produk Kami</h2>
            <p>Tersedia dalam berbagai kemasan untuk menemani setiap aktivitas Anda.</p>
        </div>
        <div class="row">
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card card-product text-center"><img src="<?= base_url('assets/img/produksi.jpeg'); ?>" class="card-img-top" alt="...">
                    <div class="card-body">
                        <h5 class="card-title fw-bold">NuCless 120ml</h5>
                        <p class="card-text text-muted">Praktis untuk dibawa.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card card-product text-center"><img src="<?= base_url('assets/img/produksi.jpeg'); ?>" class="card-img-top" alt="...">
                    <div class="card-body">
                        <h5 class="card-title fw-bold">NuCless 1500ml</h5>
                        <p class="card-text text-muted">Cukup untuk harian.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card card-product text-center"><img src="<?= base_url('assets/img/produksi.jpeg'); ?>" class="card-img-top" alt="...">
                    <div class="card-body">
                        <h5 class="card-title fw-bold">NuCless Galon 19L</h5>
                        <p class="card-text text-muted">Untuk keluarga & kantor.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card card-product text-center"><img src="<?= base_url('assets/img/produksi.jpeg'); ?>" class="card-img-top" alt="...">
                    <div class="card-body">
                        <h5 class="card-title fw-bold">NuCless Gelas 240ml</h5>
                        <p class="card-text text-muted">Pas untuk acara.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- =================================== -->
<!-- PROSES PRODUKSI SECTION START       -->
<!-- =================================== -->
<section id="proses" class="bg-light">
    <div class="container">
        <div class="section-title">
            <h2>Proses Produksi Berstandar Tinggi</h2>
            <p>Setiap langkah kami lakukan dengan presisi untuk hasil terbaik.</p>
        </div>
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <div class="video-responsive shadow">
                    <!-- PASTE KODE IFRAME DARI YOUTUBE DI SINI -->
                    <!-- Pastikan sudah ditambahkan loading="lazy" -->
                    <!-- <iframe width="560" height="315" src="https://www.youtube.com/embed/SArN4cUGl6k?si=Ot_0kdca347N-Zyz" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen loading="lazy"></iframe> -->
                    <iframe width="560" height="315" src="https://www.youtube.com/embed/IHy6s_eqg8Y?si=A2w9j3Fto59VUTnE" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen loading="lazy"></iframe>
                </div>
                <!-- <img src="https://via.placeholder.com/600x400.png?text=Area+Produksi+Higienis" class="img-fluid rounded shadow" alt="..."> -->
            </div>
            <div class="col-lg-6">
                <div class="d-flex align-items-start mb-4">
                    <div class="icon-wrapper flex-shrink-0 me-3"><i class="fas fa-tint"></i></div>
                    <div>
                        <h5 class="fw-bold">1. Pengambilan Air</h5>
                        <p class="text-muted">Air diambil dari sumber mata air terpilih yang terjaga.</p>
                    </div>
                </div>
                <div class="d-flex align-items-start mb-4">
                    <div class="icon-wrapper flex-shrink-0 me-3"><i class="fas fa-filter"></i></div>
                    <div>
                        <h5 class="fw-bold">2. Mikrofiltrasi</h5>
                        <p class="text-muted">Penyaringan modern tanpa menghilangkan mineral penting.</p>
                    </div>
                </div>
                <div class="d-flex align-items-start">
                    <div class="icon-wrapper flex-shrink-0 me-3"><i class="fas fa-box-open"></i></div>
                    <div>
                        <h5 class="fw-bold">3. Pengemasan Otomatis</h5>
                        <p class="text-muted">Pengisian dan penyegelan botol dilakukan secara higienis.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- =================================== -->
<!-- CTA SECTION START                   -->
<!-- =================================== -->
<?php
// $no_telepon = '6285258555439'; // Nomor WhatsApp yang akan digunakan
$no_telepon = '6283174971993'; // Nomor WhatsApp yang akan digunakan
?>
<section class="bg-primary text-white text-center">
    <div class="container">
        <h2 class="fw-bold">Tertarik Menjadi Mitra Kami?</h2>
        <p class="lead my-4">Jadilah bagian dari jaringan distribusi NuCless dan hadirkan kesegaran untuk lebih banyak orang.</p>
        <a href="https://wa.me/<?= $no_telepon; ?>?text=Halo%20saya%20tertarik%20untuk%20menjadi%20mitra%20NuCless" target="_blank" class="btn btn-light btn-lg px-5"><i class="fas fa-phone"></i> Hubungi Tim Marketing</a>
    </div>
</section>

<!-- =================================== -->
<!-- LOKASI KAMI SECTION START (BARU)    -->
<!-- =================================== -->
<section id="lokasi">
    <div class="container">
        <div class="section-title">
            <h2>Temukan Lokasi Kami</h2>
            <p>Kunjungi pabrik kami untuk informasi lebih lanjut atau kerja sama.</p>
        </div>
        <div class="row">
            <div class="col-12">
                <!-- Wrapper div untuk peta responsif -->
                <div class="map-container shadow">
                    <!-- Kode iframe dari Google Maps disematkan di sini -->
                    <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d126540.93329204159!2d112.0051111!3d-7.6396115!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e78457bed464e23%3A0x2b5c187e9462ec1c!2sPT%20Persada%20Nawa%20Kartika%20(AMDK%20NUCless)!5e0!3m2!1sid!2sid!4v1754456194211!5m2!1sid!2sid" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    console.log("Halaman Home Page Profesional dimuat dengan Peta.");
</script>
<?= $this->endSection() ?>