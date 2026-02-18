<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <h1 class="mb-4"><?= $title ?></h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-primary text-white">
            <h6 class="m-0 font-weight-bold"><i class="fas fa-info-circle me-2"></i> Detail Tagihan #<?= esc($invoice['invoice_number']) ?></h6>
        </div>
        <div class="card-body">
            <div id="invoiceContent" class="p-4 bg-light border rounded mb-4">
                <div class="row mb-4">
                    <div class="col-12 text-center mb-4">
                        <!-- <img src="<?= base_url('favicon.ico') ?>" alt="Logo Perusahaan" style="max-height: 80px;"> -->
                        <h2 class="mt-2">FAKTUR TAGIHAN</h2>
                        <p class="lead">Nomor: <strong><?= esc($invoice['invoice_number']) ?></strong></p>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-6">
                        <h5>Ditujukan Kepada:</h5>
                        <p class="mb-1"><strong>Nama Distributor:</strong> <?= esc($invoice['distributor_username'] ?? 'N/A') ?></p>
                        <p class="mb-1"><strong>Email:</strong> <?= esc($invoice['distributor_email'] ?? 'N/A') ?></p>
                        <p class="mb-1"><strong>Telepon:</strong> <?= esc($invoice['distributor_telpon'] ?? 'N/A') ?></p>
                        <p class="mb-1"><strong>Alamat:</strong> <?= esc($invoice['distributor_alamat'] ?? 'N/A') ?></p>
                    </div>
                    <div class="col-6 text-md-end">
                        <h5>Informasi Faktur:</h5>
                        <p class="mb-1"><strong>Tanggal Faktur:</strong> <?= date('d/m/Y', strtotime($invoice['invoice_date'])) ?></p>
                        <p class="mb-1"><strong>Jatuh Tempo:</strong> <?= date('d/m/Y', strtotime($invoice['due_date'])) ?></p>
                        <p class="mb-1"><strong>Status:</strong>
                            <?php
                            $status = strtolower($invoice['status']);
                            $text = '';
                            $badgeClass = '';
                            switch ($status) {
                                case 'unpaid':
                                    $badgeClass = 'badge bg-danger';
                                    $text = 'Belum Dibayar';
                                    break;
                                case 'paid':
                                    $badgeClass = 'badge bg-success';
                                    $text = 'Sudah Lunas';
                                    break;
                                case 'partially_paid':
                                    $badgeClass = 'badge bg-warning text-dark';
                                    $text = 'Dibayar Sebagian';
                                    break;
                                case 'cancelled':
                                    $badgeClass = 'badge bg-secondary';
                                    $text = 'Di Batalkan';
                                    break;
                                default:
                                    $badgeClass = 'badge bg-info';
                                    $text = 'Status Tidak Diketahui';
                                    break;
                            }
                            ?>
                            <span class="<?= $badgeClass ?>"><?= esc(ucfirst(str_replace('_', ' ', $text))) ?></span>
                        </p>
                        <p class="mb-1"><strong>Tanggal Pembayaran:</strong> <?= !empty($invoice['payment_date']) ? date('d/m/Y H:i', strtotime($invoice['payment_date'])) : '-' ?></p>
                    </div>
                </div>
                <hr class="my-4">

                <h5>Detail Order Asal (ID: ORD-<?= esc($invoice['order_id']) ?>)</h5>
                <div class="row mb-4">
                    <div class="col-6">
                        <p><strong>Tanggal Order:</strong> <?= date('d/m/Y H:i', strtotime($invoice['order_date'])) ?></p>
                        <p><strong>Total Pembayaran Order (Agen):</strong> Rp <?= esc(number_format($invoice['order_total_amount'], 0, ',', '.')) ?></p>
                        <p class="mb-1"><strong>Status:</strong>
                            <?php
                            $status = strtolower($invoice['status_order']);
                            $badgeClass = '';
                            $textStatus = '';
                            switch ($status) {
                                case 'pending':
                                    $badgeClass = 'bg-secondary';
                                    $textStatus = 'Tertunda';
                                    break;
                                case 'approved':
                                    $badgeClass = 'bg-info';
                                    $textStatus = 'Di Setujui';
                                    break;
                                case 'processing':
                                    $badgeClass = 'bg-warning';
                                    $textStatus = 'Di Proses';
                                    break;
                                case 'shipped':
                                    $badgeClass = 'bg-primary';
                                    $textStatus = 'Di Kirim';
                                    break;
                                case 'completed':
                                    $badgeClass = 'bg-success';
                                    $textStatus = 'Selesai';
                                    break;
                                case 'rejected':
                                    $badgeClass = 'bg-danger';
                                    $textStatus = 'Di Tolak !';
                                    break;
                                default:
                                    $badgeClass = 'bg-secondary';
                                    $textStatus = 'Tidak Diketahui !';
                                    break;
                            }
                            ?>
                            <span class="badge <?= $badgeClass ?>"><?= esc(ucfirst(str_replace('_', ' ', $textStatus))) ?></span>
                        </p>
                        <!-- <?php if (!empty($invoice['order_notes'])): ?>
                            <p><strong>Catatan Order Agen:</strong> <?= esc($invoice['order_notes']) ?></p>
                        <?php endif; ?> -->
                    </div>
                    <div class="col-6">
                        <p><strong>Agen Pemesan:</strong> <?= esc($invoice['agen_username'] ?? 'N/A') ?></p>
                        <!-- <p><strong>Email Agen:</strong> <?= esc($invoice['agen_email'] ?? 'N/A') ?></p> -->
                        <p><strong>No Telpon Agen:</strong> <?= esc($invoice['agen_telpon'] ?? 'N/A') ?></p>
                        <p><strong>Alamat Agen:</strong> <?= esc($invoice['agen_alamat'] ?? 'N/A') ?></p>
                    </div>
                </div>

                <h5>Item Produk dalam Order</h5>
                <?php if (empty($orderItems)): ?>
                    <div class="alert alert-info" role="alert">
                        Tidak ada item produk yang terkait dengan tagihan ini.
                    </div>
                <?php else: ?>
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered table-sm" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Produk</th>
                                    <th>Kuantitas</th>
                                    <th>Harga Satuan (Rp)</th>
                                    <th>Sub Total (Rp)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1;
                                foreach ($orderItems as $item): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= esc($productNames[$item['product_id']] ?? 'N/A') ?></td>
                                        <td><?= esc($item['quantity']) ?></td>
                                        <td>Rp <?= esc(number_format($item['base_price'], 0, ',', '.')) ?></td>
                                        <td>Rp <?= esc(number_format($item['quantity'] * $item['base_price'], 0, ',', '.')) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="4" class="text-end">Total Tagihan Anda:</th>
                                    <th>Rp <?= esc(number_format($invoice['total_amount'], 0, ',', '.')) ?></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                <?php endif; ?>

                <?php if (!empty($invoice['notes'])): ?>
                    <div class="mt-4">
                        <h5>Catatan Tagihan:</h5>
                        <p class="alert alert-secondary"><?= esc($invoice['notes']) ?></p>
                    </div>
                <?php endif; ?>

                <!-- <div class="text-center mt-5">
                    <p>Terima kasih atas kerja sama Anda.</p>
                    <p>Hormat kami,<br><strong>PT. IMERSA SOLUSI TEKNOLOGI</strong></p>
                </div> -->
            </div>

            <hr class="my-4">

            <div class="d-flex justify-content-between">
                <a href="<?= base_url('pabrik/distributor-invoices') ?>" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar Tagihan</a>
                <div>
                    <button type="button" class="btn btn-primary me-2" onclick="printInvoice()"><i class="fas fa-print me-1"></i> Cetak Tagihan</button>
                    <button type="button" class="btn btn-success" onclick="downloadPdfInvoice()"><i class="fas fa-file-pdf me-1"></i> Unduh PDF</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<script>
    // Fungsi untuk cetak langsung melalui browser
    function printInvoice() {
        const content = document.getElementById('invoiceContent').innerHTML;
        const originalBody = document.body.innerHTML;

        // Sembunyikan semua elemen kecuali yang ingin dicetak
        document.body.innerHTML = content;
        document.body.classList.add('print-mode'); // Tambahkan kelas untuk CSS cetak

        window.print();

        // Kembalikan body seperti semula setelah cetak atau user membatalkan
        document.body.innerHTML = originalBody;
        document.body.classList.remove('print-mode'); // Hapus kelas
        location.reload(); // Mungkin perlu reload agar JS dan event listener kembali bekerja sempurna
    }

    // Fungsi untuk unduh PDF menggunakan jsPDF dan html2canvas
    async function downloadPdfInvoice() {
        const {
            jsPDF
        } = window.jspdf;
        const element = document.getElementById('invoiceContent'); // Area yang ingin dijadikan PDF

        // Hapus elemen yang tidak perlu muncul di PDF jika ada (misal: tombol)
        const buttons = element.querySelectorAll('button, a.btn');
        buttons.forEach(btn => btn.style.display = 'none'); // Sembunyikan tombol saat membuat canvas

        Swal.fire({
            title: 'Membuat PDF...',
            text: 'Mohon tunggu sebentar.',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        try {
            const canvas = await html2canvas(element, {
                scale: 2, // Meningkatkan kualitas gambar untuk PDF
                useCORS: true // Penting jika ada gambar dari domain lain (misal logo)
            });

            const imgData = canvas.toDataURL('image/png');
            const imgWidth = 210; // Lebar A4 dalam mm
            const pageHeight = 297; // Tinggi A4 dalam mm
            const imgHeight = canvas.height * imgWidth / canvas.width;
            let heightLeft = imgHeight;

            const doc = new jsPDF('p', 'mm', 'a4');
            let position = 0;

            doc.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
            heightLeft -= pageHeight;

            while (heightLeft >= 0) {
                position = heightLeft - imgHeight;
                doc.addPage();
                doc.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
                heightLeft -= pageHeight;
            }

            doc.save(`tagihan-<?= esc($invoice['invoice_number']) ?>.pdf`);
            Swal.close();
        } catch (error) {
            console.error('Error creating PDF:', error);
            Swal.fire({
                icon: 'error',
                title: 'Gagal Membuat PDF',
                text: 'Terjadi kesalahan saat membuat file PDF. Coba lagi.',
                confirmButtonColor: '#dc3545'
            });
        } finally {
            // Pastikan tombol dikembalikan terlihat setelah proses selesai
            buttons.forEach(btn => btn.style.display = ''); // Tampilkan kembali tombol
        }
    }
</script>

<style>
    /* CSS untuk menyembunyikan elemen saat mode cetak */
    @media print {
        body>*:not(.print-mode) {
            display: none !important;
        }

        .print-mode {
            width: 100%;
            margin: 0;
            padding: 0;
            box-shadow: none;
            border: none;
        }

        .card-header,
        .card-footer,
        .d-flex.justify-content-between {
            display: none !important;
        }

        /* Sesuaikan lebih lanjut jika ada header/footer layout yang muncul */
        body {
            margin: 0;
            padding: 0;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .bg-light,
        .border,
        .rounded {
            background-color: #f8f9fa !important;
            border: 1px solid #dee2e6 !important;
            border-radius: .25rem !important;
        }

        /* Sembunyikan tombol cetak di halaman cetak itu sendiri */
        button.btn,
        a.btn {
            display: none !important;
        }
    }
</style>
<?= $this->endSection() ?>