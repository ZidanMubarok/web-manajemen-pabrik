<?= $this->extend('layouts/main_layout') ?>

<?= $this->section('title') ?>
Home Page
<?= $this->endSection() ?>

<?= $this->section('head') ?>
<style>
    .welcome-message {
        color: #0d6efd;
        font-size: 2em;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="text-center p-5 bg-white rounded-3">

    <h1 class="welcome-message">Welcome To AMDK Website!</h1>
    <img src="<?= base_url('assets/img/favicon.png'); ?>" alt="amdk" width="200" class="img-fluid mb-4">
    <p class="lead">Lorem ipsum dolor sit amet consectetur adipisicing elit. Blanditiis reiciendis et officia itaque velit, nemo repellendus. Corporis, quia possimus omnis vel voluptatem laboriosam id, iste impedit ea sapiente eligendi repellendus!</p>
    <h6>Jika ada kesalahan web anda bisa laporkan ke Admin! <a href="https://wa.me/6285258555439?text=Halo%20saya%20ingin%20bertanya">Laporkan !</a></h6>

</div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    console.log("Halaman Dashboard dimuat.");
    // Anda bisa menambahkan script JS di sini, misalnya untuk chart atau interaksi dinamis
</script>
<?= $this->endSection() ?>