<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div id="invoice-content">
        <h1 class="mb-4"><?= $title ?></h1>

        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-primary text-white">
                <h6 class="m-0 font-weight-bold"><i class="fas fa-info-circle me-2"></i> Detail Tagihan #<?= esc($invoice['invoice_number']) ?></h6>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h5>Informasi Tagihan</h5>
                        <p><strong>No. Tagihan:</strong> <?= esc($invoice['invoice_number']) ?></p>
                        <p><strong>Tanggal Tagihan:</strong> <?= date('d/m/Y', strtotime($invoice['invoice_date'])) ?></p>
                        <p><strong>Jatuh Tempo:</strong> <?= date('d/m/Y', strtotime($invoice['due_date'])) ?></p>
                        <p><strong>Total Jumlah:</strong> Rp <?= esc(number_format($invoice['total_amount'], 0, ',', '.')) ?></p>
                        <p><strong>Status:</strong>
                            <?php
                            $status = strtolower($invoice['status']);
                            $badgeClass = '';
                            $text = '';
                            switch ($status) {
                                case 'unpaid':
                                    $badgeClass = 'bg-danger';
                                    $text = 'Belum Di Bayar';
                                    break;
                                case 'paid':
                                    $badgeClass = 'bg-success';
                                    $text = 'Sudah Di Bayar';
                                    break;
                                case 'partially_paid':
                                    $badgeClass = 'bg-warning text-dark';
                                    $text = 'Sebagian Di Bayar';
                                    break;
                                case 'cancelled':
                                    $badgeClass = 'bg-secondary';
                                    $text = 'Di Batalkan !';
                                    break;
                                default:
                                    $badgeClass = 'bg-info';
                                    $text = 'Tidak Di Ketahui !';
                                    break;
                            }
                            ?>
                            <span class="badge <?= $badgeClass ?>"><?= esc(ucfirst(str_replace('_', ' ', $text))) ?></span>
                        </p>
                        <p><strong>Tanggal Pembayaran:</strong> <?= !empty($invoice['payment_date']) ? date('d/m/Y H:i', strtotime($invoice['payment_date'])) : '-' ?></p>
                    </div>
                    <div class="col-md-6">
                        <h5>Informasi Order & Agen Terkait</h5>
                        <p><strong>Order ID Asal:</strong> ORD-<?= esc($invoice['order_ref_id']) ?></p>
                        <p><strong>Agen Pemesan:</strong> <?= esc($invoice['agen_username'] ?? 'N/A') ?></p>
                    </div>
                </div>

                <h5>Item Produk dalam Order yang Ditagihkan (Harga Pabrik)</h5>
                <?php if (empty($orderItems)): ?>
                    <div class="alert alert-info" role="alert">
                        Tidak ada item produk yang terkait dengan tagihan ini.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Produk</th>
                                    <th>Kuantitas</th>
                                    <th>Harga Satuan (Pabrik)</th>
                                    <th>Sub Total (Rp)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1;
                                foreach ($orderItems as $item): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= esc($item['product_name']) ?></td>
                                        <td><?= esc($item['quantity']) ?></td>
                                        <td>Rp <?= esc(number_format($item['base_price'], 0, ',', '.')) ?></td>
                                        <td>Rp <?= esc(number_format($item['quantity'] * $item['base_price'], 0, ',', '.')) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <hr>
    <div class="d-flex justify-content-between mb-4">
        <a href="<?= base_url('distributor/invoicesme') ?>" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i> Kembali</a>
        <div>
            <button id="printBtn" class="btn btn-info"><i class="fas fa-print me-1"></i> Cetak</button>
            <!-- <button id="downloadPdfBtn" class="btn btn-danger"><i class="fas fa-file-pdf me-1"></i> Unduh PDF</button> -->
            <button onclick="downloadPdfInvoice()" class=" btn btn-danger"><i class="fas fa-file-pdf me-1"></i> Unduh PDF</button>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<script>
    document.getElementById('printBtn').addEventListener('click', function() {
        window.print();
    });

    // document.getElementById('downloadPdfBtn').addEventListener('click', function() {
    //     const {
    //         jspdf
    //     } = window.jspdf;
    //     const invoiceContent = document.getElementById('invoice-content');
    //     const invoiceNumber = "<?= esc($invoice['invoice_number']) ?>";

    //     html2canvas(invoiceContent, {
    //         scale: 2
    //     }).then(canvas => {
    //         const imgData = canvas.toDataURL('image/png');
    //         const pdf = new jspdf('p', 'mm', 'a4');
    //         const pdfWidth = pdf.internal.pageSize.getWidth();
    //         const pdfHeight = (canvas.height * pdfWidth) / canvas.width;

    //         pdf.addImage(imgData, 'PNG', 0, 0, pdfWidth, pdfHeight);
    //         pdf.save(`tagihan-${invoiceNumber}.pdf`);
    //     });
    // });
    async function downloadPdfInvoice() {
        const {
            jsPDF
        } = window.jspdf;
        const element = document.getElementById('invoice-content'); // Area yang ingin dijadikan PDF

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
<?= $this->endSection() ?>