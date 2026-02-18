<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?></title>
    <link rel="stylesheet" href="<?= base_url('/assets/css/bootstrap.min.css'); ?>">
    <style>
        /* CSS untuk tata letak profesional */
        :root {
            --doc-padding: 15mm;
        }

        body {
            background-color: #f0f2f5;
            /* Warna latar pratinjau yang lebih lembut */
        }

        .delivery-document {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #333;
            background-color: #fff;
            margin: 0 auto 20px auto;
            padding: var(--doc-padding);
            width: 210mm;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            border-radius: 5px;
            /* Sedikit lengkungan pada pratinjau */
            page-break-inside: avoid;
            /* Mencegah dokumen terpotong saat cetak */
        }

        .doc-header {
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .doc-title {
            font-size: 24px;
            font-weight: bold;
            text-align: right;
        }

        .info-section {
            padding: 15px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .info-section h5 {
            font-weight: bold;
            font-size: medium;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }

        .info-section p,
        .info-section address {
            margin-bottom: 5px;
            font-size: 14px;
            line-height: 1.6;
        }

        .item-table thead {
            background-color: #343a40;
            color: #fff;
        }

        .item-table th,
        .item-table td {
            vertical-align: middle;
        }

        .totals-section {
            margin-top: 10px;
            text-align: right;
            font-size: 14px;
        }

        .signatures {
            margin-top: 60px;
        }

        .signature-box {
            text-align: center;
        }

        .signature-box p {
            margin-bottom: 60px;
            /* Ruang untuk tanda tangan */
        }

        /* Style khusus untuk mode CETAK */
        @media print {
            body {
                background-color: #fff;
            }

            .no-print {
                display: none;
            }

            .delivery-document {
                margin: 0;
                padding: 10mm;
                box-shadow: none;
                border-radius: 0;
                border: none;
                margin-bottom: 20mm;
                /* Jarak antar dokumen jika digabung */
            }
        }
    </style>
</head>

<body>

    <div class="text-center my-3 no-print">
        <h1>Pratinjau Dokumen Pengiriman</h1>
        <p>Gunakan fungsi cetak browser Anda (Ctrl+P) untuk mencetak. Tampilan sudah dioptimalkan untuk cetak.</p>
        <button onclick="window.print()" class="btn btn-primary"><i class="fas fa-print"></i> Cetak Sekarang</button>
    </div>

    <?php foreach ($shipments as $shipment):
        // Kumpulkan semua data yang relevan untuk iterasi ini
        $order = $ordersData[$shipment['order_id']] ?? null;
        if (!$order) continue; // Safety check

        $distributor = $distributorFullData[$order['distributor_id']] ?? null;
        $agen = $agenFullData[$order['agen_id']] ?? null;

        // Menentukan siapa penerima akhir untuk ditampilkan
        $penerima = $agen ?: $distributor;
        $tipePenerima = $agen ? 'Agen' : 'Distributor';
    ?>
        <div class="delivery-document">
            <!-- ======================= HEADER ======================= -->
            <header class="row doc-header align-items-center">
                <div class="col-1">
                    <img src="/favicon.ico" alt="" width="50" height="50">
                </div>
                <div class="col-6">
                    <!-- Ganti dengan logo Anda jika ada -->
                    <h4 class="font-weight-bold">PT. Imersa Solusi Teknologi</h4>
                    <p class="mb-0">Jalan Mastrip Lambang Kuning Kertosono</p>
                </div>
                <div class="col-4">
                    <h2 class="doc-title">DOKUMEN PENGIRIMAN</h2>
                </div>
            </header>

            <!-- ======================= INFO PENGIRIMAN, PENGIRIM, PENERIMA ======================= -->
            <div class="row">
                <div class="col-8">
                    <div class="info-section h-100">
                        <h5>Informasi Penerima</h5>
                        <?php if ($penerima): ?>
                            <p class="mb-1">
                                <strong>Nama: <?= esc($penerima['username']) ?> (<?= $tipePenerima ?>)</strong>
                            </p>
                            <p>
                                <strong>No. Telepon:</strong> <?= esc($penerima['no_telpon'] ?? 'N/A') ?>
                            </p>
                            <address class="mb-1 fst-normal">
                                <strong>Alamat:</strong><br>
                                <?= nl2br(esc($penerima['alamat'] ?? 'Alamat tidak tersedia.')) ?>
                            </address>
                        <?php else: ?>
                            <p>Informasi penerima tidak tersedia.</p>
                        <?php endif; ?>
                        <h5 class="mt-3">Informasi Distributor</h5>
                        <?php if ($distributor): ?>
                            <p class="mb-1">
                                <strong>Nama: <?= esc($distributor['username']) ?> (Distributor)</strong>
                            </p>
                            <!-- <address class="mb-1 fst-normal">
                                <strong>Alamat:</strong><br>
                                <?php # nl2br(esc($distributor['alamat'] ?? 'Alamat tidak tersedia.')) 
                                ?>
                            </address> -->
                            <p>
                                <strong>No. Telepon:</strong> <?= esc($distributor['no_telpon'] ?? 'N/A') ?>
                            </p>
                        <?php else: ?>
                            <p>Informasi distributor tidak tersedia.</p>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-4">
                    <div class="info-section h-100">
                        <h5>Detail Pengiriman</h5>
                        <p><strong>ID Pengiriman:</strong><br>SHP-<?= esc($shipment['id']) ?></p>
                        <p><strong>ID Order:</strong><br>ORD-<?= esc($order['id']) ?></p>
                        <p><strong>Tanggal Kirim:</strong><br><?= date('d F Y', strtotime($shipment['shipping_date'])) ?></p>
                        <h4><strong>No. Resi:</strong><br><?= esc($shipment['tracking_number'] ?? 'Belum Ada') ?></h4>
                    </div>
                </div>
            </div>

            <!-- ======================= RINCIAN BARANG ======================= -->
            <h5 class="mt-4 font-weight-bold">Rincian Barang</h5>
            <table class="table table-bordered table-sm item-table">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 5%;">No.</th>
                        <th>Nama Produk</th>
                        <th class="text-center" style="width: 15%;">Kuantitas</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $currentOrderItems = $orderItems[$shipment['order_id']] ?? [];
                    $itemNo = 1;
                    $totalKuantitas = 0;
                    if (empty($currentOrderItems)): ?>
                        <tr>
                            <td colspan="3" class="text-center">Tidak ada item produk dalam pengiriman ini.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($currentOrderItems as $item):
                            $totalKuantitas += $item['quantity'];
                        ?>
                            <tr>
                                <td class="text-center"><?= $itemNo++ ?></td>
                                <td><?= esc($productNames[$item['product_id']] ?? 'Produk Tidak Dikenali') ?></td>
                                <td class="text-center"><?= esc($item['quantity']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            <div class="row totals-section">
                <div class="col-auto ms-auto">
                    <p class="font-weight-bold">Total Kuantitas: <?= $totalKuantitas ?> Unit</p>
                </div>
            </div>

            <!-- ======================= TANDA TANGAN ======================= -->
            <div class="row signatures">
                <div class="col-4 signature-box">
                    <p>Hormat Kami,</p>
                    <p>(______________________)</p>
                    <strong>Pihak Pabrik</strong>
                </div>
                <div class="col-4 signature-box">
                    <p>Dikirim Oleh,</p>
                    <p>(______________________)</p>
                    <strong>Kurir</strong>
                </div>
                <div class="col-4 signature-box">
                    <p>Diterima Oleh,</p>
                    <p>(______________________)</p>
                    <strong>Pihak Penerima</strong>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <script>
        // Otomatis membuka dialog cetak saat halaman dimuat
        window.onload = function() {
            window.print();
        };
    </script>
</body>

</html>