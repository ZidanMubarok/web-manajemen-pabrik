<?= $this->extend('layouts/dashboard_layout'); // Sesuaikan dengan nama file layout utama Anda 
?>

<?= $this->section('content'); // Sesuaikan dengan nama section konten Anda 
?>

<div class="container-fluid px-4">
    <div class="d-print-none"> <!-- Wrapper untuk menyembunyikan elemen ini saat print -->
        <h1 class="mt-4">Detail Tagihan</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="<?= base_url('distributor/') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= base_url('distributor/invoices') ?>">Tagihan Agen</a></li>
            <li class="breadcrumb-item active">Detail Tagihan #<?= esc($invoice['id']); ?></li>
        </ol>
    </div>

    <!-- Beri ID pada area yang ingin dicetak -->
    <div id="printable-area">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>
                    <i class="fas fa-file-invoice-dollar me-1"></i>
                    Invoice #<?= esc($invoice['id']); ?>
                </span>
                <!-- Tombol-tombol ini akan disembunyikan oleh kelas d-print-none -->
                <div class="d-print-none">
                    <button onclick="printInvoice()" class="btn btn-secondary btn-sm"><i class="fas fa-print me-1"></i> Cetak</button>
                    <button id="downloadPdf" class="btn btn-primary btn-sm"><i class="fas fa-file-pdf me-1"></i> Unduh PDF</button>
                </div>
            </div>
            <div class="card-body" id="invoice-content">
                <header class="row mb-4">
                    <div class="col-sm-6">
                        <h2 class="mb-1"><?= esc($distributor['username']); ?></h2>
                        <address>
                            <?= nl2br(esc($distributor['alamat'])); ?>
                            <br>
                            <strong>Email:</strong> <?= esc($distributor['email']); ?>
                            <br>
                            <strong>Telepon: </strong> <?= esc($distributor['no_telpon']); ?>
                        </address>
                    </div>
                    <div class="col-sm-6 text-sm-end">
                        <h1>INVOICE</h1>
                        <p class="mb-0"><strong>Invoice #:</strong> <?= esc($invoice['id']); ?></p>
                        <p class="mb-0"><strong>Tanggal Invoice:</strong> <?= date('d F Y', strtotime($invoice['invoice_date'])); ?></p>
                        <!-- <p class="mb-0"><strong>Jatuh Tempo:</strong> <?php # date('d F Y', strtotime($invoice['due_date'])); 
                                                                            ?></p> -->
                        <?php
                        // Menentukan kelas status berdasarkan status invoice
                        $status_class = 'bg-secondary';
                        if ($invoice['status'] == 'paid') {
                            $status_class = 'bg-success';
                        } elseif ($invoice['status'] == 'unpaid') {
                            $status_class = 'bg-warning text-dark';
                        } elseif ($invoice['status'] == 'cancelled') {
                            $status_class = 'bg-danger';
                        }
                        ?>
                        <p class="mb-0"><strong>Status:</strong> <span class="badge <?= $status_class; ?>"><?= ucfirst(esc($invoice['status'])); ?></span></p>
                    </div>
                </header>

                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <strong>Tagihan Untuk:</strong>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title"><?= esc($invoice['agent_username']); ?></h5>
                                <p class="card-text mb-0"><?= nl2br(esc($invoice['agent_address'])); ?></p>
                                <p class="card-text mb-0"><?= esc($invoice['agent_email']); ?></p>
                                <p class="card-text"><?= esc($invoice['agent_phone']); ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center">#</th>
                                <th>Produk</th>
                                <th class="text-center">Jumlah</th>
                                <th class="text-end">Harga Satuan</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; ?>
                            <?php foreach ($orderItems as $item) : ?>
                                <tr>
                                    <td class="text-center"><?= $no++; ?></td>
                                    <td><?= esc($item['product_name']); ?></td>
                                    <td class="text-center"><?= esc($item['quantity']); ?></td>
                                    <td class="text-end">Rp <?= number_format($item['unit_price'], 2, ',', '.'); ?></td>
                                    <td class="text-end">Rp <?= number_format($item['sub_total'], 2, ',', '.'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4" class="text-end fw-bold">Total</td>
                                <td class="text-end fw-bold fs-5">Rp <?= number_format($invoice['amount_total'], 2, ',', '.'); ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- <div class="row mt-4">
                    <div class="col-sm-7">
                        <strong>Catatan:</strong>
                        <p><?php # esc($invoice['notes'] ?: 'Tidak ada catatan tambahan.'); ?></p>
                    </div>
                    <div class="col-sm-5 text-sm-end">
                        <p>Terima kasih atas bisnis Anda!</p>
                    </div>
                </div> -->

                <hr>

                <footer class="text-center text-muted">
                    <p>Ini adalah tagihan yang dicetak oleh sistem dan sah tanpa tanda tangan.</p>
                </footer>
            </div>
        </div>
    </div>
</div>

<!-- ================================================================== -->
<!-- PERBAIKAN CSS UNTUK FITUR CETAK ADA DI SINI -->
<!-- ================================================================== -->
<style>
    @media print {

        /* 1. Sembunyikan semua elemen secara default */
        body * {
            visibility: hidden;
        }

        /* 2. Tampilkan HANYA area cetak dan semua isinya */
        #printable-area,
        #printable-area * {
            visibility: visible;
        }

        /* 3. Posisikan area cetak agar memenuhi halaman */
        #printable-area {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }

        /* 4. Hapus border dan shadow yang tidak perlu pada card */
        .card {
            border: 0 !important;
            box-shadow: none !important;
        }
    }
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<script>
    // Fungsi untuk cetak langsung
    function printInvoice() {
        window.print();
    }

    // Fungsi untuk unduh PDF (TIDAK BERUBAH)
    document.getElementById('downloadPdf').addEventListener('click', function() {
        const invoiceContent = document.getElementById('invoice-content');
        const invoiceNumber = "<?= esc($invoice['id'], 'js'); ?>";

        html2canvas(invoiceContent, {
            scale: 2
        }).then(canvas => {
            const imgData = canvas.toDataURL('image/png');
            const {
                jsPDF
            } = window.jspdf;
            const pdf = new jsPDF({
                orientation: 'p',
                unit: 'mm',
                format: 'a4'
            });
            const pdfWidth = pdf.internal.pageSize.getWidth();
            const pdfHeight = (canvas.height * pdfWidth) / canvas.width;
            pdf.addImage(imgData, 'PNG', 0, 0, pdfWidth, pdfHeight);
            pdf.save(`Invoice-${invoiceNumber}.pdf`);
        });
    });
</script>

<?= $this->endSection(); ?>